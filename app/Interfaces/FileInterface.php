<?php

namespace App\Interfaces;

use Illuminate\Http\UploadedFile;

interface FileInterface
{
    /**
     * Get all files
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllFiles();

    /**
     * Get files by user ID
     * 
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getFilesByUser($userId);

    /**
     * Get file by ID
     * 
     * @param int $id
     * @return \App\Models\File
     */
    public function getFileById($id);

    /**
     * Store uploaded file
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @param int $userId
     * @return \App\Models\File
     */
    public function storeFile(UploadedFile $file, $userId);

    /**
     * Delete file
     * 
     * @param int $id
     * @return bool
     */
    public function deleteFile($id);

    /**
     * Parse CSV file
     * 
     * @param \App\Models\File $file
     * @return array
     */
    public function parseCSV($file);

    /**
     * Parse JSON file
     * 
     * @param \App\Models\File $file
     * @return array
     */
    public function parseJSON($file);

    /**
     * Parse XML file
     * 
     * @param \App\Models\File $file
     * @return array
     */
    public function parseXML($file);

    /**
     * Process file by ID
     * 
     * @param int $id
     * @return bool
     */
    public function processFile($id);

    /**
     * Mark file as processed
     * 
     * @param int $id
     * @param string $notes
     * @return bool
     */
    public function markAsProcessed($id, $notes = '');

    /**
     * Mark file as failed
     * 
     * @param int $id
     * @param string $errorMessage
     * @return bool
     */
    public function markAsFailed($id, $errorMessage);
}