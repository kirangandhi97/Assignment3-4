@extends('layouts.app')

@section('content')
<div class="container py-5" style="background-color: #f0f4f8;">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card border-0 shadow rounded-4" style="background-color: #ffffff;">
                {{-- Header --}}
                <div class="card-header d-flex justify-content-between align-items-center bg-white border-0">
                    <h5 class="fw-semibold text-dark mb-0">{{ __('File Details') }}</h5>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('files.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
                            Back to List
                        </a>

                        @if (Auth::user()->isAdmin() && $file->isUploaded())
                            <form method="POST" action="{{ route('files.process', $file->id) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success rounded-pill px-3"
                                        onclick="return confirm('Are you sure you want to process this file?')">
                                    Process File
                                </button>
                            </form>
                        @endif

                        @if (Auth::user()->isAdmin() || $file->user_id == Auth::id())
                            <form method="POST" action="{{ route('files.destroy', $file->id) }}" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger rounded-pill px-3"
                                        onclick="return confirm('Are you sure you want to delete this file? This action cannot be undone.')">
                                    Delete File
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                {{-- Body --}}
                <div class="card-body px-4 py-4">
                    <div class="row mb-5">
                        {{-- File Info --}}
                        <div class="col-md-6">
                            <h6 class="fw-semibold text-dark mb-3">File Information</h6>
                            <table class="table table-bordered table-sm">
                                <tbody>
                                    <tr><th>Filename:</th><td>{{ $file->filename }}</td></tr>
                                    <tr><th>File Type:</th><td>{{ strtoupper($file->file_type) }}</td></tr>
                                    <tr><th>Status:</th>
                                        <td>
                                            <span class="badge bg-{{ $file->status == 'uploaded' ? 'warning' : ($file->status == 'processed' ? 'success' : 'danger') }}">
                                                {{ ucfirst($file->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr><th>Uploaded By:</th><td>{{ $file->user->name }}</td></tr>
                                    <tr><th>Upload Date:</th><td>{{ $file->created_at->format('Y-m-d H:i:s') }}</td></tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- Processing Info --}}
                        <div class="col-md-6">
                            <h6 class="fw-semibold text-dark mb-3">Processing Information</h6>
                            <table class="table table-bordered table-sm">
                                <tbody>
                                    <tr>
                                        <th>Processing Status:</th>
                                        <td>
                                            @if ($file->isUploaded())
                                                <span class="text-warning">Pending Processing</span>
                                            @elseif ($file->isProcessed())
                                                <span class="text-success">Processed Successfully</span>
                                            @else
                                                <span class="text-danger">Processing Failed</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Processing Notes:</th>
                                        <td>{{ $file->processing_notes ?: 'No processing notes available' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Last Updated:</th>
                                        <td>{{ $file->updated_at->format('Y-m-d H:i:s') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- File Preview --}}
                    <div class="row">
                        <div class="col-md-12">
                            <h6 class="fw-semibold text-dark mb-3">File Preview</h6>

                            {{-- Action Buttons --}}
                            <div class="mb-3 d-flex gap-2 flex-wrap">
                                <a href="{{ route('files.view-content', $file->id) }}" target="_blank"
                                   class="btn btn-outline-primary rounded-pill px-4">
                                    <i class="fas fa-eye"></i> View Full Content
                                </a>
                                <a href="{{ route('files.download-content', $file->id) }}"
                                   class="btn btn-outline-success rounded-pill px-4">
                                    <i class="fas fa-download"></i> Download File
                                </a>
                            </div>

                            {{-- Preview Box --}}
                            <div class="border p-3 rounded bg-light" style="white-space: pre-wrap;">
                                @php
                                    $isTextFile = in_array(strtolower($file->file_type), ['csv', 'json', 'xml', 'txt']);
                                    if ($isTextFile) {
                                        $preview = mb_substr($file->file_contents, 0, 1000, 'UTF-8');
                                        $hasMore = mb_strlen($file->file_contents, 'UTF-8') > 1000;
                                    } else {
                                        $preview = "[Binary file content - preview not available]";
                                        $hasMore = false;
                                    }
                                @endphp
                                <pre class="mb-0">{{ $preview }}{{ $hasMore ? '...' : '' }}</pre>
                            </div>
                            <div class="small text-muted mt-2">
                                Note: Preview may be truncated for large files. Use the buttons above to view or download the full content.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>    
@endsection
