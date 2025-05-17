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
use App\Http\Controllers\PayrollController;
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
        Route::put('/store', [UserController::class, 'employees_store'])->name('user.employees.store');
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


    //AHP Modified Recruitment 
    Route::get('/recruitment/weight_calculation/index', [RecruitmentController::class, 'weight_calculation_index'])->name('weight.calculation..index');
    Route::post('/recruitment/weight_calculation/weight_calculate', [RecruitmentController::class, 'weight_calculate'])->name('weight.calculate');




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

    Route::get('/time_management/request_shift/index', [TimeManagementController::class, 'indexShiftChange'])->name('change_shift.index');
    Route::post('/time_management/request_shift/approve/{id}', [TimeManagementController::class, 'approveShiftChange'])->name('change_shift.approve');
    Route::post('/time_management/request_shift/decline/{id}', [TimeManagementController::class, 'declineShiftChange'])->name('change_shift.decline');
    Route::get('/time_management/request_shift/{id}', [TimeManagementController::class, 'showShiftChange'])->name('change_shift.show');


    // Attendance
    Route::get('/time_management/employee_absent/attendance/index', [TimeManagementController::class, 'employee_absent_index'])->name('time.employee.absent.index');
    Route::get('/time_management/employee_absent/attendance/data', [TimeManagementController::class, 'getAttendanceData'])->name('attendance.data');
    Route::post('/time_management/employee_absent/attendance/import', [TimeManagementController::class, 'importAttendance'])->name('attendance.import');
    Route::get('/time_management/employee_absent/attendance/expected-hours', [TimeManagementController::class, 'getExpectedHours'])->name('attendance.expected_hours');
    Route::get('/time_management/employee_absent/attendance/{id}', [TimeManagementController::class, 'getAttendance'])->name('attendance.show');
    Route::post('/time_management/employee_absent/attendance', [TimeManagementController::class, 'storeAttendance'])->name('attendance.store');
    Route::put('/time_management/employee_absent/attendance', [TimeManagementController::class, 'updateAttendance'])->name('attendance.update');
    Route::delete('/time_management/employee_absent/attendance/{id}', [TimeManagementController::class, 'deleteAttendance'])->name('attendance.delete');
    Route::get('/time_management/employee_absent/employees', [TimeManagementController::class, 'getEmployees'])->name('attendance.employees');





    Route::get('/time_management/employee_absent/custom_holiday/index', [TimeManagementController::class, 'custom_holiday_index'])->name('time.custom.holiday.index');
    Route::get('/time_management/employee_absent/custom_holiday/create', [TimeManagementController::class, 'custom_holiday_create'])->name('time.custom.holiday.create');
    Route::post('/time_management/employee_absent/custom_holiday/store', [TimeManagementController::class, 'custom_holiday_store'])->name('time.custom.holiday.store');
    Route::get('/time_management/employee_absent/custom_holiday/edit/{id}', [TimeManagementController::class, 'custom_holiday_edit'])->name('time.custom.holiday.edit');
    Route::put('/time_management/employee_absent/custom_holiday/update/{id}', [TimeManagementController::class, 'custom_holiday_update'])->name('time.custom.holiday.update');
    Route::get('/time_management/employee_absent/custom_holiday/data', [TimeManagementController::class, 'getCustomHolidays'])->name('time.custom.holiday.data');
    Route::delete('/time_management/employee_absent/custom_holiday/delete/{id}', [TimeManagementController::class, 'custom_holiday_destroy'])->name('time.custom.holiday.destroy');
    Route::post('/time_management/employee_absent/custom_holiday/check-date', [TimeManagementController::class, 'checkDate'])->name('time.custom.holiday.check-date');
    Route::get('/api/custom-holidays', [TimeManagementController::class, 'getCustomHolidaysByYear']);
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
    Route::get('time_management/overtime/management/index', [TimeManagementController::class, 'overtime_index'])->name('overtime.index');
    Route::get('time_management/overtime/management/index2/{id}', [TimeManagementController::class, 'overtime_index2'])->name('overtime.index2');
    Route::get('time_management/overtime/management/create/{id}', [TimeManagementController::class, 'overtime_create'])->name('overtime.create');
    Route::post('time_management/overtime/management/store', [TimeManagementController::class, 'overtime_store'])->name('overtime.store');
    Route::post('time_management/overtime/management/check-overtime-eligibility', [TimeManagementController::class, 'checkEligibility']);
    Route::post('time_management/overtime/management/approve/{id}', [TimeManagementController::class, 'overtime_approve'])->name('overtime.approve');
    Route::post('time_management/overtime/management/decline/{id}', [TimeManagementController::class, 'overtime_decline'])->name('overtime.decline');
    Route::delete('time_management/overtime/management/{id}', [TimeManagementController::class, 'overtime_destroy'])->name('overtime.destroy');
    Route::post('/time_management/overtime/management/get-overtime-rate', [TimeManagementController::class, 'getOvertimeRate']);


    Route::get('time_management/overtime/report/index', [TimeManagementController::class, 'overtime_report_index'])->name('overtime.report.index');

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
    // Rule Performance Criteria routes
    Route::get('/evaluation/rule/performance/criteria/index', [EvaluationController::class, 'rule_performance_criteria_index'])->name('evaluation.rule.performance.criteria.index');
    Route::get('/evaluation/rule/performance/criteria/create', [EvaluationController::class, 'rule_performance_criteria_create'])->name('evaluation.rule.performance.criteria.create');
    Route::post('/evaluation/rule/performance/criteria/store', [EvaluationController::class, 'rule_performance_criteria_store'])->name('evaluation.rule.performance.criteria.store');
    Route::get('/evaluation/rule/performance/criteria/edit/{id}', [EvaluationController::class, 'rule_performance_criteria_edit'])->name('evaluation.rule.performance.criteria.edit');
    Route::put('/evaluation/rule/performance/criteria/update/{id}', [EvaluationController::class, 'rule_performance_criteria_update'])->name('evaluation.rule.performance.criteria.update');

    // Rule Performance Reduction routes
    Route::get('/evaluation/rule/performance/reduction/index', [EvaluationController::class, 'rule_performance_reduction_index'])->name('evaluation.rule.performance.reduction.index');
    Route::get('/evaluation/rule/performance/reduction/create', [EvaluationController::class, 'rule_performance_reduction_create'])->name('evaluation.rule.performance.reduction.create');
    Route::post('/evaluation/rule/performance/reduction/store', [EvaluationController::class, 'rule_performance_reduction_store'])->name('evaluation.rule.performance.reduction.store');
    Route::get('/evaluation/rule/performance/reduction/edit/{id}', [EvaluationController::class, 'rule_performance_reduction_edit'])->name('evaluation.rule.performance.reduction.edit');
    Route::put('/evaluation/rule/performance/reduction/update/{id}', [EvaluationController::class, 'rule_performance_reduction_update'])->name('evaluation.rule.performance.reduction.update');
    Route::post('/evaluation/rule/performance/reduction/check-type', [EvaluationController::class, 'checkTypeExists'])->name('evaluation.rule.performance.reduction.check.type');

    // Rule Performance Weight routes
    Route::get('/evaluation/rule/performance/weight/index', [EvaluationController::class, 'weight_performance_index'])->name('evaluation.rule.performance.weight.index');
    Route::get('/evaluation/rule/performance/weight/create', [EvaluationController::class, 'weight_performance_create'])->name('evaluation.rule.performance.weight.create');
    Route::post('/evaluation/rule/performance/weight/store', [EvaluationController::class, 'weight_performance_store'])->name('evaluation.rule.performance.weight.store');
    Route::get('/evaluation/rule/performance/weight/edit/{id}', [EvaluationController::class, 'weight_performance_edit'])->name('evaluation.rule.performance.weight.edit');
    Route::put('/evaluation/rule/performance/weight/update/{id}', [EvaluationController::class, 'weight_performance_update'])->name('evaluation.rule.performance.weight.update');

    // Rule Performance Grade routes
    Route::get('/evaluation/rule/performance/grade/index', [EvaluationController::class, 'grade_performance_index'])->name('evaluation.rule.performance.grade.index');
    Route::get('/evaluation/rule/performance/grade/create', [EvaluationController::class, 'grade_performance_create'])->name('evaluation.rule.performance.grade.create');
    Route::post('/evaluation/rule/performance/grade/store', [EvaluationController::class, 'grade_performance_store'])->name('evaluation.rule.performance.grade.store');
    Route::get('/evaluation/rule/performance/grade/edit/{id}', [EvaluationController::class, 'grade_performance_edit'])->name('evaluation.rule.performance.grade.edit');
    Route::put('/evaluation/rule/performance/grade/update/{id}', [EvaluationController::class, 'grade_performance_update'])->name('evaluation.rule.performance.grade.update');
    Route::delete('/evaluation/rule/performance/grade/destroy/{id}', [EvaluationController::class, 'grade_performance_destroy'])->name('evaluation.rule.performance.grade.destroy');
    Route::post('/evaluation/rule/performance/grade/check-overlap', [EvaluationController::class, 'checkPerformanceOverlap'])->name('evaluation.rule.performance.grade.check-overlap');

    // Rule Discipline Grade routes
    Route::get('/evaluation/rule/discipline/grade/index', [EvaluationController::class, 'rule_discipline_grade_index'])->name('evaluation.rule.discipline.grade.index');
    Route::get('/evaluation/rule/discipline/grade/create', [EvaluationController::class, 'rule_discipline_grade_create'])->name('evaluation.rule.discipline.grade.create');
    Route::post('/evaluation/rule/discipline/grade/store', [EvaluationController::class, 'rule_discipline_grade_store'])->name('evaluation.rule.discipline.grade.store');
    Route::get('/evaluation/rule/discipline/grade/edit/{id}', [EvaluationController::class, 'rule_discipline_grade_edit'])->name('evaluation.rule.discipline.grade.edit');
    Route::put('/evaluation/rule/discipline/grade/update/{id}', [EvaluationController::class, 'rule_discipline_grade_update'])->name('evaluation.rule.discipline.grade.update');
    Route::delete('/evaluation/rule/discipline/grade/destroy/{id}', [EvaluationController::class, 'grade_discipline_destroy'])->name('evaluation.rule.discipline.grade.destroy');
    Route::post('/evaluation/rule/discipline/grade/check-overlap', [EvaluationController::class, 'checkDisciplineOverlap'])->name('evaluation.rule.discipline.grade.check-overlap');

    // Rule E-learning Grade routes
    Route::get('/evaluation/rule/elearning/grade/index', [EvaluationController::class, 'rule_elearning_grade_index'])->name('evaluation.rule.elearning.grade.index');
    Route::get('/evaluation/rule/elearning/grade/create', [EvaluationController::class, 'rule_elearning_grade_create'])->name('evaluation.rule.elearning.grade.create');
    Route::post('/evaluation/rule/elearning/grade/store', [EvaluationController::class, 'rule_elearning_grade_store'])->name('evaluation.rule.elearning.grade.store');
    Route::get('/evaluation/rule/elearning/grade/edit/{id}', [EvaluationController::class, 'rule_elearning_grade_edit'])->name('evaluation.rule.elearning.grade.edit');
    Route::put('/evaluation/rule/elearning/grade/update/{id}', [EvaluationController::class, 'rule_elearning_grade_update'])->name('evaluation.rule.elearning.grade.update');
    Route::delete('/evaluation/rule/elearning/grade/destroy/{id}', [EvaluationController::class, 'grade_elearning_destroy'])->name('evaluation.rule.elearning.grade.destroy');
    Route::post('/evaluation/rule/elearning/grade/check-overlap', [EvaluationController::class, 'checkElearningOverlap'])->name('evaluation.rule.elearning.grade.check-overlap');




    // Rule Discipline Score routes
    Route::get('/evaluation/rule/discipline/score/index', [EvaluationController::class, 'rule_discipline_score_index'])->name('evaluation.rule.discipline.score.index');
    Route::get('/evaluation/rule/discipline/score/create', [EvaluationController::class, 'rule_discipline_score_create'])->name('evaluation.rule.discipline.score.create');
    Route::post('/evaluation/rule/discipline/score/store', [EvaluationController::class, 'rule_discipline_score_store'])->name('evaluation.rule.discipline.score.store');
    Route::get('/evaluation/rule/discipline/score/edit/{id}', [EvaluationController::class, 'rule_discipline_score_edit'])->name('evaluation.rule.discipline.score.edit');
    Route::put('/evaluation/rule/discipline/score/update/{id}', [EvaluationController::class, 'rule_discipline_score_update'])->name('evaluation.rule.discipline.score.update');
    Route::delete('/evaluation/rule/discipline/score/delete/{id}', [EvaluationController::class, 'rule_discipline_score_destroy'])->name('evaluation.rule.discipline.score.destroy');

    // Rule Final routes
    Route::get('/evaluation/rule/final/grade/salary/index', [EvaluationController::class, 'rule_grade_salary_index'])->name('evaluation.rule.grade.salary.index');
    Route::get('/evaluation/rule/final/grade/salary/create', [EvaluationController::class, 'rule_grade_salary_create'])->name('evaluation.rule.grade.salary.create');
    Route::post('/evaluation/rule/final/grade/salary/store', [EvaluationController::class, 'rule_grade_salary_store'])->name('evaluation.rule.grade.salary.store');
    Route::get('/evaluation/rule/final/grade/salary/edit/{id}', [EvaluationController::class, 'rule_grade_salary_edit'])->name('evaluation.rule.grade.salary.edit');
    Route::put('/evaluation/rule/final/grade/salary/update/{id}', [EvaluationController::class, 'rule_grade_salary_update'])->name('evaluation.rule.grade.salary.update');
    Route::delete('/evaluation/rule/final/grade/salary/delete/{id}', [EvaluationController::class, 'rule_grade_salary_destroy'])->name('evaluation.rule.grade.salary.destroy');
    Route::post('/evaluation/rule/final/grade/salary/check', [EvaluationController::class, 'rule_grade_salary_check'])
        ->name('evaluation.rule.grade.salary.check');





    // Assignment Performance routes
    Route::get('/evaluation/assign/performance/index/{id}', [EvaluationController::class, 'assign_performance_index'])->name('evaluation.assign.performance.index');
    Route::post('/evaluation/assign/performance/store', [EvaluationController::class, 'assign_performance_store'])->name('evaluation.assign.performance.store');
    Route::get('/evaluation/assign/performance/edit/{id}', [EvaluationController::class, 'assign_performance_edit'])->name('evaluation.assign.performance.edit');
    Route::put('/evaluation/assign/performance/update/{id}', [EvaluationController::class, 'assign_performance_update'])->name('evaluation.assign.performance.update');
    Route::get('/evaluation/assign/performance/create/{id}', [EvaluationController::class, 'assign_performance_create'])->name('evaluation.assign.performance.create');
    Route::get('/evaluation/check/existing', [EvaluationController::class, 'check_existing_evaluation'])->name('evaluation.check.existing');
    Route::get('/evaluation/get/criteria', [EvaluationController::class, 'get_performance_criteria'])->name('evaluation.get.criteria');
    Route::get('/evaluation/get/warning-letters', [EvaluationController::class, 'get_warning_letters'])->name('evaluation.get.warning.letters');
    Route::get('/evaluation/assign/performance/detail/{id}', [EvaluationController::class, 'assign_performance_detail'])->name('evaluation.assign.performance.detail');
    Route::get('/evaluation/assign/performance/filter', [EvaluationController::class, 'assign_performance_filter'])->name('evaluation.assign.performance.filter');

    // Evaluation Report
    Route::get('/evaluation/report/performance/index', [EvaluationController::class, 'report_performance_index'])->name('evaluation.report.performance.index');
    Route::get('/evaluation/report/performance/detail/{id}', [EvaluationController::class, 'report_performance_detail'])->name('evaluation.report.performance.detail');
    Route::get('/evaluation/report/performance/export', [EvaluationController::class, 'exportExcelAll'])->name('evaluation.report.performance.export.all');
    Route::get('/evaluation/report/performance/export/employee', [EvaluationController::class, 'exportEmployeeExcel'])->name('evaluation.report.performance.export.employee');

    Route::get('/evaluation/report/discipline/index', [EvaluationController::class, 'report_discipline_index'])->name('evaluation.report.discipline.index');
    Route::get('/evaluation/report/discipline/getDisciplineReportData', [EvaluationController::class, 'getDisciplineReportData'])->name('evaluation.report.discipline.data');
    Route::get('/evaluation/report/discipline/grades', [EvaluationController::class, 'getDisciplineGradeSettings'])->name('evaluation.report.discipline.grades');
    Route::get('/evaluation/report/discipline/export', [EvaluationController::class, 'exportDisciplineReport'])->name('evaluation.report.discipline.export');

    Route::get('/evaluation/report/elearning/index', [EvaluationController::class, 'report_elearning_index'])
        ->name('evaluation.report.elearning.index');
    Route::get('/evaluation/report/elearning/detail/{employee_id}/{year?}', [EvaluationController::class, 'report_elearning_detail'])
        ->name('evaluation.report.elearning.detail');
    Route::get('/evaluation/report/elearning/detail-answers/{invitation_id}', [EvaluationController::class, 'report_elearning_detail_answers'])
        ->name('evaluation.report.elearning.detail.answers');
    Route::get('/evaluation/report/elearning/index/export', [EvaluationController::class, 'report_elearning_export'])
        ->name('evaluation.report.elearning.export');


    Route::get('/evaluation/report/final/calculate/index', [EvaluationController::class, 'reportFinalCalculateIndex'])->name('evaluation.report.final.calculate.index');
    Route::post('/evaluation/report/final/calculate/get-data', [EvaluationController::class, 'getFinalCalculateReportData'])->name('evaluation.report.final.calculate.getData');
    Route::get('evaluation/report/final/calculate/export', [EvaluationController::class, 'finalCalculateExportToExcel'])->name('evaluation.report.final.calculate.export');
    Route::post('/evaluation/report/final/calculate/save', [EvaluationController::class, 'saveFinalCalculateResults'])->name('evaluation.report.final.calculate.save');

    // Evaluation report routes
    // API routes for select2
    Route::get('/api/employees', [EvaluationController::class, 'getEmployees'])->name('api.employees');
    Route::get('/evaluation/report/final/graph/years', [EvaluationController::class, 'getAvailableYears'])->name('evaluation.report.years');
    Route::get('/evaluation/report/final/graph/index', [EvaluationController::class, 'reportFinalGraphIndex'])->name('evaluation.report.final.graph.index');
    Route::get('/evaluation/report/final/graph/data', [EvaluationController::class, 'reportFinalGraphData'])->name('evaluation.report.final.graph.data');

    Route::get('/evaluation/report/final/result/index', [EvaluationController::class, 'reportFinalResultIndex'])
        ->name('evaluation.report.final.result.index');
    Route::put('/evaluation/report/final/result/update/{id}', [EvaluationController::class, 'reportFinalResultUpdate'])
        ->name('evaluation.report.final.result.update');
    Route::post('/evaluation/report/final/result/upload-proposal/{id}', [EvaluationController::class, 'reportFinalResultUploadProposal'])
        ->name('evaluation.report.final.result.upload-proposal');
    Route::post('/evaluation/report/final/result/mass-update', [EvaluationController::class, 'reportFinalResultMassUpdate'])
        ->name('evaluation.report.final.result.mass-update');
    Route::get('/evaluation/report/final/result/get-salary-value', [EvaluationController::class, 'reportFinalResultGetSalaryValue'])
        ->name('evaluation.report.final.result.get-salary-value');
    Route::post('/evaluation/report/final/result/save-all', [EvaluationController::class, 'reportFinalResultSaveAll'])
        ->name('evaluation.report.final.result.saveAll');


    Route::get('/payroll/master_salary/index', [PayrollController::class, 'masterSalaryIndex'])
        ->name('payroll.master.salary.index');
    Route::post('/payroll/master_salary/store', [PayrollController::class, 'masterSalaryStore'])
        ->name('payroll.master.salary.store');
    Route::put('/payroll/master_salary/update', [PayrollController::class, 'masterSalaryUpdate'])
        ->name('payroll.master.salary.update');
    Route::delete('/payroll/master_salary/destroy', [PayrollController::class, 'masterSalaryDestroy'])
        ->name('payroll.master.salary.destroy');

    Route::get('/payroll/salary_history/index', [PayrollController::class, 'salaryHistoryIndex'])
        ->name('payroll.salary_history.index');




    Route::prefix('payroll')->group(function () {
        // Employee Payroll Routes
        Route::get('/assign/index', [PayrollController::class, 'salaryAssignIndex'])->name('payroll.assign.index');
        Route::get('/assign/create', [PayrollController::class, 'salaryAssignCreate'])->name('payroll.assign.create');
        Route::post('/assign/store', [PayrollController::class, 'salaryAssignStore'])->name('payroll.assign.store');
        Route::get('/assign/edit/{id}', [PayrollController::class, 'salaryAssignEdit'])->name('payroll.assign.edit');
        Route::put('/assign/update/{id}', [PayrollController::class, 'salaryAssignUpdate'])->name('payroll.assign.update');
        Route::delete('/assign/delete/{id}', [PayrollController::class, 'salaryAssignDestroy'])->name('payroll.assign.destroy');
        Route::get('/assign/get-filtered-users', [PayrollController::class, 'getFilteredUsers'])->name('payroll.assign.get-filtered-users');

        Route::post('/assign/upload-attachment', [PayrollController::class, 'uploadAttachment'])->name('payroll.assign.upload-attachment');
        Route::post('/assign/mass-upload', [PayrollController::class, 'uploadMassAttachment'])->name('payroll.assign.mass-upload');
    });
});
