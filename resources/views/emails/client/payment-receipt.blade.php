<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment Receipt</title>
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
            background: #28a745;
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
        .payment-details {
            background: white;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .amount {
            font-size: 32px;
            font-weight: bold;
            color: #28a745;
            text-align: center;
            margin: 20px 0;
        }
        .receipt-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .receipt-table th,
        .receipt-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .receipt-table th {
            background: #f8f9fa;
            font-weight: bold;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-paid {
            background: #d4edda;
            color: #155724;
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
        <h1>Payment Receipt</h1>
        <p>Thank you for your payment!</p>
    </div>
    
    <div class="content">
        <h2>Dear {{ $client->name }},</h2>
        
        <p>We have received your payment. Here are the details:</p>
        
        <div class="payment-details">
            <div class="amount">{{ $payment->formatted_amount }}</div>
            
            <table class="receipt-table">
                <tr>
                    <th>Payment ID:</th>
                    <td>#{{ $payment->id }}</td>
                </tr>
                <tr>
                    <th>Reference Number:</th>
                    <td>{{ $payment->reference_number ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Status:</th>
                    <td>
                        <span class="status-badge status-paid">{{ $payment->status->getLabel() }}</span>
                    </td>
                </tr>
                <tr>
                    <th>Payment Method:</th>
                    <td>{{ ucfirst(str_replace('_', ' ', $payment->gateway)) }}</td>
                </tr>
                <tr>
                    <th>Paid Date:</th>
                    <td>{{ $payment->paid_at->format('F d, Y H:i') }}</td>
                </tr>
                <tr>
                    <th>Booking File:</th>
                    <td>{{ $bookingFile->file_name }}</td>
                </tr>
            </table>
            
            @if($payment->notes)
            <h4>Payment Notes:</h4>
            <p>{{ $payment->notes }}</p>
            @endif
        </div>
        
        <div class="payment-details">
            <h3>Booking Summary</h3>
            <p><strong>Total Booking Amount:</strong> {{ $bookingFile->currency }} {{ number_format($bookingFile->total_amount, 2) }}</p>
            <p><strong>Total Paid:</strong> {{ $bookingFile->currency }} {{ number_format($bookingFile->total_paid, 2) }}</p>
            <p><strong>Remaining Balance:</strong> {{ $bookingFile->currency }} {{ number_format($bookingFile->remaining_amount, 2) }}</p>
        </div>
        
        <p>This receipt serves as confirmation of your payment. Please keep this for your records.</p>
        
        <p>If you have any questions about this payment, please contact us.</p>
        
        <div class="footer">
            <p>Best regards,<br>
            Tourism Management Team</p>
            <p><small>This is an automated message. Please do not reply to this email.</small></p>
        </div>
    </div>
</body>
</html>
