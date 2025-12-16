<script>
const API_BASE = '{{ rtrim(config("services.api.base_url"), "/") }}';
const TOKEN = '{{ session("api_token") }}';
const APPLICANT_DATA = @json($applicant ?? []);

// Global variables for dropdowns
let districts = {};
let payBands = {};
let allotmentCategories = {};
let ddoDesignations = {};

document.addEventListener('DOMContentLoaded', function() {
    initializeDatePickers();
    loadDistricts();
    loadPayBands(APPLICANT_DATA.pay_band_id <= 5 ? 'new' : 'old');
    setupEventListeners();
    setupNumericInputs();
    
    // Set initial values after dropdowns are loaded
    setTimeout(() => {
        setInitialValues();
    }, 500);
});

// Set initial values for edit form
function setInitialValues() {
    // Set districts
    if (APPLICANT_DATA.permanent_district) {
        document.getElementById('permanent_district').value = APPLICANT_DATA.permanent_district;
    }
    if (APPLICANT_DATA.present_district) {
        document.getElementById('present_district').value = APPLICANT_DATA.present_district;
    }
    if (APPLICANT_DATA.office_district) {
        document.getElementById('office_district').value = APPLICANT_DATA.office_district;
    }
    if (APPLICANT_DATA.district_code) {
        document.getElementById('district').value = APPLICANT_DATA.district_code;
        loadDdoDesignations(APPLICANT_DATA.district_code);
    }
    
    // Set pay band
    if (APPLICANT_DATA.pay_band_id) {
        setTimeout(() => {
            document.getElementById('pay_band').value = APPLICANT_DATA.pay_band_id;
            loadRheFlatType(APPLICANT_DATA.pay_band_id);
        }, 300);
    }
    
    // Set DDO designation
    if (APPLICANT_DATA.ddo_id) {
        setTimeout(() => {
            document.getElementById('designation').value = APPLICANT_DATA.ddo_id;
            loadDdoAddress(APPLICANT_DATA.ddo_id);
        }, 500);
    }
    
    // Set allotment category
    if (APPLICANT_DATA.allotment_category) {
        setTimeout(() => {
            document.getElementById('reason').value = APPLICANT_DATA.allotment_category;
        }, 700);
    }
}

// Initialize date pickers
function initializeDatePickers() {
    if (typeof jQuery !== 'undefined' && jQuery.ui && jQuery.ui.datepicker) {
        jQuery("#dob").datepicker({
            dateFormat: "dd/mm/yy",
            changeMonth: true,
            changeYear: true,
            yearRange: "-70:-18",
            maxDate: "-18Y",
            autoSize: true
        });
        
        jQuery("#doj").datepicker({
            dateFormat: "dd/mm/yy",
            changeMonth: true,
            changeYear: true,
            yearRange: "-70:+0",
            maxDate: "0",
            autoSize: true
        });
        
        jQuery("#dor").datepicker({
            dateFormat: "dd/mm/yy",
            changeMonth: true,
            changeYear: true,
            yearRange: "-0:+70",
            minDate: "0",
            autoSize: true
        });
        
        jQuery("#doa").datepicker({
            dateFormat: "dd/mm/yy",
            changeMonth: true,
            changeYear: true,
            yearRange: "-70:+0",
            autoSize: true
        });
    }
}

// Load districts
async function loadDistricts() {
    try {
        const response = await fetch(`${API_BASE}/existing-applicants-helpers/districts`, {
            headers: { 'Authorization': `Bearer ${TOKEN}` }
        });
        const data = await response.json();
        if (data.status === 'success') {
            districts = data.data;
            populateSelect('#permanent_district', districts);
            populateSelect('#present_district', districts);
            populateSelect('#office_district', districts);
            populateSelect('#district', districts);
        }
    } catch (error) {
        console.error('Error loading districts:', error);
    }
}

// Load pay bands based on type
async function loadPayBands(type) {
    try {
        const response = await fetch(`${API_BASE}/existing-applicants-helpers/pay-bands?type=${type}`, {
            headers: { 'Authorization': `Bearer ${TOKEN}` }
        });
        const data = await response.json();
        if (data.status === 'success') {
            payBands = data.data;
            populateSelect('#pay_band', payBands);
        }
    } catch (error) {
        console.error('Error loading pay bands:', error);
    }
}

