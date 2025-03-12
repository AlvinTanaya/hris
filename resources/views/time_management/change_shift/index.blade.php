@extends('layouts.app')

@section('content')
<style>
    /* Improved tab styling */
    #shiftTabs .nav-link {
        color: white;
        font-weight: 500;
        padding: 1rem;
        transition: all 0.3s ease;
    }

    #shiftTabs .nav-link.active {
        color: #0d6efd;
        border-bottom: 3px solid #0d6efd;
        background-color: rgba(255, 255, 255, 0.9);
    }

    /* Full-width table fixes */
    .table-responsive {
        padding: 0;
        margin: 0;
        width: 100%;
    }

    .table {
        width: 100%;
        margin-bottom: 0;
    }

    /* Badge styling fixes */
    .badge {
        padding: 0.35em 0.65em;
        font-size: 0.85em;
        font-weight: 600;
        border-radius: 50rem;
        display: inline-block;
    }

    .badge-warning {
        background-color: #ffc107;
        color: #212529;
    }

    .badge-success {
        background-color: #198754;
        color: white;
    }

    .badge-danger {
        background-color: #dc3545;
        color: white;
    }

    .badge-secondary {
        background-color: #6c757d;
        color: white;
    }


    /* Card styling */
    .card {
        border-radius: 0.5rem;
    }

    #requestsTable_wrapper {
        width: 100%;
        margin: 0;
        padding: 0;
    }

    .dataTables_wrapper .no-footer {
        width: 100% !important;
        margin: 0 !important;
    }

    .dataTable {
        width: 100% !important;
    }
</style>

<h1 class="text-center text-warning" style="margin-bottom: 45px; margin-top:25px">
    <i class="fas fa-calendar"></i> Employee Shift
</h1>

