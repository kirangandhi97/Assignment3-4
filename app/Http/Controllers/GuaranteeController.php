<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Interfaces\GuaranteeInterface;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class GuaranteeController extends Controller
{
    protected $guaranteeRepository;

    /**
     * Create a new controller instance.
     *
     * @param \App\Interfaces\GuaranteeInterface $guaranteeRepository
     * @return void
     */
    public function __construct(GuaranteeInterface $guaranteeRepository)
    {
        $this->middleware('auth');
        $this->guaranteeRepository = $guaranteeRepository;
    }

    /**
     * Display a listing of the guarantees.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index(): View
    {
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            $guarantees = $this->guaranteeRepository->getAllGuarantees();
            return view('guarantees.index', compact('guarantees'));
        } else {
            $guarantees = $this->guaranteeRepository->getGuaranteesByUser($user->id);
            return view('guarantees.index', compact('guarantees'));
        }
    }

    /**
     * Show the form for creating a new guarantee.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create(): View
    {
        return view('guarantees.create');
    }

    /**
     * Store a newly created guarantee in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            // 'corporate_reference_number' => 'required|string|unique:guarantees,corporate_reference_number',
            'guarantee_type' => 'required|in:Bank,Bid Bond,Insurance,Surety',
            'nominal_amount' => 'required|numeric|min:0',
            'nominal_amount_currency' => 'required|string|size:3',
            'expiry_date' => 'required|date|after_or_equal:' . Carbon::now()->format('Y-m-d'),
            'applicant_name' => 'required|string',
            'applicant_address' => 'required|string',
            'beneficiary_name' => 'required|string',
            'beneficiary_address' => 'required|string',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Add user_id to the request data
        $guaranteeData = $request->all();
        $guaranteeData['user_id'] = auth()->id();
        $guaranteeData['status'] = 'draft';
        
        $guarantee = $this->guaranteeRepository->createGuarantee($guaranteeData);
        
        return redirect()->route('guarantees.show', $guarantee->id)
            ->with('success', 'Guarantee created successfully');
    }

    /**
     * Display the specified guarantee.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show($id): View|RedirectResponse
    {
        $guarantee = $this->guaranteeRepository->getGuaranteeById($id);
        
        // Check if user has permission to view this guarantee
        if (!auth()->user()->isAdmin() && $guarantee->user_id !== auth()->id()) {
            return redirect()->route('guarantees.index')
                ->with('error', 'You do not have permission to view this guarantee');
        }
        
        return view('guarantees.show', compact('guarantee'));
    }

    /**
     * Show the form for editing the specified guarantee.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit($id): View|RedirectResponse
    {
        $guarantee = $this->guaranteeRepository->getGuaranteeById($id);
        
        // Check if user has permission to edit this guarantee
        if (!auth()->user()->isAdmin() && $guarantee->user_id !== auth()->id()) {
            return redirect()->route('guarantees.index')
                ->with('error', 'You do not have permission to edit this guarantee');
        }
        
        // Only draft guarantees can be edited
        if (!$guarantee->isDraft()) {
            return redirect()->route('guarantees.show', $guarantee->id)
                ->with('error', 'Only draft guarantees can be edited');
        }
        
        return view('guarantees.edit', compact('guarantee'));
    }

    /**
     * Update the specified guarantee in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $guarantee = $this->guaranteeRepository->getGuaranteeById($id);
        
        // Check if user has permission to update this guarantee
        if (!auth()->user()->isAdmin() && $guarantee->user_id !== auth()->id()) {
            return redirect()->route('guarantees.index')
                ->with('error', 'You do not have permission to update this guarantee');
        }
        
        // Only draft guarantees can be updated
        if (!$guarantee->isDraft()) {
            return redirect()->route('guarantees.show', $guarantee->id)
                ->with('error', 'Only draft guarantees can be updated');
        }
        
        $validator = Validator::make($request->all(), [
            'guarantee_type' => 'required|in:Bank,Bid Bond,Insurance,Surety',
            'nominal_amount' => 'required|numeric|min:0',
            'nominal_amount_currency' => 'required|string|size:3',
            'expiry_date' => 'required|date|after_or_equal:' . Carbon::now()->format('Y-m-d'),
            'applicant_name' => 'required|string',
            'applicant_address' => 'required|string',
            'beneficiary_name' => 'required|string',
            'beneficiary_address' => 'required|string',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $this->guaranteeRepository->updateGuarantee($id, $request->all());
        
        return redirect()->route('guarantees.show', $id)
            ->with('success', 'Guarantee updated successfully');
    }

    /**
     * Submit the guarantee for review.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submitForReview($id): RedirectResponse
    {
        $guarantee = $this->guaranteeRepository->getGuaranteeById($id);
        
        // Check if user has permission to submit this guarantee for review
        if ($guarantee->user_id !== auth()->id()) {
            return redirect()->route('guarantees.show', $id)
                ->with('error', 'You do not have permission to submit this guarantee for review');
        }
        
        // Only draft guarantees can be submitted for review
        if (!$guarantee->isDraft()) {
            return redirect()->route('guarantees.show', $id)
                ->with('error', 'Only draft guarantees can be submitted for review');
        }
        
        $this->guaranteeRepository->submitForReview($id);
        
        return redirect()->route('guarantees.show', $id)
            ->with('success', 'Guarantee submitted for review');
    }

    /**
     * Apply for the guarantee.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function applyGuarantee($id): RedirectResponse
    {
        // Only admin users can apply for guarantees
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('guarantees.show', $id)
                ->with('error', 'Only admin users can apply for guarantees');
        }
        
        $guarantee = $this->guaranteeRepository->getGuaranteeById($id);
        
        // Only guarantees in review can be applied
        if (!$guarantee->isInReview()) {
            return redirect()->route('guarantees.show', $id)
                ->with('error', 'Only guarantees in review can be applied');
        }
        
        $this->guaranteeRepository->applyGuarantee($id);
        
        return redirect()->route('guarantees.show', $id)
            ->with('success', 'Guarantee applied successfully');
    }

    /**
     * Issue the guarantee.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function issueGuarantee($id): RedirectResponse
    {
        // Only admin users can issue guarantees
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('guarantees.show', $id)
                ->with('error', 'Only admin users can issue guarantees');
        }
        
        $guarantee = $this->guaranteeRepository->getGuaranteeById($id);
        
        // Only applied guarantees can be issued
        if (!$guarantee->isApplied()) {
            return redirect()->route('guarantees.show', $id)
                ->with('error', 'Only applied guarantees can be issued');
        }
        
        $this->guaranteeRepository->issueGuarantee($id);
        
        return redirect()->route('guarantees.show', $id)
            ->with('success', 'Guarantee issued successfully');
    }

    /**
     * Reject the guarantee.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function rejectGuarantee(Request $request, $id): RedirectResponse
    {
        // Only admin users can reject guarantees
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('guarantees.show', $id)
                ->with('error', 'Only admin users can reject guarantees');
        }
        
        $guarantee = $this->guaranteeRepository->getGuaranteeById($id);
        
        // Only guarantees in review or applied can be rejected
        if (!$guarantee->isInReview() && !$guarantee->isApplied()) {
            return redirect()->route('guarantees.show', $id)
                ->with('error', 'Only guarantees in review or applied can be rejected');
        }
        
        $validator = Validator::make($request->all(), [
            'review_notes' => 'required|string',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $this->guaranteeRepository->rejectGuarantee($id, $request->review_notes);
        
        return redirect()->route('guarantees.show', $id)
            ->with('success', 'Guarantee rejected');
    }

    /**
     * Remove the specified guarantee from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id): RedirectResponse
    {
        $guarantee = $this->guaranteeRepository->getGuaranteeById($id);
        
        // Check if user has permission to delete this guarantee
        if (!auth()->user()->isAdmin() && $guarantee->user_id !== auth()->id()) {
            return redirect()->route('guarantees.index')
                ->with('error', 'You do not have permission to delete this guarantee');
        }
        
        // Only draft or rejected guarantees can be deleted
        if (!$guarantee->isDraft() && !$guarantee->isRejected()) {
            return redirect()->route('guarantees.show', $id)
                ->with('error', 'Only draft or rejected guarantees can be deleted');
        }
        
        $this->guaranteeRepository->deleteGuarantee($id);
        
        return redirect()->route('guarantees.index')
            ->with('success', 'Guarantee deleted successfully');
    }
}