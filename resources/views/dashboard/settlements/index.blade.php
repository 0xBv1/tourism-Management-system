@extends('layouts.dashboard.app')

@section('title', 'Settlements')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Settlements Management</h3>
                    <div class="card-tools">
                        <a href="{{ route('dashboard.settlements.generate') }}" class="btn btn-success mr-2">
                            <i class="fas fa-cogs"></i> Generate Automatically
                        </a>
                        <a href="{{ route('dashboard.settlements.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add New Settlement
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- DataTable -->
                    {!! $dataTable->table(['class' => 'table table-striped table-bordered', 'style' => 'width:100%']) !!}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approve Settlement Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Approve Settlement</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="approveForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="approve_notes">Additional Notes (Optional)</label>
                        <textarea class="form-control" id="approve_notes" name="notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Approve</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Settlement Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Settlement</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="rejection_reason">Reason for Rejection <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Mark as Paid Modal -->
<div class="modal fade" id="paidModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Record Payment</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="paidForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="payment_method">Payment Method <span class="text-danger">*</span></label>
                        <select class="form-control" id="payment_method" name="payment_method" required>
                            <option value="">Select Payment Method</option>
                            <option value="cash">Cash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="check">Check</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="payment_reference">Reference Number (Optional)</label>
                        <input type="text" class="form-control" id="payment_reference" name="payment_reference">
                    </div>
                    <div class="form-group">
                        <label for="paid_notes">Additional Notes (Optional)</label>
                        <textarea class="form-control" id="paid_notes" name="notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Record Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{!! $dataTable->scripts() !!}
@endpush

<!-- Load jQuery from CDN -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    // Filter functionality
    $('#resource_type_filter, #settlement_type_filter, #status_filter, #month_filter, #year_filter').on('change', function() {
        if (window.LaravelDataTables && window.LaravelDataTables['settlements-table']) {
            window.LaravelDataTables['settlements-table'].draw();
        }
    });

    // Approve settlement
    window.approveSettlement = function(settlementId) {
        $('#approveForm').attr('action', '/dashboard/settlements/' + settlementId + '/approve');
        $('#approveModal').modal('show');
    };

    // Reject settlement
    window.rejectSettlement = function(settlementId) {
        $('#rejectForm').attr('action', '/dashboard/settlements/' + settlementId + '/reject');
        $('#rejectModal').modal('show');
    };

    // Mark as paid
    window.markAsPaid = function(settlementId) {
        $('#paidForm').attr('action', '/dashboard/settlements/' + settlementId + '/mark-paid');
        $('#paidModal').modal('show');
    };
});

// Fallback using vanilla JavaScript if jQuery fails
if (typeof $ === 'undefined') {
    document.addEventListener('DOMContentLoaded', function() {
        // Filter functionality
        const filterElements = [
            'resource_type_filter',
            'settlement_type_filter', 
            'status_filter',
            'month_filter',
            'year_filter'
        ];
        
        filterElements.forEach(function(filterId) {
            const element = document.getElementById(filterId);
            if (element) {
                element.addEventListener('change', function() {
                    if (window.LaravelDataTables && window.LaravelDataTables['settlements-table']) {
                        window.LaravelDataTables['settlements-table'].draw();
                    }
                });
            }
        });

        // Approve settlement
        window.approveSettlement = function(settlementId) {
            const form = document.getElementById('approveForm');
            const modal = document.getElementById('approveModal');
            if (form && modal) {
                form.action = '/dashboard/settlements/' + settlementId + '/approve';
                modal.style.display = 'block';
            }
        };

        // Reject settlement
        window.rejectSettlement = function(settlementId) {
            const form = document.getElementById('rejectForm');
            const modal = document.getElementById('rejectModal');
            if (form && modal) {
                form.action = '/dashboard/settlements/' + settlementId + '/reject';
                modal.style.display = 'block';
            }
        };

        // Mark as paid
        window.markAsPaid = function(settlementId) {
            const form = document.getElementById('paidForm');
            const modal = document.getElementById('paidModal');
            if (form && modal) {
                form.action = '/dashboard/settlements/' + settlementId + '/mark-paid';
                modal.style.display = 'block';
            }
        };
    });
}
</script>
