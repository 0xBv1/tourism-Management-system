<div class="d-flex justify-content-center">
    <a href="{{ route('dashboard.payments.show', $id) }}" class="btn btn-sm btn-info me-1" title="View">
        <i class="fa fa-eye"></i>
    </a>
    
    @if(admin()->can('payments.edit'))
    <a href="{{ route('dashboard.payments.edit', $id) }}" class="btn btn-sm btn-warning me-1" title="Edit">
        <i class="fa fa-edit"></i>
    </a>
    @endif
    
    @if($status === 'not_paid')
    <button type="button" class="btn btn-sm btn-success me-1 mark-as-paid-btn" 
            data-payment-id="{{ $id }}" title="Mark as Paid">
        <i class="fa fa-check"></i>
    </button>
    @endif
    
    @if(admin()->can('payments.delete'))
    <button type="button" class="btn btn-sm btn-danger delete-btn" 
            data-payment-id="{{ $id }}" title="Delete">
        <i class="fa fa-trash"></i>
    </button>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mark as paid functionality
    document.querySelectorAll('.mark-as-paid-btn').forEach(button => {
        button.addEventListener('click', function() {
            const paymentId = this.getAttribute('data-payment-id');
            
            if (confirm('Are you sure you want to mark this payment as paid?')) {
                fetch(`/dashboard/payments/${paymentId}/mark-as-paid`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.message) {
                        alert(data.message);
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while marking payment as paid.');
                });
            }
        });
    });

    // Delete functionality
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const paymentId = this.getAttribute('data-payment-id');
            
            if (confirm('Are you sure you want to delete this payment?')) {
                fetch(`/dashboard/payments/${paymentId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.message) {
                        alert(data.message);
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the payment.');
                });
            }
        });
    });
});
</script>

