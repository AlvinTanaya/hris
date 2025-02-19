@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1 class="text-center text-warning" style="margin-bottom: 65px; margin-top:25px"><i class="fas fa-pencil"></i> E-learning Duty</h1>

    <div class="container mt-4 mx-auto">
        <!-- Filter Card -->
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="text-primary mt-2"><i class="fas fa-filter"></i> Filter Duties</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('elearning.index2', Auth::user()->id) }}" method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label for="duration_range" class="form-label">Duration</label>
                        <select class="form-select" id="duration_range" name="duration_range">
                            <option value="">All Durations</option>
                            <option value="0-60" {{ request('duration_range') == '0-60' ? 'selected' : '' }}>0-60 minutes</option>
                            <option value="61-120" {{ request('duration_range') == '61-120' ? 'selected' : '' }}>1-2 hours</option>
                            <option value="121-180" {{ request('duration_range') == '121-180' ? 'selected' : '' }}>2-3 hours</option>
                            <option value="181+" {{ request('duration_range') == '181+' ? 'selected' : '' }}>3+ hours</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-4">
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
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="text-primary mt-2"><i class="fas fa-briefcase"></i> duty List</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive" style="padding-right: 1%;">
                    <table id="dutyTable" class="table table-bordered table-hover mb-3 pt-3">
                        <thead class="table-dark">
                            <tr>
                                <th>Event Name</th>
                                <th>Duration (minutes)</th>
                                <th>Date Start</th>
                                <th>Date End</th>
                                <th>Passing Grade</th>
                                <th>Grade</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($duty as $item)
                            <tr>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->duration }} minutes</td>
                                <td>{{ $item->start_date }}</td>
                                <td>{{ $item->end_date }}</td>
                                <td>{{ $item->passing_grade }}</td>
                                <td>{{ $item->grade }}</td>
                                <td>
                                    @php
                                    $today = \Carbon\Carbon::today();
                                    $startDate = \Carbon\Carbon::parse($item->start_date);
                                    $endDate = \Carbon\Carbon::parse($item->end_date);
                                    $isActive = ($today->between($startDate, $endDate) && is_null($item->grade));
                                    @endphp
                                    @if($isActive)
                                    <a href="{{ route('elearning.elearning_material', $item->invitation_id ) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-pencil-alt"></i> E-learning Task
                                    </a>
                                    @else
                                    <a class="btn btn-warning btn-sm disabled">
                                        <i class="fas fa-pencil-alt"></i> E-learning Task
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
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#dutyTable').DataTable();
    });
</script>
@endpush