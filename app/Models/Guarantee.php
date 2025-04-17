<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guarantee extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'corporate_reference_number',
        'guarantee_type',
        'nominal_amount',
        'nominal_amount_currency',
        'expiry_date',
        'applicant_name',
        'applicant_address',
        'beneficiary_name',
        'beneficiary_address',
        'status',
        'user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'expiry_date' => 'date',
        'nominal_amount' => 'decimal:2',
    ];

    /**
     * Get the user that created the guarantee
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the review associated with the guarantee
     */
    public function review()
    {
        return $this->hasOne(Review::class);
    }

    /**
     * Check if guarantee is in draft status
     */
    public function isDraft()
    {
        return $this->status === 'draft';
    }

    /**
     * Check if guarantee is in review status
     */
    public function isInReview()
    {
        return $this->status === 'review';
    }

    /**
     * Check if guarantee is in applied status
     */
    public function isApplied()
    {
        return $this->status === 'applied';
    }

    /**
     * Check if guarantee is in issued status
     */
    public function isIssued()
    {
        return $this->status === 'issued';
    }

    /**
     * Check if guarantee is in rejected status
     */
    public function isRejected()
    {
        return $this->status === 'rejected';
    }
}