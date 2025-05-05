<?php



namespace App\Http\Controllers;

use App\Imports\EmployeesImport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Illuminate\Bus\UpdatedBatchJobCounts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

use App\Models\User;
use App\Models\history_transfer_employee;
use App\Models\history_extend_employee;
use App\Models\users_education;
use App\Models\users_family;
use App\Models\users_work_experience;
use App\Models\users_language;
use App\Models\users_training;
use App\Models\users_organization;
use App\Models\Notification;
use App\Models\EmployeeDepartment;
use App\Models\EmployeePosition;


use App\Mail\TransferNotification;
use App\Mail\UpdateNotification;
use App\Mail\DepartmentUpdateNotification;
use App\Mail\NewEmployeeNotification;
use App\Mail\WelcomeNewEmployee;
use App\Mail\ContractExtensionNotification;


class UserController extends Controller
{
    // Display a listing of pegawai
    public function employees_index(Request $request)
    {
        $query = User::with(['position', 'department']); // Eager load relationships

        // Apply filters
        if ($request->filled('status')) {
            $query->where('employee_status', $request->status);
        }

        if ($request->filled('position_id')) {
            $query->where('position_id', $request->position_id);
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('status_app')) {
            $query->where('user_status', $request->status_app);
        }

        // Get the filtered results
        $users = $query->get();

        // Get options for dropdowns from related tables
        $departments = EmployeeDepartment::orderBy('department')->get();
        $positions = EmployeePosition::orderBy('ranking')->get();
        $status = User::distinct()->pluck('employee_status');
        $status_app = User::distinct()->pluck('user_status');

        return view('user.employees.index', compact('users', 'departments', 'positions', 'status', 'status_app'));
    }


    // Show the form for creating a new pegawai
    public function employees_create()
    {
        $positions = EmployeePosition::orderBy('ranking')->get();
        $departments = EmployeeDepartment::orderBy('department')->get();

        return view('user/employees/create', compact('positions', 'departments'));
    }



    // Store a newly created pegawai in the database
    public function employees_store(Request $request)
    {

        // Validate the form data
        $request->validate([
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'id_card' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'cv' => 'nullable|mimes:pdf',
            'achievement' => 'nullable|mimes:pdf',
            'name' => 'required',
            'position_id' => 'required|exists:employee_positions,id',
            'department_id' => 'required|exists:employee_departments,id',
            'join_date' => 'required',
            'email' => 'required',
            'phone_number' => 'required',
            'emergency_contact' => 'required',
            'status' => 'required',
            'employee_status' => 'required',
            'user_status' => 'required',
            'ID_number' => 'required',
            'birth_date' => 'required',
            'birth_place' => 'required',
            'ID_address' => 'required',
            'domicile_address' => 'required',
            'distance' => 'required',
            'religion' => 'required',
            'gender' => 'required',
            'contract_start_date' => 'required_if:employee_status,Contract,Part Time|nullable|date',
            'contract_end_date' => 'required_if:employee_status,Contract,Part Time|nullable|date',
        ]);


        $password = Hash::make(str_replace(' ', '', strtolower($request->name)) . '12345');
        $temp = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->storeAs(
                'user/photos_profile',
                $request->employee_id . '.' . $request->file('photo')->getClientOriginalExtension(),
                'public'
            );
            $temp = $photoPath;
        }

        $idCardPath = null;
        if ($request->hasFile('id_card')) {
            $idCardPath2 = $request->file('id_card')->storeAs(
                'user/ID_card',
                'ID_card_' . $request->employee_id . '.' . $request->file('id_card')->getClientOriginalExtension(),
                'public'
            );
            $idCardPath = $idCardPath2;
        }

        $cvPath = null;
        if ($request->hasFile('cv')) {
            $cvPath2 = $request->file('cv')->storeAs(
                'user/cv_user',
                'cv_' . $request->employee_id . '.' . $request->file('cv')->getClientOriginalExtension(),
                'public'
            );
            $cvPath = $cvPath2;
        }


        $achievementPath = null;
        if ($request->hasFile('achievement')) {
            $achievementPath2 = $request->file('achievement')->storeAs(
                'user/achievement_user',
                'achievement_' . $request->employee_id . '.' . $request->file('achievement')->getClientOriginalExtension(),
                'public'
            );
            $achievementPath = $achievementPath2;
        }

        $license = null;
        $license_number = null;
        if (!$request->has('no_license') || $request->has('sim')) {
            // Lakukan sesuatu
            $license = $request->has('sim') ? implode(',', $request->sim) : null;

            // Ambil nomor SIM sesuai SIM yang dipilih
            $selectedSimNumbers = [];
            if ($request->has('sim')) {
                foreach ($request->sim as $simType) {
                    if (!empty($request->sim_number[$simType])) {
                        $selectedSimNumbers[$simType] = $request->sim_number[$simType];
                    }
                }
            }

            // Simpan nomor SIM dengan format JSON
            $license_number = !empty($selectedSimNumbers) ? json_encode($selectedSimNumbers) : null;
        } else {
            $license = null;
            $license_number = null;
        }

        // Ambil join_date dari request
        $joinDate = $request->join_date ? Carbon::parse($request->join_date) : now();
        $yearMonth = $joinDate->format('Ym'); // Format YYYYMM

        // Cari employee terakhir di bulan tersebut
        $lastEmployee = User::where('employee_id', 'like', "{$yearMonth}%")
            ->orderBy('employee_id', 'desc')
            ->first();

        // Tentukan nomor urut
        $newEmployeeNumber = 1;
        if ($lastEmployee) {
            $lastNumber = intval(substr($lastEmployee->employee_id, -3)); // Ambil 3 angka terakhir
            $newEmployeeNumber = $lastNumber + 1;
        }
        $employeeId = $yearMonth . str_pad($newEmployeeNumber, 3, '0', STR_PAD_LEFT);

        //Bank
        $bankNames = [];
        $bankNumbers = [];

        if ($request->has('bank_name') && $request->has('bank_number')) {
            foreach ($request->bank_name as $index => $bankName) {
                if (!empty($bankName) && !empty($request->bank_number[$index])) {
                    $bankNames[] = $bankName;
                    $bankNumbers[] = $request->bank_number[$index];
                }
            }
        }
        // dd($bankNames,    $bankNumbers);

