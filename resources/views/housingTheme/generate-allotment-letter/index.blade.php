@extends('housingTheme.layouts.app')

@section('title', 'Generate Allotment Letter')

@section('content')
<div class="cms-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="cms-card">
                <div class="cms-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3><i class="fa fa-file-text me-2"></i> Generate Allotment Letter</h3>
                            <p class="mb-0">Generate allotment letters for applicants based on waiting list</p>
                        </div>
                        <a href="{{ route('dashboard') }}" class="btn btn-light">
                            <i class="fa fa-arrow-left me-2"></i> Back to Dashboard
                        </a>
                    </div>
                </div>

                @include('housingTheme.partials.alerts')

                <form method="GET" action="{{ route('generate-allotment-letter.index') }}" id="allotment-letter-form">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="form-floating">
                                <select name="flat_type_id" id="flat_type" class="form-select" required>
                                    <option value="">Select RHE Flat Type</option>
                                    @foreach($flatTypes as $flatType)
                                        <option value="{{ $flatType['value'] }}" {{ old('flat_type_id', $flatTypeId) == $flatType['value'] ? 'selected' : '' }}>
                                            {{ $flatType['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="flat_type">RHE Flat Type</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary" style="margin-top: 43px; height: 58px;">
                                <i class="fa fa-search me-2"></i> Search
                            </button>
                        </div>
                    </div>
                </form>

                @if($flatTypeId && count($waitingList) > 0)
                    @php
                        // Get flat type name to determine if grade pay column is needed
                        $showGradePay = in_array($flatTypeName, ['A', 'B']);
                    @endphp

                    <div class="table-responsive">
                        <table class="table table-list table-striped">
                            <thead>
                                <tr>
                                    <th>Applicant Details</th>
                                    <th>Roaster Counter</th>
                                    @if($showGradePay)
                                        <th>Grade Pay</th>
                                    @endif
                                    <th>Application Date</th>
                                    <th>Flat Type Wise Waiting No</th>
                                    <th>Offer</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($waitingList as $application)
                                    @if(!$application['flat_offer'])
                                        @continue
                                    @endif
                                    <tr>
                                        <td>{!! $application['applicant'] !!}</td>
                                        <td>{!! $application['roaster_counter'] !!}</td>
                                        @if($showGradePay)
                                            <td>{!! $application['grade_pay'] ?? '-' !!}</td>
                                        @endif
                                        <td>{!! $application['application_date'] !!}</td>
                                        <td>{!! $application['waiting_no'] !!}</td>
                                        <td>
                                            {!! $application['offer'] !!}
                                            @if(isset($application['waiting_no_numeric']) && $application['waiting_no_numeric'] == 1)
                                                <br>
                                                <form method="POST" action="{{ route('generate-allotment-letter.generate') }}" style="display: inline;">
                                                    @csrf
                                                    <input type="hidden" name="flat_id" value="{{ $application['flat_id'] }}">
                                                    <input type="hidden" name="online_application_id" value="{{ $application['online_application_id'] }}">
                                                    <input type="hidden" name="flat_type" value="{{ $flatTypeName }}">
                                                    <input type="hidden" name="roaster_counter" value="{{ $application['rc'] }}">
                                                    <input type="hidden" name="list_no" value="{{ $application['ln'] }}">
                                                    <input type="hidden" name="flat_type_id" value="{{ $flatTypeId }}">
                                                    <button type="submit" 
                                                            class="btn btn-link" 
                                                            style="color: #0090C7; font-weight: 400; text-decoration: underline; padding: 0; border: none; background: none;"
                                                            onclick="return confirm('Are you sure you want to generate the allotment letter?')">
                                                        Generate Allotment Letter
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @elseif($flatTypeId && count($waitingList) == 0)
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle me-2"></i> No waiting list found for the selected flat type.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#flat_type').on('change', function() {
        // Auto-submit form when flat type is selected
        // $('#allotment-letter-form').submit();
    });
});
</script>
@endpush
@endsection

