@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-plus-circle"></i> Assign New Performance Evaluation</h5>
                    <a href="{{ route('evaluation.assign.performance.index', Auth::user()->id) }}" class="btn btn-danger">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
                <div class="card-body bg-light">
                    @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('evaluation.assign.performance.store') }}" method="POST" id="evaluationForm">
                        @csrf
                        <input type="hidden" name="evaluator_id" value="{{ Auth::user()->id }}">

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="user_id" class="font-weight-bold text-primary">Select Employee</label>
                                    <select name="user_id" id="user_id" class="form-control @error('user_id') is-invalid @enderror border-primary" required>
                                        <option value="">-- Select Employee --</option>
                                        @foreach($subordinates as $subordinate)
                                        <option value="{{ $subordinate->id }}" data-position="{{ $subordinate->position_id }}">
                                            {{ $subordinate->name }} ({{ $subordinate->position->position ?? 'No Position' }})
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="evaluation_date" class="font-weight-bold text-primary">Evaluation Period</label>
                                    <input type="date" name="evaluation_date" id="evaluation_date"
                                        class="form-control @error('evaluation_date') is-invalid @enderror border-primary"
                                        required>
                                    <small class="form-text text-muted">Select any date in the month/year you want to evaluate</small>
                                    @error('evaluation_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div id="evaluation-form-container" class="d-none">
                            <h4 class="mb-3 text-primary border-bottom pb-2">Performance Evaluation Criteria</h4>

                            <div class="table-responsive">
                                <table class="table table-bordered table-hover w-100 excel-like-table">
                                    <thead class="table-primary">
                                        <tr>
                                            <th style="width: 5%; text-align: center; vertical-align: middle;" rowspan="2">No</th>
                                            <th style="width: 30%; text-align: center; vertical-align: middle;" rowspan="2">Criteria</th>
                                            <th style="width: 10%; text-align: center; vertical-align: middle;" rowspan="2">Weight</th>
                                            <th colspan="5" style="text-align: center; vertical-align: middle;">Rating</th>
                                            <th style="width: 10%; text-align: center; vertical-align: middle;" rowspan="2">Score</th>
                                        </tr>
                                        <tr class="bg-light">
                                            <th class="text-center">1<br><small>Very Poor</small></th>
                                            <th class="text-center">1.5<br><small>Poor</small></th>
                                            <th class="text-center">2<br><small>Adequate</small></th>
                                            <th class="text-center">2.5<br><small>Good</small></th>
                                            <th class="text-center">3<br><small>Excellent</small></th>
                                        </tr>
                                    </thead>
                                    <tbody id="criteria-container">
                                        <!-- Performance criteria will be loaded here dynamically -->
                                    </tbody>
                                    <tbody id="warning-letters-container">
                                        <!-- Warning letters will be loaded here dynamically -->
                                    </tbody>
                                    <tfoot class="bg-light">
                                        <tr>
                                            <th colspan="8" class="text-right font-weight-bold">Total:</th>
                                            <th id="final-score-display" class="text-center font-weight-bold text-primary">0.0</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <!-- Add a hidden input to store the final score -->
                            <input type="hidden" name="final_score" id="final-score-input" value="0">
                            <input type="hidden" name="raw_score" id="raw-score-input" value="0">
                            <input type="hidden" name="total_reduction" id="total-reduction-input" value="0">

                            <div class="alert alert-info mt-3">
                                <i class="fas fa-info-circle"></i> <strong>Note:</strong> Empty cells are waiting for your evaluation. Please select a rating for each criteria.
                            </div>

                            <!-- Add this section before the submit button in your form -->
                            <div class="card mt-4">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0"><i class="fas fa-comment"></i> Evaluation Messages</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover" id="messages-table">
                                            <thead class="table-info">
                                                <tr>
                                                    <th style="width: 5%;" class="text-center">No</th>
                                                    <th style="width: 85%;">Message</th>
                                                    <th style="width: 10%;" class="text-center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="messages-container">
                                                <!-- Messages will be added here dynamically -->
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="form-group mt-3">
                                        <label for="new-message" class="font-weight-bold text-info">Add New Message:</label>
                                        <textarea id="new-message" class="form-control border-info" rows="3" placeholder="Enter your message here..."></textarea>
                                    </div>

                                    <button type="button" id="add-message-btn" class="btn btn-info mt-2">
                                        <i class="fas fa-plus-circle"></i> Add Message
                                    </button>

                                    <!-- Hidden input to store messages data for form submission -->
                                    <input type="hidden" name="evaluation_messages" id="evaluation-messages-input" value="[]">
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-check-circle"></i> Submit Evaluation
                                </button>
                            </div>
                        </div>

                        <div id="validation-message" class="alert alert-warning d-none">
                            <i class="fas fa-exclamation-triangle"></i> This employee has already been evaluated for the selected period.
                        </div>

                        <div id="no-criteria-message" class="alert alert-info d-none">
                            <i class="fas fa-info-circle"></i> No performance criteria defined for this employee's position.
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        let totalReduction = 0;
        let reductionDetails = [];
        let messages = [];
        let messageCounter = 1;

        $('#user_id, #evaluation_date').change(function() {
            if ($('#user_id').val() && $('#evaluation_date').val()) {
                checkExistingEvaluation();
            } else {
                $('#evaluation-form-container').addClass('d-none');
                $('#validation-message').addClass('d-none');
                $('#no-criteria-message').addClass('d-none');
            }
        });

        function addMessage(text) {
            const messageId = Date.now(); // Use timestamp as unique ID

            // Add to messages array
            messages.push({
                id: messageId,
                message: text
            });

            // Add to table
            const messageHtml = `
            <tr data-message-id="${messageId}">
                <td class="text-center">${messageCounter}</td>
                <td>${text}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger delete-message" data-message-id="${messageId}">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;

            $('#messages-container').append(messageHtml);
            messageCounter++;

            // Update hidden input with messages data
            updateMessagesInput();
        }

        // Delete message click handler (using event delegation)
        $(document).on('click', '.delete-message', function() {
            const messageId = $(this).data('message-id');

            // Remove from array
            messages = messages.filter(msg => msg.id !== messageId);

            // Remove from table
            $(`tr[data-message-id="${messageId}"]`).remove();

            // Renumber rows
            $('#messages-container tr').each(function(index) {
                $(this).find('td:first').text(index + 1);
            });

            messageCounter = $('#messages-container tr').length + 1;

            // Update hidden input with messages data
            updateMessagesInput();
        });

        // Function to update hidden input with messages data
        function updateMessagesInput() {
            $('#evaluation-messages-input').val(JSON.stringify(messages));
        }

        // Add to form submit handler
        $('#evaluationForm').submit(function() {
            // Make sure messages are included in the form data
            updateMessagesInput();
            return true;
        });

        $('#add-message-btn').click(function() {
            const messageText = $('#new-message').val().trim();

            if (messageText) {
                addMessage(messageText);
                $('#new-message').val(''); // Clear the textarea
            } else {
                Swal.fire({
                    title: 'Empty message',
                    text: 'Please enter a message before adding.',
                    icon: 'warning',
                });
            }
        });

        function checkExistingEvaluation() {
            let userId = $('#user_id').val();
            let evaluationDate = $('#evaluation_date').val();

            if (userId && evaluationDate) {
                // Extract month and year from the date
                let dateObj = new Date(evaluationDate);
                let month = dateObj.getMonth() + 1; // JavaScript months are 0-indexed
                let year = dateObj.getFullYear();

                $.ajax({
                    url: '{{ route("evaluation.check.existing") }}',
                    type: 'GET',
                    data: {
                        user_id: userId,
                        month: month,
                        year: year
                    },
                    success: function(response) {
                        if (response.exists) {
                            $('#evaluation-form-container').addClass('d-none');
                            $('#validation-message').removeClass('d-none');
                            $('#no-criteria-message').addClass('d-none');
                        } else {
                            loadPerformanceCriteria();
                            checkWarningLetters(userId, month, year);
                        }
                    }
                });
            }
        }

        function checkWarningLetters(userId, month, year) {
            $.ajax({
                url: '{{ route("evaluation.get.warning.letters") }}',
                type: 'GET',
                data: {
                    user_id: userId,
                    month: month,
                    year: year
                },
                success: function(response) {
                    totalReduction = response.total_reduction;
                    reductionDetails = response.reduction_details;

                    // Reset the warning letters container
                    $('#warning-letters-container').empty();

                    if (reductionDetails.length > 0) {
                        // Add a separator row
                        $('#warning-letters-container').append(`
                            <tr>
                                <td colspan="9" class="font-weight-bold bg-danger text-white"><i class="fas fa-exclamation-triangle"></i> Warning Letter</td>
                            </tr>
                            <tr style="text-align: center; vertical-align: middle; font-weight: bold;">
                                <td>No</td>
                                <td>Type</td>
                                <td>Date</td>
                                <td colspan="5">Letter Number</td>
                                <td>Score</td>
                            </tr>
                        `);

                        // Add each warning letter to the table
                        $.each(reductionDetails, function(index, item) {
                            const reductionValue = parseFloat(item.reduction);

                            $('#warning-letters-container').append(`
                                <tr class="warning-letter-row">
                                    <td class="text-center">${index + 1}</td>
                                    <td>${item.type}</td>
                                    <td class="text-center">${item.date}</td>
                                    <td colspan="5" class="text-center">${item.letter_number ? item.letter_number : ''}</td>
                                    
                                    <td class="text-center font-weight-bold text-danger">-${isNaN(reductionValue) ? 0 : reductionValue.toFixed(0)}</td>
                                </tr>
                            `);
                        });

                        // Add the total reduction row
                        $('#warning-letters-container').append(`
                            <tr class="total-reduction-row bg-light">
                                <td colspan="8" class="text-right font-weight-bold">Total Reduction:</td>
                                <td id="total-reduction-display" class="text-center font-weight-bold text-danger">-${parseFloat(totalReduction).toFixed(0)}</td>
                            </tr>
                        `);

                        $('#total-reduction-input').val(totalReduction);
                    }

                    // Reset calculations in case they were already done
                    if ($('#criteria-container .rating-input:checked').length > 0) {
                        calculateScores();
                    }
                }
            });
        }

        function loadPerformanceCriteria() {
            let selectedOption = $('#user_id option:selected');
            let positionId = selectedOption.data('position');

            if (positionId) {
                $.ajax({
                    url: '{{ route("evaluation.get.criteria") }}',
                    type: 'GET',
                    data: {
                        position_id: positionId
                    },
                    success: function(response) {
                        if (response.criteria && response.criteria.length > 0) {
                            let html = '';

                            $.each(response.criteria, function(index, item) {
                                html += `
                                <tr data-criteria-id="${item.id}" ${index % 2 === 0 ? 'class="bg-white"' : 'class="bg-light"'}>
                                    <td class="text-center">${index + 1}</td>
                                    <td class="font-weight-bold">${item.criteria.type}</td>
                                    <td class="text-center weight-value font-weight-bold" data-weight="${item.weight}">${item.weight}</td>
                                    <input type="hidden" name="weight_performance_id[]" value="${item.id}">
                                    <td class="align-middle">
                                        <div class="d-flex justify-content-center align-items-center">
                                            <input class="form-check-input rating-input" type="radio" name="value[${item.id}]" value="1" required data-weight="${item.weight}">
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <div class="d-flex justify-content-center align-items-center">
                                            <input class="form-check-input rating-input" type="radio" name="value[${item.id}]" value="1.5" required data-weight="${item.weight}">
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <div class="d-flex justify-content-center align-items-center">
                                            <input class="form-check-input rating-input" type="radio" name="value[${item.id}]" value="2" required data-weight="${item.weight}">
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <div class="d-flex justify-content-center align-items-center">
                                            <input class="form-check-input rating-input" type="radio" name="value[${item.id}]" value="2.5" required data-weight="${item.weight}">
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <div class="d-flex justify-content-center align-items-center">
                                            <input class="form-check-input rating-input" type="radio" name="value[${item.id}]" value="3" required data-weight="${item.weight}">
                                        </div>
                                    </td>
                                    <td class="text-center score-cell font-weight-bold text-primary">0</td>
                                </tr>
                            `;
                            });

                            // Add a raw score row
                            html += `
                            <tr class="raw-score-row bg-light">
                                <td colspan="8" class="text-right font-weight-bold">Raw Score:</td>
                                <td id="raw-total-score" class="text-center font-weight-bold text-primary">0</td>
                            </tr>
                            `;

                            $('#criteria-container').html(html);
                            $('#evaluation-form-container').removeClass('d-none');
                            $('#validation-message').addClass('d-none');
                            $('#no-criteria-message').addClass('d-none');

                            // Attach event listener to rating inputs after they're added to the DOM
                            $('.rating-input').change(calculateScores);

                            // Highlight row on hover
                            $('#criteria-container tr').hover(
                                function() {
                                    $(this).addClass('bg-info-light');
                                },
                                function() {
                                    $(this).removeClass('bg-info-light');
                                }
                            );
                        } else {
                            $('#evaluation-form-container').addClass('d-none');
                            $('#validation-message').addClass('d-none');
                            $('#no-criteria-message').removeClass('d-none');
                        }
                    }
                });
            }
        }

        // Calculate scores when a rating is selected
        function calculateScores() {
            let rawTotalScore = 0;

            // For each row in the criteria table
            $('#criteria-container tr[data-criteria-id]').each(function() {
                const row = $(this);
                const criteriaId = row.data('criteria-id');
                const weight = parseFloat(row.find('.weight-value').data('weight'));
                const selectedRating = parseFloat(row.find('.rating-input:checked').val() || 0);

                // Calculate score for this criteria
                const score = weight * selectedRating;

                // Update the score cell with whole numbers (no decimals)
                row.find('.score-cell').text(Math.round(score));

                // Add to total
                rawTotalScore += score;
            });

            // Update raw total score with whole numbers
            $('#raw-total-score').text(Math.round(rawTotalScore));
            $('#raw-score-input').val(Math.round(rawTotalScore));

            // Calculate final score
            const finalScore = Math.max(0, rawTotalScore - totalReduction);
            $('#final-score-display').text(Math.round(finalScore));
            $('#final-score-input').val(Math.round(finalScore));

            // Show success message when all criteria are rated
            const totalCriteria = $('#criteria-container tr[data-criteria-id]').length;
            const ratedCriteria = $('#criteria-container tr .rating-input:checked').length;

            if (totalCriteria === ratedCriteria) {
                Swal.fire({
                    title: 'All criteria rated!',
                    text: 'You can now submit the evaluation.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        }
    });
</script>

<style>
    .bg-info-light {
        background-color: rgba(23, 162, 184, 0.1) !important;
    }

    .card {
        border-radius: 10px;
        overflow: hidden;
    }

    .card-header {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    }

    .excel-like-table {
        border-collapse: collapse;
        width: 100%;
    }

    .excel-like-table th,
    .excel-like-table td {
        border: 1px solid #dee2e6;
        padding: 8px;
        vertical-align: middle;
    }

    .warning-letter-row {
        background-color: #fff3cd;
    }

    .total-reduction-row {
        background-color: #f8f9fa;
        font-weight: bold;
    }

    .raw-score-row {
        background-color: #e9ecef;
    }

    .table thead th {
        border-top: none;
        background-color: #e6f2ff;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.1);
    }

    .form-control:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }

    .btn-success {
        background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
        border: none;
    }

    .btn-danger {
        background: linear-gradient(135deg, #e74a3b 0%, #be2617 100%);
        border: none;
    }
</style>
@endpush