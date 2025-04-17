<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        
        // Go directly to the home route instead of root
        $response = $this->get(route('home'));
        $response->assertStatus(200);
    }
}
