@extends('layouts.dashboard.app')

@section('title', 'Edit Settlement - ' . $settlement->settlement_number)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Settlement - {{ $settlement->settlement_number }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('dashboard.settlements.show', $settlement) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <form action="{{ route('dashboard.settlements.update', $settlement) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <!-- Settlement Information (Read Only) -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5>معلومات التسوية (غير قابلة للتعديل)</h5>
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>رقم التسوية:</strong></td>
                                        <td>{{ $settlement->settlement_number }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>نوع التسوية:</strong></td>
                                        <td>{{ $settlement->settlement_type_label }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>المورد:</strong></td>
                                        <td>{{ $settlement->resource_name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>الفترة:</strong></td>
                                        <td>{{ $settlement->month_year }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h5>الحالة الحالية</h5>
                                <div class="alert alert-{{ $settlement->status->getColor() }}">
                                    <strong>الحالة:</strong> {{ $settlement->status_label }}
                                </div>
                                @if($settlement->status->value !== 'pending')
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        Cannot edit settlement in this status
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if($settlement->status->value === 'pending')
                        <!-- Editable Fields -->
                        <div class="row">
                            <!-- Commission Settings -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="commission_type">Commission Type <span class="text-danger">*</span></label>
                                    <select class="form-control @error('commission_type') is-invalid @enderror" 
                                            id="commission_type" name="commission_type" required>
                                        <option value="">Select Commission Type</option>
                                        <option value="percentage" {{ old('commission_type', $settlement->commission_type->value) == 'percentage' ? 'selected' : '' }}>Percentage</option>
                                        <option value="fixed" {{ old('commission_type', $settlement->commission_type->value) == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                                        <option value="none" {{ old('commission_type', $settlement->commission_type->value) == 'none' ? 'selected' : '' }}>No Commission</option>
                                    </select>
                                    @error('commission_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="commission_value">Commission Value</label>
                                    <input type="number" step="0.01" min="0" 
                                           class="form-control @error('commission_value') is-invalid @enderror" 
                                           id="commission_value" name="commission_value" 
                                           value="{{ old('commission_value', $settlement->commission_value) }}" placeholder="0.00">
                                    @error('commission_value')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="tax_rate">Tax Rate (%)</label>
                                    <input type="number" step="0.01" min="0" max="100" 
                                           class="form-control @error('tax_rate') is-invalid @enderror" 
                                           id="tax_rate" name="tax_rate" 
                                           value="{{ old('tax_rate', $settlement->tax_rate) }}" placeholder="0.00">
                                    @error('tax_rate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Additional Settings -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="deductions">Deductions</label>
                                    <input type="number" step="0.01" min="0" 
                                           class="form-control @error('deductions') is-invalid @enderror" 
                                           id="deductions" name="deductions" 
                                           value="{{ old('deductions', $settlement->deductions) }}" placeholder="0.00">
                                    @error('deductions')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="bonuses">Bonuses</label>
                                    <input type="number" step="0.01" min="0" 
                                           class="form-control @error('bonuses') is-invalid @enderror" 
                                           id="bonuses" name="bonuses" 
                                           value="{{ old('bonuses', $settlement->bonuses) }}" placeholder="0.00">
                                    @error('bonuses')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="notes">ملاحظات</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              id="notes" name="notes" rows="3" 
                                              placeholder="أي ملاحظات إضافية...">{{ old('notes', $settlement->notes) }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        @else
                        <!-- Read Only Fields -->
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>نوع العمولة</label>
                                    <input type="text" class="form-control" value="{{ $settlement->commission_type_label }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>قيمة العمولة</label>
                                    <input type="text" class="form-control" value="{{ $settlement->commission_value }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>معدل الضريبة</label>
                                    <input type="text" class="form-control" value="{{ $settlement->tax_rate }}%" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>الخصومات</label>
                                    <input type="text" class="form-control" value="{{ $settlement->deductions }}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>المكافآت</label>
                                    <input type="text" class="form-control" value="{{ $settlement->bonuses }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Notes</label>
                                    <textarea class="form-control" rows="3" readonly>{{ $settlement->notes }}</textarea>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="card-footer">
                        @if($settlement->status->value === 'pending')
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                        @endif
                        <a href="{{ route('dashboard.settlements.show', $settlement) }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

<!-- Load jQuery from CDN -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    // Handle commission type change
    $('#commission_type').on('change', function() {
        const commissionType = $(this).val();
        const commissionValueField = $('#commission_value');
        
        if (commissionType === 'none') {
            commissionValueField.prop('disabled', true).val('0');
        } else {
            commissionValueField.prop('disabled', false);
        }
    });

    // Initialize form
    $('#commission_type').trigger('change');
});

// Fallback using vanilla JavaScript if jQuery fails
if (typeof $ === 'undefined') {
    document.addEventListener('DOMContentLoaded', function() {
        const commissionTypeSelect = document.getElementById('commission_type');
        const commissionValueField = document.getElementById('commission_value');
        
        if (commissionTypeSelect && commissionValueField) {
            commissionTypeSelect.addEventListener('change', function() {
                const commissionType = this.value;
                
                if (commissionType === 'none') {
                    commissionValueField.disabled = true;
                    commissionValueField.value = '0';
                } else {
                    commissionValueField.disabled = false;
                }
            });
            
            // Initialize form
            commissionTypeSelect.dispatchEvent(new Event('change'));
        }
    });
}
</script>
