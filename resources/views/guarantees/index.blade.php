@extends('layouts.app')

@section('content')
<div class="container py-5" style="background-color: #f0f4f8;">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card border-0 shadow rounded-4" style="background-color: #ffffff;">
                <div class="card-header d-flex justify-content-between align-items-center bg-white border-0">
                    <h5 class="fw-semibold text-dark mb-0">{{ __('Guarantees') }}</h5>
                    <a href="{{ route('guarantees.create') }}" class="btn" style="background-color: #84c5f4; color: #fff;">Create New Guarantee</a>
                </div>

                <div class="card-body px-4 py-4">
                    @if ($guarantees->isEmpty())
                        <p class="text-muted">No guarantees found.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Reference Number</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Currency</th>
                                        <th>Expiry Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($guarantees as $guarantee)
                                        <tr>
                                            <td>{{ $guarantee->corporate_reference_number }}</td>
                                            <td>{{ $guarantee->guarantee_type }}</td>
                                            <td>{{ number_format($guarantee->nominal_amount, 2) }}</td>
                                            <td>{{ $guarantee->nominal_amount_currency }}</td>
                                            <td>{{ $guarantee->expiry_date->format('Y-m-d') }}</td>
                                            <td>
                                                <span class="badge bg-{{ match($guarantee->status) {
                                                    'draft' => 'secondary',
                                                    'review' => 'info',
                                                    'applied' => 'primary',
                                                    'issued' => 'success',
                                                    default => 'danger',
                                                } }}">{{ ucfirst($guarantee->status) }}</span>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('guarantees.show', $guarantee->id) }}" class="btn btn-sm btn-info">View</a>
                                                    @if ($guarantee->isDraft() && (Auth::user()->isAdmin() || $guarantee->user_id == Auth::id()))
                                                        <a href="{{ route('guarantees.edit', $guarantee->id) }}" class="btn btn-sm btn-warning">Edit</a>
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
