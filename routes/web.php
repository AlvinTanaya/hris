<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ElearningController;
use App\Http\Controllers\TimeManagementController;
use App\Http\Controllers\RecruitmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VacancyController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AnnouncementController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Notifications\Notification;



// Routes that do not require authentication   
Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/home');
    }
    return view('welcome');
})->name('welcome');



Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);
Route::get('/job_vacancy/index', [VacancyController::class, 'index'])->name('job_vacancy.index');
Route::get('/job_vacancy/create/{id}', [VacancyController::class, 'create'])->name('job_vacancy.create');
Route::post('/job_vacancy/store/{id}', [VacancyController::class, 'store'])->name('job_vacancy.store');

Route::get('login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [App\Http\Controllers\Auth\LoginController::class, 'login']);


// Password Reset Routes
Route::get('/forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showForgotForm'])->name('password.forgot');

Route::post('/forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendOTP'])->name('password.otp.send');

Route::post('/forgot-password/resend', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'resendOTP'])->name('password.otp.resend');
Route::get('/verify-otp', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showVerifyOTPForm'])->name('otp.verify');
Route::post('/verify-otp', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'verifyOTP'])->name('otp.verify.submit');

Route::get('/reset-password', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset.form');
Route::post('/reset-password', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');



// Authentication routes  
Auth::routes(['reset' => false]);


// Routes that require authentication  
Route::middleware('auth')->group(function () {
    // Home page after login  
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

    // Employee routes  
    Route::get('/user/index', [UserController::class, 'index'])->name('user.index');
    Route::get('/user/create', [UserController::class, 'create'])->name('user.create');
    Route::post('/user/store', [UserController::class, 'store'])->name('user.store');
    Route::get('/user/edit/{id}', [UserController::class, 'edit'])->name('user.edit');
    Route::get('/user/transfer/{id}', [UserController::class, 'transfer'])->name('user.transfer');
    Route::get('/user/history/{id}', [UserController::class, 'history'])->name('user.history');
    Route::put('/user/update/{id}', [UserController::class, 'update'])->name('user.update');
    Route::put('/user/transfer_user/{id}', [UserController::class, 'transfer_user'])->name('user.transfer_user');
    Route::post('/user/extend-date/{id}', [UserController::class, 'extendDate'])->name('user.extend');
    Route::post('/employees/import', [UserController::class, 'import'])->name('employees.import');

    // E-learning routes  
    Route::get('/elearning/index', [ElearningController::class, 'index'])->name('elearning.index');
    Route::get('/elearning/index2/{id}', [ElearningController::class, 'index2'])->name('elearning.index2');
    Route::get('/elearning/create_lesson', [ElearningController::class, 'create_lesson'])->name('elearning.create_lesson');
    Route::post('/elearning/store_lesson', [ElearningController::class, 'store_lesson'])->name('elearning.store_lesson');
    Route::get('/elearning/edit_lesson/{id}', [ElearningController::class, 'edit_lesson'])->name('elearning.edit_lesson');
    Route::put('/elearning/update_lesson/{id}', [ElearningController::class, 'update_lesson'])->name('elearning.update_lesson');
    Route::get('/elearning/create_schedule', [ElearningController::class, 'create_schedule'])->name('elearning.create_schedule');
    Route::post('/elearning/store_schedule', [ElearningController::class, 'store_schedule'])->name('elearning.store_schedule');
    Route::get('/elearning/edit_schedule/{id}', [ElearningController::class, 'edit_schedule'])->name('elearning.edit_schedule');
    Route::put('/elearning/update_schedule/{id}', [ElearningController::class, 'update_schedule'])->name('elearning.update_schedule');
    Route::get('/elearning/elearning_material/{id}', [ElearningController::class, 'elearning_material'])->name('elearning.elearning_material');
    Route::get('/elearning/elearning_quiz/{id}', [ElearningController::class, 'elearning_quiz'])->name('elearning.elearning_quiz');
    Route::post('/elearning/elearning_store_quiz/{id}', [ElearningController::class, 'elearning_store_quiz'])->name('elearning.elearning_store_quiz');
    Route::get('/elearning/questions/{lessonId}', [ElearningController::class, 'getQuestions']);
    Route::get('/elearning/invitation/{scheduleId}', [ElearningController::class, 'getinvitationEmployee']);
    Route::get('/elearning/check-lesson/{lessonId}', [ElearningController::class, 'checkExistenceLessonInAnswer']);
    Route::get('/elearning/check-schedule/{scheduleId}', [ElearningController::class, 'checkExistenceScheduleInAnswer']);
    Route::post('/elearning/delete_lesson_answer/{id}', [ElearningController::class, 'delete_lesson_answer'])->name('elearning.delete_lesson_answer');
    Route::post('/elearning/delete_schedule_answer/{id}', [ElearningController::class, 'delete_schedule_answer'])->name('elearning.delete_schedule_answer');



    // Recruitment routes  
    // PTK
    Route::get('/recruitment/labor_demand/index', [RecruitmentController::class, 'index'])->name('recruitment.index');
    Route::get('/recruitment/labor_demand/create', [RecruitmentController::class, 'create_labor_demand'])->name('recruitment.labor.demand.create');
    Route::get('/recruitment/labor_demand/edit/{id}', [RecruitmentController::class, 'edit_labor_demand'])->name('recruitment.labor.demand.edit');
    Route::post('/recruitment/labor_demand/decline/{id}', [RecruitmentController::class, 'decline_labor_demand'])->name('recruitment.labor.demand.decline');
    Route::get('/recruitment/labor_demand/approve/{id}', [RecruitmentController::class, 'approve_labor_demand'])->name('recruitment.labor.demand.approve');
    Route::put('/recruitment/labor_demand/update/{id}', [RecruitmentController::class, 'update_labor_demand'])->name('recruitment.labor.demand.update');
    Route::post('/recruitment/labor_demand/store', [RecruitmentController::class, 'store_labor_demand'])->name('recruitment.labor.demand.store');
    Route::get('/recruitment/labor_demand/{id}', [RecruitmentController::class, 'show_labor_demand']);
    //AHP Recruitment 
    Route::get('/recruitment/ahp_recruitment/index', [RecruitmentController::class, 'index_ahp'])->name('recruitment.ahp');
    Route::post('/ahp/calculate', [RecruitmentController::class, 'calculate'])->name('ahp.calculate');
    Route::post('/ahp/calculate-weights', [RecruitmentController::class, 'calculateWeights']);
    Route::post('/ahp/calculate-rankings', [RecruitmentController::class, 'calculateRankings']);
    //Interview
    Route::get('/recruitment/interview/index', [RecruitmentController::class, 'index_interview'])->name('recruitment.index.interview');
    Route::get('/recruitment/interview/applicant/{id}', [RecruitmentController::class, 'applicant_list'])->name('recruitment.applicant');
    Route::get('/recruitment/applicant/show/{id}', [RecruitmentController::class, 'show_applicant'])->name('recruitment.applicant.show');
    Route::get('/recruitment/applicant/positions/{id}', [RecruitmentController::class, 'get_exchange']);


    Route::post('/recruitment/applicant/schedule/{id}', [RecruitmentController::class, 'schedule_interview'])->name('recruitment.applicant.schedule');
    Route::post('/recruitment/applicant/status/{id}', [RecruitmentController::class, 'update_status'])->name('recruitment.applicant.status');
    Route::post('/recruitment/applicant/exchange/{id}', [RecruitmentController::class, 'exchange_position'])->name('recruitment.applicant.exchange');
    Route::post('/recruitment/applicant/employee/{id}', [RecruitmentController::class, 'add_to_employee'])->name('applicant.add_to_employee');

    //notification
    Route::get('/notification/index', [NotificationController::class, 'index'])->name('notification.index');
    Route::post('/notification/mark-read/{id}', [NotificationController::class, 'markAsRead'])->name('notification.markAsRead');
    Route::post('/notification/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notification.markAllRead');

    //announcement
    Route::get('/announcement/index', [AnnouncementController::class, 'index'])->name('announcement.index');
    Route::get('/announcement/create', [AnnouncementController::class, 'create'])->name('announcement.create');
    Route::get('/announcement/users', [AnnouncementController::class, 'users'])->name('announcement.users');
    Route::post('/announcement/store', [AnnouncementController::class, 'store'])->name('announcement.store');;

    // Time Management Routes  
    Route::get('/work-shift', [TimeManagementController::class, 'workShift'])->name('time.work-shift');
    Route::get('/attendance', [TimeManagementController::class, 'attendance'])->name('time.attendance');
    Route::get('/leave', [TimeManagementController::class, 'leave'])->name('time.leave');
    Route::get('/overtime', [TimeManagementController::class, 'overtime'])->name('time.overtime');
    Route::get('/resignation', [TimeManagementController::class, 'resignation'])->name('time.resignation');
    Route::get('/warning-verbal', [TimeManagementController::class, 'warningVerbal'])->name('time.warning-verbal');
    Route::get('/warning-letter', [TimeManagementController::class, 'warningLetter'])->name('time.warning-letter');
});
