@extends('housingTheme.layouts.app')
@section('title', 'RHE Allotment')
@section('page-header', 'RHE Allotment')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="fa fa-key"></i> RHE Allotment
                        </h4>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fa fa-check-circle"></i> {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('rhe-allotment.show-vacancy') }}" id="rheAllotmentForm">
                            @csrf
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" id="allotment_type" name="allotment_type" required>
                                            <option value="">-- Select Allotment Type --</option>
                                            @foreach($flatTypes as $flatType)
                                                <option value="{{ $flatType['value'] }}" 
                                                    {{ old('allotment_type', $allotmentType) == $flatType['value'] ? 'selected' : '' }}>
                                                    {{ $flatType['label'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <label for="allotment_type">Select Allotment Type <span class="text-danger">*</span></label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <input type="hidden" name="district_code" value="{{ $districtCode }}">

                                    <div class="form-floating">
                                        <button type="submit" class="btn bg-primary btn-sm px-5 rounded-pill text-white fw-bolder w-50"
                                            style="height: 58px;">
                                            <i class="fa fa-search"></i> Show Vacancy
                                        </button>
                                        <label>&nbsp;</label>
                                    </div>
                                </div>
                                
                            </div>
                        </form>

                        @if($reportData && isset($reportData['report_data']))
                            <form method="POST" action="{{ route('rhe-allotment.process') }}" id="processAllotmentForm" 
                                onsubmit="return confirm('Are you sure you want to run Allotment process?');">
                                @csrf
                                <input type="hidden" name="allotment_type" value="{{ $allotmentType }}">
                                <input type="hidden" name="district_code" value="{{ $districtCode }}">

                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <button type="submit" class="btn bg-danger btn-sm px-5 rounded-pill text-white mb-4 fw-bolder">
                                            <i class="fa fa-key"></i> Allot Flat
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <div class="printable" id="divToPrint" style="width:100% !important;">
                                <table class="allotment table table-list table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th rowspan="2" class="align-middle">Housing</th>
                                            <th colspan="10" class="text-center">No. of Vacancy</th>
                                            <th colspan="3" class="text-center">No. of Applicant</th>
                                        </tr>
                                        <tr>
                                            <th>Floor-0</th>
                                            <th>Floor-1</th>
                                            <th>Floor-2</th>
                                            <th>Floor-3</th>
                                            <th>Floor-4</th>
                                            <th>Floor-5</th>
                                            <th>Floor-6</th>
                                            <th>Floor-7</th>
                                            <th>Floor-8</th>
                                            <th>Floor-9</th>
                                            <th>Floor-Top</th>
                                            <th>Floor shifting</th>
                                            <th>Category shifting</th>
                                            <th>New/ Fresh</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($reportData['report_data'] as $index => $row)
                                            <tr>
                                                <td>{{ $row['estate_name'] }}</td>
                                                <td>{{ $row['floor_0'] }}</td>
                                                <td>{{ $row['floor_1'] }}</td>
                                                <td>{{ $row['floor_2'] }}</td>
                                                <td>{{ $row['floor_3'] }}</td>
                                                <td>{{ $row['floor_4'] }}</td>
                                                <td>{{ $row['floor_5'] }}</td>
                                                <td>{{ $row['floor_6'] }}</td>
                                                <td>{{ $row['floor_7'] }}</td>
                                                <td>{{ $row['floor_8'] }}</td>
                                                <td>{{ $row['floor_9'] }}</td>
                                                <td>{{ $row['floor_top'] }}</td>
                                                <td>{{ $row['applicant_vs'] }}</td>
                                                <td>{{ $row['applicant_cs'] }}</td>
                                                @if($row['applicant_new'] !== null)
                                                    <td rowspan="{{ $row['rowspan'] }}" class="align-middle">
                                                        {{ $row['applicant_new'] }}
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .allotment.table {
        font-size: 0.9rem;
    }
    .allotment.table th {
        background-color: #f8f9fa;
        font-weight: 600;
        text-align: center;
        vertical-align: middle;
    }
    .allotment.table td {
        text-align: center;
        vertical-align: middle;
    }
    .printable {
        overflow-x: auto;
    }
</style>
@endpush

@push('scripts')
<script>
    function validate_rhe_allotment_form() {
        var allotmentType = document.getElementById('allotment_type').value;
        
        if (!allotmentType || allotmentType === '') {
            alert('Please select Allotment Type');
            return false;
        }
        
        return true;
    }

    // Add validation to form
    document.getElementById('rheAllotmentForm')?.addEventListener('submit', function(e) {
        if (!validate_rhe_allotment_form()) {
            e.preventDefault();
            return false;
        }
    });
</script>
@endpush

