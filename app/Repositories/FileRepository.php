<?php

namespace App\Repositories;

use App\Interfaces\FileInterface;
use App\Interfaces\GuaranteeInterface;
use App\Models\File;
use Illuminate\Http\UploadedFile;
use SimpleXMLElement;

class FileRepository implements FileInterface
{
    protected $guaranteeRepository;

    /**
     * Constructor
     * 
     * @param \App\Interfaces\GuaranteeInterface $guaranteeRepository
     */
    public function __construct(GuaranteeInterface $guaranteeRepository)
    {
        $this->guaranteeRepository = $guaranteeRepository;
    }

    /**
     * Get all files
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllFiles()
    {
        return File::with('user')->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get files by user ID
     * 
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getFilesByUser($userId)
    {
        return File::where('user_id', $userId)->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get file by ID
     * 
     * @param int $id
     * @return \App\Models\File
     */
    public function getFileById($id)
    {
        return File::findOrFail($id);
    }

 /**
 * Store uploaded file
 * 
 * @param \Illuminate\Http\UploadedFile $file
 * @param int $userId
 * @return \App\Models\File
 */
public function storeFile(UploadedFile $file, $userId)
{
    $fileContents = file_get_contents($file->getRealPath());
    $fileType = $file->getClientOriginalExtension();
    
    return File::create([
        'filename' => $file->getClientOriginalName(),
        'file_type' => $fileType,
        'file_contents' => $fileContents, // Store binary data directly
        'user_id' => $userId,
        'status' => 'uploaded',
    ]);
}

    /**
     * Delete file
     * 
     * @param int $id
     * @return bool
     */
    public function deleteFile($id)
    {
        $file = $this->getFileById($id);
        return $file->delete();
    }

/**
 * Parse CSV file
 * 
 * @param \App\Models\File $file
 * @return array
 */
public function parseCSV($file)
{
    $data = [];
    
    // Create temp file
    $tempFile = tempnam(sys_get_temp_dir(), 'csv_');
    file_put_contents($tempFile, $file->file_contents);
    
    // Parse CSV
    if (($handle = fopen($tempFile, "r")) !== false) {
        // Read header row
        $header = fgetcsv($handle, 1000, ",");
        
        // Read data rows
        while (($row = fgetcsv($handle, 1000, ",")) !== false) {
            $rowData = [];
            
            foreach ($header as $index => $columnName) {
                if (isset($row[$index])) {
                    // Convert header to snake_case
                    $key = strtolower(str_replace(' ', '_', $columnName));
                    $rowData[$key] = $row[$index];
                }
            }
            
            // Map the data to our guarantee fields
            $guaranteeData = $this->mapDataToGuaranteeModel($rowData);
            if (!empty($guaranteeData)) {
                $data[] = $guaranteeData;
            }
        }
        fclose($handle);
    }
    
    // Remove temp file
    unlink($tempFile);
    
    return $data;
}

    /**
 * Parse JSON file
 * 
 * @param \App\Models\File $file
 * @return array
 */
public function parseJSON($file)
{
    $data = [];
    
    // Parse JSON
    $jsonData = json_decode($file->file_contents, true);
    
    if (is_array($jsonData)) {
        foreach ($jsonData as $item) {
            // Map the data to our guarantee fields
            $guaranteeData = $this->mapDataToGuaranteeModel($item);
            if (!empty($guaranteeData)) {
                $data[] = $guaranteeData;
            }
        }
    }
    
    return $data;
}


  /**
 * Parse XML file
 * 
 * @param \App\Models\File $file
 * @return array
 */
public function parseXML($file)
{
    $data = [];
    
    // Parse XML
    try {
        $xml = new SimpleXMLElement($file->file_contents);
        
        foreach ($xml->guarantee as $guarantee) {
            $item = [];
            
            foreach ($guarantee as $key => $value) {
                $item[(string)$key] = (string)$value;
            }
            
            // Map the data to our guarantee fields
            $guaranteeData = $this->mapDataToGuaranteeModel($item);
            if (!empty($guaranteeData)) {
                $data[] = $guaranteeData;
            }
        }
    } catch (\Exception $e) {
        // Log error or handle exception
        return [];
    }
    
    return $data;
}

