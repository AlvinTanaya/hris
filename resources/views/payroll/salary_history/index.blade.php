@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    <div class="row">
        <div class="col-12 text-center mb-5">
            <h1 class="text-warning">
                <i class="fas fa-history me-2"></i> Salary Change History
            </h1>
        </div>
    </div>


    <!-- Filter Card -->
    <div class="card border-0 shadow mb-4">
        <div class="card-header bg-primary d-flex justify-content-between align-items-center p-3">
            <h4 class="text-white mb-0">
                <i class="fas fa-filter me-2"></i> Filter Options
            </h4>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('payroll.salary_history.index') }}" method="GET" id="filter-form">
                <div class="row g-3">
                    <!-- Name/ID Search -->
                    <div class="col-md-6 col-lg-4">
                        <label for="search" class="form-label">Name/ID</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="fas fa-search text-primary"></i></span>
                            <input type="text" class="form-control border-0 shadow-sm" id="search" name="search"
                                placeholder="Search employee..." value="{{ $search ?? '' }}">
                        </div>
                    </div>

                    <!-- Department Filter -->
                    <div class="col-md-6 col-lg-4">
                        <label for="department" class="form-label">Department</label>
                        <select class="form-select border-0 shadow-sm" id="department" name="department">
                            <option value="">All Departments</option>
                            @foreach($departments ?? [] as $dept)
                            <option value="{{ $dept->id }}" {{ ($department ?? '') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->department }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Position Filter -->
                    <div class="col-md-6 col-lg-4">
                        <label for="position" class="form-label">Position</label>
                        <select class="form-select border-0 shadow-sm" id="position" name="position">
                            <option value="">All Positions</option>
                            @foreach($positions ?? [] as $pos)
                            <option value="{{ $pos->id }}" {{ ($position ?? '') == $pos->id ? 'selected' : '' }}>
                                {{ $pos->position }}
                            </option>
                            @endforeach
                        </select>
                    </div>


                </div>

                <div class="row g-3 mt-1">
                    <!-- Start Date -->
                    <div class="col-md-6 col-lg-4">
                        <label for="date_start" class="form-label">Start Date</label>
                        <input type="date" class="form-control border-0 shadow-sm" id="date_start" name="date_start" value="{{ $dateStart ?? '' }}">
                    </div>

                    <!-- End Date -->
                    <div class="col-md-6 col-lg-4">
                        <label for="date_end" class="form-label">End Date</label>
                        <input type="date" class="form-control border-0 shadow-sm" id="date_end" name="date_end" value="{{ $dateEnd ?? '' }}">
                    </div>

                    <!-- Change Type Filter -->
                    <div class="col-md-6 col-lg-4">
                        <label for="filter" class="form-label">Change Type</label>
                        <select class="form-select border-0 shadow-sm" id="filter" name="filter">
                            <option value="all" {{ ($filter ?? 'all') == 'all' ? 'selected' : '' }}>All changes</option>
                            <option value="week" {{ ($filter ?? 'all') == 'week' ? 'selected' : '' }}>Past week</option>
                            <option value="month" {{ ($filter ?? 'all') == 'month' ? 'selected' : '' }}>Past month</option>
                            <option value="quarter" {{ ($filter ?? 'all') == 'quarter' ? 'selected' : '' }}>Past 3 months</option>
                            <option value="year" {{ ($filter ?? 'all') == 'year' ? 'selected' : '' }}>Past year</option>
                            <option value="increase" {{ ($filter ?? 'all') == 'increase' ? 'selected' : '' }}>Salary increases</option>
                            <option value="decrease" {{ ($filter ?? 'all') == 'decrease' ? 'selected' : '' }}>Salary decreases</option>
                            <option value="overtime" {{ ($filter ?? 'all') == 'overtime' ? 'selected' : '' }}>Overtime rate changes</option>
                            <option value="allowance" {{ ($filter ?? 'all') == 'allowance' ? 'selected' : '' }}>Allowance changes</option>
                        </select>
                    </div>


                </div>


                <div class="row g-3 mt-1">
                    <div class="col-md-6 offset-md-6 d-flex justify-content-end align-items-end">
                        <div class="d-flex gap-2 w-100 justify-content-end">
                            <a href="{{ route('payroll.salary_history.index') }}" class="btn btn-outline-secondary px-4 shadow-sm">
                                <i class="fas fa-redo me-2"></i> Reset
                            </a>
                            <button type="submit" class="btn btn-primary px-4 shadow-sm">
                                <i class="fas fa-filter me-2"></i> Apply Filters
                            </button>

                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>


    <!-- Stats Summary Row -->
    <div class="row mb-4">

        <div class="col-md-12 col-lg-12">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                            <i class="fas fa-history text-primary fa-lg"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0">Total Records</p>
                            <h4 class="mb-0">{{ number_format($stats['total_records'] ?? 0) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                            <i class="fas fa-arrow-up text-success fa-lg"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0">Salary Increases</p>
                            <h4 class="mb-0">{{ number_format($stats['increases'] ?? 0) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-danger bg-opacity-10 p-3 me-3">
                            <i class="fas fa-arrow-down text-danger fa-lg"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0">Salary Decreases</p>
                            <h4 class="mb-0">{{ number_format($stats['decreases'] ?? 0) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3">
                            <i class="fas fa-clock text-info fa-lg"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0">OT Rate Changes</p>
                            <h4 class="mb-0">{{ number_format($stats['overtime_changes'] ?? 0) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                            <i class="fas fa-money-bill-wave text-warning fa-lg"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0">Allowance Changes</p>
                            <h4 class="mb-0">{{ number_format($stats['allowance_changes'] ?? 0) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Main Content Card -->
    <div class="card border-0 shadow">
        <div class="card-header bg-primary d-flex justify-content-between align-items-center p-3">
            <div>
                <h4 class="text-white mb-0">
                    <i class="fas fa-file-alt me-2"></i> Salary Modification Records
                </h4>
            </div>
        </div>
        <div class="card-body">
            <!-- Table -->
            <div class="table-responsive">
                <table class="table align-middle table-hover" id="history-table">
                    <thead>
                        <tr class="bg-light">
                            <th class="text-uppercase text-primary fw-bold">EMPLOYEE</th>
                            <th class="text-uppercase text-primary fw-bold">POSITION/DEPARTMENT</th>
                            <th class="text-uppercase text-primary fw-bold">PREVIOUS SALARY</th>
                            <th class="text-uppercase text-primary fw-bold">NEW SALARY</th>
                            <th class="text-uppercase text-primary fw-bold">PREVIOUS OVERTIME PER HOUR</th>
                            <th class="text-uppercase text-primary fw-bold">NEW OVERTIME PER HOUR</th>
                            <th class="text-uppercase text-primary fw-bold">PREVIOUS ALLOWANCE</th>
                            <th class="text-uppercase text-primary fw-bold">NEW ALLOWANCE</th>
                            <th class="text-uppercase text-primary fw-bold">CHANGE DATE</th>
                            <th class="text-uppercase text-primary fw-bold text-end">DETAILS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($salaryHistories as $history)
                        <tr class="border-bottom">
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm rounded-circle bg-primary text-white me-3 d-flex align-items-center justify-content-center shadow-sm"
                                        style="width: 30px; height: 30px; font-size: 16px;">
                                        {{ substr($history->user->name, 0, 1) }}
                                    </div>

                                    <div>
                                        <h6 class="mb-0 fw-bold">{{ $history->user->name }}</h6>
                                        <p class="text-muted mb-0 small">{{ $history->user->employee_id }}</p>
                                    </div>
                                </div>
                            </td>
                            <!-- Instead of showing N/A -->

                            <td>
                                <div>
                                    <p class="mb-0 fw-semibold">{{ $history->effectivePosition->position  ?? 'N/A' }}</p>
                                    <p class="text-muted mb-0 small">{{ $history->effectiveDepartment->department  ?? 'N/A' }}</p>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="text-muted me-2">Rp</span>
                                    <span class="fw-semibold">{{ number_format($history->old_basic_salary, 0, ',', '.') }}</span>
                                </div>
                            </td>
                            <td>
                                @php
                                $salaryDifference = $history->new_basic_salary - $history->old_basic_salary;
                                $salaryPercentage = $history->old_basic_salary > 0 ?
                                round(($salaryDifference / $history->old_basic_salary) * 100, 1) : 0;
                                $salaryClass = $salaryDifference > 0 ? 'text-success' : ($salaryDifference < 0 ? 'text-danger' : 'text-muted' );
                                    $salaryIcon=$salaryDifference> 0 ? 'fa-arrow-up' : ($salaryDifference < 0 ? 'fa-arrow-down' : 'fa-minus' );
                                        @endphp
                                        <div class="d-flex align-items-center">
                                        <span class="text-muted me-2">Rp</span>
                                        <span class="fw-semibold">{{ number_format($history->new_basic_salary, 0, ',', '.') }}</span>
                                        <span class="badge {{ $salaryClass }} bg-opacity-10 ms-2 px-2">
                                            <i class="fas {{ $salaryIcon }} me-1"></i>
                                            {{ abs($salaryPercentage) }}%
                                        </span>
            </div>
            </td>
            <td>
                <div class="d-flex align-items-center">
                    <span class="text-muted me-2">Rp</span>
                    <span class="fw-semibold">{{ number_format($history->old_overtime_rate_per_hour, 0, ',', '.') }}</span>
                </div>
            </td>
            <td>
                @php
                $otDifference = $history->new_overtime_rate_per_hour - $history->old_overtime_rate_per_hour;
                $otPercentage = $history->old_overtime_rate_per_hour > 0 ?
                round(($otDifference / $history->old_overtime_rate_per_hour) * 100, 1) : 0;
                $otClass = $otDifference > 0 ? 'text-success' : ($otDifference < 0 ? 'text-danger' : 'text-muted' );
                    $otIcon=$otDifference> 0 ? 'fa-arrow-up' : ($otDifference < 0 ? 'fa-arrow-down' : 'fa-minus' );
                        @endphp
                        <div class="d-flex align-items-center">
                        <span class="text-muted me-2">Rp</span>
                        <span class="fw-semibold">{{ number_format($history->new_overtime_rate_per_hour, 0, ',', '.') }}</span>
                        <span class="badge {{ $otClass }} bg-opacity-10 ms-2 px-2">
                            <i class="fas {{ $otIcon }} me-1"></i>
                            {{ abs($otPercentage) }}%
                        </span>
        </div>
        </td>
        <td>
            <div class="d-flex align-items-center">
                <span class="text-muted me-2">Rp</span>
                <span class="fw-semibold">{{ number_format($history->old_allowance, 0, ',', '.') }}</span>
            </div>
        </td>
        <td>
            @php
            $allowanceDifference = $history->new_allowance - $history->old_allowance;
            $allowancePercentage = $history->old_allowance > 0 ?
            round(($allowanceDifference / $history->old_allowance) * 100, 1) : 0;
            $allowanceClass = $allowanceDifference > 0 ? 'text-success' : ($allowanceDifference < 0 ? 'text-danger' : 'text-muted' );
                $allowanceIcon=$allowanceDifference> 0 ? 'fa-arrow-up' : ($allowanceDifference < 0 ? 'fa-arrow-down' : 'fa-minus' );
                    @endphp
                    <div class="d-flex align-items-center">
                    <span class="text-muted me-2">Rp</span>
                    <span class="fw-semibold">{{ number_format($history->new_allowance, 0, ',', '.') }}</span>
                    <span class="badge {{ $allowanceClass }} bg-opacity-10 ms-2 px-2">
                        <i class="fas {{ $allowanceIcon }} me-1"></i>
                        {{ abs($allowancePercentage) }}%
                    </span>
    </div>
    </td>
    <td>
        <div class="d-flex align-items-center">
            <i class="fas fa-calendar-alt text-muted me-2"></i>
            <span>{{ $history->created_at->format('Y-m-d') }}</span>
            <small class="text-muted ms-2">{{ $history->created_at->format('H:i') }}</small>
        </div>
    </td>
    <td class="text-end">
        <button class="btn btn-sm btn-outline-primary rounded-pill view-details"
            data-id="{{ $history->id }}"
            data-user-name="{{ $history->user->name }}"
            data-old-salary="{{ $history->old_basic_salary }}"
            data-new-salary="{{ $history->new_basic_salary }}"
            data-old-ot="{{ $history->old_overtime_rate_per_hour }}"
            data-new-ot="{{ $history->new_overtime_rate_per_hour }}"
            data-old-allowance="{{ $history->old_allowance }}"
            data-new-allowance="{{ $history->new_allowance }}"
            data-date="{{ $history->created_at->format('Y-m-d H:i:s') }}">
            <i class="fas fa-eye"></i> Details
        </button>
    </td>
    </tr>
    @empty
    <tr>
        <td colspan="8" class="text-center py-5">
            <div class="empty-state">
                <i class="fas fa-history text-muted fa-3x mb-3"></i>
                <h5 class="text-muted">No salary history records found</h5>
                <p class="text-muted mb-0">Salary changes will appear here once modifications are made.</p>
            </div>
        </td>
    </tr>
    @endforelse
    </tbody>
    </table>
</div>
</div>


<!-- Salary History Detail Modal -->
<div class="modal fade" id="salaryDetailModal" tabindex="-1" aria-labelledby="salaryDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="salaryDetailModalLabel">
                    <i class="fas fa-file-invoice-dollar me-2"></i> Salary Change Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="employee-info mb-4 text-center">
                    <div class="avatar avatar-lg bg-primary text-white rounded-circle mb-3 mx-auto d-flex align-items-center justify-content-center"
                        style="width: 60px; height: 60px; font-size: 28px;">
                        <span class="employee-initial"></span>
                    </div>

                    <h5 class="fw-bold employee-name mb-1"></h5>
                    <p class="text-muted change-date mb-0 small"><i class="far fa-calendar-alt me-1"></i> <span></span></p>
                </div>

                <div class="row g-4">
                    <!-- Basic Salary Change -->
                    <div class="col-12">
                        <div class="card bg-light border-0">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">Basic Salary Change</h6>
                                <div class="row align-items-center">
                                    <div class="col-5">
                                        <p class="text-muted mb-1">Previous</p>
                                        <h5 class="old-salary fw-bold mb-0">Rp 0</h5>
                                    </div>
                                    <div class="col-2 text-center">
                                        <i class="fas fa-arrow-right text-primary"></i>
                                    </div>
                                    <div class="col-5">
                                        <p class="text-muted mb-1">New</p>
                                        <h5 class="new-salary fw-bold mb-0">Rp 0</h5>
                                    </div>
                                </div>
                                <div class="text-center mt-3">
                                    <span class="badge salary-change-badge"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Allowance Change -->
                    <div class="col-12">
                        <div class="card bg-light border-0">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">Allowance Change</h6>
                                <div class="row align-items-center">
                                    <div class="col-5">
                                        <p class="text-muted mb-1">Previous</p>
                                        <h5 class="old-allowance fw-bold mb-0">Rp 0</h5>
                                    </div>
                                    <div class="col-2 text-center">
                                        <i class="fas fa-arrow-right text-primary"></i>
                                    </div>
                                    <div class="col-5">
                                        <p class="text-muted mb-1">New</p>
                                        <h5 class="new-allowance fw-bold mb-0">Rp 0</h5>
                                    </div>
                                </div>
                                <div class="text-center mt-3">
                                    <span class="badge allowance-change-badge"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Overtime Rate Change -->
                    <div class="col-12">
                        <div class="card bg-light border-0">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">Overtime Rate Change (Per Hour)</h6>
                                <div class="row align-items-center">
                                    <div class="col-5">
                                        <p class="text-muted mb-1">Previous</p>
                                        <h5 class="old-ot fw-bold mb-0">Rp 0</h5>
                                    </div>
                                    <div class="col-2 text-center">
                                        <i class="fas fa-arrow-right text-primary"></i>
                                    </div>
                                    <div class="col-5">
                                        <p class="text-muted mb-1">New</p>
                                        <h5 class="new-ot fw-bold mb-0">Rp 0</h5>
                                    </div>
                                </div>
                                <div class="text-center mt-3">
                                    <span class="badge ot-change-badge"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection


<style>
    /* Global Styles */
    body {
        background-color: #f8f9fa;
    }

    /* Card Styles */
    .card {
        border-radius: 10px;
        overflow: hidden;
    }

    .card-header {
        border-bottom: 0;
    }

    /* Table Styles */
    .table th {
        font-size: 0.8rem;
        font-weight: 600;
        padding: 1rem;
        border-top: none;
        border-bottom: 1px solid #f0f0f0;
    }

    .table td {
        padding: 1rem;
        vertical-align: middle;
        border-color: #f0f0f0;
    }

    .table tr:hover {
        background-color: rgba(78, 115, 223, 0.03);
    }

    /* Avatar Styles */
    .avatar {
        width: 40px;
        height: 40px;
        font-size: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .avatar-lg {
        width: 64px;
        height: 64px;
        font-size: 1.5rem;
    }

    /* Pagination Styles */
    .page-item.active .page-link {
        background-color: #4e73df;
        border-color: #4e73df;
    }

    .page-link {
        color: #4e73df;
        border-radius: 5px;
        margin: 0 2px;
    }

    .page-link:hover {
        background-color: #eaecf4;
    }

    /* Button Styles */
    .btn-outline-primary {
        color: #4e73df;
        border-color: #4e73df;
    }

    .btn-outline-primary:hover {
        background-color: #4e73df;
        color: white;
    }

    .btn-primary {
        background-color: #4e73df;
        border-color: #4e73df;
    }

    .btn-primary:hover {
        background-color: #2e59d9;
        border-color: #2e59d9;
    }

    /* Form Styles */
    .form-control:focus,
    .form-select:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
    }

    .form-control,
    .form-select,
    .input-group-text {
        padding: 0.5rem 1rem;
    }

    /* Empty State Styles */
    .empty-state {
        padding: 2rem;
        text-align: center;
    }

    /* Modal Styles */
    .modal-content {
        border-radius: 10px;
    }

    /* Badge Styles */
    .badge {
        font-weight: 500;
        padding: 0.5rem 1rem;
        border-radius: 30px;
    }

    .text-success {
        color: #1cc88a !important;
    }

    .bg-success.bg-opacity-10 {
        background-color: rgba(28, 200, 138, 0.1) !important;
    }

    .text-danger {
        color: #e74a3b !important;
    }

    .bg-danger.bg-opacity-10 {
        background-color: rgba(231, 74, 59, 0.1) !important;
    }

    /* Additional Responsive Tweaks */
    @media (max-width: 768px) {
        .btn-group .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
    }

    /* Additional CSS fixes for responsive layout */

    /* Improve button visibility in collapsed view */
    .view-details {
        position: relative;
        z-index: 100;
        transition: all 0.2s ease;
    }

    .view-details:hover {
        background-color: #4e73df;
        color: white;
    }

    /* Ensure columns don't get too narrow on smaller screens */
    @media (max-width: 992px) {
        .table-responsive {
            overflow-x: auto;
        }

        /* Make the column with the details button fixed width */
        #history-table th:last-child,
        #history-table td:last-child {
            min-width: 100px;
            width: 100px;
            text-align: center;
        }

        /* Ensure the button is clearly visible */
        .view-details {
            padding: 6px 12px;
            width: auto;
            display: inline-block;
        }
    }

    /* Fix for very small screens */
    @media (max-width: 576px) {

        #history-table td:nth-child(5),
        #history-table td:nth-child(6),
        #history-table td:nth-child(7),
        #history-table td:nth-child(8),
        #history-table th:nth-child(5),
        #history-table th:nth-child(6),
        #history-table th:nth-child(7),
        #history-table th:nth-child(8) {
            display: none;
        }

        .view-details {
            margin: 0;
            padding: 4px 8px;
            font-size: 12px;
        }
    }
</style>


@push('scripts')
<script>
    $(document).ready(function() {
        $('#history-table').DataTable({
            responsive: true,
            autoWidth: false,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search records...",
                lengthMenu: "_MENU_ records per page",
                info: "Showing _START_ to _END_ of _TOTAL_ records",
                infoEmpty: "Showing 0 to 0 of 0 records",
                infoFiltered: "(filtered from _MAX_ total records)"
            },
            dom: '<"row"<"col-md-6"l><"col-md-6"f>>rtip',
            pagingType: "full_numbers",
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ],
            // Remove default sorting to keep the server-side ordering
            order: []
        });

        // Remove the auto-submit on per_page change since we're using DataTables now
        $('#per_page').off('change');




        // Use event delegation to handle clicks on the details button
        $(document).on('click', '.view-details', function(e) {
            e.preventDefault(); // Prevent default behavior
            e.stopPropagation(); // Stop event bubbling

            console.log("Details button clicked");

            // Get data attributes
            const id = $(this).data('id');
            const userName = $(this).data('user-name');
            const oldSalary = $(this).data('old-salary');
            const newSalary = $(this).data('new-salary');
            const oldOt = $(this).data('old-ot');
            const newOt = $(this).data('new-ot');
            const oldAllowance = $(this).data('old-allowance');
            const newAllowance = $(this).data('new-allowance');
            const date = $(this).data('date');

            // Calculate differences and percentages
            const salaryDiff = newSalary - oldSalary;
            const salaryPercentage = oldSalary > 0 ? ((salaryDiff / oldSalary) * 100).toFixed(1) : 0;

            const otDiff = newOt - oldOt;
            const otPercentage = oldOt > 0 ? ((otDiff / oldOt) * 100).toFixed(1) : 0;

            // Calculate allowance difference and percentage
            const allowanceDiff = newAllowance - oldAllowance;
            const allowancePercentage = oldAllowance > 0 ? ((allowanceDiff / oldAllowance) * 100).toFixed(1) : 0;

            // Set modal content
            $('.employee-initial').text(userName.charAt(0));
            $('.employee-name').text(userName);
            $('.change-date span').text(date);

            // Format and set salary values
            $('.old-salary').text('Rp ' + numberWithCommas(oldSalary));
            $('.new-salary').text('Rp ' + numberWithCommas(newSalary));

            // Format and set overtime rate values
            $('.old-ot').text('Rp ' + numberWithCommas(oldOt));
            $('.new-ot').text('Rp ' + numberWithCommas(newOt));

            // Format and set allowance values
            $('.old-allowance').text('Rp ' + numberWithCommas(oldAllowance));
            $('.new-allowance').text('Rp ' + numberWithCommas(newAllowance));

            // Set salary change badge
            if (salaryDiff > 0) {
                $('.salary-change-badge')
                    .removeClass('bg-danger bg-secondary')
                    .addClass('bg-success')
                    .html(`<i class="fas fa-arrow-up me-1"></i> Increased by ${Math.abs(salaryPercentage)}% (Rp ${numberWithCommas(Math.abs(salaryDiff))})`);
            } else if (salaryDiff < 0) {
                $('.salary-change-badge')
                    .removeClass('bg-success bg-secondary')
                    .addClass('bg-danger')
                    .html(`<i class="fas fa-arrow-down me-1"></i> Decreased by ${Math.abs(salaryPercentage)}% (Rp ${numberWithCommas(Math.abs(salaryDiff))})`);
            } else {
                $('.salary-change-badge')
                    .removeClass('bg-success bg-danger')
                    .addClass('bg-secondary')
                    .html(`<i class="fas fa-minus me-1"></i> No change`);
            }

            // Set overtime rate change badge
            if (otDiff > 0) {
                $('.ot-change-badge')
                    .removeClass('bg-danger bg-secondary')
                    .addClass('bg-success')
                    .html(`<i class="fas fa-arrow-up me-1"></i> Increased by ${Math.abs(otPercentage)}% (Rp ${numberWithCommas(Math.abs(otDiff))})`);
            } else if (otDiff < 0) {
                $('.ot-change-badge')
                    .removeClass('bg-success bg-secondary')
                    .addClass('bg-danger')
                    .html(`<i class="fas fa-arrow-down me-1"></i> Decreased by ${Math.abs(otPercentage)}% (Rp ${numberWithCommas(Math.abs(otDiff))})`);
            } else {
                $('.ot-change-badge')
                    .removeClass('bg-success bg-danger')
                    .addClass('bg-secondary')
                    .html(`<i class="fas fa-minus me-1"></i> No change`);
            }

            // Set allowance change badge
            if (allowanceDiff > 0) {
                $('.allowance-change-badge')
                    .removeClass('bg-danger bg-secondary')
                    .addClass('bg-success')
                    .html(`<i class="fas fa-arrow-up me-1"></i> Increased by ${Math.abs(allowancePercentage)}% (Rp ${numberWithCommas(Math.abs(allowanceDiff))})`);
            } else if (allowanceDiff < 0) {
                $('.allowance-change-badge')
                    .removeClass('bg-success bg-secondary')
                    .addClass('bg-danger')
                    .html(`<i class="fas fa-arrow-down me-1"></i> Decreased by ${Math.abs(allowancePercentage)}% (Rp ${numberWithCommas(Math.abs(allowanceDiff))})`);
            } else {
                $('.allowance-change-badge')
                    .removeClass('bg-success bg-danger')
                    .addClass('bg-secondary')
                    .html(`<i class="fas fa-minus me-1"></i> No change`);
            }

            // Show the modal
            $('#salaryDetailModal').modal('show');
        });

        // Helper function to format numbers with commas
        function numberWithCommas(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }



        // Form filter behavior - auto-submit when per_page changes
        $('#per_page').change(function() {
            $('#filter-form').submit();
        });

        // Department filter change - populate position dropdown accordingly
        $('#department').change(function() {
            const departmentId = $(this).val();
            if (departmentId) {
                // Fetch positions for the selected department
                $.ajax({
                    url: `/api/departments/${departmentId}/positions`,
                    type: 'GET',
                    success: function(data) {
                        $('#position').empty();
                        $('#position').append('<option value="">All Positions</option>');
                        $.each(data, function(index, position) {
                            $('#position').append(`<option value="${position.id}">${position.name}</option>`);
                        });
                    }
                });
            } else {
                // Reset position dropdown if no department selected
                $('#position').empty();
                $('#position').append('<option value="">All Positions</option>');
                @foreach($positions ?? [] as $pos)
                $('#position').append(`<option value="{{ $pos->id }}">{{ $pos->name }}</option>`);
                @endforeach
            }
        });

        // Helper function to format numbers with commas
        function numberWithCommas(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        // Date range validation
        $('#date_start, #date_end').change(function() {
            const startDate = $('#date_start').val();
            const endDate = $('#date_end').val();

            if (startDate && endDate && startDate > endDate) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Date Range',
                    text: 'Start date cannot be after end date.',
                    confirmButtonColor: '#4e73df'
                });
                $(this).val('');
            }
        });
    });
</script>
@endpush