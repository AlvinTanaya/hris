@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-edit"></i> Edit Performance Evaluation</h5>
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

                    <div class="alert alert-info">
                        <p class="mb-1"><strong>Employee:</strong> {{ $user->name }}</p>
                        <p class="mb-1"><strong>Position:</strong> {{ $user->position->position ?? 'N/A' }}</p>
                        <p class="mb-0"><strong>Evaluation Period:</strong> {{ date('F Y', strtotime($evaluation->date)) }}</p>
                    </div>

                    <form action="{{ route('evaluation.assign.performance.update', $evaluation->id) }}" method="POST" id="evaluationForm">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="final_score" id="final-score-input" value="{{ $evaluation->final_score }}">
                        <input type="hidden" name="raw_score" id="raw-score-input" value="{{ $evaluation->total_score }}">
                        <input type="hidden" name="total_reduction" id="total-reduction-input" value="{{ $evaluation->total_reduction }}">

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
                                    @if($performanceWeights->count() > 0)
                                    @foreach($performanceWeights as $index => $weight)
                                    @php
                                    $currentValue = $existingValues[$weight->id] ?? null;
                                    $score = $currentValue ? $weight->weight * $currentValue : 0;
                                    @endphp
                                    <tr data-criteria-id="{{ $weight->id }}" class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-light' }}">
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td class="font-weight-bold">{{ $weight->criteria->type }}</td>
                                        <td class="text-center weight-value font-weight-bold" data-weight="{{ $weight->weight }}">{{ $weight->weight }}</td>
                                        <input type="hidden" name="weight_performance_id[]" value="{{ $weight->id }}">
                                        <td class="align-middle">
                                            <div class="d-flex justify-content-center align-items-center">
                                                <input class="form-check-input rating-input" type="radio" name="value[{{ $weight->id }}]" value="1" {{ $currentValue == 1 ? 'checked' : '' }} required data-weight="{{ $weight->weight }}">
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            <div class="d-flex justify-content-center align-items-center">
                                                <input class="form-check-input rating-input" type="radio" name="value[{{ $weight->id }}]" value="1.5" {{ $currentValue == 1.5 ? 'checked' : '' }} required data-weight="{{ $weight->weight }}">
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            <div class="d-flex justify-content-center align-items-center">
                                                <input class="form-check-input rating-input" type="radio" name="value[{{ $weight->id }}]" value="2" {{ $currentValue == 2 ? 'checked' : '' }} required data-weight="{{ $weight->weight }}">
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            <div class="d-flex justify-content-center align-items-center">
                                                <input class="form-check-input rating-input" type="radio" name="value[{{ $weight->id }}]" value="2.5" {{ $currentValue == 2.5 ? 'checked' : '' }} required data-weight="{{ $weight->weight }}">
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            <div class="d-flex justify-content-center align-items-center">
                                                <input class="form-check-input rating-input" type="radio" name="value[{{ $weight->id }}]" value="3" {{ $currentValue == 3 ? 'checked' : '' }} required data-weight="{{ $weight->weight }}">
                                            </div>
                                        </td>
                                        <td class="text-center score-cell font-weight-bold text-primary">{{ number_format($score, 0) }}</td>
                                    </tr>
                                    @endforeach
                                    @else
                                    <tr>
                                        <td colspan="9" class="text-center">No performance criteria defined for this position</td>
                                    </tr>
                                    @endif
                                </tbody>
                                <tbody id="warning-letters-container">
                                    @if(count($reductionDetails) > 0)
                                    <tr class="bg-warning text-dark">
                                        <td colspan="9" class="font-weight-bold">Warning Letter</td>
                                    </tr>
                                    @foreach($reductionDetails as $index => $item)
                                    <tr class="warning-letter-row">
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>{{ $item['type'] }}</td>
                                        <td class="text-center">{{ $item['date'] }}</td>
                                        <td colspan="5" class="text-center">{{ $item['letter_number'] ?? '' }}</td>
                                        <td class="text-center font-weight-bold text-danger">-{{ number_format($item['reduction'], 0) }}</td>
                                    </tr>
                                    @endforeach
                                    <tr class="total-reduction-row bg-light">
                                        <td colspan="8" class="text-right font-weight-bold">Total Reduction:</td>
                                        <td id="total-reduction-display" class="text-center font-weight-bold text-danger">-{{ number_format($totalReduction, 0) }}</td>
                                    </tr>
                                    @endif
                                </tbody>
                                <tfoot class="bg-light">
                                    <tr>
                                        <td colspan="8" class="text-right font-weight-bold">Raw Score:</td>
                                        <td id="raw-total-score" class="text-center font-weight-bold text-primary">{{ number_format($evaluation->total_score, 0) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="8" class="text-right font-weight-bold">Final Score:</td>
                                        <td id="final-score-display" class="text-center font-weight-bold text-primary">{{ number_format($evaluation->final_score, 0) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle"></i> <strong>Note:</strong> Please select a rating for each criteria to update the evaluation.
                        </div>

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
                                            @foreach($evaluation->messages as $index => $message)
                                            <tr data-message-id="{{ $message->id }}">
                                                <td class="text-center">{{ $index + 1 }}</td>
                                                <td>{{ $message->message }}</td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-danger delete-message" data-message-id="{{ $message->id }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            @endforeach
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

                                <input type="hidden" name="evaluation_messages" id="evaluation-messages-input" value="{{ json_encode($evaluation->messages->map(function($msg) {return ['message' => $msg->message]; }), JSON_PRETTY_PRINT) }}">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-check-circle"></i> Update Evaluation
                            </button>
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
<script>
    $(document).ready(function() {
        let totalReduction = {{ $evaluation->total_reduction }};
        let reductionDetails = @json($reductionDetails);
        let messages = @json($evaluation->messages->map(function($msg) { return ['id' => $msg->id, 'message' => $msg->message]; }));
        let messageCounter = {{ $evaluation->messages->count() + 1 }};

        // Calculate initial scores on page load
        calculateScores();

        // Attach event listener to rating inputs
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

        // Add message button click handler
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

        // Delete message click handler (using event delegation)
        $(document).on('click', '.delete-message', function() {
            const messageId = $(this).data('message-id');

            // Remove from array
            messages = messages.filter(msg => msg.id != messageId);

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

        function updateMessagesInput() {
            $('#evaluation-messages-input').val(JSON.stringify(messages.map(msg => {
                return {
                    message: msg.message
                };
            })));
        }

        // Calculate scores when a rating is selected
        function calculateScores() {
            let rawTotalScore = 0;

            // For each row in the criteria table
            $('#criteria-container tr[data-criteria-id]').each(function() {
                const row = $(this);
                const weight = parseFloat(row.find('.weight-value').data('weight'));
                const selectedRating = parseFloat(row.find('.rating-input:checked').val() || 0);

                // Calculate score for this criteria
                const score = weight * selectedRating;

                // Update the score cell with whole numbers (no decimals)
                row.find('.score-cell').text(Math.round(score));

                // Add to total
                rawTotalScore += score;
            });

            // Update raw total score display and input
            $('#raw-total-score').text(Math.round(rawTotalScore));
            $('#raw-score-input').val(Math.round(rawTotalScore));

            // Calculate final score
            const finalScore = Math.max(0, rawTotalScore - totalReduction);
            $('#final-score-display').text(Math.round(finalScore));
            $('#final-score-input').val(Math.round(finalScore));

            // Show success message when all criteria are rated
            const totalCriteria = $('#criteria-container tr[data-criteria-id]').length;
            const ratedCriteria = $('#criteria-container tr .rating-input:checked').length;

            if (totalCriteria === ratedCriteria && totalCriteria > 0) {
                Swal.fire({
                    title: 'All criteria rated!',
                    text: 'You can now update the evaluation.',
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