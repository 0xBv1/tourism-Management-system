<?php

namespace App\Console\Commands;

use App\Models\Inquiry;
use Illuminate\Console\Command;

class CheckInquiryConfirmations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inquiries:check-confirmations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the status of inquiry confirmations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Inquiry Confirmation Status Report');
        $this->info('==================================');

        // Inquiries without confirmation users
        $noUsers = Inquiry::whereNull('user1_id')
            ->orWhereNull('user2_id')
            ->get();

        if ($noUsers->isNotEmpty()) {
            $this->warn("\nInquiries without confirmation users assigned:");
            $this->table(
                ['ID', 'Subject', 'Status', 'Created At'],
                $noUsers->map(function ($inquiry) {
                    return [
                        $inquiry->id,
                        substr($inquiry->subject, 0, 30) . '...',
                        $inquiry->status->value,
                        $inquiry->created_at->format('Y-m-d H:i')
                    ];
                })
            );
        } else {
            $this->info("\n✓ All inquiries have confirmation users assigned.");
        }

        // Inquiries with users but not confirmed
        $pendingConfirmations = Inquiry::whereNotNull('user1_id')
            ->whereNotNull('user2_id')
            ->where('status', '!=', 'confirmed')
            ->get()
            ->filter(function ($inquiry) {
                return !$inquiry->isFullyConfirmed();
            });

        if ($pendingConfirmations->isNotEmpty()) {
            $this->warn("\nInquiries pending confirmation:");
            $this->table(
                ['ID', 'Subject', 'User 1', 'User 2', 'Status'],
                $pendingConfirmations->map(function ($inquiry) {
                    $confirmationStatus = $inquiry->getConfirmationStatus();
                    return [
                        $inquiry->id,
                        substr($inquiry->subject, 0, 20) . '...',
                        $confirmationStatus['user1_name'] . ($confirmationStatus['user1_confirmed'] ? ' ✓' : ' ⏳'),
                        $confirmationStatus['user2_name'] . ($confirmationStatus['user2_confirmed'] ? ' ✓' : ' ⏳'),
                        $inquiry->status->value
                    ];
                })
            );
        } else {
            $this->info("\n✓ No inquiries are pending confirmation.");
        }

        // Fully confirmed inquiries
        $confirmed = Inquiry::where('status', 'confirmed')->count();
        $this->info("\n✓ Fully confirmed inquiries: {$confirmed}");

        $this->info("\nTo assign confirmation users to inquiries without them, run:");
        $this->info("php artisan inquiries:assign-confirmation-users --auto-assign");
    }
}