@extends('layouts.app')



@section('content')
<div class="container test">
    <div class="page-header d-flex align-items-center">
        <div class="logo">
            <i class="fas fa-book-reader text-primary"></i>
        </div>
        <div>
            <h2 class="mb-0">E-learning Duty</h2>
            <p class="mb-0">Manage your organization's e-learning efficiently</p>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card d-flex align-items-center">
                <div class="icon me-3">
                    <i class="fas fa-tasks text-primary"></i>
                </div>
                <div>
                    <div class="title">Total Duties</div>
                    <div class="value">{{ $duty->count() }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card d-flex align-items-center">
                <div class="icon me-3">
                    <i class="fas fa-clock text-warning"></i>
                </div>
                <div>
                    <div class="title">Active Duties</div>
                    <div class="value">{{ $duty->where('end_date', '>=', now())->count() }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card d-flex align-items-center">
                <div class="icon me-3">
                    <i class="fas fa-check-circle text-success"></i>
                </div>
                <div>
                    <div class="title">Completed</div>
                    <div class="value">{{ $duty->whereNotNull('grade')->count() }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card d-flex align-items-center">
                <div class="icon me-3">
                    <i class="fas fa-hourglass-end text-danger"></i>
                </div>
                <div>
                    <div class="title">Expired</div>
                    <div class="value">{{ $duty->where('end_date', '<', now())->whereNull('grade')->count() }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <!-- Filter Card -->
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-filter me-2"></i> Filter Duties</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('elearning.index2', Auth::user()->id) }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="duration_range" class="form-label">Duration</label>
                    <select class="form-select" id="duration_range" name="duration_range">
                        <option value="">All Durations</option>
                        <option value="0-60" {{ request('duration_range') == '0-60' ? 'selected' : '' }}>0-60 minutes</option>
                        <option value="61-120" {{ request('duration_range') == '61-120' ? 'selected' : '' }}>1-2 hours</option>
                        <option value="121-180" {{ request('duration_range') == '121-180' ? 'selected' : '' }}>2-3 hours</option>
                        <option value="181+" {{ request('duration_range') == '181+' ? 'selected' : '' }}>3+ hours</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>Apply Filters
                    </button>
                    <a href="{{ route('elearning.index2', Auth::user()->id) }}" class="btn btn-secondary">
                        <i class="fas fa-undo me-2"></i>Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Duty List Card -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5><i class="fas fa-briefcase me-2"></i> Duty List</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="dutyTable" class="table table-bordered table-hover pb-0 pt-3 table-blue">
                    <thead>
                        <tr>
                            <th>Event Name</th>
                            <th>Duration (minutes)</th>
                            <th>Date Start</th>
                            <th>Date End</th>
                            <th>Passing Grade</th>
                            <th>Grade</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($duty as $item)
                        @php
                        $today = \Carbon\Carbon::today();
                        $startDate = \Carbon\Carbon::parse($item->start_date);
                        $endDate = \Carbon\Carbon::parse($item->end_date);
                        $isActive = ($today->between($startDate, $endDate) && is_null($item->grade));
                        $isExpired = ($today->greaterThan($endDate) && is_null($item->grade));
                        $isCompleted = !is_null($item->grade);
                        @endphp
                        <tr>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->duration }} minutes</td>
                            <td>{{ $startDate->format('d M Y') }}</td>
                            <td>{{ $endDate->format('d M Y') }}</td>
                            <td>{{ $item->passing_grade }}</td>
                            <td>{{ $item->grade ?? '-' }}</td>
                            <td>
                                @if($isActive)
                                <span class="badge bg-success">Active</span>
                                @elseif($isExpired)
                                <span class="badge bg-danger">Expired</span>
                                @elseif($isCompleted)
                                <span class="badge bg-primary">Completed</span>
                                @else
                                <span class="badge bg-secondary">Upcoming</span>
                                @endif
                            </td>
                            <td>
                                @if($isActive)
                                <a href="{{ route('elearning.elearning_material', $item->invitation_id ) }}" class="btn btn-action btn-primary btn-sm">
                                    <i class="fas fa-pencil-alt me-1"></i> E-learning Task
                                </a>
                                @else
                                <a class="btn btn-danger btn-sm disabled">
                                    <i class="fas fa-pencil-alt me-1"></i> E-learning Task
                                </a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection


<style>
    .test {
        --primary-blue: #2b4e81;
        --secondary-blue: #3a6bb0;
        --light-blue: #edf2fa;
        --accent-blue: #4285f4;
        --white: #ffffff;
    }

    body {
        background-color: var(--primary-blue);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .page-header {
        color: var(--white);
        padding: 20px 0;
    }

    .page-header .logo {
        background-color: var(--white);
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
    }

    .page-title {
        color: var(--white);
        margin-bottom: 15px;
    }

    .stats-card {
        background-color: var(--white);
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .stats-card .icon {
        font-size: 24px;
        color: var(--primary-blue);
    }

    .stats-card .title {
        font-size: 12px;
        color: #666;
    }

    .stats-card .value {
        font-size: 20px;
        font-weight: bold;
        color: #333;
    }

    .card {
        border: none;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-bottom: 25px;
    }

    .card-header {
        background-color: var(--white);
        border-bottom: 1px solid #eee;
        padding: 15px 20px;
    }

    .card-header h5 {
        margin: 0;
        color: var(--primary-blue);
        font-weight: 600;
    }

    .btn-primary {
        background-color: var(--accent-blue);
        border-color: var(--accent-blue);
    }

    .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
    }

    .table-blue thead {
        background-color: var(--primary-blue);
        color: var(--white);
    }

    .badge-active {
        background-color: #28a745;
        color: white;
    }

    .badge-expired {
        background-color: #dc3545;
        color: white;
    }

    .pagination .page-item.active .page-link {
        background-color: var(--accent-blue);
        border-color: var(--accent-blue);
    }

    .pagination .page-link {
        color: var(--primary-blue);
    }

    .btn-action {
        background-color: var(--accent-blue);
        color: white;
    }
</style>


@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#dutyTable').DataTable({
            pageLength: 10,
            responsive: true,
            language: {
                search: "<i class='fas fa-search'></i>",
                searchPlaceholder: "Search duties..."
            },
        });
    });
</script>
@endpush