    /**
     * Process file by ID
     * 
     * @param int $id
     * @return bool|array
     */
    public function processFile($id)
    {
        $file = $this->getFileById($id);
        
        // Check if file is already processed
        if ($file->isProcessed()) {
            return false;
        }
        
        try {
            // Parse file based on type
            $guarantees = [];
            
            switch (strtolower($file->file_type)) {
                case 'csv':
                    $guarantees = $this->parseCSV($file);
                    break;
                case 'json':
                    $guarantees = $this->parseJSON($file);
                    break;
                case 'xml':
                    $guarantees = $this->parseXML($file);
                    break;
                default:
                    $this->markAsFailed($file->id, 'Unsupported file type');
                    return false;
            }
            
            // Process guarantees
            $results = $this->guaranteeRepository->processGuarantees($guarantees, $file->user_id);
            
            // Update file status
            if ($results['failed'] === 0) {
                $this->markAsProcessed($file->id, 'Successfully processed ' . $results['success'] . ' guarantees');
            } else {
                $errorSummary = 'Processed with errors: ' . $results['success'] . ' successful, ' . $results['failed'] . ' failed';
                $this->markAsProcessed($file->id, $errorSummary);
            }
            
            return $results;
        } catch (\Exception $e) {
            $this->markAsFailed($file->id, $e->getMessage());
            return false;
        }
    }

    /**
     * Mark file as processed
     * 
     * @param int $id
     * @param string $notes
     * @return bool
     */
    public function markAsProcessed($id, $notes = '')
    {
        $file = $this->getFileById($id);
        return $file->update([
            'status' => 'processed',
            'processing_notes' => $notes,
        ]);
    }

    /**
     * Mark file as failed
     * 
     * @param int $id
     * @param string $errorMessage
     * @return bool
     */
    public function markAsFailed($id, $errorMessage)
    {
        $file = $this->getFileById($id);
        return $file->update([
            'status' => 'failed',
            'processing_notes' => $errorMessage,
        ]);
    }

    /**
     * Map data array to guarantee model fields
     * 
     * @param array $data
     * @return array
     */
    protected function mapDataToGuaranteeModel(array $data)
    {
        $mapping = [
            // CSV/JSON/XML field => Guarantee model field
            'corporate_reference_number' => 'corporate_reference_number',
            'reference_number' => 'corporate_reference_number',
            'ref_number' => 'corporate_reference_number',
            'reference' => 'corporate_reference_number',
            
            'guarantee_type' => 'guarantee_type',
            'type' => 'guarantee_type',
            
            'nominal_amount' => 'nominal_amount',
            'amount' => 'nominal_amount',
            
            'nominal_amount_currency' => 'nominal_amount_currency',
            'currency' => 'nominal_amount_currency',
            
            'expiry_date' => 'expiry_date',
            'expiry' => 'expiry_date',
            
            'applicant_name' => 'applicant_name',
            'applicant' => 'applicant_name',
            
            'applicant_address' => 'applicant_address',
            
            'beneficiary_name' => 'beneficiary_name',
            'beneficiary' => 'beneficiary_name',
            
            'beneficiary_address' => 'beneficiary_address',
        ];
        
        $result = [];
        
        foreach ($mapping as $sourceField => $targetField) {
            if (isset($data[$sourceField]) && !isset($result[$targetField])) {
                $result[$targetField] = $data[$sourceField];
            }
        }
        
        // Check if we have all required fields
        $requiredFields = [
            'corporate_reference_number',
            'guarantee_type',
            'nominal_amount',
            'nominal_amount_currency',
            'expiry_date',
            'applicant_name',
            'applicant_address',
            'beneficiary_name',
            'beneficiary_address',
        ];
        
        foreach ($requiredFields as $field) {
            if (!isset($result[$field])) {
                return []; // Missing required field
            }
        }
        
        return $result;
    }
}