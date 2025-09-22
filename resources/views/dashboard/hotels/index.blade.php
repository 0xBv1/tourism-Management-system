@extends('layouts.dashboard.app')

@section('content')
    <div class="page-body">
        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Hotels">
            <li class="breadcrumb-item active">Hotels</li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <x-dashboard.partials.message-alert />
                    <div class="card">
                        <div class="card-header">
                            <h5>Hotels Management</h5>
                            @if(admin()->roles->count() > 0)
                                <small class="text-muted">
                                    <i class="fa fa-user-tag"></i> 
                                    Role: {{ admin()->roles->pluck('name')->join(', ') }}
                                </small>
                            @endif
                            <div class="card-header-right">
                                @if(admin()->can('hotels.calendar'))
                                    <a href="{{ route('dashboard.hotels.calendar') }}" class="btn btn-info btn-sm me-2">
                                        <i class="fa fa-calendar"></i> Availability Calendar
                                    </a>
                                @endif
                                @if(admin()->can('hotels.create'))
                                    <a href="{{ route('dashboard.hotels.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fa fa-plus"></i> Create Hotel
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="card-body order-datatable overflow-x-auto">
                            <div class="">
                                {!! $dataTable->table(['class'=>'display']) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Container-fluid Ends-->
    </div>
@endsection




