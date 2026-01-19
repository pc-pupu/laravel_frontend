@extends('housingTheme.layouts.app')

@section('title', 'Declaration Before Competent Authority')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fa fa-file-alt me-2"></i> Declaration Before Competent Authority
                    </h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('view-allotment-details.submit-declaration', ['encrypted_app_id' => $encrypted_app_id]) }}" method="POST">
                        @csrf

                        <div class="declaration-content mb-4">
                            <h3 class="text-center mb-4" style="font-size:20px"><b>DECLARATION BEFORE COMPETENT AUTHORITY</b></h3>
                            
                            @php
                                $hrms = $hrms_data ?? [];
                                // echo $hrms['applicantName'];
                                // print_r($hrms);die;
                                $applicantName = $hrms['applicantName'] ?? 'N/A';
                                $guardianName = $hrms['guardianName'] ?? 'N/A';
                                $permanentStreet = $hrms['permanentStreet'] ?? 'N/A';
                                $permanentCityTownVillage = $hrms['permanentCityTownVillage'] ?? 'N/A';
                                $permanentPostOffice = $hrms['permanentPostOffice'] ?? 'N/A';
                                $permanentDistrictCode = $hrms['permanentDistrictCode'] ?? 'N/A';
                                $permanentPincode = $hrms['permanentPincode'] ?? 'N/A';
                                $applicantDesignation = $hrms['applicantDesignation'] ?? 'N/A';
                                $applicantPostingPlace = $hrms['applicantPostingPlace'] ?? 'N/A';
                                $mobileNo = $hrms['mobileNo'] ?? 'N/A';
                            @endphp

                            <p align="left">
                                I, Sri/Smt. <b>{{ $applicantName }}</b> son/daughter/wife of Sri/Smt. <b>{{ $guardianName }}</b>, 
                                resident at <b>{{ $permanentStreet }}, {{ $permanentCityTownVillage }}, {{ $permanentPostOffice }}, {{ $permanentDistrictCode }}, {{ $permanentPincode }}</b> 
                                present working as <b>{{ $applicantDesignation }}</b> in the office/ Department of <b>{{ $applicantPostingPlace }}</b> hereby declare and affirm.
                            </p>

                            <ol>
                                <li>That I have no house/flat in my name, in my spouse's name or in name of and dependent member of my family within Kolkata Municipal Corporation area or Howrah Municipal Corporation area or within 20KM from Rental Housing Estate chosen by me.</li>
                                <li>That I shall use the flat for the purpose for which it has been allotted to me and shall not use it for any other purpose.</li>
                                <li>That I shall not let any other person to reside in flat issued in favour of me or keep it vacant for more than six months.</li>
                                <li>I shall not assign or transfer the premises in any way to any person or put any person in procession of the premises.</li>
                                <li>I shall not add to, or alter, any fixtures of the premises or make any structural alteration in the flat without the express permission in writing of the Competent Authority.</li>
                                <li>I shall not cause, or suffer to be caused, any damage to the premises beyond the normal wear and tear through the proper use and occupation of the premises.</li>
                                <li>I shall allow any offer duly authorized in this behalf by the Competent Authority to inspect the flat as when necessary.</li>
                                <li>I shall pay such licence fee for the flat as may be determined from time to time by the Competent Authority.</li>
                                <li>I shall inform the Competent Authority in Writing about every change in my post on place of posting and every change in my position by reason of going to leave or being placed under suspension or by any other reason within a week of the change taking place.</li>
                                <li>That further I do hereby undertake that I shall not violate any of the terms and conditions as laid down in the license issued in my favour. If I violate any of the terms and conditions of license the Competent Authority shall be free to terminate my license.</li>
                                <li>I further I do hereby undertake that upon the expiry of term license of upon termination or license. I shall delivered vacant possession of the flat to the Competent Authority or to any other person authorized by the Competent Authority in this behalf, in the same condition in which I took possession of the premises.</li>
                                <li>I further undertake that I will vacate the flat within 30 days from the date of my transfer (beyond 20 kms. From the concerned RHE)/ retirement/ resignation or dismissal/ removal from service. I shall be liable to pay whatever occupational charge will be fixed for the said period.</li>
                            </ol>

                            <p align="left">Mobile No. <b>+91 {{ $mobileNo }}</b></p>
                            <p align="left">Date: <b>{{ date('d/m/Y') }}</b></p>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="accept_declaration" name="accept_declaration" value="1" required>
                            <label class="form-check-label" for="accept_declaration">
                                I accept the terms and conditions stated in the Declaration before Competent Authority.
                            </label>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check me-2"></i> Submit
                            </button>
                            <a href="{{ route('dashboard') }}" class="btn btn-danger">
                                <i class="fa fa-times me-2"></i> Exit
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

