@extends('layouts.app')

@section('content')
<div class="container py-5" style="background-color: #f0f4f8;">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow rounded-4" style="background-color: #ffffff;">
                {{-- Header --}}
                <div class="card-header bg-white border-0 text-center">
                    <h5 class="fw-semibold text-dark mb-0">{{ __('Upload New File') }}</h5>
                </div>

                {{-- Body --}}
                <div class="card-body px-4 py-4">
                    {{-- Upload Form --}}
                    <form method="POST" action="{{ route('files.store') }}" enctype="multipart/form-data">
                        @csrf

                        {{-- File Input --}}
                        <div class="mb-4">
                            <label for="file" class="form-label fw-semibold text-dark">{{ __('Choose a File') }}</label>
                            <input id="file" type="file"
                                   class="form-control @error('file') is-invalid @enderror"
                                   name="file" required
                                   style="background-color: #f7f9fc; border: 1px solid #ced4da;">
                            @error('file')
                                <div class="invalid-feedback mt-1">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                            <small class="form-text text-muted mt-1">
                                Supported formats: <strong>CSV, JSON, XML</strong> · Max size: <strong>10MB</strong>
                            </small>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="submit" class="btn text-white px-4"
                                    style="background-color: #84c5f4; border-radius: 8px;">
                                {{ __('Upload File') }}
                            </button>
                            <a href="{{ route('files.index') }}" class="btn btn-outline-secondary px-4"
                               style="border-radius: 8px;">
                                {{ __('Cancel') }}
                            </a>
                        </div>
                    </form>

                    {{-- Sample Files --}}
                    <div class="mt-5">
                        <h6 class="fw-semibold text-dark mb-2">{{ __('Sample Files') }}</h6>
                        <p class="text-muted mb-3">Download sample templates for correct formatting:</p>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('samples.csv') }}" target="_blank"
                               class="btn btn-outline-primary rounded-pill px-4 py-1">
                                CSV Sample
                            </a>
                            <a href="{{ route('samples.json') }}" target="_blank"
                               class="btn btn-outline-primary rounded-pill px-4 py-1">
                                JSON Sample
                            </a>
                            <a href="{{ route('samples.xml') }}" target="_blank"
                               class="btn btn-outline-primary rounded-pill px-4 py-1">
                                XML Sample
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>    
@endsection
