<?php

namespace App\Console\Commands;

use App\Jobs\PublishPostJob;
use App\Models\Post;
use Illuminate\Console\Command;

class PublishScheduledPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:publish-scheduled-posts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish scheduled posts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $total = 0;

        Post::scheduledToPublish()
            ->select('id')
            ->chunkById(100, function ($posts) use (&$total) {
                $posts->each(function ($post) {
                    PublishPostJob::dispatch($post->id);
                });

                $total += $posts->count();
            });

        $this->info(
            $total > 0
                ? "Dispatched {$total} posts for publishing."
                : 'No scheduled posts are currently due for publishing.'
        );
    }
}
