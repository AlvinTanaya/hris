@extends('layouts.app')

@section('content')
<h1 class="text-center text-warning" style="margin-bottom: 65px; margin-top:25px">
    <i class="fas fa-user-tie"></i> Employee
</h1>
<div class="container mt-4 mx-auto">
    <!-- Filter Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="text-primary mt-2"><i class="fas fa-filter"></i> Filter Employees</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('user.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="status" class="form-label">Employment Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="Inactive" {{ request('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="Full Time" {{ request('status') == 'Full Time' ? 'selected' : '' }}>Full Time</option>
                        <option value="Part Time" {{ request('status') == 'Part Time' ? 'selected' : '' }}>Part Time</option>
                        <option value="Contract" {{ request('status') == 'Contract' ? 'selected' : '' }}>Contract</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="position" class="form-label">Position</label>
                    <select name="position" id="position" class="form-select">
                        <option value="">All Positions</option>
                        @foreach($position as $pos)
                        <option value="{{ $pos }}" {{ request('position') == $pos ? 'selected' : '' }}>{{ $pos }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="department" class="form-label">Department</label>
                    <select name="department" id="department" class="form-select">
                        <option value="">All Departments</option>
                        @foreach($department as $dept)
                        <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="status_app" class="form-label">Account Status</label>
                    <select name="status_app" id="status_app" class="form-select">
                        <option value="">All Account Status</option>
                        <option value="Banned" {{ request('status_app') == 'Banned' ? 'selected' : '' }}>Banned</option>
                        <option value="Unbanned" {{ request('status_app') == 'Unbanned' ? 'selected' : '' }}>Unbanned</option>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>Apply Filters
                    </button>
                    <a href="{{ route('user.index') }}" class="btn btn-secondary">
                        <i class="fas fa-undo me-2"></i>Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Existing Employee List Card -->
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="text-primary mt-2"><i class="fas fa-user"></i> Employee List</h5>
            <a href="{{ route('user.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i>Add Employee
            </a>
        </div>

        <div class="card-body">
            <div class="table-responsive" style="padding-right: 1%;">
                <table id="userTable" class="table table-bordered table-striped mb-3 pt-3 align-middle align-items-center">
                    <thead class="table-dark">
                        <tr>
                            <th style="width:8%">Profile</th>
                            <th>Name</th>
                            <!-- <th>Email</th> -->
                            <!-- <th>Phone Number</th> -->
                            <th>Employee Status</th>
                            <th>Contract Date</th>
                            <th>Position</th>
                            <th>Department</th>
                            <th>User Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($user as $item)
                        <tr>

                            <td class="text-center">
                                <img src="{{ $item->photo_profile_path ? asset('storage/'. $item->photo_profile_path) : asset('storage/default_profile.png') }}"
                                    class="rounded-circle"
                                    style="width: 40px; height: 40px; object-fit: cover; border: 2px solid blue;">
                            </td>
                            <td>{{ $item->name }}</td>
                            <!-- <td>{{ $item->email }}</td> -->
                            <!-- <td>{{ $item->phone_number }}</td> -->

                            {{-- Employee Status --}}
                            <td>
                                @php
                                $statusColors = [
                                'Inactive' => 'danger',
                                'Full Time' => 'success',
                                'Part Time' => 'primary',
                                'Contract' => 'warning'
                                ];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$item->employee_status] ?? 'secondary' }} fs-6">
                                    {{ $item->employee_status }}
                                </span>
                            </td>

                            {{-- Contract Date --}}
                            <td
                                @if(!is_null($item->contract_end_date) && !is_null($item->contract_start_date))
                                @php
                                $contractEndDate = \Carbon\Carbon::parse($item->contract_end_date);
                                $now = \Carbon\Carbon::now();
                                @endphp
                                @if($now->diffInDays($contractEndDate, false) <= 2 && $now <=$contractEndDate)
                                    style="background-color: yellow;"
                                    @elseif($now> $contractEndDate)
                                    style="background-color: red; color: white;"
                                    @endif
                                    @endif
                                    >
                                    {{ $item->contract_start_date ? $item->contract_start_date . ' s/d ' . $item->contract_end_date : '' }}
                            </td>


                            <td>{{ $item->position }}</td>
                            <td>{{ $item->department }}</td>

                            {{-- User Status --}}
                            <td>
                                <span class="badge bg-{{ $item->user_status == 'Banned' ? 'danger' : 'success' }} fs-6">
                                    {{ $item->user_status }}
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td class="d-flex">
                                <a href="{{ route('user.edit', $item->id) }}" class="btn btn-warning btn-sm me-2">
                                    <i class="fas fa-user-tie"></i> Employee Info
                                </a>
                                <a href="{{ route('user.transfer', $item->id) }}" class="btn btn-success btn-sm me-2">
                                    <i class="fas fa-exchange-alt"></i> Transfer
                                </a>
                                <a href="{{ route('user.history', $item->id) }}" class="btn btn-info btn-sm me-2">
                                    <i class="fas fa-history"></i> History
                                </a>
                                @if ($item->employee_status == 'Part Time' || $item->employee_status == 'Contract')
                                <button class="btn btn-primary btn-sm extend-btn" data-contract_end_date="{{ $item->contract_end_date }}" data-id="{{ $item->id }}" data-name="{{ $item->name }}">
                                    <i class="fas fa-calendar-plus"></i> Extend
                                </button>
                                @else
                                <button class="btn btn-primary btn-sm" disabled>
                                    <i class="fas fa-calendar-plus"></i> Extend
                                </button>
                                @endif

                            </td>

                        </tr>


                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Extend -->
<div class="modal fade" id="extendModal" tabindex="-1" aria-labelledby="extendModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="extendModalLabel">Extend Contract for <span id="userName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="extendForm" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="user_id" name="user_id">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="contract_start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="contract_start_date" name="contract_start_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="contract_end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="contract_end_date" name="contract_end_date" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="reason" class="form-label">Reason</label>
                            <textarea class="form-control" id="reason" name="reason" maxlength="255" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        $('#userTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true
        });

        $(".extend-btn").click(function() {
            let userId = $(this).data("id");
            let userName = $(this).data("name");
            let contractEndDate = $(this).data("contract_end_date");

            // Tambah 1 hari ke contract_end_date jika ada, kalau tidak ada pakai hari ini +1
            let contractStartDate = contractEndDate ?
                new Date(new Date(contractEndDate).setDate(new Date(contractEndDate).getDate() + 1)) :
                new Date(new Date().setDate(new Date().getDate() + 1));

            // Format ke YYYY-MM-DD
            contractStartDate = contractStartDate.toISOString().split("T")[0];

            // Set nilai di modal
            $("#user_id").val(userId);
            $("#userName").text(userName);
            $("#contract_start_date").val(contractStartDate);
            $("#contract_end_date").val(""); // Kosongkan input end date agar user bisa isi sendiri

            // Set action form dinamis
            let actionUrl = "/user/extend-date/" + userId;
            $("#extendForm").attr("action", actionUrl);

            // Tampilkan modal
            $("#extendModal").modal("show");
        });



    });

    @if(session('success'))
    Swal.fire({
        title: "Success!",
        text: "{{ session('success') }}",
        icon: "success",
        confirmButtonText: "OK"
    });
    @endif
</script>
@endpush