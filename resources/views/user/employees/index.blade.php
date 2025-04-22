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
            <form action="{{ route('user.employees.index') }}" method="GET" class="row g-3">
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
                    <label for="position_id" class="form-label">Position</label>
                    <select name="position_id" id="position_id" class="form-select">
                        <option value="">All Positions</option>
                        @foreach($positions as $position)
                        <option value="{{ $position->id }}" {{ request('position_id') == $position->id ? 'selected' : '' }}>
                            {{ $position->position }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="department_id" class="form-label">Department</label>
                    <select name="department_id" id="department_id" class="form-select">
                        <option value="">All Departments</option>
                        @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                            {{ $department->department }}
                        </option>
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
                    <a href="{{ route('user.employees.index') }}" class="btn btn-secondary">
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
            <div style="margin-right: 12px;">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importModal">
                    <i class="fas fa-file-excel me-2"></i> Import Employees
                </button>
                <a href="{{ route('user.employees.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle me-2"></i>Add Employee
                </a>
            </div>
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
                        @foreach ($users as $item)
                        <tr>

                            <td class="text-center">
                                <img src="{{ $item->photo_profile_path ? asset('storage/'. $item->photo_profile_path) : asset('storage/default_profile.png') }}"
                                    class="rounded-circle"
                                    style="width: 40px; height: 40px; object-fit: cover; border: 2px solid blue;">
                            </td>

                            <td>({{ $item->employee_id }}) - {{ $item->name }}</td>
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
                                $contractEndDate = strtotime($item->contract_end_date);
                                $now = time();
                                @endphp
                                @if(($contractEndDate - $now) <= (2 * 30 * 24 * 60 * 60) && $contractEndDate>= $now)
                                    style="background-color: yellow;"
                                    @elseif($contractEndDate < $now)
                                        style="background-color: red; color: white;"
                                        @endif
                                        @endif>
                                        {{ $item->contract_start_date ? substr($item->contract_start_date, 0, 10) . ' s/d ' . substr($item->contract_end_date, 0, 10) : '' }}
                            </td>


                            <td>{{ $item->position->position ?? 'N/A' }}</td>
                            <td>{{ $item->department->department ?? 'N/A' }}</td>

                            {{-- User Status --}}
                            <td>
                                <span class="badge bg-{{ $item->user_status == 'Banned' ? 'danger' : 'success' }} fs-6">
                                    {{ $item->user_status }}
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td class="d-flex">
                                <a href="{{ route('user.employees.edit', $item->id) }}" class="btn btn-warning btn-sm me-2">
                                    <i class="fas fa-user-tie"></i> Employee Info
                                </a>
                                <a href="{{ route('user.employees.transfer', $item->id) }}" class="btn btn-success btn-sm me-2">
                                    <i class="fas fa-exchange-alt"></i> Transfer
                                </a>
                                <a href="{{ route('user.employees.history', $item->id) }}" class="btn btn-info btn-sm me-2">
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



<!-- Modal Import -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import Employees</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="importForm" action="{{ route('employees.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="file" class="form-label">Upload Excel File</label>
                        <input type="file" name="file" id="file" class="form-control" required>
                        <small class="text-muted">Format file harus .xlsx dengan urutan kolom:</small>
                        <ul class="text-muted small">
                            <li><b>name</b> - Nama Karyawan</li>
                            <li><b>email</b> - Email Karyawan</li>
                            <li><b>position</b> - Jabatan (Use the Same Name as in Master Position)</li>
                            <li><b>department</b> - Department (Use the Same Name as in Master Department)</li>
                            <li><b>ID_number</b> - Nomor Identitas</li>
                            <li><b>birth_date</b> - Tanggal Lahir (YYYY-MM-DD)</li>
                            <li><b>birth_place</b> - Tempat Lahir</li>
                            <li><b>ID_address</b> - Alamat KTP</li>
                            <li><b>domicile_address</b> - Alamat Domisili</li>
                            <li><b>religion</b> - Islam/Kristen/Katolik/Buddha/Hindu/Konghucu</li>
                            <li><b>gender</b> - Male / Female</li>
                            <li><b>phone_number</b> - Nomor HP (08XXXXXXXXXX)</li>
                            <li><b>employee_status</b> - Full Time / Part Time / Contract</li>
                            <li><b>contract_start_date</b> - (Opsional, jika Contract/Part Time)</li>
                            <li><b>contract_end_date</b> - (Opsional, jika Contract/Part Time)</li>
                            <li><b>join_date</b> - Tanggal Bergabung (YYYY-MM-DD)</li>
                            <li><b>NPWP</b> - Format XX.XXX.XXX.X-XXX.XXX</li>
                            <li><b>bpjs_employment</b> - BPJS Ketenagakerjaan</li>
                            <li><b>bpjs_health</b> - BPJS Kesehatan</li>
                            <li><b>exit_date</b> - Tanggal Keluar (YYYY-MM-DD, Opsional)</li>
                            <li><b>status</b> - Status Pajak (TK/0, TK/1, TK/2, TK/3, K/1, K/2, K/3)</li>
                            <li><b>bank_name</b> - Nama Bank (Bank Central Asia (BCA), Bank Mandiri, Bank Rakyat Indonesia (BRI), Bank Negara Indonesia (BNI), Bank CIMB Niaga, Bank Tabungan Negara (BTN), Bank Danamon, Bank Permata, Bank Panin, Bank OCBC NISP, Bank Maybank Indonesia, Bank Mega, Bank Bukopin, Bank Sinarmas)</li>
                            <li><b>bank_number</b> - Nomor Rekening</li>
                            <li><b>emergency_contact</b> - Kontak Darurat (08XXXXXXXXXX)</li>
                            <li><b>degree</b> - Pendidikan Terakhir (SMA/SMK/S1/S2)</li>
                            <li><b>major</b> - Jurusan</li>
                        </ul>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ asset('storage/sample_employee_import.xlsx') }}" class="btn btn-info">
                            <i class="fas fa-download me-2"></i> Download Sample
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-file-excel me-2"></i> Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>




@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {

        $('#importForm').on('submit', function(event) {
            event.preventDefault();

            var formData = new FormData(this);

            $.ajax({
                url: "{{ route('user.employees.import') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Import Berhasil!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    let errorMessage = "Gagal mengimpor file.";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Import Gagal!',
                        text: errorMessage,
                    });
                }
            });
        });

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
            let actionUrl = "employees/extend-date/" + userId;
            $("#extendForm").attr("action", actionUrl);

            // Tampilkan modal
            $("#extendModal").modal("show");
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