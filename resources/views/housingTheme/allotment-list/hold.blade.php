@extends('housingTheme.layouts.app')

@section('title', 'List of Allottees on Hold')

@section('content')
<div class="cms-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="cms-card">
                <div class="cms-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3><i class="fa fa-pause me-2"></i> List of Allottees on Hold</h3>
                            <p class="mb-0">View all allottees whose allotments are on hold</p>
                        </div>
                        <a href="{{ route('dashboard') }}" class="btn btn-light">
                            <i class="fa fa-arrow-left me-2"></i> Back to Dashboard
                        </a>
                    </div>
                </div>

                @include('housingTheme.partials.alerts')

                @if(count($allottees) > 0)
                    <div class="table-responsive">
                        <table class="table table-list table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Application No.</th>
                                    <th>Date of Application</th>
                                    <th>Current Status</th>
                                    <th>Flat Allotted</th>
                                    <th>Floor</th>
                                    <th>Flat Type</th>
                                    <th>Block Name</th>
                                    <th>Name of R.H.E</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($allottees as $allottee)
                                    <tr>
                                        <td>{{ $allottee->applicant_name }}</td>
                                        <td>{{ $allottee->application_no }}</td>
                                        <td>{{ $allottee->date_of_application ? date('d/m/Y', strtotime($allottee->date_of_application)) : '-' }}</td>
                                        <td>{{ $allottee->status_description ?? $allottee->status }}</td>
                                        <td>{{ $allottee->flat_no }}</td>
                                        <td>{{ $allottee->floor }}</td>
                                        <td>
                                            @if(!empty($allottee->flat_type))
                                                {{ $allottee->flat_type }}
                                            @elseif(!empty($allottee->hft2_flat_type))
                                                {{ $allottee->hft2_flat_type }}
                                            @elseif(!empty($allottee->hft3_flat_type))
                                                {{ $allottee->hft3_flat_type }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $allottee->block_name }}</td>
                                        <td>{{ $allottee->estate_name }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle me-2"></i> No allottees found on hold.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

