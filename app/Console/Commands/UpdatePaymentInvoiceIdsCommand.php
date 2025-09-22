<?php

namespace App\Console\Commands;

use App\Models\Payment;
use Illuminate\Console\Command;

class UpdatePaymentInvoiceIdsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:update-invoice-ids';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update existing payments with generated invoice IDs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting payment invoice ID update...');

        $payments = Payment::whereNull('invoice_id')->get();
        $updated = 0;

        foreach ($payments as $payment) {
            $invoiceId = $this->generateInvoiceId($payment);
            $payment->update(['invoice_id' => $invoiceId]);
            $this->line("Updated Payment ID {$payment->id} with Invoice ID: {$invoiceId}");
            $updated++;
        }

        $this->info("Invoice ID update completed!");
        $this->info("Updated: {$updated} payments");
    }

    /**
     * Generate a unique invoice ID for a payment
     */
    private function generateInvoiceId(Payment $payment): string
    {
        $prefix = 'INV';
        $date = $payment->created_at->format('Ymd');
        $paymentId = str_pad($payment->id, 4, '0', STR_PAD_LEFT);
        $random = str_pad(mt_rand(1, 99), 2, '0', STR_PAD_LEFT);
        
        return $prefix . '-' . $date . '-' . $paymentId . $random;
    }
}
