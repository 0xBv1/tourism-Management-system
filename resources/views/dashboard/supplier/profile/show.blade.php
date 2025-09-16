@extends('layouts.dashboard.app')

@section('content')
    <div class="container-fluid">
        <h5 class="mb-3">Company Profile</h5>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Company:</strong> {{ $supplier->company_name }}</p>
                        <p><strong>Email:</strong> {{ $supplier->company_email }}</p>
                        <p><strong>Phone:</strong> {{ $supplier->phone }}</p>
                        <p><strong>Address:</strong> {{ $supplier->address }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Commission:</strong> {{ $supplier->commission_rate }}%</p>
                        <p><strong>Wallet:</strong> {{ $supplier->formatted_wallet_balance }}</p>
                        <p><strong>Status:</strong> {{ $supplier->status_label }}</p>
                    </div>
                </div>
                <a href="{{ route('supplier.profile.edit') }}" class="btn btn-primary mt-2">Edit Profile</a>
            </div>
        </div>
    </div>
@endsection


