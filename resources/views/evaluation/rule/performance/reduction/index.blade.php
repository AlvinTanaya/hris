@extends('layouts.app')
@section('content')
<h1 class="text-center text-warning" style="margin-bottom: 65px; margin-top:25px">
    <i class="fas fa-minus-circle"></i> Performance Reduction Rules
</h1>
<div class="container mt-4 mx-auto">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h5 class="text-primary mt-2"><i class="fas fa-minus-circle"></i> Performance Reduction List</h5>
            </div>
            <div class="d-flex">
                <select id="statusFilter" class="form-select me-2" style="width: 150px;">
                    <option value="">All Status</option>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
                <a href="{{ route('evaluation.rule.performance.reduction.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle me-2"></i>Add Reduction Rule
                </a>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive" style="padding-right: 1%;">
                <table id="reductionPerformanceTable" class="table table-bordered table-striped mb-3 pt-3 align-middle align-items-center">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 5%;">NO</th>
                            <th>Warning Letter Type</th>
                            <th>Weight</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reductions as $index => $reduction)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $reduction->warningLetterRule->name ?? 'N/A' }}</td>
                            <td>{{ $reduction->weight }}</td>
                            <td>
                                <span class="badge {{ $reduction->status === 'Active' ? 'bg-success' : 'bg-secondary' }} text-white py-1 px-2 rounded-pill">
                                    <i class="fas {{ $reduction->status === 'Active' ? 'fa-check-circle' : 'fa-times-circle' }} me-1"></i>
                                    {{ $reduction->status }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('evaluation.rule.performance.reduction.edit', $reduction->id) }}" class="btn btn-warning btn-sm align-items-center d-flex justify-content-center">
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
        var table = $('#reductionPerformanceTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "columnDefs": [{
                "targets": [3], // Status column
                "searchable": true,
                "orderable": true,
                "render": function(data, type, row) {
                    if (type === 'filter' || type === 'sort') {
                        return $(data).text().trim();
                    }
                    return data;
                }
            }]
        });

        // Status filter (now using actual status field instead of weight)
        $('#statusFilter').change(function() {
            var status = $(this).val();
            
            if (status === '') {
                table.search('').draw();
                return;
            }
            
            // Search in the status column (index 3) for the status text
            table.column(3).search('^' + status + '$', true, false).draw();
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