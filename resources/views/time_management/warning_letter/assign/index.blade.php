@extends('layouts.app')

@section('content')
<h1 class="text-center text-warning" style="margin-bottom: 65px; margin-top:25px">
    <i class="fas fa-exclamation-triangle"></i> Warning Letter
</h1>

<div class="container mt-4 mx-auto">
    <!-- Filter Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="text-primary mt-2"><i class="fas fa-filter"></i> Filter Employees</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('warning.letter.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Employee</label>
                    <select class="form-select" name="employee">
                        <option value="">All Employees</option>
                        @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ request('employee') == $employee->id ? 'selected' : '' }}>
                            {{ $employee->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="position" class="form-label">Position</label>
                    <select name="position" id="position" class="form-select">
                        <option value="">All Positions</option>
                        @foreach($positions as $pos)
                        <option value="{{ $pos }}" {{ request('position') == $pos ? 'selected' : '' }}>
                            {{ $pos }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Department</label>
                    <select class="form-select" name="department">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                        <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>
                            {{ $dept }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Warning Type</label>
                    <select class="form-select" name="type">
                        <option value="">All Types</option>
                        @foreach($types as $type)
                        <option value="{{ $type->id }}" {{ request('type') == $type->id ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>Apply Filters
                    </button>
                    <a href="{{ route('warning.letter.index') }}" class="btn btn-secondary">
                        <i class="fas fa-undo me-2"></i>Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="text-primary mt-2"><i class="fas fa-exclamation-triangle"></i> Warning Letter List</h5>
            <a href="{{ route('warning.letter.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i>Add Warning Letter
            </a>
        </div>

        <div class="card-body">
            <div class="table-responsive" style="padding-right: 1%;">
                <table id="warningLetterTable" class="table table-bordered table-striped mb-3 pt-3 align-middle align-items-center">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 5%;">NO</th>
                            <th>Employee</th>
                            <th>Department</th>
                            <th>Position</th>
                            <th>Reference Number</th>

                            <th>Type</th>
                            <th>Reason</th>
                            <th>Maker</th>
                            <th>Created At</th>
                            <th>Expired At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($warning_letters as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->employee_name }} ({{ $item->employee_id }})</td>
                            <td>{{ $item->employee_department ?? "N/A" }}</td>
                            <td>{{ $item->employee_position ?? "N/A" }}</td>
                            <td>{{ $item->warning_letter_number ?? "N/A" }}</td>
                            <td>{{ $item->type_name ?? "N/A"}}</td>
                            <td>{{ $item->reason_warning }}</td>
                            <td>{{ $item->maker_name }} ({{ $item->maker_id }})</td>
                            <td>{{ \Carbon\Carbon::parse($item->created_at)->format('Y-m-d') }}</td>
                            <td>{{ $item->expired_at ?? "No Expired Date"}}</td>
                            <td>
                                <a href="{{ route('warning.letter.edit', $item->id) }}" class="btn btn-warning btn-sm align-items-center d-flex justify-content-center">
                                    <i class="fa-solid fa-pen"></i>&nbsp;Edit
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $('#warningLetterTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true
        });
    });
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
@endpush