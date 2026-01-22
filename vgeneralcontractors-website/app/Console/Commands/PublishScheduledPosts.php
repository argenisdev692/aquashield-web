<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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
    protected $description = 'Publish posts that were scheduled and whose publication time has arrived';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $this->info("Checking for scheduled posts at {$now}");

        // Find posts that are scheduled and their scheduled time has passed
        $posts = Post::where('post_status', 'scheduled')
            ->where('scheduled_at', '<=', $now)
            ->get();

        $count = $posts->count();
        $this->info("Found {$count} scheduled posts to publish");

        foreach ($posts as $post) {
            try {
                $post->update([
                    'post_status' => 'published',
                    // Keep the scheduled_at date for reference
                ]);

                $this->info("Published post: {$post->post_title} (ID: {$post->id})");
                Log::info("Published scheduled post", [
                    'post_id' => $post->id,
                    'post_title' => $post->post_title,
                    'scheduled_at' => $post->scheduled_at
                ]);
            } catch (\Exception $e) {
                $this->error("Error publishing post ID {$post->id}: {$e->getMessage()}");
                Log::error("Error publishing scheduled post", [
                    'post_id' => $post->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        $this->info("Scheduled post publishing completed");
        return 0;
    }
}