<?php



namespace App\Http\Controllers;

use App\Imports\EmployeesImport;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Bus\UpdatedBatchJobCounts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

use App\Models\User;
use App\Models\history_transfer_employee;
use App\Models\history_extend_employee;
use App\Models\users_education;
use App\Models\users_family;
use App\Models\users_work_experience;
use App\Models\users_language;
use App\Models\users_training;
use App\Models\users_organization;

use App\Mail\TransferNotification;
use App\Mail\UpdateNotification;
use App\Mail\DepartmentUpdateNotification;
use App\Mail\NewEmployeeNotification;
use App\Mail\WelcomeNewEmployee;

use Carbon\Carbon;

class UserController extends Controller
{
    // Display a listing of pegawai
    public function index(Request $request)
    {
        $query = User::query();

        // Apply filters
        if ($request->filled('status')) {
            $query->where('employee_status', $request->status);
        }

        if ($request->filled('position')) {
            $query->where('position', $request->position);
        }

        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        if ($request->filled('status_app')) {
            $query->where('user_status', $request->status_app);
        }

        // Get the filtered results
        $user = $query->get();

        // Get unique values for dropdowns
        $department = User::distinct()->pluck('department');
        $position = User::distinct()->pluck('position');
        $status = User::distinct()->pluck('employee_status');
        $status_app = User::distinct()->pluck('user_status');

        return view('user.index', compact('user', 'department', 'position', 'status', 'status_app'));
    }

    // Show the form for creating a new pegawai
    public function create()
    {
        return view('user/create');
    }



