@extends('layouts.app')

@section('content')
<h1 class="text-center text-warning" style="margin-bottom: 65px; margin-top:25px">
    <i class="fas fa-calendar-check"></i> Time Off Policies
</h1>

<div class="container mt-4 mx-auto">
    <!-- Filter Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="text-primary mt-2"><i class="fas fa-filter"></i> Filter Policies</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('time.off.policy.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Policy Name</label>
                    <select class="form-select" name="name">
                        <option value="">All Policies</option>
                        @foreach($policyNames as $policyName)
                        <option value="{{ $policyName }}" {{ request('name') == $policyName ? 'selected' : '' }}>{{ $policyName }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Start Date</label>
                    <input type="date" class="form-control" name="start_date" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">End Date</label>
                    <input type="date" class="form-control" name="end_date" value="{{ request('end_date') }}">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>Apply Filters
                    </button>
                    <a href="{{ route('time.off.policy.index') }}" class="btn btn-secondary">
                        <i class="fas fa-undo me-2"></i>Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="text-primary mt-2"><i class="fas fa-list"></i> Time Off Policies List</h5>
            <a href="{{ route('time.off.policy.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i>Add Time Off Policy
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="policiesTable" class="table table-bordered table-striped mb-3 pt-3 align-middle align-items-center">
                    <thead class="table-dark">
                        <tr>
                            <th width="5%">No</th>
                            <th width="20%">Name</th>
                            <th>Description</th>
                            <th width="7%">Quota</th>
                            <th width="15%">Start Date</th>
                            <th width="15%">End Date</th>
                            <th width="10%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($policies as $key => $policy)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $policy->time_off_name }}</td>
                            <td>{{ $policy->time_off_description}}</td>
                            <td>{{ $policy->quota }}</td>
                            <td>{{ date('d-m-Y', strtotime($policy->start_date)) }}</td>
                            <td>
                                {{ $policy->end_date ? date('d-m-Y', strtotime($policy->end_date)) : 'No Expiration' }}
                            </td>

                            <td>
                                <a href="{{ route('time.off.policy.edit', $policy->id) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit me-1"></i>Edit
                                </a>
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

@push('scripts')
<script>
    $(document).ready(function() {
        $('#policiesTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true
        });
    });
</script>
@endpush