<?php

namespace App\Console\Commands;

use App\Mail\ContractExpired;
use App\Mail\ContractExpiringNotification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class ContractCheckerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contract:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check contract end dates and send appropriate notifications';
    
    /**
     * Get all active HR staff emails
     * 
     * @return array
     */
    private function getHREmails(): array
    {
        // Find all active HR staff
        $hrStaff = User::where('employee_status', '!=', 'Inactive')
            ->whereHas('department', function($query) {
                $query->where('department', 'like', '%Human Resources%');
            })
            ->get();
            
        $this->info("Found {$hrStaff->count()} active HR staff members.");
        
        // Extract emails
        $emails = [];
        foreach ($hrStaff as $staff) {
            if (!empty($staff->email)) {
                $emails[] = $staff->email;
                $this->info("Added HR staff email: {$staff->email}");
            }
        }
        
        return $emails;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->checkExpiredContracts();
        $this->checkExpiringContracts();
        
        $this->info('Contract check completed successfully.');
        
        return Command::SUCCESS;
    }

    /**
     * Check and notify about expired contracts.
     */
    private function checkExpiredContracts()
    {
        // Find users with expired contracts (contract_end_date < today and not null)
        // Also check that they are not inactive employees
        $expiredUsers = User::whereNotNull('contract_end_date')
            ->where('contract_end_date', '<', Carbon::today())
            ->where('employee_status', '!=', 'Inactive')
            ->get();
        
        $this->info("Found {$expiredUsers->count()} expired contracts.");
        
        // Get all active HR staff emails
        $hrEmails = $this->getHREmails();
        
        if (empty($hrEmails)) {
            $this->error("No active HR staff found to send notifications to!");
            return;
        }
        
        // Send notifications for expired contracts
        foreach ($expiredUsers as $user) {
            $this->info("Sending expired contract notification for {$user->name}");
            Mail::to($hrEmails)->send(new ContractExpired($user));
        }
    }

    /**
     * Check and notify about contracts that will expire soon.
     */
    private function checkExpiringContracts()
    {
        $today = Carbon::today();
        $tomorrow = $today->copy()->addDay();
        $oneWeek = $today->copy()->addWeek();
        $oneMonth = $today->copy()->addMonth();
        
        // 1 month notification
        $oneMonthUsers = User::whereNotNull('contract_end_date')
            ->where('contract_end_date', '=', $oneMonth->toDateString())
            ->where('employee_status', '!=', 'Inactive')
            ->get();
            
        // 1 week notification
        $oneWeekUsers = User::whereNotNull('contract_end_date')
            ->where('contract_end_date', '=', $oneWeek->toDateString())
            ->where('employee_status', '!=', 'Inactive')
            ->get();
            
        // 1 day notification
        $oneDayUsers = User::whereNotNull('contract_end_date')
            ->where('contract_end_date', '=', $tomorrow->toDateString())
            ->where('employee_status', '!=', 'Inactive')
            ->get();
        
        $this->info("Found {$oneMonthUsers->count()} contracts expiring in one month.");
        $this->info("Found {$oneWeekUsers->count()} contracts expiring in one week.");
        $this->info("Found {$oneDayUsers->count()} contracts expiring tomorrow.");
        
        // Get all active HR staff emails
        $hrEmails = $this->getHREmails();
        
        if (empty($hrEmails)) {
            $this->error("No active HR staff found to send notifications to!");
            return;
        }
        
        // Send notifications
        foreach ($oneMonthUsers as $user) {
            $this->info("Sending one month notification for {$user->name}");
            Mail::to($hrEmails)->send(new ContractExpiringNotification($user, '1 month'));
        }
        
        foreach ($oneWeekUsers as $user) {
            $this->info("Sending one week notification for {$user->name}");
            Mail::to($hrEmails)->send(new ContractExpiringNotification($user, '1 week'));
        }
        
        foreach ($oneDayUsers as $user) {
            $this->info("Sending one day notification for {$user->name}");
            Mail::to($hrEmails)->send(new ContractExpiringNotification($user, '1 day'));
        }
    }
}