@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Edit Guarantee') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('guarantees.update', $guarantee->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <label for="corporate_reference_number" class="col-md-4 col-form-label text-md-end">{{ __('Corporate Reference Number') }}</label>

                            <div class="col-md-6">
                                <input id="corporate_reference_number" type="text" class="form-control" name="corporate_reference_number" value="{{ $guarantee->corporate_reference_number }}" disabled readonly>
                                <small class="text-muted">Reference number cannot be changed</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="guarantee_type" class="col-md-4 col-form-label text-md-end">{{ __('Guarantee Type') }}</label>

                            <div class="col-md-6">
                                <select id="guarantee_type" class="form-select @error('guarantee_type') is-invalid @enderror" name="guarantee_type" required>
                                    <option value="">-- Select Type --</option>
                                    <option value="Bank" {{ $guarantee->guarantee_type == 'Bank' ? 'selected' : '' }}>Bank</option>
                                    <option value="Bid Bond" {{ $guarantee->guarantee_type == 'Bid Bond' ? 'selected' : '' }}>Bid Bond</option>
                                    <option value="Insurance" {{ $guarantee->guarantee_type == 'Insurance' ? 'selected' : '' }}>Insurance</option>
                                    <option value="Surety" {{ $guarantee->guarantee_type == 'Surety' ? 'selected' : '' }}>Surety</option>
                                </select>

                                @error('guarantee_type')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="nominal_amount" class="col-md-4 col-form-label text-md-end">{{ __('Nominal Amount') }}</label>

                            <div class="col-md-6">
                                <input id="nominal_amount" type="number" step="0.01" min="0" class="form-control @error('nominal_amount') is-invalid @enderror" name="nominal_amount" value="{{ $guarantee->nominal_amount }}" required>

                                @error('nominal_amount')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="nominal_amount_currency" class="col-md-4 col-form-label text-md-end">{{ __('Currency') }}</label>

                            <div class="col-md-6">
                                <input id="nominal_amount_currency" type="text" maxlength="3" class="form-control @error('nominal_amount_currency') is-invalid @enderror" name="nominal_amount_currency" value="{{ $guarantee->nominal_amount_currency }}" required>

                                @error('nominal_amount_currency')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="expiry_date" class="col-md-4 col-form-label text-md-end">{{ __('Expiry Date') }}</label>

                            <div class="col-md-6">
                                <input id="expiry_date" type="date" class="form-control @error('expiry_date') is-invalid @enderror" name="expiry_date" value="{{ $guarantee->expiry_date->format('Y-m-d') }}" required>

                                @error('expiry_date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="applicant_name" class="col-md-4 col-form-label text-md-end">{{ __('Applicant Name') }}</label>

                            <div class="col-md-6">
                                <input id="applicant_name" type="text" class="form-control @error('applicant_name') is-invalid @enderror" name="applicant_name" value="{{ $guarantee->applicant_name }}" required>

                                @error('applicant_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="applicant_address" class="col-md-4 col-form-label text-md-end">{{ __('Applicant Address') }}</label>

                            <div class="col-md-6">
                                <textarea id="applicant_address" class="form-control @error('applicant_address') is-invalid @enderror" name="applicant_address" required>{{ $guarantee->applicant_address }}</textarea>

                                @error('applicant_address')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="beneficiary_name" class="col-md-4 col-form-label text-md-end">{{ __('Beneficiary Name') }}</label>

                            <div class="col-md-6">
                                <input id="beneficiary_name" type="text" class="form-control @error('beneficiary_name') is-invalid @enderror" name="beneficiary_name" value="{{ $guarantee->beneficiary_name }}" required>

                                @error('beneficiary_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="beneficiary_address" class="col-md-4 col-form-label text-md-end">{{ __('Beneficiary Address') }}</label>

                            <div class="col-md-6">
                                <textarea id="beneficiary_address" class="form-control @error('beneficiary_address') is-invalid @enderror" name="beneficiary_address" required>{{ $guarantee->beneficiary_address }}</textarea>

                                @error('beneficiary_address')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Update Guarantee') }}
                                </button>
                                <a href="{{ route('guarantees.show', $guarantee->id) }}" class="btn btn-secondary">
                                    {{ __('Cancel') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
