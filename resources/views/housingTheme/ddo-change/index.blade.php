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

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h4 style="font-weight: 600;line-height: 30px;font-size: 17px;">
                                    Your DDO details as recorded with the Housing Department:<br/>
                                    <span style="font-size: 17px;font-weight: 300;margin-top: 11px;">
                                        {{ $old_ddo['ddo_designation'] ?? 'N/A' }}({{ $old_ddo['ddo_code'] ?? 'N/A' }})
                                    </span>
                                </h4>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h4 style="font-weight: 600;line-height: 30px;font-size: 17px;">
                                    Your current DDO information, as per HRMS data:<br/>
                                    <span style="font-size: 17px;font-weight: 300;margin-top: 11px;">
                                        @if(isset($current_ddo['ddo_designation']) && !empty($current_ddo['ddo_designation']))
                                            {{ $current_ddo['ddo_designation'] }}({!! $current_ddo['ddo_code'] !!})
                                        @else
                                            {!! $current_ddo['ddo_code'] ?? 'N/A' !!}
                                        @endif
                                    </span>
                                </h4>
                                <div class="mt-2">
                                    All further proceedings related to this application will be routed through this DDO
                                </div>
                            </div>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="agree_declaration" name="agree_declaration" value="1" required>
                            <label class="form-check-label" for="agree_declaration">
                                I hereby declare that the DDO information furnished above is true and correct to the best of my knowledge.
                            </label>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save me-2"></i> Save
                            </button>
                            <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                                <i class="fa fa-times me-2"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

