@extends('layouts.dashboard.app')

@section('content')
    <div class="page-body">
        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Tours">
            <li class="breadcrumb-item active">Tours</li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <x-dashboard.partials.message-alert />
                    <div class="card">
                        <x-dashboard.partials.table-card-header model="SupplierTour">
                            <div class="d-flex align-items-center gap-2">
                                <select id="approval-status-filter" class="form-select form-select-sm" style="width: auto;">
                                    <option value="">All Approval Status</option>
                                    <option value="approved">Approved</option>
                                    <option value="pending">Pending Approval</option>
                                </select>
                                <a data-model="Destination" href="#" type="button" class="auto-translate btn btn-primary add-row mt-md-0 mt-2">
                                    <i class="fa icon fa-language"></i> Auto Translate
                                </a>
                            </div>
                        </x-dashboard.partials.table-card-header>
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

@push('scripts')
    {!! $dataTable->scripts() !!}
    
    <script>
        $(document).ready(function() {
            // Handle approval status filter
            $('#approval-status-filter').on('change', function() {
                var status = $(this).val();
                var url = new URL(window.location);
                
                if (status) {
                    url.searchParams.set('approval_status', status);
                } else {
                    url.searchParams.delete('approval_status');
                }
                
                window.location.href = url.toString();
            });
            
            // Set the selected value based on current URL parameter
            var urlParams = new URLSearchParams(window.location.search);
            var currentStatus = urlParams.get('approval_status');
            if (currentStatus) {
                $('#approval-status-filter').val(currentStatus);
            }
        });
    </script>
@endpush
