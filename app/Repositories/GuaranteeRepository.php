<?php

namespace App\Repositories;

use App\Interfaces\GuaranteeInterface;
use App\Models\Guarantee;
use App\Repositories\ReviewRepository;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class GuaranteeRepository implements GuaranteeInterface
{
    protected $reviewRepository;

    /**
     * Constructor
     * 
     * @param \App\Repositories\ReviewRepository $reviewRepository
     */
    public function __construct(ReviewRepository $reviewRepository)
    {
        $this->reviewRepository = $reviewRepository;
    }

    /**
     * Get all guarantees
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllGuarantees()
    {
        return Guarantee::with(['user', 'review'])->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get guarantees by user ID
     * 
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getGuaranteesByUser($userId)
    {
        return Guarantee::where('user_id', $userId)->with('review')->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get guarantee by ID
     * 
     * @param int $id
     * @return \App\Models\Guarantee
     */
    public function getGuaranteeById($id)
    {
        return Guarantee::with(['user', 'review'])->findOrFail($id);
    }


    /**
 * Generate a unique corporate reference number
 * 
 * @return string
 */
protected function generateReferenceNumber()
{
    $prefix = 'TFG';
    $date = date('Ymd');
    $randomPart = strtoupper(substr(md5(uniqid()), 0, 6));
    
    $referenceNumber = $prefix . '-' . $date . '-' . $randomPart;
    
    // Check if the generated reference number already exists
    while (Guarantee::where('corporate_reference_number', $referenceNumber)->exists()) {
        $randomPart = strtoupper(substr(md5(uniqid()), 0, 6));
        $referenceNumber = $prefix . '-' . $date . '-' . $randomPart;
    }
    
    return $referenceNumber;
}


    /**
     * Create new guarantee
     * 
     * @param array $guaranteeDetails
     * @return \App\Models\Guarantee
     */
    public function createGuarantee(array $guaranteeDetails)
    {
        if (!isset($guaranteeDetails['corporate_reference_number'])) {
            $guaranteeDetails['corporate_reference_number'] = $this->generateReferenceNumber();
        }

        return Guarantee::create($guaranteeDetails);
    }

    /**
     * Update guarantee
     * 
     * @param int $id
     * @param array $guaranteeDetails
     * @return bool
     */
    public function updateGuarantee($id, array $guaranteeDetails)
    {
        $guarantee = $this->getGuaranteeById($id);
        
        // Ensure that corporate_reference_number is not changed
        if (isset($guaranteeDetails['corporate_reference_number'])) {
            unset($guaranteeDetails['corporate_reference_number']);
        }
        
        return $guarantee->update($guaranteeDetails);
    }

    /**
     * Delete guarantee
     * 
     * @param int $id
     * @return bool
     */
    public function deleteGuarantee($id)
    {
        $guarantee = $this->getGuaranteeById($id);
        
        // Only allow deletion of draft or rejected guarantees
        if (!$guarantee->isDraft() && !$guarantee->isRejected()) {
            return false;
        }
        
        return $guarantee->delete();
    }

    /**
     * Submit guarantee for review
     * 
     * @param int $id
     * @return bool
     */
    public function submitForReview($id)
    {
        $guarantee = $this->getGuaranteeById($id);
        
        // Only draft guarantees can be submitted for review
        if (!$guarantee->isDraft()) {
            return false;
        }
        
        // Update guarantee status
        $guarantee->status = 'review';
        $result = $guarantee->save();
        
        // Create review entry
        if ($result) {
            $this->reviewRepository->createReview($guarantee->id);
        }
        
        return $result;
    }

    /**
     * Apply for guarantee
     * 
     * @param int $id
     * @return bool
     */
    public function applyGuarantee($id)
    {
        $guarantee = $this->getGuaranteeById($id);
        
        // Only guarantees in review can be applied
        if (!$guarantee->isInReview()) {
            return false;
        }
        
        $guarantee->status = 'applied';
        return $guarantee->save();
    }

    /**
     * Issue guarantee
     * 
     * @param int $id
     * @return bool
     */
    public function issueGuarantee($id)
    {
        $guarantee = $this->getGuaranteeById($id);
        
        // Only applied guarantees can be issued
        if (!$guarantee->isApplied()) {
            return false;
        }
        
        $guarantee->status = 'issued';
        $result = $guarantee->save();
    
        // Update review with reviewer information
        if ($result && $guarantee->review) {
            $this->reviewRepository->updateReview($guarantee->review->id, [
                'reviewer_id' => auth()->id(),
            ]);
        }
        
        return $result;
    }

    /**
     * Reject guarantee
     * 
     * @param int $id
     * @param string $notes
     * @return bool
     */
    public function rejectGuarantee($id, $notes)
    {
        $guarantee = $this->getGuaranteeById($id);
        
        // Only guarantees in review or applied can be rejected
        if (!$guarantee->isInReview() && !$guarantee->isApplied()) {
            return false;
        }
        
        $guarantee->status = 'rejected';
        $result = $guarantee->save();
        
        // Update review notes
        if ($result && $guarantee->review) {
            $this->reviewRepository->updateReview($guarantee->review->id, [
                'review_notes' => $notes,
                'reviewer_id' => auth()->id(),
            ]);
        }
        
        return $result;
    }

    /**
     * Process guarantees from data array
     * 
     * @param array $guarantees
     * @param int $userId
     * @return array
     */
    public function processGuarantees(array $guarantees, $userId)
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => []
        ];
        
        foreach ($guarantees as $index => $guaranteeData) {
            // Add user ID to data
            $guaranteeData['user_id'] = $userId;
            $guaranteeData['status'] = 'draft';
            
            // Validate data
            $validator = Validator::make($guaranteeData, [
                'corporate_reference_number' => 'required|string|unique:guarantees,corporate_reference_number',
                'guarantee_type' => 'required|in:Bank,Bid Bond,Insurance,Surety',
                'nominal_amount' => 'required|numeric|min:0',
                'nominal_amount_currency' => 'required|string|size:3',
                'expiry_date' => 'required|date|after_or_equal:' . Carbon::now()->format('Y-m-d'),
                'applicant_name' => 'required|string',
                'applicant_address' => 'required|string',
                'beneficiary_name' => 'required|string',
                'beneficiary_address' => 'required|string',
                'user_id' => 'required|exists:users,id',
            ]);
            
            if ($validator->fails()) {
                $results['failed']++;
                $results['errors'][] = [
                    'row' => $index + 1,
                    'errors' => $validator->errors()->toArray(),
                    'data' => $guaranteeData
                ];
                continue;
            }
            
            // Create guarantee
            try {
                $this->createGuarantee($guaranteeData);
                $results['success']++;
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = [
                    'row' => $index + 1,
                    'errors' => ['exception' => $e->getMessage()],
                    'data' => $guaranteeData
                ];
            }
        }
        
        return $results;
    }
}