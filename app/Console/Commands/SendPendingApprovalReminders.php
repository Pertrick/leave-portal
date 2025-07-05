<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NotificationService;

class SendPendingApprovalReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:send-pending-approval-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminders for pending leave approval applications';

    /**
     * Execute the console command.
     */
    public function handle(NotificationService $notificationService)
    {
        $this->info('Sending pending approval reminders...');
        
        try {
            $notificationService->sendPendingApprovalReminders();
            $this->info('Pending approval reminders sent successfully!');
        } catch (\Exception $e) {
            $this->error('Failed to send pending approval reminders: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
} 