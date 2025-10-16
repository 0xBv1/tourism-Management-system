<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\BookingFile;
use App\Enums\PaymentStatus;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bookingFiles = BookingFile::all();
        
        if ($bookingFiles->isEmpty()) {
            $this->command->warn('No booking files found. Please run BookingFileSeeder first.');
            return;
        }
        
        $payments = [];
        
        foreach ($bookingFiles as $bookingFile) {
            $totalAmount = $bookingFile->total_amount;
            $paidAmount = 0;
            
            // Generate multiple payments for each booking file
            $paymentCount = rand(1, 4);
            $remainingAmount = $totalAmount;
            
            for ($i = 0; $i < $paymentCount; $i++) {
                $isLastPayment = ($i === $paymentCount - 1);
                $paymentAmount = $isLastPayment ? $remainingAmount : round($remainingAmount * (rand(20, 60) / 100), 2);
                
                if ($paymentAmount <= 0) break;
                
                $status = $this->getPaymentStatus($bookingFile->status, $i, $paymentCount);
                $paidAt = $status === PaymentStatus::PAID ? now()->subDays(rand(1, 30)) : null;
                
                $payments[] = [
                    'invoice_id' => 'INV-' . $bookingFile->inquiry_id . '-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                    'booking_id' => $bookingFile->id,
                    'gateway' => $this->getRandomGateway(),
                    'amount' => $paymentAmount,
                    'status' => $status,
                    'paid_at' => $paidAt,
                    'transaction_request' => $this->generateTransactionRequest($status),
                    'transaction_verification' => $this->generateTransactionVerification($status),
                    'notes' => $this->generatePaymentNotes($status, $i + 1, $paymentCount),
                    'reference_number' => null, // Will be auto-generated
                ];
                
                $paidAmount += $paymentAmount;
                $remainingAmount -= $paymentAmount;
                
                if ($remainingAmount <= 0) break;
            }
        }
        
        // Add some standalone payments for different scenarios
        $additionalPayments = [
            [
                'invoice_id' => 'INV-STANDALONE-001',
                'booking_id' => $bookingFiles->random()->id,
                'gateway' => 'paypal',
                'amount' => 500.00,
                'status' => PaymentStatus::PAID,
                'paid_at' => now()->subDays(15),
                'transaction_request' => [
                    'method' => 'POST',
                    'url' => 'https://api.paypal.com/v1/payments',
                    'headers' => ['Content-Type: application/json'],
                    'body' => ['amount' => 500.00, 'currency' => 'USD']
                ],
                'transaction_verification' => [
                    'transaction_id' => 'PAYPAL-' . strtoupper(uniqid()),
                    'status' => 'completed',
                    'verified_at' => now()->subDays(15),
                    'gateway_response' => 'Payment successful'
                ],
                'notes' => 'Initial deposit payment via PayPal',
                'reference_number' => null,
            ],
            [
                'invoice_id' => 'INV-STANDALONE-002',
                'booking_id' => $bookingFiles->random()->id,
                'gateway' => 'stripe',
                'amount' => 750.00,
                'status' => PaymentStatus::PENDING,
                'paid_at' => null,
                'transaction_request' => [
                    'method' => 'POST',
                    'url' => 'https://api.stripe.com/v1/charges',
                    'headers' => ['Authorization: Bearer sk_test_...'],
                    'body' => ['amount' => 75000, 'currency' => 'usd', 'source' => 'tok_visa']
                ],
                'transaction_verification' => null,
                'notes' => 'Payment processing via Stripe. Awaiting confirmation.',
                'reference_number' => null,
            ],
            [
                'invoice_id' => 'INV-STANDALONE-003',
                'booking_id' => $bookingFiles->random()->id,
                'gateway' => 'bank_transfer',
                'amount' => 1200.00,
                'status' => PaymentStatus::NOT_PAID,
                'paid_at' => null,
                'transaction_request' => [
                    'method' => 'BANK_TRANSFER',
                    'bank_details' => [
                        'bank_name' => 'National Bank of Egypt',
                        'account_number' => '1234567890',
                        'swift_code' => 'NBELEGCX',
                        'routing_number' => '123456789'
                    ],
                    'instructions' => 'Transfer to tourism account'
                ],
                'transaction_verification' => null,
                'notes' => 'Bank transfer payment. Client to initiate transfer.',
                'reference_number' => null,
            ],
            [
                'invoice_id' => 'INV-STANDALONE-004',
                'booking_id' => $bookingFiles->random()->id,
                'gateway' => 'cash',
                'amount' => 300.00,
                'status' => PaymentStatus::PAID,
                'paid_at' => now()->subDays(5),
                'transaction_request' => [
                    'method' => 'CASH',
                    'location' => 'Office reception',
                    'received_by' => 'Ahmed Hassan',
                    'witness' => 'Sarah Johnson'
                ],
                'transaction_verification' => [
                    'receipt_number' => 'CASH-' . strtoupper(uniqid()),
                    'status' => 'confirmed',
                    'verified_at' => now()->subDays(5),
                    'notes' => 'Cash payment received at office'
                ],
                'notes' => 'Cash payment received at office reception',
                'reference_number' => null,
            ],
            [
                'invoice_id' => 'INV-STANDALONE-005',
                'booking_id' => $bookingFiles->random()->id,
                'gateway' => 'wire_transfer',
                'amount' => 2000.00,
                'status' => PaymentStatus::PAID,
                'paid_at' => now()->subDays(20),
                'transaction_request' => [
                    'method' => 'WIRE_TRANSFER',
                    'bank_details' => [
                        'bank_name' => 'HSBC Bank Egypt',
                        'account_number' => '9876543210',
                        'swift_code' => 'HSBCEGCX',
                        'beneficiary' => 'Egypt Tourism Company'
                    ],
                    'reference' => 'Tour Payment - ' . $bookingFiles->random()->inquiry_id
                ],
                'transaction_verification' => [
                    'transaction_id' => 'WIRE-' . strtoupper(uniqid()),
                    'status' => 'completed',
                    'verified_at' => now()->subDays(20),
                    'bank_reference' => 'REF-' . strtoupper(uniqid())
                ],
                'notes' => 'Wire transfer payment from international client',
                'reference_number' => null,
            ]
        ];
        
        $allPayments = array_merge($payments, $additionalPayments);
        
        foreach ($allPayments as $paymentData) {
            Payment::create($paymentData);
        }

        $this->command->info('Payments seeded successfully!');
    }
    
    private function getPaymentStatus($bookingStatus, int $paymentIndex, int $totalPayments): PaymentStatus
    {
        // If booking is cancelled or refunded, payments should be refunded
        if (in_array($bookingStatus, ['cancelled', 'refunded'])) {
            return PaymentStatus::NOT_PAID;
        }
        
        // If booking is completed, all payments should be paid
        if ($bookingStatus === 'completed') {
            return PaymentStatus::PAID;
        }
        
        // For other statuses, vary the payment status
        $rand = rand(1, 100);
        
        if ($rand <= 70) {
            return PaymentStatus::PAID;
        } elseif ($rand <= 85) {
            return PaymentStatus::PENDING;
        } else {
            return PaymentStatus::NOT_PAID;
        }
    }
    
    private function getRandomGateway(): string
    {
        $gateways = ['paypal', 'stripe', 'bank_transfer', 'cash', 'wire_transfer', 'credit_card', 'debit_card'];
        return $gateways[array_rand($gateways)];
    }
    
    private function generateTransactionRequest(PaymentStatus $status): ?array
    {
        if ($status === PaymentStatus::NOT_PAID) {
            return null;
        }
        
        $gateway = $this->getRandomGateway();
        
        switch ($gateway) {
            case 'paypal':
                return [
                    'method' => 'POST',
                    'url' => 'https://api.paypal.com/v1/payments',
                    'headers' => ['Content-Type: application/json', 'Authorization: Bearer ' . uniqid()],
                    'body' => ['amount' => rand(100, 2000), 'currency' => 'USD']
                ];
            case 'stripe':
                return [
                    'method' => 'POST',
                    'url' => 'https://api.stripe.com/v1/charges',
                    'headers' => ['Authorization: Bearer sk_test_' . uniqid()],
                    'body' => ['amount' => rand(10000, 200000), 'currency' => 'usd', 'source' => 'tok_visa']
                ];
            case 'bank_transfer':
                return [
                    'method' => 'BANK_TRANSFER',
                    'bank_details' => [
                        'bank_name' => 'National Bank of Egypt',
                        'account_number' => rand(1000000000, 9999999999),
                        'swift_code' => 'NBELEGCX'
                    ]
                ];
            default:
                return [
                    'method' => 'POST',
                    'url' => 'https://api.example.com/payment',
                    'headers' => ['Content-Type: application/json'],
                    'body' => ['amount' => rand(100, 2000), 'currency' => 'USD']
                ];
        }
    }
    
    private function generateTransactionVerification(PaymentStatus $status): ?array
    {
        if ($status !== PaymentStatus::PAID) {
            return null;
        }
        
        return [
            'transaction_id' => strtoupper(uniqid()),
            'status' => 'completed',
            'verified_at' => now()->subDays(rand(1, 30)),
            'gateway_response' => 'Payment successful',
            'reference_number' => 'REF-' . strtoupper(uniqid())
        ];
    }
    
    private function generatePaymentNotes(PaymentStatus $status, int $paymentNumber, int $totalPayments): string
    {
        $notes = [];
        
        if ($paymentNumber === 1) {
            $notes[] = 'Initial deposit payment';
        } elseif ($paymentNumber === $totalPayments) {
            $notes[] = 'Final payment';
        } else {
            $notes[] = "Payment installment {$paymentNumber} of {$totalPayments}";
        }
        
        switch ($status) {
            case PaymentStatus::PAID:
                $notes[] = 'Payment confirmed and processed successfully';
                break;
            case PaymentStatus::PENDING:
                $notes[] = 'Payment processing. Awaiting confirmation from payment gateway';
                break;
            case PaymentStatus::NOT_PAID:
                $notes[] = 'Payment not yet received. Follow up required';
                break;
        }
        
        return implode('. ', $notes) . '.';
    }
}

