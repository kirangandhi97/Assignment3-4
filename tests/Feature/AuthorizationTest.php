<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Guarantee;
use App\Models\File;
use App\Models\Review;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $regularUser;
    protected $anotherUser;

    public function setUp(): void
    {
        parent::setUp();

        // Create test users
        $this->adminUser = User::factory()->create([
            'email' => 'admin@example.com',
            'role' => 'admin'
        ]);

        $this->regularUser = User::factory()->create([
            'email' => 'user@example.com',
            'role' => 'user'
        ]);

        $this->anotherUser = User::factory()->create([
            'email' => 'another@example.com',
            'role' => 'user'
        ]);
    }

    /** @test */
    public function guest_cannot_access_protected_routes()
    {
        // Try to access protected routes
        $routes = [
            route('home'),
            route('guarantees.index'),
            route('guarantees.create'),
            route('files.index'),
            route('files.create'),
        ];

        foreach ($routes as $route) {
            $response = $this->get($route);
            $response->assertRedirect(route('login'));
        }
    }

    /** @test */
    public function regular_user_cannot_access_admin_routes()
    {
        $this->actingAs($this->regularUser);

        // Admin routes to test
        $routes = [
            route('admin.dashboard'),
            route('admin.pending-reviews'),
            route('admin.file-processing'),
        ];

        foreach ($routes as $route) {
            $response = $this->get($route);
            $response->assertStatus(403); // Forbidden
        }

        // Create a guarantee in review status for testing admin.show-review-form
        $guarantee = Guarantee::factory()->create([
            'status' => 'review',
            'user_id' => $this->regularUser->id
        ]);
        
        $response = $this->get(route('admin.show-review-form', $guarantee->id));
        $response->assertStatus(403);
    }

    /** @test */
    public function admin_user_can_access_admin_routes()
    {
        $this->actingAs($this->adminUser);

        // Admin routes to test
        $routes = [
            route('admin.dashboard'),
            route('admin.pending-reviews'),
            route('admin.file-processing'),
        ];

        foreach ($routes as $route) {
            $response = $this->get($route);
            $response->assertStatus(200); // Success
        }

        // Create a guarantee in review status for testing admin.show-review-form
        $guarantee = Guarantee::factory()->create([
            'status' => 'review',
            'user_id' => $this->regularUser->id
        ]);
        
        Review::factory()->create([
            'guarantee_id' => $guarantee->id
        ]);
        
        $response = $this->get(route('admin.show-review-form', $guarantee->id));
        $response->assertStatus(200);
    }

    /** @test */
    public function user_can_only_view_own_guarantees()
    {
        $this->actingAs($this->regularUser);

        // Create guarantees for different users
        $userGuarantee = Guarantee::factory()->create([
            'user_id' => $this->regularUser->id
        ]);
        
        $otherGuarantee = Guarantee::factory()->create([
            'user_id' => $this->anotherUser->id
        ]);

        // User should see their guarantee on the index page
        $response = $this->get(route('guarantees.index'));
        $response->assertStatus(200);
        $response->assertSee($userGuarantee->corporate_reference_number);
        
        // User should not see other user's guarantee on the index page
        $response->assertDontSee($otherGuarantee->corporate_reference_number);

        // User can view their own guarantee
        $response = $this->get(route('guarantees.show', $userGuarantee->id));
        $response->assertStatus(200);
        
        // User cannot view other user's guarantee
        $response = $this->get(route('guarantees.show', $otherGuarantee->id));
        $response->assertStatus(302); // Redirect
        $response->assertRedirect(route('guarantees.index'));
    }

    /** @test */
    public function admin_can_view_all_guarantees()
    {
        $this->actingAs($this->adminUser);

        // Create guarantees for different users
        $userGuarantee = Guarantee::factory()->create([
            'user_id' => $this->regularUser->id
        ]);
        
        $otherGuarantee = Guarantee::factory()->create([
            'user_id' => $this->anotherUser->id
        ]);

        // Admin should see all guarantees on the index page
        $response = $this->get(route('guarantees.index'));
        $response->assertStatus(200);
        $response->assertSee($userGuarantee->corporate_reference_number);
        $response->assertSee($otherGuarantee->corporate_reference_number);

        // Admin can view any guarantee
        $response = $this->get(route('guarantees.show', $userGuarantee->id));
        $response->assertStatus(200);
        
        $response = $this->get(route('guarantees.show', $otherGuarantee->id));
        $response->assertStatus(200);
    }

    /** @test */
    public function user_can_only_edit_own_draft_guarantees()
    {
        $this->actingAs($this->regularUser);

        // Create guarantees with different statuses
        $draftGuarantee = Guarantee::factory()->create([
            'user_id' => $this->regularUser->id,
            'status' => 'draft'
        ]);
        
        $reviewGuarantee = Guarantee::factory()->create([
            'user_id' => $this->regularUser->id,
            'status' => 'review'
        ]);
        
        $otherUserGuarantee = Guarantee::factory()->create([
            'user_id' => $this->anotherUser->id,
            'status' => 'draft'
        ]);

        // User can edit their own draft guarantee
        $response = $this->get(route('guarantees.edit', $draftGuarantee->id));
        $response->assertStatus(200);
        
        // User cannot edit their own guarantee that's in review
        $response = $this->get(route('guarantees.edit', $reviewGuarantee->id));
        $response->assertStatus(302);
        $response->assertRedirect(route('guarantees.show', $reviewGuarantee->id));
        
        // User cannot edit another user's guarantee
        $response = $this->get(route('guarantees.edit', $otherUserGuarantee->id));
        $response->assertStatus(302);
        $response->assertRedirect(route('guarantees.index'));
    }

    /** @test */
    public function admin_can_apply_and_issue_guarantees()
    {
        $this->actingAs($this->adminUser);

        // Create guarantees with different statuses
        $reviewGuarantee = Guarantee::factory()->create([
            'user_id' => $this->regularUser->id,
            'status' => 'review'
        ]);
        
        Review::factory()->create([
            'guarantee_id' => $reviewGuarantee->id
        ]);
        
        $appliedGuarantee = Guarantee::factory()->create([
            'user_id' => $this->regularUser->id,
            'status' => 'applied'
        ]);

        // Admin can apply a guarantee in review
        $response = $this->post(route('guarantees.apply', $reviewGuarantee->id));
        $response->assertStatus(302);
        $this->assertDatabaseHas('guarantees', [
            'id' => $reviewGuarantee->id,
            'status' => 'applied'
        ]);
        
        // Admin can issue an applied guarantee
        $response = $this->post(route('guarantees.issue', $appliedGuarantee->id));
        $response->assertStatus(302);
        $this->assertDatabaseHas('guarantees', [
            'id' => $appliedGuarantee->id,
            'status' => 'issued'
        ]);
    }

    /** @test */
    public function user_cannot_apply_or_issue_guarantees()
    {
        $this->actingAs($this->regularUser);

        // Create guarantees with different statuses
        $reviewGuarantee = Guarantee::factory()->create([
            'user_id' => $this->regularUser->id,
            'status' => 'review'
        ]);
        
        Review::factory()->create([
            'guarantee_id' => $reviewGuarantee->id
        ]);
        
        $appliedGuarantee = Guarantee::factory()->create([
            'user_id' => $this->regularUser->id,
            'status' => 'applied'
        ]);

        // User cannot apply a guarantee
        $response = $this->post(route('guarantees.apply', $reviewGuarantee->id));
        $response->assertStatus(403);
        $this->assertDatabaseHas('guarantees', [
            'id' => $reviewGuarantee->id,
            'status' => 'review'
        ]);
        
        // User cannot issue a guarantee
        $response = $this->post(route('guarantees.issue', $appliedGuarantee->id));
        $response->assertStatus(403);
        $this->assertDatabaseHas('guarantees', [
            'id' => $appliedGuarantee->id,
            'status' => 'applied'
        ]);
    }

    /** @test */
    public function user_can_only_view_own_files()
    {
        $this->actingAs($this->regularUser);

        // Create files for different users
        $userFile = File::factory()->create([
            'user_id' => $this->regularUser->id
        ]);
        
        $otherFile = File::factory()->create([
            'user_id' => $this->anotherUser->id
        ]);

        // User should see their file on the index page
        $response = $this->get(route('files.index'));
        $response->assertStatus(200);
        $response->assertSee($userFile->filename);
        
        // User should not see other user's file on the index page
        $response->assertDontSee($otherFile->filename);

        // User can view their own file
        $response = $this->get(route('files.show', $userFile->id));
        $response->assertStatus(200);
        
        // User cannot view other user's file
        $response = $this->get(route('files.show', $otherFile->id));
        $response->assertStatus(302);
        $response->assertRedirect(route('files.index'));
    }

    /** @test */
    public function admin_can_view_all_files()
    {
        $this->actingAs($this->adminUser);

        // Create files for different users
        $userFile = File::factory()->create([
            'user_id' => $this->regularUser->id
        ]);
        
        $otherFile = File::factory()->create([
            'user_id' => $this->anotherUser->id
        ]);

        // Admin should see all files on the index page
        $response = $this->get(route('files.index'));
        $response->assertStatus(200);
        $response->assertSee($userFile->filename);
        $response->assertSee($otherFile->filename);

        // Admin can view any file
        $response = $this->get(route('files.show', $userFile->id));
        $response->assertStatus(200);
        
        $response = $this->get(route('files.show', $otherFile->id));
        $response->assertStatus(200);
    }

    /** @test */
    public function only_admin_can_process_files()
    {
        // Create a file
        $file = File::factory()->create([
            'user_id' => $this->regularUser->id,
            'status' => 'uploaded'
        ]);

        // Regular user cannot process files
        $this->actingAs($this->regularUser);
        $response = $this->post(route('files.process', $file->id));
        $response->assertStatus(403);
        
        // Admin can process files
        $this->actingAs($this->adminUser);
        $response = $this->post(route('files.process', $file->id));
        $response->assertStatus(302);
    }
}