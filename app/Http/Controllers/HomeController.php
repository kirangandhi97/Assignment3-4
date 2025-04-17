<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Interfaces\GuaranteeInterface;
use App\Interfaces\FileInterface;

class HomeController extends Controller
{
    protected $guaranteeRepository;
    protected $fileRepository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(GuaranteeInterface $guaranteeRepository, FileInterface $fileRepository)
    {
        $this->middleware('auth');
        $this->guaranteeRepository = $guaranteeRepository;
        $this->fileRepository = $fileRepository;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            // For admin, get all guarantees and files
            $guarantees = $this->guaranteeRepository->getAllGuarantees();
            $files = $this->fileRepository->getAllFiles();
        } else {
            // For regular users, get only their guarantees and files
            $guarantees = $this->guaranteeRepository->getGuaranteesByUser($user->id);
            $files = $this->fileRepository->getFilesByUser($user->id);
        }
        
        return view('home', compact('guarantees', 'files'));
    }
}