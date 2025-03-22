@extends('layouts.app')

@section('content')
<h1 class="text-center text-warning" style="margin-bottom: 65px; margin-top:25px">
    <i class="fas fa-file-signature"></i> Request Resignation
</h1>

<div class="container mt-4 mx-auto">
    <!-- Filter Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="text-primary mt-2"><i class="fas fa-filter"></i> Filter Requests</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('request.resign.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Employee</label>
                    <select class="form-select" name="user_id">
                        <option value="">All Employees</option>
                        @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ request('employee') == $employee->id ? 'selected' : '' }}>{{ $employee->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="position" class="form-label">Position</label>
                    <select name="position" id="position" class="form-select">
                        <option value="">All Positions</option>
                        @foreach($positions as $pos)
                        <option value="{{ $pos }}" {{ request('position') == $pos ? 'selected' : '' }}>{{ $pos }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="department" class="form-label">Department</label>
                    <select name="department" id="department" class="form-select">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                        <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="resign_type" class="form-label">Resignation Type</label>
                    <select name="resign_type" id="resign_type" class="form-select">
                        <option value="">All Types</option>
                        @foreach($finalTypes as $type)
                        <option value="{{ $type }}" {{ request('department') == $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="response_type" class="form-label">Response Type</label>
                    <select name="response_type" id="response_type" class="form-select">
                        <option value="">All Types</option>
                        <option value="Pending" {{ request('response_type') == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Approved" {{ request('response_type') == 'Approved' ? 'selected' : '' }}>Approved</option>
                        <option value="Declined" {{ request('response_type') == 'Declined' ? 'selected' : '' }}>Declined</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="date" class="form-label">Resignation Date</label>
                    <input type="date" class="form-control" id="date" name="date" value="{{ request('date') }}">
                </div>
                <div class="col-12  text-white">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>Apply Filters
                    </button>
                    <a href="{{ route('request.resign.index') }}" class="btn btn-secondary">
                        <i class="fas fa-undo me-2"></i>Reset
                    </a>
                </div>
            </form>
        </div>
    </div>
    @if($show_pending ?? true)
    <!-- Pending Requests Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-warning text-white">
            <h5 class="mt-2"><i class="fas fa-hourglass-half"></i> Pending Requests</h5>
        </div>

        <div class="card-body">
            <div class="table-responsive" style="padding-right: 1%;">
                <table id="pendingTable" class="table table-bordered table-striped mb-3 pt-3 align-middle align-items-center">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 5%;">NO</th>
                            <th>Employee</th>
                            <th>Resignation Type</th>
                            <th>Resignation Date</th>
                            <th>Reason</th>
                            <th>View File</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pending_requests as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->employee_name }} ({{ $item->user_id }}) - {{ $item->employee_position }}</td>
                            <td>{{ $item->resign_type }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->resign_date)->format('d M Y') }}</td>
                            <td>{{ $item->resign_reason }}</td>
                            <td>
                                @if($item->file_path)
                                <button type="button" class="btn btn-info btn-sm view-file-btn"
                                    data-file="{{ asset('storage/' . $item->file_path) }}"
                                    data-employee="{{ $item->employee_name }}">
                                    <i class="fas fa-file-image"></i> View
                                </button>
                                @else
                                <span class="badge bg-secondary">No File</span>
                                @endif
                            </td>
                            <td>
                                @if(\Carbon\Carbon::now()->gte(\Carbon\Carbon::parse($item->resign_date)))
                                <button type="button" class="btn btn-success btn-sm approve-btn"
                                    data-id="{{ $item->id }}"
                                    data-name="{{ $item->employee_name }}"
                                    data-position="{{ $item->employee_position }}">
                                    <i class="fas fa-check-circle"></i> Approve
                                </button>
                                @else
                                <button type="button" class="btn btn-success btn-sm" disabled>
                                    <i class="fas fa-check-circle"></i> Approve
                                </button>
                                @endif

                                <button type="button" class="btn btn-danger btn-sm decline-btn"
                                    data-id="{{ $item->id }}"
                                    data-name="{{ $item->employee_name }}"
                                    data-position="{{ $item->employee_position }}">
                                    <i class="fas fa-times-circle"></i> Decline
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    @if($show_approved ?? true)
    <!-- Approved Requests Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mt-2"><i class="fas fa-check-circle"></i> Approved Requests</h5>
        </div>

        <div class="card-body">
            <div class="table-responsive" style="padding-right: 1%;">
                <table id="approvedTable" class="table table-bordered table-striped mb-3 pt-3 align-middle align-items-center">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 5%;">NO</th>
                            <th>Employee</th>
                            <th>Resignation Type</th>
                            <th>Resignation Date</th>
                            <th>Reason</th>
                            <th>View File</th>
                            <th>Approved By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($approved_requests as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->employee_name }} ({{ $item->user_id }}) - {{ $item->employee_position }}</td>
                            <td>{{ $item->resign_type }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->resign_date)->format('d M Y') }}</td>
                            <td>{{ $item->resign_reason }}</td>
                            <td>
                                @if($item->file_path)
                                <button type="button" class="btn btn-info btn-sm view-file-btn"
                                    data-file="{{ asset('storage/' . $item->file_path) }}"
                                    data-employee="{{ $item->employee_name }}">
                                    <i class="fas fa-file-image"></i> View
                                </button>
                                @else
                                <span class="badge bg-secondary">No File</span>
                                @endif
                            </td>
                            <td>{{ $item->response_name }} ({{ $item->response_user_id }})</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif


    @if($show_declined ?? true)
    <!-- Declined Requests Card -->
    <div class="card shadow-sm">
        <div class="card-header bg-danger text-white">
            <h5 class="mt-2"><i class="fas fa-times-circle"></i> Declined Requests</h5>
        </div>

        <div class="card-body">
            <div class="table-responsive" style="padding-right: 1%;">
                <table id="declinedTable" class="table table-bordered table-striped mb-3 pt-3 align-middle align-items-center">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 5%;">NO</th>
                            <th>Employee</th>
                            <th>Resignation Type</th>
                            <th>Resignation Date</th>
                            <th>Reason</th>
                            <th>View File</th>
                            <th>Declined Reason</th>
                            <th>Declined By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($declined_requests as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->employee_name }} ({{ $item->user_id }}) - {{ $item->employee_position }}</td>
                            <td>{{ $item->resign_type }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->resign_date)->format('d M Y') }}</td>
                            <td>{{ $item->resign_reason }}</td>
                            <td>
                                @if($item->file_path)
                                <button type="button" class="btn btn-info btn-sm view-file-btn"
                                    data-file="{{ asset('storage/' . $item->file_path) }}"
                                    data-employee="{{ $item->employee_name }}">
                                    <i class="fas fa-file-image"></i> View
                                </button>
                                @else
                                <span class="badge bg-secondary">No File</span>
                                @endif
                            </td>
                            <td>{{ $item->declined_reason }}</td>
                            <td>{{ $item->response_name }} ({{ $item->response_user_id }})</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif


