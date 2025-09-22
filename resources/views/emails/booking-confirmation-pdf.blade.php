<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Booking Confirmation - #{{ $booking_id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #007bff;
            margin: 0;
            font-size: 24px;
        }
        .header h2 {
            color: #666;
            margin: 5px 0 0 0;
            font-size: 16px;
            font-weight: normal;
        }
        .section {
            margin-bottom: 25px;
        }
        .section h3 {
            color: #007bff;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 15px;
            font-size: 14px;
        }
        .info-grid {
            display: table;
            width: 100%;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            font-weight: bold;
            width: 30%;
            padding: 5px 0;
            vertical-align: top;
        }
        .info-value {
            display: table-cell;
            padding: 5px 0 5px 20px;
            vertical-align: top;
        }
        .status-badge {
            background-color: #28a745;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 10px;
        }
        .amount-highlight {
            background-color: #f8f9fa;
            padding: 10px;
            border-left: 4px solid #007bff;
            margin: 10px 0;
        }
        .notes {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>BOOKING CONFIRMATION</h1>
        <h2>Tourism Management System</h2>
    </div>

    <div class="section">
        <h3>Booking Information</h3>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Booking ID:</div>
                <div class="info-value">#{{ $booking_id }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Generated Date:</div>
                <div class="info-value">{{ $generated_at->format('F d, Y \a\t H:i') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Status:</div>
                <div class="info-value"><span class="status-badge">CONFIRMED</span></div>
            </div>
        </div>
    </div>

    <div class="section">
        <h3>Customer Information</h3>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Name:</div>
                <div class="info-value">{{ $inquiry->guest_name ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Email:</div>
                <div class="info-value">{{ $inquiry->email ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Phone:</div>
                <div class="info-value">{{ $inquiry->phone ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Nationality:</div>
                <div class="info-value">{{ $inquiry->nationality ?? 'N/A' }}</div>
            </div>
        </div>
    </div>

    <div class="section">
        <h3>Tour Details</h3>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Subject:</div>
                <div class="info-value">{{ $inquiry->subject ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Tour Name:</div>
                <div class="info-value">{{ $inquiry->tour_name ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Arrival Date:</div>
                <div class="info-value">{{ $inquiry->arrival_date ? $inquiry->arrival_date->format('F d, Y') : 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Departure Date:</div>
                <div class="info-value">{{ $inquiry->departure_date ? $inquiry->departure_date->format('F d, Y') : 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Number of Pax:</div>
                <div class="info-value">{{ $inquiry->number_pax ?? 'N/A' }}</div>
            </div>
        </div>
    </div>

    @if($inquiry->total_amount)
    <div class="section">
        <h3>Financial Information</h3>
        <div class="amount-highlight">
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Total Amount:</div>
                    <div class="info-value"><strong>USD {{ number_format($inquiry->total_amount, 2) }}</strong></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Amount Paid:</div>
                    <div class="info-value">USD {{ number_format($inquiry->paid_amount ?? 0, 2) }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Remaining Amount:</div>
                    <div class="info-value">USD {{ number_format($inquiry->remaining_amount ?? $inquiry->total_amount, 2) }}</div>
                </div>
                @if($inquiry->payment_method)
                <div class="info-row">
                    <div class="info-label">Payment Method:</div>
                    <div class="info-value">{{ $inquiry->payment_method }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    @if($inquiry->message)
    <div class="section">
        <h3>Special Requests</h3>
        <div class="notes">
            {{ $inquiry->message }}
        </div>
    </div>
    @endif

    @if($inquiry->assignedUser)
    <div class="section">
        <h3>Assigned Staff</h3>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Assigned To:</div>
                <div class="info-value">{{ $inquiry->assignedUser->name }}</div>
            </div>
        </div>
    </div>
    @endif

    @if($inquiry->admin_notes)
    <div class="section">
        <h3>Admin Notes</h3>
        <div class="notes">
            {{ $inquiry->admin_notes }}
        </div>
    </div>
    @endif

    <div class="footer">
        <p>This booking has been confirmed and is ready for processing.</p>
        <p>Generated on {{ $generated_at->format('F d, Y \a\t H:i') }} by Tourism Management System</p>
        <p>For any questions, please contact our support team.</p>
    </div>
</body>
</html>
