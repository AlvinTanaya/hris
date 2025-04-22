@extends('layouts.app')

@section('content')
<h1 class="text-center text-warning" style="margin-bottom: 65px; margin-top:25px">
    <i class="fas fa-exclamation-triangle"></i> Warning Letter
</h1>

<div class="container mt-4 mx-auto">
    <!-- Filter Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="text-primary mt-2"><i class="fas fa-filter"></i> Filter Warning Letters</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('warning.letter.index2', ['id' => request()->route('id')]) }}" method="GET" class="row g-3">
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
                <div class="col-md-3">
                    <label class="form-label">Created From</label>
                    <input type="date" class="form-control" name="created_from" value="{{ request('created_from') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Created To</label>
                    <input type="date" class="form-control" name="created_to" value="{{ request('created_to') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Include No Expiry Date</label>
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" id="includeNoExpiry" name="include_no_expiry"
                            {{ isset($includeNoExpiry) && $includeNoExpiry ? 'checked' : '' }}>
                        <label class="form-check-label" for="includeNoExpiry">Show No Expiry Date</label>
                    </div>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>Apply Filters
                    </button>
                    <a href="{{ route('warning.letter.index2', ['id' => request()->route('id')]) }}" class="btn btn-secondary">
                        <i class="fas fa-undo me-2"></i>Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="text-primary mt-2"><i class="fas fa-exclamation-triangle"></i> Warning Letter List</h5>
        </div>

        <div class="card-body">
            <div class="table-responsive" style="padding-right: 1%;">
                <table id="warningLetterTable" class="table table-bordered table-striped mb-3 pt-3 align-middle align-items-center">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 5%;">NO</th>
                            <th>Reference Number</th>
                            <th>Type</th>
                            <th style="width: 30%;">Reason</th>
                            <th>Maker</th>
                            <th>Created At</th>
                            <th>Expired At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($warning_letters as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->warning_letter_number ?? "N/A" }}</td>
                            <td>{{ $item->type_name ?? "N/A"}}</td>
                            <td>{{ $item->reason_warning }}</td>
                            <td>{{ $item->maker_name }} ({{ $item->maker_employee_id }}) - {{ $item->maker_position }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->created_at)->format('Y-m-d') }}</td>
                            <td>{{ $item->expired_at ? \Carbon\Carbon::parse($item->expired_at)->format('Y-m-d') : "No Expired Date"}}</td>
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