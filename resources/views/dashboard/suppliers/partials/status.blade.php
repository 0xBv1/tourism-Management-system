@if(!$supplier->is_verified)
    <span class="badge bg-warning">Pending Verification</span>
@else
    <span class="badge {{ $supplier->is_active ? 'bg-success' : 'bg-secondary' }}">
        {{ $supplier->is_active ? 'Active' : 'Inactive' }}
    </span>
@endif


