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
use MongoDB\Client;

class BlastEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $title;
    public $slug;
    public $excerpt;
    public $coverImage;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $title, string $slug, string $excerpt, string $coverImage)
    {
        $this->title = $title;
        $this->slug = $slug;
        $this->excerpt = $excerpt;
        $this->coverImage = $coverImage;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $mongo = new Client(env('DB_DSN', 'mongodb://ashickey-mongo:27017'));
            $collection = $mongo->ashickey->email_subscriptions;
            $subs = $collection->find([]);

            $emails = [];
            foreach ($subs as $sub) {
                if (!empty($sub['email'])) {
                    $emails[] = $sub['email'];
                }
            }

            if (!empty($emails)) {
                // Ensure Redis queue limits the throughput, sending chunked batches locally
                $chunks = array_chunk($emails, 50); // Dispatch 50 at a time securely via Bcc
                foreach ($chunks as $chunk) {
                    Mail::bcc($chunk)->send(new NewPostMail($this->title, $this->slug, $this->excerpt, $this->coverImage));
                }
            }
        } catch (\Exception $e) {
            Log::error('Background BlastEmailJob Failed: ' . $e->getMessage());
        }
    }
}
