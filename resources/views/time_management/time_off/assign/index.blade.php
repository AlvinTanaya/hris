@extends('layouts.app')

@section('content')
<style>
    table.dataTable {
        border: 1px solid #ccc !important;
        border-collapse: collapse;
    }
</style>


<div class="container-fluid">
    <div class="row mb-5 mt-5">
        <div class="col-12 text-center">
            <h1 class="text-warning"><i class="fas fa-calendar-alt me-2"></i>Time Off Assignments</h1>
        </div>

    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Filter Card -->
    <div class="card shadow mb-4 w-100">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-filter me-2"></i>Filter Employees
        </div>
        <div class="card-body">
            <form action="{{ route('time.off.assign.index') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="employee" class="form-label">Employee Name</label>
                        <select class="form-select" id="employee" name="employee">
                            <option value="">All Employees</option>
                            @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ request('employee') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="department" class="form-label">Department</label>
                        <select class="form-select" id="department" name="department">
                            <option value="">All Departments</option>
                            @foreach($departments as $department)
                            <option value="{{ $department }}" {{ request('department') == $department ? 'selected' : '' }}>
                                {{ $department }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="position" class="form-label">Position</label>
                        <select class="form-select" id="position" name="position">
                            <option value="">All Positions</option>
                            @foreach($positions as $position)
                            <option value="{{ $position }}" {{ request('position') == $position ? 'selected' : '' }}>
                                {{ $position }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="time_off_type" class="form-label">Time Off Type</label>
                        <select class="form-select" id="time_off_type" name="time_off_type">
                            <option value="">All Types</option>
                            @foreach($timeOffPolicies as $policy)
                            <option value="{{ $policy->id }}" {{ request('time_off_type') == $policy->id ? 'selected' : '' }}>
                                {{ $policy->time_off_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-12">

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i>Apply Filters
                        </button>
                        <a href="{{ route('time.off.assign.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo me-2"></i>Reset
                        </a>


                    </div>

                </div>
            </form>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card shadow my-4 w-100">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Assign Time Off</h5>
            <a href="{{ route('time.off.assign.create') }}" class="btn btn-light text-primary shadow-sm">
                <i class="fas fa-plus me-2"></i>Assign Time Off
            </a>

        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover w-100" id="timeOffAssignmentTable">
                    <thead class="table-primary">
                        <tr>
                            <th>NO</th>
                            <th>Employee ID</th>
                            <th>Employee Name</th>
                            <th>Department</th>
                            <th>Position</th>
                            <th>Time Off Policy</th>
                            <th>Balance</th>
                            <th>Quota</th>
                            <th>Created At</th>
                            <th width="60">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($timeOffAssignments as $index => $assignment)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $assignment->employee_id }}</td>
                            <td>{{ $assignment->employee_name }}</td>
                            <td>{{ $assignment->department_name }}</td>
                            <td>{{ $assignment->position_name }}</td>
                            <td>{{ $assignment->time_off_name }}</td>
                            <td>{{ $assignment->balance }}</td>
                            <td>{{ $assignment->quota }}</td>
                            <td>{{ date('d M Y', strtotime($assignment->created_at)) }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-warning edit-balance"
                                        data-id="{{ $assignment->id }}"
                                        data-balance="{{ $assignment->balance }}"
                                        data-quota="{{ $assignment->quota }}"
                                        data-employee="{{ $assignment->employee_name }}"
                                        data-policy="{{ $assignment->time_off_name }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editBalanceModal">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger delete-assignment"
                                        data-id="{{ $assignment->id }}"
                                        data-employee="{{ $assignment->employee_name }}"
                                        data-policy="{{ $assignment->time_off_name }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
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

<!-- Edit Balance Modal -->
<div class="modal fade" id="editBalanceModal" tabindex="-1" aria-labelledby="editBalanceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="editBalanceModalLabel">Edit Time Off Balance</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editBalanceForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <p><strong>Employee:</strong> <span id="employee-name"></span></p>
                        <p><strong>Time Off Policy:</strong> <span id="policy-name"></span></p>
                        <p><strong>Maximum Quota:</strong> <span id="max-quota"></span></p>
                    </div>
                    <div class="mb-3">
                        <label for="balance" class="form-label">Balance</label>
                        <input type="number" class="form-control" id="balance" name="balance" min="0" required>
                        <div class="form-text text-danger" id="balance-error" style="display: none;">
                            Balance cannot exceed the maximum quota.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" id="save-balance-btn">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


@push('scripts')

<script>
    $(document).ready(function() {
        // Initialize DataTable with "Show entries" dropdown
        var table = $('#timeOffAssignmentTable').DataTable({
            "order": [
                [2, "asc"]
            ],
            "pageLength": 10,
            "responsive": true,
            "dom": '<"row mb-3"<"col-md-6"l><"col-md-6"f>>rt<"row mt-3"<"col-md-6"i><"col-md-6"p>>',
            "language": {
                "lengthMenu": "Show _MENU_ entries",
                "search": "Search:"
            }
        });
        // Validate balance doesn't exceed quota
        $('#balance').on('input', function() {
            const balance = parseFloat($(this).val()) || 0;
            const quota = parseFloat($('#max-quota').text()) || 0;

            if (balance > quota) {
                $('#balance-error').show();
                $(this).addClass('is-invalid');
                $('#save-balance-btn').prop('disabled', true);
            } else {
                $('#balance-error').hide();
                $(this).removeClass('is-invalid');
                $('#save-balance-btn').prop('disabled', false);
            }
        });


        // Handle edit button click
        $('.edit-balance').click(function() {
            const id = $(this).data('id');
            const balance = $(this).data('balance');
            const quota = $(this).data('quota');
            const employee = $(this).data('employee');
            const policy = $(this).data('policy');

            $('#employee-name').text(employee);
            $('#policy-name').text(policy);
            $('#max-quota').text(quota);
            $('#balance').val(balance);
            $('#editBalanceForm').attr('action', "{{ route('time.off.assign.update', '') }}/" + id);

            // Reset error message
            $('#balance-error').hide();
            $('#balance').removeClass('is-invalid');
        });

        // Handle delete button click with SweetAlert2
        $('.delete-assignment').click(function() {
            const id = $(this).data('id');
            const employee = $(this).data('employee');
            const policy = $(this).data('policy');

            Swal.fire({
                title: 'Delete Time Off Assignment',
                html: `<p>Are you sure you want to delete this time off assignment?</p>
               <p><strong>Employee:</strong> ${employee}</p>
               <p><strong>Time Off Policy:</strong> ${policy}</p>
               <p class="text-danger">This action cannot be undone.</p>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Delete',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show processing state
                    Swal.fire({
                        title: 'Processing...',
                        html: '<div class="d-flex justify-content-center"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div><p class="mt-2">Please wait while we process your request.</p>',
                        allowOutsideClick: false,
                        showConfirmButton: false
                    });

                    // Create and submit the form with AJAX
                    const formData = new FormData();
                    formData.append('_token', "{{ csrf_token() }}");
                    formData.append('_method', 'DELETE');

                    $.ajax({
                        url: "{{ route('time.off.assign.destroy', '') }}/" + id,
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            // Show success message
                            Swal.fire({
                                title: 'Success!',
                                text: 'Time off assignment has been deleted successfully.',
                                icon: 'success',
                                confirmButtonColor: '#28a745'
                            }).then(() => {
                                // Reload the page to reflect changes
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            // Show error message
                            Swal.fire({
                                title: 'Error!',
                                text: 'An error occurred while processing your request.',
                                icon: 'error',
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    });
                }
            });
        });

        // For the edit form submission
        $('#editBalanceForm').submit(function(e) {
            e.preventDefault();

            // Show processing state
            Swal.fire({
                title: 'Processing...',
                html: '<div class="d-flex justify-content-center"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div><p class="mt-2">Please wait while we update the balance.</p>',
                allowOutsideClick: false,
                showConfirmButton: false
            });

            const formData = new FormData(this);

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    // Show success message
                    Swal.fire({
                        title: 'Success!',
                        text: 'Time off balance has been updated successfully.',
                        icon: 'success',
                        confirmButtonColor: '#28a745'
                    }).then(() => {
                        // Close the modal and reload the page
                        $('#editBalanceModal').modal('hide');
                        location.reload();
                    });
                },
                error: function(xhr) {
                    // Show error message or validation errors
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        if (errors.balance) {
                            $('#balance-error').text(errors.balance[0]).show();
                            $('#balance').addClass('is-invalid');
                        }
                        Swal.close();
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: 'An error occurred while processing your request.',
                            icon: 'error',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                }
            });
        });

    });
</script>
@endpush