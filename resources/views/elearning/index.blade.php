@extends('layouts.app')

@section('content')
<div class="container elearning-container">

    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-auto">
                    <div class="bg-white p-3 rounded-circle shadow">
                        <i class="fas fa-graduation-cap fa-2x text-primary"></i>
                    </div>
                </div>
                <div class="col">
                    <h1 class="mb-0"><i class="fas fa-book me-2"></i> E-learning Portal</h1>
                    <p class="mb-0 text-white-50">Manage your lessons and schedules</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Tabs Navigation -->
    <ul class="nav nav-tabs d-flex w-100" id="educationTab" role="tablist">
        <li class="nav-item flex-grow-1 text-center" role="presentation">
            <a class="nav-link active" id="lessonTab" data-bs-toggle="tab" href="#lesson" role="tab" aria-controls="lesson" aria-selected="true">
                <i class="fas fa-book"></i> Lesson Management
            </a>
        </li>
        <li class="nav-item flex-grow-1 text-center" role="presentation">
            <a class="nav-link" id="scheduleTab" data-bs-toggle="tab" href="#schedule" role="tab" aria-controls="schedule" aria-selected="false">
                <i class="fas fa-calendar-alt"></i> Schedule Management
            </a>
        </li>
    </ul>

    <div class="tab-content mt-4">
        <!-- Lesson Section -->
        <div class="tab-pane fade show active" id="lesson" role="tabpanel">
            <!-- Lesson Filter -->
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex align-items-center">
                    <i class="fas fa-filter text-primary me-2 fa-lg"></i>
                    <h5 class="text-primary mb-0">Filter Lessons</h5>
                </div>
                <div class="card-body bg-light">
                    <form action="{{ route('elearning.index') }}" method="GET" class="row g-3">
                        <input type="hidden" name="tab" value="lesson">
                        <div class="col-md-6">
                            <label for="lesson_created_at" class="form-label fw-bold">
                                <i class="far fa-calendar-alt me-1"></i> Created Date
                            </label>
                            <input type="date" class="form-control shadow-sm" id="lesson_created_at" name="lesson_created_at" value="{{ request('lesson_created_at') }}">
                        </div>
                        <div class="col-md-6">
                            <label for="duration_range" class="form-label fw-bold">
                                <i class="far fa-clock me-1"></i> Duration Range
                            </label>
                            <select class="form-select shadow-sm" id="duration_range" name="duration_range">
                                <option value="">All Durations</option>
                                <option value="0-60" {{ request('duration_range') == '0-60' ? 'selected' : '' }}>0-60 minutes</option>
                                <option value="61-120" {{ request('duration_range') == '61-120' ? 'selected' : '' }}>1-2 hours</option>
                                <option value="121-180" {{ request('duration_range') == '121-180' ? 'selected' : '' }}>2-3 hours</option>
                                <option value="181+" {{ request('duration_range') == '181+' ? 'selected' : '' }}>3+ hours</option>
                            </select>
                        </div>
                        <div class="col-12 d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Apply Filters
                            </button>
                            <a href="{{ route('elearning.index') }}?tab=lesson" class="btn btn-secondary">
                                <i class="fas fa-undo me-2"></i>Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-book-reader text-primary me-2 fa-lg"></i>
                        <h5 class="text-primary d-inline mb-0">Lesson List</h5>
                    </div>
                    <a href="{{ route('elearning.create_lesson') }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-2"></i>Add Lesson
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="lessonTable" class="table table-bordered table-hover mb-3 pt-3">
                            <thead class="table-dark">
                                <tr>
                                    <th>Lesson Name</th>
                                    <th style="width: 17%">Duration (minutes)</th>
                                    <th style="width: 10%">Passing Grade</th>
                                    <th style="width: 10%">Created At</th>
                                    <th style="width: 10%">Material</th>
                                    <th style="width: 15%">Question</th>
                                    <th style="width: 21%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($lesson as $item)
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->duration }} minutes</td>
                                    <td>{{ $item->passing_grade }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary view-pdf-btn" data-pdf-url="{{ asset('storage/' . $item->lesson_file) }}">
                                            <i class="fas fa-file-alt"></i> View File
                                        </button>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-info btn-sm view-questions-btn" data-lesson-id="{{ $item->id }}">
                                            <i class="fas fa-question-circle"></i> View Questions
                                        </button>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('elearning.edit_lesson', $item->id) }}"
                                                class="btn btn-warning btn-sm check-lesson"
                                                data-lesson-id="{{ $item->id }}">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="javascript:void(0);" class="btn btn-danger btn-sm delete-lesson"
                                                data-delete-lesson-id="{{ $item->id }}" style="display: none;">
                                                <i class="fas fa-trash"></i> Erase
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal for Viewing PDF -->
            <div class="modal fade" id="pdfModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title"><i class="far fa-file-pdf me-2"></i> View PDF Material</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-0">
                            <embed id="pdfViewer" src="" type="application/pdf" width="100%" height="600px">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal for Viewing Questions -->
            <div class="modal fade" id="questionModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-info text-white">
                            <h5 class="modal-title"><i class="far fa-question-circle me-2"></i> View Questions</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="table-responsive">
                                <table id="questionTable" class="table table-bordered mb-3 pt-3">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>No</th>
                                            <th>Question</th>
                                            <th>Score</th>
                                            <th>Choices</th>
                                        </tr>
                                    </thead>
                                    <tbody id="questionTableBody"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Schedule Section -->
        <div class="tab-pane fade" id="schedule" role="tabpanel">
            <!-- Schedule Filter -->
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex align-items-center">
                    <i class="fas fa-filter text-primary me-2 fa-lg"></i>
                    <h5 class="text-primary mb-0">Filter Schedules</h5>
                </div>
                <div class="card-body bg-light">
                    <form action="{{ route('elearning.index') }}" method="GET" class="row g-3">
                        <input type="hidden" name="tab" value="schedule">
                        <div class="col-md-4">
                            <label for="start_date" class="form-label fw-bold">
                                <i class="far fa-calendar me-1"></i> Start Date
                            </label>
                            <input type="date" class="form-control shadow-sm" id="start_date" name="start_date" value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-4">
                            <label for="end_date" class="form-label fw-bold">
                                <i class="far fa-calendar-check me-1"></i> End Date
                            </label>
                            <input type="date" class="form-control shadow-sm" id="end_date" name="end_date" value="{{ request('end_date') }}">
                        </div>
                        <div class="col-md-4">
                            <label for="schedule_created_at" class="form-label fw-bold">
                                <i class="far fa-calendar-plus me-1"></i> Created Date
                            </label>
                            <input type="date" class="form-control shadow-sm" id="schedule_created_at" name="schedule_created_at" value="{{ request('schedule_created_at') }}">
                        </div>
                        <div class="col-12 d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Apply Filters
                            </button>
                            <a href="{{ route('elearning.index') }}?tab=schedule" class="btn btn-secondary">
                                <i class="fas fa-undo me-2"></i>Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Replace your table HTML with this version that has explicit width attributes -->
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-calendar-alt text-primary me-2 fa-lg"></i>
                        <h5 class="text-primary d-inline mb-0">Schedule List</h5>
                    </div>
                    <a href="{{ route('elearning.create_schedule') }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-2"></i>Add Schedule
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive" style="width: 100%;">
                        <table id="scheduleTable" class="table table-bordered table-hover mb-3 pt-3" width="100%" style="width: 100%;">
                            <thead class="table-dark">
                                <tr>
                                    <th style="width: 25%">Event Name</th>
                                    <th style="width: 15%">Date Start</th>
                                    <th style="width: 15%">Date End</th>
                                    <th style="width: 15%">Created at</th>
                                    <th style="width: 15%">Attendance</th>
                                    <th style="width: 15%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($schedulesWithLessons as $item)
                                <tr>
                                    <td>{{ $item->lesson_name }}</td>
                                    <td>{{ $item->start_date }}</td>
                                    <td>{{ $item->end_date }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}</td>
                                    <td>
                                        <button type="button" class="btn btn-info btn-sm view-invitation-btn" data-schedule-id="{{ $item->schedule_id }}">
                                            <i class="fas fa-users"></i> View Invitations
                                        </button>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('elearning.edit_schedule', $item->schedule_id) }}"
                                                class="btn btn-warning btn-sm check-schedule"
                                                data-schedule-id="{{ $item->schedule_id }}">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="javascript:void(0);" class="btn btn-danger btn-sm delete-schedule"
                                                data-delete-schedule-id="{{ $item->schedule_id }}" style="display: none;">
                                                <i class="fas fa-trash"></i> Erase
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal for Viewing Employee Invitations -->
        <div class="modal fade" id="invitationModal" tabindex="-1" aria-labelledby="invitationModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="invitationModalLabel">
                            <i class="fas fa-user-plus me-2"></i> Employee Invitations
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table id="invitationEmployeeTable" class="table table-bordered table-hover mb-3 pt-3">
                                <thead class="table-dark text-center">
                                    <tr>
                                        <th style="width: 10%;">No</th>
                                        <th style="width: 40%;">Employee ID</th>
                                        <th style="width: 40%;">Employee Name</th>
                                    </tr>
                                </thead>
                                <tbody id="invitationTableBody">
                                    <!-- Data will be loaded via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<style>
    /* resources/css/elearning.css */
    .elearning-container {
        --primary-color: #4361ee;
        --secondary-color: #3f37c9;
        --accent-color: #4895ef;
        --success-color: #4cc9f0;
        --warning-color: #f72585;
        --light-bg: #f8f9fa;
        --dark-bg: #212529;
    }

    .elearning-container body {
        background-color: #f5f7fa;
        font-family: 'Poppins', sans-serif;
    }

    .elearning-container .page-header {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        padding: 2rem 0;
        margin-bottom: 2rem;
        border-radius: 1rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .elearning-container .card {
        border: none;
        border-radius: 0.8rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08);
        transition: transform 0.3s ease;
        margin-bottom: 1.5rem;
    }

    .elearning-container .card:hover {
        transform: translateY(-5px);
    }

    .elearning-container .card-header {
        background: white;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        border-radius: 0.8rem 0.8rem 0 0 !important;
        padding: 1rem 1.5rem;
    }

    /* Enhanced Tab Design */
    .elearning-container #educationTab {
        border-bottom: none;
        background-color: var(--dark-bg);
        border-radius: 0.8rem;
        padding: 0.3rem;
        margin-bottom: 2rem;
    }

    .elearning-container #educationTab .nav-link {
        color: rgba(255, 255, 255, 0.8);
        font-weight: 500;
        padding: 1rem 1.5rem;
        border-radius: 0.6rem;
        margin: 0.2rem;
        transition: all 0.3s ease;
    }

    .elearning-container #educationTab .nav-link:hover {
        color: white;
        background-color: rgba(255, 255, 255, 0.1);
    }

    .elearning-container #educationTab .nav-link.active {
        color: var(--dark-bg);
        background-color: white;
        border: none;
        box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
    }

    .elearning-container #educationTab .nav-link i {
        margin-right: 0.5rem;
    }

    /* Button enhancements */
    .elearning-container .btn {
        border-radius: 0.5rem;
        padding: 0.5rem 1rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .elearning-container .btn-primary {
        background-color: var(--primary-color);
        border: none;
    }

    .elearning-container .btn-primary:hover {
        background-color: var(--secondary-color);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .elearning-container .btn-warning {
        background-color: #ff9e00;
        border: none;
        color: white;
    }

    .elearning-container .btn-danger {
        background-color: var(--warning-color);
        border: none;
    }

    .elearning-container .btn-info {
        background-color: var(--success-color);
        border: none;
        color: white;
    }

    .elearning-container .btn-sm {
        padding: 0.25rem 0.75rem;
        font-size: 0.875rem;
    }

    /* Form controls */
    .elearning-container .form-control,
    .elearning-container .form-select {
        border-radius: 0.5rem;
        padding: 0.5rem 0.75rem;
        border: 1px solid #dee2e6;
    }

    .elearning-container .form-control:focus,
    .elearning-container .form-select:focus {
        box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
        border-color: var(--primary-color);
    }

    /* Modal enhancements */
    .elearning-container .modal-content {
        border: none;
        border-radius: 1rem;
        overflow: hidden;
    }

    .elearning-container .modal-header {
        border-bottom: none;
        padding: 1.5rem;
    }

    .elearning-container .modal-body {
        padding: 1.5rem;
    }

    /* Custom Checkbox Styling */
    .elearning-container .form-check-input:checked {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    /* Button with icon enhancements */
    .elearning-container .btn i {
        margin-right: 0.25rem;
    }

    /* Ensure cards take full width */
    .elearning-container .card {
        width: 100%;
    }

    .elearning-container .card-body {
        padding: 1rem;
    }


    /* Add these CSS rules to ensure modals display properly */
    .modal {
        z-index: 1050 !important;
    }

    .modal-backdrop {
        z-index: 1040 !important;
    }

    /* Fix for mobile devices */
    @media (max-width: 768px) {
        .modal-dialog {
            margin: 0.5rem auto;
            max-width: 95%;
        }

        .modal-content {
            width: 100%;
        }
    }

    /* Additional styles to ensure buttons behave properly in responsive mode */
    /* Handle both desktop and responsive modes */
    .elearning-container .btn.disabled {
        pointer-events: none !important;
        opacity: 0.65;
        cursor: not-allowed;
    }

    /* Fix for responsive DataTables */
    .dtr-data .btn.disabled {
        pointer-events: none !important;
        opacity: 0.65;
        background-color: #6c757d !important;
        border-color: #6c757d !important;
        color: white !important;
    }

    /* DataTables responsive specific rules */
    .dtr-details {
        width: 100%;
    }

    .dtr-details .dtr-data {
        display: flex !important;
        justify-content: flex-start !important;
        gap: 5px !important;
        flex-wrap: wrap !important;
    }

    /* Ensure link behavior is overridden in responsive mode */
    .dtr-data a.btn.disabled,
    .dtr-data a.btn[disabled],
    .dtr-data a.btn[data-checked="true"] {
        pointer-events: none !important;
        background-color: #6c757d !important;
        border-color: #6c757d !important;
        color: #fff !important;
        opacity: 0.65 !important;
        text-decoration: none !important;
    }

    /* Fix for collapsed rows in responsive mode */
    li.child ul.dtr-details {
        display: flex !important;
        flex-direction: column !important;
        width: 100% !important;
    }

    li.disabled-action .dtr-data a.btn.check-lesson,
    li.disabled-action .dtr-data a.btn.check-schedule {
        pointer-events: none !important;
        background-color: #6c757d !important;
        border-color: #6c757d !important;
        color: #fff !important;
        opacity: 0.65 !important;
    }

    /* Ensure proper styling in responsive view */
    @media (max-width: 768px) {
        .dtr-data .d-flex {
            display: flex !important;
            gap: 5px !important;
        }

        /* Force pointer-events none on disabled buttons even in responsive mode */
        .btn[disabled],
        .btn.disabled {
            pointer-events: none !important;
            touch-action: none !important;
            cursor: default !important;
            opacity: 0.65 !important;
        }
    }

    /* Additional styles to ensure buttons behave properly in all modes */
    .btn.disabled,
    .btn[disabled],
    .btn[data-checked="true"] {
        pointer-events: none !important;
        cursor: not-allowed !important;
        opacity: 0.65 !important;
        background-color: #6c757d !important;
        border-color: #6c757d !important;
        color: #fff !important;
        text-decoration: none !important;
    }

    /* Fix specifically for responsive DataTables child rows */
    .dtr-data .btn.disabled,
    .dtr-data .btn[disabled],
    .dtr-data .btn[data-checked="true"],
    .dtr-data a.check-lesson[data-checked="true"],
    .dtr-data a.check-schedule[data-checked="true"] {
        pointer-events: none !important;
        cursor: not-allowed !important;
        opacity: 0.65 !important;
        background-color: #6c757d !important;
        border-color: #6c757d !important;
        color: #fff !important;
        text-decoration: none !important;
    }

    /* Fix for collapsed row containers */
    li.child ul.dtr-details {
        width: 100% !important;
    }

    /* Ensure responsive design works correctly */
    .dtr-data .d-flex {
        display: flex !important;
        gap: 5px !important;
    }

    /* Additional fix for disabled parent rows */
    tr.disabled-row td.dtr-control:before {
        color: #6c757d !important;
    }

    /* Fix for DataTables responsive view */
    table.dataTable.dtr-inline.collapsed>tbody>tr>td.dtr-control:before {
        background-color: var(--primary-color);
    }

    /* Adding stronger specificity for disabled buttons in responsive mode */
    .dtr-data a.btn.check-lesson[disabled],
    .dtr-data a.btn.check-schedule[disabled] {
        pointer-events: none !important;
        background-color: #6c757d !important;
        border-color: #6c757d !important;
        color: #fff !important;
        opacity: 0.65 !important;
    }

    /* Apply styles also to wrapper elements */
    .dtr-details li.disabled-action .dtr-data {
        position: relative;
    }

    /* For touch devices */
    @media (hover: none) and (pointer: coarse) {

        .dtr-data a.btn.disabled,
        .dtr-data a.btn[disabled],
        .dtr-data a.btn[data-checked="true"] {
            pointer-events: none !important;
            touch-action: none !important;
        }
    }
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // Global objects to store checked states
        window.checkedLessons = {};
        window.checkedSchedules = {};

        // Enhanced function to handle responsive DataTables child rows
        function handleResponsiveButtons() {
            // For DataTables responsive child rows
            $('.dtr-data a.btn').each(function() {
                var btn = $(this);
                var btnId;

                // Handle lesson buttons in responsive mode
                if (btn.hasClass('check-lesson')) {
                    btnId = btn.data('lesson-id');
                    if (window.checkedLessons && window.checkedLessons[btnId]) {
                        applyDisabledState(btn);
                        // Show erase button if original is shown
                        btn.siblings('.delete-lesson').show();
                    }
                }

                // Handle schedule buttons in responsive mode
                if (btn.hasClass('check-schedule')) {
                    btnId = btn.data('schedule-id');
                    if (window.checkedSchedules && window.checkedSchedules[btnId]) {
                        applyDisabledState(btn);
                        // Show erase button if original is shown
                        btn.siblings('.delete-schedule').show();
                    }
                }
            });
        }

        // Listen for DataTables responsive events and DOM changes
        $(document).on('responsive-display', function(e, datatable, row, showHide) {
            if (showHide) {
                // Small delay to ensure DOM is updated
                setTimeout(handleResponsiveButtons, 100);
            }
        });

        // Initialize modals with proper z-index
        var pdfModal = new bootstrap.Modal(document.getElementById('pdfModal'), {
            backdrop: 'static'
        });

        var questionModal = new bootstrap.Modal(document.getElementById('questionModal'), {
            backdrop: 'static'
        });

        var invitationModal = new bootstrap.Modal(document.getElementById('invitationModal'), {
            backdrop: 'static'
        });

        // Initialize DataTables with responsive features
        const lessonTable = $('#lessonTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "responsive": {
                details: {
                    display: $.fn.dataTable.Responsive.display.childRowImmediate,
                    type: 'none',
                    target: ''
                }
            },
            "lengthMenu": [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ],
            "language": {
                "search": "<i class='fas fa-search'></i> _INPUT_",
                "searchPlaceholder": "Search records...",
                "lengthMenu": "_MENU_ records per page",
                "info": "Showing _START_ to _END_ of _TOTAL_ records",
                "infoEmpty": "Showing 0 to 0 of 0 records",
                "infoFiltered": "(filtered from _MAX_ total records)"
            },
            "dom": "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            "drawCallback": function() {
                // Re-apply button state checks after table redraw
                checkLessonButtons();
                // Also handle responsive buttons
                handleResponsiveButtons();
            }
        });

        const scheduleTable = $('#scheduleTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "responsive": {
                details: {
                    display: $.fn.dataTable.Responsive.display.childRowImmediate,
                    type: 'none',
                    target: ''
                }
            },
            "lengthMenu": [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ],
            "language": {
                "search": "<i class='fas fa-search'></i> _INPUT_",
                "searchPlaceholder": "Search records...",
                "lengthMenu": "_MENU_ records per page",
                "info": "Showing _START_ to _END_ of _TOTAL_ records",
                "infoEmpty": "Showing 0 to 0 of 0 records",
                "infoFiltered": "(filtered from _MAX_ total records)"
            },
            "dom": "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            "drawCallback": function() {
                // Re-apply button state checks after table redraw
                checkScheduleButtons();
                // Also handle responsive buttons
                handleResponsiveButtons();
            }
        });

        // Ensure table width is correct on resize
        $(window).on('resize', function() {
            $('.dataTables_scrollHeadInner, .dataTables_scrollHeadInner table').css('width', '100%');
            $('.table').css('width', '100%');

            // Check table states after resize
            handleResponsiveButtons();
        });

        // Force correct table width after initialization
        setTimeout(function() {
            $('.dataTables_scrollHeadInner, .dataTables_scrollHeadInner table').css('width', '100%');
            $('.table').css('width', '100%');
            handleResponsiveButtons();
        }, 100);

        // Check if there's a hash in URL to activate the appropriate tab
        const hash = window.location.hash;
        if (hash === '#schedule') {
            $('#scheduleTab').tab('show');
        }

        // Update URL hash when tab changes
        $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
            window.location.hash = e.target.hash;

            // Force redraw of tables when switching tabs
            if (e.target.hash === '#schedule') {
                scheduleTable.draw(false);
            } else {
                lessonTable.draw(false);
            }

            // Also handle responsive buttons after tab switch
            setTimeout(handleResponsiveButtons, 100);
        });

        // Handle opening the modal for PDF
        $(document).on('click', '.view-pdf-btn', function() {
            var pdfUrl = $(this).data('pdf-url');
            $('#pdfViewer').attr('src', pdfUrl);
            pdfModal.show();
        });

        // Enhanced function to check lesson buttons
        function checkLessonButtons() {
            $('.check-lesson').each(function() {
                var button = $(this);
                var buttonErase = button.siblings('.delete-lesson');
                var lessonId = button.data('lesson-id');

                // If we already checked this lesson, apply the state immediately
                if (window.checkedLessons[lessonId] === true) {
                    applyDisabledState(button);
                    buttonErase.show();
                    return;
                }

                $.ajax({
                    url: '/elearning/check-lesson/' + lessonId,
                    type: 'GET',
                    success: function(response) {
                        if (response.lessonExist) {
                            // Store the checked state
                            window.checkedLessons[lessonId] = true;

                            // Apply the disabled state
                            applyDisabledState(button);
                            buttonErase.show();

                            // Also update any responsive mode buttons
                            updateResponsiveButtons('lesson', lessonId);
                        }
                    },
                    error: function() {
                        console.error('Failed to check lesson data');
                    }
                });
            });
        }

        // Enhanced function to apply disabled state to buttons
        function applyDisabledState(button) {
            // Apply both class and attribute changes
            button.addClass('disabled btn-secondary').removeClass('btn-warning');
            button.attr({
                'disabled': 'disabled',
                'aria-disabled': 'true',
                'data-checked': 'true'
            });

            // Add pointer-events none with !important to override any other styles
            button.css({
                'pointer-events': 'none !important',
                'cursor': 'not-allowed !important',
                'opacity': '0.65 !important'
            });

            // Apply inline style with !important to ensure it can't be clicked
            button.attr('style', button.attr('style') + '; pointer-events: none !important; cursor: not-allowed !important; opacity: 0.65 !important;');

            // For responsive mode, find parent and mark it
            if (button.closest('li.dtr-control').length) {
                button.closest('li.dtr-control').addClass('disabled-action');
            }
        }

        // Function to update buttons in responsive mode
        function updateResponsiveButtons(type, id) {
            if (type === 'lesson') {
                $('.dtr-data a.check-lesson[data-lesson-id="' + id + '"]').each(function() {
                    applyDisabledState($(this));
                    // Find and show erase button in responsive view
                    $(this).siblings('.delete-lesson').show();
                });
            } else if (type === 'schedule') {
                $('.dtr-data a.check-schedule[data-schedule-id="' + id + '"]').each(function() {
                    applyDisabledState($(this));
                    // Find and show erase button in responsive view
                    $(this).siblings('.delete-schedule').show();
                });
            }
        }

        // Enhanced function to check schedule buttons
        function checkScheduleButtons() {
            $('.check-schedule').each(function() {
                var button = $(this);
                var buttonErase = button.siblings('.delete-schedule');
                var scheduleId = button.data('schedule-id');

                // If we already checked this schedule, apply the state immediately
                if (window.checkedSchedules[scheduleId] === true) {
                    applyDisabledState(button);
                    buttonErase.show();
                    return;
                }

                $.ajax({
                    url: '/elearning/check-schedule/' + scheduleId,
                    type: 'GET',
                    success: function(response) {
                        if (response.scheduleExist) {
                            // Store the checked state
                            window.checkedSchedules[scheduleId] = true;

                            // Apply the disabled state
                            applyDisabledState(button);
                            buttonErase.show();

                            // Also update any responsive mode buttons
                            updateResponsiveButtons('schedule', scheduleId);
                        }
                    },
                    error: function() {
                        console.error('Failed to check schedule data');
                    }
                });
            });
        }

        // Initial button checks
        checkLessonButtons();
        checkScheduleButtons();

        // Force responsive buttons check after a short delay 
        // to ensure responsive view is fully rendered
        setTimeout(handleResponsiveButtons, 500);

        // Handle "View Questions" button
        $(document).on('click', '.view-questions-btn', function() {
            var lessonId = $(this).data('lesson-id');
            $('#questionTableBody').html('<tr><td colspan="4" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>');

            $.ajax({
                url: '/elearning/questions/' + lessonId,
                type: 'GET',
                success: function(response) {
                    var rows = '';
                    if (response.questions.length > 0) {
                        response.questions.forEach(function(question, index) {
                            var choicesHtml = '';
                            var choices = question.multiple_choice.split(';');
                            var answerKey = question.answer_key;

                            choices.forEach(function(choice) {
                                var isChecked = (choice.trim() === answerKey.trim()) ? 'checked' : '';
                                var isCorrect = (choice.trim() === answerKey.trim()) ? 'text-success fw-bold' : '';

                                choicesHtml += `
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="question_${question.id}" ${isChecked} disabled>
                                    <label class="form-check-label ${isCorrect}">${choice}</label>
                                </div>
                            `;
                            });

                            rows += `
                            <tr>
                                <td class="text-center align-middle">${index + 1}</td>
                                <td class="align-middle">${question.question}</td>
                                <td class="text-center align-middle">${question.grade}</td>
                                <td>${choicesHtml}</td>
                            </tr>
                        `;
                        });
                    } else {
                        rows = '<tr><td colspan="4" class="text-center text-muted py-4"><i class="far fa-question-circle fa-2x mb-2"></i><br>No questions available</td></tr>';
                    }
                    $('#questionTableBody').html(rows);
                    questionModal.show();
                },
                error: function() {
                    $('#questionTableBody').html('<tr><td colspan="4" class="text-center text-danger py-4"><i class="fas fa-exclamation-triangle fa-2x mb-2"></i><br>Failed to load questions</td></tr>');
                }
            });
        });

        // Handle "View Invitation" button
        $(document).on('click', '.view-invitation-btn', function() {
            var scheduleId = $(this).data('schedule-id');
            $('#invitationTableBody').html('<tr><td colspan="3" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>');

            $.ajax({
                url: '/elearning/invitation/' + scheduleId,
                type: 'GET',
                success: function(response) {
                    var rows = '';
                    if (response.invitationEmployee.length > 0) {
                        response.invitationEmployee.forEach(function(invite, index) {
                            rows += `
                            <tr>
                                <td class="text-center align-middle">${index + 1}</td>
                                <td class="align-middle text-center">${invite.employee_id}</td>
                                <td class="align-middle text-center">${invite.users_name}</td>
                            </tr>
                        `;
                        });
                    } else {
                        rows = '<tr><td colspan="3" class="text-center text-muted py-4"><i class="far fa-user-circle fa-2x mb-2"></i><br>No invitations available</td></tr>';
                    }
                    $('#invitationTableBody').html(rows);
                    invitationModal.show();
                },
                error: function() {
                    $('#invitationTableBody').html('<tr><td colspan="3" class="text-center text-danger">Failed to load invitations</td></tr>');
                }
            });
        });

        // Delete answers by Lesson
        $(document).on('click', '.delete-lesson', function() {
            var lessonId = $(this).data('delete-lesson-id');

            Swal.fire({
                title: "Are you sure?",
                text: "Are you sure you want to delete all participant answers using this lesson?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, Delete!",
                cancelButtonText: "Cancel"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/elearning/delete_lesson_answer/' + lessonId,
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            Swal.fire("Deleted!", "All participant answers have been deleted.", "success");
                            location.reload();
                        },
                        error: function() {
                            Swal.fire("Failed!", "An error occurred while deleting.", "error");
                        }
                    });
                }
            });
        });

        // Delete answers by Schedule
        $(document).on('click', '.delete-schedule', function() {
            var scheduleId = $(this).data('delete-schedule-id');

            Swal.fire({
                title: "Are you sure?",
                text: "Are you sure you want to delete all participant answers for this invitation?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, Delete!",
                cancelButtonText: "Cancel"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/elearning/delete_schedule_answer/' + scheduleId,
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            Swal.fire("Deleted!", "All participant answers have been deleted.", "success");
                            location.reload();
                        },
                        error: function() {
                            Swal.fire("Failed!", "An error occurred while deleting.", "error");
                        }
                    });
                }
            });
        });

        // Add additional CSS to force disabled styles
        const styleElement = document.createElement('style');
        styleElement.textContent = `
        .btn.disabled, 
        .btn[disabled], 
        .btn[data-checked="true"] {
            pointer-events: none !important;
            cursor: not-allowed !important;
            opacity: 0.65 !important;
            background-color: #6c757d !important;
            border-color: #6c757d !important;
            color: #fff !important;
        }
        
        /* Force styles in responsive mode */
        .dtr-data .btn.disabled,
        .dtr-data .btn[disabled],
        .dtr-data .btn[data-checked="true"] {
            pointer-events: none !important;
            cursor: not-allowed !important;
            opacity: 0.65 !important;
            background-color: #6c757d !important;
            border-color: #6c757d !important;
            color: #fff !important;
            text-decoration: none !important;
        }
    `;
        document.head.appendChild(styleElement);
    });
</script>
@endpush