    // Store a newly created pegawai in the database
    public function store(Request $request)
    {
        //dd($request->all());
        // Validate the form data
        $request->validate([
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'id_card' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'cv' => 'nullable|mimes:pdf|max:2048',
            'achievement' => 'nullable|mimes:pdf|max:2048',
            'employee_id' => 'required',
            'name' => 'required',
            'position' => 'required',
            'department' => 'required',
            'join_date' => 'required',
            'email' => 'required',
            'phone_number' => 'required',
            'employee_status' => 'required',
            'user_status' => 'required',
            'ID_number' => 'required',
            'birth_date' => 'required',
            'birth_place' => 'required',
            'ID_address' => 'required',
            'domicile_address' => 'required',
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



        // Create a new Pegawai record
        User::create([
            'employee_id' => $employeeId,
            'sim' => $license ?? null,
            'sim_number' => $license_number ?? null,
            'photo_profile_path' => $temp ?? null,
            'id_card_path' => $idCardPath ?? null,
            'cv_path' => $cvPath ?? null,
            'achievement_path' => $achievementPath ?? null,
            'name' => $request->name,
            'position' => $request->position,
            'department' => $request->department,
            'join_date' => $joinDate,
            'contract_start_date' => $request->contract_start_date ?? null,
            'contract_end_date' => $request->contract_end_date ?? null,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'employee_status' => $request->employee_status,
            'user_status' => $request->user_status,
            'bpjs_employment' => $request->bpjs_employment ?? null,
            'bpjs_kesehatan' => $request->bpjs_kesehatan ?? null,
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


        $user = User::where('employee_id', $request->employee_id)->first();
        //dd($user->id);

        // data keluarga
        if ($request->has('name_family')) {
            foreach ($request->name_family as $index => $name) {
                if ($request->name_family[$index] !== null) {
                    users_family::create([
                        'users_id' => $user->id, // Ambil ID pegawai yang baru disimpan
                        'name' => $request->name_family[$index],
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
                }
            }
        }

        if ($request->has('education_level')) {
            foreach ($request->education_level as $index => $name) {
                if ($request->degree[$index] !== null) {
                    users_education::create([
                        'users_id' => $user->id,
                        'degree' => $request->education_level[$index],
                        'educational_place' => $request->education_place[$index],
                        'educational_city' => $request->education_city[$index],
                        'start_education'  => $request->start_education[$index],
                        'end_education'  => $request->end_education[$index],
                        'major'  => $request->major[$index],
                        'grade'  => $request->grade[$index],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        if ($request->has('company_name')) {
            foreach ($request->company_name as $index => $name) {
                if ($request->company_name[$index] !== null) {
                    users_work_experience::create([
                        'users_id' => $user->id,
                        'company_name' => $request->company_name[$index],
                        'position' => $request->position_working[$index],
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






        // Kirim email ke semua user yang sudah ada
        $allUsers = User::where('id', '!=', $user->id)->pluck('email');
        foreach ($allUsers as $email) {
            Mail::to($email)->send(new NewEmployeeNotification($user));
        }

        // Kirim email ke pegawai baru
        Mail::to($user->email)->send(new WelcomeNewEmployee($user));






        return redirect()->route('user.index')->with('success', 'Employee added successfully');
    }



    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv',
        ]);

        Excel::import(new EmployeesImport, $request->file('file'));

        return redirect()->back()->with('success', 'Employees imported successfully.');
    }

    public function edit($id)
    {
        // Find the Pegawai by ID
        $user = User::findOrFail($id);

        //dd($user->cv_path);

        // Retrieve associated records with the user's ID
        $userEducation = users_education::where('users_id', $id)->get();
        $userWork = users_work_experience::where('users_id', $id)->get();
        $userFamily = users_family::where('users_id', $id)->get();
        $userTraining = users_training::where('users_id', $id)->get();
        $userLanguage = users_language::where('users_id', $id)->get();
        $userOrganization = users_organization::where('users_id', $id)->get();

        // If no related data, you could check for empty sets or return defaults
        if ($userEducation->isEmpty()) {
            $userEducation = null;  // Or set defaults if necessary
        }
        if ($userWork->isEmpty()) {
            $userWork = null;
        }
        if ($userFamily->isEmpty()) {
            $userFamily = null;
        }

        if ($userOrganization->isEmpty()) {
            $userOrganization = null;
        }

        if ($userLanguage->isEmpty()) {
            $userLanguage = null;
        }

        if ($userTraining->isEmpty()) {
            $userTraining = null;
        }


        $duty = DB::table('elearning_invitation')
            ->join('elearning_lesson', 'elearning_invitation.lesson_id', '=', 'elearning_lesson.id')
            ->join('elearning_schedule', 'elearning_invitation.schedule_id', '=', 'elearning_schedule.id')
            ->where('elearning_invitation.users_id', $id)
            ->select(
                'elearning_invitation.id as invitation_id', // Alias untuk menghindari konflik ID
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

        // Pass the data to the update view
        return view('user.update', compact('user', 'userEducation', 'userWork', 'userFamily', 'userOrganization', 'userLanguage', 'userTraining', 'duty'));
    }

    public function update(Request $request, $id)
    {
        dd($request->department);
        // Validasi input


        $request->validate([
            'password' => 'nullable|min:8',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'id_card' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'cv' => 'nullable|mimes:pdf|max:2048',
            'achievement' => 'nullable|mimes:pdf|max:2048',
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
            'employee_status' => 'required',
            'email' => 'required|email',
            'join_date' => 'required',
            'position' => 'required',
            'contract_start_date' => 'required_if:employee_status,Contract,Part Time|nullable|date',
            'contract_end_date' => 'required_if:employee_status,Contract,Part Time|nullable|date',

            'department' => 'required',

        ]);

        //dd('masuk validate');

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




        // Update data pegawai
        $user->update([
            'password' => $newpassword,
            'employee_id' => $request->employee_id,
            'name' => $request->name,
            'position' => $request->position,
            'department' => $request->department,
            'join_date' => $request->join_date,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'employee_status' => $request->employee_status,
            'user_status' => $request->user_status,
            'bpjs_employment' => $request->bpjs_employment,
            'bpjs_kesehatan' => $request->bpjs_kesehatan,
            'ID_number' => $request->ID_number,
            'birth_date' => $request->birth_date,
            'birth_place' => $request->birth_place,
            'ID_address' => $request->ID_address,
            'domicile_address' => $request->domicile_address,
            'religion' => $request->religion,
            'gender' => $request->gender,
            'height' => $request->height,
            'weight' => $request->weight,
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
                            $userEducation->educational_city !== $request->educational_city[$index] ||
                            $userEducation->start_education !== $request->start_education[$index] ||
                            $userEducation->end_education !== $request->end_education[$index] ||
                            $userEducation->major !== $request->major[$index] ||
                            $userEducation->grade !== $request->grade[$index]
                        ) {
                            $userEducation->update([
                                'degree' => $education_level,
                                'educational_place' => $request->education_place[$index],
                                'educational_city' => $request->education_city[$index],
                                'start_education' => $request->start_education[$index],
                                'end_education' => $request->end_education[$index],
                                'major' => $request->major[$index],
                                'grade' => $request->grade[$index],
                                'updated_at' => now(),
                            ]);
                        }
                    } else {
                        // Create data baru jika ID education tidak ada
                        $newEducation = users_education::create([
                            'users_id' => $user->id,
                            'degree' => $education_level,
                            'educational_place' => $request->education_place[$index],
                            'educational_city' => $request->education_city[$index],
                            'start_education' => $request->start_education[$index],
                            'end_education' => $request->end_education[$index],
                            'major' => $request->major[$index],
                            'grade' => $request->grade[$index],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        $id_used_education[] = $newEducation->id;
                    }
                }
            }

            // Hapus data yang tidak ada di $id_used_education
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
                                'nama_perusahaan' => $company_name,
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
                            $userLanguage->update([
                                'language' => $language,
                                'verbal' => $request->verbal[$index] ?? null,
                                'written' => $request->written[$index] ?? null,
                                'updated_at' => now(),
                            ]);
                        }
                    } else {
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



        Mail::to($user->email)->send(new UpdateNotification($user->name));



        // Ambil email karyawan di HR
        $hr_emails = User::where('department', 'Human Resources')->where('email', '!=', $user->email)->pluck('email');


        Mail::to($hr_emails)->send(new DepartmentUpdateNotification($user->employee_id, $user->position, $user->department));


        // Redirect ke halaman index atau halaman lain dengan pesan sukses
        return redirect()->route('user.index')->with('success', 'Data Pegawai berhasil diupdate.');
    }



    public function history($id)
    {
        $user = User::findOrFail($id);

        $historyTransfers = history_transfer_employee::where('users_id', $id)->get();
        $historyExtend = history_extend_employee::where('users_id', $id)->get();

        return view('user.history', compact('user', 'historyTransfers', 'historyExtend'));
    }


    public function transfer($id)
    {
        // Find the Pegawai by ID
        $user = User::findOrFail($id);
        // Pass the data to the update view
        return view('user.transfer', compact('user'));
    }


    public function transfer_user(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'old_position' => 'required|string|max:255',
            'old_department' => 'required|string|max:255',
            'new_position' => 'string|max:255',
            'new_department' => 'string|max:255',
            'reason' => 'required|string|max:255',
            'transfer_type' => 'required|string|max:255',
        ]);

        // Create a new History
        history_transfer_employee::create([
            'users_id' => $id,
            'old_position' => $request->old_position,
            'old_department' => $request->old_department,
            'new_position' => $request->new_position,
            'new_department' => $request->new_department,
            'transfer_type' => $request->transfer_type,
            'reason' => $request->reason,
        ]);

        // Cari data pegawai berdasarkan ID
        $user = User::findOrFail($id);

        // Update user data
        if ($request->transfer_type == "Penetapan") {
            $user->update([
                'employee_status' => "Tetap",
            ]);
        } elseif ($request->transfer_type == "Resign") {
            $user->update([
                'employee_status' => "Inactive",
            ]);
        } else {
            $user->update([
                'position' => $request->new_position,
                'department' => $request->new_department,
            ]);
        }

        // Kirim email notifikasi
        Mail::to($user->email)->send(new TransferNotification(
            $request->old_position,
            $request->old_department,
            $request->new_position,
            $request->new_department,
            $request->transfer_type
        ));
        // Redirect ke halaman index atau halaman lain dengan pesan sukses
        return redirect()->route('user.index')->with('success', 'Employee Transfered successfully.');
    }


    public function extendDate(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'contract_start_date' => 'required|date',
            'contract_end_date' => 'required|date|after_or_equal:contract_start_date',
            'reason' => 'required|string|max:255',
        ]);

        // Ambil data user
        $user = User::findOrFail($id);
        // Update tanggal kontrak di tabel users
        $user->update([
            'contract_start_date' => $request->contract_start_date,
            'contract_end_date' => $request->contract_end_date,
            'updated_at' => now()
        ]);

        // Simpan ke history_extend_employee
        history_extend_employee::create([
            'users_id' => $id,
            'position' => $user->position,
            'department' => $user->department,
            'start_date' => $user->contract_start_date,
            'end_date' => $user->contract_end_date,
            'reason' => $request->reason,
        ]);

        return redirect()->back()->with('success', 'Contract extended successfully!');
    }
}
