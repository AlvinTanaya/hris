<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\elearning_invitation;
use App\Models\elearning_question;
use App\Models\elearning_schedule;
use App\Models\elearning_lesson;
use App\Models\elearning_answer;
use App\Models\EmployeeDepartment;
use App\Models\EmployeePosition;
use App\Models\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Mail\ELearningInvitationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ElearningController extends Controller
{
    public function index2($id)
    {
        $query = DB::table('elearning_invitation')
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
                'elearning_lesson.passing_grade',
                'elearning_lesson.duration',
                'elearning_lesson.lesson_file',
                'elearning_schedule.start_date',
                'elearning_schedule.end_date'
            );

        // Duration filter
        if (request()->filled('duration_range')) {
            switch (request('duration_range')) {
                case '0-60':
                    $query->where('elearning_lesson.duration', '<=', 60);
                    break;
                case '61-120':
                    $query->whereBetween('elearning_lesson.duration', [61, 120]);
                    break;
                case '121-180':
                    $query->whereBetween('elearning_lesson.duration', [121, 180]);
                    break;
                case '181+':
                    $query->where('elearning_lesson.duration', '>', 180);
                    break;
            }
        }

        // Start date filter
        if (request()->filled('start_date')) {
            $query->whereDate('elearning_schedule.start_date', request('start_date'));
        }

        // End date filter
        if (request()->filled('end_date')) {
            $query->whereDate('elearning_schedule.end_date', request('end_date'));
        }

        $duty = $query->get();

        return view('elearning.index2', compact('duty'));
    }

    // public function index()
    // {
    //     $lesson = elearning_lesson::all();
    //     $schedule = elearning_schedule::all();

    //     $schedulesWithLessons = DB::table('elearning_schedule')

    //         ->join('elearning_lesson', 'elearning_schedule.lesson_id', '=', 'elearning_lesson.id')
    //         ->select(
    //             'elearning_lesson.id as lesson_id',
    //             'elearning_lesson.name as lesson_name',
    //             'elearning_lesson.duration',
    //             'elearning_lesson.lesson_file',
    //             'elearning_schedule.id as schedule_id',
    //             'elearning_schedule.start_date',
    //             'elearning_schedule.end_date',
    //         )
    //         ->get();
    //     // dd($schedulesWithLessons);


    //     return view('elearning.index', compact('lesson', 'schedule', 'schedulesWithLessons'));
    // }

    public function index(Request $request)
    {
        // Handle Lesson filters
        $lessonQuery = elearning_lesson::query();

        if ($request->filled('lesson_created_at')) {
            $lessonQuery->whereDate('created_at', $request->lesson_created_at);
        }

        if ($request->filled('duration_range')) {
            switch ($request->duration_range) {
                case '0-60':
                    $lessonQuery->where('duration', '<=', 60);
                    break;
                case '61-120':
                    $lessonQuery->whereBetween('duration', [61, 120]);
                    break;
                case '121-180':
                    $lessonQuery->whereBetween('duration', [121, 180]);
                    break;
                case '181+':
                    $lessonQuery->where('duration', '>', 180);
                    break;
            }
        }

        $lesson = $lessonQuery->get();

        // Handle Schedule filters
        $scheduleQuery = DB::table('elearning_schedule')
            ->join('elearning_lesson', 'elearning_schedule.lesson_id', '=', 'elearning_lesson.id')
            ->select(
                'elearning_lesson.id as lesson_id',
                'elearning_lesson.name as lesson_name',
                'elearning_lesson.duration',
                'elearning_lesson.lesson_file',
                'elearning_schedule.id as schedule_id',
                'elearning_schedule.created_at',
                'elearning_schedule.start_date',
                'elearning_schedule.end_date'
            );

        if ($request->filled('start_date')) {
            $scheduleQuery->whereDate('elearning_schedule.start_date', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $scheduleQuery->whereDate('elearning_schedule.end_date', $request->end_date);
        }

        if ($request->filled('schedule_created_at')) {
            $scheduleQuery->whereDate('elearning_schedule.created_at', $request->schedule_created_at);
        }

        $schedulesWithLessons = $scheduleQuery->get();

        // Get all schedules (if needed)
        $schedule = elearning_schedule::all();

        return view('elearning.index', compact('lesson', 'schedule', 'schedulesWithLessons'));
    }

    public function checkExistenceLessonInAnswer($lessonId)
    {

        $lessonExist = elearning_answer::where('lesson_id', $lessonId)->exists();
        return response()->json(['lessonExist' => $lessonExist]);
    }

    public function checkExistenceScheduleInAnswer($scheduleId)
    {
        $scheduleExist = elearning_answer::where('schedule_id', $scheduleId)->exists();
        return response()->json(['scheduleExist' => $scheduleExist]);
    }

    public function getInvitationEmployee($scheduleId)
    {
        //dd($scheduleId);

        $invitationEmployee = DB::table('elearning_invitation')
            ->join('users', 'elearning_invitation.users_id', '=', 'users.id')
            ->join('elearning_lesson', 'elearning_invitation.lesson_id', '=', 'elearning_lesson.id')
            ->join('elearning_schedule', 'elearning_invitation.schedule_id', '=', 'elearning_schedule.id')
            ->select(
                'elearning_invitation.id as invitation_id',
                'elearning_lesson.name as lesson_name',
                'users.name as users_name',
                'users.employee_id',
                'elearning_schedule.start_date',
                'elearning_schedule.end_date',
            )
            ->where('elearning_invitation.schedule_id', $scheduleId)
            ->orderBy('users.employee_id', 'asc')
            ->get();
        return response()->json(['invitationEmployee' => $invitationEmployee]);
    }

    public function getQuestions($lessonId)
    {
        //dd($lessonId);
        $questions = elearning_question::where('lesson_id', $lessonId)->get();

        return response()->json(['questions' => $questions]);
    }

    public function delete_lesson_answer($lessonId)
    {
        elearning_answer::where('lesson_id', $lessonId)->delete();
        elearning_invitation::where('lesson_id', $lessonId)->update(['grade' => null]);


        return response()->json(['success' => true, 'message' => 'Jawaban untuk lesson ini telah dihapus']);
    }

    public function delete_schedule_answer($scheduleId)
    {
        elearning_answer::where('schedule_id', $scheduleId)->delete();
        elearning_invitation::where('schedule_id', $scheduleId)->update(['grade' => null]);
        return response()->json(['success' => true, 'message' => 'Jawaban untuk schedule ini telah dihapus']);
    }




    public function create_lesson()
    {
        return view('elearning/create_lesson');
    }



    public function store_lesson(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'name' => 'required',
            'duration' => 'required',
            'passing_grade' => 'required',
            'lesson_file' => 'required',
            'questions.*.question' => 'required',
            'questions.*.choices' => 'required',
            'questions.*.answer_key' => 'required',
            'questions.*.grade' => 'required',
        ]);
        //dd('masuk');
        $file = $request->file('lesson_file');
        $originalName = $request->name;
        $modifiedName = str_replace(' ', '_', $originalName);
        $extension = $file->getClientOriginalExtension();
        $newFileName = $modifiedName . '.' . $extension;
        $filePath = $file->storeAs('elearning_lesson_material', $newFileName, 'public');

        // Save lesson
        $lesson = elearning_lesson::create([
            'name' => $request->name,
            'duration' => $request->duration,
            'passing_grade' => $request->passing_grade,
            'lesson_file' => $filePath,
        ]);

        // Save questions
        foreach ($request->questions as $q) {
            if (!isset($q['choices'][$q['answer_key']])) {
                return redirect()->back()->with('error', 'Invalid answer key selected.');
            }

            $choices = implode(';', $q['choices']);
            $answerKey = $q['choices'][$q['answer_key']];



            elearning_question::create([
                'lesson_id' => $lesson->id,
                'question' => $q['question'],
                'multiple_choice' => $choices,
                'answer_key' => $answerKey,
                'grade' => $q['grade'],

            ]);
        }

        return redirect()->route('elearning.index')->with('success', 'Lesson and questions added successfully!');
    }


    public function edit_lesson($id)
    {
        // Find the lesson by ID
        $lesson = elearning_lesson::findOrFail($id);

        // Retrieve all questions related to this lesson
        $questions = elearning_question::where('lesson_id', $id)->get();



        // Pass data to the edit view
        return view('elearning/update_lesson', compact('lesson', 'questions'));
    }

    public function update_lesson(Request $request, $id)
    {
        // Find the lesson to update
        $lesson = elearning_lesson::findOrFail($id);

        // Handle file update if a new file is uploaded
        if ($request->hasFile('lesson_file')) {
            // Delete the old file if a new file is uploaded
            if ($lesson->lesson_file && Storage::disk('public')->exists($lesson->lesson_file)) {
                Storage::disk('public')->delete($lesson->lesson_file);
            }

            // Rename and store new file
            $modifiedName = str_replace(' ', '', $request->name);
            $extension = $request->file('lesson_file')->getClientOriginalExtension();
            $newFileName = $modifiedName . '.' . $extension;
            $filePath = $request->file('lesson_file')->storeAs('elearning_lesson_material', $newFileName, 'public');

            $lesson->update([
                'lesson_file' => $filePath,
                'updated_at' => now(),
            ]);
        }

        // Update lesson details if they are different
        if ($lesson->name !== $request->name || $lesson->duration != $request->duration || $lesson->passing_grade != $request->passing_grade) {
            $lesson->update([
                'name' => $request->name,
                'duration' => $request->duration,
                'passing_grade' => $request->passing_grade,
                'updated_at' => now(),
            ]);
        }

        // Process questions
        $existingQuestions = elearning_question::where('lesson_id', $lesson->id)->get();
        $existingQuestionIds = $existingQuestions->pluck('id')->toArray();

        // Get IDs of questions to delete
        $deletedQuestionIds = $request->deleted_questions ?? [];

        // Process existing and new questions from the form
        $incomingQuestions = $request->questions ?? [];
        $processedQuestionIds = [];

        foreach ($incomingQuestions as $index => $q) {
            // Skip empty questions
            if (empty($q['question'])) continue;

            $choicesString = isset($q['choices']) ? implode(';', $q['choices']) : '';

            // Check if answer key is set
            if (!isset($q['answer_key']) || empty($q['answer_key'])) {
                // Default to first choice if not set
                $q['answer_key'] = $q['choices'][0] ?? '';
            }

            // Handle existing question (has ID)
            if (isset($q['id']) && in_array($q['id'], $existingQuestionIds)) {
                $existingQuestion = elearning_question::find($q['id']);
                if ($existingQuestion) {
                    $existingQuestion->update([
                        'question' => $q['question'],
                        'grade' => $q['grade'],
                        'multiple_choice' => $choicesString,
                        'answer_key' => $q['answer_key'],
                        'updated_at' => now(),
                    ]);
                    $processedQuestionIds[] = $existingQuestion->id;
                }
            } else {
                // Create new question
                $newQuestion = elearning_question::create([
                    'lesson_id' => $lesson->id,
                    'question' => $q['question'],
                    'grade' => $q['grade'],
                    'multiple_choice' => $choicesString,
                    'answer_key' => $q['answer_key'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $processedQuestionIds[] = $newQuestion->id;
            }
        }

        // Determine which questions to delete: explicitly deleted + not in form
        $questionsToDelete = array_merge(
            $deletedQuestionIds,
            array_diff($existingQuestionIds, $processedQuestionIds)
        );

        // Delete questions that have foreign key constraints
        if (!empty($questionsToDelete)) {
            // First, delete related records in the related tables
            // Assuming your constraint is with a table like elearning_answers, first delete those
            // You'll need to replace 'elearning_answers' and 'question_id' with your actual table and column names
            //DB::table('elearning_answers')->whereIn('question_id', $questionsToDelete)->delete();

            // Now it's safe to delete the questions
            elearning_question::whereIn('id', $questionsToDelete)->delete();
        }

        return redirect()->route('elearning.index')->with('success', 'Lesson updated successfully.');
    }



    public function create_schedule()
    {
        $lessons = elearning_lesson::all();
        $employees = User::with(['department', 'position'])
            ->where('employee_status', '!=', 'Inactive')
            ->get();

        // Mengambil daftar departemen unik dari pegawai yang aktif
        $departments = EmployeeDepartment::all();

        // Mengambil daftar posisi unik dari pegawai yang aktif
        $positions = EmployeePosition::all();

        return view('elearning.create_schedule', compact('lessons', 'employees', 'departments', 'positions'));
    }


    public function edit_schedule($id)
    {
        // Get the schedule by ID
        $schedule = elearning_schedule::findOrFail($id);

        // Get all lessons
        $lessons = elearning_lesson::all();
        $employees = User::where('employee_status', '!=', 'Inactive')->get();
        $employees = User::with(['department', 'position'])
            ->where('employee_status', '!=', 'Inactive')
            ->get();



        // Get the invitations related to the selected schedule's lesson
        $invitations = elearning_invitation::where('lesson_id', $schedule->lesson_id)->get();

        // Get the user IDs from the invitations
        $invitedUserIds = $invitations->pluck('users_id')->toArray();

        // Get the employees based on the invited user IDs
        $invitedEmployees = User::whereIn('id', $invitedUserIds)->get();

        // Get only the IDs of invited employees as an array
        $invitedEmployeesPluck = User::whereIn('id', $invitedUserIds)->pluck('id')->toArray();



        // Mengambil daftar departemen unik dari pegawai yang aktif
        $departments = EmployeeDepartment::all();

        // Mengambil daftar posisi unik dari pegawai yang aktif
        $positions = EmployeePosition::all();

        // Return the view with the required data
        return view('elearning/update_schedule', compact('schedule', 'invitedEmployeesPluck', 'lessons', 'invitedEmployees', 'invitedUserIds', 'employees', 'departments', 'positions'));
    }
    public function store_schedule(Request $request)
    {
        // Validate request
        $request->validate([
            'lesson' => 'required',
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
            'invited_employees' => 'nullable|string',
        ]);

        // Store schedule
        $schedule = elearning_schedule::create([
            'lesson_id' => $request->lesson_id,
            'start_date' => $request->startDate,
            'end_date' => $request->endDate,
        ]);

        // Store invitations and send notifications
        if ($request->invited_employees) {
            $employeeIds = explode(',', $request->invited_employees);
            $lesson = elearning_lesson::findOrFail($request->lesson_id);

            foreach ($employeeIds as $employeeId) {
                // Create invitation
                $invitation = elearning_invitation::create([
                    'schedule_id' => $schedule->id,
                    'lesson_id' => $schedule->lesson_id,
                    'users_id' => $employeeId,
                ]);

                // Send email to invited employee
                $user = User::findOrFail($employeeId);
                Mail::to($user->email)->send(new ELearningInvitationMail(
                    $user->name,
                    $lesson->name,
                    $schedule->start_date,
                    $schedule->end_date,
                    'new'
                ));

                // Create notification
                Notification::create([
                    'users_id' => $employeeId,
                    'message' => "You have been assigned to the e-learning course: {$lesson->name}",
                    'type' => 'elearning_duty',
                    'maker_id' => Auth::user()->id,
                    'status' => 'Unread'
                ]);
            }
        }

        return redirect()->route('elearning.index')->with('success', 'Schedule added successfully!');
    }

    public function update_schedule(Request $request, $id)
    {
        $schedule = elearning_schedule::findOrFail($id);

        // Update schedule fields only if changed
        if ($schedule->lesson_id != $request->lesson_id) {
            // Update invitation lesson_id
            elearning_invitation::where('schedule_id', $id)
                ->where('lesson_id', $schedule->lesson_id)
                ->update(['lesson_id' => $request->lesson_id]);

            $schedule->lesson_id = $request->lesson_id;
        }
        if ($schedule->start_date != $request->startDate) {
            $schedule->start_date = $request->startDate;
        }
        if ($schedule->end_date != $request->endDate) {
            $schedule->end_date = $request->endDate;
        }
        $schedule->updated_at = now();
        $schedule->save();

        // Get the invited employees from the input (array of user IDs)
        $inputInvitedEmployees = $request->invited_employees ?? '';
        $invitedEmployeesArray = !empty($inputInvitedEmployees) ? explode(',', $inputInvitedEmployees) : [];

        // Get the existing invited employees for the schedule
        $existingInvitedEmployees = elearning_invitation::where('schedule_id', $schedule->id)
            ->pluck('users_id')
            ->toArray();

        // Get the lesson details
        $lesson = elearning_lesson::findOrFail($schedule->lesson_id);

        // 1. Handle adding new invitations
        $newEmployees = array_diff($invitedEmployeesArray, $existingInvitedEmployees);
        foreach ($newEmployees as $userId) {
            // Create new invitation
            $invitation = elearning_invitation::create([
                'schedule_id' => $schedule->id,
                'lesson_id' => $schedule->lesson_id,
                'users_id' => $userId,
            ]);

            // Send email to new invited employee
            $user = User::findOrFail($userId);
            Mail::to($user->email)->send(new ELearningInvitationMail(
                $user->name,
                $lesson->name,
                $schedule->start_date,
                $schedule->end_date,
                'new'
            ));

            // Create notification for new invitation
            Notification::create([
                'users_id' => $userId,
                'message' => "You have been assigned to the updated e-learning course: {$lesson->name}",
                'type' => 'elearning_duty',
                'maker_id' => Auth::user()->id,
                'status' => 'Unread'
            ]);
        }

        // 2. Handle removing old invitations
        $removedEmployees = array_diff($existingInvitedEmployees, $invitedEmployeesArray);
        if (!empty($removedEmployees)) {
            foreach ($removedEmployees as $userId) {
                // Remove invitation
                elearning_invitation::where('schedule_id', $schedule->id)
                    ->where('users_id', $userId)
                    ->delete();

                // Send email about invitation removal
                $user = User::findOrFail($userId);
                Mail::to($user->email)->send(new ELearningInvitationMail(
                    $user->name,
                    $lesson->name,
                    $schedule->start_date,
                    $schedule->end_date,
                    'update'
                ));

                // Create notification for removed invitation
                Notification::create([
                    'users_id' => $userId,
                    'message' => "You have been removed from the e-learning course: {$lesson->name}",
                    'type' => 'elearning_duty',
                    'maker_id' => Auth::user()->id,
                    'status' => 'Unread'
                ]);
            }
        }

        return redirect()->route('elearning.index')->with('success', 'Schedule updated successfully!');
    }


    public function elearning_material($id)
    {
        $task = DB::table('elearning_invitation')
            ->join('elearning_lesson', 'elearning_invitation.lesson_id', '=', 'elearning_lesson.id')
            ->join('elearning_schedule', 'elearning_invitation.schedule_id', '=', 'elearning_schedule.id') // Correct the join to use schedule_id
            ->where('elearning_invitation.id', $id)
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
            ->first();
        //dd('masuk duty', $task);

        $question = elearning_question::where('lesson_id', $task->lesson_id)->get();

        //dd('masuk question', $question);
        return view('elearning/elearning_material', compact('task', 'question'));
    }

    public function elearning_quiz($id)
    {

        $task = DB::table('elearning_invitation')
            ->join('elearning_lesson', 'elearning_invitation.lesson_id', '=', 'elearning_lesson.id')
            ->join('elearning_schedule', 'elearning_invitation.schedule_id', '=', 'elearning_schedule.id')
            ->where('elearning_invitation.id', $id)
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
            ->first();

        if (!$task) {
            return redirect()->back()->with('error', 'Task not found.');
        }

        $questions = elearning_question::where('lesson_id', $task->lesson_id)->get();

        return view('elearning/elearning_quiz', compact('task', 'questions'));
    }

    public function elearning_store_quiz(Request $request, $taskId)
    {
        try {

            DB::beginTransaction();
            //dd($taskId);
            $elearning_invitation = DB::table('elearning_invitation')
                ->join('elearning_lesson', 'elearning_invitation.lesson_id', '=', 'elearning_lesson.id')
                ->join('elearning_schedule', 'elearning_invitation.schedule_id', '=', 'elearning_schedule.id')
                ->where('elearning_invitation.id', $taskId)
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
                ->first();


            if (!$elearning_invitation) {
                throw new \Exception('Data invitation tidak ditemukan');
            }

            $questions = elearning_question::where('lesson_id', $elearning_invitation->lesson_id)->get();

            if ($questions->isEmpty()) {
                throw new \Exception('Tidak ada pertanyaan untuk kuis ini');
            }

            $totalScore = 0;
            $answers = [];

            // Process each answer
            foreach ($questions as $question) {
                $answer = $request->input('answers.' . $question->id, null); // Default to null if not answered
                $isCorrect = $answer === $question->answer_key;
                $score = $isCorrect ? $question->grade : 0;
                $totalScore += $score;

                // Prepare answer data
                $answers[] = [
                    'invitation_id' => $elearning_invitation->invitation_id,
                    'lesson_id' => $elearning_invitation->lesson_id,
                    'schedule_id' => $elearning_invitation->schedule_id,
                    'users_id' => $request->user_id,
                    'question' => $question->question,
                    'multiple_choice' => $question->multiple_choice,
                    'answer_key' => $question->answer_key,
                    'answer' => $answer, // Can be null
                    'grade' => $question->grade,
                    'mark' => $score,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }


            try {
                elearning_answer::insert($answers);
                //dd('coba');
            } catch (\Exception $e) {
                dd($e->getMessage());
            }


            // Update invitation grade
            DB::table('elearning_invitation')
                ->where('id', $taskId)
                ->update([
                    'grade' => $totalScore,
                    'updated_at' => now()
                ]);

            DB::commit();



            // Redirect dengan parameter user_id
            return redirect()->route('elearning.index2', ['id' => $elearning_invitation->users_id])
                ->with('success', "Quiz berhasil diselesaikan! Total nilai Anda: $totalScore");
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }
}
