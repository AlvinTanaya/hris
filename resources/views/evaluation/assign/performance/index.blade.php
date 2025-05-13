@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <!-- Filter Card -->
        <div class="col-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-filter me-2"></i>Filter Evaluations
                        </h5>

                    </div>
                </div>
                <div class="card-body collapse show" id="filterCollapse">
                    <form action="{{ route('evaluation.assign.performance.index', Auth::user()->id) }}" method="GET">
                        <div class="row g-3">
                            <!-- Employee Filter -->
                            <div class="col-md-4">
                                <label for="filter-name" class="form-label small text-muted">Employee</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-user"></i></span>
                                    <select id="filter-name" name="employee" class="form-select">
                                        <option value="">All Employees</option>
                                        @foreach($employeesList as $emp)
                                        <option value="{{ $emp->id }}" {{ request('employee') == $emp->id ? 'selected' : '' }}>
                                            {{ $emp->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Position Filter -->
                            <div class="col-md-4">
                                <label for="filter-position" class="form-label small text-muted">Position</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-briefcase"></i></span>
                                    <select id="filter-position" name="position" class="form-select">
                                        <option value="">All Positions</option>
                                        @foreach($positionsList as $position)
                                        <option value="{{ $position->id }}" {{ request('position') == $position->id ? 'selected' : '' }}>
                                            {{ $position->position }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Department Filter -->
                            <div class="col-md-4">
                                <label for="filter-department" class="form-label small text-muted">Department</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-building"></i></span>
                                    <select id="filter-department" name="department" class="form-select">
                                        <option value="">All Departments</option>
                                        @foreach($departmentsList as $department)
                                        <option value="{{ $department->id }}" {{ request('department') == $department->id ? 'selected' : '' }}>
                                            {{ $department->department }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Month Filter -->
                            <div class="col-md-4">
                                <label for="filter-month" class="form-label small text-muted">Month</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-calendar-alt"></i></span>
                                    <select id="filter-month" name="month" class="form-select">
                                        <option value="">All Months</option>
                                        @foreach(range(1, 12) as $month)
                                        <option value="{{ $month }}" {{ (request('month', $currentMonth) == $month) ? 'selected' : '' }}>
                                            {{ date('F', mktime(0, 0, 0, $month, 1)) }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Year Filter -->
                            <div class="col-md-4">
                                <label for="filter-year" class="form-label small text-muted">Year</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-calendar"></i></span>
                                    <select id="filter-year" name="year" class="form-select">
                                        <option value="">All Years</option>
                                        @foreach($availableYears as $year)
                                        <option value="{{ $year }}" {{ (request('year', $currentYear) == $year) ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2 flex-grow-1">
                                    <i class="fas fa-search me-1"></i> Apply Filters
                                </button>
                                <a href="{{ route('evaluation.assign.performance.index', Auth::user()->id) }}" class="btn btn-outline-secondary flex-grow-1">
                                    <i class="fas fa-redo me-1"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Data Table Card -->
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom-0 pb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-clipboard-list text-primary me-2"></i>Performance Evaluations Assigned
                        </h5>
                        <a href="{{ route('evaluation.assign.performance.create', Auth::user()->id) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i> Assign New
                        </a>
                    </div>
                </div>

                <div class="card-body pt-0">
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <div class="table-responsive">
                        <table id="evaluations-table" class="table table-hover table-bordered w-100">
                            <thead class="bg-light">
                                <tr>
                                    <th width="5%">#</th>
                                    <th>Employee</th>
                                    <th>Position</th>
                                    <th>Department</th>
                                    <th>Period</th>
                                    <th width="10%">Score</th>
                                    <th>Evaluator</th>
                                    <th width="15%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($evaluations->count() > 0)
                                @foreach($evaluations as $key => $evaluation)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-initial rounded-circle p-2 bg-primary text-white">
                                                    {{ substr($evaluation->user->name ?? 'N/A', 0, 1) }}
                                                </span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $evaluation->user->name ?? 'N/A' }}</h6>
                                                <small class="text-muted">{{ $evaluation->user->email ?? '' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info-light text-dark">
                                            {{ $evaluation->user->position->position ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>{{ $evaluation->user->department->department ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            {{ \Carbon\Carbon::parse($evaluation->date)->format('F Y') }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                        $score = $evaluation->final_score;
                                        $scoreClass = $score >= 80 ? 'success' : ($score >= 60 ? 'warning' : 'danger');
                                        @endphp
                                        <span class="badge bg-{{ $scoreClass }} rounded-pill">
                                            {{ fmod($score, 1) == 0 ? number_format($score, 0) : $score }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-xs me-2">
                                                <span class="avatar-initial rounded-circle p-2 bg-secondary text-white">
                                                    {{ substr($evaluation->evaluator->name ?? 'N/A', 0, 1) }}
                                                </span>
                                            </div>
                                            {{ $evaluation->evaluator->name ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            @if(Auth::id() == $evaluation->evaluator_id)
                                            <a href="{{ route('evaluation.assign.performance.edit', $evaluation->id) }}"
                                                class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endif
                                            <a href="{{ route('evaluation.assign.performance.detail', $evaluation->id) }}"
                                                class="btn btn-sm btn-outline-info me-1" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="empty-state">
                                            <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                                            <h4>No evaluations found</h4>
                                            <p class="text-muted">There are no evaluations matching your criteria</p>
                                            <a href="{{ route('evaluation.assign.performance.create', Auth::user()->id) }}" class="btn btn-primary mt-2">
                                                <i class="fas fa-plus me-1"></i> Assign New Evaluation
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap5.min.css">
<style>
    .card {
        border-radius: 0.5rem;
        border: none;
    }

    .card-header {
        border-radius: 0.5rem 0.5rem 0 0 !important;
    }

    .table {
        font-size: 0.875rem;
    }

    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }

    .avatar {
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .avatar-initial {
        font-weight: 600;
    }

    .avatar-sm {
        width: 32px;
        height: 32px;
        font-size: 0.875rem;
    }

    .avatar-xs {
        width: 24px;
        height: 24px;
        font-size: 0.75rem;
    }

    .empty-state {
        padding: 2rem;
        text-align: center;
    }

    .badge {
        font-weight: 500;
        padding: 0.35em 0.65em;
    }

    .bg-success-light {
        background-color: rgba(40, 167, 69, 0.1) !important;
        color: #28a745 !important;
    }

    .bg-warning-light {
        background-color: rgba(255, 193, 7, 0.1) !important;
        color: #ffc107 !important;
    }

    .bg-danger-light {
        background-color: rgba(220, 53, 69, 0.1) !important;
        color: #dc3545 !important;
    }

    .bg-info-light {
        background-color: rgba(23, 162, 184, 0.1) !important;
        color: #17a2b8 !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize DataTable with enhanced features
        var table = $('#evaluations-table').DataTable({
            dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search evaluations...",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "No entries found",
                infoFiltered: "(filtered from _MAX_ total entries)",
                paginate: {
                    previous: "<i class='fas fa-chevron-left'></i>",
                    next: "<i class='fas fa-chevron-right'></i>"
                }
            },
            responsive: true,
            columnDefs: [{
                    orderable: false,
                    targets: [7]
                }, // Disable sorting on action column
                {
                    responsivePriority: 1,
                    targets: 1
                }, // Employee name
                {
                    responsivePriority: 2,
                    targets: 7
                }, // Actions
                {
                    responsivePriority: 3,
                    targets: 5
                } // Score
            ],
            initComplete: function() {}
        });

        // Apply filters when dropdowns change
        $('.form-select').on('change', function() {
            table.draw();
        });

        // Custom filtering function
        $.fn.dataTable.ext.search.push(
            function(settings, data, dataIndex) {
                var employee = $('#filter-name').val();
                var position = $('#filter-position').val();
                var department = $('#filter-department').val();
                var month = $('#filter-month').val();
                var year = $('#filter-year').val();

                // Employee filter
                if (employee !== '' && data[1].indexOf(employee) === -1) {
                    return false;
                }

                // Position filter
                if (position !== '' && data[2].indexOf(position) === -1) {
                    return false;
                }

                // Department filter
                if (department !== '' && data[3].indexOf(department) === -1) {
                    return false;
                }

                // Date filter
                if (month !== '' && year !== '') {
                    var dateStr = data[4];
                    var dateParts = dateStr.split(' ');
                    if (dateParts.length === 2) {
                        var rowMonth = new Date(dateParts[0] + ' 1, ' + dateParts[1]).getMonth() + 1;
                        var rowYear = dateParts[1];

                        if (parseInt(month) !== rowMonth || year !== rowYear) {
                            return false;
                        }
                    }
                }

                return true;
            }
        );
    });
</script>
@endpush