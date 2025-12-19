{{-- Common Personal Information Fields --}}
@php
    $data = $data ?? [];
    $isEdit = isset($data['applicant_name']) || isset($data['applicant_id']);
    $applicantName = $data['applicant_name'] ?? old('applicant_name', '');
    $fatherName = $data['guardian_name'] ?? $data['applicant_father_name'] ?? old('applicant_father_name', '');
    $mobile = $data['mobile_no'] ?? $data['mobile'] ?? old('mobile', '');
    $email = $data['email'] ?? old('email', '');
    $dob = $data['dob'] ?? $data['date_of_birth'] ?? old('dob', '');
    $gender = $data['gender'] ?? old('gender', 'M');
@endphp

<div class="form-section">
    <h5 class="mb-3"><i class="fa fa-user me-2"></i> Personal Information (According to Service Book)</h5>
    
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="applicant_name" name="applicant_name" 
                    value="{{ $applicantName }}" placeholder="Applicant Name" required oninput="this.value=this.value.toUpperCase()">
                <label for="applicant_name" class="required">Applicant's Name</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="applicant_father_name" name="applicant_father_name" 
                    value="{{ $fatherName }}" placeholder="Father/Husband Name" required oninput="this.value=this.value.toUpperCase()">
                <label for="applicant_father_name" class="required">Father / Husband Name</label>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control" id="mobile" name="mobile" 
                    value="{{ $mobile }}" placeholder="Mobile no" maxlength="10" required>
                <label for="mobile" class="required">Mobile no</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control" id="email" name="email" 
                    value="{{ $email }}" placeholder="Email ID" oninput="this.value=this.value.toLowerCase()" required>
                <label for="email" class="required">Email ID</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control" id="dob" name="dob" 
                    value="{{ $dob }}" placeholder="Date of Birth(According to Service Book)" required 
                    autocomplete="off" maxlength="10" disabled>
                <label for="dob" class="required">Date of Birth(According to Service Book)</label>
            </div>
        </div>
    </div>
    
    <div class="row g-3 mt-2">
        <div class="col-md-4">
            <label class="required mb-2 d-block">Gender</label>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="gender" id="gender_m" value="M" required 
                    {{ $gender == 'M' ? 'checked' : '' }}>
                <label class="form-check-label" for="gender_m">Male</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="gender" id="gender_f" value="F" required
                    {{ $gender == 'F' ? 'checked' : '' }}>
                <label class="form-check-label" for="gender_f">Female</label>
            </div>
        </div>
    </div>
</div>

