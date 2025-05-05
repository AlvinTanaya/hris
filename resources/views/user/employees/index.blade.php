@extends('layouts.app')

@section('content')



<div class="container mb-5">
    <!-- Hero Header Section -->
    <div class="shadow-sm mb-4">
        <div class="container py-4">
            <div class="d-flex align-items-center">
                <div class="bg-white p-3 rounded-circle shadow me-3">
                    <i class="fas fa-user-tie text-primary fa-2x"></i>
                </div>
                <h1 class="text-white mb-0 fw-bold">Employee Management</h1>
            </div>
            <p class="text-white-50 mt-2 mb-0">Manage your organization's workforce efficiently</p>
        </div>
    </div>
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                        <i class="fas fa-users text-primary fa-2x"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Total Employees</h6>
                        <h3 class="mb-0">{{ $users->count() }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                        <i class="fas fa-briefcase text-success fa-2x"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Full Time</h6>
                        <h3 class="mb-0">{{ $users->where('employee_status', 'Full Time')->count() }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3">
                        <i class="fas fa-user-clock text-info fa-2x"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Part Time/Contract</h6>
                        <h3 class="mb-0">{{ $users->whereIn('employee_status', ['Part Time', 'Contract'])->count() }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-danger bg-opacity-10 p-3 me-3">
                        <i class="fas fa-user-slash text-danger fa-2x"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Inactive</h6>
                        <h3 class="mb-0">{{ $users->where('employee_status', 'Inactive')->count() }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm mb-4 rounded-3 overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom">
            <div class="d-flex align-items-center">
                <i class="fas fa-filter text-primary me-2"></i>
                <h5 class="card-title mb-0 fw-bold">Filter Employees</h5>
            </div>
        </div>
        <div class="card-body bg-light">
            <form action="{{ route('user.employees.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="status" class="form-label fw-semibold small text-muted mb-1">Employment Status</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-user-tag text-muted"></i>
                        </span>
                        <select name="status" id="status" class="form-select border-start-0 ps-0">
                            <option value="">All Status</option>
                            <option value="Inactive" {{ request('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="Full Time" {{ request('status') == 'Full Time' ? 'selected' : '' }}>Full Time</option>
                            <option value="Part Time" {{ request('status') == 'Part Time' ? 'selected' : '' }}>Part Time</option>
                            <option value="Contract" {{ request('status') == 'Contract' ? 'selected' : '' }}>Contract</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="position_id" class="form-label fw-semibold small text-muted mb-1">Position</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-sitemap text-muted"></i>
                        </span>
                        <select name="position_id" id="position_id" class="form-select border-start-0 ps-0">
                            <option value="">All Positions</option>
                            @foreach($positions as $position)
                            <option value="{{ $position->id }}" {{ request('position_id') == $position->id ? 'selected' : '' }}>
                                {{ $position->position }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="department_id" class="form-label fw-semibold small text-muted mb-1">Department</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-building text-muted"></i>
                        </span>
                        <select name="department_id" id="department_id" class="form-select border-start-0 ps-0">
                            <option value="">All Departments</option>
                            @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                {{ $department->department }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="status_app" class="form-label fw-semibold small text-muted mb-1">Account Status</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-user-shield text-muted"></i>
                        </span>
                        <select name="status_app" id="status_app" class="form-select border-start-0 ps-0">
                            <option value="">All Account Status</option>
                            <option value="Banned" {{ request('status_app') == 'Banned' ? 'selected' : '' }}>Banned</option>
                            <option value="Unbanned" {{ request('status_app') == 'Unbanned' ? 'selected' : '' }}>Unbanned</option>
                        </select>
                    </div>
                </div>
                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-search me-2"></i>Apply Filters
                    </button>
                    <a href="{{ route('user.employees.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-undo me-2"></i>Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Quick Action Buttons -->
    <div class="d-flex justify-content-end mb-3 gap-2">
        <button type="button" class="btn btn-success d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="fas fa-file-excel me-2"></i> Import Employees
        </button>
        <a href="{{ route('user.employees.create') }}" class="btn btn-primary d-flex align-items-center">
            <i class="fas fa-plus-circle me-2"></i>Add Employee
        </a>
    </div>

    <!-- Employee List Card -->
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom">
            <div class="d-flex align-items-center">
                <i class="fas fa-user text-primary me-2"></i>
                <h5 class="card-title mb-0 fw-bold">Employee List</h5>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive p-3">
                <table id="userTable" class="table table-hover mb-0 align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3">Employee</th>
                            <th>Status</th>
                            <th>Contract Period</th>
                            <th>Position</th>
                            <th>Department</th>
                            <th>Account</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $item)
                        <tr>
                            <td class="ps-3">
                                <div class="d-flex align-items-center">
                                    <div class="position-relative me-3">
                                        <img src="{{ $item->photo_profile_path ? asset('storage/'. $item->photo_profile_path) : asset('storage/default_profile.png') }}"
                                            class="rounded-circle"
                                            style="width: 48px; height: 48px; object-fit: cover;">

                                        @if($item->user_status == 'Banned')
                                        <span class="position-absolute bottom-0 end-0 bg-danger rounded-circle p-1"
                                            style="width: 12px; height: 12px;"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="top"
                                            title="Account Banned"></span>
                                        @else
                                        <span class="position-absolute bottom-0 end-0 bg-success rounded-circle p-1"
                                            style="width: 12px; height: 12px;"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="top"
                                            title="Account Active"></span>
                                        @endif
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $item->name }}</h6>
                                        <span class="text-muted small">ID: {{ $item->employee_id }}</span>
                                    </div>
                                </div>
                            </td>

                            <td>
                                @php
                                $statusColors = [
                                'Inactive' => ['bg-danger bg-opacity-10 text-danger', 'fa-user-times'],
                                'Full Time' => ['bg-success bg-opacity-10 text-success', 'fa-user-check'],
                                'Part Time' => ['bg-primary bg-opacity-10 text-primary', 'fa-user-clock'],
                                'Contract' => ['bg-warning bg-opacity-10 text-warning', 'fa-file-contract']
                                ];

                                $statusInfo = $statusColors[$item->employee_status] ?? ['bg-secondary bg-opacity-10 text-secondary', 'fa-question-circle'];
                                @endphp
                                <span class="badge {{ $statusInfo[0] }} px-3 py-2 rounded-pill fw-normal">
                                    <i class="fas {{ $statusInfo[1] }} me-1"></i> {{ $item->employee_status }}
                                </span>
                            </td>

                            <td>
                                @if(!is_null($item->contract_start_date) && !is_null($item->contract_end_date))
                                @php
                                $contractEndDate = strtotime($item->contract_end_date);
                                $now = time();
                                $diffDays = round(($contractEndDate - $now) / (60 * 60 * 24));
                                @endphp

                                <div class="d-flex flex-column">
                                    <span class="small">{{ date('d M Y', strtotime($item->contract_start_date)) }} - {{ date('d M Y', strtotime($item->contract_end_date)) }}</span>

                                    @if($diffDays <= 60 && $diffDays>= 0)
                                        <div class="d-flex align-items-center mt-1">
                                            <div class="progress flex-grow-1" style="height: 5px;">
                                                <div class="progress-bar bg-warning" style="width: {{ 100 - min(100, ($diffDays / 60) * 100) }}%"></div>
                                            </div>
                                            <span class="badge bg-warning text-dark ms-2 small">{{ $diffDays }} days left</span>
                                        </div>
                                        @elseif($diffDays < 0)
                                            <span class="badge bg-danger text-white mt-1">Expired {{ abs($diffDays) }} days ago</span>
                                            @else
                                            <span class="badge bg-success text-white mt-1">Active</span>
                                            @endif
                                </div>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>

                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="bg-info bg-opacity-10 text-info px-2 py-1 rounded-pill me-2">
                                        <i class="fas fa-briefcase"></i>
                                    </span>
                                    {{ $item->position->position ?? 'N/A' }}
                                </div>
                            </td>

                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="bg-primary bg-opacity-10 text-primary px-2 py-1 rounded-pill me-2">
                                        <i class="fas fa-building"></i>
                                    </span>
                                    {{ $item->department->department ?? 'N/A' }}
                                </div>
                            </td>

                            <td>
                                @if($item->user_status == 'Banned')
                                <span class="badge bg-danger px-3 py-2 rounded-pill fw-normal">
                                    <i class="fas fa-ban me-1"></i> Banned
                                </span>
                                @else
                                <span class="badge bg-success px-3 py-2 rounded-pill fw-normal">
                                    <i class="fas fa-check-circle me-1"></i> Active
                                </span>
                                @endif
                            </td>

                            <td class="text-end pe-3">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton{{ $item->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" aria-labelledby="dropdownMenuButton{{ $item->id }}">
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center" href="{{ route('user.employees.edit', $item->id) }}">
                                                <i class="fas fa-user-edit text-warning me-2"></i> Edit Employee Info
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center" href="{{ route('user.employees.transfer', $item->id) }}">
                                                <i class="fas fa-exchange-alt text-success me-2"></i> Transfer Employee
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center" href="{{ route('user.employees.history', $item->id) }}">
                                                <i class="fas fa-history text-info me-2"></i> View History
                                            </a>
                                        </li>
                                        <li>
                                            @if ($item->employee_status == 'Part Time' || $item->employee_status == 'Contract')
                                            <button class="dropdown-item d-flex align-items-center extend-btn"
                                                data-contract_end_date="{{ $item->contract_end_date }}"
                                                data-id="{{ $item->id }}"
                                                data-name="{{ $item->name }}">
                                                <i class="fas fa-calendar-plus text-primary me-2"></i> Extend Contract
                                            </button>
                                            @else
                                            <button class="dropdown-item d-flex align-items-center" disabled>
                                                <i class="fas fa-calendar-plus text-muted me-2"></i> Extend Contract
                                            </button>
                                            @endif
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Extend Contract -->
<div class="modal fade" id="extendModal" tabindex="-1" aria-labelledby="extendModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="extendModalLabel">
                    <i class="fas fa-calendar-plus me-2"></i>
                    Extend Contract for <span id="userName" class="fw-bold"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="extendForm" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <input type="hidden" id="user_id" name="user_id">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="contract_start_date" class="form-label fw-semibold">Start Date</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-calendar-day text-primary"></i>
                                </span>
                                <input type="date" class="form-control" id="contract_start_date" name="contract_start_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="contract_end_date" class="form-label fw-semibold">End Date</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-calendar-check text-primary"></i>
                                </span>
                                <input type="date" class="form-control" id="contract_end_date" name="contract_end_date" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <label for="reason" class="form-label fw-semibold">Reason for Extension</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light align-items-start">
                                    <i class="fas fa-comment-alt text-primary"></i>
                                </span>
                                <textarea class="form-control" id="reason" name="reason" rows="3" maxlength="255" required></textarea>
                            </div>
                            <div class="form-text text-end" id="reasonCounter">0/255 characters</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Import Employees -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="importModalLabel">
                    <i class="fas fa-file-excel me-2"></i>
                    Import Employees
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="importForm" action="{{ route('user.employees.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label for="file" class="form-label fw-semibold">Upload Excel File</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="fas fa-file-excel text-success"></i>
                            </span>
                            <input type="file" name="file" id="file" class="form-control" required>
                        </div>
                        <div class="form-text">File format must be Excel (.xlsx)</div>
                    </div>

                    <div class="card bg-light border-0 mb-3">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">
                                <i class="fas fa-info-circle me-1 text-primary"></i>
                                Required Column Format
                            </h6>
                            <div class="row row-cols-1 row-cols-md-2 g-3">
                                <div class="col">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item bg-transparent px-0 py-1"><b>name</b> - Nama Karyawan</li>
                                        <li class="list-group-item bg-transparent px-0 py-1"><b>email</b> - Email Karyawan</li>
                                        <li class="list-group-item bg-transparent px-0 py-1"><b>position</b> - Jabatan (Same as Master Position)</li>
                                        <li class="list-group-item bg-transparent px-0 py-1"><b>department</b> - Department (Same as Master Department)</li>
                                        <li class="list-group-item bg-transparent px-0 py-1"><b>ID_number</b> - Nomor Identitas</li>
                                        <li class="list-group-item bg-transparent px-0 py-1"><b>birth_date</b> - Tanggal Lahir (YYYY-MM-DD)</li>
                                        <li class="list-group-item bg-transparent px-0 py-1"><b>birth_place</b> - Tempat Lahir</li>
                                        <li class="list-group-item bg-transparent px-0 py-1"><b>ID_address</b> - Alamat KTP</li>
                                        <li class="list-group-item bg-transparent px-0 py-1"><b>domicile_address</b> - Alamat Domisili</li>
                                        <li class="list-group-item bg-transparent px-0 py-1"><b>religion</b> - Islam/Kristen/Katolik/Buddha/Hindu/Konghucu</li>
                                        <li class="list-group-item bg-transparent px-0 py-1"><b>gender</b> - Male / Female</li>
                                        <li class="list-group-item bg-transparent px-0 py-1"><b>phone_number</b> - Nomor HP (08XXXXXXXXXX)</li>
                                        <li class="list-group-item bg-transparent px-0 py-1"><b>employee_status</b> - Full Time / Part Time / Contract</li>
                                    </ul>
                                </div>
                                <div class="col">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item bg-transparent px-0 py-1"><b>contract_start_date</b> - (Optional for Contract/Part Time)</li>
                                        <li class="list-group-item bg-transparent px-0 py-1"><b>contract_end_date</b> - (Optional for Contract/Part Time)</li>
                                        <li class="list-group-item bg-transparent px-0 py-1"><b>join_date</b> - Tanggal Bergabung (YYYY-MM-DD)</li>
                                        <li class="list-group-item bg-transparent px-0 py-1"><b>NPWP</b> - Format XX.XXX.XXX.X-XXX.XXX</li>
                                        <li class="list-group-item bg-transparent px-0 py-1"><b>bpjs_employment</b> - BPJS Ketenagakerjaan</li>
                                        <li class="list-group-item bg-transparent px-0 py-1"><b>bpjs_health</b> - BPJS Kesehatan</li>
                                        <li class="list-group-item bg-transparent px-0 py-1"><b>exit_date</b> - Tanggal Keluar (YYYY-MM-DD, Optional)</li>
                                        <li class="list-group-item bg-transparent px-0 py-1"><b>status</b> - Status Pajak (TK/0, TK/1, TK/2, TK/3, K/1, K/2, K/3)</li>
                                        <li class="list-group-item bg-transparent px-0 py-1"><b>bank_name</b> - Nama Bank (BCA, Mandiri, BRI, etc.)</li>
                                        <li class="list-group-item bg-transparent px-0 py-1"><b>bank_number</b> - Nomor Rekening</li>
                                        <li class="list-group-item bg-transparent px-0 py-1"><b>emergency_contact</b> - Kontak Darurat</li>
                                        <li class="list-group-item bg-transparent px-0 py-1"><b>degree</b> - Pendidikan Terakhir (SMA/SMK/S1/S2)</li>
                                        <li class="list-group-item bg-transparent px-0 py-1"><b>major</b> - Jurusan</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ asset('storage/sample_employee_import.xlsx') }}" class="btn btn-info">
                            <i class="fas fa-download me-2"></i> Download Sample
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-file-import me-2"></i> Import Employees
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // Initialize DataTable with improved styling
        $('#userTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "responsive": true,
            "language": {
                "search": "<i class='fas fa-search'></i>",
                "searchPlaceholder": "Search employees...",
                "paginate": {
                    "next": "<i class='fas fa-chevron-right'></i>",
                    "previous": "<i class='fas fa-chevron-left'></i>"
                }
            },
            "dom": '<"row"<"col-md-6"l><"col-md-6"f>>rt<"row"<"col-md-6"i><"col-md-6"p>>',
            "lengthMenu": [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ],
            "autoWidth": false,
            "columnDefs": [{
                "orderable": false,
                "targets": [0, 6]
            }]
        });


        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Character counter for reason textarea
        $('#reason').on('input', function() {
            const length = $(this).val().length;
            $('#reasonCounter').text(length + '/255 characters');
        });

        // Extend contract modal handler
        $('.extend-btn').click(function() {
            const userId = $(this).data('id');
            const userName = $(this).data('name');
            const contractEndDate = $(this).data('contract_end_date');

            // Set modal content
            $('#userName').text(userName);
            $('#user_id').val(userId);

            // Set default dates
            const today = new Date().toISOString().split('T')[0];
            const defaultEndDate = new Date(contractEndDate);
            defaultEndDate.setDate(defaultEndDate.getDate() + 1);
            const nextDay = defaultEndDate.toISOString().split('T')[0];

            $('#contract_start_date').val(contractEndDate);
            $('#contract_end_date').val(nextDay);

            // Set form action
            $('#extendForm').attr('action', `/user/employees/${userId}/extend`);

            // Show modal
            $('#extendModal').modal('show');
        });

        // Form validation for extend modal
        $('#extendForm').submit(function(e) {
            const startDate = new Date($('#contract_start_date').val());
            const endDate = new Date($('#contract_end_date').val());

            if (startDate >= endDate) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Date Range',
                    text: 'End date must be after start date',
                    confirmButtonColor: '#3085d6',
                });
            }
        });

        // Import form handling
        $('#importForm').submit(function(e) {
            const fileInput = $('#file');
            const filePath = fileInput.val();
            const allowedExtensions = /(\.xlsx|\.xls)$/i;

            if (!allowedExtensions.exec(filePath)) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid File Type',
                    text: 'Please upload an Excel file (.xlsx or .xls)',
                    confirmButtonColor: '#3085d6',
                });
                fileInput.val('');
            }
        });

        // Show success/error messages from session
        @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '{{ session('success') }}',
            confirmButtonColor: '#3085d6',
            timer: 3000
        });
        @endif

        @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: '{{ session('error') }}',
            confirmButtonColor: '#3085d6'
        });
        @endif
    });
</script>
@endpush