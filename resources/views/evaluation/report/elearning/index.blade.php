@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Enhanced Page Title Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-primary shadow-lg text-white border-0">
                <div class="card-body py-5">
                    <div class="d-flex flex-column flex-md-row align-items-center justify-content-center">
                        <div class="icon-circle bg-white text-primary me-md-4 mb-3 mb-md-0">
                            <i class="fas fa-graduation-cap fa-3x"></i>
                        </div>
                        <div class="text-center text-md-start">
                            <h1 class="display-5 fw-bold mb-0">Employee E-Learning Report</h1>
                            <p class="lead text-white-50 mt-2 mb-0">Track and analyze employee progress in e-learning programs</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-uppercase text-primary fw-bold mb-1">
                                <i class="fas fa-users me-2"></i>Total Employees
                            </h6>
                            <h2 class="display-6 fw-bold mb-0">{{ count($employees) }}</h2>
                        </div>
                        <div class="icon-circle bg-light">
                            <i class="fas fa-users fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-uppercase text-success fw-bold mb-1">
                                <i class="fas fa-award me-2"></i>High Performers
                            </h6>
                            <h2 class="display-6 fw-bold mb-0">
                                {{ $employees->where('final_percentage', '>=', 80)->count() }}
                            </h2>
                            <small class="text-muted">≥80%</small>
                        </div>
                        <div class="icon-circle bg-light">
                            <i class="fas fa-award fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-uppercase text-warning fw-bold mb-1">
                                <i class="fas fa-chart-line me-2"></i>Average Performers
                            </h6>
                            <h2 class="display-6 fw-bold mb-0">
                                {{ $employees->where('final_percentage', '>=', 60)->where('final_percentage', '<', 80)->count() }}
                            </h2>
                            <small class="text-muted">60-79%</small>
                        </div>
                        <div class="icon-circle bg-light">
                            <i class="fas fa-chart-line fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-uppercase text-danger fw-bold mb-1">
                                <i class="fas fa-exclamation-triangle me-2"></i>Needs Improvement
                            </h6>
                            <h2 class="display-6 fw-bold mb-0">
                                {{ $employees->where('final_percentage', '<', 60)->count() }}
                            </h2>
                            <small class="text-muted">
                                <60% </small>
                        </div>
                        <div class="icon-circle bg-light">
                            <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Improved Filters Card -->
    <div class="card shadow mb-4 border-0">
        <div class="card-header bg-light py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="m-0 fw-bold text-primary">
                    <i class="fas fa-filter me-2"></i>Report Filters
                </h6>
            </div>
        </div>
        <div class="collapse show" id="filterCollapse">
            <div class="card-body bg-light bg-gradient">
                <form method="GET" action="{{ route('evaluation.report.elearning.index') }}" id="filter-form" class="row g-3">
                    <div class="col-md-4">
                        <div class="form-floating">
                            <select class="form-select" id="year" name="year">
                                @foreach($years as $yr)
                                <option value="{{ $yr }}" {{ $selectedYear == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                                @endforeach
                            </select>
                            <label for="year"><i class="far fa-calendar-alt me-2"></i>Select Year</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating">
                            <select class="form-select" id="department_id" name="department_id">
                                <option value="">All Departments</option>
                                @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ $selectedDepartment == $department->id ? 'selected' : '' }}>
                                    {{ $department->department }}
                                </option>
                                @endforeach
                            </select>
                            <label for="department_id"><i class="fas fa-building me-2"></i>Department</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating">
                            <select class="form-select" id="position_id" name="position_id">
                                <option value="">All Positions</option>
                                @foreach($positions as $position)
                                <option value="{{ $position->id }}" {{ $selectedPosition == $position->id ? 'selected' : '' }}>
                                    {{ $position->position }}
                                </option>
                                @endforeach
                            </select>
                            <label for="position_id"><i class="fas fa-briefcase me-2"></i>Position</label>
                        </div>
                    </div>
                    <div class="col-12 mt-3 text-end">
                        <a href="{{ route('evaluation.report.elearning.index') }}" class="btn btn-light me-2">
                            <i class="fas fa-undo me-1"></i> Reset
                        </a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-search me-1"></i> Apply Filters
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Employees Table with improved styling -->

    <div class="card shadow mb-4 border-0">
        <div class="card-header py-3 bg-white">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <h5 class="m-0 fw-bold text-primary">
                    <i class="fas fa-book-reader me-2"></i>E-Learning Summary ({{ $selectedYear }})
                </h5>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="dataTable" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">#</th>
                            <th>Employee ID</th>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Position</th>
                            <th class="text-center">Score</th>
                            <th class="text-center">Grade</th>
                            <th>Description</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $index => $employee)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>
                                <span class="fw-bold">{{ $employee->employee_id }}</span>
                            </td>
                            <td>{{ $employee->name }}</td>
                            <td>{{ $employee->department }}</td>
                            <td>{{ $employee->position }}</td>
                            <td class="text-center" style="width: 180px;">
                                @php
                                $badgeClass = 'bg-danger';
                                if ($employee->final_percentage >= 80) {
                                $badgeClass = 'bg-success';
                                } elseif ($employee->final_percentage >= 60) {
                                $badgeClass = 'bg-warning text-dark';
                                }
                                @endphp
                                <div class="progress" style="height: 25px;">
                                    <div class="progress-bar progress-bar-striped {{ $badgeClass }}"
                                        role="progressbar"
                                        style="width: {{ $employee->final_percentage }}%;"
                                        aria-valuenow="{{ $employee->final_percentage }}"
                                        aria-valuemin="0"
                                        aria-valuemax="100">
                                        {{ $employee->final_percentage }}%
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge 
                                {{ $employee->final_grade == 'A' ? 'bg-success' : 
                                   ($employee->final_grade == 'B' ? 'bg-primary' : 
                                   ($employee->final_grade == 'C' ? 'bg-warning text-dark' : 
                                   ($employee->final_grade == 'D' ? 'bg-danger' : 'bg-secondary'))) 
                                }} rounded-pill px-3 py-2 fw-bold">
                                    {{ $employee->final_grade }}
                                </span>
                            </td>
                            <td>
                                <small>{{ $employee->grade_description }}</small>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('evaluation.report.elearning.detail', ['employee_id' => $employee->id, 'year' => $selectedYear]) }}"
                                    class="btn btn-sm btn-primary rounded-pill px-3">
                                    <i class="fas fa-chart-bar me-1"></i> View Details
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-search fa-3x mb-3 text-gray-300"></i>
                                    <p class="mb-0">No e-learning data found for the selected filters.</p>
                                    <small>Try changing your filter settings or check back later.</small>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Info Card -->
    <div class="card shadow border-left-info mb-4">
        <div class="card-header py-3 bg-light">
            <div class="d-flex align-items-center">
                <div class="icon-circle bg-info text-white me-3" style="height: 40px; width: 40px;">
                    <i class="fas fa-info fa-lg"></i>
                </div>
                <h5 class="fw-bold text-info mb-0">E-Learning Grading Information</h5>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 10%">Grade</th>
                            <th style="width: 20%">Score Range</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($gradeRules as $rule)
                        <tr>
                            <td>
                                <span class="badge 
                                {{ $rule->grade == 'A' ? 'bg-success' : 
                                ($rule->grade == 'B' ? 'bg-primary' : 
                                ($rule->grade == 'C' ? 'bg-warning text-dark' : 
                                ($rule->grade == 'D' ? 'bg-danger' : 'bg-secondary'))) 
                                }} rounded-pill px-3 py-2">
                                    {{ $rule->grade }}
                                </span>
                            </td>
                            <td>{{ $rule->min_score }}% - {{ $rule->max_score }}%</td>
                            <td>{{ $rule->description }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTable with enhanced options
        $('#dataTable').DataTable({
            "ordering": true,
            "responsive": true,
            "lengthMenu": [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ],
            "pageLength": 10,
            "language": {
                "search": "<i class='fas fa-search'></i> Search:",
                "lengthMenu": "Show _MENU_ entries",
                "info": "Showing _START_ to _END_ of _TOTAL_ employees",
                "zeroRecords": "No matching records found",
                "paginate": {
                    "first": "<i class='fas fa-angle-double-left'></i>",
                    "last": "<i class='fas fa-angle-double-right'></i>",
                    "next": "<i class='fas fa-angle-right'></i>",
                    "previous": "<i class='fas fa-angle-left'></i>"
                }
            },
            "dom": '<"top d-flex justify-content-between"lf>rt<"bottom d-flex justify-content-between"ip><"clear">'
        });

        // Enhanced filter behavior
        $('#year, #department_id, #position_id').change(function() {
            // Show loading spinner
            $('body').append('<div class="position-fixed top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center bg-white bg-opacity-75" style="z-index: 9999;"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');

            // Submit form with short delay for better UX
            setTimeout(function() {
                $('#filter-form').submit();
            }, 300);
        });

        // Print functionality
        $('#printBtn').click(function() {
            window.print();
        });

        // Export functionality (placeholder - would require backend implementation)
        $('#exportBtn').click(function() {
            alert('Export functionality would be implemented on the server side. This is just a UI demonstration.');
            // Real implementation would be:
            // window.location.href = "{{ route('evaluation.report.elearning.export') }}?year=" + $('#year').val() + "&department_id=" + $('#department_id').val() + "&position_id=" + $('#position_id').val();
        });

        // Tooltip initialization
        const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltips.forEach(tooltip => {
            new bootstrap.Tooltip(tooltip);
        });

        // Add card hover effects with jQuery
        $('.card').hover(
            function() {
                $(this).addClass('shadow-lg').css('transition', 'all 0.3s');
            },
            function() {
                $(this).removeClass('shadow-lg').css('transition', 'all 0.3s');
            }
        );
    });
