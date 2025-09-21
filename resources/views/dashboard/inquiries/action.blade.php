<div class="d-flex justify-content-center">
    @if(admin()->can('inquiries.show'))
        <a href="{{ route('dashboard.inquiries.show', $id) }}" class="btn btn-primary btn-sm me-1" title="View">
            <i class="fa fa-eye"></i>
        </a>
    @endif
    @if(admin()->can('inquiries.edit'))
        <a href="{{ route('dashboard.inquiries.edit', $id) }}" class="btn btn-warning btn-sm me-1" title="Edit">
            <i class="fa fa-edit"></i>
        </a>
    @endif
    @if(admin()->can('inquiries.delete'))
        <button type="button" class="btn btn-danger btn-sm" onclick="deleteInquiry({{ $id }})" title="Delete">
            <i class="fa fa-trash"></i>
        </button>
    @endif
</div>

<script>
function deleteInquiry(id) {
    if (confirm('Are you sure you want to delete this inquiry?')) {
        fetch(`/dashboard/inquiries/${id}`, {
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
            alert('An error occurred while deleting the inquiry.');
        });
    }
}
</script>





