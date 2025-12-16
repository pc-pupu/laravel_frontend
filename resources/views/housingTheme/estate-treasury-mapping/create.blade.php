@extends('housingTheme.layouts.app')
@section('title', 'Add Estate Treasury Mapping')
@section('page-header', 'Add Estate Treasury Mapping')

@section('content')
<div class="cms-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="cms-card">
                <div class="cms-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3><i class="fa fa-plus-circle me-2"></i> Add New Estate Treasury Mapping</h3>
                            <p>Create new estate to treasury mapping</p>
                        </div>
                        <a href="{{ route('estate-treasury-selection.index') }}" class="btn btn-light">
                            <i class="fa fa-arrow-left me-2"></i> Back to List
                        </a>
                    </div>
                </div>

                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('estate-treasury-selection.store') }}" id="estateTreasuryMappingForm">
                    @csrf
                    
                    @include('housingTheme.estate-treasury-mapping.form', [
                        'mapping' => [],
                        'estates' => $estates ?? [],
                        'treasuries' => $treasuries ?? []
                    ])

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <br><br>
                                <button type="submit" class="btn bg-primary btn-sm px-5 rounded-pill text-white fw-bolder">
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
@endsection

@push('scripts')
<script src="{{ asset('/assets/housingTheme/jquery/jquery.min.js') }}"></script>
<script>
    $(document).ready(function() {
        // Form validation
        $('#estateTreasuryMappingForm').on('submit', function(e) {
            const estateId = $('#estate_dropdown').val();
            const treasuryId = $('#treasury_dropdown').val();
            const isActive = $('input[name="is_active"]:checked').val();

            if (!estateId) {
                alert('Please select a housing estate.');
                e.preventDefault();
                return false;
            }

            if (!treasuryId) {
                alert('Please select a treasury.');
                e.preventDefault();
                return false;
            }

            if (typeof isActive === 'undefined') {
                alert('Please select activation status.');
                e.preventDefault();
                return false;
            }

            return true;
        });
    });
</script>
@endpush

