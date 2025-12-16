@extends('housingTheme.layouts.app')

@section('title', 'Check Application Status')

@section('content')
<div class="cms-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="cms-card">
                <div class="cms-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3><i class="fa fa-search me-2"></i> Check Application Status</h3>
                            <p class="mb-0">Enter your application number to check status</p>
                        </div>
                        <a href="{{ route('dashboard') }}" class="btn btn-light">
                            <i class="fa fa-arrow-left me-2"></i> Back to Dashboard
                        </a>
                    </div>
                </div>

                @include('housingTheme.partials.alerts')

                <div class="cms-body">
                    <form action="{{ route('application-status.index') }}" method="GET" id="application-status-form" onsubmit="return validateApplicationStatusForm()">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-floating">
                                    <input type="text" 
                                           class="form-control form-control-sm @error('application_no') is-invalid @enderror" 
                                           id="application_no" 
                                           name="application_no" 
                                           placeholder="Enter application no." 
                                           value="{{ $applicationNo ?? old('application_no') }}"
                                           required>
                                    <label for="application_no">Enter Application No.</label>
                                    @error('application_no')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <button type="submit" class="btn bg-primary btn-sm px-5 mt-5 rounded-pill text-white fw-bolder w-100">
                                        <i class="fa fa-search me-2"></i> Search
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    @if(isset($statusHistory) && count($statusHistory) > 0)
                        <div class="table-responsive mt-4">
                            <table class="table table-list table-striped">
                                <thead>
                                    <tr>
                                        <th class="text-center">Status</th>
                                        <th class="text-center" width="40%">Verification Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($statusHistory as $status)
                                        <tr>
                                            <td class="text-center">{{ $status['status_description'] ?? $status['short_code'] ?? '-' }}</td>
                                            <td class="text-center">
                                                @if(isset($status['created_at']))
                                                    {{ date('d/m/Y H:i:s', strtotime($status['created_at'])) }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @elseif(isset($applicationNo) && $applicationNo)
                        <div class="alert alert-info mt-4">
                            <i class="fa fa-info-circle me-2"></i> No status history found for this application number.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function validateApplicationStatusForm() {
        const applicationNo = document.getElementById('application_no').value.trim();
        
        // Validate application number format: XX-XXX-XXXXXXXX-XX
        const pattern = /^[A-Z]{2}-[A-Z]{3}-\d{8}-\d+$/;
        
        if (applicationNo && !pattern.test(applicationNo)) {
            alert('Invalid Application Number. Enter Proper Application Number');
            return false;
        }
        
        return true;
    }
</script>
@endpush

