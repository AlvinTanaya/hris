<?php

namespace App\Imports;

use App\Models\User;
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

        // Buat password default (nama kecil tanpa spasi + 12345)
        $password = strtolower(str_replace(' ', '', $row['name'])) . '12345';

        dd($row['id_number']);
        return new User([
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
            'password'          => Hash::make($password),
        ]);
    }
}
