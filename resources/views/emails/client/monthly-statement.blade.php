<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Monthly Statement</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: #6c757d;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 5px 5px;
        }
        .statement-details {
            background: white;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .summary-cards {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
        }
        .summary-card {
            text-align: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
            flex: 1;
            margin: 0 5px;
        }
        .summary-card h3 {
            margin: 0;
            color: #007bff;
        }
        .summary-card p {
            margin: 5px 0 0 0;
            color: #666;
        }
        .statement-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .statement-table th,
        .statement-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .statement-table th {
            background: #f8f9fa;
            font-weight: bold;
        }
        .amount {
            font-weight: bold;
            color: #28a745;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Monthly Statement</h1>
        <p>{{ $startDate->format('F Y') }}</p>
    </div>
    
    <div class="content">
        <h2>Dear {{ $client->name }},</h2>
        
        <p>Please find your monthly statement for {{ $startDate->format('F Y') }} below:</p>
        
        <div class="statement-details">
            <div class="summary-cards">
                <div class="summary-card">
                    <h3>{{ $statementData['total_bookings'] ?? 0 }}</h3>
                    <p>Total Bookings</p>
                </div>
                <div class="summary-card">
                    <h3>{{ $statementData['total_payments'] ?? 0 }}</h3>
                    <p>Total Payments</p>
                </div>
                <div class="summary-card">
                    <h3 class="amount">${{ number_format($statementData['total_amount'] ?? 0, 2) }}</h3>
                    <p>Total Amount</p>
                </div>
                <div class="summary-card">
                    <h3 class="amount">${{ number_format($statementData['paid_amount'] ?? 0, 2) }}</h3>
                    <p>Paid Amount</p>
                </div>
            </div>
            
            <h3>Statement Period</h3>
            <p><strong>From:</strong> {{ $startDate->format('F d, Y') }}</p>
            <p><strong>To:</strong> {{ $endDate->format('F d, Y') }}</p>
        </div>
        
        @if(isset($statementData['bookings']) && count($statementData['bookings']) > 0)
        <div class="statement-details">
            <h3>Bookings This Month</h3>
            <table class="statement-table">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>File Name</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($statementData['bookings'] as $booking)
                    <tr>
                        <td>#{{ $booking['id'] }}</td>
                        <td>{{ $booking['file_name'] }}</td>
                        <td class="amount">{{ $booking['currency'] }} {{ number_format($booking['total_amount'], 2) }}</td>
                        <td>{{ $booking['status'] }}</td>
                        <td>{{ $booking['created_at'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
        
        @if(isset($statementData['payments']) && count($statementData['payments']) > 0)
        <div class="statement-details">
            <h3>Payments This Month</h3>
            <table class="statement-table">
                <thead>
                    <tr>
                        <th>Payment ID</th>
                        <th>Reference</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($statementData['payments'] as $payment)
                    <tr>
                        <td>#{{ $payment['id'] }}</td>
                        <td>{{ $payment['reference_number'] ?? 'N/A' }}</td>
                        <td class="amount">{{ $payment['currency'] }} {{ number_format($payment['amount'], 2) }}</td>
                        <td>{{ $payment['status'] }}</td>
                        <td>{{ $payment['paid_at'] ?? $payment['created_at'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
        
        <div class="statement-details">
            <h3>Account Summary</h3>
            <table class="statement-table">
                <tr>
                    <th>Total Bookings:</th>
                    <td>{{ $statementData['total_bookings'] ?? 0 }}</td>
                </tr>
                <tr>
                    <th>Total Amount:</th>
                    <td class="amount">${{ number_format($statementData['total_amount'] ?? 0, 2) }}</td>
                </tr>
                <tr>
                    <th>Total Paid:</th>
                    <td class="amount">${{ number_format($statementData['paid_amount'] ?? 0, 2) }}</td>
                </tr>
                <tr>
                    <th>Outstanding Balance:</th>
                    <td class="amount">${{ number_format(($statementData['total_amount'] ?? 0) - ($statementData['paid_amount'] ?? 0), 2) }}</td>
                </tr>
            </table>
        </div>
        
        <p>If you have any questions about this statement, please contact us.</p>
        
        <div class="footer">
            <p>Best regards,<br>
            Tourism Management Team</p>
            <p><small>This is an automated message. Please do not reply to this email.</small></p>
        </div>
    </div>
</body>
</html>