        // Create a new Pegawai record
        $user = User::create([
            'employee_id' => $employeeId,
            'sim' => $license ?? null,
            'sim_number' => $license_number ?? null,
            'photo_profile_path' => $temp ?? null,
            'id_card_path' => $idCardPath ?? null,
            'cv_path' => $cvPath ?? null,
            'achievement_path' => $achievementPath ?? null,
            'name' => $request->name,
            'position_id' => $request->position_id,
            'department_id' => $request->department_id,
            'join_date' => $joinDate,
            'contract_start_date' => $request->contract_start_date ?? null,
            'contract_end_date' => $request->contract_end_date ?? null,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'employee_status' => $request->employee_status,
            'user_status' => $request->user_status,
            'status' => $request->status,
            'NPWP' => $request->npwp,
            'emergency_contact' => $request->emergency_contact,
            'distance' => $request->distance,
            'bank_name' => !empty($bankNames) ? json_encode($bankNames) : null,
            'bank_number' => !empty($bankNumbers) ? json_encode($bankNumbers) : null,
            'bpjs_employment' => $request->bpjs_employment ?? null,
            'bpjs_health' => $request->bpjs_health ?? null,
            'ID_number' => $request->ID_number,
            'birth_date' => $request->birth_date,
            'birth_place' => $request->birth_place,
            'ID_address' => $request->ID_address,
            'domicile_address' => $request->domicile_address,
            'religion' => $request->religion,
            'gender' => $request->gender,
            'height' => $request->height ?? null,
            'weight' => $request->weight ?? null,
            'blood_type' => $request->blood_type ?? null, // Simpan blood_type
            'created_at' => now(),
            'updated_at' => now(),
            'password' => $password,

        ]);




