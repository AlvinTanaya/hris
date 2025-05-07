@extends('layouts.app')

@section('content')




<div class="container test">

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
                                            <a hidden id="deleteLessonAnswer" href="javascript:void(0);" class="btn btn-danger btn-sm delete-lesson" data-delete-lesson-id="{{ $item->id }}">
                                                <i class="fas fa-trash"></i> Erase Answers
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
                                            <a hidden id="deleteScheduleAnswer" href="javascript:void(0);" class="btn btn-danger btn-sm delete-schedule" data-delete-schedule-id="{{ $item->schedule_id }}">
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
                                    <!-- Data akan dimuat lewat AJAX -->
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
    /* Modern color scheme and styling */
    .test {
        --primary-color: #4361ee;
        --secondary-color: #3f37c9;
        --accent-color: #4895ef;
        --success-color: #4cc9f0;
        --warning-color: #f72585;
        --light-bg: #f8f9fa;
        --dark-bg: #212529;
    }

    body {
        background-color: #f5f7fa;
        font-family: 'Poppins', sans-serif;
    }

    .page-header {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        padding: 2rem 0;
        margin-bottom: 2rem;
        border-radius: 1rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .card {
        border: none;
        border-radius: 0.8rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08);
        transition: transform 0.3s ease;
        margin-bottom: 1.5rem;
    }

    .card:hover {
        transform: translateY(-5px);
    }

    .card-header {
        background: white;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        border-radius: 0.8rem 0.8rem 0 0 !important;
        padding: 1rem 1.5rem;
    }

    .table {
        border-collapse: separate;
        border-spacing: 0;
    }

    .table th {
        background-color: var(--dark-bg);
        color: white;
        font-weight: 500;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(67, 97, 238, 0.05);
    }

    /* Enhanced Tab Design */
    #educationTab {
        border-bottom: none;
        background-color: var(--dark-bg);
        border-radius: 0.8rem;
        padding: 0.3rem;
        margin-bottom: 2rem;
    }

    #educationTab .nav-link {
        color: rgba(255, 255, 255, 0.8);
        font-weight: 500;
        padding: 1rem 1.5rem;
        border-radius: 0.6rem;
        margin: 0.2rem;
        transition: all 0.3s ease;
    }

    #educationTab .nav-link:hover {
        color: white;
        background-color: rgba(255, 255, 255, 0.1);
    }

    #educationTab .nav-link.active {
        color: var(--dark-bg);
        background-color: white;
        border: none;
        box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
    }

    #educationTab .nav-link i {
        margin-right: 0.5rem;
    }

    /* Button enhancements */
    .btn {
        border-radius: 0.5rem;
        padding: 0.5rem 1rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background-color: var(--primary-color);
        border: none;
    }

    .btn-primary:hover {
        background-color: var(--secondary-color);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .btn-warning {
        background-color: #ff9e00;
        border: none;
        color: white;
    }

    .btn-danger {
        background-color: var(--warning-color);
        border: none;
    }

    .btn-info {
        background-color: var(--success-color);
        border: none;
        color: white;
    }

    .btn-sm {
        padding: 0.25rem 0.75rem;
        font-size: 0.875rem;
    }

    /* Form controls */
    .form-control,
    .form-select {
        border-radius: 0.5rem;
        padding: 0.5rem 0.75rem;
        border: 1px solid #dee2e6;
    }

    .form-control:focus,
    .form-select:focus {
        box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
        border-color: var(--primary-color);
    }

    /* Modal enhancements */
    .modal-content {
        border: none;
        border-radius: 1rem;
        overflow: hidden;
    }

    .modal-header {
        border-bottom: none;
        padding: 1.5rem;
    }

    .modal-body {
        padding: 1.5rem;
    }

    /* Custom Checkbox Styling */
    .form-check-input:checked {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    /* DataTables Styling */
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter,
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_processing,
    .dataTables_wrapper .dataTables_paginate {
        color: #666;
        padding: 0.5rem 0;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: var(--primary-color);
        color: white !important;
        border: none;
        border-radius: 0.25rem;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: var(--accent-color);
        color: white !important;
        border: none;
    }


    /* Add this to your existing CSS */
    .table-responsive {
        width: 100%;
    }

    #scheduleTable,
    #lessonTable {
        width: 100% !important;
        table-layout: fixed;
    }

    .dataTables_wrapper {
        width: 100% !important;
    }

    .table {
        width: 100% !important;
        border-collapse: separate;
        border-spacing: 0;
        margin-bottom: 1rem;
    }

    /* Fix for DataTables width issue */
    .dataTables_wrapper {
        width: 100% !important;
    }

    /* Fix DataTables specific width issues */
    .dataTables_wrapper .dataTables_scroll,
    .dataTables_wrapper .dataTables_scrollBody,
    .dataTables_wrapper .dataTables_scrollHeadInner {
        width: 100% !important;
    }

    .dataTables_wrapper .dataTables_scrollHeadInner table,
    .dataTables_wrapper .dataTables_scrollBody table {
        width: 100% !important;
    }

    /* Column width adjustments */
    #scheduleTable th:nth-child(1),
    #scheduleTable td:nth-child(1) {
        width: 25%;
    }

    #scheduleTable th:nth-child(2),
    #scheduleTable td:nth-child(2),
    #scheduleTable th:nth-child(3),
    #scheduleTable td:nth-child(3),
    #scheduleTable th:nth-child(4),
    #scheduleTable td:nth-child(4) {
        width: 15%;
    }

    #scheduleTable th:nth-child(5),
    #scheduleTable td:nth-child(5),
    #scheduleTable th:nth-child(6),
    #scheduleTable td:nth-child(6) {
        width: 15%;
    }

    /* Ensure column widths are respected */
    .table th,
    .table td {
        white-space: nowrap;
    }

    /* Make specific columns take proportional space */
    .table th:first-child,
    .table td:first-child {
        width: 25% !important;
    }

    .table th:last-child,
    .table td:last-child {
        width: 150px !important;
    }

    /* Ensure cards take full width */
    .card {
        width: 100%;
    }

    .card-body {
        padding: 1rem;
    }

    /* Ensure pagination controls stay within bounds */
    .dataTables_wrapper .dataTables_paginate {
        width: 100%;
        overflow-x: auto;
        white-space: nowrap;
    }

    /* Mobile responsiveness */
    @media (max-width: 768px) {

        .table th,
        .table td {
            min-width: 120px;
        }

        .table th:first-child,
        .table td:first-child {
            min-width: 200px;
        }
    }
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        // Simple initialization without too many complex options
        $('#scheduleTable').DataTable({
            responsive: false, // Turn off responsive to prevent column collapsing
            autoWidth: false, // Don't let DataTables calculate widths automatically
            scrollX: false, // Disable horizontal scrolling
            language: {
                search: "<i class='fas fa-search'></i> Search:",
                lengthMenu: "<i class='fas fa-list'></i> _MENU_ records per page",
                info: "Showing <strong>_START_</strong> to <strong>_END_</strong> of <strong>_TOTAL_</strong> entries",
                paginate: {
                    first: "<i class='fas fa-angle-double-left'></i>",
                    last: "<i class='fas fa-angle-double-right'></i>",
                    next: "<i class='fas fa-angle-right'></i>",
                    previous: "<i class='fas fa-angle-left'></i>"
                }
            }
        });

        // Same for lesson table
        $('#lessonTable').DataTable({
            responsive: false,
            autoWidth: false,
            scrollX: false,
            language: {
                search: "<i class='fas fa-search'></i> Search:",
                lengthMenu: "<i class='fas fa-list'></i> _MENU_ records per page",
                info: "Showing <strong>_START_</strong> to <strong>_END_</strong> of <strong>_TOTAL_</strong> entries",
                paginate: {
                    first: "<i class='fas fa-angle-double-left'></i>",
                    last: "<i class='fas fa-angle-double-right'></i>",
                    next: "<i class='fas fa-angle-right'></i>",
                    previous: "<i class='fas fa-angle-left'></i>"
                }
            }
        });

        // Force correct table width after initialization
        setTimeout(function() {
            $('.dataTables_scrollHeadInner, .dataTables_scrollHeadInner table').css('width', '100%');
            $('.table').css('width', '100%');
        }, 100);

        // Check if there's a hash in URL to activate the appropriate tab
        const hash = window.location.hash;
        if (hash === '#schedule') {
            $('#scheduleTab').tab('show');
        }

        // Update URL hash when tab changes
        $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
            window.location.hash = e.target.hash;
        });

        // Handle opening the modal for PDF
        $('.view-pdf-btn').click(function() {
            var pdfUrl = $(this).data('pdf-url');
            $('#pdfViewer').attr('src', pdfUrl);
            $('#pdfModal').modal('show');
        });

        // check lesson
        $('.check-lesson').each(function() {
            var button = $(this);
            var buttonErase = $("#deleteLessonAnswer");
            var lessonId = button.data('lesson-id');

            $.ajax({
                url: '/elearning/check-lesson/' + lessonId,
                type: 'GET',
                success: function(response) {
                    if (response.lessonExist) {
                        buttonErase.prop('hidden', false);

                        button.addClass('disabled');
                        button.addClass('btn-secondary').removeClass('btn-warning');
                    }
                },
                error: function() {
                    console.error('Gagal mengecek data');

                }
            });
        });

        // check lesson
        $('.check-schedule').each(function() {
            var button = $(this);
            var buttonErase = $("#deleteScheduleAnswer");
            var scheduleId = button.data('schedule-id');

            $.ajax({
                url: '/elearning/check-schedule/' + scheduleId,
                type: 'GET',
                success: function(response) {
                    if (response.scheduleExist) {
                        buttonErase.prop('hidden', false);
                        button.addClass('disabled');
                        button.addClass('btn-secondary').removeClass('btn-warning');
                    }
                },
                error: function() {
                    console.error('Gagal mengecek data');
                }
            });
        });

        // Handle tombol "View Questions"
        $('.view-questions-btn').click(function() {
            var lessonId = $(this).data('lesson-id'); // Ambil lesson ID
            $('#questionTableBody').html('<tr><td colspan="4" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>'); // Placeholder

            $.ajax({
                url: '/elearning/questions/' + lessonId,
                type: 'GET',
                success: function(response) {
                    var rows = '';
                    if (response.questions.length > 0) {
                        response.questions.forEach(function(question, index) {
                            var choicesHtml = '';
                            var choices = question.multiple_choice.split(';'); // Pisahkan pilihan
                            var answerKey = question.answer_key; // Ambil jawaban benar

                            choices.forEach(function(choice) {
                                var isChecked = (choice.trim() === answerKey.trim()) ? 'checked' : ''; // Tandai jawaban benar
                                var isCorrect = (choice.trim() === answerKey.trim()) ? 'text-success fw-bold' : ''; // Highlight correct answer

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
                    $('#questionModal').modal('show');
                },
                error: function() {
                    $('#questionTableBody').html('<tr><td colspan="4" class="text-center text-danger py-4"><i class="fas fa-exclamation-triangle fa-2x mb-2"></i><br>Failed to load questions</td></tr>');
                }
            });
        });

        // Handle tombol "View Invitation"
        $('.view-invitation-btn').click(function() {
            var scheduleId = $(this).data('schedule-id'); // Ambil ID schedule
            $('#invitationTableBody').html('<tr><td colspan="3" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>'); // Placeholder

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
                    $('#invitationModal').modal('show');
                },
                error: function() {
                    $('#invitationTableBody').html('<tr><td colspan="5" class="text-center text-danger">Failed to load invitations</td></tr>');
                }
            });
        });


        // Hapus Jawaban berdasarkan Lesson
        $('.delete-lesson').click(function() {
            var lessonId = $(this).data('delete-lesson-id');
            console.log(lessonId);
            Swal.fire({
                title: "Apakah Anda yakin?",
                text: "Apakah Anda yakin ingin menghapus semua jawaban peserta yang menggunakan lesson ini?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Ya, Hapus!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/elearning/delete_lesson_answer/' + lessonId,
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire("Dihapus!", "Semua jawaban peserta telah dihapus.", "success");
                            location.reload();
                        },
                        error: function() {
                            Swal.fire("Gagal!", "Terjadi kesalahan saat menghapus.", "error");
                        }
                    });
                }
            });
        });

        // Hapus Jawaban berdasarkan Schedule
        $('.delete-schedule').click(function() {
            var scheduleId = $(this).data('delete-schedule-id');

            Swal.fire({
                title: "Apakah Anda yakin?",
                text: "Apakah Anda yakin ingin menghapus semua jawaban peserta pada invitation ini?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Ya, Hapus!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/elearning/delete_schedule_answer/' + scheduleId,
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire("Dihapus!", "Semua jawaban peserta telah dihapus.", "success");
                            location.reload();
                        },
                        error: function() {
                            Swal.fire("Gagal!", "Terjadi kesalahan saat menghapus.", "error");
                        }
                    });
                }
            });
        });
    });
</script>
@endpush