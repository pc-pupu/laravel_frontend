@extends('housingTheme.layouts.app')

@section('title', 'Application Details Check')

@section('content')
<div class="cms-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="cms-card">
                <div class="cms-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3><i class="fa fa-search me-2"></i> Application Details Check</h3>
                            <p class="mb-0">Search application by application number or HRMS ID</p>
                        </div>
                        <a href="{{ route('dashboard') }}" class="btn btn-light">
                            <i class="fa fa-arrow-left me-2"></i> Back to Dashboard
                        </a>
                    </div>
                </div>

                @include('housingTheme.partials.alerts')

                <div class="cms-body">
                    <form action="{{ route('application-status-check.search') }}" method="POST" id="application-status-check-form">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Select the option via you want to search application details:</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="select_button" id="search_app_no" value="1" checked>
                                <label class="form-check-label" for="search_app_no">
                                    Application Number
                                </label>
                            </div>
                            @if(isset($user) && ($user['role'] ?? null) == 6)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="select_button" id="search_hrms" value="2">
                                <label class="form-check-label" for="search_hrms">
                                    HRMS Id
                                </label>
                            </div>
                            @endif
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-floating">
                                    <input type="text" 
                                           class="form-control form-control-sm @error('application_or_hrms_no') is-invalid @enderror" 
                                           id="application_or_hrms_no" 
                                           name="application_or_hrms_no" 
                                           placeholder="Enter Details" 
                                           value="{{ old('application_or_hrms_no') }}"
                                           required>
                                    <label for="application_or_hrms_no">
                                        @if($userRole == 6)
                                            Enter Application Number or HRMS Id:
                                        @else
                                            Enter Application Number:
                                        @endif
                                    </label>
                                    @error('application_or_hrms_no')
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
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

