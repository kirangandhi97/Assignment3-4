@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Files') }}</span>
                    <a href="{{ route('files.create') }}" class="btn text-white rounded-pill px-4" style="background-color: #84c5f4;">Upload New File</a>

                </div>

                <div class="card-body">
                    @if ($files->isEmpty())
                        <p>No files found.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Filename</th>
                                        <th>Type</th>
                                        <th>Uploaded By</th>
                                        <th>Upload Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($files as $file)
                                        <tr>
                                            <td>{{ $file->filename }}</td>
                                            <td>{{ strtoupper($file->file_type) }}</td>
                                            <td>{{ $file->user->name }}</td>
                                            <td>{{ $file->created_at->format('Y-m-d H:i:s') }}</td>
                                            <td>
                                                <span class="badge bg-{{ $file->status == 'uploaded' ? 'warning' : ($file->status == 'processed' ? 'success' : 'danger') }}">
                                                    {{ ucfirst($file->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('files.show', $file->id) }}" class="btn btn-sm btn-info">View</a>
                                                    
                                                    @if (Auth::user()->isAdmin() && $file->isUploaded())
                                                        <form method="POST" action="{{ route('files.process', $file->id) }}" class="ms-1">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Are you sure you want to process this file?')">
                                                                Process
                                                            </button>
                                                        </form>
                                                    @endif
                                                    
                                                    @if (Auth::user()->isAdmin() || $file->user_id == Auth::id())
                                                        <form method="POST" action="{{ route('files.destroy', $file->id) }}" class="ms-1">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this file? This action cannot be undone.')">
                                                                Delete
                                                            </button>
                                                        </form>
                                                    @endif
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