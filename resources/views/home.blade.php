@extends('layouts.app')

@section('content')
<div class="container py-5" style="background-color: #f0f4f8;">
    {{-- Main Card --}}
    <div class="card border-0 shadow rounded-4" style="background-color: #ffffff;">
        <div class="card-header bg-white border-0">
            <h5 class="fw-semibold text-dark mb-0">{{ __('Dashboard') }}</h5>
        </div>

        <div class="card-body px-4 py-4">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <div class="row g-4">
                {{-- Guarantees Section --}}
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-3 h-100">
                        <div class="card-header d-flex justify-content-between align-items-center bg-light">
                            <strong class="text-dark">{{ __('Your Guarantees') }}</strong>
                            <a href="{{ route('guarantees.create') }}"
                               class="btn btn-sm text-white rounded-pill px-3"
                               style="background-color: #84c5f4;">
                                Create New
                            </a>
                        </div>
                        <div class="card-body">
                            @if ($guarantees->isEmpty())
                                <p class="text-muted">No guarantees found.</p>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Reference</th>
                                                <th>Type</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($guarantees->take(5) as $guarantee)
                                                <tr>
                                                    <td>{{ $guarantee->corporate_reference_number }}</td>
                                                    <td>{{ $guarantee->guarantee_type }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ match($guarantee->status) {
                                                            'draft' => 'secondary',
                                                            'review' => 'info',
                                                            'applied' => 'primary',
                                                            'issued' => 'success',
                                                            default => 'danger',
                                                        } }}">
                                                            {{ ucfirst($guarantee->status) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('guarantees.show', $guarantee->id) }}" class="btn btn-sm btn-info">View</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @if($guarantees->count() > 5)
                                    <div class="text-center mt-3">
                                        <a href="{{ route('guarantees.index') }}" class="btn btn-outline-primary">View All</a>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Files Section --}}
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-3 h-100">
                        <div class="card-header d-flex justify-content-between align-items-center bg-light">
                            <strong class="text-dark">{{ __('Your Files') }}</strong>
                            <a href="{{ route('files.create') }}"
                               class="btn btn-sm text-white rounded-pill px-3"
                               style="background-color: #84c5f4;">
                                Upload New
                            </a>
                        </div>
                        <div class="card-body">
                            @if ($files->isEmpty())
                                <p class="text-muted">No files found.</p>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Filename</th>
                                                <th>Type</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($files->take(5) as $file)
                                                <tr>
                                                    <td>{{ $file->filename }}</td>
                                                    <td>{{ strtoupper($file->file_type) }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $file->status == 'uploaded' ? 'warning' : ($file->status == 'processed' ? 'success' : 'danger') }}">
                                                            {{ ucfirst($file->status) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('files.show', $file->id) }}" class="btn btn-sm btn-info">View</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @if($files->count() > 5)
                                    <div class="text-center mt-3">
                                        <a href="{{ route('files.index') }}" class="btn btn-outline-primary">View All</a>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div> {{-- End row --}}
        </div> {{-- End card body --}}
    </div> {{-- End main card --}}
</div> {{-- End container --}}
@endsection
