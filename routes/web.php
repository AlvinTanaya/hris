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
use App\Http\Controllers\EvaluationController;
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
Route::get('login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [App\Http\Controllers\Auth\LoginController::class, 'login']);



Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);
Route::get('/job_vacancy/index', [VacancyController::class, 'index'])->name('job_vacancy.index');
Route::get('/job_vacancy/create/{id}', [VacancyController::class, 'create'])->name('job_vacancy.create');
Route::post('/job_vacancy/store/{id}', [VacancyController::class, 'store'])->name('job_vacancy.store');



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

    // Employees Routes
    Route::prefix('user/employees')->group(function () {
        Route::get('/', [UserController::class, 'employees_index'])->name('user.employees.index');
        Route::get('/create', [UserController::class, 'employees_create'])->name('user.employees.create');
        Route::post('/store', [UserController::class, 'employees_store'])->name('user.employees.store');
        Route::get('/edit/{id}', [UserController::class, 'employees_edit'])->name('user.employees.edit');
        Route::put('/update/{id}', [UserController::class, 'employees_update'])->name('user.employees.update');
        Route::get('/transfer/{id}', [UserController::class, 'employees_transfer'])->name('user.employees.transfer');
        Route::get('/history/{id}', [UserController::class, 'employees_history'])->name('user.employees.history');
        Route::put('/transfer-user/{id}', [UserController::class, 'employees_transfer_user'])->name('user.employees.transfer_user');
        Route::post('/extend-date/{id}', [UserController::class, 'employees_extend_date'])->name('user.employees.extend');
        Route::post('/import', [UserController::class, 'employees_import'])->name('user.employees.import');
    });

    // Departments Routes
    Route::prefix('user/departments')->group(function () {
        Route::get('/', [UserController::class, 'departments_index'])->name('user.departments.index');
        Route::get('/create', [UserController::class, 'departments_create'])->name('user.departments.create');
        Route::post('/store', [UserController::class, 'departments_store'])->name('user.departments.store');
        Route::get('/edit/{id}', [UserController::class, 'departments_edit'])->name('user.departments.edit');
        Route::put('/update/{id}', [UserController::class, 'departments_update'])->name('user.departments.update');
        Route::delete('/delete/{id}', [UserController::class, 'departments_destroy'])->name('user.departments.destroy');
    });

    // Positions Routes
    Route::prefix('user/positions')->group(function () {
        Route::get('/', [UserController::class, 'positions_index'])->name('user.positions.index');
        Route::get('/create', [UserController::class, 'positions_create'])->name('user.positions.create');
        Route::post('/store', [UserController::class, 'positions_store'])->name('user.positions.store');
        Route::get('/edit/{id}', [UserController::class, 'positions_edit'])->name('user.positions.edit');
        Route::put('/update/{id}', [UserController::class, 'positions_update'])->name('user.positions.update');
        Route::delete('/delete/{id}', [UserController::class, 'positions_destroy'])->name('user.positions.destroy');
    });


    Route::get('/user/index', [UserController::class, 'index'])->name('user.index');
    Route::get('/user/create', [UserController::class, 'create'])->name('user.create');
    Route::put('/user/store', [UserController::class, 'store'])->name('user.store');
    Route::get('/user/edit/{id}', [UserController::class, 'edit'])->name('user.edit');
    Route::put('/user/update/{id}', [UserController::class, 'update'])->name('user.update');
    Route::get('/user/transfer/{id}', [UserController::class, 'transfer'])->name('user.transfer');
    Route::get('/user/history/{id}', [UserController::class, 'history'])->name('user.history');
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
    Route::post('/recruitment/labor_demand/revise/{id}', [RecruitmentController::class, 'revise_labor_demand'])->name('recruitment.labor.demand.revise');
    Route::put('/recruitment/labor_demand/update/{id}', [RecruitmentController::class, 'update_labor_demand'])->name('recruitment.labor.demand.update');
    Route::post('/recruitment/labor_demand/store', [RecruitmentController::class, 'store_labor_demand'])->name('recruitment.labor.demand.store');
    Route::get('/recruitment/labor_demand/{id}', [RecruitmentController::class, 'show_labor_demand'])->name('recruitment.labor.demand.show');
    Route::post('/recruitment/ahp-schedule-interview/{id}', [RecruitmentController::class, 'ahp_schedule_interview'])->name('recruitment.ahp_schedule_interview');



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
    // Rule Shift
    Route::get('/time_management/rule_shift/index', [TimeManagementController::class, 'rule_index'])->name('time.rule.index');
    Route::get('/time_management/rule_shift/create', [TimeManagementController::class, 'rule_create'])->name('time.rule.create');
    Route::post('/time_management/rule_shift/store', [TimeManagementController::class, 'rule_store'])->name('time.rule.store');
    Route::get('/time_management/rule_shift/edit/{id}', [TimeManagementController::class, 'rule_edit'])->name('time.rule.edit');
    Route::put('/time_management/rule_shift/update/{id}', [TimeManagementController::class, 'rule_update'])->name('time.rule.update');
    // Set Shift
    Route::get('/time_management/set_shift/index', [TimeManagementController::class, 'set_shift_index'])->name('time.set.shift.index');
    Route::get('/time_management/set_shift/create', [TimeManagementController::class, 'set_shift_create'])->name('time.set.shift.create');
    Route::post('/time_management/set_shift/store', [TimeManagementController::class, 'set_shift_store'])->name('time.set.shift.store');
    Route::put('/time_management/set_shift/update/{id}', [TimeManagementController::class, 'set_shift_update']);
    Route::delete('/time_management/set_shift/delete/{id}', [TimeManagementController::class, 'set_shift_destroy']);
    Route::post('/time_management/set_shift/exchange', [TimeManagementController::class, 'exchangeShifts'])->name('time.set.shift.exchange');
    Route::post('/time_management/set_shift/approve/{id}', [TimeManagementController::class, 'approveShiftChange'])->name('change_shift.approve');
    Route::post('/time_management/set_shift/decline/{id}', [TimeManagementController::class, 'declineShiftChange'])->name('change_shift.decline');
    // Attendance
    Route::get('/time_management/employee_absent/index', [TimeManagementController::class, 'employee_absent_index'])->name('time.employee.absent.index');
    Route::get('/time_management/employee_absent/attendance/data', [TimeManagementController::class, 'getAttendanceData'])->name('attendance.data');
    Route::post('/time_management/employee_absent/attendance/import', [TimeManagementController::class, 'importAttendance'])->name('attendance.import');
    // Warning Letter
    Route::get('/time_management/warning_letter/rule/index', [TimeManagementController::class, 'warning_letter_rule_index'])->name('warning.letter.rule.index');
    Route::get('/time_management/warning_letter/rule/create', [TimeManagementController::class, 'warning_letter_rule_create'])->name('warning.letter.rule.create');
    Route::post('/time_management/warning_letter/rule/store', [TimeManagementController::class, 'warning_letter_rule_store'])->name('warning.letter.rule.store');
    Route::get('/time_management/warning_letter/rule/edit/{id}', [TimeManagementController::class, 'warning_letter_rule_edit'])->name('warning.letter.rule.edit');
    Route::put('/time_management/warning_letter/rule/update/{id}', [TimeManagementController::class, 'warning_letter_rule_update'])->name('warning.letter.rule.update');

    Route::get('/time_management/warning_letter/assign/index', [TimeManagementController::class, 'warning_letter_index'])->name('warning.letter.index');
    Route::get('/time_management/warning_letter/assign/index2/{id}', [TimeManagementController::class, 'warning_letter_index2'])->name('warning.letter.index2');
    Route::get('/time_management/warning_letter/assign/create', [TimeManagementController::class, 'warning_letter_create'])->name('warning.letter.create');
    Route::post('/time_management/warning_letter/assign/store', [TimeManagementController::class, 'warning_letter_store'])->name('warning.letter.store');
    Route::get('/time_management/warning_letter/assign/edit/{id}', [TimeManagementController::class, 'warning_letter_edit'])->name('warning.letter.edit');
    Route::put('/time_management/warning_letter/assign/update/{id}', [TimeManagementController::class, 'warning_letter_update'])->name('warning.letter.update');
    Route::get('/warning-letter/get-available-types', [TimeManagementController::class, 'getAvailableWarningTypes'])->name('warning.letter.get-available-types');
    Route::get('/warning-letter/get-available-types-for-edit', [TimeManagementController::class, 'getAvailableWarningTypesForEdit'])->name('warning.letter.get-available-types-for-edit');

    // Resign
    Route::get('time_management/request_resign/index', [TimeManagementController::class, 'request_resign_index'])->name('request.resign.index');
    Route::get('time_management/request_resign/index2/{id}', [TimeManagementController::class, 'request_resign_index2'])->name('request.resign.index2');
    Route::get('time_management/request_resign/create/{id}', [TimeManagementController::class, 'request_resign_create'])->name('request.resign.create');
    Route::post('time_management/request_resign/store', [TimeManagementController::class, 'request_resign_store'])->name('request.resign.store');
    Route::get('time_management/request_resign/edit/{id}', [TimeManagementController::class, 'request_resign_edit'])->name('request.resign.edit');
    Route::put('time_management/request_resign/update/{id}', [TimeManagementController::class, 'request_resign_update'])->name('request.resign.update');
    Route::put('time_management/request_resign/approve/{id}', [TimeManagementController::class, 'request_resign_approve'])->name('request.resign.approve');
    Route::put('time_management/request_resign/decline/{id}', [TimeManagementController::class, 'request_resign_decline'])->name('request.resign.decline');
    Route::delete('time_management/request_resign/{id}', [TimeManagementController::class, 'request_resign_destroy'])->name('request.resign.destroy');
    // Change Shift
    Route::get('time_management/change_shift/index/{id}', [TimeManagementController::class, 'change_shift_index'])->name('change.shift.index');
    Route::get('time_management/change_shift/create/{id}', [TimeManagementController::class, 'change_shift_create'])->name('change.shift.create');
    Route::post('time_management/change_shift/store', [TimeManagementController::class, 'change_shift_store'])->name('change.shift.store');
    Route::get('time_management/change_shift/get-exchange-partners', [TimeManagementController::class, 'getExchangePartners'])->name('change.shift.get-exchange-partners');
    Route::get('time_management/change_shift/get-shift-preview', [TimeManagementController::class, 'getShiftPreview'])->name('change.shift.get-shift-preview');
    Route::delete('/time_management/change_shift/delete/{id}', [TimeManagementController::class, 'destroy_request'])->name('change.shift.destroy');
    Route::get('/time_management/change_shift/existing-requests', [TimeManagementController::class, 'getExistingRequests'])->name('change.shift.get-existing-requests');
    //Overtime
    Route::get('time_management/overtime/index', [TimeManagementController::class, 'overtime_index'])->name('overtime.index');
    Route::get('time_management/overtime/index2/{id}', [TimeManagementController::class, 'overtime_index2'])->name('overtime.index2');
    Route::get('time_management/overtime/create/{id}', [TimeManagementController::class, 'overtime_create'])->name('overtime.create');
    Route::post('time_management/overtime/store', [TimeManagementController::class, 'overtime_store'])->name('overtime.store');
    Route::post('time_management/overtime/check-overtime-eligibility', [TimeManagementController::class, 'checkEligibility']);
    Route::post('time_management/overtime/approve/{id}', [TimeManagementController::class, 'overtime_approve'])->name('overtime.approve');
    Route::post('time_management/overtime/decline/{id}', [TimeManagementController::class, 'overtime_decline'])->name('overtime.decline');
    Route::delete('time_management/overtime/{id}', [TimeManagementController::class, 'overtime_destroy'])->name('overtime.destroy');

    // Time Off Policy Routes
    Route::get('time_management/time_off/policy/index', [TimeManagementController::class, 'time_off_policy_index'])->name('time.off.policy.index');
    Route::get('time_management/time_off/policy/create', [TimeManagementController::class, 'time_off_policy_create'])->name('time.off.policy.create');
    Route::post('time_management/time_off/policy/store', [TimeManagementController::class, 'time_off_policy_store'])->name('time.off.policy.store');
    Route::get('time_management/time_off/policy/edit/{id}', [TimeManagementController::class, 'time_off_policy_edit'])->name('time.off.policy.edit');
    Route::put('time_management/time_off/policy/update/{id}', [TimeManagementController::class, 'time_off_policy_update'])->name('time.off.policy.update');


    // Time Off Assignment Routes
    Route::get('time_management/time_off/assign/index', [TimeManagementController::class, 'time_off_assign_index'])
        ->name('time.off.assign.index');
    Route::get('time_management/time_off/assign/create', [TimeManagementController::class, 'time_off_assign_create'])
        ->name('time.off.assign.create');
    Route::post('time_management/time_off/assign/store', [TimeManagementController::class, 'time_off_assign_store'])
        ->name('time.off.assign.store');
    Route::put('time_management/time_off/assign/update/{id}', [TimeManagementController::class, 'time_off_assign_update'])
        ->name('time.off.assign.update');
    Route::delete('time_management/time_off/assign/destroy/{id}', [TimeManagementController::class, 'time_off_assign_destroy'])
        ->name('time.off.assign.destroy');
    Route::get('time_management/time_off/assign/get-assigned-employees', [TimeManagementController::class, 'getAssignedEmployees'])
        ->name('time.off.get.assigned.employees');
    Route::get('time_management/time_off/assign/quota', [TimeManagementController::class,  'getTimeOffPolicyQuota'])
        ->name('time.off.get.policy.quota');

    // Time Off Request Routes
    Route::get('time_management/time_off/request_time_off/index', [TimeManagementController::class, 'request_time_off_index'])->name('request.time.off.index');
    Route::get('time_management/time_off/request_time_off/index2/{id}', [TimeManagementController::class, 'request_time_off_index2'])->name('request.time.off.index2');
    Route::get('time_management/time_off/request_time_off/create/{id}', [TimeManagementController::class, 'request_time_off_create'])->name('request.time.off.create');
    Route::post('time_management/time_off/request_time_off/store', [TimeManagementController::class, 'request_time_off_store'])->name('request.time.off.store');
    Route::post('time_management/time_off/request_time_off/approve/{id}', [TimeManagementController::class, 'request_time_off_approve'])->name('request.time.off.approve');
    Route::post('time_management/time_off/request_time_off/decline/{id}', [TimeManagementController::class, 'request_time_off_decline'])->name('request.time.off.decline');
    Route::delete('time_management/time_off/request_time_off/destroy/{id}', [TimeManagementController::class, 'request_time_off_destroy'])->name('request.time.off.destroy');
    Route::get('time_management/time_off/request_time_off/check-time-off-balance', [TimeManagementController::class, 'checkBalance']);
    Route::get('time_management/time_off/request_time_off/check-requires-time',  [TimeManagementController::class, 'checkRequiresTimeInput']);
    Route::get('time_management/time_off/request_time_off/get-employee-shift', [TimeManagementController::class, 'getEmployeeShift']);





    //Evaluation
    // Rule Performance
    Route::get('/evaluation/rule/performance/index', [EvaluationController::class, 'rule_performance_index'])->name('evaluation.rule.performance.index');
    Route::get('/evaluation/rule/performance/create', [EvaluationController::class, 'rule_performance_create'])->name('evaluation.rule.performance.create');
    Route::post('/evaluation/rule/performance/store', [EvaluationController::class, 'rule_performance_store'])->name('evaluation.rule.performance.store');
    Route::get('/evaluation/rule/performance/edit/{id}', [EvaluationController::class, 'rule_performance_edit'])->name('evaluation.rule.performance.edit');
    Route::put('/evaluation/rule/performance/update/{id}', [EvaluationController::class, 'rule_performance_update'])->name('evaluation.rule.performance.update');

    Route::get('/evaluation/assign/performance/index/{id}', [EvaluationController::class, 'assign_performance_index'])->name('evaluation.assign.performance.index');
    Route::get('/evaluation/assign/performance/create', [EvaluationController::class, 'assign_performance_create'])->name('evaluation.assign.performance.create');
    Route::post('/evaluation/assign/performance/store', [EvaluationController::class, 'assign_performance_store'])->name('evaluation.assign.performance.store');
    Route::get('/evaluation/assign/performance/edit/{id}', [EvaluationController::class, 'assign_performance_edit'])->name('evaluation.assign.performance.edit');
    Route::put('/evaluation/assign/performance/update/{id}', [EvaluationController::class, 'assign_performance_update'])->name('evaluation.assign.performance.update');
    Route::get('/evaluation/assign/performance/details', [EvaluationController::class, 'getEvaluationDetails'])->name('evaluation.assign.performance.details');
});
