<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Tag;
use Tests\TestCase;

class ApiRouteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Post::truncate();
        Tag::truncate();
    }

    public function test_health_check_returns_ok()
    {
        $response = $this->get('/api/health');

        $response->assertStatus(200);
        $response->assertJsonStructure(['status', 'timestamp']);
        $this->assertEquals('ok', $response->json('status'));
    }

    public function test_can_fetch_public_posts()
    {
        $response = $this->get('/api/posts');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'posts',
            'nextCursor',
            'hasMore'
        ]);
        $response->assertHeader('Cache-Control');
    }



    public function test_can_fetch_public_tags()
    {
        $response = $this->get('/api/tags');

        $response->assertStatus(200);
        $response->assertJsonStructure(['tags']);
    }
}
