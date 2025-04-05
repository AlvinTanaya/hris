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
                <select id="yearFilter" class="form-control mr-2 me-2" style="width: 100px;">
                    @foreach ($years as $year)
                    <option value="{{ $year }}" {{ now()->year == $year ? 'selected' : '' }}>
                        {{ $year }}
                    </option>
                    @endforeach
                </select>

                <select id="monthFilter" class="form-control mr-2 me-2" style="width: 130px;">
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ date('m') == $m ? 'selected' : '' }}>
                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                        </option>
                        @endfor
                </select>

                <button class="btn btn-success" id="importBtn">
                    <i class="fas fa-file-upload"></i> Import Excel
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

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Attendance</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="importForm" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Select Excel File</label>
                        <small class="form-text text-muted">
                            Required columns: NIP, Date, Hour In, Hour Out, Place
                        </small>
                        <input type="file" class="form-control-file mt-2" id="excelFile"
                            accept=".xlsx,.xls,.csv">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                <button type="button" class="btn btn-primary" id="uploadBtn">Upload</button>
            </div>
        </div>
    </div>
</div>

@endsection

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
        background-color: rgba(0, 0, 0, 0.9);
        color: white;
        border-radius: 4px;
        font-size: 12px;
        z-index: 1000;
        display: none;
        pointer-events: none;
        min-width: 220px;
        padding: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .tooltip-content {
        padding: 8px;
    }

    .attendance-cell {
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .attendance-cell:hover {
        opacity: 0.9;
    }

    /* Status colors */
    .bg-success {
        background-color: #28a745 !important;
        color: white;
    }

    .bg-warning {
        background-color: #ffc107 !important;
        color: black;
    }

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
</style>

@push('scripts')
<script>
    // Indonesian Holidays for 2025
    let holidayCache = {}; // Cache untuk menyimpan data holiday per tahun
    let attendanceDataTable = null;

    $(document).ready(function() {
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

    // Optimized function to render attendance table
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
                                    lateIndicator = '<i class="fas fa-check-circle ml-1"></i>';
                                } else {
                                    inClass = 'bg-danger text-white'; // Unapproved late (red)
                                    lateIndicator = '<i class="fas fa-exclamation-circle ml-1"></i>';
                                }
                            } else if (isEarly) {
                                inClass = 'bg-info text-white'; // Early arrival (different color)
                                lateIndicator = '<i class="fas fa-arrow-down ml-1"></i>';
                            }

                            inCell.addClass(inClass).html(`${dayData.hour_in.split(':').slice(0, 2).join(':')}${lateIndicator}`);

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
                                    earlyIndicator = '<i class="fas fa-check-circle ml-1"></i>';
                                } else {
                                    outClass = dayData.early_minutes > 10 ? 'bg-danger text-white' : 'bg-warning'; // Unapproved early
                                    earlyIndicator = '<i class="fas fa-exclamation-circle ml-1"></i>';
                                }
                            } else if (isLate) {
                                outClass = 'bg-info text-white'; // Stayed late
                                earlyIndicator = '<i class="fas fa-arrow-up ml-1"></i>';
                            }

                            outCell.addClass(outClass).html(`${dayData.hour_out.split(':').slice(0, 2).join(':')}${earlyIndicator}`);

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
                    }
                }

                row.append(inCell);
                row.append(outCell);
            }

            $('#attendanceBody').append(row);
        });
    }

    // Enhanced tooltip setup with additional holiday information handling
    function setupTooltip() {
        $(document).on('mouseenter', '.attendance-cell', function(e) {
            const data = $(this).data('details');
            const holidayInfo = $(this).data('holiday-info');

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

                // Set tooltip content and position
                $('#timeTooltip .tooltip-content').html(tooltipContent);
                // In your setupTooltip() function, replace the tooltip positioning code with this:
                const cellPosition = $(this).offset();
                const tooltipWidth = $('#timeTooltip').outerWidth();
                const windowWidth = $(window).width();
                const cellWidth = $(this).outerWidth();

                // Calculate the left position - try to keep it within view
                let leftPos = cellPosition.left - tooltipWidth;

                // If positioning to the left would push it off-screen, position it to the right
                if (leftPos < 10) {
                    leftPos = cellPosition.left + cellWidth;
                }

                $('#timeTooltip').css({
                    top: cellPosition.top + 5, // Position it at the top of the cell instead of below
                    left: leftPos,
                    display: 'block'
                });

                return; // Exit early for holiday tooltips
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

            // In your setupTooltip() function, replace the tooltip positioning code with this:
            const cellPosition = $(this).offset();
            const tooltipWidth = $('#timeTooltip').outerWidth();
            const windowWidth = $(window).width();
            const cellWidth = $(this).outerWidth();

            // Calculate the left position - try to keep it within view
            let leftPos = cellPosition.left - tooltipWidth;

            // If positioning to the left would push it off-screen, position it to the right
            if (leftPos < 10) {
                leftPos = cellPosition.left + cellWidth;
            }

            $('#timeTooltip').css({
                top: cellPosition.top + 5, // Position it at the top of the cell instead of below
                left: leftPos,
                display: 'block'
            });
        });

        $(document).on('mouseleave', '.attendance-cell', function() {
            $('#timeTooltip').hide();
        });
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