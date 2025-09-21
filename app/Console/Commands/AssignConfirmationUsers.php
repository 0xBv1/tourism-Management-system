<?php

namespace App\Console\Commands;

use App\Models\Inquiry;
use App\Models\User;
use Illuminate\Console\Command;

class AssignConfirmationUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inquiries:assign-confirmation-users 
                            {--inquiry-id= : Specific inquiry ID to assign users to}
                            {--user1-id= : User 1 ID for confirmation}
                            {--user2-id= : User 2 ID for confirmation}
                            {--auto-assign : Auto-assign users to inquiries without confirmation users}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign confirmation users to inquiries';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('auto-assign')) {
            $this->autoAssignUsers();
            return;
        }

        $inquiryId = $this->option('inquiry-id');
        $user1Id = $this->option('user1-id');
        $user2Id = $this->option('user2-id');

        if (!$inquiryId || !$user1Id || !$user2Id) {
            $this->error('Please provide inquiry-id, user1-id, and user2-id options.');
            return;
        }

        $inquiry = Inquiry::find($inquiryId);
        if (!$inquiry) {
            $this->error("Inquiry with ID {$inquiryId} not found.");
            return;
        }

        $user1 = User::find($user1Id);
        $user2 = User::find($user2Id);

        if (!$user1 || !$user2) {
            $this->error('One or both users not found.');
            return;
        }

        if ($user1Id === $user2Id) {
            $this->error('User 1 and User 2 cannot be the same.');
            return;
        }

        $inquiry->update([
            'user1_id' => $user1Id,
            'user2_id' => $user2Id,
            'user1_confirmed_at' => null,
            'user2_confirmed_at' => null,
        ]);

        $this->info("Successfully assigned confirmation users to inquiry #{$inquiryId}:");
        $this->info("- User 1: {$user1->name} (ID: {$user1Id})");
        $this->info("- User 2: {$user2->name} (ID: {$user2Id})");
    }

    private function autoAssignUsers()
    {
        $inquiries = Inquiry::whereNull('user1_id')
            ->orWhereNull('user2_id')
            ->get();

        if ($inquiries->isEmpty()) {
            $this->info('No inquiries found that need confirmation users assigned.');
            return;
        }

        $users = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['Sales', 'Reservation', 'Operation', 'Admin', 'Administrator']);
        })->get();

        if ($users->count() < 2) {
            $this->error('Not enough users found to assign. Need at least 2 users with appropriate roles.');
            return;
        }

        $this->info("Found {$inquiries->count()} inquiries that need confirmation users assigned.");
        $this->info("Available users: " . $users->pluck('name')->join(', '));

        if (!$this->confirm('Do you want to proceed with auto-assignment?')) {
            $this->info('Operation cancelled.');
            return;
        }

        $user1 = $users->first();
        $user2 = $users->skip(1)->first();

        $assigned = 0;
        foreach ($inquiries as $inquiry) {
            $inquiry->update([
                'user1_id' => $user1->id,
                'user2_id' => $user2->id,
                'user1_confirmed_at' => null,
                'user2_confirmed_at' => null,
            ]);
            $assigned++;
        }

        $this->info("Successfully assigned confirmation users to {$assigned} inquiries:");
        $this->info("- User 1: {$user1->name} (ID: {$user1->id})");
        $this->info("- User 2: {$user2->name} (ID: {$user2->id})");
    }
}