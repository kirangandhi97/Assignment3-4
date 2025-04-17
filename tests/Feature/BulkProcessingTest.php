<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\File;
use App\Models\Guarantee;
use Carbon\Carbon;

class BulkProcessingTest extends TestCase
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
    public function admin_can_process_csv_file_with_valid_data()
    {
        $this->actingAs($this->adminUser);

        // Create a CSV file with valid data
        $csvContent = "corporate_reference_number,guarantee_type,nominal_amount,nominal_amount_currency,expiry_date,applicant_name,applicant_address,beneficiary_name,beneficiary_address\n";
        $csvContent .= "TFG-20250501-ABC123,Bank,50000.00,USD,2025-12-31,\"Global Trading Corp\",\"123 Commerce St, New York, NY 10001, USA\",\"International Suppliers Ltd\",\"456 Export Ave, London, EC2R 8AH, UK\"\n";
        $csvContent .= "TFG-20250502-DEF456,\"Bid Bond\",25000.00,EUR,2025-09-15,\"European Construction Group\",\"78 Boulevard Saint-Michel, 75006 Paris, France\",\"City of Berlin\",\"Rathausstraße 15, 10178 Berlin, Germany\"";
        
        $file = File::create([
            'filename' => 'valid_data.csv',
            'file_type' => 'csv',
            'file_contents' => $csvContent,
            'user_id' => $this->regularUser->id,
            'status' => 'uploaded'
        ]);

        $response = $this->post(route('files.process', $file->id));

        $response->assertStatus(302);
        $response->assertRedirect(route('files.show', $file->id));
        
        // File should be marked as processed
        $this->assertDatabaseHas('files', [
            'id' => $file->id,
            'status' => 'processed'
        ]);
        
        // Guarantees should be created
        $this->assertDatabaseHas('guarantees', [
            'corporate_reference_number' => 'TFG-20250501-ABC123',
            'guarantee_type' => 'Bank',
            'nominal_amount' => 50000.00,
            'nominal_amount_currency' => 'USD',
            'status' => 'draft'
        ]);
        
        $this->assertDatabaseHas('guarantees', [
            'corporate_reference_number' => 'TFG-20250502-DEF456',
            'guarantee_type' => 'Bid Bond',
            'nominal_amount' => 25000.00,
            'nominal_amount_currency' => 'EUR',
            'status' => 'draft'
        ]);
    }

    /** @test */
    public function admin_can_process_json_file_with_valid_data()
    {
        $this->actingAs($this->adminUser);

        // Create a JSON file with valid data
        $jsonData = [
            [
                "corporate_reference_number" => "TFG-20250601-GHI789",
                "guarantee_type" => "Insurance",
                "nominal_amount" => 100000.00,
                "nominal_amount_currency" => "CAD",
                "expiry_date" => "2026-03-31",
                "applicant_name" => "North American Shipping Inc",
                "applicant_address" => "500 Lakeshore Blvd, Toronto, ON M5V 2V9, Canada",
                "beneficiary_name" => "Pacific Marine Insurers",
                "beneficiary_address" => "888 Harbor Dr, Vancouver, BC V6C 3E8, Canada"
            ],
            [
                "corporate_reference_number" => "TFG-20250602-JKL012",
                "guarantee_type" => "Surety",
                "nominal_amount" => 75000.00,
                "nominal_amount_currency" => "GBP",
                "expiry_date" => "2025-11-30",
                "applicant_name" => "UK Construction Partners",
                "applicant_address" => "45 Oxford Street, London, W1D 2DZ, UK",
                "beneficiary_name" => "Scotland Development Authority",
                "beneficiary_address" => "100 Royal Mile, Edinburgh, EH1 1SG, Scotland"
            ]
        ];
        
        $file = File::create([
            'filename' => 'valid_data.json',
            'file_type' => 'json',
            'file_contents' => json_encode($jsonData),
            'user_id' => $this->regularUser->id,
            'status' => 'uploaded'
        ]);

        $response = $this->post(route('files.process', $file->id));

        $response->assertStatus(302);
        $response->assertRedirect(route('files.show', $file->id));
        
        // File should be marked as processed
        $this->assertDatabaseHas('files', [
            'id' => $file->id,
            'status' => 'processed'
        ]);
        
        // Guarantees should be created
        $this->assertDatabaseHas('guarantees', [
            'corporate_reference_number' => 'TFG-20250601-GHI789',
            'guarantee_type' => 'Insurance',
            'nominal_amount' => 100000.00,
            'nominal_amount_currency' => 'CAD',
            'status' => 'draft'
        ]);
        
        $this->assertDatabaseHas('guarantees', [
            'corporate_reference_number' => 'TFG-20250602-JKL012',
            'guarantee_type' => 'Surety',
            'nominal_amount' => 75000.00,
            'nominal_amount_currency' => 'GBP',
            'status' => 'draft'
        ]);
    }

    /** @test */
    public function admin_can_process_xml_file_with_valid_data()
    {
        $this->actingAs($this->adminUser);

        // Create an XML file with valid data
        $xmlContent = '<?xml version="1.0" encoding="UTF-8"?>
        <guarantees>
          <guarantee>
            <corporate_reference_number>TFG-20250701-MNO345</corporate_reference_number>
            <guarantee_type>Bank</guarantee_type>
            <nominal_amount>200000.00</nominal_amount>
            <nominal_amount_currency>USD</nominal_amount_currency>
            <expiry_date>2026-02-28</expiry_date>
            <applicant_name>American Export Company</applicant_name>
            <applicant_address>1200 Market St, Philadelphia, PA 19107, USA</applicant_address>
            <beneficiary_name>Asian Import Consortium</beneficiary_name>
            <beneficiary_address>88 Connaught Road, Central, Hong Kong</beneficiary_address>
          </guarantee>
          <guarantee>
            <corporate_reference_number>TFG-20250702-PQR678</corporate_reference_number>
            <guarantee_type>Bid Bond</guarantee_type>
            <nominal_amount>30000.00</nominal_amount>
            <nominal_amount_currency>AUD</nominal_amount_currency>
            <expiry_date>2025-08-15</expiry_date>
            <applicant_name>Australian Engineering Services</applicant_name>
            <applicant_address>42 Collins Street, Melbourne, VIC 3000, Australia</applicant_address>
            <beneficiary_name>Sydney Metro Authority</beneficiary_name>
            <beneficiary_address>33 George Street, Sydney, NSW 2000, Australia</beneficiary_address>
          </guarantee>
        </guarantees>';
        
        $file = File::create([
            'filename' => 'valid_data.xml',
            'file_type' => 'xml',
            'file_contents' => $xmlContent,
            'user_id' => $this->regularUser->id,
            'status' => 'uploaded'
        ]);

        $response = $this->post(route('files.process', $file->id));

        $response->assertStatus(302);
        $response->assertRedirect(route('files.show', $file->id));
        
        // File should be marked as processed
        $this->assertDatabaseHas('files', [
            'id' => $file->id,
            'status' => 'processed'
        ]);
        
        // Guarantees should be created
        $this->assertDatabaseHas('guarantees', [
            'corporate_reference_number' => 'TFG-20250701-MNO345',
            'guarantee_type' => 'Bank',
            'nominal_amount' => 200000.00,
            'nominal_amount_currency' => 'USD',
            'status' => 'draft'
        ]);
        
        $this->assertDatabaseHas('guarantees', [
            'corporate_reference_number' => 'TFG-20250702-PQR678',
            'guarantee_type' => 'Bid Bond',
            'nominal_amount' => 30000.00,
            'nominal_amount_currency' => 'AUD',
            'status' => 'draft'
        ]);
    }

    /** @test */
    public function admin_cannot_process_file_with_invalid_data()
    {
        $this->actingAs($this->adminUser);

        // Create a CSV file with invalid data (missing required fields)
        $csvContent = "corporate_reference_number,guarantee_type,nominal_amount\n"; // Missing many required fields
        $csvContent .= "TFG-20250801-STU901,Insurance,150000.00";
        
        $file = File::create([
            'filename' => 'invalid_data.csv',
            'file_type' => 'csv',
            'file_contents' => $csvContent,
            'user_id' => $this->regularUser->id,
            'status' => 'uploaded'
        ]);

        $response = $this->post(route('files.process', $file->id));

        $response->assertStatus(302);
        $response->assertRedirect(route('files.show', $file->id));
        
        // File should be marked as processed but with errors
        $this->assertDatabaseHas('files', [
            'id' => $file->id,
            'status' => 'processed'
        ]);
        
        // No guarantees should be created
        $this->assertDatabaseMissing('guarantees', [
            'corporate_reference_number' => 'TFG-20250801-STU901'
        ]);
    }

    /** @test */
    public function admin_cannot_process_file_with_expired_dates()
    {
        $this->actingAs($this->adminUser);

        // Create a CSV file with past expiry date
        $csvContent = "corporate_reference_number,guarantee_type,nominal_amount,nominal_amount_currency,expiry_date,applicant_name,applicant_address,beneficiary_name,beneficiary_address\n";
        $csvContent .= "TFG-20250901-VWX234,Surety,45000.00,EUR,2020-10-31,\"German Infrastructure GmbH\",\"Friedrichstraße 123, 10117 Berlin, Germany\",\"Munich Municipal Authority\",\"Marienplatz 8, 80331 Munich, Germany\"";
        
        $file = File::create([
            'filename' => 'invalid_date.csv',
            'file_type' => 'csv',
            'file_contents' => $csvContent,
            'user_id' => $this->regularUser->id,
            'status' => 'uploaded'
        ]);

        $response = $this->post(route('files.process', $file->id));

        $response->assertStatus(302);
        $response->assertRedirect(route('files.show', $file->id));
        
        // File should be marked as processed but with errors
        $this->assertDatabaseHas('files', [
            'id' => $file->id,
            'status' => 'processed'
        ]);
        
        // No guarantees should be created due to expired date
        $this->assertDatabaseMissing('guarantees', [
            'corporate_reference_number' => 'TFG-20250901-VWX234'
        ]);
    }

    /** @test */
    public function admin_cannot_process_file_with_duplicate_reference_numbers()
    {
        $this->actingAs($this->adminUser);

        // First create a guarantee with a specific reference number
        Guarantee::create([
            'corporate_reference_number' => 'TFG-20251001-YZA567',
            'guarantee_type' => 'Bank',
            'nominal_amount' => 80000.00,
            'nominal_amount_currency' => 'CHF',
            'expiry_date' => Carbon::now()->addYear(),
            'applicant_name' => 'Swiss Precision Instruments AG',
            'applicant_address' => 'Bahnhofstrasse 45, 8001 Zurich, Switzerland',
            'beneficiary_name' => 'European Medical Supplies',
            'beneficiary_address' => 'Avenue de la Gare 10, 1003 Lausanne, Switzerland',
            'user_id' => $this->regularUser->id,
            'status' => 'draft'
        ]);

        // Now try to create a file with the same reference number
        $csvContent = "corporate_reference_number,guarantee_type,nominal_amount,nominal_amount_currency,expiry_date,applicant_name,applicant_address,beneficiary_name,beneficiary_address\n";
        $csvContent .= "TFG-20251001-YZA567,Bank,90000.00,CHF,2026-04-30,\"Swiss Banking AG\",\"Bahnhofstrasse 50, 8001 Zurich, Switzerland\",\"Global Medical Inc\",\"Avenue de la Gare 15, 1003 Lausanne, Switzerland\"";
        
        $file = File::create([
            'filename' => 'duplicate_reference.csv',
            'file_type' => 'csv',
            'file_contents' => $csvContent,
            'user_id' => $this->regularUser->id,
            'status' => 'uploaded'
        ]);

        $response = $this->post(route('files.process', $file->id));

        $response->assertStatus(302);
        $response->assertRedirect(route('files.show', $file->id));
        
        // File should be marked as processed but with errors
        $this->assertDatabaseHas('files', [
            'id' => $file->id,
            'status' => 'processed'
        ]);
        
        // There should be only one guarantee with that reference number
        $this->assertEquals(1, Guarantee::where('corporate_reference_number', 'TFG-20251001-YZA567')->count());
        
        // And its amount should be the original amount, not the new one
        $this->assertDatabaseHas('guarantees', [
            'corporate_reference_number' => 'TFG-20251001-YZA567',
            'nominal_amount' => 80000.00
        ]);
    }

    /** @test */
    public function admin_can_process_file_with_mixed_valid_invalid_data()
    {
        $this->actingAs($this->adminUser);

        // Create a CSV file with both valid and invalid data
        $csvContent = "corporate_reference_number,guarantee_type,nominal_amount,nominal_amount_currency,expiry_date,applicant_name,applicant_address,beneficiary_name,beneficiary_address\n";
        // Valid record
        $csvContent .= "TFG-20251101-BCD890,\"Bid Bond\",60000.00,USD,2025-12-15,\"American Engineering Solutions\",\"500 Fifth Avenue, New York, NY 10110, USA\",\"California State Department\",\"1315 10th Street, Sacramento, CA 95814, USA\"\n";
        // Invalid record (past expiry date)
        $csvContent .= "TFG-20251102-EFG123,Insurance,120000.00,SGD,2020-11-15,\"Singapore Logistics Pte Ltd\",\"10 Anson Road, Singapore 079903\",\"Asia Pacific Maritime Ltd\",\"1 Marina Boulevard, Singapore 018989\"\n";
        // Valid record
        $csvContent .= "TFG-20251103-HIJ456,Surety,90000.00,CAD,2025-09-30,\"Canadian Development Corporation\",\"200 Bay Street, Toronto, ON M5J 2J2, Canada\",\"Alberta Infrastructure Department\",\"10800 97 Avenue, Edmonton, AB T5K 2B6, Canada\"";
        
        $file = File::create([
            'filename' => 'mixed_data.csv',
            'file_type' => 'csv',
            'file_contents' => $csvContent,
            'user_id' => $this->regularUser->id,
            'status' => 'uploaded'
        ]);

        $response = $this->post(route('files.process', $file->id));

        $response->assertStatus(302);
        $response->assertRedirect(route('files.show', $file->id));
        
        // File should be marked as processed
        $this->assertDatabaseHas('files', [
            'id' => $file->id,
            'status' => 'processed'
        ]);
        
        // Only valid guarantees should be created
        $this->assertDatabaseHas('guarantees', [
            'corporate_reference_number' => 'TFG-20251101-BCD890',
            'guarantee_type' => 'Bid Bond',
            'nominal_amount' => 60000.00,
            'nominal_amount_currency' => 'USD'
        ]);
        
        $this->assertDatabaseHas('guarantees', [
            'corporate_reference_number' => 'TFG-20251103-HIJ456',
            'guarantee_type' => 'Surety',
            'nominal_amount' => 90000.00,
            'nominal_amount_currency' => 'CAD'
        ]);
        
        // Invalid guarantee should not be created
        $this->assertDatabaseMissing('guarantees', [
            'corporate_reference_number' => 'TFG-20251102-EFG123'
        ]);
    }

    /** @test */
    public function cannot_process_already_processed_file()
    {
        $this->actingAs($this->adminUser);

        // Create a file that's already been processed
        $file = File::create([
            'filename' => 'already_processed.csv',
            'file_type' => 'csv',
            'file_contents' => 'some content',
            'user_id' => $this->regularUser->id,
            'status' => 'processed',
            'processing_notes' => 'Already processed'
        ]);

        $response = $this->post(route('files.process', $file->id));

        $response->assertStatus(302);
        $response->assertRedirect(route('files.show', $file->id));
        $response->assertSessionHas('error');
    }

    /** @test */
    public function cannot_process_failed_file()
    {
        $this->actingAs($this->adminUser);

        // Create a file that's already failed processing
        $file = File::create([
            'filename' => 'failed_file.csv',
            'file_type' => 'csv',
            'file_contents' => 'some content',
            'user_id' => $this->regularUser->id,
            'status' => 'failed',
            'processing_notes' => 'Failed to process'
        ]);

        $response = $this->post(route('files.process', $file->id));

        $response->assertStatus(302);
        $response->assertRedirect(route('files.show', $file->id));
        $response->assertSessionHas('error');
    }

    /** @test */
    public function user_can_download_sample_files()
    {
        $this->actingAs($this->regularUser);

        // Test CSV sample
        $response = $this->get(route('samples.csv'));
        $response->assertStatus(200);
        $this->assertTrue(
            strpos($response->headers->get('Content-Type'), 'text/csv') !== false,
            'Content-Type header does not contain text/csv'
        );
        $response->assertHeader('Content-Disposition', 'attachment; filename="sample_guarantees.csv"');

        // Test JSON sample
        $response = $this->get(route('samples.json'));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');
        $response->assertHeader('Content-Disposition', 'attachment; filename="sample_guarantees.json"');

        // Test XML sample
        $response = $this->get(route('samples.xml'));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/xml');
        $response->assertHeader('Content-Disposition', 'attachment; filename="sample_guarantees.xml"');
    }
}