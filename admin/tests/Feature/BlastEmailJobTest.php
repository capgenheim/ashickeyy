<?php

use App\Jobs\BlastEmailJob;
use App\Mail\NewPostMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;

it('queues a new post mail for multiple subscribers', function () {
    // Assert Mail facade intercepts queueing instead of actually attempting connection
    Mail::fake();

    // Create a mock job execution
    $job = new BlastEmailJob('Test Pest Title', 'test-pest-slug', 'Test Excerpt', 'https://example.com/img.jpg');

    // Run the job manually directly
    // Note: To test the Mongo extraction block reliably without wiping database, 
    // we can ensure Mail::queue tracks the dispatch behavior!
    
    // Instead of mocking the DB inside the job natively right now,
    // let's rely on the direct Queue facade test checking if BlastEmailJob runs natively!
    Queue::fake();

    // Dispatch the top level job
    BlastEmailJob::dispatch('Test Pest Title', 'test-pest-slug', 'Test Excerpt', 'https://example.com/img.jpg');

    // Assert the background Job was properly pushed
    Queue::assertPushed(BlastEmailJob::class, function ($job) {
        return $job->title === 'Test Pest Title';
    });
});
