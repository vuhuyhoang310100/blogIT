<?php

declare(strict_types=1);

namespace App\Repositories\Decorators;

use App\Models\Post;
use App\Models\User;
use App\Repositories\Contracts\BaseRepositoryInterface;
use App\Repositories\Contracts\PostRepositoryInterface;
use App\Repositories\Events\RepositoryChanged;
use App\Repositories\Exceptions\RepositoryException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Event;

/**
 * @property PostRepositoryInterface $inner
 */
final class EventfulPostRepository extends SoftDeleteEventfulRepository implements PostRepositoryInterface
{
    /**
     * @param  PostRepositoryInterface  $inner  The inner repository (enforced by type hint and check).
     */
    public function __construct(BaseRepositoryInterface $inner, string $namespace)
    {
        if (! $inner instanceof PostRepositoryInterface) {
            throw new RepositoryException('Inner repository must implement PostRepositoryInterface');
        }

        parent::__construct($inner, $namespace);
    }

    /**
     * Duplicates a post, publishing it, and dispatches a RepositoryChanged event.
     *
     * @param  Post  $model  The post to duplicate.
     * @return Post The duplicated post.
     */
    public function duplicate(Post $model): Post
    {
        $result = $this->inner->duplicate($model);
        Event::dispatch(new RepositoryChanged($this->namespace));

        return $result;
    }

    /**
     * Publishes a post, and dispatches a RepositoryChanged event.
     *
     * @param  Post  $model  The post to publish.
     * @return Post The published post.
     */
    public function publish(Post $model): Post
    {
        $result = $this->inner->publish($model);
        Event::dispatch(new RepositoryChanged($this->namespace));

        return $result;
    }

    /**
     * Unpublishes a post, and dispatches a RepositoryChanged event.
     *
     * @param  Post  $model  The post to unpublish.
     * @return Post The unpublished post.
     */
    public function unpublish(Post $model): Post
    {
        $result = $this->inner->unpublish($model);
        Event::dispatch(new RepositoryChanged($this->namespace));

        return $result;
    }

    /**
     * Gets posts by IDs including trashed records.
     *
     * @param  array<int>  $ids  The post IDs to retrieve.
     * @param  array<string>  $columns  The columns to retrieve (default: ['*']).
     * @return Collection<Post> The posts matching the IDs and columns.
     */
    public function getByIdsIncludingTrashed(array $ids, array $columns = ['*']): Collection
    {
        return $this->inner->getByIdsIncludingTrashed($ids, $columns);
    }

    /**
     * Finds a published post by its slug.
     *
     * @param  string  $slug  The slug of the post to retrieve.
     * @return Post|null The post matching the slug, or null if no post is found.
     */
    public function findPublishedBySlug(string $slug): ?Post
    {
        return $this->inner->findPublishedBySlug($slug);
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
        return $this->inner->findRelatedByCategory($categoryId, $excludePostId, $limit);
    }

    /**
     * Retrieves the latest posts, in descending order of published_at.
     *
     * @param  int  $limit  The maximum number of results to return.
     * @return Collection<Post> The latest posts.
     */
    public function getLatestPosts(int $limit = 6): Collection
    {
        return $this->inner->getLatestPosts($limit);
    }

    /**
     * Retrieves featured posts.
     *
     * @param  int  $limit  The maximum number of results to return.
     * @return Collection<Post> The featured posts.
     */
    public function getFeaturedPosts(int $limit = 4): Collection
    {
        return $this->inner->getFeaturedPosts($limit);
    }

    /**
     * Retrieves trending posts.
     *
     * @param  int  $limit  The maximum number of results to return.
     * @param  int  $days  The number of days to consider when calculating trending posts.
     * @return Collection<Post> The trending posts.
     */
    public function getTrendingPosts(int $limit = 6, int $days = 14): Collection
    {
        return $this->inner->getTrendingPosts($limit, $days);
    }

    /**
     * Retrieves a personalized feed of posts, based on the given user's preferences.
     *
     * If the user is not provided, the method will return the trending posts.
     *
     * @param  ?User  $user  The user to generate the personalized feed for.
     * @param  int  $limit  The maximum number of results to return.
     * @return Collection<Post> The personalized feed.
     */
    public function getPersonalizedFeed(?User $user = null, int $limit = 6): Collection
    {
        return $this->inner->getPersonalizedFeed($user, $limit);
    }
}