</script>

<style>
    /* Enhanced custom styles */
    .bg-gradient-primary {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    }

    .icon-circle {
        height: 60px;
        width: 60px;
        border-radius: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
    }

    .card {
        border-radius: 0.7rem;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
    }

    .progress {
        border-radius: 50px;
        overflow: hidden;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    .progress-bar {
        font-weight: 600;
    }

    .table thead th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.05em;
    }

    .badge {
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .btn {
        border-radius: 50px;
        padding-left: 1.5rem;
        padding-right: 1.5rem;
        transition: all 0.3s;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    /* Enhanced DataTables styling */
    .dataTables_wrapper .dataTables_filter input {
        border-radius: 50px;
        border: 1px solid #dee2e6;
        padding: 0.375rem 1rem;
    }

    .dataTables_wrapper .dataTables_length select {
        border-radius: 50px;
        border: 1px solid #dee2e6;
        padding: 0.375rem 1rem;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        border-radius: 50px !important;
        margin: 0 2px;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #4e73df !important;
        border-color: #4e73df !important;
        color: #fff !important;
    }

    /* Print styles */
    @media print {

        .no-print,
        .no-print * {
            display: none !important;
        }

        .card {
            border: 1px solid #ddd !important;
            box-shadow: none !important;
        }

        .progress {
            border: 1px solid #ddd;
        }

        .table td,
        .table th {
            background-color: transparent !important;
        }
    }
</style>
@endpush