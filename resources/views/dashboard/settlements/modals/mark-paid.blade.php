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
