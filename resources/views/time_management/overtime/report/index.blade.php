@extends('layouts.app')

@section('content')


<div class="container-fluid py-4 overtime-container">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-header d-flex align-items-center justify-content-between">
                <h1 class="mb-0 text-primary">
                    <i class="fas fa-chart-line me-2"></i> Overtime Reports
                </h1>
                <div class="d-flex align-items-center">
                    <button class="btn btn-primary me-2" id="printReportBtn">
                        <i class="fas fa-print me-1"></i> Print Report
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card summary-card border-left-primary h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Approved Requests</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $approvedRequests->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card summary-card border-left-success h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Hours</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalHours, 1) }} hours</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card summary-card border-left-info h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Payment</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp{{ number_format($totalPayment, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card summary-card border-left-warning h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Avg. Payment/Request</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp{{ number_format($avgPaymentPerRequest, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calculator fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card filter-card mb-4">
        <div class="card-header bg-gradient-primary text-white">
            <h5 class="mb-0 d-flex align-items-center">
                <i class="fas fa-filter me-2"></i> Filter Reports
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('overtime.report.index') }}" method="GET" id="filterForm" class="m-0">
                <div class="row g-3">
                    <div class="col-md-4 col-lg-3">
                        <label for="employee" class="form-label">Employee</label>
                        <select class="form-select" name="employee" id="employee">
                            <option value="all">All Employees</option>
                            @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ request('employee') == $emp->id ? 'selected' : '' }}>
                                {{ $emp->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 col-lg-3">
                        <label for="department_request" class="form-label">Department</label>
                        <select class="form-select" name="department_request" id="department_request">
                            <option value="">All Departments</option>
                            @foreach($departments as $dept)
                            <option value="{{ $dept }}" {{ request('department_request') == $dept ? 'selected' : '' }}>
                                {{ $dept }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 col-lg-3">
                        <label for="position_request" class="form-label">Position</label>
                        <select class="form-select" name="position_request" id="position_request">
                            <option value="">All Positions</option>
                            @foreach($positions as $pos)
                            <option value="{{ $pos }}" {{ request('position_request') == $pos ? 'selected' : '' }}>
                                {{ $pos }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 col-lg-3">
                        <label for="approved_by" class="form-label">Approved By</label>
                        <select class="form-select" name="approved_by" id="approved_by">
                            <option value="">Any Approval</option>
                            <option value="dept" {{ request('approved_by') == 'dept' ? 'selected' : '' }}>Department Head</option>
                            <option value="admin" {{ request('approved_by') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="both" {{ request('approved_by') == 'both' ? 'selected' : '' }}>Both</option>
                        </select>
                    </div>

                    <div class="col-md-4 col-lg-3">
                        <label for="date_start" class="form-label">Date From</label>
                        <input type="date" class="form-control" name="date_start" id="date_start" value="{{ request('date_start') }}">
                    </div>

                    <div class="col-md-4 col-lg-3">
                        <label for="date_end" class="form-label">Date To</label>
                        <input type="date" class="form-control" name="date_end" id="date_end" value="{{ request('date_end') }}">
                    </div>

                    <div class="col-md-4 col-lg-3">
                        <label for="min_amount" class="form-label">Min Payment</label>
                        <input type="number" class="form-control" name="min_amount" id="min_amount" placeholder="Min amount" value="{{ request('min_amount') }}">
                    </div>

                    <div class="col-md-4 col-lg-3">
                        <label for="max_amount" class="form-label">Max Payment</label>
                        <input type="number" class="form-control" name="max_amount" id="max_amount" placeholder="Max amount" value="{{ request('max_amount') }}">
                    </div>

                    <div class="col-md-12 d-flex align-items-end">
                        <div class="d-flex w-100 justify-content-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-filter me-1"></i> Apply Filters
                            </button>
                            <a href="{{ route('overtime.report.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-redo me-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Charts and Data Analysis Section -->
    <div class="row mb-4">
        <!-- Department Breakdown Chart -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-building me-2"></i> Department Breakdown</h5>
                </div>
                <div class="card-body">
                    <canvas id="departmentBreakdownChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Monthly Overtime Chart -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i> Monthly Overtime</h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyOvertimeChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Employees Table -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-trophy me-2"></i> Top Employees by Overtime</h5>
                    <span class="badge bg-primary">Top 5</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>Employee</th>
                                    <th>Total Requests</th>
                                    <th>Total Hours</th>
                                    <th>Total Payment</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($employeeSummary as $employee => $data)
                                <tr>
                                    <td>{{ $employee }}</td>
                                    <td>{{ $data['count'] }}</td>
                                    <td>{{ number_format($data['total_hours'], 1) }} hours</td>
                                    <td>Rp{{ number_format($data['total_payment'], 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Report Table -->
    <div class="card mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0 d-flex align-items-center">
                <i class="fas fa-table me-2 text-primary"></i> Detailed Overtime Report
            </h5>
            <span class="badge bg-primary">
                Showing {{ $approvedRequests->count() }} approved requests
            </span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="overtimeReportTable" class="table table-hover w-100">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Department</th>
                            <th>Position</th>
                            <th>Date</th>
                            <th>Hours</th>
                            <th>Rate/Hour</th>
                            <th>Total Payment</th>
                            <th>Approved By</th>
                            <th>Reason</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($approvedRequests as $request)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar me-2">
                                        <img src="{{ $request->user->photo_profile_path ? asset('storage/' . $request->user->photo_profile_path) : asset('storage/default_profile.png') }}"
                                            alt="{{ $request->employee_name }}"
                                            class="employee-avatar rounded-circle"
                                            style="width: 35px; height: 35px; object-fit: cover; border: 2px solid #4361ee;">
                                    </div>
                                    <div>{{ $request->employee_name }}</div>
                                </div>
                            </td>
                            <td>{{ $request->employee_department }}</td>
                            <td>{{ $request->employee_position }}</td>
                            <td>{{ \Carbon\Carbon::parse($request->date)->format('d M Y') }}</td>
                            <td>{{ $request->total_hours }} hours</td>
                            <td>Rp{{ number_format($request->overtime_rate, 0, ',', '.') }}</td>
                            <td>Rp{{ number_format($request->overtime_payment, 0, ',', '.') }}</td>
                            <td>
                                <div class="d-flex flex-column gap-2">
                                    @if($request->dept_approved_by)
                                    <span class="badge bg-light text-dark">
                                        <i class="fas fa-user-tie me-1"></i>
                                        Dept: {{ $request->dept_approved_by }}
                                    </span>
                                    @endif
                                    @if($request->admin_approved_by)
                                    <span class="badge bg-light text-dark">
                                        <i class="fas fa-user-shield me-1"></i>
                                        Admin: {{ $request->admin_approved_by }}
                                    </span>
                                    @endif
                                </div>
                            </td>
                            <td>{{ Str::limit($request->reason, 30) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Department Summary Table -->
    <div class="card mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-sitemap me-2 text-primary"></i> Department Summary</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Department</th>
                            <th>Total Requests</th>
                            <th>Total Hours</th>
                            <th>Total Payment</th>
                            <th>Avg Payment/Request</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($departmentSummary as $department => $data)
                        <tr>
                            <td>{{ $department }}</td>
                            <td>{{ $data['count'] }}</td>
                            <td>{{ number_format($data['total_hours'], 1) }} hours</td>
                            <td>Rp{{ number_format($data['total_payment'], 0, ',', '.') }}</td>
                            <td>Rp{{ number_format($data['count'] > 0 ? $data['total_payment'] / $data['count'] : 0, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
<style>
    /* Custom CSS for Overtime Report */
    .overtime-container {
        /* Color Variables */
        --primary-color: #4361ee;
        --secondary-color: #3f37c9;
        --success-color: #4cc9f0;
        --warning-color: #f8961e;
        --danger-color: #f72585;
        --light-bg: #f8f9fa;
        --dark-bg: #212529;
        --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    .page-header {
        padding: 1rem;
        background: white;
        border-radius: 0.5rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .summary-card {
        border-radius: 0.5rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        border-left: 4px solid !important;
    }

    .summary-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
    }

    .filter-card {
        border-radius: 0.5rem;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    }

    .filter-card .card-header {
        padding: 1rem 1.5rem;
    }

    .card {
        border: none;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        padding: 1rem 1.5rem;
        background: white;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .table-responsive {
        border-radius: 0.5rem;
        overflow: hidden;
    }

    table {
        margin-bottom: 0 !important;
    }

    .table thead th {
        background-color: #f8fafc;
        border-bottom-width: 1px;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(67, 97, 238, 0.05);
    }

    .employee-avatar {
        transition: all 0.3s ease;
    }

    .employee-avatar:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .badge {
        font-weight: 500;
        padding: 0.35em 0.65em;
    }

    .bg-gradient-primary {
        background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
    }

    /* Perbaikan untuk bar chart yang memanjang ke bawah */
    .recharts-wrapper {
        height: 400px !important;
        max-height: 400px !important;
        overflow: hidden;
    }

    .recharts-surface {
        overflow: visible;
    }

    /* Container untuk chart */
    .chart-container {
        height: 400px;
        max-height: 400px;
        overflow: hidden;
    }

    /* Custom scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    ::-webkit-scrollbar-thumb {
        background: #4361ee;
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #3a0ca3;
    }

    /* Animation for cards */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .card,
    .summary-card {
        animation: fadeIn 0.5s ease forwards;
    }

    /* Delay animations for each card */
    .summary-card:nth-child(1) {
        animation-delay: 0.1s;
    }

    .summary-card:nth-child(2) {
        animation-delay: 0.2s;
    }

    .summary-card:nth-child(3) {
        animation-delay: 0.3s;
    }

    .summary-card:nth-child(4) {
        animation-delay: 0.4s;
    }

    /* Custom checkbox for filters */
    .form-check-input:checked {
        background-color: #4361ee;
        border-color: #4361ee;
    }

    /* Custom select styling */
    .form-select {
        border-radius: 0.375rem;
        border: 1px solid #dee2e6;
        transition: all 0.3s;
    }

    .form-select:focus {
        border-color: #4361ee;
        box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
    }

    /* Button styling */
    .btn {
        border-radius: 0.375rem;
        font-weight: 500;
        transition: all 0.3s;
    }

    .btn-primary {
        background-color: #4361ee;
        border-color: #4361ee;
    }

    .btn-primary:hover {
        background-color: #3a0ca3;
        border-color: #3a0ca3;
    }

    .btn-outline-secondary {
        border-color: #dee2e6;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .page-header>div {
            margin-top: 1rem;
        }
    }

    /* Tambahan: mengatur chart container */
    .monthly-overtime-chart {
        height: 400px;
        position: relative;
    }

    /* Tambahan: mengatur grafik bar chart agar tidak memanjang ke bawah */
    .recharts-responsive-container {
        height: 100% !important;
        min-height: 400px !important;
    }

    /* Agar konten chart tidak overflow */
    .recharts-wrapper,
    .recharts-surface,
    .recharts-layer {
        overflow: visible !important;
    }

    /* Membatasi tinggi bar */
    .recharts-bar {
        max-height: 350px;
    }
</style>

@push('scripts')
<!-- Latest DataTables CSS and JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    $(document).ready(function() {
        // Initialize DataTable with modern styling
        const dataTableConfig = {
            responsive: true,
            processing: true,
            lengthMenu: [10, 25, 50, 100],
            dom: '<"top"<"row align-items-center"<"col-md-6"l><"col-md-6"f>>>rt<"bottom"<"row align-items-center"<"col-md-6"i><"col-md-6"p>><"clear">>',
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search...",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "Showing 0 to 0 of 0 entries",
                infoFiltered: "(filtered from _MAX_ total entries)",
                paginate: {
                    first: '<i class="fas fa-angle-double-left"></i>',
                    last: '<i class="fas fa-angle-double-right"></i>',
                    next: '<i class="fas fa-angle-right"></i>',
                    previous: '<i class="fas fa-angle-left"></i>'
                }
            },
            initComplete: function() {},
            drawCallback: function() {}
        };

        $('#overtimeReportTable').DataTable(dataTableConfig);

        // Print Report
        $('#printReportBtn').click(function() {
            window.print();
        });

        // Department Breakdown Chart
        const departmentCtx = document.getElementById('departmentBreakdownChart').getContext('2d');
        const departmentLabels = @json($departmentSummary -> keys());
        const departmentData = @json($departmentSummary -> values() -> pluck('total_payment'));

        new Chart(departmentCtx, {
            type: 'doughnut',
            data: {
                labels: departmentLabels,
                datasets: [{
                    data: departmentData,
                    backgroundColor: [
                        '#4361ee', '#3a0ca3', '#4895ef', '#4cc9f0', '#f72585',
                        '#b5179e', '#7209b7', '#560bad', '#480ca8', '#3a0ca3'
                    ],
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            boxWidth: 12,
                            padding: 20,
                            font: {
                                family: "'Nunito', sans-serif"
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += 'Rp' + new Intl.NumberFormat('id-ID').format(context.raw);
                                return label;
                            }
                        },
                        bodyFont: {
                            family: "'Nunito', sans-serif",
                            size: 14
                        },
                        padding: 10
                    }
                }
            }
        });

        // Monthly Overtime Chart
        const monthlyCtx = document.getElementById('monthlyOvertimeChart').getContext('2d');
        const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        const monthlyPayments = [];
        const monthlyHours = [];
        @foreach($monthlyData as $month => $data)
            monthlyPayments.push({{$data['total_payment']}});
            monthlyHours.push({{$data['total_hours']}});
        @endforeach

        new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: monthNames,
                datasets: [{
                        label: 'Payment (Rp)',
                        backgroundColor: 'rgba(67, 97, 238, 0.7)',
                        borderColor: 'rgba(67, 97, 238, 1)',
                        borderWidth: 1,
                        borderRadius: 4,
                        data: monthlyPayments,
                        yAxisID: 'y-axis-1',
                    },
                    {
                        label: 'Hours',
                        backgroundColor: 'rgba(76, 201, 240, 0.7)',
                        borderColor: 'rgba(76, 201, 240, 1)',
                        borderWidth: 1,
                        borderRadius: 4,
                        data: monthlyHours,
                        yAxisID: 'y-axis-2',
                    }
                ]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    'y-axis-1': {
                        type: 'linear',
                        position: 'left',
                        grid: {
                            drawOnChartArea: false,
                        },
                        ticks: {
                            callback: function(value) {
                                return 'Rp' + new Intl.NumberFormat('id-ID').format(value);
                            }
                        }
                    },
                    'y-axis-2': {
                        type: 'linear',
                        position: 'right',
                        grid: {
                            drawOnChartArea: false,
                        },
                        ticks: {
                            callback: function(value) {
                                return value + ' hrs';
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: {
                                family: "'Nunito', sans-serif"
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.dataset.yAxisID === 'y-axis-1') {
                                    label += 'Rp' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                                } else {
                                    label += context.parsed.y + ' hours';
                                }
                                return label;
                            }
                        },
                        bodyFont: {
                            family: "'Nunito', sans-serif"
                        }
                    }
                }
            }
        });
    });
</script>
@endpush