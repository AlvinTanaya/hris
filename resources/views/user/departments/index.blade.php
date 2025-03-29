@extends('layouts.app')

@section('content')
<div class="container mt-4 mx-auto">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mt-2"><i class="fas fa-building"></i> Departments</h5>
                <a href="{{ route('user.departments.create') }}" class="btn btn-light text-primary">
                    <i class="fas fa-plus-circle me-1"></i> Add Department
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="departmentsTable" class="table table-bordered table-striped mb-3 pt-3 align-middle align-items-center">
                    <thead class="table-dark">
                        <tr>
                            <th width="10%">ID</th>
                            <th>Department Name</th>
                            <th width="15%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($departments as $department)
                        <tr>
                            <td>{{ $department->id }}</td>
                            <td>{{ $department->department }}</td>
                            <td>
                                <a href="{{ route('user.departments.edit', $department->id) }}"
                                    class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> Edit
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
        $('#departmentsTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true
        });
    });
</script>
@endpush