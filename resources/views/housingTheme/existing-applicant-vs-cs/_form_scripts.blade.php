<script>
document.addEventListener('DOMContentLoaded', function() {
    const backendUrl = '{{ env("BACKEND_API") }}';
    const token = '{{ session("api_token") }}';
    const serviceBookMinDate = new Date(1945, 0, 1);
    const serviceBookMinDateString = '01/01/1945';
    const possessionMinDate = new Date(1975, 0, 1);
    const possessionMinDateString = '01/01/1975';
    const today = new Date();

    // Initialize date pickers
    $("#application_date").datepicker({
        dateFormat: "dd/mm/yy",
        changeMonth: true,
        changeYear: true,
        yearRange: "-70:+0",
        autoSize: true
    });

    $("#dob").datepicker({
        dateFormat: "dd/mm/yy",
        changeMonth: true,
        changeYear: true,
        yearRange: "1945:" + (new Date().getFullYear() - 18),
        minDate: serviceBookMinDate,
        maxDate: "-18Y",
        autoSize: true
    });

    $("#doj").datepicker({
        dateFormat: "dd/mm/yy",
        changeMonth: true,
        changeYear: true,
        yearRange: "1945:+0",
        minDate: serviceBookMinDate,
        maxDate: "0",
        autoSize: true
    });

    $("#dor").datepicker({
        dateFormat: "dd/mm/yy",
        changeMonth: true,
        changeYear: true,
        yearRange: "-0:+70",
        minDate: "0",
        autoSize: true
    });

    function parseDdMmYyyy(dateStr) {
        if (!dateStr) {
            return null;
        }
        const parts = dateStr.split('/');
        if (parts.length !== 3) {
            return null;
        }
        const [day, month, year] = parts.map(Number);
        if (!day || !month || !year) {
            return null;
        }
        return new Date(year, month - 1, day);
    }

    function formatDate(dateObj) {
        const day = String(dateObj.getDate()).padStart(2, '0');
        const month = String(dateObj.getMonth() + 1).padStart(2, '0');
        const year = dateObj.getFullYear();
        return `${day}/${month}/${year}`;
    }

    // Load districts
    function loadDistricts(elementId, defaultValue = '') {
        $.ajax({
            url: backendUrl + '/api/existing-applicants-helpers/districts',
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            },
            success: function(response) {
                if (response.status === 'success') {
                    const select = $('#' + elementId);
                    select.html('<option value="">- Select -</option>');
                    for (const code in response.data) {
                        if (code !== '') {
                            select.append('<option value="' + code + '">' + response.data[code] + '</option>');
                        }
                    }
                    // Only set value if defaultValue is provided, otherwise select blank option
                    if (defaultValue) {
                        select.val(defaultValue);
                    } else {
                        select.val('');
                    }
                }
            }
        });
    }

    // Load DDO designations
    function loadDdoDesignations(districtCode, defaultValue = '') {
        if (!districtCode) {
            $('#replace_designation').html(
                '<div class="form-floating">' +
                    '<select class="form-select" id="designation" name="designation">' +
                        '<option value="">- Select DDO Designation -</option>' +
                    '</select>' +
                    '<label for="designation">DDO Designation</label>' +
                '</div>'
            );
            $('#ddo_address').val('');
            return;
        }
        // console.log(districtCode);
        
        $.ajax({
            url: backendUrl + '/api/existing-applicants-helpers/ddo-designations',
            method: 'GET',
            data: { district_code: districtCode },
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            },
            success: function(response) {
                // console.log(response);
                
                if (response.status === 'success') {
                    let html = '<div class="form-floating">';
                    html += '<select class="form-select" id="designation" name="designation">';
                    html += '<option value="">- Select DDO Designation -</option>';

                    for (const key in response.data) {
                        if (response.data.hasOwnProperty(key)) {
                            html += '<option value="' + key + '">' + response.data[key] + '</option>';
                        }
                    }

                    html += '</select>';
                    html += '<label for="designation">DDO Designation</label>';
                    html += '</div>';

                    $('#replace_designation').html(html);

                    if (defaultValue) {
                        $('#designation').val(defaultValue).trigger('change');
                    }
                }
            }
        });
    }


    // Load DDO address
    function loadDdoAddress(ddoId) {
        if (!ddoId) {
            $('#ddo_address').val('');
            return;
        }

        $.ajax({
            url: backendUrl + '/api/existing-applicants-helpers/ddo-address',
            method: 'GET',
            data: { ddo_id: ddoId },
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            },
            success: function(response) {
                if (response.status === 'success') {
                    $('#ddo_address').val(response.data || '');
                }
            }
        });
    }

    // Load pay bands
    function loadPayBands(payBandType, defaultValue = '') {
        $.ajax({
            url: backendUrl + '/api/existing-applicants-helpers/pay-bands',
            method: 'GET',
            data: { pay_band_type: payBandType },
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            },
            success: function(response) {
                if (response.status === 'success') {
                    const select = $('#pay_band');
                    select.html('<option value="">- Select Pay Band First -</option>');
                    for (const id in response.data) {
                        select.append('<option value="' + id + '">' + response.data[id] + '</option>');
                    }
                    if (defaultValue) {
                        select.val(defaultValue).trigger('change');
                    }
                }
            }
        });
    }

    // Load RHE flat type
    function loadRheFlatType(payBandId) {
        if (!payBandId) {
            $('#flat_type_display').val('');
            return;
        }

        $.ajax({
            url: backendUrl + '/api/existing-applicants-helpers/rhe-flat-type',
            method: 'GET',
            data: { pay_band_id: payBandId },
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            },
            success: function(response) {
                if (response.status === 'success') {
                    $('#flat_type_display').val('Your flat type is: ' + (response.data || 'Not Found'));
                }
            }
        });
    }

    // Load housing estates
    function loadHousingEstates(defaultValue = '') {
        $.ajax({
            url: backendUrl + '/api/existing-applicant-vs-cs-helpers/housing-estates',
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            },
            success: function(response) {
                if (response.status === 'success') {
                    const select = $('#occupation_estate');
                    select.html('<option value="">- Select Housing -</option>');
                    for (const id in response.data) {
                        select.append('<option value="' + id + '">' + response.data[id] + '</option>');
                    }
                    if (defaultValue) {
                        select.val(defaultValue).trigger('change');
                    }
                }
            }
        });
    }

    // Load housing blocks
    function loadHousingBlocks(estateId, defaultValue = '') {
        if (!estateId) {
            $('#block_replace').html('<div class="form-floating"><select class="form-select" id="occupation_block" name="occupation_block" required><option value="">- Select Block -</option></select><label for="occupation_block" class="required">Select Block</label></div>');
            return;
        }

        $.ajax({
            url: backendUrl + '/api/existing-applicant-vs-cs-helpers/housing-blocks',
            method: 'GET',
            data: { estate_id: estateId },
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            },
            success: function(response) {
                if (response.status === 'success') {
                    let html = '<div class="form-floating">';
                    html += '<select class="form-select" id="occupation_block" name="occupation_block" required>';
                    html += '<option value="">- Select Block -</option>';
                    for (const id in response.data) {
                        html += '<option value="' + id + '">' + response.data[id] + '</option>';
                    }
                    html += '</select>';
                    html += '<label for="occupation_block" class="required">Select Block</label>';
                    html += '</div>';
                    $('#block_replace').html(html);
                    if (defaultValue) {
                        $('#occupation_block').val(defaultValue).trigger('change');
                    }
                }
            }
        });
    }

    // Load housing flats
    function loadHousingFlats(estateId, blockId, defaultValue = '') {
        if (!estateId || !blockId) {
            $('#flat_no_replace').html('<div class="form-floating"><select class="form-select" id="occupation_flat" name="occupation_flat" required><option value="">- Select Flat No -</option></select><label for="occupation_flat" class="required">Flat No</label></div>');
            return;
        }

        $.ajax({
            url: backendUrl + '/api/existing-applicant-vs-cs-helpers/housing-flats',
            method: 'GET',
            data: { estate_id: estateId, block_id: blockId },
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            },
            success: function(response) {
                if (response.status === 'success') {
                    let html = '<div class="form-floating">';
                    html += '<select class="form-select" id="occupation_flat" name="occupation_flat" required>';
                    html += '<option value="">- Select Flat No -</option>';
                    for (const id in response.data) {
                        html += '<option value="' + id + '">' + response.data[id] + '</option>';
                    }
                    html += '</select>';
                    html += '<label for="occupation_flat" class="required">Flat No</label>';
                    html += '</div>';
                    $('#flat_no_replace').html(html);
                    if (defaultValue) {
                        $('#occupation_flat').val(defaultValue).trigger('change');
                    }
                }
            }
        });
    }

    // Load possession date
    function loadPossessionDate(flatId) {
        if (!flatId) {
            $('#possession_date').val('');
            return;
        }

        $.ajax({
            url: backendUrl + '/api/existing-applicant-vs-cs-helpers/possession-date',
            method: 'GET',
            data: { flat_id: flatId },
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            },
            success: function(response) {
                if (response.status === 'success' && response.data) {
                    let parsed = parseDdMmYyyy(response.data);
                    if (!parsed) {
                        parsed = possessionMinDate;
                    }
                    if (parsed < possessionMinDate) {
                        parsed = possessionMinDate;
                    }
                    if (parsed > today) {
                        parsed = today;
                    }
                    $('#possession_date').val(formatDate(parsed));
                } else {
                    $('#possession_date').val(possessionMinDateString);
                }
            }
        });
    }

    // Event handlers
    $('input[name="pay_type_new"]').on('change', function() {
        loadPayBands($(this).val());
        $('#flat_type_display').val('');
    });

    $('#pay_band').on('change', function() {
        loadRheFlatType($(this).val());
    });

    $('#district').on('change', function() {
        loadDdoDesignations($(this).val());
    });

    $(document).on('change', '#designation', function() {
        loadDdoAddress($(this).val());
    });

    $('#occupation_estate').on('change', function() {
        loadHousingBlocks($(this).val());
        $('#flat_no_replace').html('<div class="form-floating"><select class="form-select" id="occupation_flat" name="occupation_flat" required><option value="">- Select Flat No -</option></select><label for="occupation_flat" class="required">Flat No</label></div>');
        $('#possession_date').val('');
    });

    $(document).on('change', '#occupation_block', function() {
        const estateId = $('#occupation_estate').val();
        loadHousingFlats(estateId, $(this).val());
        $('#possession_date').val('');
    });

    $(document).on('change', '#occupation_flat', function() {
        loadPossessionDate($(this).val());
    });

    function enforceMinServiceBookDate(selector) {
        const $field = $(selector);
        const value = $field.val();
        if (!value) {
            $field.val(serviceBookMinDateString);
            return;
        }
        const [day, month, year] = value.split('/');
        const parsedDate = new Date(year, month - 1, day);
        if (parsedDate < serviceBookMinDate) {
            $field.val(serviceBookMinDateString);
        }
    }

    // Initial loads
    const initialPermanentDistrict = '{{ old("permanent_district", $applicantData["permanent_district"] ?? "17") }}';
    const initialPresentDistrict = '{{ old("present_district", $applicantData["present_district"] ?? "17") }}';
    const initialOfficeDistrict = '{{ old("office_district", $applicantData["office_district"] ?? "17") }}';
    const initialDdoDistrict = '{{ old("district", $applicantData["district_code"] ?? "") }}';
    const initialDdoDesignation = '{{ old("designation", $applicantData["ddo_id"] ?? "") }}';

    loadDistricts('permanent_district', initialPermanentDistrict);
    loadDistricts('present_district', initialPresentDistrict);
    loadDistricts('office_district', initialOfficeDistrict);
    loadDistricts('district', initialDdoDistrict || '');
    
    const initialPayBandType = $('input[name="pay_type_new"]:checked').val() || 'old';
    loadPayBands(initialPayBandType, '{{ old("pay_band") }}');
    
    if (initialDdoDistrict) {
        loadDdoDesignations(initialDdoDistrict, initialDdoDesignation);
    }
    
    loadHousingEstates('{{ old("occupation_estate") }}');
    
    if ('{{ old("occupation_estate") }}') {
        loadHousingBlocks('{{ old("occupation_estate") }}', '{{ old("occupation_block") }}');
    }
    
    if ('{{ old("occupation_estate") }}' && '{{ old("occupation_block") }}') {
        loadHousingFlats('{{ old("occupation_estate") }}', '{{ old("occupation_block") }}', '{{ old("occupation_flat") }}');
    }

    enforceMinServiceBookDate('#dob');
    enforceMinServiceBookDate('#doj');

    const existingPossession = $('#possession_date').val();
    if (existingPossession) {
        let parsed = parseDdMmYyyy(existingPossession) || possessionMinDate;
        if (parsed < possessionMinDate) {
            parsed = possessionMinDate;
        }
        if (parsed > today) {
            parsed = today;
        }
        $('#possession_date').val(formatDate(parsed));
    }

    // Form validation
    $('#vsCsForm').on('submit', function(e) {
        let errors = [];

        // Required fields
        if (!$('#application_type').val()) {
            errors.push('Application Type is required.');
        }
        if (!$('#application_date').val()) {
            errors.push('Date of Application is required.');
        }
        if (!$('#applicant_name').val()) {
            errors.push('Applicant Name is required.');
        }
        if (!$('#dor').val()) {
            errors.push('Date of Retirement is required.');
        }
        if (!$('#pay_band').val()) {
            errors.push('Pay Band is required.');
        }
        if (!$('#occupation_estate').val()) {
            errors.push('Select Housing is required.');
        }
        if (!$('#occupation_block').val()) {
            errors.push('Select Block is required.');
        }
        if (!$('#occupation_flat').val()) {
            errors.push('Flat No is required.');
        }
        if (!$('#possession_date').val()) {
            errors.push('Date of Possession is required.');
        }

        if (errors.length > 0) {
            e.preventDefault();
            alert('Please correct the following errors:\n' + errors.join('\n'));
            return false;
        }
    });
});
</script>

