@extends('layouts.dashboard.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5>Wallet</h5>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Current Balance</h6>
                                <h3 class="mb-0">{{ number_format($stats['current_balance'], 2) }} EGP</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="mdi mdi-wallet" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Total Earnings</h6>
                                <h3 class="mb-0">{{ number_format($stats['total_earnings'], 2) }} EGP</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="mdi mdi-cash-multiple" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Total Commissions</h6>
                                <h3 class="mb-0">{{ number_format($stats['total_commissions'], 2) }} EGP</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="mdi mdi-percent" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Pending Amount</h6>
                                <h3 class="mb-0">{{ number_format($stats['total_pending'], 2) }} EGP</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="mdi mdi-clock" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transactions DataTable -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Recent Transactions</h5>
            </div>
            <div class="card-body">
                {{ $dataTable->table() }}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{ $dataTable->scripts() }}
@endpush


