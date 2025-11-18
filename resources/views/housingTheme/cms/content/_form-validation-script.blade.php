@once
    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('cmsContentForm');
        if (!form) return;

        const errorAlert = document.getElementById('cmsFormError');
        const requiredFieldIds = ['content_type', 'content_title', 'content_description', 'order_no', 'date_of_notification'];
        const fileInput = document.getElementById('content_file_upload');
        const fileError = document.getElementById('fileUploadError');
        const maxFileSize = 1024 * 1024; // 1 MB
        const allowedMimeTypes = ['application/pdf'];

        const clearAlert = () => {
            if (!errorAlert) return;
            errorAlert.classList.add('d-none');
            errorAlert.innerHTML = '';
        };

        const showAlert = (messages) => {
            if (!errorAlert) return;
            errorAlert.classList.remove('d-none');
            errorAlert.innerHTML = `
                <strong>Please fix the following:</strong>
                <ul class="mb-0 mt-2">
                    ${messages.map(message => `<li>${message}</li>`).join('')}
                </ul>
            `;
            window.scrollTo({ top: form.offsetTop - 120, behavior: 'smooth' });
        };

        const setInvalid = (input) => {
            if (!input) return;
            input.classList.add('is-invalid');
        };

        const clearFileError = () => {
            if (!fileError) return;
            fileError.textContent = '';
            fileError.classList.add('d-none');
        };

        const setFileError = (message) => {
            if (!fileError) {
                showAlert([message]);
                return;
            }
            fileError.textContent = message;
            fileError.classList.remove('d-none');
        };

        const clearInvalidOnInput = (input) => {
            if (!input) return;
            const handler = () => input.classList.remove('is-invalid');
            input.addEventListener('input', handler);
            input.addEventListener('change', handler);
        };

        requiredFieldIds.forEach(id => clearInvalidOnInput(document.getElementById(id)));
        if (fileInput) clearInvalidOnInput(fileInput);

        const validateFileInput = (showImmediate = false) => {
            if (!fileInput || !fileInput.files.length) {
                clearFileError();
                return [];
            }

            const errors = [];
            const file = fileInput.files[0];
            const fileName = file.name || '';
            const fileNameLower = fileName.toLowerCase();

            if (!fileNameLower.endsWith('.pdf') || (file.type && !allowedMimeTypes.includes(file.type))) {
                errors.push('Uploaded file must be a PDF document.');
            }

            const dotCount = (fileName.match(/\./g) || []).length;
            if (dotCount > 1) {
                errors.push('Uploaded file name cannot contain multiple periods.');
            }

            if (file.size > maxFileSize) {
                errors.push('Uploaded file must not exceed 1 MB.');
            }

            if (showImmediate) {
                if (errors.length) {
                    setInvalid(fileInput);
                    setFileError(errors[0]);
                } else {
                    clearFileError();
                }
            }

            return errors;
        };

        if (fileInput) {
            fileInput.addEventListener('change', () => {
                validateFileInput(true);
            });
        }

        form.addEventListener('submit', function (event) {
            const errors = [];
            clearAlert();

            requiredFieldIds.forEach(id => {
                const field = document.getElementById(id);
                if (field && !field.value.trim()) {
                    errors.push(`${field.labels && field.labels.length ? field.labels[0].innerText : id} is required.`);
                    setInvalid(field);
                }
            });

            const fileErrors = validateFileInput();
            if (fileErrors.length) {
                errors.push(...fileErrors);
                setInvalid(fileInput);
            }

            if (errors.length) {
                event.preventDefault();
                showAlert(errors);
                if (fileErrors.length) {
                    setFileError(fileErrors[0]);
                }
            }
        });
    });
    </script>
    @endpush
@endonce

