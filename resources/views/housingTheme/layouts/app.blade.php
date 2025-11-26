<!doctype html>
<html lang="en">
    <head>
       <meta charset="utf-8">
       <meta name="viewport" content="width=device-width, initial-scale=1">
       <title>@yield('title', 'Welcome to e-Allotment of Rental Housing Estate') | e-Allotment of Rental Housing Estate</title>
       <link rel="stylesheet" href="{{ asset('/assets/housingTheme/bootstrap/css/bootstrap.min.css') }}">
       <link rel="stylesheet" href="{{ asset('/assets/housingTheme/bootstrap/css/bootstrap-icons.min.css') }}">
       <link rel="stylesheet" href="{{ asset('/assets/housingTheme/css/style.css') }}">
       <link rel="stylesheet" href="{{ asset('/assets/housingTheme/css/font-awesome.css') }}">
       <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
       <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
       {{-- <link rel="stylesheet" href="{{ asset('/DataTables/dataTables.min.css') }}"> --}}
       @stack('styles')
    </head>
    <body>
        <div class="dashboard">
            @include('housingTheme.partials.dashboard-sidebar')
            <div id="content-wrapper" class="content-wrapper">
                <div class="main-content w-100 p-4 pb-0">
                    @include('housingTheme.partials.alerts')
                    @include('housingTheme.partials.dashboard-header')
                    @yield('content')
                    @include('housingTheme.partials.dashboard-footer')
                </div>
            </div>
        </div>
        {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script> --}}
        <script src="{{ asset('/assets/housingTheme/jquery/jquery.min.js') }}"></script>
        <script src="{{ asset('/assets/housingTheme/bootstrap/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('/assets/housingTheme/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('/themes/dashboard-theme/js/custome.js') }}"></script>
        <script>
            window.__oldInputs = @json(session()->getOldInput());
            window.__formErrors = @json($errors->toArray());

            function applyOldInputs() {
                const oldInputs = window.__oldInputs || {};
                Object.entries(oldInputs).forEach(([field, value]) => {
                    if (value === null || typeof value === 'undefined') {
                        return;
                    }

                    let elements = document.querySelectorAll(`[name="${field}"]`);
                    if (!elements.length) {
                        elements = document.querySelectorAll(`[name="${field}[]"]`);
                    }
                    if (!elements.length) {
                        return;
                    }

                    elements.forEach(element => {
                        if (element.type === 'file') {
                            return;
                        }

                        if (element.type === 'radio' || element.type === 'checkbox') {
                            const values = Array.isArray(value) ? value : [value];
                            element.checked = values.includes(element.value);
                            return;
                        }

                        if (!element.value) {
                            if (Array.isArray(value)) {
                                element.value = value[0];
                            } else {
                                element.value = value;
                            }
                        }
                    });
                });
            }

            function renderFieldErrors() {
                const formErrors = window.__formErrors || {};
                Object.entries(formErrors).forEach(([field, messages]) => {
                    const message = Array.isArray(messages) ? messages[0] : messages;
                    if (!message) {
                        return;
                    }

                    let elements = document.querySelectorAll(`[name="${field}"]`);
                    if (!elements.length) {
                        elements = document.querySelectorAll(`[name="${field}[]"]`);
                    }

                    if (!elements.length) {
                        return;
                    }

                    const container = elements[0].closest('.form-floating')
                        || elements[0].closest('.form-check')
                        || elements[0].closest('.form-group')
                        || elements[0].parentElement;

                    elements.forEach(element => element.classList.add('is-invalid'));

                    if (container && !container.querySelector('.invalid-feedback')) {
                        const feedback = document.createElement('div');
                        feedback.classList.add('invalid-feedback', 'd-block', 'mt-1');
                        feedback.textContent = message;
                        container.appendChild(feedback);
                    }
                });
            }

            document.addEventListener('DOMContentLoaded', function () {
                applyOldInputs();
                renderFieldErrors();
            });
        </script>
        @stack('scripts')
    </body>
</html>