</div>

<!-- Decline Modal -->
<div class="modal fade" id="declineModal" tabindex="-1" aria-labelledby="declineModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="declineModalLabel">Decline Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="declineForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <p>You are about to decline the resignation request for <strong id="employee-name"></strong>.</p>
                    <div class="mb-3">
                        <label for="declined_reason" class="form-label">Reason for Decline</label>
                        <textarea class="form-control" id="declined_reason" name="declined_reason" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Confirm Decline</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- File View Modal -->
<div class="modal fade" id="fileViewModal" tabindex="-1" aria-labelledby="fileViewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="fileViewModalLabel">Documentary Evidence</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <h6 id="file-employee-name" class="mb-3"></h6>
                <img id="file-image" src="" alt="Documentary Evidence" class="img-fluid" style="max-height: 70vh;">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a id="download-file" href="" class="btn btn-primary" download>
                    <i class="fas fa-download"></i> Download
                </a>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // Initialize DataTables
        $('#pendingTable, #approvedTable, #declinedTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true
        });

        // Handle Decline button click
        $('.decline-btn').on('click', function() {
            const id = $(this).data('id');
            const employeeName = $(this).data('name');
            const employeePosition = $(this).data('position');

            $('#employee-name').text(employeeName + ' - ' + employeePosition);
            $('#declineForm').attr('action', "{{ url('time_management/request_resign/decline') }}/" + id);
            $('#declineModal').modal('show');
        });

        $('.view-file-btn').on('click', function() {
            const fileUrl = $(this).data('file');
            const employeeName = $(this).data('employee');

            $('#file-employee-name').text('Employee: ' + employeeName);
            $('#file-image').attr('src', fileUrl);
            $('#download-file').attr('href', fileUrl);
            $('#fileViewModal').modal('show');
        });

        // Handle Approve button click - now using SweetAlert2
        $('.approve-btn').on('click', function() {
            const id = $(this).data('id');
            const employeeName = $(this).data('name');
            const employeePosition = $(this).data('position');

            Swal.fire({
                title: 'Approve Resignation',
                html: `Are you sure you want to approve the resignation request for <strong>${employeeName} - ${employeePosition}</strong>?<br><br>This will mark the employee status as Inactive.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Approve',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create a form dynamically and submit it
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = "{{ url('time_management/request_resign/approve') }}/" + id;

                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = "{{ csrf_token() }}";

                    const method = document.createElement('input');
                    method.type = 'hidden';
                    method.name = '_method';
                    method.value = 'PUT';

                    form.appendChild(csrfToken);
                    form.appendChild(method);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    });
</script>
@if(session('success'))
<script>
    Swal.fire({
        title: "Success!",
        text: "{{ session('success') }}",
        icon: "success",
        confirmButtonText: "OK"
    });
</script>
@endif
@if(session('error'))
<script>
    Swal.fire({
        title: "Error!",
        text: "{{ session('error') }}",
        icon: "error",
        confirmButtonText: "OK"
    });
</script>
@endif
@endpush