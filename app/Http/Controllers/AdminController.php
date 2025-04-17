<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Interfaces\GuaranteeInterface;
use App\Interfaces\FileInterface;
use App\Interfaces\ReviewInterface;

class AdminController extends Controller
{
    protected $guaranteeRepository;
    protected $fileRepository;
    protected $reviewRepository;

    /**
     * Create a new controller instance.
     *
     * @param \App\Interfaces\GuaranteeInterface $guaranteeRepository
     * @param \App\Interfaces\FileInterface $fileRepository
     * @param \App\Interfaces\ReviewInterface $reviewRepository
     * @return void
     */
    public function __construct(
        GuaranteeInterface $guaranteeRepository,
        FileInterface $fileRepository,
        ReviewInterface $reviewRepository
    ) {
        $this->middleware('auth');
        $this->middleware('admin');
        $this->guaranteeRepository = $guaranteeRepository;
        $this->fileRepository = $fileRepository;
        $this->reviewRepository = $reviewRepository;
    }

    /**
     * Show admin dashboard.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $guarantees = $this->guaranteeRepository->getAllGuarantees();
        $files = $this->fileRepository->getAllFiles();
        
        // Count guarantees by status
        $guaranteesByStatus = [
            'draft' => $guarantees->where('status', 'draft')->count(),
            'review' => $guarantees->where('status', 'review')->count(),
            'applied' => $guarantees->where('status', 'applied')->count(),
            'issued' => $guarantees->where('status', 'issued')->count(),
            'rejected' => $guarantees->where('status', 'rejected')->count(),
        ];
        
        // Count files by status
        $filesByStatus = [
            'uploaded' => $files->where('status', 'uploaded')->count(),
            'processed' => $files->where('status', 'processed')->count(),
            'failed' => $files->where('status', 'failed')->count(),
        ];
        
        return view('admin.dashboard', compact('guarantees', 'files', 'guaranteesByStatus', 'filesByStatus'));
    }

    /**
     * Show pending reviews.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function pendingReviews()
    {
        $pendingGuarantees = $this->guaranteeRepository->getAllGuarantees()
            ->where('status', 'review');
        
        return view('admin.pending-reviews', compact('pendingGuarantees'));
    }

    /**
     * Show file processing page.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function fileProcessing()
    {
        $pendingFiles = $this->fileRepository->getAllFiles()
            ->where('status', 'uploaded');
        
        return view('admin.file-processing', compact('pendingFiles'));
    }

    /**
     * Show review form for a guarantee.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showReviewForm($id)
    {
        $guarantee = $this->guaranteeRepository->getGuaranteeById($id);
        
        // Check if guarantee is in review
        if (!$guarantee->isInReview()) {
            return redirect()->route('admin.pending-reviews')
                ->with('error', 'Only guarantees in review can be reviewed');
        }
        
        return view('admin.review-form', compact('guarantee'));
    }
}