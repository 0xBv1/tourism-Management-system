<div class="d-flex justify-content-center">
    <a href="{{ route('dashboard.bookings.show', $id) }}" class="btn btn-sm btn-primary me-1" title="View">
        <i class="fa fa-eye"></i>
    </a>
    
    <a href="{{ route('dashboard.bookings.download', $id) }}" class="btn btn-sm btn-success me-1" title="Download">
        <i class="fa fa-download"></i>
    </a>
    
    <a href="{{ route('dashboard.bookings.send', $id) }}" class="btn btn-sm btn-info me-1" title="Send">
        <i class="fa fa-paper-plane"></i>
    </a>
    
    <button type="button" class="btn btn-sm btn-danger" onclick="deleteBooking({{ $id }})" title="Delete">
        <i class="fa fa-trash"></i>
    </button>
</div>

<script>
function deleteBooking(id) {
    if (confirm('Are you sure you want to delete this booking?')) {
        fetch(`/dashboard/bookings/${id}`, {
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
            alert('An error occurred while deleting the booking.');
        });
    }
}
</script>
