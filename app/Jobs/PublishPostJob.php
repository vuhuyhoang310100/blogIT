<?php

namespace App\Jobs;

use App\Enums\PostStatus;
// use App\Events\PostPublished;
use App\Models\Post;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

class PublishPostJob implements ShouldQueue
{
	use Queueable;

	/**
	 * Create a new job instance.
	 */
	public function __construct(private readonly int $postId)
	{
		//
	}

	/**
	 * Execute the job.
	 */
	public function handle(): void
	{
		$post = null;
		DB::transaction(function () use (&$post) {
			$post = Post::whereKey($this->postId)
				->scheduledToPublish()
				->lockForUpdate()
				->first();

			if (! $post) {
				return;
			}

			$post->fill([
				'status' => PostStatus::Published,
				'published_at' => now(),
				'publish_at' => null,
			]);

			$post->save();
		});
		// if ($post) {
		// 	DB::afterCommit(fn() => event(new PostPublished($post->id)));
		// }
	}
}
