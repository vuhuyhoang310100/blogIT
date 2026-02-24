<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Enums\PostStatus;
use App\Models\Post;
use App\Models\User;
use App\Repositories\Contracts\PostRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

final class PostRepository extends SoftDeleteRepository implements PostRepositoryInterface
{
    /**
     * Get the model class for the repository.
     */
    public function model(): string
    {
        return Post::class;
    }

    /**
     * Duplicate a post and its tags.
     *
     * @param  Post  $model  The post to duplicate.
     * @return Post The duplicated post.
     */
    public function duplicate(Post $model): Post
    {
        return DB::transaction(function () use ($model) {
            $model->loadMissing('tags:id');

            $new = $model->replicate();

            $new->forceFill([
                'status' => PostStatus::Draft->value,
                'published_at' => null,
                'slug' => $model->slug.'-copy-'.now()->format('YmdHis'),
                'title' => $model->title.' (Copy)',
            ]);

            // Handle Image Duplication
            if ($model->image && Storage::disk('public')->exists($model->image)) {
                $extension = pathinfo($model->image, PATHINFO_EXTENSION);
                $dirname = pathinfo($model->image, PATHINFO_DIRNAME);
                $filename = pathinfo($model->image, PATHINFO_FILENAME);

                // Create a unique name for the copy
                $newFilename = $filename.'-copy-'.now()->timestamp.'.'.$extension;
                $newPath = ($dirname === '.' ? '' : $dirname.'/').$newFilename;

                if (Storage::disk('public')->copy($model->image, $newPath)) {
                    $new->image = $newPath;
                } else {
                    $new->image = null; // Fallback if copy fails
                }
            } else {
                // If image doesn't exist on disk, don't reference it
                $new->image = null;
            }

            $new->save();

            // Sync tags to maintain the Aggregate state on the new copy
            $new->tags()->sync($model->tags->pluck('id')->all());

            return $new;
        });
    }

    /**
     * Publish a post.
     *
     * If the post is already published, it is left unchanged.
     *
     * @param  Post  $model  The post to publish.
     * @return Post The published post.
     */
    public function publish(Post $model): Post
    {
        if ($model->status === PostStatus::Published) {
            return $model;
        }

        $model->forceFill([
            'status' => PostStatus::Published->value,
            'published_at' => $model->published_at ?? Carbon::now(),
        ])->save();

        return $model;
    }

    /**
     * Unpublishes a post.
     *
     * If the post is not published, it is left unchanged.
     *
     * @param  Post  $model  The post to unpublish.
     * @return Post The unpublished post.
     */
    public function unpublish(Post $model): Post
    {
        if ($model->status !== PostStatus::Published) {
            return $model;
        }

        $model->forceFill([
            'status' => PostStatus::Draft->value,
            'published_at' => null,
        ])->save();

        return $model;
    }

    /**
     * Retrieves posts by IDs including trashed records.
     *
     * @param  array<int>  $ids  The post IDs to retrieve.
     * @param  array<string>  $columns  The columns to retrieve (default: ['*']).
     * @return Collection<Post> The posts matching the IDs and columns.
     */
    public function getByIdsIncludingTrashed(array $ids, array $columns = ['*']): Collection
    {
        if ($ids === []) {
            return new Collection;
        }

        return $this->model->newQuery()->withTrashed()->whereKey($ids)->select($columns)->get();
    }

    /**
     * Finds a published post by its slug.
     *
     * @param  string  $slug  The slug of the post to retrieve.
     * @return Post|null The post matching the slug, or null if no post is found.
     */
    public function findPublishedBySlug(string $slug): ?Post
    {
        return Post::query()
            ->select($this->getColumns())
            ->with(['user', 'category', 'tags'])
            ->withCount(['likes', 'views', 'comments'])
            ->published()
            ->where('slug', $slug)
            ->first();
    }

    /**
     * Finds related posts by category, excluding the given post ID.
     *
     * @param  int  $categoryId  The category ID to find related posts for.
     * @param  int  $excludePostId  The post ID to exclude from the results.
     * @param  int  $limit  The maximum number of results to return.
     * @return Collection<Post> The related posts.
     */
    public function findRelatedByCategory(int $categoryId, int $excludePostId, int $limit): Collection
    {
        return Post::query()
            ->select($this->getColumns())
            ->published()
            ->where('category_id', $categoryId)
            ->where('id', '!=', $excludePostId)
            ->limit($limit)
            ->with(['user:id,name', 'category:id,name'])
            ->get();
    }

    /**
     * Retrieves the latest posts, in descending order of published_at.
     *
     * @param  int  $limit  The maximum number of results to return.
     * @return Collection<Post> The latest posts.
     */
    public function getLatestPosts(int $limit = 6): Collection
    {
        return $this->query()
            ->published()
            ->withFrontendMetadata()
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Retrieves featured posts.
     *
     * @param  int  $limit  The maximum number of results to return.
     * @return Collection<Post> The featured posts.
     */
    public function getFeaturedPosts(int $limit = 4): Collection
    {
        return $this->query()
            ->published()
            ->featured()
            ->withFrontendMetadata()
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Retrieves trending posts.
     *
     * @param  int  $limit  The maximum number of results to return.
     * @param  int  $days  The number of days to consider when calculating trending posts.
     * @return Collection<post> The trending posts.
     */
    public function getTrendingPosts(int $limit = 6, int $days = 14): Collection
    {
        return $this->query()
            ->published()
            ->withFrontendMetadata()
            ->trending($days)
            ->limit($limit)
            ->get();
    }

    /**
     * Retrieves a personalized feed of posts, based on the given user's preferences.
     *
     * If the user is not provided, the method will return the trending posts.
     *
     * @param  ?User  $user  The user to generate the personalized feed for.
     * @param  int  $limit  The maximum number of results to return.
     * @return Collection<post> The personalized feed.
     */
    public function getPersonalizedFeed(?User $user = null, int $limit = 6): Collection
    {
        $query = $this->query()->published();

        if ($user && method_exists($user, 'following')) {
            $followingIds = $user->following()->pluck('users.id');
            if ($followingIds->isNotEmpty()) {
                $query->whereIn('user_id', $followingIds);
            } else {
                $query->featured();
            }
        } else {
            $query->orderByDesc('views_count');
        }

        return $query->latest()
            ->withFrontendMetadata()
            ->limit($limit)
            ->get();
    }

    /**
     * Returns the default columns to be retrieved from the database.
     *
     * @return array<string> The default columns to be retrieved.
     */
    public function getColumns(): array
    {
        return [
            'id',
            'title',
            'excerpt',
            'content',
            'slug',
            'user_id',
            'category_id',
            'image',
            'is_featured',
            'meta_title',
            'meta_description',
            'published_at',
            'created_at',
        ];
    }
}
