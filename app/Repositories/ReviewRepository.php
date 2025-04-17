<?php

namespace App\Repositories;

use App\Interfaces\ReviewInterface;
use App\Models\Review;

class ReviewRepository implements ReviewInterface
{
    /**
     * Get all reviews
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllReviews()
    {
        return Review::with(['guarantee', 'guarantee.user', 'reviewer'])->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get review by guarantee ID
     * 
     * @param int $guaranteeId
     * @return \App\Models\Review
     */
    public function getReviewByGuarantee($guaranteeId)
    {
        return Review::where('guarantee_id', $guaranteeId)->first();
    }

    /**
     * Create review for guarantee
     * 
     * @param int $guaranteeId
     * @param array $reviewDetails
     * @return \App\Models\Review
     */
    public function createReview($guaranteeId, array $reviewDetails = [])
    {
        $reviewData = array_merge(['guarantee_id' => $guaranteeId], $reviewDetails);
        return Review::create($reviewData);
    }

    /**
     * Update review
     * 
     * @param int $id
     * @param array $reviewDetails
     * @return bool
     */
    public function updateReview($id, array $reviewDetails)
    {
        $review = Review::findOrFail($id);
        return $review->update($reviewDetails);
    }

    /**
     * Delete review
     * 
     * @param int $id
     * @return bool
     */
    public function deleteReview($id)
    {
        $review = Review::findOrFail($id);
        return $review->delete();
    }
}