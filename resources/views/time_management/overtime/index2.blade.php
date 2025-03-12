@extends('layouts.app')

@section('content')
<style>
    #overtimeTabs .nav-link {
        color: white;
        font-weight: 500;
        padding: 1rem;
        transition: all 0.3s ease;
    }

    #overtimeTabs .nav-link.active {
        color: #0d6efd;
        border-bottom: 3px solid #0d6efd;
    }

    #overtimeTabs .nav-link.active span {
        color: #0d6efd;
    }



    .dataTable {
        width: 100% !important;
    }

    .table th,
    .table td {
        vertical-align: middle;
    }

    .badge {
        font-size: 90%;
        padding: 0.4em 0.6em;
    }

    /* Improved button styling */
    .btn-new-request {
        padding: 10px 20px;
        font-weight: 600;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        margin-bottom: 20px;
    }

    .btn-new-request:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
    }

    .btn-new-request i {
        margin-right: 8px;
    }

    /* Fix search field spacing */
    .dataTables_filter {
        margin-bottom: 10px;

    }

    /* Badge improvements */
    .badge-info {
        background-color: #17a2b8;
        color: white;
    }

    .badge-primary {
        background-color: #007bff;
        color: white;
    }
</style>

<h1 class="text-center text-warning" style="margin-bottom: 60px; margin-top:25px">
    <i class="fas fa-clock"></i> Overtime Requests for {{ $employee->name }}
</h1>

