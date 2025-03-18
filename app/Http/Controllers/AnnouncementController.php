<?php

namespace App\Http\Controllers;



use Carbon\Carbon;
use Illuminate\Bus\UpdatedBatchJobCounts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;

use App\Imports\EmployeesImport;
use App\Models\User;
use App\Models\notification;

use App\Mail\EmployeeInvitationMail;


use function Laravel\Prompts\note;

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
        $announcements = $query->select(
            'message',
            'maker_id',
            'created_at',
            DB::raw('GROUP_CONCAT(users_id) as users_list') // Gabungkan users_id dalam satu kolom
        )
            ->groupBy('message', 'maker_id', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get unique values for dropdowns
        $makers = User::whereIn('id', Notification::pluck('maker_id'))->get();
        $users = User::whereIn('id', Notification::pluck('users_id'))->get();

        return view('announcement.index', compact('announcements', 'makers', 'users'));
    }





    public function users(Request $request)
    {
        // Ambil semua users_id yang memiliki message dan created_at yang sama
        $userIds = notification::where('message', $request->message)
            ->where('created_at', $request->created_at)
            ->pluck('users_id'); // Ambil ID user aja

        // Cari data users berdasarkan user_id yang ditemukan di notifikasi
        $users = User::whereIn('id', $userIds)
            ->select('employee_id', 'name', 'position', 'department')
            ->get();

        // dd($users);

        return response()->json(['users' => $users]);
    }




    public function create()
    {

        $employees = User::where('employee_status', '!=', 'Inactive')->get();

        // Mengambil daftar departemen unik dari pegawai yang aktif
        $departments = User::where('employee_status', '!=', 'Inactive')
            ->whereNotNull('department')
            ->distinct()
            ->pluck('department');

        // Mengambil daftar posisi unik dari pegawai yang aktif
        $positions = User::where('employee_status', '!=', 'Inactive')
            ->whereNotNull('position')
            ->distinct()
            ->pluck('position');

        return view('announcement.create', compact('employees', 'departments', 'positions'));
    }




    public function store(Request $request)
    {

        //dd($request->all());
        // Validasi request
        $request->validate([
            'message' => 'required',
            'invited_employees' => 'nullable|string',
        ]);

        // Ambil nama pembuat pengumuman
        $maker = User::find($request->maker_id);

        if ($maker) {
            if ($maker->position == $maker->department) {
                $makerName = "{$maker->name} - {$maker->position}";
            } else {
                $makerName = "{$maker->name} - {$maker->position} ({$maker->department})";
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

            $user = User::find($request->maker_id);

            if ($user) {
                if ($user->position == $user->department) {
                    $userName = "{$user->name} - {$user->position}";
                } else {
                    $userName = "{$user->name} - {$user->position} ({$user->department})";
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
