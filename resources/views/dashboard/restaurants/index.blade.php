@extends('layouts.dashboard.app')

@section('title')
    {{ __('Restaurants Management') }}
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-utensils me-2"></i>{{ __('Restaurants Management') }}
                    </h4>
                    @can('restaurants.create')
                        <a href="{{ route('dashboard.restaurants.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>{{ __('Create New Restaurant') }}
                        </a>
                    @endcan
                </div>
                <p class="text-muted mb-0">{{ __('Manage restaurant listings and reservations') }}</p>
            </div>
            <div class="card-body">
                {!! $dataTable->table(['class' => 'table table-striped table-responsive']) !!}
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.table th {
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
}

.badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
}

.btn-group-sm > .btn, .btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

/* Meal preview styling */
.text-truncate {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Featured meals badge */
.badge.bg-warning {
    background-color: #ffc107 !important;
    color: #000 !important;
}

/* Meals count badge */
.badge.bg-info {
    background-color: #0dcaf0 !important;
    color: #000 !important;
}

/* Table responsive improvements */
.table-responsive {
    border-radius: 0.375rem;
}

/* Action buttons spacing */
.btn-group .btn {
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}
</style>
@endpush

@push('scripts')
{!! $dataTable->scripts() !!}
@endpush
