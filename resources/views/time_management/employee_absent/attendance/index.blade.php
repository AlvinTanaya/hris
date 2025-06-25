@extends('layouts.app')
@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12 text-center mb-5">
            <h1 class="text-warning">
                <i class="fas fa-user-clock"></i> Employee Attendance
            </h1>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white mb-1 d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">Attendance Records</h3>
            <div class="d-flex">
                <select id="yearFilter" class="form-control me-2" style="width: 100px;">
                    @foreach ($years as $year)
                    <option value="{{ $year }}" {{ now()->year == $year ? 'selected' : '' }}>
                        {{ $year }}
                    </option>
                    @endforeach
                </select>

                <select id="monthFilter" class="form-control me-2" style="width: 130px;">
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ date('m') == $m ? 'selected' : '' }}>
                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                        </option>
                        @endfor
                </select>

                <button class="btn btn-success me-2" id="importBtn">
                    <i class="fas fa-file-upload me-1"></i> Import Excel
                </button>

                <button class="btn btn-light text-primary" id="addAttendanceBtn">
                    <i class="fas fa-plus me-1"></i> Add Attendance
                </button>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive " style="padding-right: 3px;">
                <table id="attendanceTable" class="table table-bordered table-striped align-middle align-items-center">
                    <thead class="table-dark">
                        <tr>
                            <th rowspan="3" class="employee-col" style="vertical-align: middle; text-align: center;">Employee (NIK)</th>

                        </tr>

                        <tr id="date-headers">

                        </tr>

                        <tr id="in-out-headers">
                            <!-- In/Out headers will be added here -->
                        </tr>
                    </thead>
                    <tbody id="attendanceBody">
                        <!-- Attendance data will be dynamically populated here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <!-- Time Details Tooltip -->
    <div id="timeTooltip" class="tooltip-custom">
        <div class="tooltip-content"></div>
    </div>
</div>

<!-- Import Attendance Modal -->
<div class="modal fade" id="importModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document"> <!-- removed modal-dialog-centered -->
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header bg-success text-white rounded-top-4">
                <h5 class="modal-title">
                    <i class="fas fa-file-import me-2"></i>Import Attendance
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <!-- Success Alert -->
                <div id="importSuccess" class="alert alert-success d-none" role="alert">
                    <i class="fas fa-check-circle me-2"></i>Import successful!
                </div>

                <form id="importForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="excelFile" class="form-label fw-bold">Select Excel File</label>
                        <input type="file" class="form-control" id="excelFile" accept=".xlsx,.xls,.csv">
                        <div class="form-text">
                            Required columns: <code>NIP</code>, <code>Date</code>, <code>Hour In</code>, <code>Hour Out</code>, <code>Place</code>
                        </div>
                    </div>

                    <div class="text-end">
                        <a href="{{ asset('storage/sample_employee_absence_import.xlsx') }}" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-download me-1"></i>Download Sample
                        </a>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Close
                </button>
                <button type="button" class="btn btn-success" id="uploadBtn">
                    <i class="fas fa-upload me-1"></i>Upload
                </button>
            </div>
        </div>
    </div>
</div>




<!-- Styled Attendance Modal -->
<div class="modal fade" id="attendanceModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document" style="border-radius: 10px;">
        <div class="modal-content" style="border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
            <div class="modal-header bg-primary text-white" style="border-top-left-radius: 12px; border-top-right-radius: 12px;">
                <h5 class="modal-title" id="attendanceModalTitle">🕒 Add Attendance Record</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body" style="padding: 1.5rem 2rem;">
                <form id="attendanceForm">
                    <input type="hidden" id="attendanceId" name="id">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="employeeSelect" class="form-label fw-semibold">👤 Employee</label>
                            <select class="form-control select2" id="employeeSelect" name="user_id" required>
                                <option value="">Select Employee</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="attendanceDate" class="form-label fw-semibold">📅 Date</label>
                            <input type="date" class="form-control" id="attendanceDate" name="date" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="hourIn" class="form-label fw-semibold">🕘 Hour In</label>
                            <input type="time" class="form-control" id="hourIn" name="hour_in">
                        </div>
                        <div class="col-md-6">
                            <label for="hourOut" class="form-label fw-semibold">🕔 Hour Out</label>
                            <input type="time" class="form-control" id="hourOut" name="hour_out">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="absentPlace" class="form-label fw-semibold">📍 Absent Place</label>
                            <input type="text" class="form-control" id="absentPlace" name="absent_place" placeholder="e.g., Office Lobby">
                        </div>
                    </div>

                    <div class="row" id="calculatedFieldsSection" style="display:none;">
                        <div class="col-12">
                            <div class="card mb-3" style="background: #f9f9f9; border: 1px solid #ddd;">
                                <div class="card-header bg-light fw-bold">📊 Calculated Information</div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Expected In:</strong> <span id="expectedIn">-</span></p>
                                            <p class="mb-0"><strong>Expected Out:</strong> <span id="expectedOut">-</span></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Status In:</strong> <span id="statusIn">-</span></p>
                                            <p class="mb-0"><strong>Status Out:</strong> <span id="statusOut">-</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer" style="padding: 1rem 2rem;">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    ❌ Close
                </button>
                <button type="button" class="btn btn-primary" id="saveAttendanceBtn" style="box-shadow: 0 2px 8px rgba(0,123,255,0.4);">
                    💾 Save
                </button>
            </div>
        </div>
    </div>
</div>



