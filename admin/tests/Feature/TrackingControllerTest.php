<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TrackingControllerTest extends TestCase
{
    /**
     * Test the telemetry endpoints map correctly onto MongoDB backend
     */
    public function test_telemetry_action_logs_correctly_on_initial_app_open()
    {
        $this->withoutExceptionHandling();
        // Mock a visitor ID
        $visitorId = 'fake-uuid-visitor';

        $response = $this->postJson('/api/tracking/analytics', [
            'visitorId' => $visitorId,
            'action' => 'app_open',
            'latitude' => 3.1390,
            'longitude' => 101.6869, 
            'userAgent' => 'Pest Test Engine'
        ]);

        file_put_contents('/tmp/error.json', $response->content());
        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
    }

    public function test_telemetry_action_actual_reader_classification()
    {
        $response = $this->postJson('/api/tracking/analytics', [
            'visitorId' => 'fake-uuid-visitor',
            'action' => 'actual_reader',
            'postSlug' => 'test-post-slug',
            'userAgent' => 'Pest Test Engine'
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
    }
}
