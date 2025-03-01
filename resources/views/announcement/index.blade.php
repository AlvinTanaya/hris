@extends('layouts.app')

@section('content')
<h1 class="text-center text-warning" style="margin-bottom: 65px; margin-top:25px">
    <i class="fa-solid fa-bullhorn"></i> Announcement
</h1>

<div class="container mt-4 mx-auto">
    <!-- Filter Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="text-primary mt-2"><i class="fas fa-filter"></i> Filter Announcement</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('announcement.index') }}" method="GET" class="row g-3">
                <!-- Maker Filter -->
                <div class="col-md-4">
                    <label for="maker_id" class="form-label">Maker</label>
                    <select name="maker_id" id="maker_id" class="form-select">
                        <option value="">All</option>
                        @foreach($makers as $maker)
                        <option value="{{ $maker->id }}" {{ request('maker_id') == $maker->id ? 'selected' : '' }}>
                            {{ $maker->employee_id }}_{{ $maker->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Users Filter -->
                <div class="col-md-4">
                    <label for="user_id" class="form-label">Users</label>
                    <select name="user_id" id="user_id" class="form-select">
                        <option value="">All</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->employee_id }}_{{ $user->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Date Filter -->
                <div class="col-md-4">
                    <label for="created_at" class="form-label">Date</label>
                    <input type="date" name="created_at" id="created_at" class="form-control" value="{{ request('created_at') }}">
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>Apply Filters
                    </button>
                    <a href="{{ route('announcement.index') }}" class="btn btn-secondary">
                        <i class="fas fa-undo me-2"></i>Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- List Card -->
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="text-primary mt-2"><i class="fa-solid fa-bullhorn"></i> Announcement List</h5>
            <a href="{{ route('announcement.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i> Add Announcement
            </a>
        </div>

        <div class="card-body">
            <div class="table-responsive" style="padding-right: 1%;">
                <table id="announcementTable" class="table table-bordered table-striped mb-3 pt-3 align-middle align-items-center">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th style="width:15%">Maker</th>
                            <th>Message</th>
                            <th>Users</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($announcements as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->maker->employee_id?? '-' }}_{{ $item->maker->name ?? '-' }}</td> <!-- Ambil nama maker -->
                            <td>{{ $item->message }}</td>
                            <td>
                                <button class="btn btn-info btn-sm view-users" data-message="{{ $item->message }}" data-created-at="{{ $item->created_at }}">
                                    <i class="fas fa-eye"></i> View
                                </button>

                            </td>
                            <td>{{ $item->created_at }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="usersModal" tabindex="-1" aria-labelledby="usersModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg"> <!-- Tambahkan modal-lg di sini -->
        <div class="modal-content">
            <div class="modal-header text-white bg-primary">
                <h5 class="modal-title" id="usersModalLabel"><i class="fas fa-users"></i> User List</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="table-responsive" style="padding-right: 1%;">
                    <table id="viewTable" class="table table-bordered table-striped mb-3 pt-3 align-middle align-items-center">
                        <thead class="table-dark">
                            <tr>
                                <th>Emp ID</th>
                                <th>Name</th>
                                <th>Position</th>
                                <th>Department</th>
                            </tr>
                        </thead>
                        <tbody id="usersTableBody">
                            <!-- Data akan dimasukkan dengan AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>



@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function() {
        $('#announcementTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true
        });

        $('#viewTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true
        });

        $('.view-users').click(function() {
            var message = $(this).data('message');
            var created_at = $(this).data('created-at'); // Ambil created_at dari tombol

            $.ajax({
                url: "{{ route('announcement.users') }}",
                type: "GET",
                data: {
                    message: message,
                    created_at: created_at // Kirim created_at
                },
                success: function(response) {
                    console.log(response.users); // Cek apakah ada data users

                    $('#usersTableBody').empty();

                    if (response.users.length > 0) {
                        response.users.forEach(user => {
                            console.log(user); // Debug setiap user sebelum ditampilkan
                            $('#usersTableBody').append(`
                                <tr>
                                    <td>${user.employee_id ?? '-'}</td>
                                    <td>${user.name ?? '-'}</td>
                                    <td>${user.position ?? '-'}</td>
                                    <td>${user.department ?? '-'}</td>
                                </tr>
                            `);
                        });
                    } else {
                        $('#usersTableBody').append(`
                            <tr><td colspan="4" class="text-center">No users found</td></tr>
                        `);
                    }

                    $('#usersModal').modal('show');
                }

            });
        });


    });
</script>
@endpush