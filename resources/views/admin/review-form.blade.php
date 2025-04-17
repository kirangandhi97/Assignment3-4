@extends('layouts.app')

@section('content')
<div class="container py-5" style="background-color: #f0f4f8;">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card border-0 shadow rounded-4" style="background-color: #ffffff;">
                {{-- Header --}}
                <div class="card-header d-flex justify-content-between align-items-center bg-white border-0">
                    <h5 class="fw-semibold text-dark mb-0">{{ __('Review Guarantee') }}</h5>
                    <a href="{{ route('admin.pending-reviews') }}" class="btn btn-outline-secondary rounded-pill px-3">
                        {{ __('Back to Pending Reviews') }}
                    </a>
                </div>

                {{-- Body --}}
                <div class="card-body px-4 py-4">
                    <div class="row mb-5">
                        {{-- Guarantee Info --}}
                        <div class="col-md-6">
                            <h6 class="fw-semibold text-dark mb-3">Guarantee Information</h6>
                            <table class="table table-bordered table-sm">
                                <tbody>
                                    <tr><th>Reference Number:</th><td>{{ $guarantee->corporate_reference_number }}</td></tr>
                                    <tr><th>Type:</th><td>{{ $guarantee->guarantee_type }}</td></tr>
                                    <tr><th>Nominal Amount:</th><td>{{ number_format($guarantee->nominal_amount, 2) }} {{ $guarantee->nominal_amount_currency }}</td></tr>
                                    <tr><th>Expiry Date:</th><td>{{ $guarantee->expiry_date->format('Y-m-d') }}</td></tr>
                                    <tr><th>Status:</th>
                                        <td><span class="badge bg-info">{{ ucfirst($guarantee->status) }}</span></td>
                                    </tr>
                                    <tr><th>Submitted By:</th><td>{{ $guarantee->user->name }}</td></tr>
                                    <tr><th>Created At:</th><td>{{ $guarantee->created_at->format('Y-m-d H:i:s') }}</td></tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- Parties Info --}}
                        <div class="col-md-6">
                            <h6 class="fw-semibold text-dark mb-3">Parties Information</h6>
                            <table class="table table-bordered table-sm">
                                <tbody>
                                    <tr><th>Applicant Name:</th><td>{{ $guarantee->applicant_name }}</td></tr>
                                    <tr><th>Applicant Address:</th><td>{{ $guarantee->applicant_address }}</td></tr>
                                    <tr><th>Beneficiary Name:</th><td>{{ $guarantee->beneficiary_name }}</td></tr>
                                    <tr><th>Beneficiary Address:</th><td>{{ $guarantee->beneficiary_address }}</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="row">
                        <div class="col-md-12">
                            <h6 class="fw-semibold text-dark mb-3">Review Actions</h6>
                            <div class="d-flex gap-3">
                                <form method="POST" action="{{ route('guarantees.apply', $guarantee->id) }}">
                                    @csrf
                                    <button type="submit"
                                            class="btn text-white px-4 rounded-pill"
                                            style="background-color: #84c5f4;"
                                            onclick="return confirm('Are you sure you want to apply this guarantee?')">
                                        Approve & Apply Guarantee
                                    </button>
                                </form>

                                <button type="button"
                                        class="btn btn-danger rounded-pill px-4"
                                        data-bs-toggle="modal"
                                        data-bs-target="#rejectModal">
                                    Reject Guarantee
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal --}}
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content rounded-3">
                <form method="POST" action="{{ route('guarantees.reject', $guarantee->id) }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="rejectModalLabel">Reject Guarantee</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="review_notes" class="form-label">Rejection Reason</label>
                            <textarea class="form-control" id="review_notes" name="review_notes" rows="4" required style="background-color: #f7f9fc;"></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger rounded-pill">Reject Guarantee</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
