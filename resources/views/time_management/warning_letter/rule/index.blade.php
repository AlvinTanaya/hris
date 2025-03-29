@extends('layouts.app')

@section('content')
<h1 class="text-center text-warning" style="margin-bottom: 65px; margin-top:25px">
    <i class="fa-solid fa-scale-balanced"></i> Warning Letter Rules
</h1>

<div class="container mt-4 mx-auto">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="text-primary mt-2"><i class="fas fa-list"></i> Rules List</h5>
            <a href="{{ route('warning.letter.rule.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i>Add New Rule
            </a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="rulesTable" class="table table-bordered table-striped mb-3 pt-3">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 5%;">NO</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Expired Length (months)</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rules as $index => $rule)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $rule->name }}</td>
                            <td>{{ $rule->description ?? '-' }}</td>
                            <td>{{ $rule->expired_length ?? '-' }}</td>
                            <td>
                                <a href="{{ route('warning.letter.rule.edit', $rule->id) }}"
                                    class="btn btn-warning btn-sm">
                                    <i class="fa-solid fa-pen"></i> Edit
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
        $('#rulesTable').DataTable({
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