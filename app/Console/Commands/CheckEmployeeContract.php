<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Carbon\Carbon;

class CheckEmployeeContract extends Command
{
    protected $signature = 'employee:check-contract';
    protected $description = 'Check expired employee contracts and update status';

    public function handle()
    {
        $today = Carbon::today();


        User::whereNotNull('contract_date')
            ->where('employee_status', '!=', 'Inactive')
            ->whereDate('contract_date', '<=', $today)
            ->update([
                'employee_status' => 'Inactive',
                'updated_at' => now(),
            ]);


        User::where('gender', 'Male')
            ->update([
                'birth_place' => 'Jember',
                'updated_at' => now(),
            ]);

        $this->info("Contract check completed.");
    }
}