// Load RHE flat type based on pay band
async function loadRheFlatType(payBandId) {
    if (!payBandId) {
        document.getElementById('rhe_flat_type').value = '';
        document.getElementById('reason').innerHTML = '<option value="">--Choose Allotment Reason--</option>';
        return;
    }
    
    try {
        const response = await fetch(`${API_BASE}/existing-applicants-helpers/rhe-flat-type?pay_band_id=${payBandId}`, {
            headers: { 'Authorization': `Bearer ${TOKEN}` }
        });
        const data = await response.json();
        if (data.status === 'success') {
            document.getElementById('rhe_flat_type').value = data.data || '';
            if (data.data) {
                loadAllotmentCategories(data.data);
            }
        }
    } catch (error) {
        console.error('Error loading RHE flat type:', error);
    }
}

// Load allotment categories
async function loadAllotmentCategories(rheFlatType) {
    try {
        const response = await fetch(`${API_BASE}/existing-applicants-helpers/allotment-categories?rhe_flat_type=${encodeURIComponent(rheFlatType)}`, {
            headers: { 'Authorization': `Bearer ${TOKEN}` }
        });
        const data = await response.json();
        if (data.status === 'success') {
            allotmentCategories = data.data;
            populateSelect('#reason', allotmentCategories);
        }
    } catch (error) {
        console.error('Error loading allotment categories:', error);
    }
}

// Load DDO designations
async function loadDdoDesignations(districtCode) {
    if (!districtCode) {
        document.getElementById('designation').innerHTML = '<option value="">- Select -</option>';
        return;
    }
    
    try {
        const response = await fetch(`${API_BASE}/existing-applicants-helpers/ddo-designations?district_code=${districtCode}`, {
            headers: { 'Authorization': `Bearer ${TOKEN}` }
        });
        const data = await response.json();
        if (data.status === 'success') {
            ddoDesignations = data.data;
            populateSelect('#designation', ddoDesignations);
        }
    } catch (error) {
        console.error('Error loading DDO designations:', error);
    }
}

// Load DDO address
async function loadDdoAddress(ddoId) {
    if (!ddoId) {
        document.getElementById('ddo_address').value = '';
        document.getElementById('replace_ddo_address').style.display = 'none';
        return;
    }
    
    try {
        const response = await fetch(`${API_BASE}/existing-applicants-helpers/ddo-address?ddo_id=${ddoId}`, {
            headers: { 'Authorization': `Bearer ${TOKEN}` }
        });
        const data = await response.json();
        if (data.status === 'success') {
            document.getElementById('ddo_address').value = data.data || '';
            document.getElementById('replace_ddo_address').style.display = 'block';
        }
    } catch (error) {
        console.error('Error loading DDO address:', error);
    }
}

// Populate select dropdown
function populateSelect(selector, options) {
    const select = document.querySelector(selector);
    if (!select) return;
    
    const currentValue = select.value;
    select.innerHTML = '';
    
    // Always add blank option first
    const blankOption = document.createElement('option');
    blankOption.value = '';
    blankOption.textContent = '- Select -';
    select.appendChild(blankOption);
    
    // Add other options
    for (const [value, text] of Object.entries(options)) {
        const option = document.createElement('option');
        option.value = value;
        option.textContent = text;
        select.appendChild(option);
    }
    
    // Set the value - if empty or not found, select blank option
    if (currentValue && options[currentValue]) {
        select.value = currentValue;
    } else {
        select.value = '';
    }
}

