<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Booking Confirmation</title>
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
            background: #007bff;
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
        .booking-details {
            background: white;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-confirmed {
            background: #d4edda;
            color: #155724;
        }
        .amount {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
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
        <h1>Booking Confirmation</h1>
        <p>Your booking has been confirmed!</p>
    </div>
    
    <div class="content">
        <h2>Dear {{ $client->name }},</h2>
        
        <p>We are pleased to confirm your booking. Here are the details:</p>
        
        <div class="booking-details">
            <h3>Booking Information</h3>
            <p><strong>Booking File:</strong> {{ $bookingFile->file_name }}</p>
            <p><strong>Status:</strong> 
                <span class="status-badge status-confirmed">{{ $bookingFile->status->getLabel() }}</span>
            </p>
            <p><strong>Total Amount:</strong> 
                <span class="amount">{{ $bookingFile->currency }} {{ number_format($bookingFile->total_amount, 2) }}</span>
            </p>
            <p><strong>Booking Date:</strong> {{ $bookingFile->created_at->format('F d, Y') }}</p>
            
            @if($bookingFile->notes)
            <h4>Additional Notes:</h4>
            <p>{{ $bookingFile->notes }}</p>
            @endif
        </div>
        
        <div class="booking-details">
            <h3>Inquiry Details</h3>
            <p><strong>Subject:</strong> {{ $inquiry->subject }}</p>
            <p><strong>Message:</strong> {{ $inquiry->message }}</p>
            @if($inquiry->admin_notes)
            <p><strong>Admin Notes:</strong> {{ $inquiry->admin_notes }}</p>
            @endif
        </div>
        
        <p>If you have any questions or need to make changes to your booking, please don't hesitate to contact us.</p>
        
        <p>Thank you for choosing our services!</p>
        
        <div class="footer">
            <p>Best regards,<br>
            Tourism Management Team</p>
            <p><small>This is an automated message. Please do not reply to this email.</small></p>
        </div>
    </div>
</body>
</html>
