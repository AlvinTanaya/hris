@extends('layouts.app')

@section('content')
<style>
    .overtime-container {
        --primary-color: #4361ee;
        --secondary-color: #3f37c9;
        --success-color: #4cc9f0;
        --warning-color: #f8961e;
        --danger-color: #f72585;
        --light-bg: #f8f9fa;
        --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    #overtimeTabs {
        border-bottom: none;
        gap: 8px;
        padding: 0 8px;
    }

    #overtimeTabs .nav-link {
        color: #495057;
        font-weight: 600;
        padding: 12px 20px;
        transition: var(--transition);
        border: none;
        background-color: #f1f3f5;
        border-radius: 8px;
        margin-right: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    #overtimeTabs .nav-link:hover {
        background-color: #e9ecef;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    #overtimeTabs .nav-link.active {
        color: white;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
    }

    #overtimeTabs .nav-link.active .badge {
        background-color: rgba(255, 255, 255, 0.2);
        color: white;
    }

    #overtimeTabs .nav-link i {
        margin-right: 8px;
        font-size: 1.1rem;
    }

    .card {
        border: none;
        border-radius: 12px;
        box-shadow: var(--card-shadow);
        transition: var(--transition);
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

    .badge {
        font-size: 85%;
        padding: 0.4em 0.75em;
        font-weight: 600;
        border-radius: 8px;
    }

    .btn-new-request {
        padding: 0.5rem 1.25rem;
        border-radius: 8px;
        font-weight: 600;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        transition: var(--transition);
    }

    .btn-new-request:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .avatar-initial {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        font-weight: 600;
    }

    @media (max-width: 992px) {
        #overtimeTabs .nav-link {
            padding: 0.75rem 0.5rem;
            font-size: 0.9rem;
        }

        .table-responsive {
            overflow-x: auto;
        }
    }
</style>

