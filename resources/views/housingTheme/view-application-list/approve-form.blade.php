@extends('housingTheme.layouts.app')

@section('title', 'Approve Application')

@section('content')
<div class="cms-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="cms-card">
                <div class="cms-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3><i class="fa fa-check-circle me-2"></i> Approve Application</h3>
                            <p class="mb-0">Upload signed application form and approve</p>
                        </div>
                        <a href="{{ route('view_application', [
                            'status' => \App\Helpers\UrlEncryptionHelper::encryptUrl($status),
                            'entity' => \App\Helpers\UrlEncryptionHelper::encryptUrl($entity),
                            'page_status' => $pageStatus
                        ]) }}" class="btn btn-light">
                            <i class="fa fa-arrow-left me-2"></i> Back
                        </a>
                    </div>
                </div>

                @include('housingTheme.partials.alerts')

                <div class="cms-body">
                    <form action="{{ route('application-approve.store', [
                        'id' => \App\Helpers\UrlEncryptionHelper::encryptUrl($applicationId),
                        'status' => \App\Helpers\UrlEncryptionHelper::encryptUrl($status),
                        'entity' => \App\Helpers\UrlEncryptionHelper::encryptUrl($entity),
                        'page_status' => $pageStatus,
                        'computer_serial_no' => \App\Helpers\UrlEncryptionHelper::encryptUrl($computerSerialNo),
                        'flat_type' => \App\Helpers\UrlEncryptionHelper::encryptUrl($flatType)
                    ]) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="application_type" value="{{ $entityType['type'] ?? 'Application' }}" disabled>
                                    <label for="application_type">Application Type</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="application_no" value="{{ $application['application_no'] ?? '' }}" disabled>
                                    <label for="application_no">Application No.</label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="form-floating">
                                    <input type="file" class="form-control @error('application_form_file') is-invalid @enderror" id="application_form_file" name="application_form_file" accept=".pdf" required>
                                    <label for="application_form_file">Upload Signed Application Form <span class="text-danger">*</span></label>
                                    <small class="form-text text-muted">
                                        <strong>Allowed Extension: pdf<br>Maximum File Size: 1 MB</strong>
                                    </small>
                                    @error('application_form_file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-success me-2">
                                <i class="fa fa-check me-2"></i> Upload & Approve Application
                            </button>
                            <a href="{{ route('view_application', [
                                'status' => \App\Helpers\UrlEncryptionHelper::encryptUrl($status),
                                'entity' => \App\Helpers\UrlEncryptionHelper::encryptUrl($entity),
                                'page_status' => $pageStatus
                            ]) }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