<div class="container mt-4 mx-auto">
    <!-- Improved Action Button -->
    <a href="{{ route('overtime.create', $employee->id) }}" class="btn btn-primary btn-new-request">
        <i class="fas fa-plus"></i> New Overtime Request
    </a>

    <!-- Tabs Navigation -->
    <div class="container-fluid p-0">
        <ul class="nav nav-tabs w-100" id="overtimeTabs" role="tablist">
            <li class="nav-item flex-grow-1 text-center" role="presentation">
                <a class="nav-link active" id="pending-tab" data-bs-toggle="tab" href="#pending" role="tab" aria-controls="pending" aria-selected="true">
                    <i class="fas fa-hourglass-half text-warning"></i>
                    <span class="badge badge-warning mr-1">{{ count($pendingRequests) }}</span> Pending Requests
                </a>
            </li>
            <li class="nav-item flex-grow-1 text-center" role="presentation">
                <a class="nav-link" id="approved-tab" data-bs-toggle="tab" href="#approved" role="tab" aria-controls="approved" aria-selected="false">
                    <i class="fas fa-check-circle text-success"></i>
                    <span class="badge badge-success mr-1">{{ count($approvedRequests) }}</span> Approved Requests
                </a>
            </li>
            <li class="nav-item flex-grow-1 text-center" role="presentation">
                <a class="nav-link" id="declined-tab" data-bs-toggle="tab" href="#declined" role="tab" aria-controls="declined" aria-selected="false">
                    <i class="fas fa-times-circle text-danger"></i>
                    <span class="badge badge-danger mr-1">{{ count($declinedRequests) }}</span> Declined Requests
                </a>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content p-0 mt-3" id="overtimeTabsContent">
            <!-- Pending Requests Tab -->
            <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                <div class="card shadow-sm w-100">
                    <div class="card-header bg-warning text-white">
                        <h5 class="mt-2"><i class="fas fa-calendar"></i> Pending Overtime Requests</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="pendingTable" class="table table-bordered table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="12%">Date</th>
                                        <th width="10%">Start Time</th>
                                        <th width="10%">End Time</th>
                                        <th width="8%">Hours</th>
                                        <th width="15%">Overtime Type</th>
                                        <th width="20%">Reason</th>
                                        <th width="20%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach($pendingRequests as $index => $overtime)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ date('d M Y', strtotime($overtime->date)) }}</td>
                                        <td>{{ date('H:i', strtotime($overtime->start_time)) }}</td>
                                        <td>{{ date('H:i', strtotime($overtime->end_time)) }}</td>
                                        <td>{{ (int)$overtime->total_hours }} hrs</td>
                                        <td>
                                            @if($overtime->overtime_type == 'Paid_Overtime')
                                            <span class="badge badge-info">Paid Overtime</span>
                                            @else
                                            <span class="badge badge-primary">Overtime Leave</span>
                                            @endif
                                        </td>
                                        <td>{{ $overtime->reason }}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-danger delete-btn"
                                                data-id="{{ $overtime->id }}">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Approved Requests Tab -->
            <div class="tab-pane fade" id="approved" role="tabpanel" aria-labelledby="approved-tab">
                <div class="card shadow-sm w-100">
                    <div class="card-header bg-success text-white">
                        <h5 class="mt-2"><i class="fas fa-calendar-check"></i> Approved Overtime Requests</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="approvedTable" class="table table-bordered table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="12%">Date</th>
                                        <th width="10%">Start Time</th>
                                        <th width="10%">End Time</th>
                                        <th width="8%">Hours</th>
                                        <th width="15%">Overtime Type</th>
                                        <th width="20%">Reason</th>
                                        <th width="20%">Approved By/On</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach($approvedRequests as $index => $overtime)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ date('d M Y', strtotime($overtime->date)) }}</td>
                                        <td>{{ date('H:i', strtotime($overtime->start_time)) }}</td>
                                        <td>{{ date('H:i', strtotime($overtime->end_time)) }}</td>
                                        <td>{{ (int)$overtime->total_hours }} hrs</td>
                                        <td>
                                            @if($overtime->overtime_type == 'Paid_Overtime')
                                            <span class="badge badge-info">Paid Overtime</span>
                                            @else
                                            <span class="badge badge-primary">Overtime Leave</span>
                                            @endif
                                        </td>
                                        <td>{{ $overtime->reason }}</td>
                                        <td>
                                            {{ $overtime->response_name ?: 'Unknown' }}<br>
                                            <small class="text-muted">{{ date('d M Y', strtotime($overtime->updated_at)) }}</small>
                                        </td>
                                    </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Declined Requests Tab -->
            <div class="tab-pane fade" id="declined" role="tabpanel" aria-labelledby="declined-tab">
                <div class="card shadow-sm w-100">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mt-2"><i class="fas fa-calendar-times"></i> Declined Overtime Requests</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="declinedTable" class="table table-bordered table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="12%">Date</th>
                                        <th width="10%">Start Time</th>
                                        <th width="10%">End Time</th>
                                        <th width="8%">Hours</th>
                                        <th width="12%">Overtime Type</th>
                                        <th width="20%">Reason</th>
                                        <th width="23%">Declined Reason/By</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach($declinedRequests as $index => $overtime)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ date('d M Y', strtotime($overtime->date)) }}</td>
                                        <td>{{ date('H:i', strtotime($overtime->start_time)) }}</td>
                                        <td>{{ date('H:i', strtotime($overtime->end_time)) }}</td>
                                        <td>{{ (int)$overtime->total_hours }} hrs</td>
                                        <td>
                                            @if($overtime->overtime_type == 'Paid_Overtime')
                                            <span class="badge badge-info">Paid Overtime</span>
                                            @else
                                            <span class="badge badge-primary">Overtime Leave</span>
                                            @endif
                                        </td>
                                        <td>{{ $overtime->reason }}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-info view-reason-btn mb-1"
                                                data-reason="{{ $overtime->declined_reason }}">
                                                <i class="fas fa-info-circle"></i> View Reason
                                            </button><br>
                                            <small>By: {{ $overtime->response_name ?: 'Unknown' }}</small>
                                        </td>
                                    </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTables
        $('#pendingTable, #approvedTable, #declinedTable').DataTable({
            responsive: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search records...",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries"
            },
            order: [
                [1, 'desc']
            ], // Sort by date column descending
            pageLength: 10,
            lengthMenu: [
                [5, 10, 25, 50, -1],
                [5, 10, 25, 50, "All"]
            ],
            // Increase spacing for search field
            dom: '<"row mb-3"<"col-md-6"l><"col-md-6"f>><"row"<"col-md-12"t>><"row"<"col-md-5"i><"col-md-7"p>>'
        });

        // View declined reason with SweetAlert
        $('.view-reason-btn').click(function() {
            const reason = $(this).data('reason');
            Swal.fire({
                title: 'Decline Reason',
                text: reason || 'No reason provided',
                icon: 'info',
                confirmButtonColor: '#3085d6'
            });
        });

        // Delete confirmation with SweetAlert
        $('.delete-btn').click(function() {
            const id = $(this).data('id');
            const deleteUrl = `{{ url('time_management/overtime') }}/${id}`;

            Swal.fire({
                title: 'Delete Overtime Request',
                text: 'Are you sure you want to delete this overtime request? This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create and submit form for DELETE request
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = deleteUrl;
                    form.style.display = 'none';

                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';

                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'DELETE';

                    form.appendChild(csrfToken);
                    form.appendChild(methodField);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    });
</script>
@endpush