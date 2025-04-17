@extends('layouts.app')

@section('content')
<div class="container py-5" style="background-color: #f0f4f8;">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card border-0 shadow rounded-4" style="background-color: #ffffff;">
                {{-- Header --}}
                <div class="card-header d-flex justify-content-between align-items-center bg-white border-0">
                    <h5 class="fw-semibold text-dark mb-0">{{ __('File Processing') }}</h5>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary rounded-pill px-3">
                        {{ __('Back to Dashboard') }}
                    </a>
                </div>

                {{-- Body --}}
                <div class="card-body px-4 py-4">
                    @if ($pendingFiles->isEmpty())
                        <div class="alert alert-info mb-0 rounded" style="background-color: #e6f0ff; color: #0c5460;">
                            {{ __('No files pending processing.') }}
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Filename</th>
                                        <th>Type</th>
                                        <th>Uploaded By</th>
                                        <th>Upload Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pendingFiles as $file)
                                        <tr>
                                            <td>{{ $file->filename }}</td>
                                            <td>{{ strtoupper($file->file_type) }}</td>
                                            <td>{{ $file->user->name }}</td>
                                            <td>{{ $file->created_at->format('Y-m-d H:i:s') }}</td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('files.show', $file->id) }}"
                                                       class="btn btn-sm btn-outline-info rounded-pill px-3">
                                                        View
                                                    </a>
                                                    <form method="POST" action="{{ route('files.process', $file->id) }}">
                                                        @csrf
                                                        <button type="submit"
                                                                class="btn btn-sm btn-success rounded-pill px-3"
                                                                onclick="return confirm('Are you sure you want to process this file?')">
                                                            Process
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
