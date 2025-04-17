<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\File;

class FileUploadTest extends TestCase
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
        
        // Set up fake storage
        Storage::fake('local');
    }

    /** @test */
    public function user_can_upload_csv_file()
    {
        $this->actingAs($this->regularUser);

        $csvContent = "corporate_reference_number,guarantee_type,nominal_amount,nominal_amount_currency,expiry_date,applicant_name,applicant_address,beneficiary_name,beneficiary_address\n";
        $csvContent .= "TFG-20250501-ABC123,Bank,50000.00,USD,2025-12-31,\"Global Trading Corp\",\"123 Commerce St, New York, NY 10001, USA\",\"International Suppliers Ltd\",\"456 Export Ave, London, EC2R 8AH, UK\"";
        
        $file = UploadedFile::fake()->createWithContent(
            'test_data.csv',
            $csvContent
        );

        $response = $this->post(route('files.store'), [
            'file' => $file,
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('files.index'));
        
        $this->assertDatabaseHas('files', [
            'filename' => 'test_data.csv',
            'file_type' => 'csv',
            'user_id' => $this->regularUser->id,
            'status' => 'uploaded'
        ]);
    }

    /** @test */
    public function user_can_upload_json_file()
    {
        $this->actingAs($this->regularUser);

        $jsonContent = json_encode([
            [
                "corporate_reference_number" => "TFG-20250501-ABC123",
                "guarantee_type" => "Bank",
                "nominal_amount" => 50000.00,
                "nominal_amount_currency" => "USD",
                "expiry_date" => "2025-12-31",
                "applicant_name" => "Global Trading Corp",
                "applicant_address" => "123 Commerce St, New York, NY 10001, USA",
                "beneficiary_name" => "International Suppliers Ltd",
                "beneficiary_address" => "456 Export Ave, London, EC2R 8AH, UK"
            ]
        ]);
        
        $file = UploadedFile::fake()->createWithContent(
            'test_data.json',
            $jsonContent
        );

        $response = $this->post(route('files.store'), [
            'file' => $file,
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('files', [
            'filename' => 'test_data.json',
            'file_type' => 'json',
            'status' => 'uploaded'
        ]);
    }

    /** @test */
    public function user_can_upload_xml_file()
    {
        $this->actingAs($this->regularUser);

        $xmlContent = '<?xml version="1.0" encoding="UTF-8"?>
        <guarantees>
          <guarantee>
            <corporate_reference_number>TFG-20250501-ABC123</corporate_reference_number>
            <guarantee_type>Bank</guarantee_type>
            <nominal_amount>50000.00</nominal_amount>
            <nominal_amount_currency>USD</nominal_amount_currency>
            <expiry_date>2025-12-31</expiry_date>
            <applicant_name>Global Trading Corp</applicant_name>
            <applicant_address>123 Commerce St, New York, NY 10001, USA</applicant_address>
            <beneficiary_name>International Suppliers Ltd</beneficiary_name>
            <beneficiary_address>456 Export Ave, London, EC2R 8AH, UK</beneficiary_address>
          </guarantee>
        </guarantees>';
        
        $file = UploadedFile::fake()->createWithContent(
            'test_data.xml',
            $xmlContent
        );

        $response = $this->post(route('files.store'), [
            'file' => $file,
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('files', [
            'filename' => 'test_data.xml',
            'file_type' => 'xml',
            'status' => 'uploaded'
        ]);
    }

    /** @test */
    public function user_cannot_upload_unsupported_file_type()
    {
        $this->actingAs($this->regularUser);

        $file = UploadedFile::fake()->create('test_document.docx', 100, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

        $response = $this->post(route('files.store'), [
            'file' => $file,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('file');
        $this->assertDatabaseMissing('files', [
            'filename' => 'test_document.docx',
        ]);
    }

    /** @test */
    public function user_can_view_own_uploaded_file()
    {
        $this->actingAs($this->regularUser);

        $file = File::factory()->create([
            'user_id' => $this->regularUser->id,
            'status' => 'uploaded'
        ]);

        $response = $this->get(route('files.show', $file->id));

        $response->assertStatus(200);
        $response->assertSee($file->filename);
    }

    /** @test */
    public function user_cannot_view_others_file()
    {
        $this->actingAs($this->regularUser);

        $otherUser = User::factory()->create(['role' => 'user']);
        $file = File::factory()->create([
            'user_id' => $otherUser->id,
            'status' => 'uploaded'
        ]);

        $response = $this->get(route('files.show', $file->id));

        $response->assertStatus(302);
        $response->assertRedirect(route('files.index'));
        $response->assertSessionHas('error', 'You do not have permission to view this file');
    }

    /** @test */
    public function admin_can_view_any_file()
    {
        $this->actingAs($this->adminUser);

        $file = File::factory()->create([
            'user_id' => $this->regularUser->id,
            'status' => 'uploaded'
        ]);

        $response = $this->get(route('files.show', $file->id));

        $response->assertStatus(200);
        $response->assertSee($file->filename);
    }

    /** @test */
    public function admin_can_process_uploaded_file()
    {
        $this->actingAs($this->adminUser);

        // Create a CSV file
        $csvContent = "corporate_reference_number,guarantee_type,nominal_amount,nominal_amount_currency,expiry_date,applicant_name,applicant_address,beneficiary_name,beneficiary_address\n";
        $csvContent .= "TFG-20250501-ABC123,Bank,50000.00,USD,2025-12-31,\"Global Trading Corp\",\"123 Commerce St, New York, NY 10001, USA\",\"International Suppliers Ltd\",\"456 Export Ave, London, EC2R 8AH, UK\"";
        
        $file = File::factory()->create([
            'user_id' => $this->regularUser->id,
            'status' => 'uploaded',
            'file_type' => 'csv',
            'file_contents' => $csvContent
        ]);

        $response = $this->post(route('files.process', $file->id));

        $response->assertStatus(302);
        $response->assertRedirect(route('files.show', $file->id));
        
        // Check file status update
        $this->assertDatabaseHas('files', [
            'id' => $file->id,
            'status' => 'processed'
        ]);
        
        // Check if guarantee was created from CSV data
        $this->assertDatabaseHas('guarantees', [
            'corporate_reference_number' => 'TFG-20250501-ABC123',
            'guarantee_type' => 'Bank',
            'nominal_amount' => 50000.00,
            'nominal_amount_currency' => 'USD',
            'status' => 'draft'
        ]);
    }

    /** @test */
    public function user_cannot_process_uploaded_file()
    {
        $this->actingAs($this->regularUser);

        $file = File::factory()->create([
            'user_id' => $this->regularUser->id,
            'status' => 'uploaded'
        ]);

        $response = $this->post(route('files.process', $file->id));

        $response->assertStatus(403); // Forbidden
        $this->assertDatabaseHas('files', [
            'id' => $file->id,
            'status' => 'uploaded' // Status should not change
        ]);
    }

    /** @test */
    public function user_can_delete_own_file()
    {
        $this->actingAs($this->regularUser);

        $file = File::factory()->create([
            'user_id' => $this->regularUser->id,
            'status' => 'uploaded'
        ]);

        $response = $this->delete(route('files.destroy', $file->id));

        $response->assertStatus(302);
        $response->assertRedirect(route('files.index'));
        $this->assertDatabaseMissing('files', [
            'id' => $file->id
        ]);
    }

    /** @test */
    public function user_cannot_delete_others_file()
    {
        $this->actingAs($this->regularUser);

        $otherUser = User::factory()->create(['role' => 'user']);
        $file = File::factory()->create([
            'user_id' => $otherUser->id,
            'status' => 'uploaded'
        ]);

        $response = $this->delete(route('files.destroy', $file->id));

        $response->assertStatus(302);
        $response->assertRedirect(route('files.index'));
        $this->assertDatabaseHas('files', [
            'id' => $file->id
        ]);
    }

    /** @test */
    public function admin_can_delete_any_file()
    {
        $this->actingAs($this->adminUser);

        $file = File::factory()->create([
            'user_id' => $this->regularUser->id,
            'status' => 'uploaded'
        ]);

        $response = $this->delete(route('files.destroy', $file->id));

        $response->assertStatus(302);
        $response->assertRedirect(route('files.index'));
        $this->assertDatabaseMissing('files', [
            'id' => $file->id
        ]);
    }
    
    /** @test */
    public function user_can_view_file_content()
    {
        $this->actingAs($this->regularUser);

        $file = File::factory()->create([
            'user_id' => $this->regularUser->id,
            'status' => 'uploaded',
            'file_type' => 'csv',
            'file_contents' => 'test content'
        ]);

        $response = $this->get(route('files.view-content', $file->id));

        $response->assertStatus(200);
        $this->assertTrue(
            strpos($response->headers->get('Content-Type'), 'text/csv') !== false,
            'Content-Type header does not contain text/csv'
        );
        $this->assertEquals('test content', $response->getContent());
    }

    /** @test */
    public function user_can_download_own_file()
    {
        $this->actingAs($this->regularUser);

        $file = File::factory()->create([
            'user_id' => $this->regularUser->id,
            'status' => 'uploaded',
            'filename' => 'test.csv',
            'file_type' => 'csv',
            'file_contents' => 'test content'
        ]);

        $response = $this->get(route('files.download-content', $file->id));

        $response->assertStatus(200);
        $this->assertTrue(
            strpos($response->headers->get('Content-Type'), 'text/csv') !== false,
            'Content-Type header does not contain text/csv'
        );
        $response->assertHeader('Content-Disposition', 'attachment; filename="test.csv"');
        $this->assertEquals('test content', $response->getContent());
    }
}