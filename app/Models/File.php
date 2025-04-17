<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'filename',
        'file_type',
        'file_contents',
        'user_id',
        'status',
        'processing_notes',
    ];

    /**
     * Get the user that uploaded the file
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if file is in uploaded status
     */
    public function isUploaded()
    {
        return $this->status === 'uploaded';
    }

    /**
     * Check if file is in processed status
     */
    public function isProcessed()
    {
        return $this->status === 'processed';
    }

    /**
     * Check if file processing failed
     */
    public function isFailed()
    {
        return $this->status === 'failed';
    }
}