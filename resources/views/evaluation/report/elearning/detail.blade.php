@extends('layouts.app')

@section('content')
<style>
    .theme-custom {
        --primary-color: #4361ee;
        --secondary-color: #3f37c9;
        --accent-color: #4cc9f0;
        --success-color: #4ade80;
        --warning-color: #fbbf24;
        --danger-color: #f87171;
        --light-color: #f8f9fa;
        --dark-color: #212529;
        --text-muted: #6c757d;
    }




    body {
        background-color: #f5f7fb;
    }

    .card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        background-color: white;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        padding: 1.25rem 1.5rem;
        font-weight: 600;
        color: var(--dark-color);
    }

    .card-body {
        padding: 1.5rem;
    }

    .nav-tabs {
        border-bottom: 2px solid rgba(0, 0, 0, 0.05);
    }

    .nav-tabs .nav-link {
        border: none;
        color: var(--text-muted);
        font-weight: 500;
        padding: 0.75rem 1.25rem;
        border-radius: 8px 8px 0 0;
        margin-right: 5px;
        transition: all 0.3s ease;
        white-space: nowrap;
    }

    .nav-tabs .nav-link:hover {
        color: var(--primary-color);
        background-color: rgba(67, 97, 238, 0.1);
    }

    .nav-tabs .nav-link.active {
        color: var(--primary-color);
        background-color: transparent;
        border-bottom: 3px solid var(--primary-color);
        font-weight: 600;
    }

    .badge {
        font-weight: 500;
        padding: 0.5em 0.75em;
        border-radius: 8px;
    }

    .badge-lg {
        font-size: 1rem;
        padding: 0.6em 1em;
    }

    .page-header {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        color: white;
        overflow: hidden;
        position: relative;
        z-index: 1;
        /* Ensure proper stacking context */
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 100%;
        height: 200%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 70%);
    }

    .page-title {
        position: relative;
        font-weight: 700;
        margin-bottom: 0;
    }

    .back-btn {
        position: absolute;
        left: 1.5rem;
        top: 50%;
        transform: translateY(-50%);
        background-color: rgba(255, 255, 255, 0.2);
        border: none;
        border-radius: 8px;
        padding: 0.5rem 1rem;
        color: white;
        transition: all 0.3s ease;
        z-index: 10;
        /* Ensure button is clickable */
        display: inline-block;
        /* Ensure it's properly rendered as a block element */
        text-decoration: none;
        /* Remove underline */
        cursor: pointer;
        /* Add pointer cursor */
    }


    .back-btn:hover {
        background-color: rgba(255, 255, 255, 0.4);
        text-decoration: none;
        color: white;
    }

    /* Make sure the button text is visible */
    .back-btn i {
        margin-right: 5px;
    }

    .info-icon {
        font-size: 1.25rem;
        margin-right: 0.75rem;
        color: var(--primary-color);
    }

    .employee-info-item {
        margin-bottom: 1rem;
    }

    .employee-info-label {
        font-size: 0.8rem;
        color: var(--text-muted);
        font-weight: 500;
        margin-bottom: 0.25rem;
    }

    .employee-info-value {
        font-weight: 600;
        color: var(--dark-color);
    }

    .score-card {
        height: 100%;
        display: flex;
        flex-direction: column;
        border-left: 4px solid var(--primary-color);
    }

    .score-card .card-body {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .score-value {
        font-size: 2rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 0.5rem;
    }

    .score-description {
        color: var(--text-muted);
        font-size: 0.9rem;
    }

    .table-responsive {
        border-radius: 10px;
        overflow: hidden;
    }

    .table {
        margin-bottom: 0;
    }

    .table thead th {
        background-color: var(--primary-color);
        color: white;
        border: none;
        padding: 1rem;
        font-weight: 500;
    }

    .table tbody td {
        padding: 1rem;
        vertical-align: middle;
        border-top: 1px solid rgba(0, 0, 0, 0.03);
    }

    .table-hover tbody tr:hover {
        background-color: rgba(67, 97, 238, 0.05);
    }

    .status-badge {
        padding: 0.5em 0.75em;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
    }

    .btn-detail {
        background-color: var(--primary-color);
        color: white;
        border-radius: 8px;
        padding: 0.5rem 1rem;
        font-size: 0.85rem;
        transition: all 0.3s ease;
    }

    .btn-detail:hover {
        background-color: var(--secondary-color);
        color: white;
        transform: translateY(-2px);
    }

    .btn-export {
        background-color: var(--success-color);
        color: white;
        border-radius: 8px;
        padding: 0.75rem 1.5rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-export:hover {
        background-color: #22c55e;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(74, 222, 128, 0.3);
    }

    .summary-card {
        border-left: 4px solid;
        height: 100%;
    }

    .summary-card .card-body {
        padding: 1.5rem;
    }

    .summary-title {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: var(--text-muted);
        margin-bottom: 0.5rem;
    }

    .summary-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--dark-color);
    }

    .summary-icon {
        font-size: 2rem;
        color: rgba(0, 0, 0, 0.1);
    }

    .modal-content {
        border: none;
        border-radius: 10px;
        overflow: hidden;
    }

    .modal-header {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        border-bottom: none;
        padding: 1.5rem;
    }

    .modal-title {
        font-weight: 600;
    }

    .btn-close-white {
        filter: invert(1);
    }

    .lesson-info-card {
        background-color: rgba(67, 97, 238, 0.05);
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .lesson-info-title {
        font-weight: 600;
        color: var(--primary-color);
        margin-bottom: 1rem;
    }

    .lesson-info-item {
        margin-bottom: 0.5rem;
    }

    .lesson-info-label {
        font-weight: 500;
        color: var(--text-muted);
    }

    .lesson-info-value {
        font-weight: 600;
    }

    @media (max-width: 768px) {
        .nav-tabs {
            overflow-x: auto;
            flex-wrap: nowrap;
            -webkit-overflow-scrolling: touch;
        }

        .nav-tabs .nav-item {
            min-width: 120px;
        }

        .page-header {
            padding: 1.5rem 1rem 1.5rem 4rem;
        }

        .back-btn {
            left: 0.5rem;
            padding: 0.5rem;
        }
    }
</style>
<div class="theme-custom">
    <div class="container-fluid">
        <!-- Page Header with Gradient Background -->
        <div class="page-header">
            <!-- Replace your current back button with this -->
            <a href="{{ route('evaluation.report.elearning.index', ['year' => $year ?? date('Y')]) }}" class="back-btn" role="button">
                <i class="fas fa-arrow-left"></i> Back
            </a>




            <h1 class="page-title text-center">
                <i class="fas fa-clipboard-check me-2"></i> E-Learning Detail Report
            </h1>
        </div>

        <!-- Employee Info and Scores Row -->
        <div class="row mb-4">
            <!-- Employee Information Card -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <i class="fas fa-user-circle info-icon"></i>
                        <h5 class="mb-0">Employee Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="employee-info-item">
                                    <div class="employee-info-label">
                                        <i class="fas fa-id-card me-1"></i> Employee ID
                                    </div>
                                    <div class="employee-info-value">
                                        {{ $employee->employee_id }}
                                    </div>
                                </div>

                                <div class="employee-info-item">
                                    <div class="employee-info-label">
                                        <i class="fas fa-building me-1"></i> Department
                                    </div>
                                    <div class="employee-info-value">
                                        {{ $employee->department->department }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="employee-info-item">
                                    <div class="employee-info-label">
                                        <i class="fas fa-user me-1"></i> Name
                                    </div>
                                    <div class="employee-info-value">
                                        {{ $employee->name }}
                                    </div>
                                </div>

                                <div class="employee-info-item">
                                    <div class="employee-info-label">
                                        <i class="fas fa-briefcase me-1"></i> Position
                                    </div>
                                    <div class="employee-info-value">
                                        {{ $employee->position->position }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="employee-info-item">
                                    <div class="employee-info-label">
                                        <i class="fas fa-calendar-alt me-1"></i> Evaluation Year
                                    </div>
                                    <div class="employee-info-value">
                                        {{ $year }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Scores & Export Cards -->
            <div class="col-lg-6 mb-4">
                <div class="row h-100">
                    <!-- Final Grade Card -->
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="card score-card">
                            <div class="card-header d-flex align-items-center">
                                <i class="fas fa-star info-icon"></i>
                                <h5 class="mb-0">Final Grade</h5>
                            </div>
                            <div class="card-body text-center">
                                <div class="score-value">{{ $yearScores['final_grade'] }}</div>
                                <div class="score-description">{{ $yearScores['grade_description'] }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Final Score Card -->
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="card score-card" style="border-left-color: var(--accent-color);">
                            <div class="card-header d-flex align-items-center">
                                <i class="fas fa-chart-bar info-icon"></i>
                                <h5 class="mb-0">Final Score</h5>
                            </div>
                            <div class="card-body text-center">
                                <span class="badge bg-{{ $yearScores['final_percentage'] >= 80 ? 'success' : ($yearScores['final_percentage'] >= 60 ? 'warning' : 'danger') }} badge-lg mb-2">
                                    {{ $yearScores['final_percentage'] }}%
                                </span>
                                <div class="score-description">
                                    {{ $yearScores['final_percentage'] >= 80 ? 'Excellent' : ($yearScores['final_percentage'] >= 60 ? 'Good' : 'Needs Improvement') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Export Card -->
                    <div class="col-md-4">
                        <div class="card score-card" style="border-left-color: var(--success-color);">
                            <div class="card-header d-flex align-items-center">
                                <i class="fas fa-file-export info-icon"></i>
                                <h5 class="mb-0">Export Report</h5>
                            </div>
                            <div class="card-body d-flex align-items-center justify-content-center">
                                <button id="btn-export-excel" class="btn btn-export w-100">
                                    <i class="fas fa-file-excel me-2"></i> Excel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Performance Tab Card -->
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center">
                <i class="fas fa-chart-line info-icon"></i>
                <h5 class="mb-0">Monthly E-Learning Performance</h5>
            </div>
            <div class="card-body">
                <!-- Month Tabs -->
                <div class="mb-4">
                    <ul class="nav nav-tabs" id="monthTabs" role="tablist">
                        @php
                        $months = ['January', 'February', 'March', 'April', 'May', 'June',
                        'July', 'August', 'September', 'October', 'November',
                        'December', 'Final'];
                        @endphp

                        @foreach($months as $index => $month)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $index == 0 ? 'active' : '' }}"
                                id="month-{{ $index+1 }}-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#month-{{ $index+1 }}"
                                type="button"
                                role="tab"
                                aria-controls="month-{{ $index+1 }}"
                                aria-selected="{{ $index == 0 ? 'true' : 'false' }}">
                                <i class="fas fa-{{ $month == 'Final' ? 'flag-checkered' : 'calendar-day' }} me-1"></i> {{ $month }}
                            </button>
                        </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Tab Content -->
                <div class="tab-content" id="monthTabContent">
                    @for($month = 1; $month <= 12; $month++)
                        <div class="tab-pane fade {{ $month == 1 ? 'show active' : '' }}"
                        id="month-{{ $month }}"
                        role="tabpanel"
                        aria-labelledby="month-{{ $month }}-tab">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="20%">Lesson Name</th>
                                        <th width="10%">Start Date</th>
                                        <th width="10%">End Date</th>
                                        <th width="10%">Passing Grade</th>
                                        <th width="10%">Passing %</th>
                                        <th width="10%">Raw Score</th>
                                        <th width="10%">Score %</th>
                                        <th width="5%">Grade</th>
                                        <th width="10%">Status</th>
                                        <th width="5%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($monthlyData[$month] as $index => $invitation)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $invitation->lesson_name }}</strong>
                                        </td>
                                        <td>{{ date('d M Y', strtotime($invitation->start_date)) }}</td>
                                        <td>{{ date('d M Y', strtotime($invitation->end_date)) }}</td>
                                        <td>{{ $invitation->passing_grade }}</td>
                                        <td>
                                            @if(is_numeric($invitation->passing_grade_percentage))
                                            {{ $invitation->passing_grade_percentage }}%
                                            @else
                                            {{ $invitation->passing_grade_percentage }}
                                            @endif
                                        </td>
                                        <td>{{ $invitation->raw_score }}{{ is_numeric($invitation->total_possible) ? '/'.$invitation->total_possible : '' }}</td>
                                        <td>
                                            @if(is_numeric($invitation->score_percentage))
                                            <span class="badge bg-{{ $invitation->score_percentage >= $invitation->passing_grade_percentage ? 'success' : 'danger' }} p-2">
                                                {{ $invitation->score_percentage }}%
                                            </span>
                                            @else
                                            {{ $invitation->score_percentage }}
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $invitation->grade }}</strong>
                                        </td>
                                        <td>
                                            @if($invitation->status == 'Completed')
                                            <span class="status-badge bg-success text-white">
                                                Completed
                                            </span>
                                            @elseif($invitation->status == 'Not Completed')
                                            <span class="status-badge bg-danger  text-white">
                                                Not Completed
                                            </span>
                                            @else
                                            <span class="status-badge bg-warning text-dark">
                                                Pending
                                            </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($invitation->status == 'Completed')
                                            <button class="btn btn-sm btn-detail view-answers"
                                                data-invitation-id="{{ $invitation->invitation_id }}">
                                                <i class="fas fa-eye"></i> Detail
                                            </button>
                                            @else
                                            <button class="btn btn-sm btn-secondary" disabled>
                                                <i class="fas fa-eye-slash"></i> N/A
                                            </button>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="11" class="text-center py-4">
                                            <i class="fas fa-info-circle me-2 text-info"></i>
                                            No e-learning data found for this month.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                </div>
                @endfor

                <!-- Final Tab -->
                <div class="tab-pane fade"
                    id="month-13"
                    role="tabpanel"
                    aria-labelledby="month-13-tab">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <h5 class="card-title mb-4">
                                        <i class="fas fa-flag-checkered me-2 text-primary"></i>
                                        Annual E-Learning Performance Summary
                                    </h5>

                                    <!-- Summary Cards -->
                                    <div class="row mb-4">
                                        <div class="col-md-4 mb-3">
                                            <div class="card summary-card border-left-primary">
                                                <div class="card-body">
                                                    <div class="row align-items-center">
                                                        <div class="col">
                                                            <div class="summary-title">Final Score</div>
                                                            <div class="summary-value">{{ $yearScores['final_percentage'] }}%</div>
                                                        </div>
                                                        <div class="col-auto">
                                                            <i class="fas fa-percent summary-icon"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <div class="card summary-card border-left-success">
                                                <div class="card-body">
                                                    <div class="row align-items-center">
                                                        <div class="col">
                                                            <div class="summary-title">Final Grade</div>
                                                            <div class="summary-value">{{ $yearScores['final_grade'] }}</div>
                                                        </div>
                                                        <div class="col-auto">
                                                            <i class="fas fa-award summary-icon"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <div class="card summary-card border-left-info">
                                                <div class="card-body">
                                                    <div class="row align-items-center">
                                                        <div class="col">
                                                            <div class="summary-title">Description</div>
                                                            <div class="summary-value">{{ $yearScores['grade_description'] }}</div>
                                                        </div>
                                                        <div class="col-auto">
                                                            <i class="fas fa-clipboard-list summary-icon"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Monthly Summary Chart -->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="card border-0">
                                                <div class="card-header bg-white border-0 d-flex align-items-center">
                                                    <i class="fas fa-chart-bar info-icon"></i>
                                                    <h5 class="mb-0">Monthly Performance Trend</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="chart-container" style="height: 300px;">
                                                        <canvas id="monthlyChart"></canvas>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Answer Detail Modal -->
<div class="modal fade" id="answerDetailModal" tabindex="-1" aria-labelledby="answerDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-list-alt me-2"></i> Answer Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Lesson Info Card -->
                <div class="lesson-info-card">
                    <h6 class="lesson-info-title">
                        <i class="fas fa-book me-2"></i> <span id="modal-lesson-name"></span>
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="lesson-info-item">
                                <span class="lesson-info-label">
                                    <i class="fas fa-check-circle me-1"></i> Passing Grade:
                                </span>
                                <span class="lesson-info-value" id="modal-passing-grade"></span>
                            </div>

                            <div class="lesson-info-item">
                                <span class="lesson-info-label">
                                    <i class="fas fa-percent me-1"></i> Passing Grade %:
                                </span>
                                <span class="lesson-info-value" id="modal-passing-grade-percent"></span>
                            </div>

                            <div class="lesson-info-item">
                                <span class="lesson-info-label">
                                    <i class="fas fa-question-circle me-1"></i> Total Questions:
                                </span>
                                <span class="lesson-info-value" id="modal-total-questions"></span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="lesson-info-item">
                                <span class="lesson-info-label">
                                    <i class="fas fa-calculator me-1"></i> Raw Score:
                                </span>
                                <span class="lesson-info-value" id="modal-raw-score"></span>
                            </div>

                            <div class="lesson-info-item">
                                <span class="lesson-info-label">
                                    <i class="fas fa-percent me-1"></i> Score %:
                                </span>
                                <span class="lesson-info-value" id="modal-score-percent"></span>
                            </div>

                            <div class="lesson-info-item">
                                <span class="lesson-info-label">
                                    <i class="fas fa-info-circle me-1"></i> Status:
                                </span>
                                <span class="lesson-info-value" id="modal-status"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Answers Table -->
                <div class="table-responsive">
                    <table class="table table-hover" id="answersTable">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th width="40%">Question</th>
                                <th width="10%">Points</th>
                                <th width="15%">Answer Key</th>
                                <th width="15%">User Answer</th>
                                <th width="15%">Result</th>
                            </tr>
                        </thead>
                        <tbody id="answer-details-body">
                            <!-- Data will be loaded dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
       // Export to Excel
    $('#btn-export-excel').click(function() {
        window.location.href = "{{ route('evaluation.report.elearning.export') }}?employee_id={{ $employee->id }}&year={{ $year }}";
    });

    // View answers button click handler
    $('.view-answers').click(function() {
        const invitationId = $(this).data('invitation-id');
        
        // Reset and show modal
        resetModal();
        $('#answerDetailModal').modal('show');
        
        // Load data
        fetchAnswerDetails(invitationId);
    });
    
    // Reset modal to initial state
    function resetModal() {
        $('#modal-lesson-name').text('');
        $('#modal-passing-grade').text('');
        $('#modal-total-questions').text('');
        $('#modal-raw-score').text('');
        $('#modal-score-percent').text('');
        $('#modal-passing-grade-percent').text('');
        $('#modal-status').html('');
        
        // Show loading in table
        $('#answer-details-body').html(`
            <tr>
                <td colspan="6" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3">Loading answer details...</p>
                </td>
            </tr>
        `);
    }
    
    // Fetch answer details from API
    function fetchAnswerDetails(invitationId) {
        $.ajax({
            url: "{{ url('evaluation/report/elearning/detail-answers') }}/" + invitationId,
            type: "GET",
            dataType: "json",
            success: function(response) {
                // Fill modal with data
                $('#modal-lesson-name').text(response.lesson.name);
                $('#modal-passing-grade').text(response.lesson.passing_grade);
                
                // Calculate scores
                let totalPossiblePoints = 0;
                let earnedPoints = 0;
                
                response.answers.forEach(answer => {
                    const questionValue = parseFloat(answer.grade);
                    totalPossiblePoints += questionValue;
                    
                    if (answer.answer == answer.answer_key) {
                        earnedPoints += questionValue;
                    }
                });
                
                // Calculate score percentage
                const scorePercentage = totalPossiblePoints > 0 ? 
                    ((earnedPoints / totalPossiblePoints) * 100).toFixed(2) : 0;
                    
                // Calculate passing grade percentage
                const passingGradePercentage = totalPossiblePoints > 0 ?
                    ((response.lesson.passing_grade / totalPossiblePoints) * 100).toFixed(2) : 0;
                
                $('#modal-total-questions').text(response.answers.length);
                $('#modal-raw-score').text(earnedPoints + "/" + totalPossiblePoints);
                $('#modal-score-percent').text(scorePercentage + "%");
                $('#modal-passing-grade-percent').text(passingGradePercentage + "%");
                
                // Determine if passing or not
                const isPassing = earnedPoints >= response.lesson.passing_grade;
                $('#modal-status').html(isPassing ? 
                    '<span class="status-badge bg-success"><i class="fas fa-check-circle me-1"></i> Passed</span>' : 
                    '<span class="status-badge bg-danger"><i class="fas fa-times-circle me-1"></i> Failed</span>');
                    
                // Generate answers table
                let tableHtml = '';
                response.answers.forEach((answer, index) => {
                    const isCorrect = answer.answer == answer.answer_key;
                    const points = isCorrect ? answer.grade : 0;
                    
                    tableHtml += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${answer.question}</td>
                            <td>${answer.grade}</td>
                            <td>${answer.answer_key}</td>
                            <td>${answer.answer}</td>
                            <td>
                                ${isCorrect ? 
                                    'Correct (' + points + ')</span>' : 
                                    'Incorrect (0)</span>'}
                            </td>
                        </tr>
                    `;
                });
                
                $('#answer-details-body').html(tableHtml);
            },
            error: function(error) {
                console.error("Error fetching answer details:", error);
                $('#answer-details-body').html(`
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Failed to load answer details. Please try again.
                            </div>
                        </td>
                    </tr>
                `);
            }
        });
    }

    // Create monthly performance chart
    createMonthlyChart();

        // Create monthly performance chart
        function createMonthlyChart() {
            const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const monthlyScores = [];
            
            // Calculate average score for each month
            @for($month = 1; $month <= 12; $month++)
                @php
                    // Calculate average score for this month
                    $completedLessons = 0;
                    $totalPercentage = 0;
                    
                    foreach($monthlyData[$month] as $invitation) {
                        if(is_numeric($invitation->score_percentage)) {
                            $totalPercentage += $invitation->score_percentage;
                            $completedLessons++;
                        }
                    }
                    
                    $avgScore = $completedLessons > 0 ? round($totalPercentage / $completedLessons, 2) : 0;
                @endphp
                
                monthlyScores.push({{ $avgScore }});
            @endfor
            
            const ctx = document.getElementById('monthlyChart').getContext('2d');
            const monthlyChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: monthNames,
                    datasets: [{
                        label: 'Monthly Performance (%)',
                        data: monthlyScores,
                        backgroundColor: 'rgba(67, 97, 238, 0.1)',
                        borderColor: 'rgba(67, 97, 238, 1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true,
                        pointBackgroundColor: 'rgba(67, 97, 238, 1)',
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: {
                                    weight: 'bold'
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.parsed.y + '%';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }
        

    });
</script>
@endpush