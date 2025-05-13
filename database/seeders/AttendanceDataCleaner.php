<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmployeeAbsent;
use App\Models\CustomHoliday;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class AttendanceDataCleaner extends Seeder
{
    protected $nationalHolidays = [];
    protected $customHolidays = [];
    protected $years = [2023, 2024, 2025]; // Years to clean
    protected $deleted = 0;
    
    /**
     * Run the database cleaner.
     */
    public function run(): void
    {
        $this->command->info('Starting attendance data cleaning...');
        
        // Fetch all relevant holidays
        $this->fetchNationalHolidays();
        $this->fetchCustomHolidays();
        
        // Print holiday info
        $this->command->info('Found ' . count($this->nationalHolidays) . ' national holidays');
        $this->command->info('Found ' . count($this->customHolidays) . ' custom holidays');
        
        // Clean data for Sundays
        $this->cleanSundayAttendance();
        
        // Clean data for national holidays
        $this->cleanNationalHolidayAttendance();
        
        // Clean data for custom holidays
        $this->cleanCustomHolidayAttendance();
        
        $this->command->info('Attendance data cleaning completed. Total records deleted: ' . $this->deleted);
    }
    
    /**
     * Fetch national holidays from API for specified years and months
     */
    private function fetchNationalHolidays(): void
    {
        $this->command->info('Fetching national holidays from API...');
        
        $hasErrors = false;
        $apiErrors = [];
        
        foreach ($this->years as $year) {
            for ($month = 1; $month <= 12; $month++) {
                try {
                    $this->command->line("Fetching holidays for {$year}-{$month}...");
                    
                    $response = Http::get("https://api-harilibur.vercel.app/api", [
                        'month' => $month,
                        'year' => $year
                    ]);
                    
                    if ($response->successful()) {
                        $holidays = $response->json();
                        
                        foreach ($holidays as $holiday) {
                            if (isset($holiday['holiday_date']) && isset($holiday['is_national_holiday']) && $holiday['is_national_holiday']) {
                                $this->nationalHolidays[] = $holiday['holiday_date'];
                                $this->command->line("Added national holiday: {$holiday['holiday_date']} - {$holiday['holiday_name']}");
                            }
                        }
                    } else {
                        $hasErrors = true;
                        $errorMessage = "Failed to fetch holidays for {$year}-{$month}: " . $response->status();
                        $this->command->error($errorMessage);
                        $apiErrors[] = $errorMessage;
                    }
                } catch (\Exception $e) {
                    $hasErrors = true;
                    $errorMessage = "Error fetching holidays for {$year}-{$month}: " . $e->getMessage();
                    $this->command->error($errorMessage);
                    $apiErrors[] = $errorMessage;
                }
                
                // Sleep to avoid rate limiting
                usleep(500000); // 0.5 seconds
            }
        }
        
        // If there are API errors, ask the user what to do
        if ($hasErrors) {
            $this->command->error('Some errors occurred while fetching holidays from the API.');
            
            // List all errors
            $this->command->line('API Errors:');
            foreach ($apiErrors as $index => $error) {
                $this->command->line("  " . ($index + 1) . ". $error");
            }
            
            // Check if we have at least some holidays
            if (count($this->nationalHolidays) > 0) {
                $this->command->warn('Partially successful: Found ' . count($this->nationalHolidays) . ' holidays from API.');
                
                if (!$this->command->confirm('Continue with the partially fetched holidays?', true)) {
                    $this->command->error('Process aborted by user.');
                    exit(1);
                }
            } else {
                $this->command->error('Failed to fetch any holidays from API.');
                
                if ($this->command->confirm('Retry API request?', true)) {
                    $this->command->info('Retrying API requests...');
                    $this->nationalHolidays = [];
                    $this->fetchNationalHolidays();
                    return;
                } else {
                    $this->command->error('Process aborted by user.');
                    exit(1);
                }
            }
        }
        
        $this->command->info('Successfully fetched ' . count($this->nationalHolidays) . ' national holidays.');
    }
    
    /**
     * Fetch custom holidays from database
     */
    private function fetchCustomHolidays(): void
    {
        $this->customHolidays = CustomHoliday::pluck('date')->toArray();
    }
    
    /**
     * Clean attendance data for Sundays
     */
    private function cleanSundayAttendance(): void
    {
        $this->command->info('Cleaning attendance data for Sundays...');
        
        // Get all attendance records
        $sundayDates = EmployeeAbsent::select(DB::raw('DISTINCT date'))
            ->whereRaw('DAYOFWEEK(date) = 1') // Sunday is 1 in MySQL's DAYOFWEEK
            ->get()
            ->pluck('date')
            ->toArray();
        
        if (empty($sundayDates)) {
            $this->command->info('No Sunday attendance records found.');
            return;
        }
        
        $this->command->info('Found ' . count($sundayDates) . ' Sundays with attendance records');
        
        // Create progress bar
        $progressBar = $this->command->getOutput()->createProgressBar(count($sundayDates));
        $progressBar->start();
        
        foreach ($sundayDates as $date) {
            $count = EmployeeAbsent::where('date', $date)->delete();
            $this->deleted += $count;
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->command->info("\nDeleted attendance records for Sundays: " . $this->deleted);
    }
    
    /**
     * Clean attendance data for national holidays
     */
    private function cleanNationalHolidayAttendance(): void
    {
        $this->command->info('Cleaning attendance data for national holidays...');
        
        $totalBeforeNational = $this->deleted;
        
        if (empty($this->nationalHolidays)) {
            $this->command->warn('No national holidays found. Cannot clean attendance records for national holidays.');
            return;
        }
        
        // Create progress bar
        $progressBar = $this->command->getOutput()->createProgressBar(count($this->nationalHolidays));
        $progressBar->start();
        
        foreach ($this->nationalHolidays as $date) {
            $count = EmployeeAbsent::where('date', $date)->delete();
            $this->deleted += $count;
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->command->info("\nDeleted attendance records for national holidays: " . ($this->deleted - $totalBeforeNational));
    }
    
    /**
     * Clean attendance data for custom holidays
     */
    private function cleanCustomHolidayAttendance(): void
    {
        $this->command->info('Cleaning attendance data for custom holidays...');
        
        $totalBeforeCustom = $this->deleted;
        
        if (empty($this->customHolidays)) {
            $this->command->info('No custom holidays found.');
            return;
        }
        
        // Create progress bar
        $progressBar = $this->command->getOutput()->createProgressBar(count($this->customHolidays));
        $progressBar->start();
        
        foreach ($this->customHolidays as $date) {
            $count = EmployeeAbsent::where('date', $date)->delete();
            $this->deleted += $count;
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->command->info("\nDeleted attendance records for custom holidays: " . ($this->deleted - $totalBeforeCustom));
    }
}