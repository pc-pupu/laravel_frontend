@extends('housingTheme.layouts.app')
@section('title', 'Edit Existing Occupant')
@section('page-header', 'Edit Existing Occupant')


@section('content')
<div class="cms-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="cms-card">
                <div class="cms-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3><i class="fa fa-edit me-2"></i> Edit Existing Occupant</h3>
                            <p>{{ $occupant['applicant_name'] ?? 'Update occupant information' }}</p>
                        </div>
                        <a href="{{ route('existing-occupant.index') }}" class="btn btn-light">
                            <i class="fa fa-arrow-left me-2"></i> Back to List
                        </a>
                    </div>
                </div>

                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                @if(isset($occupant))
                    <form method="POST" action="{{ route('existing-occupant.update', encrypt($occupant['online_application_id'] ?? '')) }}" id="editOccupantForm" novalidate>
                        @csrf
                        @method('PUT')

                        <div class="alert alert-info">
                            <i class="fa fa-info-circle me-2"></i>
                            <strong>Note:</strong> Edit form implementation will be completed based on Drupal existing_occupant_edit_form logic.
                        </div>

                        @include('housingTheme.existing-occupant._form', [
                            'flatDetails' => $flatDetails ?? [],
                            'metaData' => $metaData ?? [],
                            'occupant' => $occupant ?? []
                        ])

                        <div class="mt-4 d-flex justify-content-end gap-3">
                            <a href="{{ route('existing-occupant.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fa fa-save me-2"></i>Update Occupant
                            </button>
                        </div>
                    </form>
                @else
                    <div class="alert alert-warning">
                        Occupant data not found.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

