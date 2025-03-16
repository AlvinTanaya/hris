@extends('layouts.app')

@section('content')
<style>
    #timeOffTabs .nav-link {
        color: white;
        font-weight: 500;
        padding: 1rem;
        transition: all 0.3s ease;
    }

    #timeOffTabs .nav-link.active {
        color: #0d6efd;
        border-bottom: 3px solid #0d6efd;
    }

    #timeOffTabs .nav-link.active span {
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

    /* Time off balance card styles */

    /* Time off balance card styling */
    .time-off-balance-section {
        margin-bottom: 35px;
    }

    .time-off-balance-section h4 {
        margin-bottom: 20px;
        font-weight: 600;
        color: #343a40;
    }

    .balance-card {
        border-radius: 12px;
        background-color: #ffffff;
        padding: 25px;
        border: none;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        margin-bottom: 20px;
        transition: all 0.3s ease;
        height: 100%;
    }

    .balance-card:hover {
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        transform: translateY(-5px);
    }

    .balance-card h3 {
        color: #343a40;
        font-size: 16px;
        margin-bottom: 15px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .balance-card .balance-value {
        font-size: 42px;
        font-weight: 700;
        color: #0d6efd;
        line-height: 1;
        margin-bottom: 5px;
    }

    .balance-card .balance-unit {
        font-size: 18px;
        color: #6c757d;
        margin-bottom: 15px;
        display: block;
    }

    .balance-card .balance-details {
        font-size: 14px;
        color: #495057;
        background-color: #f8f9fa;
        padding: 8px 12px;
        border-radius: 6px;
        display: inline-block;
    }

    .balance-progress {
        height: 8px;
        border-radius: 4px;
        margin-top: 10px;
        background-color: #e9ecef;
    }

    .balance-progress-bar {
        height: 100%;
        border-radius: 4px;
        background-color: #0d6efd;
    }
</style>

<h1 class="text-center text-warning" style="margin-bottom: 60px; margin-top:25px">
    <i class="fas fa-calendar-alt"></i> Time Off Requests for {{ $employee->name }}
</h1>

<div class="container mt-4 mx-auto">
    <div class="time-off-balance-section">
        <h4 class="text-white">Time Off Balance Summary</h4>
        <div class="row">
            @foreach($timeOffAssignments as $assignment)
            @php
            $progress = ($assignment->quota > 0) ? ($assignment->balance / $assignment->quota) * 100 : 0;
            $progressColor = $progress >= 50 ? 'bg-success' : ($progress >= 25 ? 'bg-warning' : 'bg-danger');
            @endphp
            <div class="col-md-4">
                <div class="balance-card p-3 border rounded shadow">
                    <h3 class="text-primary text-center">{{ $assignment->time_off_name }}</h3>
                    <div class="balance-value text-center fw-bold">{{ $assignment->balance }}</div>
                    <div class="balance-unit text-center text-muted">days</div>
                    <div class="balance-details text-center">Available: {{ $assignment->balance }}/{{ $assignment->quota }} days</div>
                    <div class="balance-progress mt-2">
                        <div class="balance-progress-bar {{ $progressColor }}" style="width: {{ $progress }}%; height: 8px;"></div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>





    <!-- Improved Action Button -->
    <a href="{{ route('request.time.off.create', $employee->id) }}" class="btn btn-primary btn-new-request">
        <i class="fas fa-plus"></i> New Time Off Request
    </a>



    <!-- Tabs Navigation -->
    <div class="container-fluid p-0">
        <ul class="nav nav-tabs w-100" id="timeOffTabs" role="tablist">
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
        <div class="tab-content p-0 mt-3" id="timeOffTabsContent">
            <!-- Pending Requests Tab -->
            <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                <div class="card shadow-sm w-100">
                    <div class="card-header bg-warning text-white">
                        <h5 class="mt-2"><i class="fas fa-calendar"></i> Pending Time Off Requests</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="pendingTable" class="table table-bordered table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="10%">Start Date</th>
                                        <th width="10%">End Date</th>
                                        <th width="8%">Days</th>
                                        <th width="12%">Type</th>
                                        <th width="20%">Reason</th>
                                        <th width="15%">Proof File</th>
                                        <th width="20%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingRequests as $index => $timeOff)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ date('d M Y', strtotime($timeOff->start_date)) }}</td>
                                        <td>{{ date('d M Y', strtotime($timeOff->end_date)) }}</td>
                                        <td>
                                            @php
                                            $start = new DateTime($timeOff->start_date);
                                            $end = new DateTime($timeOff->end_date);
                                            $interval = $start->diff($end);
                                            $days = $interval->days + 1; // Including both start and end days
                                            @endphp
                                            {{ $days }} days
                                        </td>
                                        <td>
                                            @php
                                            $type = App\Models\TimeOffPolicy::find($timeOff->time_off_id);
                                            $typeName = $type ? $type->time_off_name : 'Unknown';
                                            @endphp
                                            <span class="badge badge-info">{{ $typeName }}</span>
                                        </td>
                                        <td>{{ $timeOff->reason }}</td>
                                        <td>
                                            @if($timeOff->file_reason_path)
                                            <button type="button" class="btn btn-sm btn-info view-file-btn text-white"
                                                data-file="{{ $timeOff->file_reason_path }}">
                                                <i class="fas fa-file-image"></i> View File
                                            </button>
                                            @else
                                            <span class="text-muted">No file attached</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-danger delete-btn"
                                                data-id="{{ $timeOff->id }}">
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
                        <h5 class="mt-2"><i class="fas fa-calendar-check"></i> Approved Time Off Requests</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="approvedTable" class="table table-bordered table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="12%">Start Date</th>
                                        <th width="12%">End Date</th>
                                        <th width="8%">Days</th>
                                        <th width="15%">Type</th>
                                        <th width="23%">Reason</th>
                                        <th width="25%">Approved By/On</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($approvedRequests as $index => $timeOff)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ date('d M Y', strtotime($timeOff->start_date)) }}</td>
                                        <td>{{ date('d M Y', strtotime($timeOff->end_date)) }}</td>
                                        <td>
                                            @php
                                            $start = new DateTime($timeOff->start_date);
                                            $end = new DateTime($timeOff->end_date);
                                            $interval = $start->diff($end);
                                            $days = $interval->days + 1; // Including both start and end days
                                            @endphp
                                            {{ $days }} days
                                        </td>
                                        <td>
                                            @php
                                            $type = App\Models\TimeOffPolicy::find($timeOff->time_off_id);
                                            $typeName = $type ? $type->time_off_name : 'Unknown';
                                            @endphp
                                            <span class="badge badge-info">{{ $typeName }}</span>
                                        </td>
                                        <td>{{ $timeOff->reason }}</td>
                                        <td>
                                            @php
                                            $approver = App\Models\User::find($timeOff->answered_by);
                                            $approverName = $approver ? $approver->name : 'Unknown';
                                            @endphp
                                            {{ $approverName }}<br>
                                            <small class="text-muted">{{ date('d M Y', strtotime($timeOff->updated_at)) }}</small>
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
                        <h5 class="mt-2"><i class="fas fa-calendar-times"></i> Declined Time Off Requests</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="declinedTable" class="table table-bordered table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="12%">Start Date</th>
                                        <th width="12%">End Date</th>
                                        <th width="8%">Days</th>
                                        <th width="12%">Type</th>
                                        <th width="18%">Reason</th>
                                        <th width="33%">Declined Reason/By</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($declinedRequests as $index => $timeOff)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ date('d M Y', strtotime($timeOff->start_date)) }}</td>
                                        <td>{{ date('d M Y', strtotime($timeOff->end_date)) }}</td>
                                        <td>
                                            @php
                                            $start = new DateTime($timeOff->start_date);
                                            $end = new DateTime($timeOff->end_date);
                                            $interval = $start->diff($end);
                                            $days = $interval->days + 1; // Including both start and end days
                                            @endphp
                                            {{ $days }} days
                                        </td>
                                        <td>
                                            @php
                                            $type = App\Models\TimeOffPolicy::find($timeOff->time_off_id);
                                            $typeName = $type ? $type->time_off_name : 'Unknown';
                                            @endphp
                                            <span class="badge badge-info">{{ $typeName }}</span>
                                        </td>
                                        <td>{{ $timeOff->reason }}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-info view-reason-btn mb-1"
                                                data-reason="{{ $timeOff->declined_reason }}">
                                                <i class="fas fa-info-circle"></i> View Reason
                                            </button><br>
                                            @php
                                            $approver = App\Models\User::find($timeOff->answered_by);
                                            $approverName = $approver ? $approver->name : 'Unknown';
                                            @endphp
                                            <small>By: {{ $approverName }}</small>
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
<!-- Proof File Modal -->
<div class="modal fade" id="proofFileModal" tabindex="-1" aria-labelledby="proofFileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="proofFileModalLabel">Proof Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="proofFileImage" src="" alt="Proof Document" class="img-fluid">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // View proof file with modal
        $('.view-file-btn').click(function() {
            const filePath = $(this).data('file');
            const fileUrl = "{{ asset('storage/') }}/" + filePath;

            // Set the image source
            $('#proofFileImage').attr('src', fileUrl);

            // Show the modal
            $('#proofFileModal').modal('show');
        });


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
            ], // Sort by start date column descending
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
            const deleteUrl = `{{ url('time_management/time_off/request_time_off') }}/${id}`;

            Swal.fire({
                title: 'Delete Time Off Request',
                text: 'Are you sure you want to delete this time off request? This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Send DELETE request
                    $.ajax({
                        url: deleteUrl,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire(
                                'Deleted!',
                                'Your time off request has been deleted.',
                                'success'
                            ).then(() => {
                                // Reload the page to refresh the data
                                window.location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire(
                                'Error!',
                                'There was an error deleting the request. Please try again.',
                                'error'
                            );
                            console.error(xhr.responseText);
                        }
                    });
                }
            });
        });

        // Add active class to current tab based on URL hash or default to pending
        const hash = window.location.hash || '#pending';
        $(`#timeOffTabs a[href="${hash}"]`).tab('show');

        // Update URL hash when tab changes
        $('#timeOffTabs a').on('click', function(e) {
            window.location.hash = $(this).attr('href');
        });

        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@endpush