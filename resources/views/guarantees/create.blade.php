@extends('layouts.app')

@section('content')
<div class="container py-5" style="background-color: #f0f4f8;">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card border-0 shadow rounded-4" style="background-color: #ffffff;">
                <div class="card-header bg-white border-0 text-center">
                    <h5 class="fw-semibold text-dark mb-0">{{ __('Create New Guarantee') }}</h5>
                </div>

                <div class="card-body px-4 py-4">
                    <form method="POST" action="{{ route('guarantees.store') }}">
                        @csrf

                        {{-- Guarantee Type --}}
                        <div class="mb-3">
                            <label for="guarantee_type" class="form-label text-dark">{{ __('Guarantee Type') }}</label>
                            <select id="guarantee_type" class="form-select @error('guarantee_type') is-invalid @enderror" name="guarantee_type" required style="background-color: #f7f9fc;">
                                <option value="">-- Select Type --</option>
                                <option value="Bank" {{ old('guarantee_type') == 'Bank' ? 'selected' : '' }}>Bank</option>
                                <option value="Bid Bond" {{ old('guarantee_type') == 'Bid Bond' ? 'selected' : '' }}>Bid Bond</option>
                                <option value="Insurance" {{ old('guarantee_type') == 'Insurance' ? 'selected' : '' }}>Insurance</option>
                                <option value="Surety" {{ old('guarantee_type') == 'Surety' ? 'selected' : '' }}>Surety</option>
                            </select>
                            @error('guarantee_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Nominal Amount --}}
                        <div class="mb-3">
                            <label for="nominal_amount" class="form-label text-dark">{{ __('Nominal Amount') }}</label>
                            <input id="nominal_amount" type="number" step="0.01" min="0" class="form-control @error('nominal_amount') is-invalid @enderror"
                                   name="nominal_amount" value="{{ old('nominal_amount') }}" required style="background-color: #f7f9fc;">
                            @error('nominal_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Currency --}}
                        <div class="mb-3">
                            <label for="nominal_amount_currency" class="form-label text-dark">{{ __('Currency') }}</label>
                            <input id="nominal_amount_currency" type="text" maxlength="3"
                                   class="form-control @error('nominal_amount_currency') is-invalid @enderror"
                                   name="nominal_amount_currency" value="{{ old('nominal_amount_currency', 'USD') }}" required
                                   placeholder="USD" style="background-color: #f7f9fc;">
                            @error('nominal_amount_currency')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Expiry Date --}}
                        <div class="mb-3">
                            <label for="expiry_date" class="form-label text-dark">{{ __('Expiry Date') }}</label>
                            <input id="expiry_date" type="date"
                                   class="form-control @error('expiry_date') is-invalid @enderror"
                                   name="expiry_date" value="{{ old('expiry_date') }}" required style="background-color: #f7f9fc;">
                            @error('expiry_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Applicant Name --}}
                        <div class="mb-3">
                            <label for="applicant_name" class="form-label text-dark">{{ __('Applicant Name') }}</label>
                            <input id="applicant_name" type="text"
                                   class="form-control @error('applicant_name') is-invalid @enderror"
                                   name="applicant_name" value="{{ old('applicant_name') }}" required style="background-color: #f7f9fc;">
                            @error('applicant_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Applicant Address --}}
                        <div class="mb-3">
                            <label for="applicant_address" class="form-label text-dark">{{ __('Applicant Address') }}</label>
                            <textarea id="applicant_address"
                                      class="form-control @error('applicant_address') is-invalid @enderror"
                                      name="applicant_address" required style="background-color: #f7f9fc;">{{ old('applicant_address') }}</textarea>
                            @error('applicant_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Beneficiary Name --}}
                        <div class="mb-3">
                            <label for="beneficiary_name" class="form-label text-dark">{{ __('Beneficiary Name') }}</label>
                            <input id="beneficiary_name" type="text"
                                   class="form-control @error('beneficiary_name') is-invalid @enderror"
                                   name="beneficiary_name" value="{{ old('beneficiary_name') }}" required style="background-color: #f7f9fc;">
                            @error('beneficiary_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Beneficiary Address --}}
                        <div class="mb-3">
                            <label for="beneficiary_address" class="form-label text-dark">{{ __('Beneficiary Address') }}</label>
                            <textarea id="beneficiary_address"
                                      class="form-control @error('beneficiary_address') is-invalid @enderror"
                                      name="beneficiary_address" required style="background-color: #f7f9fc;">{{ old('beneficiary_address') }}</textarea>
                            @error('beneficiary_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Buttons --}}
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="submit" class="btn text-white px-4" style="background-color: #84c5f4; border-radius: 8px;">
                                {{ __('Create Guarantee') }}
                            </button>
                            <a href="{{ route('guarantees.index') }}" class="btn btn-outline-secondary px-4" style="border-radius: 8px;">
                                {{ __('Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
