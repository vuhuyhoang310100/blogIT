<?php

namespace Tests\Feature;

use App\Enums\PostStatus;
use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use App\Jobs\PublishPostJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class PostSchedulingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure we have a user and category for posts
        $this->user = User::factory()->create();
        $this->category = Category::factory()->create();
    }

    public function test_scheduled_to_publish_scope_returns_eligible_posts(): void
    {
        // Post scheduled for the past
        $pastPost = Post::factory()->create([
            'status' => PostStatus::Schedule,
            'publish_at' => now()->subMinute(),
        ]);

        // Post scheduled for the future
        $futurePost = Post::factory()->create([
            'status' => PostStatus::Schedule,
            'publish_at' => now()->addHour(),
        ]);

        // Post that is already published
        $publishedPost = Post::factory()->create([
            'status' => PostStatus::Published,
            'publish_at' => now()->subHour(),
        ]);

        $scheduledPosts = Post::scheduledToPublish()->get();

        $this->assertCount(1, $scheduledPosts);
        $this->assertTrue($scheduledPosts->contains($pastPost));
        $this->assertFalse($scheduledPosts->contains($futurePost));
        $this->assertFalse($scheduledPosts->contains($publishedPost));
    }

    public function test_publish_scheduled_posts_command_dispatches_jobs(): void
    {
        Queue::fake();

        Post::factory()->create([
            'status' => PostStatus::Schedule,
            'publish_at' => now()->subMinute(),
        ]);

        Post::factory()->create([
            'status' => PostStatus::Schedule,
            'publish_at' => now()->subMinute(),
        ]);

        $this->artisan('app:publish-scheduled-posts')
            ->expectsOutput('Dispatched 2 posts for publishing.')
            ->assertExitCode(0);

        Queue::assertPushed(PublishPostJob::class, 2);
    }

    public function test_publish_post_job_publishes_the_post(): void
    {
        $post = Post::factory()->create([
            'status' => PostStatus::Schedule,
            'publish_at' => now()->subMinute(),
        ]);

        $job = new PublishPostJob($post->id);
        $job->handle();

        $post->refresh();
        $this->assertEquals(PostStatus::Published, $post->status);
        $this->assertNotNull($post->published_at);
        $this->assertNull($post->publish_at); // Should we clear it? Service clears it, let's see.
    }
}
