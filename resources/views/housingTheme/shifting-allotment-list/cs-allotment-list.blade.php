@extends('housingTheme.layouts.app')

@section('title', 'List of Category Shifting Allottees')

@section('content')
<div class="cms-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="cms-card">
                <div class="cms-header">
                    <h3><i class="fa fa-list me-2"></i> List of Category Shifting Allottees</h3>
                </div>
                @include('housingTheme.partials.alerts')
                
                <form method="GET" action="{{ route('shifting-allotment-list.cs') }}" id="cs-allotment-list-form">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="form-floating">
                                <select name="cs_allotment_process_date" id="cs_allotment_process_date" class="form-select" required>
                                    @foreach($processDates as $value => $label)
                                        <option value="{{ $value }}" {{ $selectedDate == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="cs_allotment_process_date">CS Allotment Process Date <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-md-4" id="cs_allotment_process_no_replace">
                            <div class="form-floating">
                                <select name="cs_allotment_process_no" id="cs_allotment_process_no" class="form-select" 
                                    {{ !$selectedDate ? 'disabled' : '' }} required>
                                    @foreach($processNumbers as $value => $label)
                                        <option value="{{ $value }}" {{ $selectedProcessNo == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="cs_allotment_process_no">CS Allotment Process No. <span class="text-danger">*</span></label>
                            </div>
                        </div>
                    </div>
                </form>

                <div id="cs_allotment_list_replace">
                    @if(!empty($allottees) && count($allottees) > 0)
                        <div class="mb-3">
                            <a href="{{ route('shifting-allotment-list.cs.pdf', [
                                'date' => \App\Helpers\UrlEncryptionHelper::encryptUrl($selectedDate),
                                'process_no' => \App\Helpers\UrlEncryptionHelper::encryptUrl($selectedProcessNo),
                                'filename' => 'cs_allottee_list_' . time()
                            ]) }}" 
                            target="_blank" 
                            class="btn bg-primary btn-sm px-5 rounded-pill text-white fw-bolder mb-2">
                                <i class="fa fa-download me-2"></i> Download CS Allottee List PDF
                            </a>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-list table-striped table-hover table-bordered">
                                <thead>
                                    <tr class="table-primary">
                                        <th>Sl. No.</th>
                                        <th>Flat Type</th>
                                        <th>Date of Application</th>
                                        <th>Application No</th>
                                        <th>Date of Allotment</th>
                                        <th>Allotment No.</th>
                                        <th>View Allotment Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($allottees as $index => $allottee)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $allottee['flat_type'] ?? 'N/A' }}</td>
                                            <td>{{ !empty($allottee['date_of_application']) ? \Carbon\Carbon::parse($allottee['date_of_application'])->format('d/m/Y') : 'N/A' }}</td>
                                            <td>{{ $allottee['application_no'] ?? 'N/A' }}</td>
                                            <td>{{ !empty($allottee['allotment_date']) ? \Carbon\Carbon::parse($allottee['allotment_date'])->format('d/m/Y') : 'N/A' }}</td>
                                            <td>{{ $allottee['allotment_no'] ?? 'N/A' }}</td>
                                            <td>
                                                @if(!empty($allottee['encrypted_online_application_id']))
                                                    <a href="{{ route('view-shifting-allotment-details.cs', ['id' => $allottee['encrypted_online_application_id']]) }}" 
                                                       class="btn btn-outline-primary btn-sm px-4 rounded-pill fw-bolder"
                                                       target="_blank">
                                                        <i class="fa fa-eye me-2"></i> View Details
                                                    </a>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @elseif($selectedDate && $selectedProcessNo)
                        <div class="alert alert-info text-center">
                            No allottees found for the selected criteria.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('cs_allotment_process_date').addEventListener('change', function() {
    const selectedDate = this.value;
    const processNoSelect = document.getElementById('cs_allotment_process_no');
    
    if (!selectedDate) {
        processNoSelect.innerHTML = '<option value="">Select CS Allotment Process No.</option>';
        processNoSelect.disabled = true;
        document.getElementById('cs_allotment_list_replace').innerHTML = '';
        return;
    }

    // Fetch process numbers via AJAX
    fetch(`{{ route('shifting-allotment-list.cs.process-numbers') }}?allotment_process_date=${selectedDate}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            let options = '<option value="">Select CS Allotment Process No.</option>';
            data.data.forEach(item => {
                options += `<option value="${item.value}">${item.label}</option>`;
            });
            processNoSelect.innerHTML = options;
            processNoSelect.disabled = false;
            
            // Clear allottee list
            document.getElementById('cs_allotment_list_replace').innerHTML = '';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error fetching process numbers');
    });
});

document.getElementById('cs_allotment_process_no').addEventListener('change', function() {
    const selectedDate = document.getElementById('cs_allotment_process_date').value;
    const selectedProcessNo = this.value;
    
    if (!selectedDate || !selectedProcessNo) {
        document.getElementById('cs_allotment_list_replace').innerHTML = '';
        return;
    }

    // Fetch allottees via AJAX
    fetch(`{{ route('shifting-allotment-list.cs.allottees') }}?allotment_process_date=${selectedDate}&allotment_process_no=${selectedProcessNo}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            const allottees = data.data;
            let html = '';
            
            if (allottees.length > 0) {
                html += `<div class="mb-3">
                    <a href="{{ url('cs_allottee_list_pdf') }}/` + 
                    `{{ \App\Helpers\UrlEncryptionHelper::encryptUrl($selectedDate) }}/` +
                    `{{ \App\Helpers\UrlEncryptionHelper::encryptUrl($selectedProcessNo) }}/` +
                    `cs_allottee_list_${Date.now()}" 
                    target="_blank" 
                    class="btn bg-primary btn-sm px-5 rounded-pill text-white fw-bolder mb-2">
                        <i class="fa fa-download me-2"></i> Download CS Allottee List PDF
                    </a>
                </div>`;
                
                html += '<div class="table-responsive"><table class="table table-list table-striped table-hover table-bordered"><thead><tr class="table-primary"><th>Sl. No.</th><th>Flat Type</th><th>Date of Application</th><th>Application No</th><th>Date of Allotment</th><th>Allotment No.</th><th>View Allotment Details</th></tr></thead><tbody>';
                
                allottees.forEach((allottee, index) => {
                    const appDate = allottee.date_of_application ? new Date(allottee.date_of_application).toLocaleDateString('en-GB') : 'N/A';
                    const allotDate = allottee.allotment_date ? new Date(allottee.allotment_date).toLocaleDateString('en-GB') : 'N/A';
                    const viewLink = allottee.encrypted_online_application_id 
                        ? `<a href="{{ url('view_cs_allotment_details') }}/${allottee.encrypted_online_application_id}" class="btn btn-outline-primary btn-sm px-4 rounded-pill fw-bolder" target="_blank"><i class="fa fa-eye me-2"></i> View Details</a>`
                        : 'N/A';
                    
                    html += `<tr>
                        <td>${index + 1}</td>
                        <td>${allottee.flat_type || 'N/A'}</td>
                        <td>${appDate}</td>
                        <td>${allottee.application_no || 'N/A'}</td>
                        <td>${allotDate}</td>
                        <td>${allottee.allotment_no || 'N/A'}</td>
                        <td>${viewLink}</td>
                    </tr>`;
                });
                
                html += '</tbody></table></div>';
            } else {
                html = '<div class="alert alert-info text-center">No allottees found for the selected criteria.</div>';
            }
            
            document.getElementById('cs_allotment_list_replace').innerHTML = html;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error fetching allottees');
    });
});
</script>
@endsection