<div class="container-fluid py-4 overtime-container">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-header d-flex align-items-center justify-content-between text-warning">
                <h1 class="mb-0 d-flex align-items-center">
                    <i class="fas fa-clock me-2"></i> Overtime Requests for {{ $employee->name }}
                </h1>
                <div class="d-flex align-items-center">
                    <span class="badge bg-light text-white me-2" style="font-size: 1.2rem;">
                        <i class="fas fa-user-clock me-1"></i> Total Requests: {{ count($pendingRequests) + count($approvedRequests) + count($declinedRequests) }}
                    </span>
                </div>
            </div>
            <hr class="mt-2" style="border-top: 2px solid rgba(67, 97, 238, 0.1);">
        </div>
    </div>

    <!-- Action Button -->
    <div class="mb-4">
        <a href="{{ route('overtime.create', $employee->id) }}" class="btn btn-primary btn-new-request">
            <i class="fas fa-plus me-1"></i> New Overtime Request
        </a>
    </div>

    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs mb-4 p-0" id="overtimeTabs" role="tablist">
        <li class="nav-item flex-grow-1 text-center" role="presentation">
            <a class="nav-link active" id="pending-tab" data-bs-toggle="tab" href="#pending" role="tab" aria-controls="pending" aria-selected="true">
                <i class="fas fa-hourglass-half"></i>
                <span class="badge bg-warning">{{ count($pendingRequests) }}</span>&nbsp;&nbsp;Pending
            </a>
        </li>
        <li class="nav-item flex-grow-1 text-center" role="presentation">
            <a class="nav-link" id="approved-tab" data-bs-toggle="tab" href="#approved" role="tab" aria-controls="approved" aria-selected="false">
                <i class="fas fa-check-circle"></i>
                <span class="badge bg-success">{{ count($approvedRequests) }}</span>&nbsp;&nbsp;Approved
            </a>
        </li>
        <li class="nav-item flex-grow-1 text-center" role="presentation">
            <a class="nav-link" id="declined-tab" data-bs-toggle="tab" href="#declined" role="tab" aria-controls="declined" aria-selected="false">
                <i class="fas fa-times-circle"></i>
                <span class="badge bg-danger">{{ count($declinedRequests) }}</span>&nbsp;&nbsp;Declined
            </a>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="overtimeTabsContent">
        <!-- Pending Requests Tab -->
        <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 d-flex align-items-center">
                        <i class="fas fa-hourglass-half text-warning me-2"></i> Pending Overtime Requests
                    </h5>
                    <span class="badge bg-light text-dark">
                        Showing {{ count($pendingRequests) }} requests
                    </span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <!-- Perubahan untuk tabel Pending Requests pada index2 -->
                        <table id="pendingTable" class="table table-hover w-100">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Time Range</th>
                                    <th>Hours</th>
                                    <th>Rate/Hour</th>
                                    <th>Total Payment</th>
                                    <th>Type</th>
                                    <th>Reason</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingRequests as $request)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($request['date'])->format('d M Y') }}</td>
                                    <td>{{ $request['start_time'] }} - {{ $request['end_time'] }}</td>
                                    <td>{{ $request['total_hours'] }} hrs</td>
                                    <td>{{ number_format($request['overtime_rate'], 0, ',', '.') }}</td>
                                    <td>{{ number_format($request['overtime_payment'], 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $request['overtime_type'] ?? 'Regular' }}</span>
                                    </td>
                                    <td>{{ Str::limit($request['reason'], 30) }}</td>
                                    <td>
                                        <div class="d-flex flex-column gap-2">
                                            <span class="badge bg-{{ $request['dept_approval_status'] === 'Approved' ? 'success' : ($request['dept_approval_status'] === 'Declined' ? 'danger' : 'warning') }}">
                                                <i class="fas fa-{{ $request['dept_approval_status'] === 'Approved' ? 'check' : ($request['dept_approval_status'] === 'Declined' ? 'times' : 'clock') }} me-1"></i>
                                                Dept: {{ $request['dept_approval_status'] ?? 'Pending' }}
                                            </span>
                                            <span class="badge bg-{{ $request['admin_approval_status'] === 'Approved' ? 'success' : ($request['admin_approval_status'] === 'Declined' ? 'danger' : 'warning') }}">
                                                <i class="fas fa-{{ $request['admin_approval_status'] === 'Approved' ? 'check' : ($request['admin_approval_status'] === 'Declined' ? 'times' : 'clock') }} me-1"></i>
                                                Admin: {{ $request['admin_approval_status'] ?? 'Pending' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>

                                        <button type="button" class="btn btn-sm btn-danger delete-btn"
                                            data-id="{{ $request['id'] }}"
                                            data-employee="{{ $request['employee_name'] }}">
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
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 d-flex align-items-center">
                        <i class="fas fa-check-circle text-success me-2"></i> Approved Overtime Requests
                    </h5>
                    <span class="badge bg-light text-dark">
                        Showing {{ count($approvedRequests) }} requests
                    </span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <!-- Perubahan untuk tabel Approved Requests pada index2 -->
                        <table id="approvedTable" class="table table-hover w-100">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Time Range</th>
                                    <th>Hours</th>
                                    <th>Rate/Hour</th>
                                    <th>Total Payment</th>
                                    <th>Type</th>
                                    <th>Reason</th>
                                    <th>Approved By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($approvedRequests as $request)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($request['date'])->format('d M Y') }}</td>
                                    <td>{{ $request['start_time'] }} - {{ $request['end_time'] }}</td>
                                    <td>{{ $request['total_hours'] }} hrs</td>
                                    <td>{{ number_format($request['overtime_rate'], 0, ',', '.') }}</td>
                                    <td>{{ number_format($request['overtime_payment'], 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $request['overtime_type'] ?? 'Regular' }}</span>
                                    </td>
                                    <td>{{ Str::limit($request['reason'], 30) }}</td>
                                    <td>
                                        <div class="d-flex flex-column gap-2">
                                            @if($request['dept_approver_name'])
                                            <span class="badge bg-light text-dark">
                                                <i class="fas fa-user-tie me-1"></i>
                                                Dept: {{ $request['dept_approver_name'] }}
                                            </span>
                                            @endif
                                            @if($request['admin_approver_name'])
                                            <span class="badge bg-light text-dark">
                                                <i class="fas fa-user-shield me-1"></i>
                                                Admin: {{ $request['admin_approver_name'] }}
                                            </span>
                                            @endif
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
        <div class="tab-pane fade" id="declined" role="tabpanel" aria-labelledby="declined-tab">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 d-flex align-items-center">
                        <i class="fas fa-times-circle text-danger me-2"></i> Declined Overtime Requests
                    </h5>
                    <span class="badge bg-light text-dark">
                        Showing {{ count($declinedRequests) }} requests
                    </span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <!-- Perubahan untuk tabel Declined Requests pada index2 -->
                        <table id="declinedTable" class="table table-hover w-100">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Time Range</th>
                                    <th>Hours</th>
                                    <th>Rate/Hour</th>
                                    <th>Total Payment</th>
                                    <th>Type</th>
                                    <th>Declined By</th>
                                    <th>Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($declinedRequests as $request)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($request['date'])->format('d M Y') }}</td>
                                    <td>{{ $request['start_time'] }} - {{ $request['end_time'] }}</td>
                                    <td>{{ $request['total_hours'] }} hrs</td>
                                    <td>{{ number_format($request['overtime_rate'], 0, ',', '.') }}</td>
                                    <td>{{ number_format($request['overtime_payment'], 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $request['overtime_type'] ?? 'Regular' }}</span>
                                    </td>
                                    <td>{{ $request['declined_by'] ?? 'Unknown' }}</td>
                                    <td>
                                        <span class="text-danger">
                                            {{ Str::limit($request['declined_reason'], 50) }}
                                        </span>
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

<!-- Decline Reason Modal -->
<div class="modal fade" id="declineReasonModal" tabindex="-1" aria-labelledby="declineReasonModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="declineReasonModalLabel">Decline Reason</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="declineReasonText"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        // Initialize DataTables
        const dataTableConfig = {
            responsive: true,
            order: [
                [1, 'desc']
            ], // Sort by date column descending
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50, 100],
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
            dom: '<"top"<"row align-items-center"<"col-md-6"l><"col-md-6"f>>>rt<"bottom"<"row align-items-center"<"col-md-6"i><"col-md-6"p>><"clear">>'
        };

        $('#pendingTable').DataTable(dataTableConfig);
        $('#approvedTable').DataTable(dataTableConfig);
        $('#declinedTable').DataTable(dataTableConfig);

        // View decline reason
        $(document).on('click', '.view-reason-btn', function() {
            const reason = $(this).data('reason') || 'No reason provided';
            $('#declineReasonText').text(reason);
            $('#declineReasonModal').modal('show');
        });

        // Delete confirmation
        $(document).on('click', '.delete-btn', function() {
            const id = $(this).data('id');
            const employee = $(this).data('employee');
            const deleteUrl = "{{ route('overtime.destroy', ['id' => '__ID__']) }}".replace('__ID__', id);

            Swal.fire({
                title: 'Delete Overtime Request',
                html: `Are you sure you want to delete <strong>${employee}'s</strong> overtime request?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create form for DELETE request
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

        // Tab change handler to redraw DataTables
        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
            const target = $(e.target).data('bs-target');

            if (target === '#pending') {
                $('#pendingTable').DataTable().columns.adjust().responsive.recalc();
            } else if (target === '#approved') {
                $('#approvedTable').DataTable().columns.adjust().responsive.recalc();
            } else if (target === '#declined') {
                $('#declinedTable').DataTable().columns.adjust().responsive.recalc();
            }
        });
    });
</script>
@endpush