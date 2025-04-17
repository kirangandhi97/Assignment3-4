<?php

namespace Database\Factories;

use App\Models\File;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = File::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $fileTypes = ['csv', 'json', 'xml'];
        $statuses = ['uploaded', 'processed', 'failed'];
        
        $fileType = $this->faker->randomElement($fileTypes);
        $sampleContents = $this->getSampleContent($fileType);
        
        return [
            'filename' => $this->faker->word . '.' . $fileType,
            'file_type' => $fileType,
            'file_contents' => $sampleContents,
            'user_id' => User::factory(),
            'status' => $this->faker->randomElement($statuses),
            'processing_notes' => $this->faker->optional(0.5)->sentence,
        ];
    }

    /**
     * Set the file's status to uploaded.
     *
     * @return Factory
     */
    public function uploaded()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'uploaded',
                'processing_notes' => null,
            ];
        });
    }

    /**
     * Set the file's status to processed.
     *
     * @return Factory
     */
    public function processed()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'processed',
                'processing_notes' => $this->faker->sentence,
            ];
        });
    }

    /**
     * Set the file's status to failed.
     *
     * @return Factory
     */
    public function failed()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'failed',
                'processing_notes' => 'Error: ' . $this->faker->sentence,
            ];
        });
    }

    /**
     * Generate sample content based on file type.
     *
     * @param string $fileType
     * @return mixed
     */
    protected function getSampleContent($fileType)
    {
        switch ($fileType) {
            case 'csv':
                return "corporate_reference_number,guarantee_type,nominal_amount,nominal_amount_currency,expiry_date,applicant_name,applicant_address,beneficiary_name,beneficiary_address\n" .
                       "TFG-" . date('Ymd') . "-ABC123,Bank,50000.00,USD,2025-12-31,\"Sample Corp\",\"123 Test St\",\"Test Ltd\",\"456 Sample St\"";
            
            case 'json':
                $data = [
                    [
                        "corporate_reference_number" => "TFG-" . date('Ymd') . "-ABC123",
                        "guarantee_type" => "Bank",
                        "nominal_amount" => 50000.00,
                        "nominal_amount_currency" => "USD",
                        "expiry_date" => "2025-12-31",
                        "applicant_name" => "Sample Corp",
                        "applicant_address" => "123 Test St",
                        "beneficiary_name" => "Test Ltd",
                        "beneficiary_address" => "456 Sample St"
                    ]
                ];
                return json_encode($data);
            
            case 'xml':
                return '<?xml version="1.0" encoding="UTF-8"?>' .
                       '<guarantees>' .
                       '<guarantee>' .
                       '<corporate_reference_number>TFG-' . date('Ymd') . '-ABC123</corporate_reference_number>' .
                       '<guarantee_type>Bank</guarantee_type>' .
                       '<nominal_amount>50000.00</nominal_amount>' .
                       '<nominal_amount_currency>USD</nominal_amount_currency>' .
                       '<expiry_date>2025-12-31</expiry_date>' .
                       '<applicant_name>Sample Corp</applicant_name>' .
                       '<applicant_address>123 Test St</applicant_address>' .
                       '<beneficiary_name>Test Ltd</beneficiary_name>' .
                       '<beneficiary_address>456 Sample St</beneficiary_address>' .
                       '</guarantee>' .
                       '</guarantees>';
            
            default:
                return "Sample content";
        }
    }
}