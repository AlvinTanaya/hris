@extends('layouts.app')

@section('content')


<h1 class="text-center text-warning" style="margin-bottom: 60px; margin-top:25px">
    <i class="fas fa-calendar-alt"></i> Time Off Requests for {{ $employee->name }}
</h1>

<div class="container mt-4 mx-auto time-off-container">
    <div class="time-off-balance-section">
        <h4 class="text-white">Time Off Balance Summary</h4>
        <div class="row">
            @foreach($timeOffAssignments as $assignment)
            @php
            $progress = ($assignment->quota > 0) ? ($assignment->balance / $assignment->quota) * 100 : 0;
            $progressColor = $progress >= 50 ? 'bg-success' : ($progress >= 25 ? 'bg-warning' : 'bg-danger');
            @endphp
            <div class="col-md-3 mb-4">
                <div class="balance-card mb-2 p-3 border rounded shadow">
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
                    <span style="background-color: white; color: #ffc107; font-weight: bold; padding: 2px 8px; border-radius: 10px; font-size: 0.9rem;">
                        {{ count($pendingRequests) }}
                    </span>&nbsp;Pending Requests
                </a>
            </li>
            <li class="nav-item flex-grow-1 text-center" role="presentation">
                <a class="nav-link" id="approved-tab" data-bs-toggle="tab" href="#approved" role="tab" aria-controls="approved" aria-selected="false">
                    <i class="fas fa-check-circle text-success"></i>
                    <span style="background-color: white; color: #28a745; font-weight: bold; padding: 2px 8px; border-radius: 10px; font-size: 0.9rem;">
                        {{ count($approvedRequests) }}
                    </span>&nbsp;Approved Requests
                </a>
            </li>
            <li class="nav-item flex-grow-1 text-center" role="presentation">
                <a class="nav-link" id="declined-tab" data-bs-toggle="tab" href="#declined" role="tab" aria-controls="declined" aria-selected="false">
                    <i class="fas fa-times-circle text-danger"></i>
                    <span style="background-color: white; color: #dc3545; font-weight: bold; padding: 2px 8px; border-radius: 10px; font-size: 0.9rem;">
                        {{ count($declinedRequests) }}
                    </span>&nbsp;Declined Requests
                </a>
            </li>
        </ul>


        <!-- Tab Content -->
        <div class="tab-content p-0 mt-3" id="timeOffTabsContent">
            <!-- Pending Requests Tab -->
            <div class="tab-pane fade show active" id="pending" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-white">
                        <h5 class="mt-2"><i class="fas fa-calendar"></i> Pending Requests</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="pendingTable" class="table table-bordered table-striped table-hover w-100">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="3%">No</th>
                                        <th width="12%">Start Date</th>
                                        <th width="12%">End Date</th>
                                        <th width="8%">Duration</th>
                                        <th width="12%">Type</th>
                                        <th width="23%">Reason</th>
                                        <th width="15%">Proof File</th>
                                        <th width="15%">Actions</th>
                                    </tr>

                                </thead>
                                <tbody>
                                    @foreach($pendingRequests as $index => $request)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $request->formatted_start_date }}</td>
                                        <td>{{ $request->formatted_end_date }}</td>
                                        <td>{{ $request->duration }}</td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ $request->timeOffPolicy->time_off_name ?? 'Unknown' }}
                                            </span>
                                        </td>
                                        <td>{{ $request->reason }}</td>
                                        <td>
                                            @if($request->file_reason_path)
                                            <button class="btn btn-sm btn-info view-file-btn" data-file="{{ $request->file_reason_path }}">
                                                <i class="fas fa-file-image"></i> View
                                            </button>
                                            @else
                                            <span class="text-muted">No file</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-danger delete-btn" data-id="{{ $request->id }}">
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
            <div class="tab-pane fade" id="approved" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mt-2"><i class="fas fa-calendar-check"></i> Approved Requests</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="approvedTable" class="table table-bordered table-striped table-hover w-100">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="3%">No</th>
                                        <th width="13%">Start Date</th>
                                        <th width="13%">End Date</th>
                                        <th width="8%">Duration</th>
                                        <th width="16%">Type</th>
                                        <th width="23%">Reason</th>
                                        <th width="24%">Approved By/On</th>
                                    </tr>

                                </thead>
                                <tbody>
                                    @foreach($approvedRequests as $index => $request)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $request->formatted_start_date }}</td>
                                        <td>{{ $request->formatted_end_date }}</td>
                                        <td>{{ $request->duration }}</td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ $request->timeOffPolicy->time_off_name ?? 'Unknown' }}
                                            </span>
                                        </td>
                                        <td>{{ $request->reason }}</td>

                                        <td>
                                            <div>
                                                <small class="d-block">Dept:
                                                    @if($request->dept_approval_user_id)
                                                    {{ \App\Models\User::find($request->dept_approval_user_id)->name ?? 'Unknown' }}
                                                    @else
                                                    N/A
                                                    @endif
                                                </small>
                                                <small class="d-block">Admin:
                                                    @if($request->admin_approval_user_id)
                                                    {{ \App\Models\User::find($request->admin_approval_user_id)->name ?? 'Unknown' }}
                                                    @else
                                                    N/A
                                                    @endif
                                                </small>
                                                <small class="text-muted">{{ $request->updated_at->format('d M Y H:i') }}</small>
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

            <!-- Declined Requests Tab -->
            <div class="tab-pane fade" id="declined" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mt-2"><i class="fas fa-calendar-times"></i> Declined Requests</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="declinedTable" class="table table-bordered table-striped table-hover w-100">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="3%">No</th>
                                        <th width="13%">Start Date</th>
                                        <th width="13%">End Date</th>
                                        <th width="8%">Duration</th>
                                        <th width="14%">Type</th>
                                        <th width="22%">Reason</th>
                                        <th width="27%">Declined Reason/By</th>
                                    </tr>

                                </thead>
                                <tbody>
                                    @foreach($declinedRequests as $index => $request)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $request->formatted_start_date }}</td>
                                        <td>{{ $request->formatted_end_date }}</td>
                                        <td>{{ $request->duration }}</td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ $request->timeOffPolicy->time_off_name ?? 'Unknown' }}
                                            </span>
                                        </td>
                                        <td>{{ $request->reason }}</td>

                                        <td>
                                            <div>
                                                <button class="btn btn-sm btn-info view-reason-btn mb-1"
                                                    data-reason="{{ $request->declined_reason }}">
                                                    <i class="fas fa-info-circle"></i> View Reason
                                                </button><br>
                                                <small class="d-block">Dept:
                                                    @if($request->dept_approval_user_id)
                                                    {{ \App\Models\User::find($request->dept_approval_user_id)->name ?? 'Unknown' }}
                                                    @else
                                                    N/A
                                                    @endif
                                                </small>
                                                <small class="d-block">Admin:
                                                    @if($request->admin_approval_user_id)
                                                    {{ \App\Models\User::find($request->admin_approval_user_id)->name ?? 'Unknown' }}
                                                    @else
                                                    N/A
                                                    @endif
                                                </small>
                                                <small class="text-muted">{{ $request->updated_at->format('d M Y H:i') }}</small>
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

