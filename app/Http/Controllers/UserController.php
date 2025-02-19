<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\history_transfer_employee;
use App\Models\history_extend_employee;
use App\Models\users_education;
use App\Models\users_family;
use App\Models\users_work_experience;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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
            'height' => 'required',
            'weight' => 'required',
            'blood_type' => 'required',
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


        // Create a new Pegawai record
        User::create([
            'photo_profile_path' => $temp ?? null,
            'id_card_path' => $idCardPath ?? null,
            'cv_path' => $cvPath ?? null,
            'employee_id' => $request->employee_id,
            'name' => $request->name,
            'position' => $request->position,
            'department' => $request->department,
            'join_date' => $request->join_date,
            'contract_start_date' => $request->contract_start_date,
            'contract_end_date' => $request->contract_end_date,
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
            'blood_type' => $request->blood_type, // Simpan blood_type
            'created_at' => now(),
            'updated_at' => now(),
            'password' => $password,
        ]);


        $user = User::where('employee_id', $request->employee_id)->first();
        //dd($user->id);
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
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        return redirect()->route('user.index')->with('success', 'Employee added successfully');
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
        return view('user.update', compact('user', 'userEducation', 'userWork', 'userFamily', 'duty'));
    }

    public function update(Request $request, $id)
    {
        //dd($request->all()); 
        // Validasi input


        $request->validate([
            'password' => 'nullable|min:8',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'id_card' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'cv' => 'nullable|mimes:pdf|max:2048',
            'employee_id' => 'required',
            'name' => 'required',
            'position' => 'required',
            'department' => 'required',
            'join_date' => 'required',
            'contract_date' => 'required_if:employee_status,Contract,Part Time|nullable|date',
            'email' => 'required|email',
            'phone_number' => 'required',
            'employee_status' => 'required',
            'user_status' => 'required',
            'bpjs_employment' => 'required',
            'bpjs_kesehatan' => 'required',
            'ID_number' => 'required',
            'birth_date' => 'required',
            'birth_place' => 'required|date',
            'ID_address' => 'required',
            'domicile_address' => 'required',
            'religion' => 'required',
            'gender' => 'required',
            'height' => 'required',
            'weight' => 'required',
            'blood_type' => 'required',



            // Data array yang tidak wajib diisi (nullable)
            'id_family' => 'nullable|array',
            'name_family' => 'nullable|array',
            'relation' => 'nullable|array',
            'birth_date_family' => 'nullable|array',
            'birth_place_family' => 'nullable|array',
            'ID_number_family' => 'nullable|array',
            'phone_number_family' => 'nullable|array',
            'address_family' => 'nullable|array',
            'gender_family' => 'nullable|array',
            'job' => 'nullable|array',

            // Pendidikan
            'id_education' => 'nullable|array',
            'education_level' => 'nullable|array',
            'education_place' => 'nullable|array',
            'major' => 'nullable|array',
            'start_education' => 'nullable|array',
            'end_education' => 'nullable|array',
            'grade' => 'nullable|array',

            // job
            'id_work' => 'nullable|array',
            'company_name' => 'nullable|array',
            'position_working' => 'nullable|array',
            'start_working' => 'nullable|array',
            'end_working' => 'nullable|array',
        ]);

        // Update password only if a new one is provided


        //dd('masuk validate');

        // Cari data pegawai berdasarkan ID
        $user = User::findOrFail($id);


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
                    if (array_key_exists($index, $request->id_family) && !is_null($request->id_family[$index])) {
                        // dd($request->id_family[$index]);
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

                    if (array_key_exists($index, $request->id_education) && !is_null($request->id_education[$index])) {
                        // Update data jika ID education ada
                        $id_used_education[] = $request->id_education[$index];

                        $userEducation = users_education::findOrFail($request->id_education[$index]);

                        // Cek apakah ada perubahan pada data
                        if (
                            $userEducation->degree !== $education_level ||
                            $userEducation->educational_place !== $request->education_place[$index] ||
                            $userEducation->mulai_edukasi !== $request->start_education[$index] ||
                            $userEducation->akhir_edukasi !== $request->end_education[$index] ||
                            $userEducation->major !== $request->major[$index] ||
                            $userEducation->grade !== $request->grade[$index]
                        ) {
                            $userEducation->update([
                                'degree' => $education_level,
                                'educational_place' => $request->education_place[$index],
                                'mulai_edukasi' => $request->start_education[$index],
                                'akhir_edukasi' => $request->end_education[$index],
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
                            'mulai_edukasi' => $request->start_education[$index],
                            'akhir_edukasi' => $request->end_education[$index],
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

                    if (array_key_exists($index, $request->id_work) && !is_null($request->id_work[$index])) {
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
                                'position' => $request->position[$index],
                                'start_working' => $request->start_work[$index],
                                'end_working' => $request->end_work[$index],
                                'updated_at' => now(),
                            ]);
                        }
                    } else {
                        // Create data baru jika ID work tidak ada
                        $newWork = users_work_experience::create([
                            'users_id' => $user->id,
                            'nama_perusahaan' => $company_name,
                            'position' => $request->position[$index],
                            'start_working' => $request->start_work[$index],
                            'end_working' => $request->end_work[$index],
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
        // dd($request->all());
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

        // Redirect ke halaman index atau halaman lain dengan pesan sukses
        return redirect()->route('home')->with('success', 'Employee Transfered successfully.');
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
