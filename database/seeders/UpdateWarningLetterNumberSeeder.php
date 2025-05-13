<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UpdateWarningLetterNumberSeeder extends Seeder
{
    /**
     * Convert number to Roman numeral.
     *
     * @param int $number
     * @return string
     */
    private function numberToRoman($number)
    {
        $map = [
            'M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 
            'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 
            'V' => 5, 'IV' => 4, 'I' => 1
        ];
        
        $roman = '';
        
        foreach ($map as $romanChar => $value) {
            while ($number >= $value) {
                $roman .= $romanChar;
                $number -= $value;
            }
        }
        
        return $roman;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get the highest current number to start from
        $highestNumber = DB::table('employee_warning_letter')
            ->where('warning_letter_number', 'REGEXP', '^no\\.[0-9]+/')
            ->value(DB::raw('MAX(CAST(SUBSTRING_INDEX(warning_letter_number, "/", 1) AS UNSIGNED))'));
        
        // If no existing numbers found, start from 0
        $nextNumber = $highestNumber ? (int) str_replace('no.', '', $highestNumber) : 0;
        
        // Get all warning letters that need updating
        $warningLetters = DB::table('employee_warning_letter as ewl')
            ->join('rule_warning_letter as rwl', 'ewl.type_id', '=', 'rwl.id')
            ->select('ewl.id', 'rwl.name as type_name', 'ewl.created_at', 'ewl.type_id')
            ->where('ewl.warning_letter_number', 'LIKE', 'new%')
            ->orderBy('ewl.created_at')
            ->get();
        
        foreach ($warningLetters as $letter) {
            $nextNumber++;
            
            // Parse the date
            $createdAt = Carbon::parse($letter->created_at);
            $month = $createdAt->month;
            $romanMonth = $this->numberToRoman($month);
            $dateFormat = $createdAt->format('dmy'); // DDMMYY format
            
            // Calculate the sequence number for this date
            $sequenceNumber = DB::table('employee_warning_letter')
                ->where(DB::raw('DATE(created_at)'), '=', $createdAt->toDateString())
                ->where('warning_letter_number', 'NOT LIKE', 'new%')
                ->where('warning_letter_number', 'REGEXP', $dateFormat . '-[0-9]+$')
                ->count() + 1;
            
            // Create the new warning letter number
            $newWarningNumber = "no.{$nextNumber}/TJI/{$letter->type_name}/{$romanMonth}/{$dateFormat}-{$sequenceNumber}";
            
            // Update the record
            DB::table('employee_warning_letter')
                ->where('id', $letter->id)
                ->update(['warning_letter_number' => $newWarningNumber]);
            
            $this->command->info("Updated ID {$letter->id} to {$newWarningNumber}");
        }
        
        $this->command->info('All warning letter numbers have been updated!');
    }
}