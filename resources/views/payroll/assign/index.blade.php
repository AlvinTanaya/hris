@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <!-- Page Header with Gradient Background -->
    <div class="row">
        <div class="col-12">
            <div class="page-header-box bg-light p-4 mb-4 rounded-3 shadow-sm">
                <div class="d-flex align-items-center">
                    <i class="fas fa-money-check-alt fa-3x text-white me-3"></i>
                    <div>
                        <h1 class="text-white mb-0">Employee Payroll Management</h1>
                        <p class="text-white-50 mb-0">Manage and process employee payrolls efficiently</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards with Icons -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Payrolls</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $payrolls->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-invoice-dollar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Employees</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $employees->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Current Month</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ date('F Y') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Uploads</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingUploads ?? 4 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-cloud-upload-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter me-1"></i> Filter Payroll Data
            </h6>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="dropdownMenuLink">
                    <li><a class="dropdown-item" href="#" id="clear-all-filters">Clear all filters</a></li>
                    <li><a class="dropdown-item" href="#" id="save-filter-preset">Save current filter</a></li>
                </ul>
            </div>
        </div>
        <div class="card-body">
            <form id="filterForm" method="GET">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="month_year" class="form-label small text-muted">Month & Year</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                            <input type="month" class="form-control" id="month_year" name="month_year" value="{{ request('month_year') }}">
                            <button type="button" class="btn btn-outline-secondary" id="clear-month" title="Clear month filter">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <small class="text-muted">Leave empty to show all periods</small>
                    </div>

                    <div class="col-md-3">
                        <label for="department_id" class="form-label small text-muted">Department</label>
                        <select class="form-control select2" id="department_id" name="department_id">
                            <option value="">All Departments</option>
                            @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                {{ $department->department }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="position_id" class="form-label small text-muted">Position</label>
                        <select class="form-control select2" id="position_id" name="position_id">
                            <option value="">All Positions</option>
                            @foreach($positions as $position)
                            <option value="{{ $position->id }}" {{ request('position_id') == $position->id ? 'selected' : '' }}>
                                {{ $position->position }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="employee_ids" class="form-label small text-muted">Employees</label>
                        <select class="form-control select2-multiple" id="employee_ids" name="employee_ids[]" multiple="multiple">
                            @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ in_array($employee->id, request('employee_ids', [])) ? 'selected' : '' }}>
                                {{ $employee->name }} ({{ $employee->employee_id ?? 'N/A' }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 text-end">
                        <a href="{{ route('payroll.assign.index') }}" class="btn btn-light">
                            <i class="fas fa-redo me-1"></i> Reset
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i> Apply Filters
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Payroll List Card -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list-alt me-1"></i> Employee Payroll List
            </h6>
            <div>
                <a href="{{ route('payroll.assign.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus-circle me-1"></i> Assign Payroll
                </a>
            </div>
        </div>
        <div class="card-body">
            @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover table-bordered" id="payrollsTable" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th width="3%">#</th>
                            <th width="15%">Employee</th>
                            <th width="10%">Department</th>
                            <th width="10%">Position</th>
                            <th width="10%">Period</th>
                            <th width="10%" class="text-end">Basic Salary</th>
                            <th width="8%" class="text-end">Bonus</th>
                            <th width="8%" class="text-end">Reduction</th>
                            <th width="10%" class="text-end">Total</th>
                            <th width="15%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payrolls as $payroll)
                        <tr>
                            <td>{{ $payroll->display_number }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-circle symbol-40 me-3">
                                        <img src="{{ $payroll->user->photo_profile_path ? asset('storage/'.$payroll->user->photo_profile_path) : asset('storage/default_profile.png') }}"
                                            alt="{{ $payroll->user->name }}"
                                            class="rounded-circle"
                                            width="30"
                                            height="30"
                                            onerror="this.onerror=null;this.src='{{ asset('storage/default_profile.png') }}'">
                                    </div>
                                    <div>
                                        <span class="fw-bold">{{ $payroll->user->name }}</span>
                                        <div class="text-muted small">{{ $payroll->user->employee_id ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-primary bg-opacity-10 text-primary">
                                    {{ $payroll->historical_department ?? $payroll->user->department->department ?? 'N/A' }}
                                </span>
                            </td>
                            <td>{{ $payroll->historical_position ?? $payroll->user->position->position ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-info bg-opacity-10 text-info">
                                    {{ $payroll->created_at->format('F Y') }}
                                </span>
                            </td>
                            <td class="text-end">{{ number_format($payroll->basic_salary, 2) }}</td>
                            <td class="text-end">{{ number_format($payroll->bonus, 2) }}</td>
                            <td class="text-end">{{ number_format($payroll->reduction_salary, 2) }}</td>
                            <td class="text-end fw-bold text-primary">
                                {{ number_format($payroll->basic_salary + $payroll->overtime_salary + $payroll->allowance + $payroll->bonus - $payroll->reduction_salary, 2) }}
                            </td>
                            <td>
                                <div class="d-flex">
                                    <form class="me-1" action="{{ route('payroll.assign.upload-attachment') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="payroll_id" value="{{ $payroll->id }}">
                                        <label for="attachment-{{ $payroll->id }}" class="btn btn-icon btn-sm btn-info" title="Upload Attachment">
                                            <i class="fas fa-upload"></i>
                                            <input type="file" id="attachment-{{ $payroll->id }}" name="attachment" class="d-none" accept="image/png,image/jpeg,image/jpg" onchange="this.form.submit()">
                                        </label>
                                    </form>

                                    @if($payroll->file_path)
                                    <button type="button" class="btn btn-icon btn-sm btn-primary me-1 view-attachment" data-bs-toggle="modal" data-bs-target="#viewAttachmentModal" data-id="{{ $payroll->id }}" data-filepath="{{ $payroll->file_path }}" data-employee="{{ $payroll->user->name }}" data-period="{{ $payroll->created_at->format('F Y') }}" title="View Attachment">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @endif

                                    <a href="{{ route('payroll.assign.edit', $payroll->id) }}" class="btn btn-icon btn-sm btn-warning me-1" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <button type="button" class="btn btn-icon btn-sm btn-danger delete-payroll" data-id="{{ $payroll->id }}" data-employee="{{ $payroll->user->name }}" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold">
                            <td colspan="5" class="text-end">Totals:</td>
                            <td class="text-end">{{ number_format($payrolls->sum('basic_salary'), 2) }}</td>
                            <td class="text-end">{{ number_format($payrolls->sum('bonus'), 2) }}</td>
                            <td class="text-end">{{ number_format($payrolls->sum('reduction_salary'), 2) }}</td>
                            <td class="text-end text-primary">{{ number_format($payrolls->sum('basic_salary') + $payrolls->sum('overtime_salary') + $payrolls->sum('allowance') + $payrolls->sum('bonus') - $payrolls->sum('reduction_salary'), 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>
    </div>
</div>

<!-- View Attachment Modal -->
<div class="modal fade" id="viewAttachmentModal" tabindex="-1" aria-labelledby="viewAttachmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="viewAttachmentModalLabel">
                    <i class="fas fa-file-image me-2"></i> Payroll Attachment
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3" id="view-attachment-info"></div>
                <div id="image-container" class="text-center mb-4">
                    <img id="payroll-image" src="" class="payroll-image-preview img-fluid border rounded d-none" alt="Payroll Attachment">
                    <p id="no-image-message" class="d-none text-muted alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i> No attachment uploaded yet.
                    </p>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Close
                </button>
                <a id="download-attachment" href="#" class="btn btn-primary" download>
                    <i class="fas fa-download me-1"></i> Download
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Delete Form (Hidden) -->
<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
    .page-header-box {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    }

    .card {
        border: none;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

    .border-left-primary {
        border-left: 0.25rem solid #4e73df !important;
    }

    .border-left-success {
        border-left: 0.25rem solid #1cc88a !important;
    }

    .border-left-info {
        border-left: 0.25rem solid #36b9cc !important;
    }

    .border-left-warning {
        border-left: 0.25rem solid #f6c23e !important;
    }

    .select2-container .select2-selection--single,
    .select2-container .select2-selection--multiple {
        height: 38px !important;
        border: 1px solid #d1d3e2 !important;
        border-radius: 0.35rem !important;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #4e73df;
        border-color: #4e73df;
        color: white;
        padding: 0 8px;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: white;
        margin-right: 5px;
    }

    .table th {
        font-weight: 600;
        background-color: #f8f9fc;
        border-top: 1px solid #e3e6f0;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }

    .table tbody tr {
        transition: background-color 0.2s;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .payroll-image-preview {
        max-width: 100%;
        max-height: 500px;
        margin: 0 auto;
        box-shadow: 0 0.15rem 0.5rem rgba(0, 0, 0, 0.1);
    }

    .btn-icon {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .badge {
        padding: 0.35em 0.65em;
        font-size: 0.75em;
        font-weight: 600;
    }

    .pagination .page-item.active .page-link {
        background-color: #4e73df;
        border-color: #4e73df;
    }

    .pagination .page-link {
        color: #4e73df;
    }

    .modal-header {
        border-bottom: none;
    }

    .modal-footer {
        border-top: none;
    }

    .bg-gradient-primary-to-secondary {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    }

    .symbol {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .symbol-circle {
        border-radius: 50%;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // Initialize DataTable with better options
        $('#payrollsTable').DataTable({
           
        });

        // Initialize Select2
        $('.select2').select2({
            placeholder: 'Select an option',
            width: '100%',
            allowClear: true
        });

        $('.select2-multiple').select2({
            placeholder: 'Select employee(s)',
            width: '100%',
            allowClear: true
        });

        // Clear month filter
        $('#clear-month').click(function() {
            $('#month_year').val('');
            $('#filterForm').submit();
        });

        // Clear all filters
        $('#clear-all-filters').click(function(e) {
            e.preventDefault();
            $('.select2').val(null).trigger('change');
            $('.select2-multiple').val(null).trigger('change');
            $('#month_year').val('');
            $('#filterForm').submit();
        });

        // View attachment handler
        $(document).on('click', '.view-attachment', function() {
            let filePath = $(this).data('filepath');
            let employee = $(this).data('employee');
            let period = $(this).data('period');

            $('#view-attachment-info').html(
                `<div class="text-start">
                    <p class="mb-1"><strong>Employee:</strong> ${employee}</p>
                    <p class="mb-1"><strong>Period:</strong> ${period}</p>
                </div>`
            );

            if (filePath) {
                let fullPath = "{{ asset('storage') }}/" + filePath;
                $('#payroll-image').attr('src', fullPath).removeClass('d-none');
                $('#download-attachment').attr('href', fullPath).removeClass('d-none');
                $('#no-image-message').addClass('d-none');
            } else {
                $('#payroll-image').addClass('d-none');
                $('#download-attachment').addClass('d-none');
                $('#no-image-message').removeClass('d-none');
            }
        });

        // Delete Payroll Confirmation
        $('.delete-payroll').on('click', function() {
            const id = $(this).data('id');
            const employee = $(this).data('employee');

            Swal.fire({
                title: 'Confirm Deletion',
                html: `<div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i> 
                    You are about to delete payroll data for <strong>${employee}</strong>.
                    This action cannot be undone!
                </div>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74a3b',
                cancelButtonColor: '#858796',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                focusCancel: true,
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = $('#delete-form');
                    form.attr('action', `{{ route('payroll.assign.index') }}/${id}`);
                    form.submit();
                }
            });
        });

        // Placeholder for month input
        if (!$('#month_year').val()) {
            $('#month_year').attr('placeholder', 'All periods');
        }
    });
</script>
@endpush