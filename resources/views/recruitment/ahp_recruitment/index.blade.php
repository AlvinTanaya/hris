@extends('layouts.app')

@section('content')

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<style>
    #container-body {
        margin: 0;
        padding: 20px;
        min-height: 100vh;
    }

    .page-title {
        text-align: center;
        margin: 2rem 0;
        font-size: 2.5rem;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }


    .card {
        border-radius: 15px;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        margin-bottom: 2rem;
        overflow: hidden;
        transition: transform 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
    }

    .card-header {
        color: #0d6efd;
        padding: 1.5rem;
    }

    .card-header h5 {
        margin: 0;
        font-size: 1.25rem;
    }

    .card-header small {
        opacity: 0.8;
        display: block;
        margin-top: 0.5rem;
    }

    .card-body {
        padding: 2rem;
    }

    .form-select {
        width: 100%;
        padding: 0.75rem;
        border: 2px solid #ddd;
        border-radius: 8px;
        font-size: 1rem;
        margin-bottom: 1.5rem;
    }

    .criteria-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
        justify-content: center;
        margin-bottom: 1.5rem;
    }

    .criteria-box {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 15px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        text-align: center;
        min-width: 150px;
    }

    .criteria-label {
        font-size: 14px;
        font-weight: bold;
        color: #2c3e50;
    }

    .criteria-percentage {
        font-size: 16px;
        font-weight: bold;
        color: #3498db;
    }

    .criteria-percentage {
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        font-weight: bold;
        color: #3498db;
    }



    .criteria-input {
        flex: 0 0 60%;
        padding: 0 15px;
    }

    .percentage-input {
        width: 60px;
        padding: 5px;
        border: 2px solid #ddd;
        border-radius: 8px;
        text-align: center;
        font-weight: bold;
        color: #2c3e50;
    }



    .calculate-btn {
        display: block;
        width: 200px;
        margin: 0 auto;
        background-color: #3498db;
        color: white;
        padding: 10px;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s;
    }

    .calculate-btn:hover {
        border: 2px solid #ccc;
        background-color: #2ecc71;
        /* Hijau terang */
        color: white;
        /* Warna teks agar kontras */
    }



    .percentage-total {
        text-align: center;
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 15px;
        color: #3498db;
        padding: 1rem;
    }

    .percentage-total.error {
        color: #e74c3c;
    }

    .btn-primary,
    .btn-success {
        color: white;
        border: none;
        padding: 1rem 2rem;
        border-radius: 8px;
        font-size: 1.1rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background: #3498db;
    }

    .btn-success {
        background: #2ecc71;
        margin-top: 1rem;
    }

    .btn-primary:hover {
        background: #2c3e50;
        transform: scale(1.05);
    }

    .btn-success:hover {
        background: #27ae60;
        transform: scale(1.05);
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1rem;
    }

    .table th {
        background: #2c3e50;
        color: white;
        padding: 1rem;
        text-align: left;
    }

    .table td {
        padding: 1rem;
        border-bottom: 1px solid #ddd;
    }

    .table tbody tr:hover {
        background: #ecf0f1;
    }

    .spinner-border {
        display: inline-block;
        width: 1rem;
        height: 1rem;
        border: 3px solid white;
        border-right-color: transparent;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }
</style>

<h1 class="page-title text-warning mb-5">
    <i class="fas fa-calculator"></i> AHP Recommendation System
</h1>
<div class="container mb-4 p-0 mx-auto" id="container-body">
    <!-- Criteria Percentage Form -->
    <div class="card">
        <div class="card-header">
            <h5>Criteria Weights</h5>
            <small>Assign percentage weights to each criterion (total must be 100%)</small>
        </div>
        <div class="card-body pt-3">
            <form id="ahpForm" onsubmit="return false;">
                @csrf
                <div>
                    <label for="demandId" class="mb-3">Select Demand:</label>
                    <select name="demandId" id="demandId" class="form-select" required>
                        <option value="" disabled selected>-- Select Demand --</option>
                        @foreach($demands as $demand)
                        <option value="{{ $demand->id }}">{{ $demand->recruitment_demand_id }} - {{ $demand->position }} ({{ $demand->department }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="criteria-container d-flex justify-content-center gap-3">
                    @foreach($criteria as $key => $label)
                    <div class="criteria-box text-center p-3">
                        <div class="criteria-label">{{ $label }}</div>
                        <div class="criteria-percentage mt-2">
                            <input type="number" name="{{ $key }}"
                                class="percentage-input criteria-percentage"
                                min="0" max="100" value="{{ 100 / count($criteria) }}"
                                required style="width: 80px; height: 45px; font-size: 20px;">


                            <span>%</span>
                        </div>
                    </div>
                    @endforeach


                    <div class="percentage-total" id="percentageTotal">
                        <strong>Total: <span id="totalValue">100.0</span>%</strong>
                    </div>

                </div>

                <div style="text-align: center">
                    <button type="button" id="calculateBtn" class="calculate-btn">Calculate Ranking</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Ranking Results -->
    <div class="card">
        <div class="card-header">
            <h5>Applicant Ranking Results</h5>
        </div>
        <div class="card-body">
            <div style="overflow-x: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Name</th>
                            <th>Age</th>
                            <th>Education</th>
                            <th>Experience</th>
                            <th>Training</th>
                            <th>Language</th>
                            <th>Organization</th>
                            <th>Total Score</th>
                        </tr>
                    </thead>
                    <tbody id="rankingResults">
                        <tr>
                            <td colspan="9" style="text-align: center">Please fill out the criteria weights above to see results</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Core Scripts -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function() {
        const $form = $('#ahpForm');
        const $calculateBtn = $('#calculateBtn');
        const $rankingResults = $('#rankingResults');
        const $percentageInputs = $('.criteria-percentage');
        const $totalValueSpan = $('#totalValue');
        const $percentageTotalDiv = $('#percentageTotal');

        // Update total when any percentage input changes
        $percentageInputs.on('input', updateTotal);

        function updateTotal() {
            let total = 0;
            $percentageInputs.each(function() {
                total += Number($(this).val()) || 0;
            });

            $totalValueSpan.text(total.toFixed(1));

            if (Math.abs(total - 100) > 0.1) {
                $percentageTotalDiv.addClass('error');
            } else {
                $percentageTotalDiv.removeClass('error');
            }
        }

        $calculateBtn.on('click', function() {
            const $demandSelect = $('#demandId');

            if (!$demandSelect.val()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Choose Labor Demand First!',
                    confirmButtonColor: '#3498db',
                    confirmButtonText: 'OK'
                }).then(() => {
                    $demandSelect.focus();
                });
                return;
            }

            // Check if total is 100%
            let total = 0;
            $percentageInputs.each(function() {
                total += Number($(this).val()) || 0;
            });

            if (Math.abs(total - 100) > 0.1) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Weights',
                    text: 'Total percentage must be 100%',
                    confirmButtonColor: '#3498db',
                    confirmButtonText: 'OK'
                });
                return;
            }

            $calculateBtn.prop('disabled', true);
            $calculateBtn.html('<span class="spinner-border" role="status" aria-hidden="true"></span> Loading...');

            const formData = $form.serializeArray();
            const jsonData = {};
            $.each(formData, function(_, field) {
                jsonData[field.name] = field.value;
            });

            $.ajax({
                url: '/ahp/calculate',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').val(),
                    'Accept': 'application/json'
                },
                contentType: 'application/json',
                data: JSON.stringify(jsonData),
                success: function(data) {
                    if (data.success) {
                        displayRankings(data.rankings);
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Server Error', 'error');
                },
                complete: function() {
                    $calculateBtn.prop('disabled', false);
                    $calculateBtn.html('Calculate Ranking');
                }
            });
        });

        function displayRankings(rankings) {
            let html = '';
            $.each(rankings, function(index, item) {
                try {
                    if (!item.applicant || !item.applicant.birth_date) return;
                    const age = moment().diff(moment(item.applicant.birth_date), 'years');
                    html += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item.applicant.name}</td>
                        <td>${age} Year</td>
                        <td>${(item.breakdown.education * 100).toFixed(1)}%</td>
                        <td>${(item.breakdown.experience_duration * 100).toFixed(1)}%</td>
                        <td>${(item.breakdown.training * 100).toFixed(1)}%</td>
                        <td>${(item.breakdown.language * 100).toFixed(1)}%</td>
                        <td>${(item.breakdown.organization * 100).toFixed(1)}%</td>
                        <td>${(item.score * 100).toFixed(2)}%</td>
                    </tr>
                `;
                } catch (error) {
                    console.error("Error processing item:", error);
                }
            });
            $rankingResults.html(html || '<tr><td colspan="9" style="text-align: center">No valid data to display</td></tr>');
        }

        // Initialize total
        updateTotal();
    });
</script>