<style>
    .table-responsive {
        max-height: 75vh;
        overflow-y: auto;
        overflow-x: auto;
        white-space: nowrap;
    }

    .employee-col {
        min-width: 200px;
        position: sticky;
        left: 0;
        background-color: #ffffff;
        z-index: 10;
        text-align: left;
        font-weight: bold;
        box-shadow: 2px 0 5px -2px rgba(0, 0, 0, 0.2);
    }

    .attendance-table th,
    .attendance-table td {
        text-align: center;
        vertical-align: middle;
        padding: 8px 10px;
        min-width: 60px;
    }

    /* Improve spacing in headers */
    .table thead th {
        padding: 10px 8px;
        border: 1px solid #dee2e6;
    }

    /* Make the borders more visible */
    .table td,
    .table th {
        border: 1px solid #dee2e6;
    }

    /* Tooltip Styling */
    .tooltip-custom {
        position: absolute;
        background-color: rgba(33, 37, 41, 0.95);
        color: white;
        border-radius: 6px;
        font-size: 13px;
        z-index: 1000;
        display: none;
        pointer-events: none;
        min-width: 250px;
        padding: 12px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        border-left: 4px solid #0d6efd;
        max-width: 350px;
    }

    .tooltip-content {
        padding: 5px;
    }



    /* Status colors */
    .bg-danger {
        background-color: #dc3545 !important;
        color: white;
    }

    /* Highlight current day */
    .current-day {
        font-weight: bold;
        background-color: #f8f9fa;
    }

    /* Fix DataTable controls spacing */
    .dataTables_wrapper .dataTables_length {
        padding-bottom: 10px;
        margin-right: 20px;
    }

    .dataTables_wrapper .dataTables_filter {
        padding-bottom: 10px;
    }

    /* Make sure the sort arrows are properly spaced */
    table.dataTable thead .sorting,
    table.dataTable thead .sorting_asc,
    table.dataTable thead .sorting_desc {
        background-position: center right 8px;
    }

    /* Ensure proper spacing in fixed columns */
    .DTFC_LeftBodyLiner {
        border-right: 1px solid #dee2e6;
    }


    /* Better spacing for pagination controls */
    .dataTables_paginate {
        margin-top: 15px !important;
    }

    .dataTables_paginate .paginate_button {
        margin: 0 5px !important;
        padding: 5px 10px !important;
    }

    /* Enhanced styles for action buttons */
    .btn-outline-primary,
    .btn-outline-danger {
        border-width: 2px;
        padding: 3px 8px;
        transition: all 0.3s;
        font-weight: bold;
        background-color: rgba(255, 255, 255, 0.9);
    }

    .btn-outline-primary {
        border-color: #0d6efd;
        color: #0d6efd;
    }

    .btn-outline-danger {
        border-color: #dc3545;
        color: #dc3545;
    }

    .btn-outline-primary:hover {
        background-color: #0d6efd;
        color: white;
        transform: scale(1.05);
    }

    .btn-outline-danger:hover {
        background-color: #dc3545;
        color: white;
        transform: scale(1.05);
    }

    /* Improved styling for "Need Action" cells */
    .bg-danger.need-action {
        background-color: #ffdddd !important;
        color: #dc3545 !important;
        font-weight: bold;
        border: 1px dashed #dc3545;
    }

    /* Better action button container */
    .action-group {
        display: flex;
        justify-content: flex-start;
        align-items: center;
        margin-top: 5px;
        background-color: rgba(255, 255, 255, 0.5);
        padding: 3px;
        border-radius: 4px;
    }

    /* Fix for Need Action buttons */
    .bg-danger.need-action {
        background-color: #ffdddd !important;
        color: #dc3545 !important;
        font-weight: bold;
        border: 2px dashed #dc3545;
    }



    /* Better header styling */
    .card-header.bg-primary {
        background: linear-gradient(135deg, #0d6efd, #0a58ca) !important;
    }




    /* Fix spacing for arrows */
    .dataTables_wrapper .dataTables_paginate .paginate_button.previous {
        margin-right: 15px !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.next {
        margin-left: 15px !important;
    }

    /* Status color refinements */
    .bg-success {
        background-color: #198754 !important;
    }

    .bg-warning {
        background-color: #fd7e14 !important;
        color: white !important;
    }

    .bg-purple {
        background-color: #6f42c1 !important;
        color: white !important;
    }

    /* Improved attendance record cells */


    /* Additional hover effect for cells */
    .attendance-cell:hover {
        box-shadow: 0 0 5px rgba(0, 0, 150, 0.3);
        transform: scale(1.02);
        opacity: 0.9;
    }
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script>
    // Indonesian Holidays for 2025
    let holidayCache = {}; // Cache untuk menyimpan data holiday per tahun
    let attendanceDataTable = null;

    $(document).ready(function() {
        // Initialize select2 for employee dropdown
        if ($.fn.select2) {
            $('.select2').select2({
                dropdownParent: $('#attendanceModal')
            });
        }

        // Open modal when Add Attendance button is clicked
        $('#addAttendanceBtn').click(function() {
            $('#attendanceModalTitle').text('Add Attendance Record');
            $('#attendanceForm')[0].reset();
            $('#attendanceId').val('');
            $('#calculatedFieldsSection').hide();

            // Reset employee select and enable it
            $('#employeeSelect').prop('disabled', false).val('').trigger('change');

            // Enable date input and reset styling
            $('#attendanceDate').prop('readonly', false).val('');
            $('#attendanceDate').css({
                'background-color': '', // clear custom background
                'color': '', // reset text color
                'cursor': '' // reset cursor
            });

            // Load employees for the dropdown
            loadEmployees();

            // Set today's date as default
            const today = new Date();
            const formattedDate = today.toISOString().split('T')[0];
            $('#attendanceDate').val(formattedDate);

            $('#attendanceModal').modal('show');
        });

        // Calculate expected hours and status when employee or date changes
        $('#employeeSelect, #attendanceDate').change(function() {
            calculateExpectedHours();
        });

        // Calculate status when time fields change
        $('#hourIn, #hourOut').change(function() {
            calculateStatus();
        });

        // Save attendance record
        $('#saveAttendanceBtn').click(function() {
            saveAttendance();
        });


        function loadEmployees() {
            $.ajax({

                // GET all employees
                url: '/time_management/employee_absent/employees',
                method: 'GET',
                success: function(data) {
                    const select = $('#employeeSelect');
                    select.empty().append('<option value="">Select Employee</option>');

                    data.forEach(function(employee) {
                        select.append(`<option value="${employee.id}">${employee.name} (${employee.employee_id})</option>`);
                    });
                },
                error: function(error) {

                    console.log('asd');
                    console.error('Error loading employees:', error);
                    Swal.fire('Error', 'Failed to load employees list', 'error');
                }
            });
        }

        function calculateExpectedHours() {
            const employeeId = $('#employeeSelect').val();
            const dateStr = $('#attendanceDate').val();

            if (!employeeId || !dateStr) return;

            $.ajax({
                // GET expected hours
                url: '/time_management/employee_absent/attendance/expected-hours',

                method: 'GET',
                data: {
                    user_id: employeeId,
                    date: dateStr
                },
                success: function(data) {
                    if (data.rule_in && data.rule_out) {
                        $('#expectedIn').text(data.rule_in);
                        $('#expectedOut').text(data.rule_out);
                        $('#calculatedFieldsSection').show();
                    } else {
                        $('#expectedIn').text('No shift defined');
                        $('#expectedOut').text('No shift defined');
                        $('#calculatedFieldsSection').show();
                    }
                },
                error: function(error) {
                    console.error('Error calculating expected hours:', error);
                    $('#calculatedFieldsSection').hide();
                }
            });
        }

        // Calculate status based on times
        function calculateStatus() {
            const hourIn = $('#hourIn').val();
            const hourOut = $('#hourOut').val();
            const expectedIn = $('#expectedIn').text();
            const expectedOut = $('#expectedOut').text();

            if (expectedIn === 'No shift defined' || expectedIn === '-') return;

            // Logic for status_in
            if (hourIn && expectedIn) {
                const actualIn = new Date(`2000-01-01T${hourIn}`);
                const ruleIn = new Date(`2000-01-01T${expectedIn}`);

                if (actualIn <= ruleIn) {
                    $('#statusIn').text('early').removeClass('text-danger').addClass('text-success');
                } else {
                    $('#statusIn').text('late').removeClass('text-success').addClass('text-danger');
                }
            } else {
                $('#statusIn').text('-').removeClass('text-success text-danger');
            }

            // Logic for status_out
            if (hourOut && expectedOut) {
                const actualOut = new Date(`2000-01-01T${hourOut}`);
                const ruleOut = new Date(`2000-01-01T${expectedOut}`);

                if (actualOut >= ruleOut) {
                    $('#statusOut').text('late').removeClass('text-danger').addClass('text-success');
                } else {
                    $('#statusOut').text('early').removeClass('text-success').addClass('text-danger');
                }
            } else {
                $('#statusOut').text('-').removeClass('text-success text-danger');
            }
        }

        // Save attendance record
        function saveAttendance() {
            const formData = {
                id: $('#attendanceId').val(),
                user_id: $('#employeeSelect').val(),
                date: $('#attendanceDate').val(),
                hour_in: $('#hourIn').val() || null,
                hour_out: $('#hourOut').val() || null,
                absent_place: $('#absentPlace').val() || null
            };

            // Validate required fields
            if (!formData.user_id || !formData.date) {
                Swal.fire('Error', 'Please select an employee and date', 'error');
                return;
            }

            // Show loading
            Swal.fire({
                title: 'Saving...',
                text: 'Please wait while we save the attendance record',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Send to server
            $.ajax({

                url: '/time_management/employee_absent/attendance',

                method: formData.id ? 'PUT' : 'POST',
                data: JSON.stringify(formData),
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    Swal.fire({
                        title: 'Success',
                        text: 'Attendance record saved successfully',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        $('#attendanceModal').modal('hide');
                        loadAttendanceData(); // Reload the table
                    });
                },
                error: function(xhr) {
                    let errorMessage = 'Failed to save attendance record';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage += ': ' + xhr.responseJSON.message;
                    }

                    Swal.fire({
                        title: 'Error',
                        text: errorMessage,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }

        // Edit these parts in your script section:

        // Make sure these functions are defined in the global scope
        window.editAttendance = function(id) {
            // Show loading
            Swal.fire({
                title: 'Loading...',
                text: 'Fetching attendance record',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Fetch the attendance details
            $.ajax({
                url: `/time_management/employee_absent/attendance/${id}`,
                method: 'GET',
                success: function(data) {
                    Swal.close();

                    $('#attendanceModalTitle').text('Edit Attendance Record');
                    $('#attendanceId').val(id);

                    // Load employees for the dropdown
                    loadEmployees();

                    setTimeout(() => {
                        // Set values
                        $('#employeeSelect').val(data.user_id).trigger('change');
                        $('#attendanceDate').val(data.date);
                        $('#hourIn').val(data.hour_in);
                        $('#hourOut').val(data.hour_out);
                        $('#absentPlace').val(data.absent_place);

                        // Make employee select and date readonly
                        $('#employeeSelect').prop('disabled', true);
                        $('#attendanceDate').prop('readonly', true)
                            .css({
                                'background-color': '#e9ecef',
                                'color': '#495057',
                                'cursor': 'not-allowed'
                            });


                        // Set expected hours and status
                        $('#expectedIn').text(data.rule_in || '-');
                        $('#expectedOut').text(data.rule_out || '-');
                        $('#statusIn').text(data.status_in || '-');
                        $('#statusOut').text(data.status_out || '-');

                        $('#calculatedFieldsSection').show();
                        $('#attendanceModal').modal('show');
                    }, 300);

                },
                error: function(error) {
                    console.error('Error loading attendance record:', error);
                    Swal.fire('Error', 'Failed to load attendance record', 'error');
                }
            });
        };

        window.deleteAttendance = function(id) {
            console.log("asd");
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Delete the record
                    $.ajax({
                        url: `/time_management/employee_absent/attendance/${id}`,
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function() {
                            Swal.fire(
                                'Deleted!',
                                'Attendance record has been deleted.',
                                'success'
                            );
                            loadAttendanceData(); // Reload the table
                        },
                        error: function(xhr) {
                            let errorMessage = 'Failed to delete attendance record';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage += ': ' + xhr.responseJSON.message;
                            }

                            Swal.fire({
                                title: 'Error',
                                text: errorMessage,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });
        };



        // Configure DataTables error handling
        $.fn.dataTable.ext.errMode = 'none';
        $.fn.dataTable.ext.errMode = function(settings, helpPage, message) {
            if (message.indexOf('Cannot reinitialise DataTable') === -1) {
                console.warn('DataTables warning: ' + message);
            }
            // Otherwise, suppress the warning
        };

        loadAttendanceData();

        // Enable horizontal scrolling with mouse wheel
        $('.table-responsive').on('wheel', function(event) {
            if (event.originalEvent.deltaY !== 0) {
                event.preventDefault();
                $(this).scrollLeft($(this).scrollLeft() + event.originalEvent.deltaY);
            }
        });

        // Set up filters to reload data when changed
        $('#yearFilter, #monthFilter').change(function() {
            loadAttendanceData();
        });

        // Import button handler
        $('#importBtn').click(function() {
            $('#importModal').modal('show');
        });

        // Set up the tooltip behavior
        setupTooltip();

        // Handle file upload
        $('#uploadBtn').click(function() {
            const fileInput = document.getElementById('excelFile');
            const file = fileInput.files[0];

            if (!file) {
                Swal.fire('Error', 'Please select a file to upload', 'error');
                return;
            }

            // Show loading indicator
            Swal.fire({
                title: 'Processing',
                text: 'Uploading and processing data...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const reader = new FileReader();
            reader.onload = function(e) {
                const data = e.target.result;

                // Process the Excel data
                const processedData = processExcelData(data);

                if (processedData.length === 0) {
                    Swal.fire('Error', 'No valid attendance data found in the file', 'error');
                    return;
                }

                // Get current year and month
                const today = new Date();
                const year = today.getFullYear();
                const month = today.getMonth() + 1; // JavaScript months are 0-11

                // Send to server via AJAX
                $.ajax({
                    url: "{{ route('attendance.import') }}",
                    type: "POST",
                    data: JSON.stringify({
                        data: processedData,
                        year: year,
                        month: month
                    }),
                    contentType: "application/json",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Swal.fire({
                            title: 'Success',
                            text: response.message,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            // Reload the page when the user clicks "OK"
                            if (result.isConfirmed) {
                                window.location.reload();
                            }
                        });

                        if (response.errors && response.errors.length > 0) {
                            // Create error log
                            let errorLog = '<h4>Import Errors:</h4><ul>';
                            response.errors.forEach(error => {
                                errorLog += `<li>${error}</li>`;
                            });
                            errorLog += '</ul>';

                            // Display errors in a modal or section
                            $('#errorContainer').html(errorLog).show();
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Failed to import data';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage += ': ' + xhr.responseJSON.message;
                        }

                        Swal.fire({
                            title: 'Error',
                            text: errorMessage,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            };
            reader.readAsBinaryString(file);
        });
    });

    // Function to process Excel data with the complex header structure
    function processExcelData(binaryData) {
        // Parse Excel data
        const workbook = XLSX.read(binaryData, {
            type: 'binary'
        });
        const worksheet = workbook.Sheets[workbook.SheetNames[0]];

        // Get the raw data without automatic header parsing
        const rawData = XLSX.utils.sheet_to_json(worksheet, {
            header: 1
        });

        // Process the data
        const processedData = [];

        if (rawData.length < 3) {
            console.error("Excel file doesn't have enough rows");
            return [];
        }

        // Extract date columns from the date row
        const dateRow = rawData[0];
        const inOutRow = rawData[1]; // Row with "In" and "Out" labels

        const dateColumns = [];

        // Start from column 7 (after basic employee info columns)
        for (let i = 7; i < dateRow.length; i++) {
            const header = dateRow[i];
            // Check if it's a date in format DD/MM/YYYY
            if (header && typeof header === 'string' && /^\d{2}\/\d{2}\/\d{4}$/.test(header)) {
                // Find the corresponding In/Out columns
                let inCol = -1;
                let outCol = -1;

                // Look for "In" and "Out" in the column and next column
                for (let j = i; j < i + 2 && j < inOutRow.length; j++) {
                    const subHeader = inOutRow[j];
                    if (subHeader === "In") {
                        inCol = j;
                    } else if (subHeader === "Out") {
                        outCol = j;
                    }
                }

                // If we didn't find explicit In/Out markers, use position-based approach
                if (inCol === -1 || outCol === -1) {
                    // Common pattern is that date spans 2 columns, with In followed by Out
                    dateColumns.push({
                        date: header,
                        inIndex: i,
                        outIndex: i + 1
                    });
                    i++; // Skip the next column which is presumed to be "Out"
                } else {
                    dateColumns.push({
                        date: header,
                        inIndex: inCol,
                        outIndex: outCol
                    });
                    // Skip to the column after Out
                    i = Math.max(inCol, outCol);
                }
            }
        }

        // Process each employee row
        for (let rowIndex = 2; rowIndex < rawData.length; rowIndex++) {
            const row = rawData[rowIndex];
            if (!row || row.length === 0) continue;

            // Get employee details
            const pin = row[0] || null;
            const nip = row[1] || null;
            const name = row[2] || null;
            const position = row[3] || null;
            const department = row[4] || null;
            const workplace = row[5] || null;

            if (!nip) continue; // Skip rows without NIP

            // Process each date for this employee
            for (const dateCol of dateColumns) {
                const inTime = row[dateCol.inIndex];
                const outTime = row[dateCol.outIndex];

                // Skip if both are empty or "00:00"
                if ((!inTime || inTime === "00:00") && (!outTime || outTime === "00:00")) {
                    continue;
                }

                // Create attendance record
                processedData.push({
                    'NIP': nip,
                    'Date': dateCol.date,
                    'Hour In': inTime !== "00:00" ? inTime : null,
                    'Hour Out': outTime !== "00:00" ? outTime : null,
                    'Place': workplace || null
                });
            }
        }

        return processedData;
    }

    // Optimized function to load attendance data (removed the duplicate)
    function loadAttendanceData() {
        var year = $('#yearFilter').val();
        var month = $('#monthFilter').val();

        // Show loading indicator
        Swal.fire({
            title: 'Loading...',
            text: 'Please wait while we fetch the data',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Properly destroy DataTable if it exists
        if ($.fn.DataTable.isDataTable('#attendanceTable')) {
            $('#attendanceTable').DataTable().clear().destroy();
            attendanceDataTable = null;
        }

        // Clear table contents
        $('#date-headers').empty();
        $('#in-out-headers').empty();
        $('#attendanceBody').empty();

        // Fetch data and render
        Promise.all([
                fetchHolidays(year),
                $.ajax({
                    url: '{{ route("attendance.data") }}',
                    method: 'GET',
                    data: {
                        year: year,
                        month: month
                    }
                })
            ])
            .then(function([holidays, attendanceData]) {
                // Render the table with data
                renderAttendanceTable(attendanceData, year, month, holidays);

                // Initialize DataTable with a small delay to ensure DOM is ready
                setTimeout(() => {
                    attendanceDataTable = $('#attendanceTable').DataTable({
                        ordering: true,
                        paging: true,
                        searching: true,
                        info: true,
                        columnDefs: [{
                            orderable: true,
                            targets: 0
                        }, {
                            orderable: false,
                            targets: '_all'
                        }],
                        scrollX: true,
                        scrollY: '75vh',
                        scrollCollapse: true,
                        fixedColumns: {
                            left: 1
                        },
                        pageLength: 10,
                        lengthMenu: [10, 25, 50, 100]
                    });

                    Swal.close();
                }, 200);
            })
            .catch(function(error) {
                console.error('Error loading attendance data:', error);
                Swal.fire('Error', 'Failed to load attendance data', 'error');
            });
    }

    // Helper function to check if a date is Sunday
    function isSunday(date) {
        return date.getDay() === 0; // 0 is Sunday in JavaScript
    }

    // Optimized function to render attendance table dengan tampilan bersih seperti Image 2
    function renderAttendanceTable(data, year, month, holidays) {
        // Clear existing table data
        $('#date-headers').empty();
        $('#in-out-headers').empty();
        $('#attendanceBody').empty();

        // Create date headers
        const daysInMonth = new Date(year, month, 0).getDate();
        const today = new Date();

        // Add date headers to the table
        for (let day = 1; day <= daysInMonth; day++) {
            const dateStr = `${year}-${month.toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
            const fullDate = new Date(year, month - 1, day);
            const isSundayCheck = isSunday(fullDate);
            const holidayName = getHolidayName(dateStr, holidays);
            const isHolidayCheck = !!holidayName;
            const isToday = fullDate.toDateString() === today.toDateString();

            // Add the date to the headers
            $('#date-headers').append(
                `<th colspan="2" class="${(isSundayCheck || isHolidayCheck) ? 'bg-light text-danger' : ''} ${isToday ? 'current-day' : ''}">${day}</th>`
            );

            $('#in-out-headers').append('<th>In</th><th>Out</th>');
        }

        // Add employee rows
        data.forEach(employee => {
            const row = $('<tr>');
            row.append(`<td class="employee-col">${employee.name} (${employee.employee_id})</td>`);

            for (let day = 1; day <= daysInMonth; day++) {
                const dateStr = `${year}-${month.toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
                const fullDate = new Date(year, month - 1, day);
                const isSundayCheck = isSunday(fullDate);
                const holidayInfo = getHolidayInfo(dateStr, holidays);
                const isHolidayCheck = !!holidayInfo;
                const dayData = employee.attendance[day];

                const inCell = $('<td class="attendance-cell">');
                const outCell = $('<td class="attendance-cell">');

                // Handle holidays and Sundays
                if (isHolidayCheck || isSundayCheck) {
                    inCell.addClass('bg-light text-danger');
                    outCell.addClass('bg-light text-danger');

                    // Display "Holiday" or "Sunday" and store the holiday info for tooltip
                    if (isHolidayCheck) {
                        inCell.html('<small>Holiday</small>');
                        outCell.html('<small>Holiday</small>');

                        // Store holiday name data for tooltip
                        inCell.data('holiday-info', holidayInfo);
                        outCell.data('holiday-info', holidayInfo);
                    } else {
                        inCell.html('<small>Sunday</small>');
                        outCell.html('<small>Sunday</small>');
                    }
                }
                // Handle regular days with data
                else if (dayData) {
                    // Check if this is a full-day leave type
                    const isFullDayLeave = [
                        'sick_leave',
                        'annual_leave',
                        'permission_leave',
                        'full_day_leave'
                    ].includes(dayData.leave_type);

                    // 1. Handle time-off cases with attendance (override)
                    if (dayData.time_off && (dayData.hour_in || dayData.hour_out)) {
                        if (isFullDayLeave) {
                            // Full day leave with attendance -> show Override
                            inCell.addClass('bg-warning text-dark')
                                .html(dayData.hour_in ? `${dayData.hour_in} (Override)` : 'Override');
                            outCell.addClass('bg-warning text-dark')
                                .html(dayData.hour_out ? `${dayData.hour_out} (Override)` : 'Override');

                            // Tooltip data for override cases
                            const tooltipData = {
                                ...dayData,
                                override_note: 'Employee worked despite approved leave'
                            };
                            inCell.data('details', tooltipData);
                            outCell.data('details', tooltipData);
                        } else {
                            // Partial leave (masuk siang/pulang awal) -> show normally with indicator
                            if (dayData.hour_in) {
                                inCell.addClass(dayData.has_late_arrival_request ? 'bg-primary text-white' : 'bg-success text-white')
                                    .html(`${dayData.hour_in}${dayData.has_late_arrival_request ? '*' : ''}`);
                            }
                            if (dayData.hour_out) {
                                outCell.addClass(dayData.has_early_departure_request ? 'bg-primary text-white' : 'bg-success text-white')
                                    .html(`${dayData.hour_out}${dayData.has_early_departure_request ? '*' : ''}`);
                            }

                            // Tooltip data for partial leaves
                            const tooltipData = {
                                ...dayData,
                                is_override: false
                            };
                            inCell.data('details', tooltipData);
                            outCell.data('details', tooltipData);
                        }
                    }
                    // 2. Handle pure time-off cases (no attendance)
                    else if (dayData.time_off && !dayData.hour_in && !dayData.hour_out) {
                        let displayText = 'Off';
                        let bgClass = 'bg-secondary text-white';

                        // Custom styling for different leave types
                        switch (dayData.leave_type) {
                            case 'sick_leave':
                                displayText = 'Sick';
                                bgClass = 'bg-danger text-white';
                                break;
                            case 'annual_leave':
                                displayText = 'Annual';
                                bgClass = 'bg-info text-white';
                                break;
                            case 'permission_leave':
                                displayText = 'Ijin';
                                bgClass = 'bg-purple text-white'; // Different color for permission
                                break;
                            case 'full_day_leave':
                                displayText = 'Leave';
                                bgClass = 'bg-secondary text-white';
                                break;
                        }

                        inCell.addClass(bgClass).html(`<small>${displayText}</small>`);
                        outCell.addClass(bgClass).html(`<small>${displayText}</small>`);

                        // Tooltip data for pure time-off
                        const tooltipData = {
                            time_off_type: dayData.leave_type,
                            time_off_name: dayData.time_off_name,
                            time_off_reason: dayData.time_off_reason || '',
                            rule_type: dayData.rule_type || 'N/A'
                        };
                        inCell.data('details', tooltipData);
                        outCell.data('details', tooltipData);
                    } else {
                        // IN cell handling
                        if (dayData.hour_in) {
                            let inClass = 'bg-success text-white'; // Default on-time
                            let lateIndicator = '';

                            // Calculate if actually late (positive minutes)
                            const isLate = dayData.late_minutes > 0;
                            const isEarly = dayData.late_minutes < 0; // Negative means early

                            if (isLate) {
                                if (dayData.has_late_arrival_request) {
                                    inClass = 'bg-primary text-white'; // Approved late (blue)
                                    lateIndicator = '<i class="fas fa-check-circle ms-1"></i>';
                                } else {
                                    inClass = 'bg-danger text-white'; // Unapproved late (red)
                                    lateIndicator = '<i class="fas fa-exclamation-circle ms-1"></i>';
                                }
                            } else if (isEarly) {
                                inClass = 'bg-info text-white'; // Early arrival (different color)
                                lateIndicator = '<i class="fas fa-arrow-down ms-1"></i>';
                            }

                            // Buat tampilan waktu dengan format yang lebih rapi
                            const timeDisplay = $('<div class="time-display d-flex justify-content-center align-items-center"></div>');
                            timeDisplay.html(`${dayData.hour_in.split(':').slice(0, 2).join(':')}${lateIndicator}`);

                            inCell.addClass(inClass).html('');
                            inCell.append(timeDisplay);

                            const inTooltipData = {
                                ...dayData,
                                isInCell: true,
                                tooltipExtra: isLate ?
                                    `<p class="${dayData.has_late_arrival_request ? 'text-primary' : 'text-danger'}">
                                <i class="fas ${dayData.has_late_arrival_request ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
                                <strong>${dayData.has_late_arrival_request ? 'Approved' : 'Unapproved'} Late:</strong> 
                                ${dayData.late_minutes} minutes
                            </p>` : (isEarly ?
                                        `<p class="text-info">
                                    <i class="fas fa-arrow-down"></i>
                                    <strong>Early Arrival:</strong> 
                                    ${Math.abs(dayData.late_minutes)} minutes
                                </p>` : '')
                            };
                            inCell.data('details', inTooltipData);
                        }

                        // OUT cell handling
                        if (dayData.hour_out) {
                            let outClass = 'bg-success text-white'; // Default on-time
                            let earlyIndicator = '';

                            const isEarly = dayData.early_minutes > 0;
                            const isLate = dayData.early_minutes < 0; // Negative means stayed late

                            if (isEarly) {
                                if (dayData.has_early_departure_request) {
                                    outClass = 'bg-primary text-white'; // Approved early (blue)
                                    earlyIndicator = '<i class="fas fa-check-circle ms-1"></i>';
                                } else {
                                    outClass = dayData.early_minutes > 10 ? 'bg-danger text-white' : 'bg-warning'; // Unapproved early
                                    earlyIndicator = '<i class="fas fa-exclamation-circle ms-1"></i>';
                                }
                            } else if (isLate) {
                                outClass = 'bg-info text-white'; // Stayed late
                                earlyIndicator = '<i class="fas fa-arrow-up ms-1"></i>';
                            }

                            // Buat tampilan waktu dengan format yang lebih rapi
                            const timeDisplay = $('<div class="time-display d-flex justify-content-center align-items-center"></div>');
                            timeDisplay.html(`${dayData.hour_out.split(':').slice(0, 2).join(':')}${earlyIndicator}`);

                            outCell.addClass(outClass).html('');
                            outCell.append(timeDisplay);

                            const outTooltipData = {
                                ...dayData,
                                isOutCell: true,
                                tooltipExtra: isEarly ?
                                    `<p class="${dayData.has_early_departure_request ? 'text-primary' : 'text-danger'}">
                                <i class="fas ${dayData.has_early_departure_request ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
                                <strong>${dayData.has_early_departure_request ? 'Approved' : 'Unapproved'} Early:</strong> 
                                ${dayData.early_minutes} minutes
                            </p>` : (isLate ?
                                        `<p class="text-info">
                                    <i class="fas fa-arrow-up"></i>
                                    <strong>Stayed Late:</strong> 
                                    ${Math.abs(dayData.early_minutes)} minutes
                                </p>` : '')
                            };
                            outCell.data('details', outTooltipData);
                        }

                        // Buat container untuk tombol-tombol dengan tampilan yang lebih rapi
                        const buttonContainer = $('<div class="d-flex justify-content-center mt-2 button-container"></div>');

                        // Tambahkan styling tombol yang lebih bagus seperti di Image 2
                        const editBtn = $('<button class="btn btn-sm me-1" style="background: white; border: 1px solid #ccc;" title="Edit Record"><i class="fas fa-edit text-primary"></i></button>');
                        editBtn.on('click', function(e) {
                            e.stopPropagation();
                            editAttendance(dayData.id); // Panggil fungsi langsung seperti kode asli
                        });

                        const deleteBtn = $('<button class="btn btn-sm" style="background: white; border: 1px solid #ccc;" title="Delete Record"><i class="fas fa-trash text-danger"></i></button>');
                        deleteBtn.on('click', function(e) {
                            e.stopPropagation();
                            deleteAttendance(dayData.id); // Panggil fungsi langsung seperti kode asli
                        });

                        buttonContainer.append(editBtn).append(deleteBtn);

                        // Tambahkan tombol ke cell yang sesuai
                        if (dayData.hour_in) {
                            inCell.append(buttonContainer);
                        } else if (dayData.hour_out) {
                            outCell.append(buttonContainer);
                        }

                        // Store the record ID in the cell's data
                        inCell.data('record-id', dayData.id);
                        outCell.data('record-id', dayData.id);
                    }
                } else {
                    // Tampilan "Need Action!" yang lebih bersih
                    const needActionContent = `
                <div class="d-flex flex-column justify-content-center align-items-center h-100">
                    <div><i class="fas fa-exclamation-triangle text-danger"></i></div>
                    <div class="mt-1"><small>Need Action!</small></div>
                </div>`;

                    inCell.addClass('attendance-cell bg-danger text-white need-action')
                        .html(needActionContent);
                    outCell.addClass('attendance-cell bg-danger text-white need-action')
                        .html(needActionContent);
                }

                row.append(inCell);
                row.append(outCell);
            }

            $('#attendanceBody').append(row);
        });

        // Tambahkan CSS untuk memperbaiki tampilan tombol dan cell
        $('<style>')
            .prop('type', 'text/css')
            .html(`
        .attendance-cell {
            position: relative;
            min-height: 70px;
            padding: 8px 4px;
            text-align: center;
        }
        .time-display {
            font-size: 16px;
            font-weight: bold;
            padding: 2px;
            height: 26px;
        }
        .button-container {
            margin-top: 5px;
        }
        .button-container button {
            width: 32px;
            height: 32px;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .need-action {
      
            align-items: center;
            justify-content: center;
        }
    `)
            .appendTo('head');
    }


    // setupTooltip function
    function setupTooltip() {
        $(document).on('mouseenter', '.attendance-cell', function(e) {
            const data = $(this).data('details');
            const holidayInfo = $(this).data('holiday-info');

            // Don't show tooltip when hovering over buttons
            if ($(e.target).closest('button').length) {
                return;
            }

            // Handle holiday tooltips
            if (holidayInfo) {
                let tooltipContent = `
            <div class="p-2">
                <h5 class="text-danger mb-2"><i class="fas fa-calendar-times"></i> Holiday</h5>
                <p class="mb-1"><strong>Holiday Name:</strong> ${holidayInfo.name}</p>
            `;

                // Add additional info if it's a custom holiday
                if (holidayInfo.is_custom) {
                    tooltipContent += `<p class="mb-1"><small class="text-muted">Custom company holiday</small></p>`;
                } else {
                    tooltipContent += `<p class="mb-1"><small class="text-muted">National holiday</small></p>`;
                }

                tooltipContent += `</div>`;

                // Set tooltip content
                $('#timeTooltip .tooltip-content').html(tooltipContent);

                // Improved positioning logic
                positionTooltip(this);
                return;
            }

            // If no holiday info and no details data, exit
            if (!data) return;

            // Regular attendance tooltip structure
            let tooltipContent = `
        <div class="p-2">
            <p class="mb-1"><strong>Shift:</strong> ${data.rule_type || 'N/A'}</p>
        `;

            // 1. Time-off information section
            if (data.time_off_name) {
                // Determine icon and color based on leave type
                let leaveIcon = '';
                let leaveColor = 'text-primary';

                switch (data.time_off_type) {
                    case 'sick_leave':
                        leaveIcon = '<i class="fas fa-procedures"></i>';
                        leaveColor = 'text-danger';
                        break;
                    case 'annual_leave':
                        leaveIcon = '<i class="fas fa-umbrella-beach"></i>';
                        leaveColor = 'text-info';
                        break;
                    case 'permission_leave':
                        leaveIcon = '<i class="fas fa-file-signature"></i>';
                        leaveColor = 'text-purple';
                        break;
                    case 'approved_late_arrival':
                        leaveIcon = '<i class="fas fa-clock"></i>';
                        leaveColor = 'text-primary';
                        break;
                    case 'approved_early_departure':
                        leaveIcon = '<i class="fas fa-running"></i>';
                        leaveColor = 'text-primary';
                        break;
                    default:
                        leaveIcon = '<i class="fas fa-calendar-day"></i>';
                }

                tooltipContent += `
            <div class="mb-2">
                <p class="mb-1"><strong>Leave Type:</strong> ${leaveIcon} <span class="${leaveColor}">${data.time_off_name}</span></p>
                <p class="mb-1"><strong>Reason:</strong> ${data.time_off_reason || 'Not specified'}</p>
            `;

                // Special indicators for approved partial leaves
                if (data.has_late_arrival_request) {
                    tooltipContent += `<p class="mb-1 text-primary"><i class="fas fa-check-circle"></i> Approved Late Arrival</p>`;
                }
                if (data.has_early_departure_request) {
                    tooltipContent += `<p class="mb-1 text-primary"><i class="fas fa-check-circle"></i> Approved Early Departure</p>`;
                }

                tooltipContent += `</div>`;
            }

            // 2. Override warning for full-day leaves with attendance
            if (data.override_note) {
                tooltipContent += `
            <div class="alert alert-warning py-1 mb-2">
                <i class="fas fa-exclamation-triangle"></i> <strong>Note:</strong> ${data.override_note}
            </div>
            `;
            }

            // 3. Time comparison section
            tooltipContent += `
        <div class="mt-2 border-top pt-2">
            <p class="mb-1"><strong>Scheduled:</strong> ${data.rule_in || 'N/A'} - ${data.rule_out || 'N/A'}</p>
            <p class="mb-1"><strong>Actual:</strong> ${data.hour_in || 'N/A'} - ${data.hour_out || 'N/A'}</p>
        `;

            // 4. Late/early information
            if (data.late_minutes && !data.has_late_arrival_request) {
                tooltipContent += `
            <p class="mb-1 text-danger">
                <i class="fas fa-exclamation-circle"></i> <strong>Late:</strong> ${data.late_minutes} minutes
            </p>
            `;
            }
            if (data.early_minutes && !data.has_early_departure_request) {
                tooltipContent += `
            <p class="mb-1 text-danger">
                <i class="fas fa-exclamation-circle"></i> <strong>Early:</strong> ${data.early_minutes} minutes
            </p>
            `;
            }

            // 5. Location information
            tooltipContent += `
            <p class="mb-1"><strong>Location:</strong> ${data.absent_place || 'N/A'}</p>
        </div>
        `;

            // Set tooltip content
            $('#timeTooltip .tooltip-content').html(tooltipContent);

            // Position tooltip
            positionTooltip(this);
        });

        $(document).on('mouseleave', '.attendance-cell', function() {
            $('#timeTooltip').hide();
        });

        // Add an event handler to hide tooltip when hovering over buttons
        $(document).on('mouseenter', '.attendance-cell button', function() {
            $('#timeTooltip').hide();
        });
    }

    // Helper function to position tooltip better
    function positionTooltip(element) {
        const cellPosition = $(element).offset();
        const tooltipWidth = $('#timeTooltip').outerWidth();
        const windowWidth = $(window).width();
        const cellWidth = $(element).outerWidth();
        const cellHeight = $(element).outerHeight();

        // Position tooltip near the cursor but slightly offset to the left
        let leftPos = cellPosition.left - (tooltipWidth / 2);

        // Ensure tooltip stays within screen bounds
        if (leftPos < 10) {
            leftPos = 10;
        } else if (leftPos + tooltipWidth > windowWidth - 20) {
            leftPos = windowWidth - tooltipWidth - 20;
        }

        // Position vertically below the cell
        let topPos = cellPosition.top + cellHeight + 5;

        // If tooltip would go below viewport, show it above the cell instead
        if (topPos + $('#timeTooltip').outerHeight() > $(window).height() - 10) {
            topPos = cellPosition.top - $('#timeTooltip').outerHeight() - 10;
        }

        $('#timeTooltip').css({
            top: topPos,
            left: leftPos,
            display: 'block',
            opacity: 0
        }).animate({
            opacity: 1
        }, 200);
    }


    // Fetch holiday data from API and custom sources
    function fetchHolidays(year) {
        // Check cache first
        if (holidayCache[year]) {
            return Promise.resolve(holidayCache[year]);
        }

        // Get both national holidays and custom holidays
        return Promise.all([
            // National holidays from API
            $.ajax({
                url: `https://api-harilibur.vercel.app/api?year=${year}`,
                method: 'GET',
                dataType: 'json'
            }),
            // Custom holidays from your server
            $.ajax({
                url: '/api/custom-holidays', // You need to create this endpoint
                method: 'GET',
                data: {
                    year: year
                }
            })
        ]).then(function([nationalHolidays, customHolidays]) {
            // Process national holidays
            const processedNational = nationalHolidays
                .filter(item => item.is_national_holiday)
                .map(item => {
                    const holidayDate = new Date(item.holiday_date);
                    return {
                        date: `${holidayDate.getFullYear()}-${(holidayDate.getMonth() + 1).toString().padStart(2, '0')}-${holidayDate.getDate().toString().padStart(2, '0')}`,
                        name: item.holiday_name,
                        is_custom: false
                    };
                });

            // Process custom holidays
            const processedCustom = customHolidays.map(item => {
                const holidayDate = new Date(item.date);
                return {
                    date: `${holidayDate.getFullYear()}-${(holidayDate.getMonth() + 1).toString().padStart(2, '0')}-${holidayDate.getDate().toString().padStart(2, '0')}`,
                    name: item.name,
                    is_custom: true
                };
            });

            // Combine both types of holidays
            const allHolidays = [...processedNational, ...processedCustom];

            // Store in cache
            holidayCache[year] = allHolidays;
            return allHolidays;

        }).catch(function(error) {
            console.error('Error fetching holidays:', error);
            return []; // Return empty array on error
        });
    }


    // Function to get full holiday information including name and custom status
    function getHolidayInfo(dateStr, holidays) {
        if (!holidays || !Array.isArray(holidays)) return null;

        const holiday = holidays.find(h => h.date === dateStr);
        return holiday || null;
    }

    // Helper function to just get the holiday name (used in other parts of the code)
    function getHolidayName(dateStr, holidays) {
        const holiday = getHolidayInfo(dateStr, holidays);
        return holiday ? holiday.name : null;
    }
</script>


@endpush