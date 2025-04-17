@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Admin Dashboard') }}</div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">{{ __('Guarantee Status Overview') }}</div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <div class="card text-white bg-secondary">
                                                <div class="card-body text-center">
                                                    <h5 class="card-title">Draft</h5>
                                                    <p class="card-text display-4">{{ $guaranteesByStatus['draft'] }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="card text-white bg-info">
                                                <div class="card-body text-center">
                                                    <h5 class="card-title">Review</h5>
                                                    <p class="card-text display-4">{{ $guaranteesByStatus['review'] }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="card text-white bg-primary">
                                                <div class="card-body text-center">
                                                    <h5 class="card-title">Applied</h5>
                                                    <p class="card-text display-4">{{ $guaranteesByStatus['applied'] }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="card text-white bg-success">
                                                <div class="card-body text-center">
                                                    <h5 class="card-title">Issued</h5>
                                                    <p class="card-text display-4">{{ $guaranteesByStatus['issued'] }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="card text-white bg-danger">
                                                <div class="card-body text-center">
                                                    <h5 class="card-title">Rejected</h5>
                                                    <p class="card-text display-4">{{ $guaranteesByStatus['rejected'] }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-center mt-3">
                                        <a href="{{ route('guarantees.index') }}" class="btn btn-primary">View All Guarantees</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">{{ __('File Status Overview') }}</div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <div class="card text-white bg-warning">
                                                <div class="card-body text-center">
                                                    <h5 class="card-title">Uploaded</h5>
                                                    <p class="card-text display-4">{{ $filesByStatus['uploaded'] }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="card text-white bg-success">
                                                <div class="card-body text-center">
                                                    <h5 class="card-title">Processed</h5>
                                                    <p class="card-text display-4">{{ $filesByStatus['processed'] }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="card text-white bg-danger">
                                                <div class="card-body text-center">
                                                    <h5 class="card-title">Failed</h5>
                                                    <p class="card-text display-4">{{ $filesByStatus['failed'] }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-center mt-3">
                                        <a href="{{ route('files.index') }}" class="btn btn-primary">View All Files</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card mb-4">
                                <div class="card-header">{{ __('Quick Actions') }}</div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <div class="card">
                                                <div class="card-body text-center">
                                                    <h5 class="card-title">Pending Reviews</h5>
                                                    <p class="card-text display-4">{{ $guaranteesByStatus['review'] }}</p>
                                                    <a href="{{ route('admin.pending-reviews') }}" class="btn btn-info">View Pending Reviews</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="card">
                                                <div class="card-body text-center">
                                                    <h5 class="card-title">Files to Process</h5>
                                                    <p class="card-text display-4">{{ $filesByStatus['uploaded'] }}</p>
                                                    <a href="{{ route('admin.file-processing') }}" class="btn btn-warning">Process Files</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="card">
                                                <div class="card-body text-center">
                                                    <h5 class="card-title">Create Guarantee</h5>
                                                    <p class="card-text">Create a new guarantee manually.</p>
                                                    <a href="{{ route('guarantees.create') }}" class="btn btn-primary">Create Guarantee</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection