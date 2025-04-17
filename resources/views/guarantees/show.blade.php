@extends('layouts.app')

@section('content')
<div class="container py-5" style="background-color: #f0f4f8;">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card border-0 shadow rounded-4" style="background-color: #ffffff;">
                {{-- Header --}}
                <div class="card-header d-flex justify-content-between align-items-center bg-white border-0">
                    <h5 class="fw-semibold text-dark mb-0">{{ __('Guarantee Details') }}</h5>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('guarantees.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
                            Back to List
                        </a>
                        @if ($guarantee->isDraft() && (Auth::user()->isAdmin() || $guarantee->user_id == Auth::id()))
                            <a href="{{ route('guarantees.edit', $guarantee->id) }}"
                               class="btn btn-warning rounded-pill px-3">
                                Edit
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Body --}}
                <div class="card-body px-4 py-4">
                    {{-- Guarantee Info --}}
                    <div class="row mb-5">
                        <div class="col-md-6">
                            <h6 class="fw-semibold text-dark mb-3">Guarantee Information</h6>
                            <table class="table table-bordered table-sm">
                                <tr><th>Reference Number:</th><td>{{ $guarantee->corporate_reference_number }}</td></tr>
                                <tr><th>Type:</th><td>{{ $guarantee->guarantee_type }}</td></tr>
                                <tr><th>Nominal Amount:</th><td>{{ number_format($guarantee->nominal_amount, 2) }} {{ $guarantee->nominal_amount_currency }}</td></tr>
                                <tr><th>Expiry Date:</th><td>{{ $guarantee->expiry_date->format('Y-m-d') }}</td></tr>
                                <tr><th>Status:</th>
                                    <td>
                                        <span class="badge bg-{{ $guarantee->status == 'draft' ? 'secondary' : ($guarantee->status == 'review' ? 'info' : ($guarantee->status == 'applied' ? 'primary' : ($guarantee->status == 'issued' ? 'success' : 'danger'))) }}">
                                            {{ ucfirst($guarantee->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr><th>Created By:</th><td>{{ $guarantee->user->name }}</td></tr>
                                <tr><th>Created At:</th><td>{{ $guarantee->created_at->format('Y-m-d H:i:s') }}</td></tr>
                                <tr><th>Last Updated:</th><td>{{ $guarantee->updated_at->format('Y-m-d H:i:s') }}</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-semibold text-dark mb-3">Parties Information</h6>
                            <table class="table table-bordered table-sm">
                                <tr><th>Applicant Name:</th><td>{{ $guarantee->applicant_name }}</td></tr>
                                <tr><th>Applicant Address:</th><td>{{ $guarantee->applicant_address }}</td></tr>
                                <tr><th>Beneficiary Name:</th><td>{{ $guarantee->beneficiary_name }}</td></tr>
                                <tr><th>Beneficiary Address:</th><td>{{ $guarantee->beneficiary_address }}</td></tr>
                            </table>
                        </div>
                    </div>

                    {{-- Review Info --}}
                    @if ($guarantee->review)
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h6 class="fw-semibold text-dark mb-3">Review Information</h6>
                                <table class="table table-bordered table-sm">
                                    <tr><th>Review Notes:</th><td>{{ $guarantee->review->review_notes ?: 'No notes available' }}</td></tr>
                                    <tr><th>Reviewed By:</th><td>{{ $guarantee->review->reviewer ? $guarantee->review->reviewer->name : 'Not yet reviewed' }}</td></tr>
                                    <tr><th>Submitted for Review:</th><td>{{ $guarantee->review->created_at->format('Y-m-d H:i:s') }}</td></tr>
                                </table>
                            </div>
                        </div>
                    @endif

                    {{-- Actions --}}
                    <div class="row">
                        <div class="col-md-12">
                            <h6 class="fw-semibold text-dark mb-3">Actions</h6>
                            <div class="d-flex gap-2 flex-wrap">
                                @if ($guarantee->isDraft() && $guarantee->user_id == Auth::id())
                                    <form method="POST" action="{{ route('guarantees.submit-for-review', $guarantee->id) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-info rounded-pill px-4"
                                                onclick="return confirm('Submit this guarantee for review?')">
                                            Submit for Review
                                        </button>
                                    </form>
                                @endif

                                @if (Auth::user()->isAdmin() && $guarantee->isInReview())
                                    <form method="POST" action="{{ route('guarantees.apply', $guarantee->id) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-primary rounded-pill px-4"
                                                onclick="return confirm('Apply this guarantee?')">
                                            Apply Guarantee
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-danger rounded-pill px-4"
                                            data-bs-toggle="modal" data-bs-target="#rejectModal">
                                        Reject Guarantee
                                    </button>
                                @endif

                                @if (Auth::user()->isAdmin() && $guarantee->isApplied())
                                    <form method="POST" action="{{ route('guarantees.issue', $guarantee->id) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-success rounded-pill px-4"
                                                onclick="return confirm('Issue this guarantee?')">
                                            Issue Guarantee
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-danger rounded-pill px-4"
                                            data-bs-toggle="modal" data-bs-target="#rejectModal">
                                        Reject Guarantee
                                    </button>
                                @endif

                                @if (($guarantee->isDraft() || $guarantee->isRejected()) && (Auth::user()->isAdmin() || $guarantee->user_id == Auth::id()))
                                    <form method="POST" action="{{ route('guarantees.destroy', $guarantee->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger rounded-pill px-4"
                                                onclick="return confirm('Delete this guarantee? This action cannot be undone.')">
                                            Delete Guarantee
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div> {{-- End Card Body --}}
            </div>
        </div>
    </div>
</div>

{{-- Reject Modal --}}
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-3">
            <form method="POST" action="{{ route('guarantees.reject', $guarantee->id) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Reject Guarantee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="review_notes" class="form-label">Rejection Reason</label>
                    <textarea class="form-control" id="review_notes" name="review_notes"
                              rows="3" required style="background-color: #f7f9fc;"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary rounded-pill"
                            data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-danger rounded-pill">
                        Reject Guarantee
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
