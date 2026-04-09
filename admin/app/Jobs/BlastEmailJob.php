<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\NewPostMail;
use App\Models\Subscriber;
use App\Models\AuditLog;

class BlastEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $title;
    public $slug;
    public $excerpt;
    public $coverImage;

    /**
     * Create a new job instance.
     */
    public function __construct(string $title, string $slug, string $excerpt, string $coverImage = '')
    {
        $this->title = $title;
        $this->slug = $slug;
        $this->excerpt = $excerpt;
        $this->coverImage = $coverImage;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            $subscribers = Subscriber::pluck('email')->filter()->toArray();

            if (!empty($subscribers)) {
                foreach ($subscribers as $email) {
                    Mail::to($email)->queue(new NewPostMail($this->title, $this->slug, $this->excerpt, $this->coverImage));
                }

                AuditLog::record('email_blast', 'system', [
                    'resource' => 'post',
                    'details' => [
                        'post_title' => $this->title,
                        'post_slug' => $this->slug,
                        'recipients_count' => count($subscribers),
                    ],
                ]);
            }

            Log::info("BlastEmailJob sent to " . count($subscribers) . " subscribers for post: {$this->title}");
        } catch (\Exception $e) {
            Log::error('BlastEmailJob Failed: ' . $e->getMessage());
        }
    }
}
