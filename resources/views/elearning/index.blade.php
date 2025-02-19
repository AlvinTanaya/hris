@extends('layouts.app')

@section('content')
<style>
    #educationTab .nav-link {
        color: white;
        font-weight: 500;
        padding: 1rem;
        transition: all 0.3s ease;
    }

    #educationTab .nav-link.active {
        color: #0d6efd;
        border-bottom: 3px solid #0d6efd;
    }



    /* Background modal lebih gelap dengan efek blur */
    /* .modal-backdrop {
        background-color: rgba(0, 0, 0, 0.7) !important;
        backdrop-filter: blur(5px);
    } */
</style>

<h1 class="text-center text-warning" style="margin-bottom: 65px; margin-top:25px"><i class="fas fa-book"></i> E-learning</h1>

<div class="container mt-4 mx-auto">


    <ul class="nav nav-tabs d-flex w-100" id="educationTab" role="tablist">
        <li class="nav-item flex-grow-1 text-center" role="presentation">
            <a class="nav-link active" id="lessonTab" data-bs-toggle="tab" href="#lesson" role="tab" aria-controls="lesson" aria-selected="true">
                <i class="fas fa-book"></i> Lesson
            </a>
        </li>
        <li class="nav-item flex-grow-1 text-center" role="presentation">
            <a class="nav-link" id="scheduleTab" data-bs-toggle="tab" href="#schedule" role="tab" aria-controls="schedule" aria-selected="false">
                <i class="fas fa-calendar-alt"></i> Schedule
            </a>
        </li>
    </ul>


    <div class="tab-content mt-4">
        <!-- Lesson Section -->
        <div class="tab-pane fade show active" id="lesson" role="tabpanel">

            <!-- Lesson Filter -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="text-primary mt-2"><i class="fas fa-filter"></i> Filter Lessons</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('elearning.index') }}" method="GET" class="row g-3">
                        <input type="hidden" name="tab" value="lesson">
                        <div class="col-md-6">
                            <label for="lesson_created_at" class="form-label">Created Date</label>
                            <input type="date" class="form-control" id="lesson_created_at" name="lesson_created_at" value="{{ request('lesson_created_at') }}">
                        </div>
                        <div class="col-md-6">
                            <label for="duration_range" class="form-label">Duration Range</label>
                            <select class="form-select" id="duration_range" name="duration_range">
                                <option value="">All Durations</option>
                                <option value="0-60" {{ request('duration_range') == '0-60' ? 'selected' : '' }}>0-60 minutes</option>
                                <option value="61-120" {{ request('duration_range') == '61-120' ? 'selected' : '' }}>1-2 hours</option>
                                <option value="121-180" {{ request('duration_range') == '121-180' ? 'selected' : '' }}>2-3 hours</option>
                                <option value="181+" {{ request('duration_range') == '181+' ? 'selected' : '' }}>3+ hours</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Apply Filters
                            </button>
                            <a href="{{ route('elearning.index') }}?tab=lesson" class="btn btn-secondary">
                                <i class="fas fa-undo me-2"></i>Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="text-primary mt-2"><i class="fas fa-book-reader"></i> Lesson List</h5>
                    <a href="{{ route('elearning.create_lesson') }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-2"></i>Add Lesson
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="lessonTable" class="table table-bordered table-hover mb-3 pt-3">
                            <thead class="table-dark">
                                <tr>
                                    <th>Lesson Name</th>
                                    <th style="width: 17%">Duration (minutes)</th>
                                    <th style="width: 10%">Passing Grade</th>
                                    <th style="width: 10%">Created At</th>
                                    <th style="width: 10%">Material</th>
                                    <th style="width: 15%">Question</th>
                                    <th style="width: 21%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($lesson as $item)
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->duration }} minutes</td>
                                    <td>{{ $item->passing_grade }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary view-pdf-btn" data-pdf-url="{{ asset('storage/' . $item->lesson_file) }}">
                                            <i class="fas fa-file-alt"></i> View File
                                        </button>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-info btn-sm view-questions-btn" data-lesson-id="{{ $item->id }}">
                                            <i class="fas fa-question-circle"></i> View Questions
                                        </button>
                                    </td>
                                    <td>
                                        <a href="{{ route('elearning.edit_lesson', $item->id) }}"
                                            class="btn btn-warning btn-sm check-lesson"
                                            data-lesson-id="{{ $item->id }}">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        &nbsp;
                                        <a hidden id="deleteLessonAnswer" href="javascript:void(0);" class="btn btn-danger btn-sm delete-lesson" data-delete-lesson-id="{{ $item->id }}">
                                            <i class="fas fa-trash"></i> Erase All User Answers
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal for Viewing PDF -->
            <div class="modal fade" id="pdfModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title"><i class="far fa-file-pdf"></i> View PDF Material</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <embed id="pdfViewer" src="" type="application/pdf" width="100%" height="500px">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal for Viewing Questions -->
            <div class="modal fade" id="questionModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-info text-dark">
                            <h5 class="modal-title"><i class="far fa-question-circle"></i> View Questions</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="table-responsive">
                                <table id="questionTable" class="table table-bordered mb-3 pt-3">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>No</th>
                                            <th>Question</th>
                                            <th>Score</th>
                                            <th>Choices</th>
                                        </tr>
                                    </thead>
                                    <tbody id="questionTableBody"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Schedule Section -->
        <div class="tab-pane fade" id="schedule" role="tabpanel">
            <!-- Schedule Filter -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="text-primary mt-2"><i class="fas fa-filter"></i> Filter Schedules</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('elearning.index') }}" method="GET" class="row g-3">
                        <input type="hidden" name="tab" value="schedule">
                        <div class="col-md-4">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-4">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                        </div>
                        <div class="col-md-4">
                            <label for="schedule_created_at" class="form-label">Created Date</label>
                            <input type="date" class="form-control" id="schedule_created_at" name="schedule_created_at" value="{{ request('schedule_created_at') }}">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Apply Filters
                            </button>
                            <a href="{{ route('elearning.index') }}?tab=schedule" class="btn btn-secondary">
                                <i class="fas fa-undo me-2"></i>Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>


            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="text-primary mt-2"><i class="fas fa-calendar-alt"></i> Schedule</h5>
                    <a href="{{ route('elearning.create_schedule') }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-2"></i>Add Schedule
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="scheduleTable" class="table table-bordered table-hover mb-3 pt-3">
                            <thead class="table-dark">
                                <tr>
                                    <th style="width: 25%">Event Name</th>
                                    <th>Date Start</th>
                                    <th>Date End</th>
                                    <th>Created at</th>
                                    <th>Attendance</th>
                                    <th style="width: 150px">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($schedulesWithLessons as $item)
                                <tr>
                                    <td>{{ $item->lesson_name }}</td>
                                    <td>{{ $item->start_date }}</td>
                                    <td>{{ $item->end_date }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}</td>
                                    <td>
                                        <button type="button" class="btn btn-info btn-sm view-invitation-btn" data-schedule-id="{{ $item->schedule_id }}">
                                            <i class="fas fa-users"></i> View Invitations
                                        </button>
                                    </td>
                                    <td>
                                        <a href="{{ route('elearning.edit_schedule', $item->schedule_id) }}"
                                            class="btn btn-warning btn-sm check-schedule"
                                            data-schedule-id="{{ $item->schedule_id }}">
                                            <i class="fas fa-edit"></i> Edit Schedule
                                        </a>
                                        &nbsp;
                                        <a hidden id="deleteScheduleAnswer" href="javascript:void(0);" class="btn btn-danger btn-sm delete-schedule" data-delete-schedule-id="{{ $item->schedule_id }}">
                                            <i class="fas fa-trash"></i> Erase All Schedule Answers
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

        <!-- Modal for Viewing Employee Invitations -->
        <div class="modal fade" id="invitationModal" tabindex="-1" aria-labelledby="invitationModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="invitationModalLabel">Employee Invitations</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table id="invitationEmployeeTable" class="table table-bordered table-hover mb-3 pt-3">
                                <thead class="table-dark text-center">
                                    <tr>
                                        <th style="width: 10%;">No</th>
                                        <th style="width: 40%;">Employee ID</th>
                                        <th style="width: 40%;">Employee Name</th>
                                    </tr>
                                </thead>
                                <tbody id="invitationTableBody">
                                    <!-- Data akan dimuat lewat AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        $('#lessonTable').DataTable();
        $('#scheduleTable').DataTable();
        // $('#questionTable').DataTable();
        // $('#invitationEmployeeTable').DataTable();

        // Handle opening the modal for PDF
        $('.view-pdf-btn').click(function() {
            var pdfUrl = $(this).data('pdf-url');
            $('#pdfViewer').attr('src', pdfUrl);
            $('#pdfModal').modal('show');
        });

        // check lesson
        $('.check-lesson').each(function() {
            var button = $(this);
            var buttonErase = $("#deleteLessonAnswer");
            var lessonId = button.data('lesson-id');

            $.ajax({
                url: '/elearning/check-lesson/' + lessonId,
                type: 'GET',
                success: function(response) {
                    if (response.lessonExist) {
                        buttonErase.prop('hidden', false);

                        button.addClass('disabled');
                        button.addClass('btn-secondary').removeClass('btn-warning');
                    }
                },
                error: function() {
                    console.error('Gagal mengecek data');

                }
            });
        });

        // check lesson
        $('.check-schedule').each(function() {
            var button = $(this);
            var buttonErase = $("#deleteScheduleAnswer");
            var scheduleId = button.data('schedule-id');

            $.ajax({
                url: '/elearning/check-schedule/' + scheduleId,
                type: 'GET',
                success: function(response) {
                    if (response.scheduleExist) {
                        buttonErase.prop('hidden', false);
                        button.addClass('disabled');
                        button.addClass('btn-secondary').removeClass('btn-warning');
                    }
                },
                error: function() {
                    console.error('Gagal mengecek data');
                }
            });
        });

        // Handle tombol "View Questions"
        $('.view-questions-btn').click(function() {
            var lessonId = $(this).data('lesson-id'); // Ambil lesson ID
            $('#questionTableBody').html('<tr><td colspan="4" class="text-center">Loading...</td></tr>'); // Placeholder

            $.ajax({
                url: '/elearning/questions/' + lessonId,
                type: 'GET',
                success: function(response) {
                    var rows = '';
                    if (response.questions.length > 0) {
                        response.questions.forEach(function(question, index) {
                            var choicesHtml = '';
                            var choices = question.multiple_choice.split(';'); // Pisahkan pilihan
                            var answerKey = question.answer_key; // Ambil jawaban benar

                            choices.forEach(function(choice) {
                                var isChecked = (choice.trim() === answerKey.trim()) ? 'checked' : ''; // Tandai jawaban benar

                                choicesHtml += `
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="question_${question.id}" ${isChecked} disabled>
                                        <label class="form-check-label">${choice}</label>
                                    </div>
                                `;
                            });

                            rows += `
                                <tr>
                                    <td class="text-center align-middle">${index + 1}</td>
                                    <td class="align-middle">${question.question}</td>
                                    <td class="text-center align-middle">${question.grade}</td>
                                    <td>${choicesHtml}</td>
                                </tr>
                            `;
                        });
                    } else {
                        rows = '<tr><td colspan="4" class="text-center text-muted">No questions available</td></tr>';
                    }
                    $('#questionTableBody').html(rows);
                    $('#questionModal').modal('show');
                },
                error: function() {
                    $('#questionTableBody').html('<tr><td colspan="4" class="text-center text-danger">Failed to load questions</td></tr>');
                }
            });
        });

        // Handle tombol "View Invitation"
        $('.view-invitation-btn').click(function() {
            var scheduleId = $(this).data('schedule-id'); // Ambil ID schedule
            $('#invitationTableBody').html('<tr><td colspan="5" class="text-center">Loading...</td></tr>'); // Placeholder

            $.ajax({
                url: '/elearning/invitation/' + scheduleId,
                type: 'GET',
                success: function(response) {
                    var rows = '';
                    if (response.invitationEmployee.length > 0) {
                        response.invitationEmployee.forEach(function(invite, index) {
                            rows += `
                        <tr>
                            <td class="text-center align-middle">${index + 1}</td>
                            
                            <td class="align-middle text-center">${invite.employee_id}</td>
                            <td class="align-middle text-center">${invite.users_name}</td>
                        </tr>
                    `;
                        });
                    } else {
                        rows = '<tr><td colspan="5" class="text-center text-muted">No invitations available</td></tr>';
                    }
                    $('#invitationTableBody').html(rows);
                    $('#invitationModal').modal('show');
                },
                error: function() {
                    $('#invitationTableBody').html('<tr><td colspan="5" class="text-center text-danger">Failed to load invitations</td></tr>');
                }
            });
        });


        // Hapus Jawaban berdasarkan Lesson
        $('.delete-lesson').click(function() {
            var lessonId = $(this).data('delete-lesson-id');
            console.log(lessonId);
            Swal.fire({
                title: "Apakah Anda yakin?",
                text: "Apakah Anda yakin ingin menghapus semua jawaban peserta yang menggunakan lesson ini?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Ya, Hapus!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/elearning/delete_lesson_answer/' + lessonId,
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire("Dihapus!", "Semua jawaban peserta telah dihapus.", "success");
                            location.reload();
                        },
                        error: function() {
                            Swal.fire("Gagal!", "Terjadi kesalahan saat menghapus.", "error");
                        }
                    });
                }
            });
        });

        // Hapus Jawaban berdasarkan Schedule
        $('.delete-schedule').click(function() {
            var scheduleId = $(this).data('delete-schedule-id');

            Swal.fire({
                title: "Apakah Anda yakin?",
                text: "Apakah Anda yakin ingin menghapus semua jawaban peserta pada invitation ini?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Ya, Hapus!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/elearning/delete_schedule_answer/' + scheduleId,
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire("Dihapus!", "Semua jawaban peserta telah dihapus.", "success");
                            location.reload();
                        },
                        error: function() {
                            Swal.fire("Gagal!", "Terjadi kesalahan saat menghapus.", "error");
                        }
                    });
                }
            });
        });
    });
</script>
@endpush