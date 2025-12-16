@extends('housingTheme.layouts.app')

@section('title', 'Add Flat Release Date')

@section('content')
<div class="cms-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="cms-card">
                <div class="cms-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3><i class="fa fa-calendar me-2"></i> Add Flat Release Date</h3>
                            <p class="mb-0">Enter flat release date for the application</p>
                        </div>
                        <a href="{{ route('application-status-check.view-list', ['id' => $id, 'status' => $status]) }}" class="btn btn-light">
                            <i class="fa fa-arrow-left me-2"></i> Back
                        </a>
                    </div>
                </div>

                @include('housingTheme.partials.alerts')

                <div class="cms-body">
                    <form action="{{ route('application-status-check.store-release', ['id' => $id, 'status' => $status]) }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" 
                                           class="form-control datepicker @error('add_release_date') is-invalid @enderror" 
                                           id="add_release_date" 
                                           name="add_release_date" 
                                           placeholder="Enter Flat Release Date" 
                                           value="{{ old('add_release_date') }}"
                                           required>
                                    <label for="add_release_date">Enter Flat Release Date</label>
                                    @error('add_release_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <button type="submit" class="btn bg-primary btn-sm px-5 mt-5 rounded-pill text-white fw-bolder w-100">
                                        <i class="fa fa-save me-2"></i> Save
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

@push('scripts')
<script src="{{ asset('assets/housingTheme/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('assets/housingTheme/jquery-ui/jquery-ui.min.js') }}"></script>
<script>
    $(function() {
        $("#add_release_date").datepicker({
            dateFormat: 'dd/mm/yy',
            changeMonth: true,
            changeYear: true,
            yearRange: '2020:2050'
        });
    });
</script>
@endpush

