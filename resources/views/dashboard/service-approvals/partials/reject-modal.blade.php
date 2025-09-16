<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Reject Service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
                        <textarea 
                            class="form-control @error('rejection_reason') is-invalid @enderror" 
                            id="rejection_reason" 
                            name="rejection_reason" 
                            rows="4" 
                            placeholder="Please provide a detailed reason for rejecting this service..."
                            required
                            minlength="10"
                            maxlength="1000">{{ old('rejection_reason') }}</textarea>
                        @error('rejection_reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Minimum 10 characters, maximum 1000 characters.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Service</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const rejectModal = document.getElementById('rejectModal');
    const rejectForm = document.getElementById('rejectForm');
    
    rejectModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const approvalId = button.getAttribute('data-approval-id');
        const formAction = `{{ url('dashboard/service-approvals') }}/${approvalId}/reject`;
        rejectForm.setAttribute('action', formAction);
    });
    
    rejectForm.addEventListener('submit', function(e) {
        const reason = document.getElementById('rejection_reason').value.trim();
        if (reason.length < 10) {
            e.preventDefault();
            alert('Please provide a reason with at least 10 characters.');
            return false;
        }
        
        if (!confirm('Are you sure you want to reject this service?')) {
            e.preventDefault();
            return false;
        }
    });
});
</script>
