<?php
namespace App\Imports;

use App\Models\User;
use App\Models\users_education;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class EmployeesImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Pastikan data yang diperlukan tersedia
        if (!isset($row['name'], $row['email'], $row['position'], $row['department'], $row['join_date'])) {
            return null; // Lewati jika ada data penting yang kosong
        }
        
        // Cek apakah email sudah ada di database
        if (User::where('email', $row['email'])->exists()) {
            // Jika email sudah ada, skip import untuk data ini
            return null;
        }
        
        // Cek apakah nama sudah ada di database
        if (User::where('name', $row['name'])->exists()) {
            // Jika nama sudah ada, skip import untuk data ini
            return null;
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
            $lastNumber = intval(substr($lastEmployee->employee_id, -3)); // Ambil 3 angka terakhir
            $newEmployeeNumber = $lastNumber + 1;
        }
        $employeeId = $yearMonth . str_pad($newEmployeeNumber, 3, '0', STR_PAD_LEFT);

        // Validasi gender
        $gender = strtolower($row['gender']);
        if (!in_array($gender, ['male', 'female'])) {
            return null; // Skip jika gender tidak valid
        }

        // Validasi agama
        $religion = ucfirst(strtolower($row['religion']));
        $validReligions = ['Islam', 'Kristen', 'Katolik', 'Buddha', 'Hindu', 'Konghucu'];
        if (!in_array($religion, $validReligions)) {
            return null; // Skip jika agama tidak valid
        }

        // Validasi nomor telepon
        $phoneNumber = preg_replace('/[^0-9]/', '', $row['phone_number']); // Hanya angka
        if (!preg_match('/^08[0-9]{8,10}$/', $phoneNumber)) {
            return null; // Skip jika nomor telepon tidak valid
        }
        
        // Validasi emergency contact
        $emergencyContact = isset($row['emergency_contact']) ? preg_replace('/[^0-9]/', '', $row['emergency_contact']) : null;
        if ($emergencyContact && !preg_match('/^08[0-9]{8,10}$/', $emergencyContact)) {
            return null; // Skip jika nomor kontak darurat tidak valid
        }

        // Validasi employee_status
        $employeeStatus = strtolower($row['employee_status']);
        $validStatuses = ['full time', 'part time', 'contract'];
        if (!in_array($employeeStatus, $validStatuses)) {
            return null; // Skip jika status tidak valid
        }

        // Validasi posisi dan departemen
        $position = ucfirst(strtolower($row['position']));
        $validPositions = ['Director', 'General Manager', 'Manager', 'Supervisor', 'Staff'];
        if (!in_array($position, $validPositions)) {
            return null;
        }

        // Aturan untuk department berdasarkan posisi
        $department = ucfirst(strtolower($row['department']));
        if ($position == 'Director' && $department !== 'Director') {
            return null;
        } elseif ($position == 'General Manager' && $department !== 'General') {
            return null;
        }
        
        // Validasi status pajak
        $taxStatus = isset($row['status']) ? strtoupper(trim($row['status'])) : null;
        $validTaxStatuses = ['TK/0', 'TK/1', 'TK/2', 'TK/3', 'K/1', 'K/2', 'K/3'];
        if ($taxStatus && !in_array($taxStatus, $validTaxStatuses)) {
            return null; // Skip jika status pajak tidak valid
        }
        
        // Validasi nama bank dan nomor rekening - diubah menjadi format JSON array
        $bankName = isset($row['bank_name']) ? trim($row['bank_name']) : null;
        $bankNumber = isset($row['bank_number']) ? trim($row['bank_number']) : null;
        
        $validBanks = [
            'Bank Central Asia (BCA)', 'Bank Mandiri', 'Bank Rakyat Indonesia (BRI)', 
            'Bank Negara Indonesia (BNI)', 'Bank CIMB Niaga', 'Bank Tabungan Negara (BTN)', 
            'Bank Danamon', 'Bank Permata', 'Bank Panin', 'Bank OCBC NISP', 
            'Bank Maybank Indonesia', 'Bank Mega', 'Bank Bukopin', 'Bank Sinarmas'
        ];
        
        // Inisialisasi array untuk bank
        $bankNames = [];
        $bankNumbers = [];
        
        // Jika ada data bank, tambahkan ke array
        if ($bankName) {
            // Jika bank valid, tambahkan ke array
            if (in_array($bankName, $validBanks)) {
                $bankNames[] = $bankName;
                
                // Jika ada nomor rekening, tambahkan ke array
                if ($bankNumber) {
                    $bankNumbers[] = $bankNumber;
                }
            }
        }
        
        // Encode sebagai JSON string
        $bankNamesJson = json_encode($bankNames);
        $bankNumbersJson = json_encode($bankNumbers);
        
        // Validasi NPWP format
        $npwp = isset($row['npwp']) ? trim($row['npwp']) : null;
        if ($npwp && !preg_match('/^\d{2}\.\d{3}\.\d{3}\.\d-\d{3}\.\d{3}$/', $npwp)) {
            return null; // Skip jika format NPWP tidak valid
        }

        // Buat password default (nama kecil tanpa spasi + 12345)
        $password = strtolower(str_replace(' ', '', $row['name'])) . '12345';

        // Buat data user
        $user = new User([
            'employee_id'       => $employeeId,
            'name'              => $row['name'],
            'email'             => $row['email'],
            'position'          => $position,
            'department'        => $department,
            'user_status'       => 'Unbanned',
            'ID_number'         => $row['id_number'] ?? null,
            'birth_date'        => $row['birth_date'] ?? null,
            'birth_place'       => $row['birth_place'] ?? null,
            'ID_address'        => $row['id_address'] ?? null,
            'domicile_address'  => $row['domicile_address'] ?? null,
            'religion'          => $religion,
            'gender'            => ucfirst($gender),
            'phone_number'      => $phoneNumber,
            'employee_status'   => ucfirst($employeeStatus),
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
        
        // Jika ada data pendidikan, simpan ke tabel users_education
        if (isset($row['degree']) || isset($row['major'])) {
            $degree = isset($row['degree']) ? trim($row['degree']) : null;
            $validDegrees = ['SMA', 'SMK', 'S1', 'S2'];
            
            if ($degree && in_array($degree, $validDegrees)) {
                users_education::create([
                    'users_id' => $user->id,
                    'degree' => $degree,
                    'major' => $row['major'] ?? null,
                ]);
            }
        }
        
        return null; // Karena kita sudah menyimpan data user secara manual
    }
}