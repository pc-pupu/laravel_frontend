@extends('housingTheme.layouts.app')

@section('title', 'Upload Supporting Document')

@section('content')
<div class="cms-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="cms-card">
                <div class="cms-header d-flex justify-content-between align-items-center">
                    <div>
                        <h3><i class="fa fa-upload me-2"></i> Upload Supporting Document</h3>
                        <p class="mb-0">Upload declaration signed form (PDF, max 1 MB)</p>
                    </div>
                    <a href="{{ route('dashboard') }}" class="btn btn-light">
                        <i class="fa fa-arrow-left me-2"></i> Back to Dashboard
                    </a>
                </div>

                @include('housingTheme.partials.alerts')

                <div class="cms-body">
                    <div class="alert alert-info" role="alert">
                        <h4 class="alert-heading">Instructions to Upload Supporting Document</h4>
                        <p>
                            You must upload the supporting document because your allotment category is within
                            <strong>"Recommended" / "Single Earning Lady" / "Legal Heir" / "Physically Handicapped or Serious Illness" / "Transfer"</strong>.
                            If you do not upload it, your allotment may be rejected and you may not be able to access your dashboard properly.
                        </p>
                    </div>

                    <form method="POST"
                          action="{{ route('new-application.supporting-doc-upload.submit', ['id' => $encryptedId]) }}"
                          enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text"
                                           class="form-control"
                                           id="application_no"
                                           value="{{ $applicationNo ?? 'N/A' }}"
                                           readonly>
                                    <label for="application_no">Application Number</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text"
                                           class="form-control"
                                           id="allotment_category"
                                           value="{{ $allotmentCategory ?? 'N/A' }}"
                                           readonly>
                                    <label for="allotment_category">Allotment Category</label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="supporting_doc" class="form-label">
                                        Upload Declaration Signed Form
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="file"
                                           name="extra_doc"
                                           id="supporting_doc"
                                           class="form-control @error('extra_doc') is-invalid @enderror"
                                           accept="application/pdf">
                                    <small class="form-text text-muted">
                                        Allowed Extension: <strong>pdf</strong> | Maximum File Size: <strong>1 MB</strong>
                                    </small>
                                    @error('extra_doc')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-6">
                                <button type="submit" class="btn bg-primary btn-sm px-5 rounded-pill text-white fw-bolder">
                                    <i class="fa fa-upload me-2"></i> Upload
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

