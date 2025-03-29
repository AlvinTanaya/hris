@extends('layouts.app')

@section('content')
<h1 class="text-center text-warning" style="margin-bottom: 65px; margin-top:25px">
    <i class="fas fa-exclamation-triangle"></i> Warning Letter
</h1>

<div class="container mt-4 mx-auto">
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
                            <th>Type</th>
                            <th style="width: 50%;">Reason</th>
                            <th>From</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($warning_letter as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{$item->type}}</td>
                            <td>{{$item->reason_warning}}</td>
                            <td>
                                {{ $item->employee_name }} ({{ $item->employee_id }}) - {{ $item->employee_position }}
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