@extends('housingTheme.layouts.app')

@section('title', 'Allotment Detail')

@section('content')
<div class="cms-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="cms-card">
                <div class="cms-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3><i class="fa fa-info-circle me-2"></i> Allotment Information</h3>
                            <p class="mb-0">View detailed allotment information</p>
                        </div>
                        <a href="{{ route('allotment-list.index') }}" class="btn btn-light">
                            <i class="fa fa-arrow-left me-2"></i> Back to List
                        </a>
                    </div>
                </div>

                @if($allotment)
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th style="background-color: #415b92; color: white; width: 30%;">Allottee Name</th>
                                <td style="background-color: #e1f5fb;">{{ $allotment->applicant_name }}</td>
                            </tr>
                            <tr>
                                <th style="background-color: #415b92; color: white;">Allotment No.</th>
                                <td style="background-color: #e1f5fb;">{{ $allotment->allotment_no }}</td>
                            </tr>
                            <tr>
                                <th style="background-color: #415b92; color: white;">Allotment Category</th>
                                <td style="background-color: #e1f5fb;">{{ $allotment->allotment_category ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color: #415b92; color: white;">Allotment Date</th>
                                <td style="background-color: #e1f5fb;">
                                    {{ $allotment->allotment_date ? date('d/m/Y', strtotime($allotment->allotment_date)) : '-' }}
                                </td>
                            </tr>
                            <tr>
                                <th style="background-color: #415b92; color: white;">Flat Type</th>
                                <td style="background-color: #e1f5fb;">{{ $allotment->flat_type }}</td>
                            </tr>
                            <tr>
                                <th style="background-color: #415b92; color: white;">District Name</th>
                                <td style="background-color: #e1f5fb;">{{ $allotment->district_name }}</td>
                            </tr>
                            <tr>
                                <th style="background-color: #415b92; color: white;">Estate Name</th>
                                <td style="background-color: #e1f5fb;">{{ $allotment->estate_name }}</td>
                            </tr>
                            <tr>
                                <th style="background-color: #415b92; color: white;">Estate Address</th>
                                <td style="background-color: #e1f5fb;">{{ $allotment->estate_address }}</td>
                            </tr>
                            <tr>
                                <th style="background-color: #415b92; color: white;">Flat No.</th>
                                <td style="background-color: #e1f5fb;">{{ $allotment->flat_no }}</td>
                            </tr>
                        </table>
                    </div>
                @else
                    <div class="alert alert-danger">
                        <i class="fa fa-exclamation-circle me-2"></i> Allotment not found.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