// Setup event listeners
function setupEventListeners() {
    // Pay band type change
    document.querySelectorAll('input[name="pay_band_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            loadPayBands(this.value);
            document.getElementById('pay_band').value = '';
            document.getElementById('rhe_flat_type').value = '';
            document.getElementById('reason').innerHTML = '<option value="">--Choose Allotment Reason--</option>';
        });
    });
    
    // Pay band change
    const payBandSelect = document.getElementById('pay_band');
    if (payBandSelect) {
        payBandSelect.addEventListener('change', function() {
            loadRheFlatType(this.value);
        });
    }
    
    // DDO district change
    const ddoDistrictSelect = document.getElementById('district');
    if (ddoDistrictSelect) {
        ddoDistrictSelect.addEventListener('change', function() {
            loadDdoDesignations(this.value);
            document.getElementById('designation').value = '';
            document.getElementById('ddo_address').value = '';
            document.getElementById('replace_ddo_address').style.display = 'none';
        });
    }
    
    // DDO designation change
    const designationSelect = document.getElementById('designation');
    if (designationSelect) {
        designationSelect.addEventListener('change', function() {
            loadDdoAddress(this.value);
        });
    }
}

// Setup numeric inputs
function setupNumericInputs() {
    document.querySelectorAll('.numeric_positive').forEach(input => {
        input.addEventListener('keypress', function(e) {
            if (e.which !== 8 && e.which !== 0 && (e.which < 48 || e.which > 57)) {
                e.preventDefault();
            }
        });
    });
}

// Form validation (based on Drupal validate_existing_applicant_form)
function validate_existing_applicant_form() {
    const form = document.getElementById('editApplicantForm');
    if (!form) return false;
    
    // Basic required field validation
    const requiredFields = [
        { id: 'applicant_name', name: 'Applicant Name' },
        { id: 'applicant_father_name', name: 'Father / Husband Name' },
        { id: 'dob', name: 'Date of Birth' },
        { id: 'gender', name: 'Gender' },
        { id: 'app_designation', name: 'Designation' },
        { id: 'app_posting_place', name: 'Place of Posting' },
        { id: 'pay_band_type', name: 'Pay Band Type' },
        { id: 'pay_band', name: 'Basic Pay Range' },
        { id: 'pay_in', name: 'Basic Pay' },
        { id: 'dor', name: 'Date of Retirement' },
        { id: 'office_name', name: 'Name of the Office' },
        { id: 'office_street', name: 'Office Address' },
        { id: 'office_city', name: 'Office City' },
        { id: 'office_district', name: 'Office District' },
        { id: 'office_pincode', name: 'Office Pincode' },
        { id: 'rhe_flat_type', name: 'Flat TYPE' },
        { id: 'reason', name: 'Allotment Category' },
        { id: 'doa', name: 'Date of Application' },
        { id: 'computer_serial_no', name: 'Computer Serial No' },
        { id: 'physical_application_no', name: 'Physical Application Number' },
    ];
    
    let hasErrors = false;
    
    requiredFields.forEach(field => {
        const element = document.getElementById(field.id);
        if (element) {
            if (element.type === 'radio') {
                const checked = document.querySelector(`input[name="${field.id}"]:checked`);
                if (!checked) {
                    alert(`Please select ${field.name}.`);
                    hasErrors = true;
                }
            } else if (element.type === 'select-one') {
                if (!element.value) {
                    alert(`Please select ${field.name}.`);
                    hasErrors = true;
                }
            } else {
                if (!element.value.trim()) {
                    alert(`Please enter ${field.name}.`);
                    hasErrors = true;
                }
            }
        }
    });
    
    // Validate date formats
    const dateFields = ['dob', 'doj', 'dor', 'doa'];
    dateFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field && field.value) {
            const datePattern = /^\d{2}\/\d{2}\/\d{4}$/;
            if (!datePattern.test(field.value)) {
                alert(`${field.previousElementSibling.textContent} must be in DD/MM/YYYY format.`);
                hasErrors = true;
            }
        }
    });
    
    // Validate mobile number
    const mobile = document.getElementById('mobile');
    if (mobile && mobile.value) {
        if (!/^\d{10}$/.test(mobile.value)) {
            alert('Mobile number must be exactly 10 digits.');
            hasErrors = true;
        }
    }
    
    // Validate email
    const email = document.getElementById('email');
    if (email && email.value) {
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email.value)) {
            alert('Please enter a valid email address.');
            hasErrors = true;
        }
    }
    
    if (hasErrors) {
        return false;
    }
    
    return true;
}
</script>

