@extends('layouts.dashboard.app')

@section('content')
    <div class="container-fluid">
        <h5 class="mb-3">Supplier Dashboard</h5>
        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6>Wallet Balance</h6>
                        <div class="h4">{{ $stats['wallet_balance'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6>Commission Rate</h6>
                        <div class="h4">{{ $stats['commission_rate'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6>Total Services</h6>
                        <div class="h4">{{ array_sum($stats['services']) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6>Pending Approvals</h6>
                        <div class="h4">{{ array_sum($stats['pending_approvals']) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mt-3">
            <div class="card-header">Recent Bookings</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Service</th>
                                <th>Client</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stats['recent_bookings']['hotels'] as $b)
                                <tr>
                                    <td>Hotel</td>
                                    <td>{{ $b->hotel->name ?? 'N/A' }}</td>
                                    <td>{{ $b->client->name ?? 'N/A' }}</td>
                                    <td>{{ $b->formatted_supplier_amount }}</td>
                                    <td>{{ $b->status_label }}</td>
                                    <td>{{ $b->created_at?->format('Y-m-d') }}</td>
                                </tr>
                            @endforeach
                            @foreach($stats['recent_bookings']['tours'] as $b)
                                <tr>
                                    <td>Tour</td>
                                    <td>{{ $b->tour->title ?? 'N/A' }}</td>
                                    <td>{{ $b->client->name ?? 'N/A' }}</td>
                                    <td>{{ $b->formatted_supplier_amount }}</td>
                                    <td>{{ $b->status_label }}</td>
                                    <td>{{ $b->created_at?->format('Y-m-d') }}</td>
                                </tr>
                            @endforeach
                            @foreach($stats['recent_bookings']['trips'] as $b)
                                <tr>
                                    <td>Trip</td>
                                    <td>{{ $b->tour->trip_name ?? 'N/A' }}</td>
                                    <td>{{ $b->client->name ?? 'N/A' }}</td>
                                    <td>{{ $b->formatted_supplier_amount }}</td>
                                    <td>{{ $b->status_label }}</td>
                                    <td>{{ $b->created_at?->format('Y-m-d') }}</td>
                                </tr>
                            @endforeach
                            @foreach($stats['recent_bookings']['transports'] as $b)
                                <tr>
                                    <td>Transport</td>
                                    <td>{{ $b->transport->name ?? 'N/A' }}</td>
                                    <td>{{ $b->client->name ?? 'N/A' }}</td>
                                    <td>{{ $b->formatted_supplier_amount }}</td>
                                    <td>{{ $b->status_label }}</td>
                                    <td>{{ $b->created_at?->format('Y-m-d') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection


