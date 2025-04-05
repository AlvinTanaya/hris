@extends('layouts.app')
@section('content')
<h1 class="text-center text-warning" style="margin-bottom: 65px; margin-top:25px">
    <i class="fas fa-chart-line"></i> Criteria Evaluation Performance
</h1>
<div class="container mt-4 mx-auto">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="text-primary mt-2"><i class="fas fa-chart-line"></i> Criteria Performance List</h5>
            <a href="{{ route('evaluation.rule.performance.criteria.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i>Add Criteria Performance
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive" style="padding-right: 1%;">
                <table id="criteriaPerformanceTable" class="table table-bordered table-striped mb-3 pt-3 align-middle align-items-center">
                    <thead class="table-dark">
                        
                        <tr>
                            <th style="width: 5%;">NO</th>
                            <th>Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($criteria_performances as $index => $performance)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $performance->type }}</td>
                            <td>
                                <a href="{{ route('evaluation.rule.performance.criteria.edit', $performance->id) }}" class="btn btn-warning btn-sm align-items-center d-flex justify-content-center">
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
        $('#criteriaPerformanceTable').DataTable({
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