@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 font-weight-bold">Custom Holiday Management</h5>
                    <a href="{{ route('time.custom.holiday.create') }}" class="btn btn-light text-primary btn-sm">
                        <i class="fas fa-plus"></i> Add Holiday
                    </a>
                </div>
                <div class="card-body">


                    <div class="table-responsive">
                        <table id="holidays-table" class="table table-bordered table-striped mb-3 pt-3 align-middle align-items-center">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($holidays as $holiday)
                                <tr>
                                    <td>{{ $holiday->id }}</td>
                                    <td>{{ $holiday->name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($holiday->date)->format('d M Y') }}</td>

                                    <td>{{ $holiday->description }}</td>
                                    <td>
                                        <a href="{{ route('time.custom.holiday.edit', $holiday->id) }}"
                                            class="btn btn-sm btn-warning">Edit</a>
                                        <form action="{{ route('time.custom.holiday.destroy', $holiday->id) }}"
                                            method="POST" style="display:inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
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
</div>
@endsection

@push('scripts')


<script>
    $(document).ready(function() {
        $('#holidays-table').DataTable({
            responsive: true,

        });
    });
</script>
@endpush