@extends('layouts.app')

@section('content')

<div class="container-fluid py-4">
    <!-- Page Title -->
    <div class="row">
        <div class="col-12 text-center mb-5">
            <h1 class="text-warning">
                <i class="fas fa-clipboard-check"></i> Final Employee Evaluation Results
            </h1>
        </div>
    </div>
    <!-- Overview Cards - Aligned properly -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card card-stats mb-4 mb-xl-0 shadow-sm border-0">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="card-title text-uppercase text-muted mb-0">Total Evaluations</h5>
                            <span class="h2 font-weight-bold mb-0">{{ count($evaluations) }}</span>
                        </div>
                        <div class="col-auto">
                            <!-- Increased circle size for better visibility -->
                            <div class="icon icon-shape bg-primary text-white rounded-circle shadow" style="width: 64px; height: 64px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-users fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card card-stats mb-4 mb-xl-0 shadow-sm border-0">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="card-title text-uppercase text-muted mb-0">High Grade (A, B)</h5>
                            <span class="h2 font-weight-bold mb-0">
                                {{ $evaluations->filter(function($eval) {
                            $grade = $eval->proposal_grade ?? $eval->final_grade;
                            return in_array($grade, ['A', 'A-', 'B+', 'B', 'B-']);
                        })->count() }}
                            </span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-success text-white rounded-circle shadow" style="width: 64px; height: 64px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-award fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card card-stats mb-4 mb-xl-0 shadow-sm border-0">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="card-title text-uppercase text-muted mb-0">Average Grade (C)</h5>
                            <span class="h2 font-weight-bold mb-0">
                                {{ $evaluations->filter(function($eval) {
                            $grade = $eval->proposal_grade ?? $eval->final_grade;
                            return in_array($grade, ['C+', 'C', 'C-']);
                        })->count() }}
                            </span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-warning text-white rounded-circle shadow" style="width: 64px; height: 64px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-chart-line fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card card-stats mb-4 mb-xl-0 shadow-sm border-0">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="card-title text-uppercase text-muted mb-0">Low Grade (D, E, F)</h5>
                            <span class="h2 font-weight-bold mb-0">
                                {{ $evaluations->filter(function($eval) {
                            $grade = $eval->proposal_grade ?? $eval->final_grade;
                            return in_array($grade, ['D+', 'D', 'D-', 'E+', 'E', 'E-', 'F']);
                        })->count() }}
                            </span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-danger text-white rounded-circle shadow" style="width: 64px; height: 64px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-exclamation-triangle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <!-- Filters -->
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-header bg-primary text-white p-3">
                    <h6 class="mb-0">
                        <i class="fas fa-filter me-2"></i>Filter Options
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Year</label>
                            <select class="form-select form-select-sm filter" id="filter-year">
                                <option value="">All Years</option>
                                @foreach($years as $year)
                                <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Employee</label>
                            <select class="form-select form-select-sm filter" id="filter-user">
                                <option value="">All Employees</option>
                                @foreach($users as $user)
                                <option value="{{ $user['id'] }}" {{ request('user_id') == $user['id'] ? 'selected' : '' }}>
                                    {{ $user['name'] }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Position</label>
                            <select class="form-select form-select-sm filter" id="filter-position">
                                <option value="">All Positions</option>
                                @foreach($positions as $position)
                                <option value="{{ $position->id }}" {{ request('position_id') == $position->id ? 'selected' : '' }}>{{ $position->position }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Department</label>
                            <select class="form-select form-select-sm filter" id="filter-department">
                                <option value="">All Departments</option>
                                @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>{{ $department->department }}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-2 mb-2">
                            <label class="form-label">Performance Grade</label>
                            <select class="form-select form-select-sm filter" id="filter-performance">
                                <option value="">All Grades</option>
                                @foreach($grades as $grade)
                                <option value="{{ $grade }}" {{ request('performance') == $grade ? 'selected' : '' }}>{{ $grade }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label class="form-label">Discipline Grade</label>
                            <select class="form-select form-select-sm filter" id="filter-discipline">
                                <option value="">All Grades</option>
                                @foreach($grades as $grade)
                                <option value="{{ $grade }}" {{ request('discipline') == $grade ? 'selected' : '' }}>{{ $grade }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label class="form-label">E-Learning Grade</label>
                            <select class="form-select form-select-sm filter" id="filter-elearning">
                                <option value="">All Grades</option>
                                @foreach($grades as $grade)
                                <option value="{{ $grade }}" {{ request('elearning') == $grade ? 'selected' : '' }}>{{ $grade }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label class="form-label">Final Grade</label>
                            <select class="form-select form-select-sm filter" id="filter-final-grade">
                                <option value="">All Grades</option>
                                @foreach($grades as $grade)
                                <option value="{{ $grade }}" {{ request('final_grade') == $grade ? 'selected' : '' }}>{{ $grade }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label class="form-label">Proposal Grade</label>
                            <select class="form-select form-select-sm filter" id="filter-proposal-grade">
                                <option value="">All Grades</option>
                                @foreach($grades as $grade)
                                <option value="{{ $grade }}" {{ request('proposal_grade') == $grade ? 'selected' : '' }}>{{ $grade }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end mb-2">
                            <button class="btn btn-primary btn-sm w-100" id="apply-filters">
                                <i class="fas fa-search me-1"></i>Apply Filters
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 shadow-lg border-0 rounded-3">
                <div class="card-header bg-primary p-3">
                    <div class="row align-items-center">
                        <div class="col-auto d-flex align-items-center pe-0">
                            <i class="fas fa-users text-white fs-3"></i>
                        </div>
                        <div class="col text-start">
                            <h5 class="text-white mb-0">Employee Final Evaluations</h5>
                            <p class="text-white text-sm mb-0 opacity-8">Track and analyze employee performance metrics</p>
                        </div>
                        <div class="col-auto text-end">
                            <button class="btn btn-light text-primary btn-sm fw-bold" id="mass-update-btn">
                                <i class="fas fa-users me-2"></i>Mass Update
                            </button>
                            <button class="btn btn-success btn-sm fw-bold ms-2" id="save-all-btn">
                                <i class="fas fa-save me-2"></i>Save All Changes
                            </button>
                        </div>
                    </div>
                </div>


                <div class="card-body">
                    <!-- Table -->
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <!-- Modified table columns to show current salary and remove salary decreases -->
                                <table class="table align-items-center table-hover mb-0" id="evaluations-table">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="text-center" width="40">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="select-all">
                                                </div>
                                            </th>
                                            <th>Employee</th>
                                            <th>Position</th>
                                            <th>Department</th>
                                            <th>Year</th>
                                            <th>Performance</th>
                                            <th>Discipline</th>
                                            <th>E-Learning</th>
                                            <th>Final Score</th>
                                            <th>Final Grade</th>
                                            <th>File Proposal</th>
                                            <th>Proposal Grade</th>
                                            <th>Current Salary</th>
                                            <th>Salary Increase</th>
                                            <th>New Salary</th>
                                            <th>Warning Letter</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    @php
                                    function getGradeColor($grade) {
                                    $colors = [
                                    'A' => 'success',
                                    'A-' => 'success',
                                    'B+' => 'success',
                                    'B' => 'success',
                                    'B-' => 'success',
                                    'C+' => 'info',
                                    'C' => 'info',
                                    'C-' => 'info',
                                    'D+' => 'warning',
                                    'D' => 'warning',
                                    'D-' => 'warning',
                                    'E+' => 'danger',
                                    'E' => 'danger',
                                    'E-' => 'danger',
                                    'F' => 'danger',
                                    ];
                                    return $colors[$grade] ?? 'secondary';
                                    }

                                    function getScoreColor($score) {
                                    if ($score >= 2.7) {
                                    return 'success';
                                    } elseif ($score >= 2.0) {
                                    return 'info';
                                    } elseif ($score >= 1.0) {
                                    return 'warning';
                                    } else {
                                    return 'danger';
                                    }
                                    }
                                    @endphp


                                    <tbody>
                                        @foreach($evaluations as $evaluation)
                                        <tr data-id="{{ $evaluation->id }}">
                                            <td class="text-center">
                                                <div class="form-check">
                                                    <input class="form-check-input evaluation-select" type="checkbox" value="{{ $evaluation->id }}">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm rounded-circle bg-primary me-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <span class="text-white text-uppercase">{{ substr($evaluation->user->name, 0, 1) }}</span>
                                                    </div>
                                                    <span>{{ $evaluation->user->name }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                @if(isset($evaluation->position) && $evaluation->position)
                                                <span class="badge bg-info text-white">
                                                    {{ $evaluation->position->position }}
                                                </span>
                                                @else
                                                <span class="badge bg-secondary">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if(isset($evaluation->department) && $evaluation->department)
                                                <span class="badge bg-light text-dark">
                                                    {{ $evaluation->department->department }}
                                                </span>
                                                @else
                                                <span class="badge bg-secondary">N/A</span>
                                                @endif
                                            </td>
                                            <td>{{ $evaluation->year }}</td>
                                            <td>
                                                <span class="badge bg-{{ getGradeColor($evaluation->performance) }}">
                                                    {{ $evaluation->performance }}
                                                </span>
                                                <small class="text-muted">({{ $evaluation->performance_score }})</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ getGradeColor($evaluation->discipline) }}">
                                                    {{ $evaluation->discipline }}
                                                </span>
                                                <small class="text-muted">({{ $evaluation->discipline_score }})</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ getGradeColor($evaluation->elearning) }}">
                                                    {{ $evaluation->elearning }}
                                                </span>
                                                <small class="text-muted">({{ $evaluation->elearning_score }})</small>
                                            </td>
                                            <td>
                                                <div class="progress" style="height: 6px;">
                                                    <div class="progress-bar bg-{{ getScoreColor($evaluation->final_score) }}"
                                                        role="progressbar"
                                                        style="width: {{ min(100, $evaluation->final_score * 33.33) }}%;"
                                                        aria-valuenow="{{ $evaluation->final_score }}"
                                                        aria-valuemin="0"
                                                        aria-valuemax="3">
                                                    </div>
                                                </div>
                                                <small class="fw-bold">{{ $evaluation->final_score }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ getGradeColor($evaluation->final_grade) }} px-3 py-2">
                                                    {{ $evaluation->final_grade }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <form class="file-upload-form me-2" data-id="{{ $evaluation->id }}">
                                                        <input type="file" name="file_proposal" class="d-none file-input" accept="application/pdf">
                                                        <button type="button" class="btn btn-sm btn-outline-primary upload-btn">
                                                            <i class="fas fa-upload"></i>
                                                        </button>
                                                    </form>
                                                    @if($evaluation->file_proposal)
                                                    <button type="button" class="btn btn-sm btn-info view-pdf" data-file="{{ asset('storage/' . $evaluation->file_proposal) }}">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <select class="form-select form-select-sm proposal-grade" data-id="{{ $evaluation->id }}">
                                                    <option value="">Select Grade</option>
                                                    @foreach($grades as $grade)
                                                    <option value="{{ $grade }}" {{ $evaluation->proposal_grade == $grade ? 'selected' : '' }}>{{ $grade }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">Rp</span>
                                                    <input type="text" class="form-control current-salary"
                                                        value="{{ number_format($evaluation->current_salary ?? 0, 0, ',', '.') }}"
                                                        readonly>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">Rp</span>
                                                    <input type="number" class="form-control salary-increase"
                                                        data-id="{{ $evaluation->id }}"
                                                        data-final-grade="{{ $evaluation->final_grade }}"
                                                        data-proposal-grade="{{ $evaluation->proposal_grade }}"
                                                        value="{{ $evaluation->salary_increases }}"
                                                        min="0">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">Rp</span>
                                                    <input type="text" class="form-control new-salary"
                                                        value="{{ number_format(($evaluation->current_salary ?? 0) + ($evaluation->salary_increases ?? 0), 0, ',', '.') }}"
                                                        readonly>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $evaluation->warning_letters == '-' ? 'secondary' : 'danger' }}">
                                                    {{ $evaluation->warning_letters }}
                                                </span>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-success save-row" data-id="{{ $evaluation->id }}">
                                                    <i class="fas fa-save"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <!-- Modified Mass Update Modal - Removed salary decrease field -->
                                <div class="modal fade" id="massUpdateModal" tabindex="-1" aria-labelledby="massUpdateModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title text-white" id="massUpdateModalLabel">Mass Update Selected Evaluations</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Proposal Grade</label>
                                                    <select class="form-select" id="mass-proposal-grade">
                                                        <option value="">No Change</option>
                                                        @foreach($grades as $grade)
                                                        <option value="{{ $grade }}">{{ $grade }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Salary Increase</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">Rp</span>
                                                        <input type="number" class="form-control" id="mass-salary-increase" placeholder="Leave empty for no change" min="0">
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="mass-auto-increase">
                                                        <label class="form-check-label" for="mass-auto-increase">
                                                            Auto-calculate salary increase based on grade
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="button" class="btn btn-primary" id="save-mass-update">Save Changes</button>
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

<!-- PDF Viewer Modal -->
<div class="modal fade" id="pdfModal" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white" id="pdfModalLabel">Proposal Document</h5>
                <button type="button" class="btn-close bg-light" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <iframe id="pdf-iframe" style="width: 100%; height: 600px;" frameborder="0"></iframe>
            </div>
        </div>
    </div>
</div>

<!-- Mass Update Modal -->
<div class="modal fade" id="massUpdateModal" tabindex="-1" aria-labelledby="massUpdateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white" id="massUpdateModalLabel">Mass Update Selected Evaluations</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Proposal Grade</label>
                    <select class="form-select" id="mass-proposal-grade">
                        <option value="">No Change</option>
                        @foreach($grades as $grade)
                        <option value="{{ $grade }}">{{ $grade }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Salary Increase</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" class="form-control" id="mass-salary-increase" placeholder="Leave empty for no change" min="0">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Salary Decrease</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" class="form-control" id="mass-salary-decrease" placeholder="Leave empty for no change" min="0">
                    </div>
                </div>
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="mass-auto-increase">
                        <label class="form-check-label" for="mass-auto-increase">
                            Auto-calculate salary increase based on grade
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="save-mass-update">Save Changes</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        // Initialize DataTables with more appropriate options
        const evaluationTable = $('#evaluations-table').DataTable({
            responsive: true,
            processing: true,
            pageLength: 10,
            lengthMenu: [
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ],
            order: [
                [4, 'desc'],
                [8, 'desc']
            ],
            dom: '<"row"<"col-md-6"l><"col-md-6"f>>rtip',
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search evaluations...",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries"
            },
            // Preserve your existing filter functionality
            serverSide: false, // Set to true if you're using server-side processing
            stateSave: true,
            initComplete: function() {
                // Move DataTables elements to match your layout
                $('.dataTables_filter input').addClass('form-control');
                $('.dataTables_length select').addClass('form-select');
            }
        });

        // Make your existing filters work with DataTables
        $('#apply-filters').on('click', function(e) {
            e.preventDefault();

            // Instead of redirecting, use DataTables API for filtering
            evaluationTable.search('').columns().search('').draw();

            // Apply custom filtering for each filter
            if ($('#filter-year').val()) {
                evaluationTable.column(4).search($('#filter-year').val()).draw();
            }

            if ($('#filter-department').val()) {
                evaluationTable.column(3).search($('#filter-department').val()).draw();
            }

            // Add similar conditions for other filters
            // Note: Column indices may need adjustment based on your HTML structure
        });

        // Preserve your existing functionality
        $('#select-all').on('change', function() {
            $('.evaluation-select').prop('checked', $(this).is(':checked'));
        });


        // Add this code to make filters work with DataTables
        $.fn.dataTable.ext.search.push(
            function(settings, data, dataIndex) {
                // Year filter
                const yearFilter = $('#filter-year').val();
                const yearValue = data[4]; // Adjust index based on your column order
                if (yearFilter && yearFilter !== yearValue) return false;

                // Department filter
                const departmentFilter = $('#filter-department').val();
                const departmentValue = data[3]; // Adjust index
                if (departmentFilter && !departmentValue.includes(departmentFilter)) return false;

                // Add similar conditions for other filters...

                return true;
            }
        );

        // Update filter handling
        $('#apply-filters').on('click', function() {
            table.draw();
        });

        // Function to show SweetAlert notification - Moved to bottom right
        function showAlert(message, icon = 'success') {
            Swal.fire({
                icon: icon,
                title: icon === 'success' ? 'Success' : 'Error',
                text: message,
                toast: true,
                position: 'bottom-end', // Changed to bottom-right corner
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        }

        // View PDF
        $(document).on('click', '.view-pdf', function() {
            const fileUrl = $(this).data('file');
            $('#pdf-iframe').attr('src', fileUrl);
            $('#pdfModal').modal('show');
        });

        // File upload trigger
        $(document).on('click', '.upload-btn', function() {
            $(this).closest('.file-upload-form').find('.file-input').click();
        });

        // File input change
        $(document).on('change', '.file-input', function() {
            const form = $(this).closest('.file-upload-form');
            const evaluationId = form.data('id');
            const fileData = new FormData();

            fileData.append('file_proposal', this.files[0]);
            fileData.append('_token', '{{ csrf_token() }}');

            $.ajax({
                url: `/evaluation/report/final/result/upload-proposal/${evaluationId}`,
                type: 'POST',
                data: fileData,
                contentType: false,
                processData: false,
                success: function(response) {
                    showAlert(response.message);

                    // Add view button if not already present
                    if (!form.next().hasClass('view-pdf')) {
                        form.after(`
                            <button type="button" class="btn btn-sm btn-info view-pdf" data-file="${response.file_url}">
                                <i class="fas fa-eye"></i>
                            </button>
                        `);
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    showAlert(response.message || 'Error uploading file', 'error');
                }
            });
        });

        // Update proposal grade
        $(document).on('change', '.proposal-grade', function() {
            const id = $(this).data('id');
            const grade = $(this).val();
            const row = $(this).closest('tr');

            if (grade) {
                // Auto-calculate salary increase
                $.ajax({
                    url: `/evaluation/report/final/result/get-salary-value`,
                    type: 'GET',
                    data: {
                        grade: grade
                    },
                    success: function(response) {
                        if (response.value_salary) {
                            row.find('.salary-increase').val(response.value_salary);
                        }
                    }
                });
            }
        });


        function formatNumber(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        }

        // Function to calculate and update new salary when salary increase changes
        $(document).on('input', '.salary-increase', function() {
            const row = $(this).closest('tr');
            const currentSalary = parseInt(row.find('.current-salary').val().replace(/\./g, '')) || 0;
            const increase = parseInt($(this).val()) || 0;
            const newSalary = currentSalary + increase;

            row.find('.new-salary').val(formatNumber(newSalary));
        });


        // Modified save row function with confirmation
        $(document).on('click', '.save-row', function() {
            const id = $(this).data('id');
            const row = $(this).closest('tr');
            const increase = row.find('.salary-increase').val();
            const proposalGrade = row.find('.proposal-grade').val();

            // Only show confirmation if there's a salary increase
            if (increase > 0) {
                Swal.fire({
                    title: 'Confirm Salary Update',
                    html: `
                <p>You are about to update an employee's salary. This action cannot be reversed.</p>
                <p>Are you sure you want to proceed?</p>
            `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, update it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        saveRowChanges(id, row);
                    }
                });
            } else {
                // If no salary increase, just save proposal grade without confirmation
                saveRowChanges(id, row);
            }
        });

        // Function to actually save the row changes
        function saveRowChanges(id, row) {
            const data = {
                proposal_grade: row.find('.proposal-grade').val(),
                salary_increases: row.find('.salary-increase').val(),
                _token: '{{ csrf_token() }}'
            };

            Swal.fire({
                title: 'Saving Changes',
                text: 'Please wait...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: `/evaluation/report/final/result/update/${id}`,
                type: 'PUT',
                data: data,
                success: function(response) {
                    Swal.close();
                    showAlert(response.message);

                    // If successful salary update, update the display values
                    if (response.salary_updated) {
                        row.find('.current-salary').val(formatNumber(response.new_salary));
                        row.find('.salary-increase').val(0);
                        row.find('.new-salary').val(formatNumber(response.new_salary));
                    }
                },
                error: function(xhr) {
                    Swal.close();
                    const response = xhr.responseJSON;
                    showAlert(response.message || 'Error updating record', 'error');
                }
            });
        }

        // Apply filters
        $('#apply-filters').on('click', function() {
            const url = new URL(window.location);

            // Get all filter values
            const year = $('#filter-year').val();
            const userId = $('#filter-user').val();
            const departmentId = $('#filter-department').val();
            const positionId = $('#filter-position').val();
            const finalGrade = $('#filter-final-grade').val();
            const performance = $('#filter-performance').val();
            const discipline = $('#filter-discipline').val();
            const elearning = $('#filter-elearning').val();
            const proposalGrade = $('#filter-proposal-grade').val();

            // Set or remove query parameters
            if (year) url.searchParams.set('year', year);
            else url.searchParams.delete('year');
            if (userId) url.searchParams.set('user_id', userId);
            else url.searchParams.delete('user_id');
            if (departmentId) url.searchParams.set('department_id', departmentId);
            else url.searchParams.delete('department_id');
            if (positionId) url.searchParams.set('position_id', positionId);
            else url.searchParams.delete('position_id');
            if (finalGrade) url.searchParams.set('final_grade', finalGrade);
            else url.searchParams.delete('final_grade');
            if (performance) url.searchParams.set('performance', performance);
            else url.searchParams.delete('performance');
            if (discipline) url.searchParams.set('discipline', discipline);
            else url.searchParams.delete('discipline');
            if (elearning) url.searchParams.set('elearning', elearning);
            else url.searchParams.delete('elearning');
            if (proposalGrade) url.searchParams.set('proposal_grade', proposalGrade);
            else url.searchParams.delete('proposal_grade');

            // Add loading animation
            Swal.fire({
                title: 'Applying Filters',
                text: 'Please wait...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            window.location = url.toString();
        });

        // Select all checkboxes
        $('#select-all').on('change', function() {
            $('.evaluation-select').prop('checked', $(this).is(':checked'));
        });

        // Mass update button click
        $('#mass-update-btn').on('click', function() {
            const selectedCount = $('.evaluation-select:checked').length;

            if (selectedCount === 0) {
                showAlert('Please select at least one record to update', 'error');
                return;
            }

            $('#massUpdateModal').modal('show');
        });

        // Save mass update
        $('#save-mass-update').on('click', function() {
            const selectedIds = [];
            $('.evaluation-select:checked').each(function() {
                selectedIds.push($(this).val());
            });

            const salaryIncrease = $('#mass-salary-increase').val();

            // Only show confirmation if there's a salary increase
            if (salaryIncrease > 0) {
                Swal.fire({
                    title: 'Confirm Mass Salary Update',
                    html: `
                <p>You are about to update salaries for ${selectedIds.length} employees. This action cannot be reversed.</p>
                <p>Are you sure you want to proceed?</p>
            `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, update all!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        saveMassUpdates(selectedIds);
                    }
                });
            } else {
                // If no salary increase, just save proposal grades without confirmation
                saveMassUpdates(selectedIds);
            }
        });

        // Function to save mass updates
        function saveMassUpdates(selectedIds) {
            const data = {
                ids: selectedIds,
                proposal_grade: $('#mass-proposal-grade').val(),
                salary_increases: $('#mass-salary-increase').val(),
                auto_increase: $('#mass-auto-increase').is(':checked'),
                _token: '{{ csrf_token() }}'
            };

            Swal.fire({
                title: 'Updating Records',
                text: 'Please wait...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '/evaluation/report/final/result/mass-update',
                type: 'POST',
                data: data,
                success: function(response) {
                    Swal.close();
                    showAlert(response.message);

                    // Refresh the page to show updated data
                    setTimeout(function() {
                        window.location.reload();
                    }, 1500);
                },
                error: function(xhr) {
                    Swal.close();
                    const response = xhr.responseJSON;
                    showAlert(response.message || 'Error performing mass update', 'error');
                }
            });

            // Close the modal
            $('#massUpdateModal').modal('hide');
        }


        // Auto-calculate checkbox toggle
        $('#mass-auto-increase').on('change', function() {
            if ($(this).is(':checked')) {
                $('#mass-salary-increase').prop('disabled', true);
            } else {
                $('#mass-salary-increase').prop('disabled', false);
            }
        });

        // Save all changes button
        $('#save-all-btn').on('click', function() {
            const allRows = $('#evaluations-table tbody tr');
            const totalRows = allRows.length;
            let processingRows = [];

            // Check if there are any salary increases
            let hasSalaryIncreases = false;
            allRows.each(function() {
                const increase = parseInt($(this).find('.salary-increase').val()) || 0;
                if (increase > 0) {
                    hasSalaryIncreases = true;
                    return false; // Break the loop
                }
            });

            // Show confirmation if there are salary increases
            if (hasSalaryIncreases) {
                Swal.fire({
                    title: 'Confirm All Salary Updates',
                    html: `
                <p>You are about to update multiple employee salaries. This action cannot be reversed.</p>
                <p>Are you sure you want to proceed?</p>
            `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, update all!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        saveAllChanges(allRows, totalRows);
                    }
                });
            } else {
                // No salary increases, just save proposal grades
                saveAllChanges(allRows, totalRows);
            }
        });

        // Function to save all changes
        function saveAllChanges(allRows, totalRows) {
            let processedRows = 0;

            if (totalRows === 0) {
                showAlert('No records to save', 'error');
                return;
            }

            Swal.fire({
                title: 'Saving All Changes',
                text: `Processing 0/${totalRows} records...`,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            allRows.each(function() {
                const id = $(this).data('id');
                const row = $(this);
                const data = {
                    proposal_grade: row.find('.proposal-grade').val(),
                    salary_increases: row.find('.salary-increase').val(),
                    _token: '{{ csrf_token() }}'
                };

                $.ajax({
                    url: `/evaluation/report/final/result/update/${id}`,
                    type: 'PUT',
                    data: data,
                    success: function(response) {
                        processedRows++;
                        Swal.update({
                            text: `Processing ${processedRows}/${totalRows} records...`
                        });

                        // Update the row if salary was updated
                        if (response.salary_updated) {
                            row.find('.current-salary').val(formatNumber(response.new_salary));
                            row.find('.salary-increase').val(0);
                            row.find('.new-salary').val(formatNumber(response.new_salary));
                        }

                        if (processedRows === totalRows) {
                            Swal.close();
                            showAlert('All records updated successfully');
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        console.error(`Error updating record ID ${id}:`, response.message || 'Unknown error');

                        processedRows++;
                        if (processedRows === totalRows) {
                            Swal.close();
                            showAlert('Some records may not have been updated correctly. Check console for details.', 'warning');
                        }
                    }
                });
            });
        }


        // Clear all filters
        $('#clear-filters').on('click', function() {
            window.location = window.location.pathname;
        });

        // Export data
        $('#export-data').on('click', function() {
            const url = new URL(window.location);
            url.pathname = '/evaluation/report/final/result/export';

            Swal.fire({
                title: 'Exporting Data',
                text: 'Please wait...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch(url)
                .then(response => response.blob())
                .then(blob => {
                    Swal.close();
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.style.display = 'none';
                    a.href = url;
                    a.download = 'employee_evaluations.xlsx';
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                })
                .catch(() => {
                    Swal.close();
                    showAlert('Error exporting data', 'error');
                });
        });

        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush