@extends('layouts.app')

@section('content')


<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<style>
    #container-body {
        /* font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; */

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

    .comparison-container {
        background: #ecf0f1;
        padding: 2rem;
        border-radius: 10px;
        margin-bottom: 2rem;
    }

    .row {
        display: flex;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .col-4 {
        flex: 0 0 33.333333%;
        padding: 0 15px;
    }

    .text-end {
        text-align: right;
        font-weight: 600;
        color: #34495e;
    }

    .form-range {
        width: 100%;
        height: 8px;
        background: #ddd;
        border-radius: 4px;
        outline: none;
    }

    .form-range::-webkit-slider-thumb {
        appearance: none;
        width: 20px;
        height: 20px;
        background: #3498db;
        border-radius: 50%;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    .form-range::-webkit-slider-thumb:hover {
        background: #2c3e50;
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

    .weights-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }

    .weight-item {
        background: #ecf0f1;
        padding: 1rem;
        border-radius: 8px;
        text-align: center;
    }

    .weight-item h4 {
        color: #2c3e50;
        margin: 0 0 0.5rem 0;
    }

    .weight-value {
        font-size: 1.5rem;
        color: #3498db;
        font-weight: bold;
    }
</style>


<h1 class="page-title text-warning mb-5">
    <i class="fas fa-calculator"></i> AHP Recommendation System
</h1>
<div class="container mb-4 p-0 mx-auto" id="container-body">
    <!-- Criteria Comparison Form -->
    <div class="card">
        <div class="card-header">
            <h5>Criteria Pairwise Comparison</h5>
            <small>Move the slider to the right if the left criterion is more important, to the left if the right criterion is more important</small>
        </div>
        <div class="card-body">
            <form id="ahpForm" onsubmit="return false;">
                @csrf
                <div>
                    <label for="demandId">Select Demand:</label>
                    <select name="demandId" id="demandId" class="form-select" required>
                        <option value="" disabled selected>-- Select Demand --</option>
                        @foreach($demands as $demand)
                        <option value="{{ $demand->id }}">{{ $demand->recruitment_demand_id }} - {{ $demand->position }} ({{ $demand->department }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="comparison-container">
                    <div class="row">
                        <div class="col-4 text-end">Age</div>
                        <div class="col-4">
                            <input type="range" class="form-range" name="age_education" min="0.11" max="9" step="0.01" value="1">
                            <div style="text-align: center" class="mt-2"><small>Importance Level</small></div>
                        </div>
                        <div class="col-4">Education Level</div>
                    </div>

                    <div class="row">
                        <div class="col-4 text-end">Age</div>
                        <div class="col-4">
                            <input type="range" class="form-range" name="age_grade" min="0.11" max="9" step="0.01" value="1">
                            <div style="text-align: center" class="mt-2"><small>Importance Level</small></div>
                        </div>
                        <div class="col-4">Education Score</div>
                    </div>

                    <div class="row">
                        <div class="col-4 text-end">Age</div>
                        <div class="col-4">
                            <input type="range" class="form-range" name="age_experience" min="0.11" max="9" step="0.01" value="1">
                            <div style="text-align: center" class="mt-2"><small>Importance Level</small></div>
                        </div>
                        <div class="col-4">Years of Experience</div>
                    </div>

                    <div class="row">
                        <div class="col-4 text-end">Education Level</div>
                        <div class="col-4">
                            <input type="range" class="form-range" name="education_experience" min="0.11" max="9" step="0.01" value="1">
                            <div style="text-align: center" class="mt-2"><small>Importance Level</small></div>
                        </div>
                        <div class="col-4">Years of Experience</div>
                    </div>

                    <div class="row">
                        <div class="col-4 text-end">Years of Experience</div>
                        <div class="col-4">
                            <input type="range" class="form-range" name="experience_company" min="0.11" max="9" step="0.01" value="1">
                            <div style="text-align: center" class="mt-2"><small>Importance Level</small></div>
                        </div>
                        <div class="col-4">Number of Companies</div>
                    </div>
                </div>

                <div style="text-align: center">
                    <button type="button" id="calculateBtn" class="btn-primary">Calculate Ranking</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Criteria Weights Display -->
    <div class="card" id="criteriaWeightsCard" style="display: none;">
        <div class="card-header">
            <h5>Criteria Weights Results</h5>
        </div>
        <div class="card-body">
            <div class="weights-grid" id="weightsGrid">
                <!-- Weights will be displayed here -->
            </div>
            <div style="text-align: center; margin-top: 2rem;">
                <button type="button" id="showRankingsBtn" class="btn-success">Show Applicant Rankings</button>
            </div>
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
                            <th>Education Level</th>
                            <th>Score</th>
                            <th>Years of Experience</th>
                            <th>Number of Companies</th>
                            <th>Total Score</th>
                        </tr>
                    </thead>
                    <tbody id="rankingResults">
                        <tr>
                            <td colspan="8" style="text-align: center">Please fill out the comparison form above to see results</td>
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('ahpForm');
        const calculateBtn = document.getElementById('calculateBtn');
        const showRankingsBtn = document.getElementById('showRankingsBtn');
        const rankingResults = document.getElementById('rankingResults');
        const criteriaWeightsCard = document.getElementById('criteriaWeightsCard');
        const rankingResultsCard = document.getElementById('rankingResultsCard');
        let savedWeights = null;

        calculateBtn.addEventListener('click', function() {
            const demandSelect = document.getElementById('demandId');

            if (!demandSelect.value) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Choose Labor Demand First!',
                    confirmButtonColor: '#3498db',
                    confirmButtonText: 'OK'
                }).then(() => {
                    demandSelect.focus();
                });
                return;
            }

            calculateBtn.disabled = true;
            calculateBtn.innerHTML = '<span class="spinner-border" role="status" aria-hidden="true"></span> Loading...';

            const formData = new FormData(form);
            const jsonData = {};
            formData.forEach((value, key) => {
                jsonData[key] = value;
            });

            fetch('/ahp/calculate-weights', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(jsonData)
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        savedWeights = data.weights;
                        displayWeights(data.weights);
                        criteriaWeightsCard.style.display = 'block';
                        rankingResultsCard.style.display = 'none';
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                // .catch(error => {
                //     console.error('Error:', error);
                //     Swal.fire('Error', 'Server Error', 'error');
                // })
                .finally(() => {
                    calculateBtn.disabled = false;
                    calculateBtn.innerHTML = 'Calculate Weights';
                });

        });

        showRankingsBtn.addEventListener('click', function() {
            if (!savedWeights) {
                Swal.fire('Error', 'Please calculate weights first', 'error');
                return;
            }

            showRankingsBtn.disabled = true;
            showRankingsBtn.innerHTML = '<span class="spinner-border" role="status" aria-hidden="true"></span> Loading...';

            const formData = new FormData(form);
            const jsonData = {
                demandId: formData.get('demandId'),
                weights: savedWeights
            };

            fetch('/ahp/calculate-rankings', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(jsonData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayRankings(data.rankings);
                        rankingResultsCard.style.display = 'block';
                    } else {

                        Swal.fire('Error', data.message, 'error');
                    }
                })
                // .catch(error => {
                //     console.error('Error:', error);
                //     Swal.fire('Error', 'Server Error', 'error');
                // })
                .finally(() => {
                    showRankingsBtn.disabled = false;
                    showRankingsBtn.innerHTML = 'Show Applicant Rankings';
                });
        });

        function displayWeights(weights) {
            const weightsGrid = document.getElementById('weightsGrid');
            weightsGrid.innerHTML = '';

            const weightLabels = {
                age: 'Age',
                education: 'Education Level',
                grade: 'Education Score',
                experience: 'Years of Experience',
                company: 'Number of Companies'
            };

            Object.entries(weights).forEach(([key, value]) => {
                const weightItem = document.createElement('div');
                weightItem.className = 'weight-item';
                weightItem.innerHTML = `
                        <h4>${(weightLabels[key] || key).replace(/_/g, ' ').toUpperCase()}</h4>

                        <div class="weight-value">${(value * 100).toFixed(2)}%</div>
                    `;
                weightsGrid.appendChild(weightItem);
            });
        }

        function displayRankings(rankings) {
            let html = '';
            rankings.forEach((item, index) => {
                try {
                    if (!item.applicant || !item.applicant.birth_date) return;
                    const age = moment().diff(moment(item.applicant.birth_date), 'years');
                    html += `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${item.applicant.name}</td>
                                <td>${age} Year</td>
                                <td>${(item.breakdown.education_level * 100).toFixed(1)}%</td>
                                <td>${(item.breakdown.education_grade * 100).toFixed(1)}%</td>
                                <td>${(item.breakdown.experience_duration * 100).toFixed(1)}%</td>
                                <td>${(item.breakdown.company_count * 100).toFixed(1)}%</td>
                                <td>${(item.score * 100).toFixed(2)}%</td>
                            </tr>
                        `;
                } catch (error) {
                    console.error("Error processing item:", error);
                }
            });
            rankingResults.innerHTML = html || '<tr><td colspan="8" style="text-align: center">No valid data to display</td></tr>';
        }
    });
</script>