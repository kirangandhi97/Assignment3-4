<?php

namespace App\Interfaces;

interface GuaranteeInterface
{
    /**
     * Get all guarantees
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllGuarantees();

    /**
     * Get guarantees by user ID
     * 
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getGuaranteesByUser($userId);

    /**
     * Get guarantee by ID
     * 
     * @param int $id
     * @return \App\Models\Guarantee
     */
    public function getGuaranteeById($id);

    /**
     * Create new guarantee
     * 
     * @param array $guaranteeDetails
     * @return \App\Models\Guarantee
     */
    public function createGuarantee(array $guaranteeDetails);

    /**
     * Update guarantee
     * 
     * @param int $id
     * @param array $guaranteeDetails
     * @return bool
     */
    public function updateGuarantee($id, array $guaranteeDetails);

    /**
     * Delete guarantee
     * 
     * @param int $id
     * @return bool
     */
    public function deleteGuarantee($id);

    /**
     * Submit guarantee for review
     * 
     * @param int $id
     * @return bool
     */
    public function submitForReview($id);

    /**
     * Apply for guarantee
     * 
     * @param int $id
     * @return bool
     */
    public function applyGuarantee($id);

    /**
     * Issue guarantee
     * 
     * @param int $id
     * @return bool
     */
    public function issueGuarantee($id);

    /**
     * Reject guarantee
     * 
     * @param int $id
     * @param string $notes
     * @return bool
     */
    public function rejectGuarantee($id, $notes);

    /**
     * Process guarantees from data array
     * 
     * @param array $guarantees
     * @param int $userId
     * @return array
     */
    public function processGuarantees(array $guarantees, $userId);
}
