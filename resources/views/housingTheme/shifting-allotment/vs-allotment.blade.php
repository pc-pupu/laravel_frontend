@extends('housingTheme.layouts.app')

@section('title', 'RHE Vertical Shifting Allotment')

@section('content')
<div class="cms-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="cms-card">
                <div class="cms-header">
                    <h3><i class="fa fa-key me-2"></i> RHE Vertical Shifting Allotment</h3>
                </div>
                @include('housingTheme.partials.alerts')
                
                <form method="GET" action="{{ route('shifting-allotment.vs') }}" id="vs-allotment-form">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="form-floating">
                                <select name="rhe_id" id="rhe_id" class="form-select" required>
                                    @foreach($rhes as $id => $name)
                                        <option value="{{ $id }}" {{ (int)$selectedRheId === (int)$id ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="rhe_id">Select RHE Name <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating">
                                <button type="button" class="btn bg-primary btn-sm px-5 rounded-pill text-white fw-bolder" 
                                    style="height: 58px;" onclick="loadCounts()">
                                    <i class="fa fa-refresh me-2"></i> Refresh Counts
                                </button>
                                <label>&nbsp;</label>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h5 class="card-title">Total no of Vacancy</h5>
                                <p class="card-text fs-3 fw-bold" id="vacancy_count">{{ $vacancyCount }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h5 class="card-title">Total no of Applicant</h5>
                                <p class="card-text fs-3 fw-bold" id="applicant_count">{{ $applicantCount }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('shifting-allotment.vs.process') }}" 
                    id="process-vs-allotment-form" 
                    onsubmit="return validateShiftingAllotmentForm()">
                    @csrf
                    <input type="hidden" name="rhe_id" id="process_rhe_id" value="{{ $selectedRheId }}">
                    
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <button type="submit" class="btn bg-primary btn-sm px-5 rounded-pill text-white fw-bolder"
                                {{ $selectedRheId == 0 || $vacancyCount == 0 || $applicantCount == 0 ? 'disabled' : '' }}>
                                <i class="fa fa-key me-2"></i> Click For Vertical Shifting Allotment
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function loadCounts() {
    const rheId = document.getElementById('rhe_id').value;
    
    if (rheId == 0 || rheId == '') {
        alert('Please select RHE first');
        return;
    }

    // Update hidden input
    document.getElementById('process_rhe_id').value = rheId;

    // Fetch counts via AJAX
    fetch(`{{ route('shifting-allotment.vs.counts') }}?rhe_id=${rheId}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            document.getElementById('vacancy_count').textContent = data.data.vacancy_count;
            document.getElementById('applicant_count').textContent = data.data.applicant_count;
            
            // Enable/disable submit button
            const submitBtn = document.querySelector('#process-vs-allotment-form button[type="submit"]');
            if (data.data.vacancy_count > 0 && data.data.applicant_count > 0) {
                submitBtn.disabled = false;
            } else {
                submitBtn.disabled = true;
            }
        } else {
            alert('Failed to fetch counts');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error fetching counts');
    });
}

function validateShiftingAllotmentForm() {
    const rheId = document.getElementById('rhe_id').value;
    const vacancyCount = parseInt(document.getElementById('vacancy_count').textContent);
    const applicantCount = parseInt(document.getElementById('applicant_count').textContent);

    if (rheId == 0 || rheId == '') {
        alert('Please select RHE first');
        return false;
    }

    if (vacancyCount <= 0 || applicantCount <= 0) {
        alert('No. of vacancy or No. of Applicant or both are Zero, Allotment not possible!!!');
        return false;
    }

    return confirm('Are you sure you want to process Vertical Shifting Allotment?');
}

// Auto-load counts when RHE is selected
document.getElementById('rhe_id').addEventListener('change', function() {
    if (this.value != 0 && this.value != '') {
        loadCounts();
    }
});

// Load counts on page load if RHE is selected
@if($selectedRheId > 0)
    loadCounts();
@endif
</script>
@endsection