        // data keluarga
        if ($request->has('name_family')) {
            foreach ($request->name_family as $index => $name) {
                if ($request->name_family[$index] !== null) {
                    users_family::create([
                        'users_id' => $user->id, // Ambil ID pegawai yang baru disimpan
                        'name' => $request->name_family[$index] ?? null,
                        'relation' => $request->relation[$index] ?? null,
                        'birth_date' => $request->birth_date_family[$index] ?? null,
                        'birth_place' => $request->birth_place_family[$index] ?? null,
                        'ID_number' => $request->ID_number_family[$index] ?? null,
                        'phone_number' => $request->phone_number_family[$index] ?? null,
                        'address' => $request->address_family[$index] ?? null,
                        'gender' => $request->gender_family[$index] ?? null,
                        'job' => $request->job[$index] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        if ($request->has('education_level')) {
            foreach ($request->education_level as $index => $name) {
                if ($request->education_level[$index] !== null) {
                    $newEducation = users_education::create([
                        'users_id' => $user->id,
                        'degree' => $request->education_level[$index] ?? null,
                        'educational_place' => $request->education_place[$index] ?? null,
                        'educational_city' => $request->education_city[$index] ?? null,
                        'educational_province' => $request->education_province[$index] ?? null,
                        'start_education'  => $request->start_education[$index] ?? null,
                        'end_education'  => $request->end_education[$index] ?? null,
                        'major'  => $request->educational_major[$index] ?? null,
                        'grade'  => $request->grade[$index] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }


                if ($request->hasFile("education_transcript.$index")) {
                    $file = $request->file("education_transcript.$index");
                    $extension = $file->getClientOriginalExtension();

                    // Buat nama file yang sesuai
                    $fileName = "transcript_" . "{$user->id}_{$request->education_level[$index]}_{$newEducation->id}.{$extension}";

                    // Simpan file ke public/user/transcript_user
                    $filePath = $file->storeAs('user/transcript_user', $fileName, 'public');

                    // Update path file di database
                    $newEducation->update(['transcript_file_path' => "user/transcript_user/{$fileName}"]);
                }
            }
        }

        if ($request->has('company_name')) {
            foreach ($request->company_name as $index => $name) {
                if ($request->company_name[$index] !== null) {
                    users_work_experience::create([
                        'users_id' => $user->id,
                        'company_name' => $request->company_name[$index],
                        'position' => $request->position_work[$index],
                        'start_working' => $request->start_work[$index],
                        'end_working' => $request->end_work[$index],

                        'company_address' => $request->company_address[$index] ?? null,
                        'company_phone' => $request->company_phone[$index] ?? null,
                        'salary' => $request->previous_salary[$index] ?? null,
                        'supervisor_name' => $request->supervisor_name[$index] ?? null,
                        'supervisor_phone' => $request->supervisor_phone[$index] ?? null,
                        'job_desc' => str_replace(["\r", "\n", "-", "  "], ["", ";", "", " "], trim($request->job_desc[$index] ?? null)),
                        'reason' => str_replace(["\r", "\n", "-", "  "], ["", ";", "", " "], trim($request->reason[$index] ?? null)),
                        'benefit' => str_replace(["\r", "\n", "-", "  "], ["", ";", "", " "], trim($request->benefit[$index] ?? null)),
                        'facility' => str_replace(["\r", "\n", "-", "  "], ["", ";", "", " "], trim($request->facility[$index] ?? null)),


                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }


        if ($request->has('training_name')) {
            foreach ($request->training_name as $index => $training_name) {
                if (!empty($training_name)) {
                    users_training::create([
                        'users_id' => $user->id,
                        'training_name' => $training_name,
                        'training_city' => $request->training_city[$index] ?? null,
                        'training_province' => $request->training_province[$index] ?? null,
                        'start_date' => $request->training_start_date[$index] ?? null,
                        'end_date' => $request->training_end_date[$index] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        if ($request->has('organization_name')) {
            foreach ($request->organization_name as $index => $organization_name) {
                if (!empty($organization_name)) {
                    users_organization::create([
                        'users_id' => $user->id,
                        'organization_name' => $organization_name,
                        'position' => $request->organization_position[$index] ?? null,
                        'city' => $request->organization_city[$index] ?? null,
                        'province' => $request->organization_province[$index] ?? null,
                        'activity_type' => str_replace(["\r", "\n", "-", "  "], ["", ";", "", " "], trim($request->activity_type[$index] ?? null)),
                        'start_date' => $request->organization_start_date[$index] ?? null,
                        'end_date' => $request->organization_end_date[$index] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        if ($request->has('language')) {
            foreach ($request->language as $index => $language) {
                if (!empty($language)) {
                    if ($language === 'Other') {
                        $otherLanguage = $request->other_language[$index] ?? null;
                        if (empty($otherLanguage)) {
                            continue;
                        }
                        $language = $otherLanguage;
                    }



                    users_language::create([
                        'users_id' => $user->id,
                        'language' => $language,
                        'verbal' => $request->verbal[$index] ?? null,
                        'written' => $request->written[$index] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }




        // Send email to all existing users except the new one
        $allUsers = User::where('id', '!=', $user->id)->get();
        foreach ($allUsers as $recipient) {
            Mail::to($recipient->email)->send(new NewEmployeeNotification($user));

            Notification::create([
                'users_id' => $recipient->id,  // Fixed variable name
                'message' => "A new employee {$user->name} has joined",  // Added meaningful message
                'type' => 'new_employee',
                'maker_id' => Auth::user()->id,
                'status' => 'Unread'
            ]);
        }

        // Send welcome email to the new employee
        Mail::to($user->email)->send(new WelcomeNewEmployee($user));

        Notification::create([
            'users_id' => $user->id,
            'message' => "Welcome to our team!",
            'type' => 'welcome',
            'maker_id' => Auth::user()->id,
            'status' => 'Unread'
        ]);


        // Redirect ke halaman index atau halaman lain dengan pesan sukses
        return response()->json([
            'message' => 'Employee Added successfully',
            'redirect' => route('user.employees.index')
        ]);
    }



    public function employees_import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv',
        ]);

        Excel::import(new EmployeesImport, $request->file('file'));

        return redirect()->back()->with('success', 'Employees imported successfully.');
    }

    public function employees_edit($id)
    {
        // Find the User with position and department relationships
        $user = User::with(['position', 'department'])->findOrFail($id);


        // Get all positions and departments for dropdowns
        $positions = EmployeePosition::orderBy('ranking')->get();
        $departments = EmployeeDepartment::orderBy('department')->get();

        // Your existing code for other data...
        $bankNames = json_decode($user->bank_name, true) ?: [];
        $bankNumbers = json_decode($user->bank_number, true) ?: [];

        $bankData = [];
        foreach ($bankNames as $index => $name) {
            if (isset($bankNumbers[$index])) {
                $bankData[] = [
                    'name' => $name,
                    'number' => $bankNumbers[$index]
                ];
            }
        }

        // Retrieve associated records
        $userEducation = users_education::where('users_id', $id)->get();
        $userWork = users_work_experience::where('users_id', $id)->get();
        $userFamily = users_family::where('users_id', $id)->get();
        $userTraining = users_training::where('users_id', $id)->get();
        $userLanguage = users_language::where('users_id', $id)->get();
        $userOrganization = users_organization::where('users_id', $id)->get();

        // Your duty query remains the same
        $duty = DB::table('elearning_invitation')
            ->join('elearning_lesson', 'elearning_invitation.lesson_id', '=', 'elearning_lesson.id')
            ->join('elearning_schedule', 'elearning_invitation.schedule_id', '=', 'elearning_schedule.id')
            ->where('elearning_invitation.users_id', $id)
            ->select(
                'elearning_invitation.id as invitation_id',
                'elearning_invitation.lesson_id',
                'elearning_invitation.schedule_id',
                'elearning_invitation.users_id',
                'elearning_invitation.grade',
                'elearning_lesson.name',
                'elearning_lesson.duration',
                'elearning_lesson.lesson_file',
                'elearning_schedule.start_date',
                'elearning_schedule.end_date',
            )
            ->get();

        return view('user/employees/update', compact(
            'user',
            'positions',
            'departments',
            'userEducation',
            'userWork',
            'userFamily',
            'userOrganization',
            'userLanguage',
            'userTraining',
            'duty',
            'bankData'
        ));
    }

    public function employees_update(Request $request, $id)
    {
        // dd($request->department_id);
        // Validasi input

        $request->validate([
            'password' => 'nullable|min:8',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'id_card' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'cv' => 'nullable|mimes:pdf',
            'achievement' => 'nullable|mimes:pdf',
            'employee_id' => 'required',
            'name' => 'required',
            'user_status' => 'required',
            'ID_number' => 'required',
            'birth_date' => 'required|date',
            'birth_place' => 'required',
            'ID_address' => 'required',
            'domicile_address' => 'required',
            'religion' => 'required',
            'gender' => 'required',
            'phone_number' => 'required',
            'status' => 'required',
            'emergency_contact' => 'required',
            'employee_status' => 'required',
            'email' => 'required|email',
            'join_date' => 'required',
            'distance' => 'required',
            'contract_start_date' => 'required_if:employee_status,Contract,Part Time|nullable|date',
            'contract_end_date' => 'required_if:employee_status,Contract,Part Time|nullable|date',
            'position_id' => 'required|exists:employee_positions,id',
            'department_id' => 'required|exists:employee_departments,id'

        ]);


        // dd('masuk validate');

        // Cari data pegawai berdasarkan ID
        $user = User::findOrFail($id);

        // Update password only if a new one is provided
        if (!empty($request->password)) {
            $newpassword = bcrypt($request->password);
        } else {
            $newpassword = $user->password;
        }

        // dd($newpassword);

        if ($request->hasFile('photo')) {
            // Delete old photo if exists

            if ($user->photo_profile_path) {
                Storage::disk('public')->delete('user/photos_profile/' . $user->photo_profile_path);
            }

            // Store new photo with custom filename
            $photoPath = $request->file('photo')->storeAs(
                'user/photos_profile',
                $user->employee_id . '.' . $request->file('photo')->getClientOriginalExtension(),
                'public'
            );

            // Update photo path
            $user->photo_profile_path = $photoPath;
        }

        if ($request->hasFile('id_card')) {
            // Hapus file lama jika ada
            if ($user->id_card_path) {
                Storage::disk('public')->delete('user/ID_card/' . $user->id_card_path);
            }

            // Simpan file baru
            $idCardPath = $request->file('id_card')->storeAs(
                'user/ID_card',
                'ID_card_' . $user->employee_id . '.' . $request->file('id_card')->getClientOriginalExtension(),
                'public'
            );

            // Update path
            $user->id_card_path = $idCardPath;
        }

        if ($request->hasFile('cv')) {
            // Hapus file lama jika ada
            if ($user->cv_path) {
                Storage::disk('public')->delete('user/cv_user/' . $user->cv_path);
            }

            // Simpan file baru
            $cvPath = $request->file('cv')->storeAs(
                'user/cv_user',
                'cv_' . $user->employee_id . '.' . $request->file('cv')->getClientOriginalExtension(),
                'public'
            );

            // Update path
            $user->cv_path = $cvPath;
        }


        if ($request->hasFile('achievement')) {
            // Hapus file lama jika ada
            if ($user->achievement_path) {
                Storage::disk('public')->delete('user/achievement_user/' . $user->cv_path);
            }

            // Simpan file baru
            $achievementPath = $request->file('achievement')->storeAs(
                'user/achievement_user',
                'achievement_' . $user->employee_id . '.' . $request->file('achievement')->getClientOriginalExtension(),
                'public'
            );

            // Update path
            $user->achievement_path = $achievementPath;
        }



        //sim
        if (!$request->has('no_license') || $request->has('sim')) {
            // Lakukan sesuatu
            $user->sim = $request->has('sim') ? implode(',', $request->sim) : null;

            // Ambil nomor SIM sesuai SIM yang dipilih
            $selectedSimNumbers = [];
            if ($request->has('sim')) {
                foreach ($request->sim as $simType) {
                    if (!empty($request->sim_number[$simType])) {
                        $selectedSimNumbers[$simType] = $request->sim_number[$simType];
                    }
                }
            }

            // Simpan nomor SIM dengan format JSON
            $user->sim_number = !empty($selectedSimNumbers) ? json_encode($selectedSimNumbers) : null;
        } else {
            $user->sim = null;
            $user->sim_number = null;
        }


        //bank
        $bankNames = [];
        $bankNumbers = [];

        if ($request->has('bank_name') && $request->has('bank_number')) {
            foreach ($request->bank_name as $index => $bankName) {
                if (!empty($bankName) && !empty($request->bank_number[$index])) {
                    $bankNames[] = $bankName;
                    $bankNumbers[] = $request->bank_number[$index];
                }
            }
        }
        // dd($bankNames,   $bankNumbers);


        // Update data pegawai
        $user->update([
            'password' => $newpassword,
            'employee_id' => $request->employee_id,
            'name' => $request->name,
            'position_id' => $request->position_id,
            'department_id' => $request->department_id,
            'join_date' => $request->join_date,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'employee_status' => $request->employee_status,
            'user_status' => $request->user_status,
            'bpjs_employment' => $request->bpjs_employment,
            'bpjs_health' => $request->bpjs_health,
            'ID_number' => $request->ID_number,
            'birth_date' => $request->birth_date,
            'birth_place' => $request->birth_place,
            'ID_address' => $request->ID_address,
            'domicile_address' => $request->domicile_address,
            'religion' => $request->religion,
            'gender' => $request->gender,
            'height' => $request->height,
            'weight' => $request->weight,
            'distance' => $request->distance,
            'status' => $request->status,
            'NPWP' => $request->npwp,
            'exit_date' => $request->exit_date ?? null,
            'emergency_contact' => $request->emergency_contact,
            'bank_name' => !empty($bankNames) ? json_encode($bankNames) : null,
            'bank_number' => !empty($bankNumbers) ? json_encode($bankNumbers) : null,
            'updated_at' => now(),
        ]);


        // data keluarga
        if ($request->has('name_family')) {
            $id_used = [];

            foreach ($request->name_family as $index => $name) {
                // Cek apakah nama tidak kosong

                if (!is_null($name)) {
                    //dd($name);
                    // Jika ID family sudah ada (update)
                    if (isset($request->id_family) && array_key_exists($index, $request->id_family) && !is_null($request->id_family[$index])) {

                        $id_used[] = $request->id_family[$index]; // Simpan ID untuk pengecekan akhir

                        // Cari data yang ada di database
                        $userfamily = users_family::findOrFail($request->id_family[$index]);

                        // Cek apakah data di database berbeda dengan data dari request
                        if (
                            $userfamily->name !== $name ||
                            $userfamily->relation !== $request->relation[$index] ||
                            $userfamily->birth_date !== $request->birth_date_family[$index] ||
                            $userfamily->birth_place !== $request->birth_place_family[$index] ||
                            $userfamily->ID_number !== $request->ID_number_family[$index] ||
                            $userfamily->phone_number !== $request->phone_number_family[$index] ||
                            $userfamily->address !== $request->address_family[$index] ||
                            $userfamily->gender !== $request->gender_family[$index] ||
                            $userfamily->job !== $request->job[$index]
                        ) {
                            // Update hanya jika ada perubahan
                            $userfamily->update([
                                'name' => $name,
                                'relation' => $request->relation[$index],
                                'birth_date' => $request->birth_date_family[$index],
                                'birth_place' => $request->birth_place_family[$index],
                                'ID_number' => $request->ID_number_family[$index],
                                'phone_number' => $request->phone_number_family[$index],
                                'address' => $request->address_family[$index],
                                'gender' => $request->gender_family[$index],
                                'job' => $request->job[$index],
                                'updated_at' => now(),
                            ]);
                        }
                    } else {
                        // Jika data baru (create)
                        $newFamily = users_family::create([
                            'users_id' => $user->id,
                            'name' => $name,
                            'relation' => $request->relation[$index],
                            'birth_date' => $request->birth_date_family[$index],
                            'birth_place' => $request->birth_place_family[$index],
                            'ID_number' => $request->ID_number_family[$index],
                            'phone_number' => $request->phone_number_family[$index],
                            'address' => $request->address_family[$index],
                            'gender' => $request->gender_family[$index],
                            'job' => $request->job[$index],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        $id_used[] = $newFamily->id; // Simpan ID yang baru di-create
                    }
                }
            }

            // Hapus data yang tidak ada di $id_used
            users_family::where('users_id', $user->id)
                ->whereNotIn('id', $id_used)
                ->delete();
        }


        // Logika untuk Education
        if ($request->has('education_level')) {
            $id_used_education = [];

            foreach ($request->education_level as $index => $education_level) {
                // Cek apakah degree pendidikan tidak kosong
                if (!is_null($education_level)) {
                    if (isset($request->id_education) && array_key_exists($index, $request->id_education) && !is_null($request->id_education[$index])) {
                        // Update data jika ID education ada
                        $id_used_education[] = $request->id_education[$index];

                        $userEducation = users_education::findOrFail($request->id_education[$index]);

                        // Cek apakah ada perubahan pada data
                        if (
                            $userEducation->degree !== $education_level ||
                            $userEducation->educational_place !== $request->education_place[$index] ||
                            $userEducation->educational_city !== $request->education_city[$index] ||
                            $userEducation->start_education !== $request->start_education[$index] ||
                            $userEducation->end_education !== $request->end_education[$index] ||
                            $userEducation->major !== $request->major[$index] ||
                            $userEducation->grade !== $request->grade[$index]
                        ) {
                            $userEducation->update([
                                'degree' => $education_level,
                                'educational_place' => $request->education_place[$index],
                                'educational_city' => $request->education_city[$index],
                                'educational_province' => $request->education_province[$index],
                                'start_education' => $request->start_education[$index],
                                'end_education' => $request->end_education[$index],
                                'major' => $request->major[$index],
                                'grade' => $request->grade[$index],
                                'updated_at' => now(),
                            ]);
                        }

                        // Handle file upload untuk sertifikat/transkrip
                        if ($request->hasFile("education_transcript.$index")) {
                            // Jika ada file lama, hapus terlebih dahulu
                            if ($userEducation->transcript_file_path && Storage::disk('public')->exists($userEducation->transcript_file_path)) {
                                Storage::disk('public')->delete($userEducation->transcript_file_path);
                            }

                            $file = $request->file("education_transcript.$index");
                            $extension = $file->getClientOriginalExtension();

                            // Buat nama file yang sesuai
                            $fileName = "transcript_" . Str::slug($user->name) . "_{$user->id}_{$education_level}_{$userEducation->id}.{$extension}";

                            // Simpan file ke public/user/achievement_user
                            $filePath = $file->storeAs('user/achievement_user', $fileName, 'public');

                            // Update path file di database
                            $userEducation->update(['transcript_file_path' => "user/achievement_user/{$fileName}"]);
                        }
                    } else {
                        // Create data baru jika ID education tidak ada
                        $newEducation = users_education::create([
                            'users_id' => $user->id,
                            'degree' => $education_level,
                            'educational_place' => $request->education_place[$index],
                            'educational_city' => $request->education_city[$index],
                            'educational_province' => $request->education_province[$index],
                            'start_education' => $request->start_education[$index],
                            'end_education' => $request->end_education[$index],
                            'major' => $request->major[$index],
                            'grade' => $request->grade[$index],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        $id_used_education[] = $newEducation->id;

                        // Handle file upload untuk sertifikat/transkrip
                        if ($request->hasFile("education_transcript.$index")) {
                            $file = $request->file("education_transcript.$index");
                            $extension = $file->getClientOriginalExtension();

                            // Buat nama file yang sesuai
                            $fileName = "transcript_" . Str::slug($user->name) . "_{$user->id}_{$education_level}_{$newEducation->id}.{$extension}";

                            // Simpan file ke public/user/achievement_user
                            $filePath = $file->storeAs('user/achievement_user', $fileName, 'public');

                            // Update path file di database
                            $newEducation->update(['transcript_file_path' => "user/achievement_user/{$fileName}"]);
                        }
                    }
                }
            }

            // Ambil data pendidikan yang akan dihapus
            $educationsToDelete = users_education::where('users_id', $user->id)
                ->whereNotIn('id', $id_used_education)
                ->get();

            // Hapus file transkrip terlebih dahulu
            foreach ($educationsToDelete as $education) {
                if ($education->transcript_file_path && Storage::disk('public')->exists($education->transcript_file_path)) {
                    Storage::disk('public')->delete($education->transcript_file_path);
                }
            }

            // Kemudian hapus data dari database
            users_education::where('users_id', $user->id)
                ->whereNotIn('id', $id_used_education)
                ->delete();
        }

        // Logika untuk Work
        if ($request->has('company_name')) {
            $id_used_work = [];

            foreach ($request->company_name as $index => $company_name) {
                // Cek apakah nama perusahaan tidak kosong
                if (!is_null($company_name)) {
                    if (isset($request->id_work) && array_key_exists($index, $request->id_work) && !is_null($request->id_work[$index])) {

                        // Update data jika ID work ada
                        $id_used_work[] = $request->id_work[$index];

                        $userWork = users_work_experience::findOrFail($request->id_work[$index]);

                        // Cek apakah ada perubahan pada data
                        if (
                            $userWork->nama_perusahaan !== $company_name ||
                            $userWork->position !== $request->position[$index] ||
                            $userWork->start_work !== $request->start_work[$index] ||
                            $userWork->end_work !== $request->end_work[$index]
                        ) {
                            $userWork->update([
                                'company_name' => $company_name,
                                'position' => $request->position_work[$index] ?? null,
                                'start_working' => $request->start_work[$index] ?? null,
                                'end_working' => $request->end_work[$index] ?? null,

                                'company_address' => $request->company_address[$index] ?? null,
                                'company_phone' => $request->company_phone[$index] ?? null,
                                'salary' => $request->previous_salary[$index] ?? null,
                                'supervisor_name' => $request->supervisor_name[$index] ?? null,
                                'supervisor_phone' => $request->supervisor_phone[$index] ?? null,
                                'job_desc' => str_replace(["\r", "\n", "-", "  "], ["", ";", "", " "], trim($request->job_desc[$index] ?? null)),
                                'reason' => str_replace(["\r", "\n", "-", "  "], ["", ";", "", " "], trim($request->reason[$index] ?? null)),
                                'benefit' => str_replace(["\r", "\n", "-", "  "], ["", ";", "", " "], trim($request->benefit[$index] ?? null)),
                                'facility' => str_replace(["\r", "\n", "-", "  "], ["", ";", "", " "], trim($request->facility[$index] ?? null)),

                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    } else {
                        // Create data baru jika ID work tidak ada
                        $newWork = users_work_experience::create([
                            'users_id' => $user->id,
                            'nama_perusahaan' => $company_name,

                            'position' => $request->position_work[$index] ?? null,
                            'start_working' => $request->start_work[$index] ?? null,
                            'end_working' => $request->end_work[$index] ?? null,
                            'company_address' => $request->company_address[$index] ?? null,
                            'company_phone' => $request->company_phone[$index] ?? null,
                            'salary' => $request->salary[$index] ?? null,
                            'supervisor_name' => $request->supervisor_name[$index] ?? null,
                            'supervisor_phone' => $request->supervisor_phone[$index] ?? null,
                            'job_desc' => str_replace(["\r", "\n", "-", "  "], ["", ";", "", " "], trim($request->job_desc[$index] ?? null)),
                            'reason' => str_replace(["\r", "\n", "-", "  "], ["", ";", "", " "], trim($request->reason[$index] ?? null)),
                            'benefit' => str_replace(["\r", "\n", "-", "  "], ["", ";", "", " "], trim($request->benefit[$index] ?? null)),
                            'facility' => str_replace(["\r", "\n", "-", "  "], ["", ";", "", " "], trim($request->facility[$index] ?? null)),

                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        $id_used_work[] = $newWork->id;
                    }
                }
            }

            // Hapus data yang tidak ada di $id_used_work
            users_work_experience::where('users_id', $user->id)
                ->whereNotIn('id', $id_used_work)
                ->delete();

            // dd('beres');
        }



        // Logika untuk Training
        if ($request->has('training_name')) {
            $id_used_training = [];

            foreach ($request->training_name as $index => $training_name) {
                if (!is_null($training_name)) {
                    if (isset($request->id_training) && array_key_exists($index, $request->id_training) && !is_null($request->id_training[$index])) {
                        $id_used_training[] = $request->id_training[$index];
                        $userTraining = users_training::findOrFail($request->id_training[$index]);

                        if (
                            $userTraining->training_name !== $training_name ||
                            $userTraining->training_city !== $request->training_city[$index] ||
                            $userTraining->start_date !== $request->training_start_date[$index] ||
                            $userTraining->end_date !== $request->training_end_date[$index]
                        ) {
                            $userTraining->update([
                                'training_name' => $training_name,
                                'training_city' => $request->training_city[$index] ?? null,
                                'training_province' => $request->training_province[$index] ?? null,
                                'start_date' => $request->training_start_date[$index] ?? null,
                                'end_date' => $request->training_end_date[$index] ?? null,
                                'updated_at' => now(),
                            ]);
                        }
                    } else {
                        $newTraining = users_training::create([
                            'users_id' => $user->id,
                            'training_name' => $training_name,
                            'training_city' => $request->training_city[$index] ?? null,
                            'training_province' => $request->training_province[$index] ?? null,
                            'start_date' => $request->training_start_date[$index] ?? null,
                            'end_date' => $request->training_end_date[$index] ?? null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        $id_used_training[] = $newTraining->id;
                    }
                }
            }

            users_training::where('users_id', $user->id)
                ->whereNotIn('id', $id_used_training)
                ->delete();
        }

        // Logika untuk Organization
        if ($request->has('organization_name')) {
            $id_used_organization = [];

            foreach ($request->organization_name as $index => $organization_name) {
                if (!is_null($organization_name)) {
                    if (isset($request->id_organization) && array_key_exists($index, $request->id_organization) && !is_null($request->id_organization[$index])) {

                        $id_used_organization[] = $request->id_organization[$index];
                        $userOrganization = users_organization::findOrFail($request->id_organization[$index]);

                        if (
                            $userOrganization->organization_name !== $organization_name ||
                            $userOrganization->position !== $request->organization_position[$index] ||
                            $userOrganization->start_date !== $request->organization_start_date[$index] ||
                            $userOrganization->end_date !== $request->organization_end_date[$index]
                        ) {
                            $userOrganization->update([
                                'organization_name' => $organization_name,
                                'position' => $request->organization_position[$index] ?? null,
                                'city' => $request->organization_city[$index] ?? null,
                                'province' => $request->organization_province[$index] ?? null,
                                'activity_type' => str_replace(["\r", "\n", "-", "  "], ["", ";", "", " "], trim($request->activity_type[$index] ?? null)),

                                'start_date' => $request->organization_start_date[$index] ?? null,
                                'end_date' => $request->organization_end_date[$index] ?? null,
                                'updated_at' => now(),
                            ]);
                        }
                    } else {
                        $newOrganization = users_organization::create([
                            'users_id' => $user->id,
                            'organization_name' => $organization_name,
                            'position' => $request->organization_position[$index] ?? null,
                            'city' => $request->organization_city[$index] ?? null,
                            'province' => $request->organization_province[$index] ?? null,
                            'activity_type' => str_replace(["\r", "\n", "-", "  "], ["", ";", "", " "], trim($request->activity_type[$index] ?? null)),
                            'start_date' => $request->organization_start_date[$index] ?? null,
                            'end_date' => $request->organization_end_date[$index] ?? null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        $id_used_organization[] = $newOrganization->id;
                    }
                }
            }

            users_organization::where('users_id', $user->id)
                ->whereNotIn('id', $id_used_organization)
                ->delete();
        }

        // Logika untuk Language
        if ($request->has('language')) {
            $id_used_language = [];

            foreach ($request->language as $index => $language) {
                if (!is_null($language)) {
                    if (isset($request->id_language) && array_key_exists($index, $request->id_language) && !is_null($request->id_language[$index])) {

                        $id_used_language[] = $request->id_language[$index];
                        $userLanguage = users_language::findOrFail($request->id_language[$index]);




                        if (
                            $userLanguage->language !== $language ||
                            $userLanguage->verbal !== $request->verbal[$index] ||
                            $userLanguage->written !== $request->written[$index]
                        ) {
                            if ($language === 'Other') {
                                $otherLanguage = $request->other_language[$index] ?? null;
                                if (empty($otherLanguage)) {
                                    continue;
                                }
                                $language = $otherLanguage;
                            }



                            $userLanguage->update([
                                'language' => $language,
                                'verbal' => $request->verbal[$index] ?? null,
                                'written' => $request->written[$index] ?? null,
                                'updated_at' => now(),
                            ]);
                        }
                    } else {
                        if ($language === 'Other') {
                            $otherLanguage = $request->other_language[$index] ?? null;
                            if (empty($otherLanguage)) {
                                continue;
                            }
                            $language = $otherLanguage;
                        }

                        $newLanguage = users_language::create([
                            'users_id' => $user->id,
                            'language' => $language,
                            'verbal' => $request->verbal[$index] ?? null,
                            'written' => $request->written[$index] ?? null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        $id_used_language[] = $newLanguage->id;
                    }
                }
            }

            users_language::where('users_id', $user->id)
                ->whereNotIn('id', $id_used_language)
                ->delete();
        }


        // 1. Send update notification to the employee who was updated
        Mail::to($user->email)->send(new UpdateNotification($user->name));

        // Create notification for the updated employee
        Notification::create([
            'users_id' => $user->id,
            'message' => "Your profile information has been updated",
            'type' => 'employee_update',
            'maker_id' => Auth::user()->id,
            'status' => 'Unread'
        ]);

        // 2. Get HR department users (first we need to find HR department ID)
        $hrDepartment = EmployeeDepartment::where('department', 'Human Resources')->first();
        if ($hrDepartment) {
            // Get HR users excluding the updated user (if they're in HR)
            $hrUsers = User::where('department_id', $hrDepartment->id)
                ->where('id', '!=', $user->id)
                ->get();

            // Send email to all HR staff
            $hrEmails = $hrUsers->pluck('email');
            Mail::to($hrEmails)->send(new DepartmentUpdateNotification(
                $user->employee_id,
                $user->position->position, // Access position name through relationship
                $user->department->department // Access department name through relationship
            ));

            // Create notifications for each HR staff
            foreach ($hrUsers as $hrUser) {
                Notification::create([
                    'users_id' => $hrUser->id,
                    'message' => "Employee data for {$user->name} (ID: {$user->employee_id}) has been updated",
                    'type' => 'employee_update',
                    'maker_id' => Auth::user()->id,
                    'status' => 'Unread'
                ]);
            }
        }

        // Redirect ke halaman index atau halaman lain dengan pesan sukses
        return response()->json([
            'message' => 'Employee Updated successfully',
            'redirect' => route('user.employees.index')
        ]);
    }

    public function employees_history($id)
    {
        $user = User::findOrFail($id);

        $historyTransfers = history_transfer_employee::where('users_id', $id)->get();
        $historyExtend = history_extend_employee::where('users_id', $id)->get();

        return view('user.employees.history', compact('user', 'historyTransfers', 'historyExtend'));
    }


    public function employees_transfer($id)
    {
        // Find the Pegawai by ID
        $user = User::with(['position', 'department'])->findOrFail($id);
        $departments = EmployeeDepartment::orderBy('department')->get();
        $positions = EmployeePosition::orderBy('ranking')->get();
        // Pass the data to the update view
        return view('user.employees.transfer', compact('user', 'departments', 'positions'));
    }



    public function employees_transfer_user(Request $request, $id)
    {
        $request->validate([
            'old_position_id' => 'required|exists:employee_positions,id',
            'old_department_id' => 'required|exists:employee_departments,id',
            'new_position_id' => 'nullable|exists:employee_positions,id',
            'new_department_id' => 'nullable|exists:employee_departments,id',
            'reason' => 'required|string|max:255',
            'transfer_type' => 'required|string|max:255',
        ]);

        $user = User::with(['position', 'department'])->findOrFail($id);

        // Get position and department names
        $oldPosition = EmployeePosition::find($request->old_position_id);
        $oldDepartment = EmployeeDepartment::find($request->old_department_id);
        $newPosition = $request->new_position_id ? EmployeePosition::find($request->new_position_id) : null;
        $newDepartment = $request->new_department_id ? EmployeeDepartment::find($request->new_department_id) : null;

        // Create transfer history
        history_transfer_employee::create([
            'users_id' => $id,
            'old_position_id' => $request->old_position_id,
            'old_department_id' => $request->old_department_id,
            'new_position_id' => $request->new_position_id,
            'new_department_id' => $request->new_department_id,
            'transfer_type' => $request->transfer_type,
            'reason' => $request->reason,
        ]);

        // Update user based on transfer type
        if ($request->transfer_type == "Penetapan") {
            $user->update(['employee_status' => "Full Time"]);
        } elseif ($request->transfer_type == "Resign") {
            $user->update(['employee_status' => "Inactive"]);
        } else {
            $user->update([
                'position_id' => $request->new_position_id,
                'department_id' => $request->new_department_id,
            ]);
        }

        // Send email notification to employee
        Mail::to($user->email)->send(new TransferNotification(
            $user,
            $oldPosition->position,
            $oldDepartment->department,
            $newPosition ? $newPosition->position : null,
            $newDepartment ? $newDepartment->department : null,
            $request->transfer_type,
            $request->reason,
            false // isHR flag
        ));

        // Create notification for the user
        $message = $this->getTransferMessage($request->transfer_type, $oldPosition, $oldDepartment, $newPosition, $newDepartment);

        Notification::create([
            'users_id' => $user->id,
            'message' => $message,
            'type' => 'employee_transfer',
            'maker_id' => Auth::id(),
            'status' => 'Unread'
        ]);

        // Send email notification to HR department
        $this->sendHRNotification($user, $request->transfer_type, $oldPosition, $oldDepartment, $newPosition, $newDepartment, $request->reason);

        return redirect()->route('user.employees.index')->with('success', 'Employee transferred successfully.');
    }



    protected function getTransferMessage($transferType, $oldPosition, $oldDepartment, $newPosition, $newDepartment)
    {
        switch ($transferType) {
            case 'Penetapan':
                return "Your employment status has been changed to Permanent (Tetap)";
            case 'Resign':
                return "Your employment status has been changed to Inactive (Resign)";
            default:
                return sprintf(
                    "Your position has been changed from %s (%s) to %s (%s)",
                    $oldPosition->position,
                    $oldDepartment->department,
                    $newPosition ? $newPosition->position : 'N/A',
                    $newDepartment ? $newDepartment->department : 'N/A'
                );
        }
    }

    protected function sendHRNotification($user, $transferType, $oldPosition, $oldDepartment, $newPosition, $newDepartment, $reason)
    {
        $hrDepartment = EmployeeDepartment::where('department', 'Human Resources')->first();

        if ($hrDepartment) {
            $hrUsers = User::where('department_id', $hrDepartment->id)
                ->where('id', '!=', $user->id)
                ->get();

            foreach ($hrUsers as $hrUser) {
                // Send email to each HR staff
                Mail::to($hrUser->email)->send(new TransferNotification(
                    $user,
                    $oldPosition->position,
                    $oldDepartment->department,
                    $newPosition ? $newPosition->position : null,
                    $newDepartment ? $newDepartment->department : null,
                    $transferType,
                    $reason,
                    true // isHR flag
                ));

                // Create notification for HR staff
                Notification::create([
                    'users_id' => $hrUser->id,
                    'message' => $this->getHRNotificationMessage($user, $transferType, $oldPosition, $oldDepartment, $newPosition, $newDepartment),
                    'type' => 'employee_transfer',
                    'maker_id' => Auth::id(),
                    'status' => 'Unread'
                ]);
            }
        }
    }

    protected function getHRNotificationMessage($user, $transferType, $oldPosition, $oldDepartment, $newPosition, $newDepartment)
    {
        switch ($transferType) {
            case 'Penetapan':
                return sprintf(
                    "Employee %s (ID: %s) has been made Permanent (Tetap)",
                    $user->name,
                    $user->employee_id
                );
            case 'Resign':
                return sprintf(
                    "Employee %s (ID: %s) has resigned",
                    $user->name,
                    $user->employee_id
                );
            default:
                return sprintf(
                    "Employee %s (ID: %s) has been transferred from %s (%s) to %s (%s)",
                    $user->name,
                    $user->employee_id,
                    $oldPosition->position,
                    $oldDepartment->department,
                    $newPosition ? $newPosition->position : 'N/A',
                    $newDepartment ? $newDepartment->department : 'N/A'
                );
        }
    }




    public function employees_extend_date(Request $request, $id)
    {

        // Validasi input
        $request->validate([
            'contract_start_date' => 'required|date',
            'contract_end_date' => 'required|date|after_or_equal:contract_start_date',
            'reason' => 'required|string|max:255',
        ]);

        // Ambil data user
        $user = User::with(['position', 'department'])->findOrFail($id);

        // Update tanggal kontrak di tabel users
        $user->update([
            'contract_start_date' => $request->contract_start_date,
            'contract_end_date' => $request->contract_end_date,
            'updated_at' => now()
        ]);

        // Simpan ke history_extend_employee
        history_extend_employee::create([
            'users_id' => $id,
            'position_id' => $user->position_id,
            'department_id' => $user->department_id,
            'start_date' => $user->contract_start_date,
            'end_date' => $user->contract_end_date,
            'reason' => $request->reason,
        ]);

        // Send email notification to employee
        Mail::to($user->email)->send(new ContractExtensionNotification(
            $user,
            $request->contract_start_date,
            $request->contract_end_date,
            $request->reason,
            false // isHR flag
        ));

        // Create notification for the employee
        Notification::create([
            'users_id' => $user->id,
            'message' => "Your contract has been extended until " . date('F j, Y', strtotime($request->contract_end_date)),
            'type' => 'contract_extension',
            'maker_id' => Auth::id(),
            'status' => 'Unread'
        ]);

        // Send email notification to HR department
        $this->notifyHRExtension($user, $request->contract_start_date, $request->contract_end_date, $request->reason);

        return redirect()->back()->with('success', 'Contract extended successfully!');
    }

    protected function notifyHRExtension($user, $startDate, $endDate, $reason)
    {
        $hrDepartment = EmployeeDepartment::where('department', 'Human Resources')->first();

        if ($hrDepartment) {
            $hrUsers = User::where('department_id', $hrDepartment->id)
                ->where('id', '!=', $user->id)
                ->get();

            foreach ($hrUsers as $hrUser) {
                // Send email to each HR staff
                Mail::to($hrUser->email)->send(new ContractExtensionNotification(
                    $user,
                    $startDate,
                    $endDate,
                    $reason,
                    true // isHR flag
                ));

                // Create notification for HR staff
                Notification::create([
                    'users_id' => $hrUser->id,
                    'message' => "Contract extended for {$user->name} (ID: {$user->employee_id}) until " . date('F j, Y', strtotime($endDate)),
                    'type' => 'contract_extension',
                    'maker_id' => Auth::id(),
                    'status' => 'Unread'
                ]);
            }
        }
    }



    // ... method employees yang sudah ada ...

    // ==================== DEPARTMENT METHODS ====================
    public function departments_index()
    {
        $departments = EmployeeDepartment::all();
        return view('user.departments.index', compact('departments'));
    }

    public function departments_create()
    {
        return view('user.departments.create');
    }

    public function departments_store(Request $request)
    {
        $request->validate(['department' => 'required|string|max:255']);

        EmployeeDepartment::create(['department' => $request->department]);
        return redirect()->route('user.departments.index')->with('success', 'Department created');
    }

    public function departments_edit($id)
    {
        $department = EmployeeDepartment::findOrFail($id);
        return view('user/departments/update', compact('department'));
    }

    public function departments_update(Request $request, $id)
    {
        $request->validate(['department' => 'required|string|max:255']);

        EmployeeDepartment::findOrFail($id)->update(['department' => $request->department]);
        return redirect()->route('user.departments.index')->with('success', 'Department updated');
    }

    // ==================== POSITION METHODS ====================
    public function positions_index()
    {
        $positions = EmployeePosition::orderBy('ranking', 'asc')->get();
        return view('user.positions.index', compact('positions'));
    }

    public function positions_create()
    {
        return view('user.positions.create');
    }

    public function positions_store(Request $request)
    {
        $request->validate([
            'position' => 'required|string|max:255',
            'ranking' => 'required|integer'
        ]);

        EmployeePosition::create($request->only(['position', 'ranking']));
        return redirect()->route('user.positions.index')->with('success', 'Position created');
    }

    public function positions_edit($id)
    {
        $position = EmployeePosition::findOrFail($id);
        return view('user/positions/update', compact('position'));
    }

    public function positions_update(Request $request, $id)
    {
        $request->validate([
            'position' => 'required|string|max:255',
            'ranking' => 'required|integer'
        ]);

        EmployeePosition::findOrFail($id)->update($request->only(['position', 'ranking']));
        return redirect()->route('user.positions.index')->with('success', 'Position updated');
    }
}