</div>
@endsection


<style>
    /* Modern UI Improvements - Scoped to time-off page */
    .time-off-container {
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

    /* Enhanced Tab Styling */
    #timeOffTabs {
        border-bottom: none;
        gap: 8px;
        padding: 0 8px;
    }

    #timeOffTabs .nav-link {
        color: #495057;
        font-weight: 600;
        padding: 12px 20px;
        transition: var(--transition);
        border: none;
        background-color: #f1f3f5;
        border-radius: 8px;
        margin-right: 0;
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    #timeOffTabs .nav-link:hover {
        background-color: #e9ecef;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    #timeOffTabs .nav-link.active {
        color: white;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
    }

    #timeOffTabs .nav-link.active .badge {
        background-color: rgba(255, 255, 255, 0.2);
        color: white;
    }

    #timeOffTabs .nav-link i {
        margin-right: 8px;
        font-size: 1.1rem;
    }

    /* Card Styling */
    .card {
        border: none;
        border-radius: 12px;
        box-shadow: var(--card-shadow);
        transition: var(--transition);
        overflow: hidden;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
    }

    .card-header {
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        padding: 1.25rem 1.5rem;
        background-color: white;
    }

    .card-header h5 {
        font-weight: 700;
        display: flex;
        align-items: center;
    }

    .card-header h5 i {
        margin-right: 10px;
    }

    /* Button Styling */
    .btn {
        border-radius: 8px;
        font-weight: 600;
        padding: 0.5rem 1.25rem;
        transition: var(--transition);
        border: none;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .btn-sm {
        padding: 0.35rem 0.75rem;
        font-size: 0.85rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    }

    .btn-success {
        background: linear-gradient(135deg, #4cc9f0, #4895ef);
    }

    .btn-warning {
        background: linear-gradient(135deg, #f8961e, #f3722c);
    }

    .btn-danger {
        background: linear-gradient(135deg, #f72585, #b5179e);
    }

    .btn-info {
        background: linear-gradient(135deg, #3a86ff, #4361ee);
    }

    .btn-secondary {
        background: linear-gradient(135deg, #6c757d, #495057);
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    /* Table Styling */
    .table {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
        font-size: 0.95rem;
    }

    .table th {
        font-weight: 700;
        background-color: #f8f9fa;
        color: #495057;
        padding: 1rem;
        border-bottom: 2px solid #e9ecef;
        position: sticky;
        top: 0;
    }

    .table td {
        padding: 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #e9ecef;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(67, 97, 238, 0.03);
    }

    /* Badge Styling */
    .badge {
        font-size: 85%;
        padding: 0.4em 0.75em;
        font-weight: 600;
        border-radius: 8px;
        letter-spacing: 0.5px;
    }

    /* Balance card styling */
    .balance-card {
        border-radius: 12px;
        background-color: #ffffff;
        padding: 25px;
        border: none;
        box-shadow: var(--card-shadow);
        margin-bottom: 20px;
        transition: var(--transition);
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
        color: var(--primary-color);
        line-height: 1;
        margin-bottom: 5px;
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
    }

    /* Modal Styling */
    .modal-content {
        border: none;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        overflow: hidden;
    }

    .modal-header {
        border-bottom: none;
        padding: 1.5rem;
    }

    .modal-body {
        padding: 1.5rem;
    }

    .modal-footer {
        border-top: none;
        padding: 1.25rem 1.5rem;
    }

    /* New request button */
    .btn-new-request {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        padding: 12px 24px;
        font-weight: 600;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(67, 97, 238, 0.2);
        transition: var(--transition);
        margin-bottom: 20px;
    }

    .btn-new-request:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 15px rgba(67, 97, 238, 0.3);
    }

    /* Animation for tab switching */
    .tab-pane.fade.show.active {
        animation: fadeIn 0.3s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(5px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Responsive Improvements */
    @media (max-width: 992px) {
        #timeOffTabs .nav-link {
            padding: 0.75rem 0.5rem;
            font-size: 0.9rem;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .btn-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .btn-group .btn {
            margin-bottom: 0;
            width: 100%;
        }
    }
</style>

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
            const deleteUrl = `{{ url('time_management/time_off/request_time_off/destroy/') }}/${id}`;

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