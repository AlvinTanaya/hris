@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Card -->


    <!-- Page Title -->
    <div class="row">
        <div class="col-12 text-center mb-5">
            <h1 class="text-warning">
                <i class="fas fa-money-bill-wave me-2"></i> Employee Salary Management
            </h1>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="card border-0 shadow">
        <div class="card-header bg-primary d-flex justify-content-between align-items-center p-3">
            <div>
                <h4 class="text-white mb-0">
                    <i class="fas fa-list-ul me-2"></i> Employee Salary Records
                </h4>
            </div>
            <button type="button" class="btn btn-light rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addSalaryModal">
                <i class="fas fa-plus me-1"></i> Add New Salary
            </button>
        </div>
        <div class="card-body">
            <!-- Filter Controls -->
            <div class="row mb-4 align-items-center">
                <div class="col-md-3">
                    <div class="input-group input-group-sm shadow-sm rounded">
                        <span class="input-group-text bg-light border-0">Show</span>
                        <select id="entries-select" class="form-select border-0">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <span class="input-group-text bg-light border-0">entries</span>
                    </div>
                </div>
                <div class="col-md-5 ms-auto">
                    <div class="input-group input-group-sm shadow-sm rounded">
                        <span class="input-group-text bg-light border-0"><i class="fas fa-search text-primary"></i></span>
                        <input type="text" id="search-input" class="form-control border-0" placeholder="Search employees...">
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table align-middle table-hover" id="salary-table">
                    <thead>
                        <tr class="bg-light">
                            <th class="text-uppercase text-primary fw-bold">EMPLOYEE</th>
                            <th class="text-uppercase text-primary fw-bold">BASIC SALARY</th>
                            <th class="text-uppercase text-primary fw-bold">OVERTIME RATE</th>
                            <th class="text-uppercase text-primary fw-bold">ALLOWANCE</th>
                            <th class="text-uppercase text-primary fw-bold">CREATED</th>
                            <th class="text-uppercase text-primary fw-bold">UPDATED</th>
                            <th class="text-end text-uppercase text-primary fw-bold">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employeeSalaries as $salary)
                        <tr class="border-bottom">
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm rounded-circle p-1 bg-primary text-white me-3 d-flex align-items-center justify-content-center shadow-sm">
                                        {{ substr($salary->user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold">{{ $salary->user->name }}</h6>
                                        <p class="text-muted mb-0 small">{{ $salary->user->employee_id }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 fw-semibold">
                                    Rp {{ number_format($salary->basic_salary, 0, ',', '.') }}
                                </span>
                            </td>
                            <td>
                                <div>
                                    <p class="mb-0 fw-semibold">Rp {{ number_format($salary->overtime_rate_per_hour, 0, ',', '.') }}</p>
                                    <small class="text-muted">Per Hour</small>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info bg-opacity-10 text-info px-3 py-2 fw-semibold">
                                    Rp {{ number_format($salary->allowance, 0, ',', '.') }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-calendar-alt text-muted me-2"></i>
                                    <span class="text-muted small">{{ $salary->created_at->format('Y-m-d') }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-clock text-muted me-2"></i>
                                    <span class="text-muted small">{{ $salary->updated_at->format('Y-m-d') }}</span>
                                </div>
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-primary rounded-pill edit-salary"
                                        data-id="{{ $salary->id }}"
                                        data-user-id="{{ $salary->users_id }}"
                                        data-user-name="{{ $salary->user->name }}"
                                        data-basic-salary="{{ $salary->basic_salary }}"
                                        data-overtime-rate="{{ $salary->overtime_rate_per_hour }}"
                                        data-allowance="{{ $salary->allowance }}">
                                        <i class="fas fa-pen-to-square"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger rounded-pill ms-2 delete-salary" data-id="{{ $salary->id }}">
                                        <i class="fas fa-trash-alt"></i> Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-folder-open text-muted fa-3x mb-3"></i>
                                    <h5 class="text-muted">No salary records found</h5>
                                    <p class="text-muted mb-0">Add new salary records to see them listed here.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="row mt-4 align-items-center">
                <div class="col-sm-12 col-md-5">
                    <div class="text-muted small">
                        Showing {{ $employeeSalaries->firstItem() ?? 0 }} to {{ $employeeSalaries->lastItem() ?? 0 }} of {{ $employeeSalaries->total() }} entries
                    </div>
                </div>
                <div class="col-sm-12 col-md-7">
                    <div class="d-flex justify-content-md-end">
                        <nav>
                            <ul class="pagination pagination-sm mb-0">
                                <li class="page-item {{ $employeeSalaries->onFirstPage() ? 'disabled' : '' }}">
                                    <a class="page-link" href="{{ $employeeSalaries->previousPageUrl() }}" aria-label="Previous">
                                        <span aria-hidden="true"><i class="fas fa-chevron-left"></i></span>
                                    </a>
                                </li>
                                @for ($i = 1; $i <= $employeeSalaries->lastPage(); $i++)
                                    <li class="page-item {{ $employeeSalaries->currentPage() == $i ? 'active' : '' }}">
                                        <a class="page-link" href="{{ $employeeSalaries->url($i) }}">{{ $i }}</a>
                                    </li>
                                    @endfor
                                    <li class="page-item {{ $employeeSalaries->hasMorePages() ? '' : 'disabled' }}">
                                        <a class="page-link" href="{{ $employeeSalaries->nextPageUrl() }}" aria-label="Next">
                                            <span aria-hidden="true"><i class="fas fa-chevron-right"></i></span>
                                        </a>
                                    </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Salary Modal -->
<div class="modal fade" id="addSalaryModal" tabindex="-1" aria-labelledby="addSalaryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="addSalaryModalLabel">
                    <i class="fas fa-plus-circle me-2"></i> Add New Employee Salary
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addSalaryForm" action="{{ route('payroll.master.salary.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label for="employee" class="form-label fw-bold">Select Employee</label>
                        <select class="form-select border-0 shadow-sm" id="employee" name="users_id" required>
                            <option value="">-- Select Employee --</option>
                            @foreach($availableEmployees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="basic_salary" class="form-label fw-bold">Basic Salary</label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text border-0 bg-light">Rp</span>
                            <input type="number" class="form-control border-0" id="basic_salary" name="basic_salary" min="0" placeholder="Enter amount" required>
                        </div>
                        <small class="form-text text-muted mt-2">Monthly basic salary amount (before deductions)</small>
                    </div>
                    <div class="mb-4">
                        <label for="overtime_rate_per_hour" class="form-label fw-bold">Overtime Rate</label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text border-0 bg-light">Rp</span>
                            <input type="number" class="form-control border-0" id="overtime_rate_per_hour" name="overtime_rate_per_hour" min="0" placeholder="Enter hourly rate" required>
                        </div>
                        <small class="form-text text-muted mt-2">Amount paid per overtime hour worked</small>
                    </div>
                    <div class="mb-3">
                        <label for="allowance" class="form-label fw-bold">Allowance</label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text border-0 bg-light">Rp</span>
                            <input type="number" class="form-control border-0" id="allowance" name="allowance" min="0" placeholder="Enter allowance amount" required>
                        </div>
                        <small class="form-text text-muted mt-2">Additional allowance payments (transport, meals, etc.)</small>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                        <i class="fas fa-save me-1"></i> Save Salary
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Salary Modal -->
<div class="modal fade" id="editSalaryModal" tabindex="-1" aria-labelledby="editSalaryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="editSalaryModalLabel">
                    <i class="fas fa-edit me-2"></i> Edit Employee Salary
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editSalaryForm" action="{{ route('payroll.master.salary.update') }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_salary_id" name="id">
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label for="edit_employee" class="form-label fw-bold">Employee</label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text border-0 bg-light"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control border-0 bg-light" id="edit_employee_name" readonly>
                        </div>
                        <input type="hidden" id="edit_users_id" name="users_id">
                    </div>
                    <div class="mb-4">
                        <label for="edit_basic_salary" class="form-label fw-bold">Basic Salary</label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text border-0 bg-light">Rp</span>
                            <input type="number" class="form-control border-0" id="edit_basic_salary" name="basic_salary" min="0" required>
                        </div>
                        <small class="form-text text-muted mt-2">Monthly basic salary amount (before deductions)</small>
                    </div>

                    <div class="mb-4">
                        <label for="edit_overtime_rate_per_hour" class="form-label fw-bold">Overtime Rate</label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text border-0 bg-light">Rp</span>
                            <input type="number" class="form-control border-0" id="edit_overtime_rate_per_hour" name="overtime_rate_per_hour" min="0" required>
                        </div>
                        <small class="form-text text-muted mt-2">Amount paid per overtime hour worked</small>
                    </div>
                    <div class="mb-3">
                        <label for="edit_allowance" class="form-label fw-bold">Allowance</label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text border-0 bg-light">Rp</span>
                            <input type="number" class="form-control border-0" id="edit_allowance" name="allowance" min="0" required>
                        </div>
                        <small class="form-text text-muted mt-2">Additional allowance payments (transport, meals, etc.)</small>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                        <i class="fas fa-check me-1"></i> Update Salary
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
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



    /* Empty State Styles */
    .empty-state {
        padding: 2rem;
        text-align: center;
    }

    /* Modal Styles */
    .modal-content {
        border-radius: 10px;
    }

    /* Additional Responsive Tweaks */
    @media (max-width: 768px) {
        .btn-group .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        // Handle the edit button click
        $('.edit-salary').on('click', function() {
            var id = $(this).data('id');
            var userId = $(this).data('user-id');
            var userName = $(this).data('user-name');
            var basicSalary = $(this).data('basic-salary');
            var overtimeRate = $(this).data('overtime-rate');
            var allowance = $(this).data('allowance');

            $('#edit_salary_id').val(id);
            $('#edit_users_id').val(userId);
            $('#edit_employee_name').val(userName);
            $('#edit_basic_salary').val(basicSalary);
            $('#edit_overtime_rate_per_hour').val(overtimeRate);
            $('#edit_allowance').val(allowance);

            $('#editSalaryModal').modal('show');
        });

        // Handle the delete button click
        $('.delete-salary').on('click', function() {
            var id = $(this).data('id');

            Swal.fire({
                title: 'Delete Salary Record?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#e74a3b',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                borderRadius: '10px'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Send delete request
                    $.ajax({
                        url: "{{ route('payroll.master.salary.destroy') }}",
                        type: "DELETE",
                        data: {
                            "_token": "{{ csrf_token() }}",
                            "id": id
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: 'Employee salary record has been deleted.',
                                confirmButtonColor: '#4e73df',
                                timer: 2000,
                                timerProgressBar: true
                            }).then(() => {
                                window.location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Something went wrong. Please try again.',
                                confirmButtonColor: '#4e73df'
                            });
                        }
                    });
                }
            });
        });

        // Search functionality with highlighting
        $('#search-input').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $("#salary-table tbody tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });

            // Show message when no results
            if ($("#salary-table tbody tr:visible").length === 0) {
                if ($("#no-results-row").length === 0) {
                    $("#salary-table tbody").append(
                        '<tr id="no-results-row"><td colspan="6" class="text-center py-4">' +
                        '<i class="fas fa-search text-muted fa-2x mb-3"></i>' +
                        '<p class="mb-0">No matching records found</p></td></tr>'
                    );
                }
            } else {
                $("#no-results-row").remove();
            }
        });

        // Form validation for basic salary and overtime rate
        $('input[name="basic_salary"], input[name="overtime_rate_per_hour"]').on('input', function() {
            if ($(this).val() < 0) {
                $(this).val(0);
                $(this).addClass('is-invalid');
                setTimeout(() => {
                    $(this).removeClass('is-invalid');
                }, 1000);
            }
        });

        // Handle entries selection
        $('#entries-select').change(function() {
            var url = new URL(window.location.href);
            url.searchParams.set('per_page', $(this).val());
            window.location.href = url.toString();
        });

        // Set the current entries value
        const urlParams = new URLSearchParams(window.location.search);
        const perPage = urlParams.get('per_page');
        if (perPage) {
            $('#entries-select').val(perPage);
        }

        // Show success message if exists
        @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: "{{ session('success') }}",
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            iconColor: '#4e73df',
            background: '#fff',
            customClass: {
                popup: 'shadow-sm border-0'
            }
        });
        @endif

        // Show error message if exists
        @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: "{{ session('error') }}",
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            iconColor: '#e74a3b',
            background: '#fff',
            customClass: {
                popup: 'shadow-sm border-0'
            }
        });
        @endif

        // Animation for table rows
        $("#salary-table tbody tr").each(function(index) {
            $(this).css({
                'opacity': 0,
                'transform': 'translateY(20px)'
            }).animate({
                'opacity': 1,
                'transform': 'translateY(0px)'
            }, 300 + (index * 100));
        });
    });
</script>
@endpush