<div class="container mt-4 mx-auto">
    <ul class="nav nav-tabs d-flex w-100" id="shiftTabs" role="tablist" style="background-color: #0d6efd; border-radius: 0.5rem 0.5rem 0 0;">
        <li class="nav-item flex-grow-1 text-center" role="presentation">
            <a class="nav-link active" id="shifts-tab" data-bs-toggle="tab" href="#shifts" role="tab" aria-controls="shifts" aria-selected="true">
                <i class="fas fa-calendar-day me-2"></i>Current Shifts
            </a>
        </li>
        <li class="nav-item flex-grow-1 text-center" role="presentation">
            <a class="nav-link" id="requests-tab" data-bs-toggle="tab" data-bs-target="#requests" type="button" role="tab" aria-controls="requests" aria-selected="false">
                <i class="fas fa-exchange-alt me-2"></i>Change Requests
            </a>
        </li>
    </ul>

    <div class="tab-content mt-4" id="shiftTabsContent">
        <!-- Current Shifts Tab -->
        <div class="tab-pane fade show active" id="shifts" role="tabpanel" aria-labelledby="shifts-tab">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="text-primary mt-2"><i class="fas fa-calendar"></i> Your Shift</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>Shift Type</th>
                                    <th>Days & Times</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employeeShifts as $shift)
                                <tr>
                                    <td><span class="badge rounded-pill bg-{{ $shift->type == 'Morning' ? 'primary' : ($shift->type == 'Afternoon' ? 'warning' : 'secondary') }}">{{ $shift->type }}</span></td>
                                    <td>
                                        @php
                                        $times = [];
                                        if(is_array(json_decode($shift->hour_start)) && is_array(json_decode($shift->hour_end))) {
                                        $start_times = json_decode($shift->hour_start);
                                        $end_times = json_decode($shift->hour_end);
                                        $days = json_decode($shift->days);

                                        foreach($days as $index => $day) {
                                        $time_key = $start_times[$index] . ' - ' . $end_times[$index];
                                        if(!isset($times[$time_key])) {
                                        $times[$time_key] = [];
                                        }
                                        $times[$time_key][] = $day;
                                        }
                                        } else {
                                        $times[$shift->hour_start . ' - ' . $shift->hour_end] = [$shift->days];
                                        }
                                        @endphp

                                        @foreach($times as $time => $days_list)
                                        <div class="mb-1">
                                            <span class="fw-bold">{{ implode(', ', $days_list) }}:</span> {{ $time }}
                                        </div>
                                        @endforeach
                                    </td>
                                    <td>{{ $shift->start_date }}</td>
                                    <td>
                                        @if($shift->end_date)
                                        {{ $shift->end_date }}
                                        @else
                                        <span class="badge bg-success">Ongoing</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">No shifts assigned yet.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Change Requests Tab -->
        <div class="tab-pane fade" id="requests" role="tabpanel" aria-labelledby="requests-tab">
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center mb-0">
                    <h5 class="text-primary mt-2"><i class="fa-solid fa-right-left"></i> Request Change Schedule</h5>
                    <a href="{{ url('time_management/change_shift/create/' . Auth::user()->id) }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-2"></i>New Request
                    </a>
                </div>

                <div class="card-body">
                    @if($pendingRequests->count() > 0)
                    <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle me-2"></i>You have {{ $pendingRequests->count() }} pending shift change request(s).
                    </div>
                    @endif

                    <!-- In your blade template, add this right below the info alert or above the table -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            Show
                            <select id="entriesSelector" class="form-select form-select-sm d-inline-block w-auto">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="-1">All</option>
                            </select>
                            entries
                        </div>
                        <div class="input-group" style="max-width: 300px;">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" id="tableSearch" class="form-control form-control-sm" placeholder="Search...">
                        </div>
                    </div>

                    @if(count($allRequests) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover" id="requestsTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>Request Date</th>
                                    <th>Current Shift</th>
                                    <th>Requested Shift</th>
                                    <th>Exchange With</th>
                                    <th>Exchange Current Shift</th>
                                    <th>Exchange New Shift</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Reason</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($allRequests as $request)
                                <tr>
                                    <td>{{ Carbon\Carbon::parse($request->created_at)->format('Y-m-d') }}</td>
                                    <td>
                                        @if($request->ruleShiftBefore)
                                        <strong>{{ $request->ruleShiftBefore->type }}</strong><br>
                                        <small>
                                            @php
                                            $days = json_decode($request->ruleShiftBefore->days);
                                            $hourStart = json_decode($request->ruleShiftBefore->hour_start);
                                            $hourEnd = json_decode($request->ruleShiftBefore->hour_end);
                                            $dayNames = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                                            $schedule = [];

                                            // Get start and end dates
                                            $startDate = Carbon\Carbon::parse($request->date_change_start);
                                            $endDate = Carbon\Carbon::parse($request->date_change_end);

                                            // Loop through the range
                                            $currentDate = clone $startDate;
                                            while ($currentDate <= $endDate) {
                                                $dayOfWeek=$currentDate->dayOfWeek;
                                                // Adjust for Carbon's day numbering (0=Sunday, 1=Monday)
                                                $dayIndex = $dayOfWeek == 0 ? 6 : $dayOfWeek - 1;

                                                if(isset($days[$dayIndex]) && $days[$dayIndex]) {
                                                $dayName = substr($dayNames[$dayIndex], 0, 3);
                                                $schedule[] = $dayName . ': ' . $hourStart[$dayIndex] . '-' . $hourEnd[$dayIndex];
                                                }

                                                $currentDate->addDay();
                                                }

                                                echo implode('<br>', $schedule);
                                                @endphp
                                        </small>
                                        @else
                                        Unknown
                                        @endif
                                    </td>
                                    <td>
                                        @if($request->ruleShiftAfter)
                                        <strong>{{ $request->ruleShiftAfter->type }}</strong><br>
                                        <small>
                                            @php
                                            $days = json_decode($request->ruleShiftAfter->days);
                                            $hourStart = json_decode($request->ruleShiftAfter->hour_start);
                                            $hourEnd = json_decode($request->ruleShiftAfter->hour_end);
                                            $dayNames = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                                            $schedule = [];

                                            // Get start and end dates
                                            $startDate = Carbon\Carbon::parse($request->date_change_start);
                                            $endDate = Carbon\Carbon::parse($request->date_change_end);

                                            // Loop through the range
                                            $currentDate = clone $startDate;
                                            while ($currentDate <= $endDate) {
                                                $dayOfWeek=$currentDate->dayOfWeek;
                                                // Adjust for Carbon's day numbering (0=Sunday, 1=Monday)
                                                $dayIndex = $dayOfWeek == 0 ? 6 : $dayOfWeek - 1;

                                                if(isset($days[$dayIndex]) && $days[$dayIndex]) {
                                                $dayName = substr($dayNames[$dayIndex], 0, 3);
                                                $schedule[] = $dayName . ': ' . $hourStart[$dayIndex] . '-' . $hourEnd[$dayIndex];
                                                }

                                                $currentDate->addDay();
                                                }

                                                echo implode('<br>', $schedule);
                                                @endphp
                                        </small>
                                        @else
                                        Unknown
                                        @endif
                                    </td>
                                    <td>
                                        @if($request->exchangeUser)
                                        {{ $request->exchangeUser->name }}
                                        @else
                                        No Exchange
                                        @endif
                                    </td>
                                    <td>
                                        @if($request->ruleExchangeBefore)
                                        <strong>{{ $request->ruleExchangeBefore->type }}</strong><br>
                                        <small>
                                            @php
                                            $days = json_decode($request->ruleExchangeBefore->days);
                                            $hourStart = json_decode($request->ruleExchangeBefore->hour_start);
                                            $hourEnd = json_decode($request->ruleExchangeBefore->hour_end);
                                            $dayNames = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                                            $schedule = [];

                                            // Get start and end dates
                                            $startDate = Carbon\Carbon::parse($request->date_change_start);
                                            $endDate = Carbon\Carbon::parse($request->date_change_end);

                                            // Loop through the range
                                            $currentDate = clone $startDate;
                                            while ($currentDate <= $endDate) {
                                                $dayOfWeek=$currentDate->dayOfWeek;
                                                // Adjust for Carbon's day numbering (0=Sunday, 1=Monday)
                                                $dayIndex = $dayOfWeek == 0 ? 6 : $dayOfWeek - 1;

                                                if(isset($days[$dayIndex]) && $days[$dayIndex]) {
                                                $dayName = substr($dayNames[$dayIndex], 0, 3);
                                                $schedule[] = $dayName . ': ' . $hourStart[$dayIndex] . '-' . $hourEnd[$dayIndex];
                                                }

                                                $currentDate->addDay();
                                                }

                                                echo implode('<br>', $schedule);
                                                @endphp
                                        </small>
                                        @else
                                        N/A
                                        @endif
                                    </td>
                                    <td>
                                        @if($request->ruleExchangeAfter)
                                        <strong>{{ $request->ruleExchangeAfter->type }}</strong><br>
                                        <small>
                                            @php
                                            $days = json_decode($request->ruleExchangeAfter->days);
                                            $hourStart = json_decode($request->ruleExchangeAfter->hour_start);
                                            $hourEnd = json_decode($request->ruleExchangeAfter->hour_end);
                                            $dayNames = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                                            $schedule = [];

                                            // Get start and end dates
                                            $startDate = Carbon\Carbon::parse($request->date_change_start);
                                            $endDate = Carbon\Carbon::parse($request->date_change_end);

                                            // Loop through the range
                                            $currentDate = clone $startDate;
                                            while ($currentDate <= $endDate) {
                                                $dayOfWeek=$currentDate->dayOfWeek;
                                                // Adjust for Carbon's day numbering (0=Sunday, 1=Monday)
                                                $dayIndex = $dayOfWeek == 0 ? 6 : $dayOfWeek - 1;

                                                if(isset($days[$dayIndex]) && $days[$dayIndex]) {
                                                $dayName = substr($dayNames[$dayIndex], 0, 3);
                                                $schedule[] = $dayName . ': ' . $hourStart[$dayIndex] . '-' . $hourEnd[$dayIndex];
                                                }

                                                $currentDate->addDay();
                                                }

                                                echo implode('<br>', $schedule);
                                                @endphp
                                        </small>
                                        @else
                                        N/A
                                        @endif
                                    </td>
                                    <td>{{ Carbon\Carbon::parse($request->date_change_start)->format('Y-m-d') }}</td>
                                    <td>{{ Carbon\Carbon::parse($request->date_change_end)->format('Y-m-d') }}</td>
                                    <td class="text-wrap">{{ $request->reason_change }}</td>
                                    <td>
                                        @if($request->status_change == 'Pending')
                                        <span class="badge badge-warning">Pending</span>
                                        @elseif($request->status_change == 'Approved')
                                        <span class="badge badge-success">Approved</span>
                                        @elseif($request->status_change == 'Rejected')
                                        <span class="badge badge-danger">Rejected</span>
                                        @else
                                        <span class="badge badge-secondary">{{ $request->status_change }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($request->status_change == 'Pending')
                                        <button type="button" class="btn btn-sm btn-danger delete-request" data-id="{{ $request->id }}">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                        @else
                                        <button type="button" class="btn btn-sm btn-danger" disabled>
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="11" class="text-center">No shift change requests found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-info">
                        <p class="text-center mb-0">No shift change requests found.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTable
        var table = $('#requestsTable').DataTable({
            "order": [
                [0, "desc"]
            ],
            "pageLength": 10, // Default page length
            "responsive": true,
            "searching": true,
            "lengthChange": true,
            "dom": '<"top">rt<"bottom"ip><"clear">',
            "language": {
                "search": ""
            }
        });

        // Connect custom entries selector to DataTable
        $('#entriesSelector').on('change', function() {
            table.page.len($(this).val()).draw();
        });

        // Connect custom search box to DataTable
        $('#tableSearch').on('keyup', function() {
            table.search(this.value).draw();
        });

        // Fix Bootstrap 5 badge compatibility
        $('.badge:not([class*="bg-"])').each(function() {
            if ($(this).hasClass('badge-warning')) {
                $(this).removeClass('badge-warning').addClass('bg-warning text-dark');
            } else if ($(this).hasClass('badge-success')) {
                $(this).removeClass('badge-success').addClass('bg-success');
            } else if ($(this).hasClass('badge-danger')) {
                $(this).removeClass('badge-danger').addClass('bg-danger');
            } else if ($(this).hasClass('badge-secondary')) {
                $(this).removeClass('badge-secondary').addClass('bg-secondary');
            }
        });

        // SweetAlert Delete Confirmation
        $(document).on('click', '.delete-request', function() {
            const requestId = $(this).data('id');
            const row = $(this).closest('tr');

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/time_management/change_shift/delete/' + requestId,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                // Remove row from DataTable
                                table.row(row).remove().draw();

                                Swal.fire(
                                    'Deleted!',
                                    'Your shift change request has been deleted.',
                                    'success'
                                );

                                // Update pending requests count if needed
                                if (response.pendingCount !== undefined) {
                                    const pendingAlert = $('.alert-info');
                                    if (response.pendingCount > 0) {
                                        pendingAlert.html('<i class="fas fa-info-circle me-2"></i>You have ' + response.pendingCount + ' pending shift change request(s).');
                                    } else {
                                        pendingAlert.remove();
                                    }
                                }
                            } else {
                                Swal.fire(
                                    'Error!',
                                    response.message || 'Something went wrong.',
                                    'error'
                                );
                            }
                        },
                        error: function(xhr) {
                            Swal.fire(
                                'Error!',
                                'There was a problem deleting the request.',
                                'error'
                            );
                        }
                    });
                }
            });
        });
    });
</script>
@endpush