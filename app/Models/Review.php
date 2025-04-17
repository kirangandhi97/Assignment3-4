<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'guarantee_id',
        'review_notes',
        'reviewer_id',
    ];

    /**
     * Get the guarantee that is being reviewed
     */
    public function guarantee()
    {
        return $this->belongsTo(Guarantee::class);
    }

    /**
     * Get the admin who reviewed the guarantee
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}