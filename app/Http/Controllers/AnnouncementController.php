<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

use App\Models\User;
use App\Models\Notification;
use App\Models\EmployeeDepartment;
use App\Models\EmployeePosition;

use App\Mail\EmployeeInvitationMail;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $query = Notification::query();
    
        // Apply filters if available
        if ($request->filled('maker_id')) {
            $query->where('maker_id', $request->maker_id);
        }
    
        if ($request->filled('user_id')) {
            $query->where('users_id', $request->user_id);
        }
    
        if ($request->filled('created_at')) {
            $query->whereDate('created_at', $request->created_at);
        }
    
        // Group by to merge similar messages
        $announcements = $query
            ->with(['maker' => function($q) {
                $q->with('position', 'department');
            }])
            ->select(
                'message',
                'maker_id',
                'created_at',
                DB::raw('GROUP_CONCAT(users_id) as users_list')
            )
            ->groupBy('message', 'maker_id', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();
    
        // Get unique values for dropdowns with eager loading
        $makers = User::with('position', 'department')
            ->whereIn('id', Notification::pluck('maker_id'))
            ->get();
    
        $users = User::with('position', 'department')
            ->whereIn('id', Notification::pluck('users_id'))
            ->get();
    
        return view('announcement.index', compact('announcements', 'makers', 'users'));
    }

    public function users(Request $request)
    {
        // Ambil semua users_id yang memiliki message dan created_at yang sama
        $userIds = Notification::where('message', $request->message)
            ->where('created_at', $request->created_at)
            ->pluck('users_id');

        // Cari data users berdasarkan user_id yang ditemukan di notifikasi
        $users = User::whereIn('id', $userIds)
            ->with('position', 'department') // Eager load relationships
            ->select('id', 'employee_id', 'name', 'position_id', 'department_id')
            ->get()
            ->map(function ($user) {
                return [
                    'employee_id' => $user->employee_id,
                    'name' => $user->name,
                    'position' => $user->position ? $user->position->position : '-',
                    'department' => $user->department ? $user->department->department : '-'
                ];
            });

        return response()->json(['users' => $users]);
    }

    public function create()
    {
        $employees = User::with(['department', 'position'])
        ->where('employee_status', '!=', 'Inactive')
        ->get();

        // Mengambil daftar departemen unik dari pegawai yang aktif
        $departments = EmployeeDepartment::all();

        // Mengambil daftar posisi unik dari pegawai yang aktif
        $positions = EmployeePosition::all();

        return view('announcement.create', compact('employees', 'departments', 'positions'));
    }

    public function store(Request $request)
    {
        // Validasi request
        $request->validate([
            'message' => 'required',
            'invited_employees' => 'nullable|string',
        ]);

        // Ambil nama pembuat pengumuman
        $maker = User::with('position', 'department')->find($request->maker_id);

        if ($maker) {
            $makerName = $maker->name;
            $makerPosition = $maker->position ? $maker->position->position : '-';
            $makerDepartment = $maker->department ? $maker->department->department : '-';

            if ($makerPosition == $makerDepartment) {
                $makerName = "{$maker->name} - {$makerPosition}";
            } else {
                $makerName = "{$maker->name} - {$makerPosition} ({$makerDepartment})";
            }
        } else {
            $makerName = 'PT. Timur Jaya Indosteel';
        }

        // Jika ada karyawan yang diundang
        if ($request->invited_employees) {
            $employeeIds = explode(',', $request->invited_employees);

            // Ambil email berdasarkan ID karyawan
            $emails = User::whereIn('id', $employeeIds)->pluck('email')->toArray();

            foreach ($employeeIds as $employeeId) {
                Notification::create([
                    'message' => $request->message,
                    'type' => 'general',
                    'maker_id' => $request->maker_id,
                    'users_id' => $employeeId,
                    'status' => 'Unread',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $user = User::with('position', 'department')->find($request->maker_id);

            if ($user) {
                $userName = $user->name;
                $userPosition = $user->position ? $user->position->position : '-';
                $userDepartment = $user->department ? $user->department->department : '-';

                if ($userPosition == $userDepartment) {
                    $userName = "{$user->name} - {$userPosition}";
                } else {
                    $userName = "{$user->name} - {$userPosition} ({$userDepartment})";
                }
            } else {
                $userName = 'Employee';
            }

            // Kirim email dengan nama pembuat pengumuman
            Mail::to($emails)->send(new EmployeeInvitationMail($request->message, $makerName, $userName));
        }

        return redirect()->route('announcement.index')->with('success', 'Announcement added successfully!');
    }
}