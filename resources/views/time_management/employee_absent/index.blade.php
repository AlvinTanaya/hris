@extends('layouts.app')

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
            <div class="table-responsive" style="padding-right: 3px;">
                <table id="attendanceTable" class="table table-bordered table-striped align-middle align-items-center">
                    <thead class="table-dark">
                        <tr>
                            <th rowspan="3" class="employee-col" style="vertical-align: middle; text-align: center;">Employee</th>

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



@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedcolumns/4.0.2/css/fixedColumns.dataTables.min.css">
<script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/fixedcolumns/4.0.2/js/dataTables.fixedColumns.min.js"></script>
<!-- Bootstrap 5 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<script>
    // Indonesian Holidays for 2025
    let holidayCache = {}; // Cache untuk menyimpan data holiday per tahun
    let attendanceDataTable = null;





    $(document).ready(function() {
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

        $('#yearFilter, #monthFilter').change(function() {
            loadAttendanceData();
        });

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



    // Function to process Excel data
    // Function to process Excel data with the complex header structure shown in the screenshot
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
        if (attendanceDataTable !== null) {
            attendanceDataTable.destroy();
            attendanceDataTable = null;
            // Also remove DataTable added classes to completely reset the table
            $('#attendanceTable').removeClass('dataTable').removeClass('no-footer').removeClass('DTFC_Cloned');
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
                // Render the table with data without initializing DataTable
                renderAttendanceTable(attendanceData, year, month, holidays, false);

                // Initialize DataTable ONLY ONCE here
                setTimeout(() => {
                    // Make sure the table is fully rendered
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


    function setupTooltip() {
        $(document).on('mouseenter', '.attendance-cell', function(e) {
            var data = $(this).data('details');
            if (data) {
                let tooltipContent = `
                <div class="p-2">
                    <p class="mb-1"><strong>Shift:</strong> ${data.rule_type || 'N/A'}</p>
            `;

                if (data.isInCell) {
                    tooltipContent += `
                    <p class="mb-1"><strong>Expected In:</strong> ${data.rule_in || 'N/A'}</p>
                    <p class="mb-1"><strong>Actual In:</strong> ${data.hour_in || 'N/A'}</p>
                    <p class="mb-1"><strong>Place:</strong> ${data.absent_place || 'N/A'}</p>
                    ${data.late_minutes ? `<p class="mb-1"><strong>Late By:</strong> ${data.late_minutes} minutes</p>` : ''}
                `;
                } else if (data.isOutCell) {
                    tooltipContent += `
                    <p class="mb-1"><strong>Expected Out:</strong> ${data.rule_out || 'N/A'}</p>
                    <p class="mb-1"><strong>Actual Out:</strong> ${data.hour_out || 'N/A'}</p>
                    <p class="mb-1"><strong>Place:</strong> ${data.absent_place || 'N/A'}</p>
                    ${data.early_minutes ? `<p class="mb-1"><strong>Early By:</strong> ${data.early_minutes} minutes</p>` : ''}
                `;
                }

                tooltipContent += `</div>`;

                $('#timeTooltip .tooltip-content').html(tooltipContent);

                // Position code remains the same
                var cellPosition = $(this).offset();
                var cellWidth = $(this).outerWidth();
                var cellHeight = $(this).outerHeight();
                var tooltipWidth = $('#timeTooltip').outerWidth();

                $('#timeTooltip').css({
                    top: cellPosition.top + cellHeight,
                    left: cellPosition.left - (tooltipWidth / 2) + (cellWidth / 2),
                    display: 'block'
                });
            }
        });

        // Mouse leave event stays the same
        $(document).on('mouseleave', '.attendance-cell', function() {
            $('#timeTooltip').css('display', 'none');
        });
    }

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

        // Cek jika DataTable sudah ada, lalu destroy dan clear datanya
        if ($.fn.DataTable.isDataTable('#attendanceTable')) {
            $('#attendanceTable').DataTable().clear().destroy();
        }

        // Bersihkan isi tabel sebelum memuat data baru
        $('#date-headers').empty();
        $('#in-out-headers').empty();
        $('#attendanceBody').empty();

        // Ambil data libur dan absensi secara bersamaan
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
                // Render ulang tabel dengan data baru
                renderAttendanceTable(attendanceData, year, month, holidays);

                // Tunggu sebentar sebelum menginisialisasi DataTable agar DOM siap
                setTimeout(() => {
                    $('#attendanceTable').DataTable({
                        ordering: true,
                        paging: true,
                        searching: true,
                        info: true,
                        columnDefs: [{
                                orderable: true,
                                targets: 0
                            },
                            {
                                orderable: false,
                                targets: '_all'
                            }
                        ],
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
                }, 200); // Delay kecil agar DOM siap sebelum inisialisasi DataTable
            })
            .catch(function(error) {
                console.error('Error loading attendance data:', error);
                Swal.fire('Error', 'Failed to load attendance data', 'error');
            });
    }


    function isHoliday(dateStr, holidays) {
        // Check if the date is in our holidays array
        const holiday = holidays.find(h => h.date === dateStr);
        return !!holiday;
    }

    function isSunday(date) {
        return new Date(date).getDay() === 0; // 0 is Sunday in JavaScript
    }

    function renderAttendanceTable(data, year, month, holidays) {
        // Clear existing table data
        // $('#date-headers').empty();
        // $('#in-out-headers').empty();
        // $('#attendanceBody').empty();

        // Create date headers
        var daysInMonth = new Date(year, month, 0).getDate();

        // Add date headers to the table
        for (var day = 1; day <= daysInMonth; day++) {
            let dateStr = `${year}-${month.toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
            let fullDate = new Date(year, month - 1, day);
            let isSundayCheck = isSunday(fullDate);
            let holidayName = getHolidayName(dateStr, holidays);
            let isHolidayCheck = !!holidayName;

            // Add the date to the headers
            $('#date-headers').append(
                `<th colspan="2" class="${(isSundayCheck || isHolidayCheck) ? 'bg-light text-danger' : ''}">${day}</th>`
            ).find('th:last').css({
                'text-align': 'center',
                'vertical-align': 'middle'
            });

            $('#in-out-headers').append('<th>In</th><th>Out</th>').find('th:last-child, th:nth-last-child(2)').css({
                'text-align': 'center',
                'vertical-align': 'middle'
            });
        }

        // Add employee rows
        data.forEach(function(employee) {
            var row = $('<tr>');
            row.append(`<td class="employee-col">${employee.name} (${employee.employee_id})</td>`);

            for (var day = 1; day <= daysInMonth; day++) {
                let dateStr = `${year}-${month.toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
                let fullDate = new Date(year, month - 1, day);
                let isSundayCheck = isSunday(fullDate);
                let holidayName = getHolidayName(dateStr, holidays);
                let isHolidayCheck = !!holidayName;

                var dayData = employee.attendance[day];
                var inCell = $('<td class="attendance-cell">');
                var outCell = $('<td class="attendance-cell">');

                // If it's a holiday or Sunday, add a special class
                if (isHolidayCheck || isSundayCheck) {
                    inCell.addClass('bg-light text-danger');
                    outCell.addClass('bg-light text-danger');

                    if (isHolidayCheck) {
                        inCell.html(`<small>Holiday</small>`);
                        outCell.html(`<small>Holiday</small>`);
                    } else {
                        inCell.html('<small>Sunday</small>');
                        outCell.html('<small>Sunday</small>');
                    }
                } else if (dayData) {
                    // Handle IN cell
                    if (dayData.hour_in) {
                        let inClass = '';

                        // Determine class based on status and minutes
                        if (dayData.status_in === 'late') {
                            if (dayData.late_minutes <= 10 && dayData.late_minutes >= 1) {
                                inClass = 'bg-warning';
                            } else {
                                inClass = 'bg-danger text-white';
                            }
                            inCell.html(dayData.hour_in);
                        } else {
                            inClass = 'bg-success text-white';
                            inCell.html(dayData.hour_in);
                        }

                        inCell.addClass(inClass);
                    }

                    // Handle OUT cell
                    if (dayData.hour_out) {
                        let outClass = '';

                        // Determine class based on status and minutes
                        if (dayData.status_out === 'early') {
                            if (dayData.early_minutes <= 10 && dayData.early_minutes >= 1) {
                                outClass = 'bg-warning';
                            } else {
                                outClass = 'bg-danger text-white';
                            }
                            outCell.html(dayData.hour_out);
                        } else {
                            outClass = 'bg-success text-white';
                            outCell.html(dayData.hour_out);
                        }

                        outCell.addClass(outClass);
                    }

                    // Add data for tooltip
                    if (dayData.hour_in || dayData.hour_out) {
                        // Create separate data objects for in and out
                        let inData = {
                            ...dayData
                        };
                        let outData = {
                            ...dayData
                        };

                        // For IN cell, focus on IN-related info
                        inData.isInCell = true;
                        inCell.data('details', inData);

                        // For OUT cell, focus on OUT-related info
                        outData.isOutCell = true;
                        outCell.data('details', outData);
                    }
                }

                row.append(inCell);
                row.append(outCell);
            }

            $('#attendanceBody').append(row);
        });

        // After table is populated, initialize DataTable with proper configuration
        if ($.fn.DataTable.isDataTable('#attendanceTable')) {
            $('#attendanceTable').DataTable().destroy();
        }

        $('#attendanceTable').DataTable({
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
    }

    function fetchHolidays(year) {
        // Periksa cache dulu
        if (holidayCache[year]) {
            return Promise.resolve(holidayCache[year]);
        }

        // Gunakan API untuk mendapatkan hari libur Indonesia
        return $.ajax({
            url: `https://api-harilibur.vercel.app/api?year=${year}`,
            method: 'GET',
            dataType: 'json'
        }).then(function(response) {
            // Filter hanya hari libur nasional
            const holidays = response
                .filter(item => item.is_national_holiday)
                .map(item => {
                    return {
                        date: item.holiday_date,
                        name: item.holiday_name
                    };
                });

            // Simpan dalam cache
            holidayCache[year] = holidays;
            return holidays;
        }).catch(function(error) {
            console.error('Error fetching holidays:', error);
            return []; // Return empty array on error
        });
    }

    // Fungsi untuk memeriksa hari libur
    function getHolidayName(dateStr, holidays) {
        const holiday = holidays.find(h => h.date === dateStr);
        return holiday ? holiday.name : null;
    }
</script>



@endpush