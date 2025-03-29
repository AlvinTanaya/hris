<?php

namespace App\Imports;

use App\Models\User;
use App\Models\EmployeeDepartment;
use App\Models\EmployeePosition;
use App\Models\users_education;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class EmployeesImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Pastikan data yang diperlukan tersedia
        if (!isset($row['name'], $row['email'], $row['position'], $row['department'], $row['join_date'])) {
            throw new \Exception("Data penting tidak lengkap. Pastikan kolom name, email, position, department, dan join_date terisi.");
        }

        // Cek apakah email sudah ada di database
        if (User::where('email', $row['email'])->exists()) {
            throw new \Exception("Email '{$row['email']}' sudah terdaftar dalam database.");
        }

        // Cek apakah nama sudah ada di database
        if (User::where('name', $row['name'])->exists()) {
            throw new \Exception("Nama karyawan '{$row['name']}' sudah terdaftar dalam database.");
        }

        // Format YYYYMM untuk employee_id
        $yearMonth = Carbon::parse($row['join_date'])->format('Ym');

        // Cari employee terakhir di bulan yang sama
        $lastEmployee = User::where('employee_id', 'like', "{$yearMonth}%")
            ->orderBy('employee_id', 'desc')
            ->first();

        // Tentukan nomor urut baru
        $newEmployeeNumber = 1;
        if ($lastEmployee) {
            $lastNumber = intval(substr($lastEmployee->employee_id, -3));
            $newEmployeeNumber = $lastNumber + 1;
        }
        $employeeId = $yearMonth . str_pad($newEmployeeNumber, 3, '0', STR_PAD_LEFT);

        // Validasi gender
        $gender = strtolower($row['gender']);
        if (!in_array($gender, ['male', 'female'])) {
            throw new \Exception("Gender '{$row['gender']}' tidak valid. Harus Male atau Female.");
        }

        // Validasi agama
        $religion = ucfirst(strtolower($row['religion']));
        $validReligions = ['Islam', 'Kristen', 'Katolik', 'Buddha', 'Hindu', 'Konghucu'];
        if (!in_array($religion, $validReligions)) {
            throw new \Exception("Agama '{$row['religion']}' tidak valid. Pilihan yang tersedia: Islam, Kristen, Katolik, Buddha, Hindu, Konghucu.");
        }


        // Function to validate and format Indonesian phone numbers
        function validatePhoneNumber($input, $fieldName)
        {
            // Remove all non-digit characters
            $cleaned = preg_replace('/[^0-9]/', '', $input);

            // Handle case where Excel might remove leading zero
            if (strlen($cleaned) >= 9 && strlen($cleaned) <= 12) {
                if (!str_starts_with($cleaned, '0')) {
                    $cleaned = '0' . $cleaned;
                }
            }

            // List semua prefix nomor handphone Indonesia yang valid
            $validPrefixPatterns = [
                '/^081[123]\d{7,8}$/',    // 0811-0813 (Telkomsel)
                '/^082[123]\d{7,8}$/',    // 0821-0823 (Telkomsel)
                '/^085[235678]\d{7,8}$/', // 0852-0853, 0855-0858 (Indosat)
                '/^087[789]\d{7,8}$/',    // 0877-0879 (XL)
                '/^088[1-9]\d{7,8}$/',    // 0881-0889 (Smartfren)
                '/^089[5-9]\d{7,8}$/',    // 0895-0899 (Three/Hutchison)
                '/^081[456789]\d{7,8}$/', // 0814-0819 (Lainnya)
                '/^083[1238]\d{7,8}$/',   // 0831-0833, 0838 (Axis)
                '/^089[1234]\d{7,8}$/'    // 0891-0894 (Lainnya)
            ];

            $isValid = false;
            foreach ($validPrefixPatterns as $pattern) {
                if (preg_match($pattern, $cleaned)) {
                    $isValid = true;
                    break;
                }
            }

            if (!$isValid) {
                throw new \Exception("Nomor $fieldName '$input' tidak valid. Format harus: 08xx-xxxx-xxxx dengan total 10-12 digit. Contoh: 0812345678, 085678901234");
            }

            return $cleaned;
        }

        // In your model function:
        try {
            // Validasi nomor telepon
            $phoneNumber = validatePhoneNumber($row['phone_number'], 'telepon');

            // Validasi nomor kontak darurat (jika ada)
            $emergencyContact = isset($row['emergency_contact'])
                ? validatePhoneNumber($row['emergency_contact'], 'kontak darurat')
                : null;
        } catch (\Exception $e) {
            throw $e;
        }


        // Validasi employee_status
        $employeeStatus = $row['employee_status'];
        $validStatuses = ['Full Time', 'Part Time', 'Contract'];
        if (!in_array($employeeStatus, $validStatuses)) {
            throw new \Exception("Status karyawan '{$row['employee_status']}' tidak valid. Pilihan yang tersedia: Full Time, Part Time, Contract.");
        }

        // Cari department di master table
        $department = ucfirst(strtolower($row['department']));
        $departmentRecord = EmployeeDepartment::where('department', $department)->first();

        if (!$departmentRecord) {
            throw new \Exception("Department '{$department}' tidak ditemukan di Master Department.");
        }

        // Cari position di master table
        $position = ucfirst(strtolower($row['position']));
        $positionRecord = EmployeePosition::where('position', $position)->first();

        if (!$positionRecord) {
            throw new \Exception("Position '{$position}' tidak ditemukan di Master Position.");
        }

        // Validasi status pajak
        $taxStatus = isset($row['status']) ? strtoupper(trim($row['status'])) : null;
        $validTaxStatuses = ['TK/0', 'TK/1', 'TK/2', 'TK/3', 'K/1', 'K/2', 'K/3'];
        if ($taxStatus && !in_array($taxStatus, $validTaxStatuses)) {
            throw new \Exception("Status pajak '{$row['status']}' tidak valid. Pilihan yang tersedia: TK/0, TK/1, TK/2, TK/3, K/1, K/2, K/3.");
        }

        // Validasi nama bank dan nomor rekening
        $bankName = isset($row['bank_name']) ? trim($row['bank_name']) : null;
        $bankNumber = isset($row['bank_number']) ? trim($row['bank_number']) : null;

        $validBanks = [
            'Bank Central Asia (BCA)',
            'Bank Mandiri',
            'Bank Rakyat Indonesia (BRI)',
            'Bank Negara Indonesia (BNI)',
            'Bank CIMB Niaga',
            'Bank Tabungan Negara (BTN)',
            'Bank Danamon',
            'Bank Permata',
            'Bank Panin',
            'Bank OCBC NISP',
            'Bank Maybank Indonesia',
            'Bank Mega',
            'Bank Bukopin',
            'Bank Sinarmas'
        ];

        $bankNames = [];
        $bankNumbers = [];

        if ($bankName) {
            if (in_array($bankName, $validBanks)) {
                $bankNames[] = $bankName;
                if ($bankNumber) {
                    $bankNumbers[] = $bankNumber;
                }
            } else {
                throw new \Exception("Nama bank '{$bankName}' tidak valid. Pilihan bank yang tersedia: " . implode(', ', $validBanks));
            }
        }

        $bankNamesJson = json_encode($bankNames);
        $bankNumbersJson = json_encode($bankNumbers);

        // Validasi NPWP format
        $npwp = isset($row['npwp']) ? trim($row['npwp']) : null;
        if ($npwp && !preg_match('/^\d{2}\.\d{3}\.\d{3}\.\d-\d{3}\.\d{3}$/', $npwp)) {
            throw new \Exception("Format NPWP '{$row['npwp']}' tidak valid. Format yang benar: XX.XXX.XXX.X-XXX.XXX");
        }

        // Buat password default
        $password = strtolower(str_replace(' ', '', $row['name'])) . '12345';

        // Buat data user
        $user = new User([
            'employee_id'       => $employeeId,
            'name'              => $row['name'],
            'email'             => $row['email'],
            'position_id'       => $positionRecord->id,
            'department_id'      => $departmentRecord->id,
            'user_status'       => 'Unbanned',
            'ID_number'         => $row['id_number'] ?? null,
            'birth_date'        => $row['birth_date'] ?? null,
            'birth_place'       => $row['birth_place'] ?? null,
            'ID_address'        => $row['id_address'] ?? null,
            'domicile_address'  => $row['domicile_address'] ?? null,
            'religion'          => $religion,
            'gender'            => ucfirst($gender),
            'phone_number'      => $phoneNumber,
            'employee_status'   => $employeeStatus,
            'contract_start_date' => $row['contract_start_date'] ?? null,
            'contract_end_date'   => $row['contract_end_date'] ?? null,
            'join_date'         => $row['join_date'],
            'NPWP'              => $npwp,
            'bpjs_employment'   => $row['bpjs_employment'] ?? null,
            'bpjs_health'       => $row['bpjs_health'] ?? null,
            'bank_name'         => $bankNamesJson,
            'bank_number'       => $bankNumbersJson,
            'emergency_contact' => $emergencyContact,
            'status'            => $taxStatus,
            'exit_date'         => $row['exit_date'] ?? null,
            'password'          => Hash::make($password),
        ]);

        $user->save();

        // Jika ada data pendidikan
        if (isset($row['degree']) || isset($row['major'])) {
            $degree = isset($row['degree']) ? trim($row['degree']) : null;
            $validDegrees = ['SMA', 'SMK', 'S1', 'S2'];

            if ($degree) {
                if (in_array($degree, $validDegrees)) {
                    users_education::create([
                        'users_id' => $user->id,
                        'degree' => $degree,
                        'major' => $row['major'] ?? null,
                    ]);
                } else {
                    throw new \Exception("Pendidikan terakhir '{$degree}' tidak valid. Pilihan yang tersedia: SMA, SMK, S1, S2.");
                }
            }
        }

        return null;
    }
}
