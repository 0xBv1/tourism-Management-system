@extends('layouts.dashboard.app')

@section('content')

    <div class="page-body">
        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Dashboard">
            <li class="breadcrumb-item active">Dashboard</li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Cards -->
        <div class="row">
            <x-dashboard.partials.box-card permission="users.list" title="Users" :count="\App\Models\User::count()" icon="users"
                                           color="danger"/>

            <x-dashboard.partials.box-card permission="countries.list" title="Countries" :count="\App\Models\Country::count()" icon="globe"
                                           color="primary"/>
        </div>

        <!-- Tables -->
        <div class="row">
            <div class="col-xl-12 xl-100">
                <div class="card">
                    <div class="card-header">
                        <h5>System Overview</h5>
                    </div>
                    <div class="card-body">
                        <div class="user-status table-responsive latest-order-table">
                            <p>Welcome to your clean Laravel application! All tourism-related components have been removed.</p>
                            <p>You can now build your new application on this clean foundation.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection