@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Pending Reviews') }}</span>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
                </div>

                <div class="card-body">
                    @if ($pendingGuarantees->isEmpty())
                        <div class="alert alert-info">No guarantees pending review.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Reference Number</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Applicant</th>
                                        <th>Beneficiary</th>
                                        <th>Submitted By</th>
                                        <th>Submitted On</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pendingGuarantees as $guarantee)
                                        <tr>
                                            <td>{{ $guarantee->corporate_reference_number }}</td>
                                            <td>{{ $guarantee->guarantee_type }}</td>
                                            <td>{{ number_format($guarantee->nominal_amount, 2) }} {{ $guarantee->nominal_amount_currency }}</td>
                                            <td>{{ $guarantee->applicant_name }}</td>
                                            <td>{{ $guarantee->beneficiary_name }}</td>
                                            <td>{{ $guarantee->user->name }}</td>
                                            <td>{{ $guarantee->review->created_at->format('Y-m-d H:i:s') }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('guarantees.show', $guarantee->id) }}" class="btn btn-sm btn-info">View</a>
                                                    <a href="{{ route('admin.show-review-form', $guarantee->id) }}" class="btn btn-sm btn-primary">Review</a>
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
