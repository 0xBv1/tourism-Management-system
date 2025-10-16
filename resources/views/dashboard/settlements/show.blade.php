@extends('layouts.dashboard.app')

@section('title', 'View Settlement - ' . $settlement->settlement_number)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Settlement Details - {{ $settlement->settlement_number }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('dashboard.settlements.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                        @if($settlement->status === \App\Enums\SettlementStatus::PENDING)
                            <a href="{{ route('dashboard.settlements.edit', $settlement) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <!-- Settlement Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Settlement Information</h5>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Settlement Number:</strong></td>
                                    <td>{{ $settlement->settlement_number }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Settlement Type:</strong></td>
                                    <td>{{ ucfirst($settlement->settlement_type->value) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Resource:</strong></td>
                                    <td>{{ $settlement->resource_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Resource Type:</strong></td>
                                    <td>{{ ucfirst($settlement->resource_type) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Period:</strong></td>
                                    <td>
                                        @if($settlement->settlement_type === \App\Enums\SettlementType::MONTHLY && $settlement->month)
                                            {{ \Carbon\Carbon::create()->month($settlement->month)->format('F') }} {{ $settlement->year }}
                                        @elseif($settlement->settlement_type === \App\Enums\SettlementType::CUSTOM && $settlement->start_date && $settlement->end_date)
                                            {{ $settlement->start_date->format('M d') }} - {{ $settlement->end_date->format('M d, Y') }}
                                        @else
                                            {{ $settlement->year }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>From Date:</strong></td>
                                    <td>{{ $settlement->start_date->format('Y-m-d') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>To Date:</strong></td>
                                    <td>{{ $settlement->end_date->format('Y-m-d') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Financial Calculations</h5>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Number of Bookings:</strong></td>
                                    <td>{{ $settlement->total_bookings }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Total Hours:</strong></td>
                                    <td>{{ number_format($settlement->total_hours, 2) }} hours</td>
                                </tr>
                                <tr>
                                    <td><strong>Total Days:</strong></td>
                                    <td>{{ number_format($settlement->total_days, 2) }} days</td>
                                </tr>
                                <tr class="table-success">
                                    <td><strong>Total Amount:</strong></td>
                                    <td><strong>{{ $settlement->currency }} {{ number_format($settlement->total_amount, 2) }}</strong></td>
                                </tr>
                                <tr class="table-warning">
                                    <td><strong>Commission ({{ ucfirst($settlement->commission_type?->value ?? 'percentage') }}):</strong></td>
                                    <td><strong>{{ $settlement->currency }} {{ number_format($settlement->commission_amount, 2) }}</strong></td>
                                </tr>
                                <tr class="table-info">
                                    <td><strong>Tax:</strong></td>
                                    <td><strong>{{ $settlement->currency }} {{ number_format($settlement->tax_amount, 2) }}</strong></td>
                                </tr>
                                <tr class="table-danger">
                                    <td><strong>Deductions:</strong></td>
                                    <td><strong>{{ $settlement->currency }} {{ number_format($settlement->deductions, 2) }}</strong></td>
                                </tr>
                                <tr class="table-success">
                                    <td><strong>Bonuses:</strong></td>
                                    <td><strong>{{ $settlement->currency }} {{ number_format($settlement->bonuses, 2) }}</strong></td>
                                </tr>
                                <tr class="table-primary">
                                    <td><strong>Net Amount:</strong></td>
                                    <td><strong>{{ $settlement->currency }} {{ number_format($settlement->net_amount, 2) }}</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Status Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5>حالة التسوية</h5>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-{{ $settlement->status->getColor() }}">
                                            <i class="fas fa-info-circle"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">الحالة الحالية</span>
                                            <span class="info-box-number">{{ ucfirst($settlement->status->value) }}</span>
                                        </div>
                                    </div>
                                </div>
                                @if($settlement->calculated_at)
                                <div class="col-md-3">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-info">
                                            <i class="fas fa-calculator"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">تاريخ الحساب</span>
                                            <span class="info-box-number">{{ $settlement->calculated_at->format('Y-m-d H:i') }}</span>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @if($settlement->approved_at)
                                <div class="col-md-3">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-success">
                                            <i class="fas fa-check"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">تاريخ الاعتماد</span>
                                            <span class="info-box-number">{{ $settlement->approved_at->format('Y-m-d H:i') }}</span>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @if($settlement->paid_at)
                                <div class="col-md-3">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-success">
                                            <i class="fas fa-money-bill"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">تاريخ الدفع</span>
                                            <span class="info-box-number">{{ $settlement->paid_at->format('Y-m-d H:i') }}</span>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5>الإجراءات</h5>
                            <div class="btn-group" role="group">
                                @if($settlement->status === \App\Enums\SettlementStatus::PENDING)
                                    <form method="POST" action="{{ route('dashboard.settlements.calculate', $settlement) }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-primary" onclick="return confirm('هل تريد حساب التسوية؟')">
                                            <i class="fas fa-calculator"></i> حساب التسوية
                                        </button>
                                    </form>
                                @endif

                                @if($settlement->status === \App\Enums\SettlementStatus::CALCULATED)
                                    <button type="button" class="btn btn-success" onclick="approveSettlement({{ $settlement->id }})">
                                        <i class="fas fa-check"></i> اعتماد
                                    </button>
                                    <button type="button" class="btn btn-danger" onclick="rejectSettlement({{ $settlement->id }})">
                                        <i class="fas fa-times"></i> رفض
                                    </button>
                                @endif

                                @if($settlement->status === \App\Enums\SettlementStatus::APPROVED)
                                    <button type="button" class="btn btn-success" onclick="markAsPaid({{ $settlement->id }})">
                                        <i class="fas fa-money-bill"></i> تسجيل الدفع
                                    </button>
                                @endif

                                @if($settlement->status === \App\Enums\SettlementStatus::PENDING)
                                    <form method="POST" action="{{ route('dashboard.settlements.destroy', $settlement) }}" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('هل تريد حذف التسوية؟')">
                                            <i class="fas fa-trash"></i> حذف
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Settlement Items -->
                    @if($settlement->settlementItems->count() > 0)
                    <div class="row">
                        <div class="col-12">
                            <h5>تفاصيل الحجوزات</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>تاريخ الحجز</th>
                                            <th>اسم العميل</th>
                                            <th>اسم الرحلة</th>
                                            <th>المدة</th>
                                            <th>السعر الوحدة</th>
                                            <th>إجمالي السعر</th>
                                            <th>ملاحظات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($settlement->settlementItems as $item)
                                        <tr>
                                            <td>{{ $item->booking_date->format('Y-m-d') }}</td>
                                            <td>{{ $item->client_name }}</td>
                                            <td>{{ $item->tour_name }}</td>
                                            <td>{{ $item->duration_hours }}h {{ $item->duration_days }}d</td>
                                            <td>{{ $item->currency }} {{ number_format($item->unit_price, 2) }}</td>
                                            <td>{{ $item->currency }} {{ number_format($item->total_price, 2) }}</td>
                                            <td>{{ $item->notes ?? '-' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-primary">
                                            <th colspan="5">المجموع</th>
                                            <th>{{ $settlement->currency }} {{ number_format($settlement->total_amount, 2) }}</th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Notes -->
                    @if($settlement->notes)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>ملاحظات</h5>
                            <div class="alert alert-info">
                                {{ $settlement->notes }}
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Rejection Reason -->
                    @if($settlement->status === \App\Enums\SettlementStatus::REJECTED && $settlement->rejection_reason)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>سبب الرفض</h5>
                            <div class="alert alert-danger">
                                {{ $settlement->rejection_reason }}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
@include('dashboard.settlements.modals.approve')
@include('dashboard.settlements.modals.reject')
@include('dashboard.settlements.modals.mark-paid')
@endsection

<!-- Load jQuery from CDN -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
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
