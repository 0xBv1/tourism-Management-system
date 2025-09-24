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
                <div class="info-value"><strong>#{{ $booking_id }}</strong></div>
            </div>
            <div class="info-row">
                <div class="info-label">Inquiry ID:</div>
                <div class="info-value">{{ $inquiry->inquiry_id ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Generated Date:</div>
                <div class="info-value">{{ $generated_at->format('F d, Y \a\t H:i') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Confirmed Date:</div>
                <div class="info-value">{{ $inquiry->confirmed_at ? $inquiry->confirmed_at->format('F d, Y \a\t H:i') : 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Status:</div>
                <div class="info-value"><span class="status-badge">CONFIRMED</span></div>
            </div>
            <div class="info-row">
                <div class="info-label">Created Date:</div>
                <div class="info-value">{{ $inquiry->created_at->format('F d, Y \a\t H:i') }}</div>
            </div>
        </div>
    </div>

    <div class="section">
        <h3>Customer Information</h3>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Full Name:</div>
                <div class="info-value"><strong>{{ $inquiry->guest_name ?? 'N/A' }}</strong></div>
            </div>
            <div class="info-row">
                <div class="info-label">Email Address:</div>
                <div class="info-value">{{ $inquiry->email ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Phone Number:</div>
                <div class="info-value">{{ $inquiry->phone ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Nationality:</div>
                <div class="info-value">{{ $inquiry->nationality ?? 'N/A' }}</div>
            </div>
            @if($inquiry->client)
            <div class="info-row">
                <div class="info-label">Client ID:</div>
                <div class="info-value">{{ $inquiry->client->id ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Client Name:</div>
                <div class="info-value">{{ $inquiry->client->name ?? 'N/A' }}</div>
            </div>
            @endif
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
                <div class="info-label">Duration:</div>
                <div class="info-value">
                    @if($inquiry->arrival_date && $inquiry->departure_date)
                        {{ $inquiry->arrival_date->diffInDays($inquiry->departure_date) + 1 }} days
                    @else
                        N/A
                    @endif
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Number of Pax:</div>
                <div class="info-value">{{ $inquiry->number_pax ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Inquiry ID:</div>
                <div class="info-value">{{ $inquiry->inquiry_id ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Created Date:</div>
                <div class="info-value">{{ $inquiry->created_at->format('F d, Y \a\t H:i') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Confirmed Date:</div>
                <div class="info-value">{{ $inquiry->confirmed_at ? $inquiry->confirmed_at->format('F d, Y \a\t H:i') : 'N/A' }}</div>
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
                <div class="info-row">
                    <div class="info-label">Payment Status:</div>
                    <div class="info-value">
                        @if(($inquiry->paid_amount ?? 0) >= $inquiry->total_amount)
                            <span style="color: green; font-weight: bold;">FULLY PAID</span>
                        @elseif(($inquiry->paid_amount ?? 0) > 0)
                            <span style="color: orange; font-weight: bold;">PARTIALLY PAID</span>
                        @else
                            <span style="color: red; font-weight: bold;">NOT PAID</span>
                        @endif
                    </div>
                </div>
                @if($inquiry->payment_method)
                <div class="info-row">
                    <div class="info-label">Payment Method:</div>
                    <div class="info-value">{{ $inquiry->payment_method }}</div>
                </div>
                @endif
                <div class="info-row">
                    <div class="info-label">Currency:</div>
                    <div class="info-value">USD (US Dollar)</div>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($inquiry->bookingFile && $inquiry->bookingFile->payments->count() > 0)
    <div class="section">
        <h3>Payment History</h3>
        <div class="info-grid">
            @foreach($inquiry->bookingFile->payments as $payment)
                <div class="info-row">
                    <div class="info-label">Payment #{{ $payment->id }}:</div>
                    <div class="info-value">
                        <strong>USD {{ number_format($payment->amount, 2) }}</strong>
                        <br>
                        <small class="text-muted">
                            Date: {{ $payment->created_at->format('M d, Y H:i') }}
                            @if($payment->payment_method)
                                | Method: {{ $payment->payment_method }}
                            @endif
                            @if($payment->status)
                                | Status: {{ ucfirst($payment->status) }}
                            @endif
                        </small>
                        @if($payment->notes)
                            <br><small class="text-muted">Notes: {{ $payment->notes }}</small>
                        @endif
                    </div>
                </div>
            @endforeach
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

    @php
        $assignedUsers = $inquiry->getAllAssignedUsers();
    @endphp
    
    @if(!empty($assignedUsers))
    <div class="section">
        <h3>User Assignments</h3>
        <div class="info-grid">
            @foreach($assignedUsers as $assignment)
                <div class="info-row">
                    <div class="info-label">
                        @if($assignment['type'] === 'user')
                            {{ $assignment['role'] }}:
                        @elseif($assignment['type'] === 'resource')
                            {{ $assignment['role'] }}:
                        @endif
                    </div>
                    <div class="info-value">
                        @if($assignment['type'] === 'user')
                            <strong>{{ $assignment['user']->name }}</strong>
                        @elseif($assignment['type'] === 'resource')
                            <strong>{{ $assignment['resource']->resource_name }}</strong>
                            @if($assignment['added_by'])
                                <br><small class="text-muted">Added by: {{ $assignment['added_by']->name }}</small>
                            @endif
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @else
    <div class="section">
        <h3>User Assignments</h3>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Status:</div>
                <div class="info-value">No users or resources assigned to this inquiry.</div>
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
