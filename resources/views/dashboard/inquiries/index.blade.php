@extends('layouts.dashboard.app')

@section('content')
    <div class="page-body">
        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Inquiries">
            <li class="breadcrumb-item active">Inquiries</li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <x-dashboard.partials.message-alert />
                    
                    @if(auth()->user()->hasRole(['Reservation', 'Operation']))
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i> 
                            <strong>Filtered View:</strong> You are viewing only inquiries assigned to you. 
                            <small class="text-muted">(Role: {{ auth()->user()->roles->pluck('name')->join(', ') }})</small>
                        </div>
                    @endif
                    
                    <div class="card">
                        <div class="card-header">
                            <h5>Inquiries Management</h5>
                            @if(admin()->roles->count() > 0)
                                <small class="text-muted">
                                    <i class="fa fa-user-tag"></i> 
                                    Role: {{ admin()->roles->pluck('name')->join(', ') }}
                                </small>
                            @endif
                            <div class="card-header-right">
                                @if(admin()->can('inquiries.create'))
                                    <a href="{{ route('dashboard.inquiries.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fa fa-plus"></i> Create Inquiry
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





