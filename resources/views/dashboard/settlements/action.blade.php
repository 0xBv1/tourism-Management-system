@if($status === 'pending')
    <div class="d-flex justify-content-center">
        <a href="{{ route('dashboard.settlements.show', $id) }}" class="btn btn-sm btn-primary me-1" title="View">
            <i class="fa fa-eye"></i>
        </a>
        <a href="{{ route('dashboard.settlements.edit', $id) }}" class="btn btn-sm btn-warning me-1" title="Edit">
            <i class="fa fa-edit"></i>
        </a>
        <form method="POST" action="{{ route('dashboard.settlements.calculate', $id) }}" style="display: inline;">
            @csrf
            <button type="submit" class="btn btn-sm btn-info me-1" title="Calculate" onclick="return confirm('Are you sure you want to calculate this settlement?')">
                <i class="fa fa-calculator"></i>
            </button>
        </form>
    </div>
@elseif($status === 'calculated')
    <div class="d-flex justify-content-center">
        <a href="{{ route('dashboard.settlements.show', $id) }}" class="btn btn-sm btn-primary me-1" title="View">
            <i class="fa fa-eye"></i>
        </a>
        <button type="button" class="btn btn-sm btn-success me-1" onclick="approveSettlement({{ $id }})" title="Approve">
            <i class="fa fa-check"></i>
        </button>
        <button type="button" class="btn btn-sm btn-danger me-1" onclick="rejectSettlement({{ $id }})" title="Reject">
            <i class="fa fa-times"></i>
        </button>
    </div>
@elseif($status === 'approved')
    <div class="d-flex justify-content-center">
        <a href="{{ route('dashboard.settlements.show', $id) }}" class="btn btn-sm btn-primary me-1" title="View">
            <i class="fa fa-eye"></i>
        </a>
        <button type="button" class="btn btn-sm btn-success me-1" onclick="markAsPaid({{ $id }})" title="Mark as Paid">
            <i class="fa fa-money-bill"></i>
        </button>
    </div>
@elseif($status === 'rejected')
    <div class="d-flex justify-content-center">
        <a href="{{ route('dashboard.settlements.show', $id) }}" class="btn btn-sm btn-primary me-1" title="View">
            <i class="fa fa-eye"></i>
        </a>
        <a href="{{ route('dashboard.settlements.edit', $id) }}" class="btn btn-sm btn-warning me-1" title="Edit">
            <i class="fa fa-edit"></i>
        </a>
    </div>
@elseif($status === 'paid')
    <div class="d-flex justify-content-center">
        <a href="{{ route('dashboard.settlements.show', $id) }}" class="btn btn-sm btn-primary me-1" title="View">
            <i class="fa fa-eye"></i>
        </a>
        <span class="btn btn-sm btn-secondary" title="Paid">
            <i class="fa fa-check-circle"></i>
        </span>
    </div>
@else
    <div class="d-flex justify-content-center">
        <a href="{{ route('dashboard.settlements.show', $id) }}" class="btn btn-sm btn-primary me-1" title="View">
            <i class="fa fa-eye"></i>
        </a>
        <a href="{{ route('dashboard.settlements.edit', $id) }}" class="btn btn-sm btn-warning me-1" title="Edit">
            <i class="fa fa-edit"></i>
        </a>
    </div>
@endif
