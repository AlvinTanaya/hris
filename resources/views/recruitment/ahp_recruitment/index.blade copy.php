@extends('layouts.app')

@section('content')
<h1 class="text-center text-warning" style="margin-bottom: 65px; margin-top:25px">
    <i class="fas fa-calculator"></i> AHP Recommendation System
</h1>
<div class="container mx-auto">

    <!-- Criteria Comparison Form -->
    <div class="card mb-4 m-0">
        <div class="card-header text-white bg-primary">
            <h5>Criteria Pairwise Comparison</h5>
            <small class="text-white">Move the slider to the right if the left criterion is more important, to the left if the right criterion is more important</small>
        </div>
        <div class="card-body">
            <form id="ahpForm" onsubmit="return false;">
                @csrf
                <div class="mb-4">
                    <label for="demandId" class="form-label">Select Demand:</label>
                    <select name="demandId" id="demandId" class="form-select" required>
                        <option value="" disabled selected>-- Select Demand --</option>
                        @foreach($demands as $demand)
                        <option value="{{ $demand->id }}">{{ $demand->recruitment_demand_id }} - {{ $demand->position }} ({{ $demand->department }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="comparison-container">
                    <div class="mb-4">
                        <div class="row align-items-center">
                            <div class="col-4 text-end">Age</div>
                            <div class="col-4">
                                <input type="range" class="form-range" name="age_education" min="0.11" max="9" step="0.01" value="1">
                                <div class="text-center"><small>Importance Level</small></div>
                            </div>
                            <div class="col-4">Education Level</div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="row align-items-center">
                            <div class="col-4 text-end">Age</div>
                            <div class="col-4">
                                <input type="range" class="form-range" name="age_grade" min="0.11" max="9" step="0.01" value="1">
                                <div class="text-center"><small>Importance Level</small></div>
                            </div>
                            <div class="col-4">Education Score</div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="row align-items-center">
                            <div class="col-4 text-end">Age</div>
                            <div class="col-4">
                                <input type="range" class="form-range" name="age_experience" min="0.11" max="9" step="0.01" value="1">
                                <div class="text-center"><small>Importance Level</small></div>
                            </div>
                            <div class="col-4">Years of Experience</div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="row align-items-center">
                            <div class="col-4 text-end">Education Level</div>
                            <div class="col-4">
                                <input type="range" class="form-range" name="education_experience" min="0.11" max="9" step="0.01" value="1">
                                <div class="text-center"><small>Importance Level</small></div>
                            </div>
                            <div class="col-4">Years of Experience</div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="row align-items-center">
                            <div class="col-4 text-end">Years of Experience</div>
                            <div class="col-4">
                                <input type="range" class="form-range" name="experience_company" min="0.11" max="9" step="0.01" value="1">
                                <div class="text-center"><small>Importance Level</small></div>
                            </div>
                            <div class="col-4">Number of Companies</div>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <button type="button" id="calculateBtn" class="btn btn-primary">Calculate Ranking</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Criteria Weights Display -->
    <div class="card mb-4" id="criteriaWeightsCard" style="display: none;">
        <div class="card-header">
            <h5>Criteria Weights</h5>
        </div>
        <div class="card-body" id="criteriaWeightsBody">
        </div>
    </div>

    <!-- Ranking Results -->
    <div class="card">
        <div class="card-header">
            <h5>Applicant Ranking Results</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
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
                            <td colspan="8" class="text-center">Please fill out the comparison form above to see results</td>
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
        const rankingResults = document.getElementById('rankingResults');

        calculateBtn.addEventListener('click', function() {
            const demandSelect = document.getElementById('demandId');

            if (!demandSelect.value) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Choose Labor Demand First!',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                }).then(() => {
                    demandSelect.focus();
                });
                return;
            }



            // Show loading state
            calculateBtn.disabled = true;
            calculateBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...';

            // Get all form data
            const formData = new FormData(form);

            // Convert FormData to JSON object
            const jsonData = {};
            formData.forEach((value, key) => {
                jsonData[key] = value;
            });

            // Make AJAX request
            fetch('{{ route("ahp.calculate") }}', {
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
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log("Data received from server:", data);

                    if (data.success) {
                        let html = '';
                        console.log("Rankings data:", data.rankings);

                        data.rankings.forEach((item, index) => {
                            try {
                                console.log("Processing item:", index, item);

                                // Check if birth_date exists
                                if (!item.applicant || !item.applicant.birth_date) {
                                    console.error("Birth date is missing for item:", item);
                                    return; // Skip this item
                                }

                                const age = moment().diff(moment(item.applicant.birth_date), 'years');
                                console.log("Age calculated:", age);

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
                                console.error("Error processing item:", item, error);
                            }
                        });

                        if (html === '') {
                            console.error("No valid data to display");
                            rankingResults.innerHTML = '<tr><td colspan="8" class="text-center">Tidak ada data yang valid untuk ditampilkan</td></tr>';
                        } else {
                            rankingResults.innerHTML = html;
                        }
                    } else {
                        alert(data.message || 'Something Wrong With The AHP Calculation');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Server Error');
                })
                .finally(() => {
                    // Reset button state
                    calculateBtn.disabled = false;
                    calculateBtn.innerHTML = 'Calculate Rangking';
                });
        });
    });
</script>