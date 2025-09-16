@extends('layouts.dashboard.app')

@section('content')
    <div class="container-fluid">
        <h5 class="mb-3">Statistics</h5>
        <form class="row g-2 mb-3" method="get">
            <div class="col-auto">
                <input type="date" name="start_date" value="{{ $startDate }}" class="form-control">
            </div>
            <div class="col-auto">
                <input type="date" name="end_date" value="{{ $endDate }}" class="form-control">
            </div>
            <div class="col-auto">
                <button class="btn btn-primary">Filter</button>
                <a href="{{ route('supplier.statistics.export', request()->all()) }}" class="btn btn-outline-secondary">Export CSV</a>
            </div>
        </form>

        <div class="row">
            <div class="col-md-3"><div class="card"><div class="card-body"><h6>Total Bookings</h6><div class="h4">{{ $stats['overview']['total_bookings'] }}</div></div></div></div>
            <div class="col-md-3"><div class="card"><div class="card-body"><h6>Total Revenue</h6><div class="h4">{{ number_format($stats['overview']['total_revenue'], 2) }} EGP</div></div></div></div>
            <div class="col-md-3"><div class="card"><div class="card-body"><h6>Total Commissions</h6><div class="h4">{{ number_format($stats['overview']['total_commissions'], 2) }} EGP</div></div></div></div>
            <div class="col-md-3"><div class="card"><div class="card-body"><h6>Net Revenue</h6><div class="h4">{{ number_format($stats['overview']['net_revenue'], 2) }} EGP</div></div></div></div>
        </div>

        <div class="card mt-3">
            <div class="card-header">Revenue by Service</div>
            <div class="card-body">
                <ul>
                    <li>Hotels: {{ number_format($stats['revenue_by_service']['hotels'], 2) }} EGP</li>
                    <li>Trips: {{ number_format($stats['revenue_by_service']['trips'], 2) }} EGP</li>
                    <li>Tours: {{ number_format($stats['revenue_by_service']['tours'], 2) }} EGP</li>
                    <li>Transports: {{ number_format($stats['revenue_by_service']['transports'], 2) }} EGP</li>
                </ul>
            </div>
        </div>
    </div>
@endsection


