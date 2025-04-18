@extends('layouts.app')

@section('content')
<h1 class="text-center text-warning" style="margin-bottom: 65px; margin-top:25px">
    <i class="fas fa-file-signature"></i> My Resignation Requests
</h1>

<div class="container mt-4 mx-auto">
    <!-- User Info Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
            <h5 class="mt-2"><i class="fas fa-user"></i> Employee Information</h5>

            <a href="{{ url('time_management/request_resign/create/' . Auth::user()->id) }}" class="btn btn-light text-primary">
                <i class="fas fa-plus-circle me-2"></i>New Resignation Request
            </a>
        </div>

    </div>

    <!-- Pending Requests Card -->
    @if(count($pending_requests) > 0)
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
                                @if ($item->resign_status == 'Pending')
                                <a href="{{ route('request.resign.edit', $item->id) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <button type="button" class="btn btn-danger btn-sm"
                                    onclick="confirmCancel('{{ $item->id }}', '{{ $item->resign_type }}')">
                                    <i class="fas fa-times-circle"></i> Cancel
                                </button>
                                @else
                                <button type="button" class="btn btn-warning btn-sm" disabled>
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button type="button" class="btn btn-danger btn-sm" disabled>
                                    <i class="fas fa-times-circle"></i> Cancel
                                </button>
                                @endif

                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @else
    <div class="alert alert-info mb-4">
        <i class="fas fa-info-circle me-2"></i> You don't have any pending resignation requests.
    </div>
    @endif

    <!-- Approved Requests Card -->
    @if(count($approved_requests) > 0)
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

    <!-- Declined Requests Card -->
    @if(count($declined_requests) > 0)
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
                            <th>Resignation Type</th>
                            <th>Resignation Date</th>
                            <th>Reason</th>
                            <th>Declined Reason</th>
                            <th>View File</th>
                            <th>Declined By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($declined_requests as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->resign_type }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->resign_date)->format('d M Y') }}</td>
                            <td>{{ $item->resign_reason }}</td>
                            <td>{{ $item->declined_reason }}</td>
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

        $('.view-file-btn').on('click', function() {
            const fileUrl = $(this).data('file');
            const employeeName = $(this).data('employee');

            $('#file-employee-name').text('Employee: ' + employeeName);
            $('#file-image').attr('src', fileUrl);
            $('#download-file').attr('href', fileUrl);
            $('#fileViewModal').modal('show');
        });

    });

    function confirmCancel(id, resignType) {
        Swal.fire({
            title: 'Cancel Resignation Request',
            html: `Are you sure you want to cancel your <strong>${resignType}</strong> resignation request?<br><span class="text-danger">This action cannot be undone.</span>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, cancel it!',
            cancelButtonText: 'No, keep it'
        }).then((result) => {
            if (result.isConfirmed) {
                // Buat form untuk submit
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = "{{ url('time_management/request_resign') }}/" + id;

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = "{{ csrf_token() }}";

                const method = document.createElement('input');
                method.type = 'hidden';
                method.name = '_method';
                method.value = 'DELETE';

                form.appendChild(csrfToken);
                form.appendChild(method);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
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