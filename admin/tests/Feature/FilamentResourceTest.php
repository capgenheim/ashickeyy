<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilamentResourceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Ensure dummy admin user exists for routing validations safely natively
        $this->admin = User::firstOrCreate([
            'email' => 'admin-test-filament@ashickey.space'
        ], [
            'name' => 'Test Admin',
            'password' => bcrypt('password123')
        ]);
    }

    public function test_filament_dashboard_renders_securely()
    {
        $response = $this->actingAs($this->admin)->get('/kcg5025');
        // Validate exactly that our custom Telemetry Widget boots natively without breaking SQL rules
        $response->assertStatus(200);
    }

    public function test_post_resource_index_page_mounts_correctly()
    {
        $response = $this->actingAs($this->admin)->get('/kcg5025/posts');
        $response->assertStatus(200);
    }

    public function test_category_resource_index_page_mounts_correctly()
    {
        $response = $this->actingAs($this->admin)->get('/kcg5025/categories');
        $response->assertStatus(200);
    }

    public function test_tag_resource_index_page_mounts_correctly()
    {
        $response = $this->actingAs($this->admin)->get('/kcg5025/tags');
        $response->assertStatus(200);
    }
}
