@extends('layouts.app')
@section('content')
<h1 class="text-center text-warning" style="margin-bottom: 30px; margin-top:25px">
    <i class="fas fa-chart-line"></i> Weight Performance Management
</h1>

<div class="container mt-4 mx-auto">
    <!-- Filter Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-filter"></i> Filter Weight Performance</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('evaluation.rule.performance.weight.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-3">
                        <label for="position" class="form-label">Position</label>
                        <select id="position" name="position" class="form-select">
                            <option value="">All Positions</option>
                            @foreach($positions as $position)
                            <option value="{{ $position->id }}" {{ $positionFilter == $position->id ? 'selected' : '' }}>
                                {{ $position->position }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="criteria" class="form-label">Criteria</label>
                        <select id="criteria" name="criteria" class="form-select">
                            <option value="">All Criteria</option>
                            @foreach($criteria as $criterion)
                            <option value="{{ $criterion->id }}" {{ $criteriaFilter == $criterion->id ? 'selected' : '' }}>
                                {{ $criterion->type }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select id="status" name="status" class="form-select">
                            <option value="">All Statuses</option>
                            @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ $statusFilter == $status ? 'selected' : '' }}>
                                {{ $status }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <div class="d-flex gap-2 w-100">
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                <i class="fas fa-search me-1"></i> Apply Filters
                            </button>
                            <a href="{{ route('evaluation.rule.performance.weight.index') }}" class="btn btn-secondary flex-grow-1">
                                <i class="fas fa-sync-alt me-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="text-primary mt-2"><i class="fas fa-weight"></i> Weight Performance List</h5>
            <a href="{{ route('evaluation.rule.performance.weight.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i>Add Weight Performance
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive" style="padding-right: 1%;">
                <table class="table table-bordered table-striped mb-3 pt-3 align-middle align-items-center">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 5%;">NO</th>
                            <th>Criteria</th>
                            <th>Weight</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($groupedWeights as $positionId => $positionWeights)
                        @php
                        $position = $positionWeights->first()->position->position ?? 'N/A';
                        $rowNumber = 1; // Reset row number for each position group
                        @endphp

                        <tr class="table-info">
                            <td colspan="5" class="fw-bold">
                                {{ $position }}
                            </td>
                        </tr>

                        @foreach ($positionWeights as $weight)
                        <tr>
                            <td>{{ $rowNumber++ }}</td>
                            <td>{{ $weight->criteria->type ?? 'N/A' }}</td>
                            <td>{{ $weight->weight }}</td>
                            <td>
                                <span class="badge {{ $weight->status == 'Active' ? 'bg-success' : 'bg-danger' }}">
                                    {{ $weight->status }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('evaluation.rule.performance.weight.edit', $weight->id) }}" class="btn btn-warning btn-sm align-items-center d-flex justify-content-center">
                                    <i class="fa-solid fa-pen"></i>&nbsp;Edit
                                </a>
                            </td>
                        </tr>
                        @endforeach
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">No data found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
@endpush