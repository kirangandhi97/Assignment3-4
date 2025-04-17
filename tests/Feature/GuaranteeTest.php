<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Guarantee;
use App\Models\Review;
use Carbon\Carbon;

class GuaranteeTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $adminUser;
    protected $regularUser;

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
    }

    /** @test */
    public function user_can_create_guarantee()
    {
        $this->actingAs($this->regularUser);

        $guaranteeData = [
            'guarantee_type' => 'Bank',
            'nominal_amount' => 50000.00,
            'nominal_amount_currency' => 'USD',
            'expiry_date' => Carbon::now()->addMonths(6)->format('Y-m-d'),
            'applicant_name' => 'Test Applicant',
            'applicant_address' => 'Test Address',
            'beneficiary_name' => 'Test Beneficiary',
            'beneficiary_address' => 'Test Beneficiary Address',
        ];

        $response = $this->post(route('guarantees.store'), $guaranteeData);

        $response->assertStatus(302); // Redirect after successful creation
        
        $guarantee = Guarantee::where('user_id', $this->regularUser->id)->latest()->first();
        $response->assertRedirect(route('guarantees.show', $guarantee->id));

        $this->assertDatabaseHas('guarantees', [
            'guarantee_type' => 'Bank',
            'nominal_amount' => 50000.00,
            'nominal_amount_currency' => 'USD',
            'user_id' => $this->regularUser->id,
            'status' => 'draft'
        ]);

        // Verify corporate_reference_number is generated
        $guarantee = Guarantee::first();
        $this->assertNotNull($guarantee->corporate_reference_number);
    }

    /** @test */
    public function admin_can_create_guarantee()
    {
        $this->actingAs($this->adminUser);

        $guaranteeData = [
            'guarantee_type' => 'Surety',
            'nominal_amount' => 75000.00,
            'nominal_amount_currency' => 'EUR',
            'expiry_date' => Carbon::now()->addMonths(12)->format('Y-m-d'),
            'applicant_name' => 'Admin Test Applicant',
            'applicant_address' => 'Admin Test Address',
            'beneficiary_name' => 'Admin Test Beneficiary',
            'beneficiary_address' => 'Admin Test Beneficiary Address',
        ];

        $response = $this->post(route('guarantees.store'), $guaranteeData);

        $response->assertStatus(302);
        $this->assertDatabaseHas('guarantees', [
            'guarantee_type' => 'Surety',
            'nominal_amount' => 75000.00,
            'nominal_amount_currency' => 'EUR',
            'user_id' => $this->adminUser->id,
        ]);
    }

    /** @test */
    public function user_can_view_own_guarantee()
    {
        $this->actingAs($this->regularUser);

        $guarantee = Guarantee::factory()->create([
            'user_id' => $this->regularUser->id,
            'status' => 'draft'
        ]);

        $response = $this->get(route('guarantees.show', $guarantee->id));

        $response->assertStatus(200);
        $response->assertSee($guarantee->corporate_reference_number);
    }

    /** @test */
    public function user_cannot_view_others_guarantee()
    {
        $this->actingAs($this->regularUser);

        $otherUser = User::factory()->create(['role' => 'user']);
        $guarantee = Guarantee::factory()->create([
            'user_id' => $otherUser->id,
            'status' => 'draft'
        ]);

        $response = $this->get(route('guarantees.show', $guarantee->id));

        $response->assertStatus(302); // Redirected away
        $response->assertRedirect(route('guarantees.index'));
        $response->assertSessionHas('error', 'You do not have permission to view this guarantee');
    }

    /** @test */
    public function admin_can_view_any_guarantee()
    {
        $this->actingAs($this->adminUser);

        $guarantee = Guarantee::factory()->create([
            'user_id' => $this->regularUser->id,
            'status' => 'draft'
        ]);

        $response = $this->get(route('guarantees.show', $guarantee->id));

        $response->assertStatus(200);
        $response->assertSee($guarantee->corporate_reference_number);
    }

    /** @test */
    public function user_can_update_own_draft_guarantee()
    {
        $this->actingAs($this->regularUser);

        $guarantee = Guarantee::factory()->create([
            'user_id' => $this->regularUser->id,
            'status' => 'draft',
            'nominal_amount' => 50000.00
        ]);

        $updateData = [
            'guarantee_type' => $guarantee->guarantee_type,
            'nominal_amount' => 75000.00,
            'nominal_amount_currency' => $guarantee->nominal_amount_currency,
            'expiry_date' => $guarantee->expiry_date->format('Y-m-d'),
            'applicant_name' => $guarantee->applicant_name,
            'applicant_address' => $guarantee->applicant_address,
            'beneficiary_name' => $guarantee->beneficiary_name,
            'beneficiary_address' => $guarantee->beneficiary_address,
        ];

        $response = $this->put(route('guarantees.update', $guarantee->id), $updateData);

        $response->assertStatus(302);
        $response->assertRedirect(route('guarantees.show', $guarantee->id));
        $this->assertDatabaseHas('guarantees', [
            'id' => $guarantee->id,
            'nominal_amount' => 75000.00
        ]);
    }

    /** @test */
    public function user_cannot_update_others_guarantee()
    {
        $this->actingAs($this->regularUser);

        $otherUser = User::factory()->create(['role' => 'user']);
        $guarantee = Guarantee::factory()->create([
            'user_id' => $otherUser->id,
            'status' => 'draft'
        ]);

        $updateData = [
            'guarantee_type' => $guarantee->guarantee_type,
            'nominal_amount' => 75000.00,
            'nominal_amount_currency' => $guarantee->nominal_amount_currency,
            'expiry_date' => $guarantee->expiry_date->format('Y-m-d'),
            'applicant_name' => $guarantee->applicant_name,
            'applicant_address' => $guarantee->applicant_address,
            'beneficiary_name' => $guarantee->beneficiary_name,
            'beneficiary_address' => $guarantee->beneficiary_address,
        ];

        $response = $this->put(route('guarantees.update', $guarantee->id), $updateData);

        $response->assertStatus(302);
        $response->assertRedirect(route('guarantees.index'));
        $this->assertDatabaseHas('guarantees', [
            'id' => $guarantee->id,
            'nominal_amount' => $guarantee->nominal_amount
        ]);
    }

    /** @test */
    public function user_can_submit_own_guarantee_for_review()
    {
        $this->actingAs($this->regularUser);

        $guarantee = Guarantee::factory()->create([
            'user_id' => $this->regularUser->id,
            'status' => 'draft'
        ]);

        $response = $this->post(route('guarantees.submit-for-review', $guarantee->id));

        $response->assertStatus(302);
        $response->assertRedirect(route('guarantees.show', $guarantee->id));
        $this->assertDatabaseHas('guarantees', [
            'id' => $guarantee->id,
            'status' => 'review'
        ]);
        $this->assertDatabaseHas('reviews', [
            'guarantee_id' => $guarantee->id
        ]);
    }

    /** @test */
    public function admin_can_apply_guarantee_in_review()
    {
        $this->actingAs($this->adminUser);

        $guarantee = Guarantee::factory()->create([
            'user_id' => $this->regularUser->id,
            'status' => 'review'
        ]);

        Review::factory()->create([
            'guarantee_id' => $guarantee->id
        ]);

        $response = $this->post(route('guarantees.apply', $guarantee->id));

        $response->assertStatus(302);
        $response->assertRedirect(route('guarantees.show', $guarantee->id));
        $this->assertDatabaseHas('guarantees', [
            'id' => $guarantee->id,
            'status' => 'applied'
        ]);
    }

    /** @test */
    public function user_cannot_apply_guarantee()
    {
        $this->actingAs($this->regularUser);

        $guarantee = Guarantee::factory()->create([
            'user_id' => $this->regularUser->id,
            'status' => 'review'
        ]);

        Review::factory()->create([
            'guarantee_id' => $guarantee->id
        ]);

        $response = $this->post(route('guarantees.apply', $guarantee->id));

        $response->assertStatus(403); // Forbidden
        $this->assertDatabaseHas('guarantees', [
            'id' => $guarantee->id,
            'status' => 'review'
        ]);
    }

    /** @test */
    public function admin_can_issue_applied_guarantee()
    {
        $this->actingAs($this->adminUser);

        $guarantee = Guarantee::factory()->create([
            'user_id' => $this->regularUser->id,
            'status' => 'applied'
        ]);

        $response = $this->post(route('guarantees.issue', $guarantee->id));

        $response->assertStatus(302);
        $response->assertRedirect(route('guarantees.show', $guarantee->id));
        $this->assertDatabaseHas('guarantees', [
            'id' => $guarantee->id,
            'status' => 'issued'
        ]);
    }

    /** @test */
    public function admin_can_reject_guarantee()
    {
        $this->actingAs($this->adminUser);

        $guarantee = Guarantee::factory()->create([
            'user_id' => $this->regularUser->id,
            'status' => 'review'
        ]);

        Review::factory()->create([
            'guarantee_id' => $guarantee->id
        ]);

        $response = $this->post(route('guarantees.reject', $guarantee->id), [
            'review_notes' => 'Rejected for testing purposes'
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('guarantees.show', $guarantee->id));
        $this->assertDatabaseHas('guarantees', [
            'id' => $guarantee->id,
            'status' => 'rejected'
        ]);
        $this->assertDatabaseHas('reviews', [
            'guarantee_id' => $guarantee->id,
            'review_notes' => 'Rejected for testing purposes',
            'reviewer_id' => $this->adminUser->id
        ]);
    }

    /** @test */
    public function user_can_delete_own_draft_guarantee()
    {
        $this->actingAs($this->regularUser);

        $guarantee = Guarantee::factory()->create([
            'user_id' => $this->regularUser->id,
            'status' => 'draft'
        ]);

        $response = $this->delete(route('guarantees.destroy', $guarantee->id));

        $response->assertStatus(302);
        $response->assertRedirect(route('guarantees.index'));
        $this->assertDatabaseMissing('guarantees', [
            'id' => $guarantee->id
        ]);
    }

    /** @test */
    public function user_can_delete_own_rejected_guarantee()
    {
        $this->actingAs($this->regularUser);

        $guarantee = Guarantee::factory()->create([
            'user_id' => $this->regularUser->id,
            'status' => 'rejected'
        ]);

        $response = $this->delete(route('guarantees.destroy', $guarantee->id));

        $response->assertStatus(302);
        $response->assertRedirect(route('guarantees.index'));
        $this->assertDatabaseMissing('guarantees', [
            'id' => $guarantee->id
        ]);
    }

    /** @test */
    public function user_cannot_delete_own_guarantee_in_process()
    {
        $this->actingAs($this->regularUser);

        $statusesToTest = ['review', 'applied', 'issued'];

        foreach ($statusesToTest as $status) {
            $guarantee = Guarantee::factory()->create([
                'user_id' => $this->regularUser->id,
                'status' => $status
            ]);

            $response = $this->delete(route('guarantees.destroy', $guarantee->id));

            $response->assertStatus(302);
            $response->assertRedirect(route('guarantees.show', $guarantee->id));
            $this->assertDatabaseHas('guarantees', [
                'id' => $guarantee->id,
                'status' => $status
            ]);
        }
    }

    /** @test */
    public function admin_can_delete_any_draft_or_rejected_guarantee()
    {
        $this->actingAs($this->adminUser);

        $draftGuarantee = Guarantee::factory()->create([
            'user_id' => $this->regularUser->id,
            'status' => 'draft'
        ]);

        $rejectedGuarantee = Guarantee::factory()->create([
            'user_id' => $this->regularUser->id,
            'status' => 'rejected'
        ]);

        $response1 = $this->delete(route('guarantees.destroy', $draftGuarantee->id));
        $response1->assertStatus(302);
        $this->assertDatabaseMissing('guarantees', [
            'id' => $draftGuarantee->id
        ]);

        $response2 = $this->delete(route('guarantees.destroy', $rejectedGuarantee->id));
        $response2->assertStatus(302);
        $this->assertDatabaseMissing('guarantees', [
            'id' => $rejectedGuarantee->id
        ]);
    }
}