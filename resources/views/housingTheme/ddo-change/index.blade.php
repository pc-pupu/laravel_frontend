@extends('housingTheme.layouts.app')

@section('title', 'DDO Declaration')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fa fa-file-alt me-2"></i> DDO Declaration
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

                    <form action="{{ route('ddo-change.submit', ['encrypted_app_id' => $encrypted_app_id]) }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-12 mt-4">
                                <h4 style="font-weight: 600;line-height: 30px;font-size: 17px;">
                                    Your DDO details as recorded with the Housing Department:<br/>
                                    <span style="font-size: 17px;font-weight: 300;margin-top: 11px;">
                                        {{ $old_ddo['ddo_designation'] ?? '' }}({{ $old_ddo['ddo_code'] ?? '' }})
                                    </span>
                                </h4>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <h4 style="font-weight: 600;line-height: 30px;font-size: 17px;">
                                    Your current DDO information, as per HRMS data:<br/>
                                    <span style="font-size: 17px;font-weight: 300;margin-top: 11px;">
                                        @if(isset($current_ddo['ddo_designation']) && !empty($current_ddo['ddo_designation']))
                                            {{ $current_ddo['ddo_designation'] }}({!! $current_ddo['ddo_code'] !!})
                                        @else
                                            {!! $current_ddo['ddo_code'] ?? '' !!}
                                        @endif
                                    </span>
                                </h4>
                                <div>All further proceedings related to this application will be routed through this DDO</div>
                            </div>
                        </div>
                        <br>

                        @php
                            // Check if DDO code is not found (error message present or ddo_id is 0)
                            $ddoNotFound = false;
                            if (isset($current_ddo['ddo_code'])) {
                                $ddoNotFound = (
                                    (isset($current_ddo['ddo_id']) && $current_ddo['ddo_id'] == 0) ||
                                    strpos($current_ddo['ddo_code'], 'DDO code not found') !== false ||
                                    empty($current_ddo['ddo_code'])
                                );
                            }
                        @endphp

                        @if(!$ddoNotFound)
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="agree_declaration" name="agree_declaration" value="1" required>
                                <label class="form-check-label" for="agree_declaration">
                                    I hereby declare that the DDO information furnished above is true and correct to the best of my knowledge.
                                </label>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn bg-primary btn-sm px-5 rounded-pill text-white fw-bolder">
                                    Save
                                </button>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="fa fa-exclamation-triangle me-2"></i>
                                <strong>Note:</strong> Your DDO code is not found in the Housing Department records. Please contact the department for updation before proceeding.
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

