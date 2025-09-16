@extends('layouts.dashboard.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>Supplier: {{ $supplier->company_name }}</h5>
                        <div>
                            <form action="{{ route('dashboard.suppliers.toggle-verification', $supplier) }}" method="post" class="d-inline">
                                @csrf
                                <button class="btn btn-sm btn-{{ $supplier->is_verified ? 'warning' : 'success' }}">
                                    {{ $supplier->is_verified ? 'Unverify' : 'Verify' }}
                                </button>
                            </form>
                            <form action="{{ route('dashboard.suppliers.toggle-active', $supplier) }}" method="post" class="d-inline">
                                @csrf
                                <button class="btn btn-sm btn-{{ $supplier->is_active ? 'secondary' : 'primary' }}">
                                    {{ $supplier->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6>Company Info</h6>
                                <p><strong>Email:</strong> {{ $supplier->company_email }}</p>
                                <p><strong>Phone:</strong> {{ $supplier->phone }}</p>
                                <p><strong>Commission:</strong> {{ $supplier->commission_rate }}%</p>
                                <p><strong>Wallet:</strong> {{ $supplier->formatted_wallet_balance }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6>User</h6>
                                <p><strong>Name:</strong> {{ $supplier->user->name ?? 'N/A' }}</p>
                                <p><strong>Email:</strong> {{ $supplier->user->email ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <hr>
                        <h6>Statistics</h6>
                        <div class="row">
                            <div class="col-md-3"><strong>Total Services:</strong> {{ $stats['total_services'] }}</div>
                            <div class="col-md-3"><strong>Pending Approvals:</strong> {{ $stats['pending_approvals'] }}</div>
                            <div class="col-md-3"><strong>Commission:</strong> {{ $stats['commission_rate'] }}%</div>
                            <div class="col-md-3"><strong>Wallet:</strong> {{ $stats['wallet_balance'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


