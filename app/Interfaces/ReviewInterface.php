<?php

namespace App\Interfaces;

interface ReviewInterface
{
    /**
     * Get all reviews
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllReviews();

    /**
     * Get review by guarantee ID
     * 
     * @param int $guaranteeId
     * @return \App\Models\Review
     */
    public function getReviewByGuarantee($guaranteeId);

    /**
     * Create review for guarantee
     * 
     * @param int $guaranteeId
     * @param array $reviewDetails
     * @return \App\Models\Review
     */
    public function createReview($guaranteeId, array $reviewDetails = []);

    /**
     * Update review
     * 
     * @param int $id
     * @param array $reviewDetails
     * @return bool
     */
    public function updateReview($id, array $reviewDetails);

    /**
     * Delete review
     * 
     * @param int $id
     * @return bool
     */
    public function deleteReview($id);
}