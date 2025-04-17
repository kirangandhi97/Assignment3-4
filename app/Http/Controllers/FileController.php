<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Interfaces\FileInterface;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class FileController extends Controller
{
    protected $fileRepository;

    /**
     * Create a new controller instance.
     *
     * @param \App\Interfaces\FileInterface $fileRepository
     * @return void
     */
    public function __construct(FileInterface $fileRepository)
    {
        $this->middleware('auth');
        $this->fileRepository = $fileRepository;
    }

    /**
     * Display a listing of the files.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index(): View
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            $files = $this->fileRepository->getAllFiles();
        } else {
            $files = $this->fileRepository->getFilesByUser($user->id);
        }

        return view('files.index', compact('files'));
    }

    /**
     * Show the form for uploading a new file.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create(): View
    {
        return view('files.create');
    }

    /**
     * Store a newly uploaded file in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:csv,json,xml|max:10240',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $file = $this->fileRepository->storeFile($request->file('file'), auth()->id());

        return redirect()->route('files.index')
            ->with('success', 'File uploaded successfully');
    }

    /**
     * Display the specified file.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show($id): View|RedirectResponse
    {
        $file = $this->fileRepository->getFileById($id);

        // Check if user has permission to view this file
        if (!auth()->user()->isAdmin() && $file->user_id !== auth()->id()) {
            return redirect()->route('files.index')
                ->with('error', 'You do not have permission to view this file');
        }

        return view('files.show', compact('file'));
    }

    /**
     * Process the uploaded file.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function process($id): RedirectResponse
    {
        // Only admin users can process files
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('files.show', $id)
                ->with('error', 'Only admin users can process files');
        }

        $file = $this->fileRepository->getFileById($id);

        // Only uploaded files can be processed
        if (!$file->isUploaded()) {
            return redirect()->route('files.show', $id)
                ->with('error', 'This file has already been processed or failed');
        }

        $results = $this->fileRepository->processFile($id);

        if ($results === false) {
            return redirect()->route('files.show', $id)
                ->with('error', 'File processing failed');
        }


        if (is_array($results) && isset($results['success']) && isset($results['failed'])) {
            return redirect()->route('files.show', $id)
                ->with('success', 'File processed successfully: ' . $results['success'] . ' guarantees created, ' . $results['failed'] . ' failed');
        } else {
            return redirect()->route('files.show', $id)
                ->with('success', 'File processed successfully');
        }
    }

    /**
     * Remove the specified file from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id): RedirectResponse
    {
        $file = $this->fileRepository->getFileById($id);

        // Check if user has permission to delete this file
        if (!auth()->user()->isAdmin() && $file->user_id !== auth()->id()) {
            return redirect()->route('files.index')
                ->with('error', 'You do not have permission to delete this file');
        }

        $this->fileRepository->deleteFile($id);

        return redirect()->route('files.index')
            ->with('success', 'File deleted successfully');
    }



    /**
     * View the full content of the file.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function viewContent($id): Response|RedirectResponse
    {
        $file = $this->fileRepository->getFileById($id);

        // Check if user has permission to view this file
        if (!auth()->user()->isAdmin() && $file->user_id !== auth()->id()) {
            return redirect()->route('files.index')
                ->with('error', 'You do not have permission to view this file');
        }

        $contentType = $this->getContentType($file->file_type);

        return response($file->file_contents)->header('Content-Type', $contentType);
    }

    /**
     * Download the file content.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function downloadContent($id): Response|RedirectResponse
    {
        $file = $this->fileRepository->getFileById($id);

        // Check if user has permission to download this file
        if (!auth()->user()->isAdmin() && $file->user_id !== auth()->id()) {
            return redirect()->route('files.index')
                ->with('error', 'You do not have permission to download this file');
        }

        $contentType = $this->getContentType($file->file_type);

        return response($file->file_contents)
            ->header('Content-Type', $contentType)
            ->header('Content-Disposition', 'attachment; filename="' . $file->filename . '"');
    }


    /**
     * Get content type based on file type.
     *
     * @param  string  $fileType
     * @return string
     */
    private function getContentType($fileType)
    {
        switch (strtolower($fileType)) {
            case 'csv':
                return 'text/csv';
            case 'json':
                return 'application/json';
            case 'xml':
                return 'application/xml';
            default:
                return 'text/plain';
        }
    }
}