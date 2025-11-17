<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'CMS Content' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" referrerpolicy="no-referrer" />
    <style>
        body {
            background-color: #f5f7fb;
            min-height: 100vh;
        }
        .cms-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            padding: 2rem;
        }
        .cms-header {
            border-bottom: 1px solid #e9ecef;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
        }
        .form-floating > label {
            color: #6c757d;
        }
        .table thead {
            background: #f0f2f5;
        }
        .required::after {
            content: " *";
            color: #dc3545;
        }
    </style>
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ route('cms-content.index') }}">CMS Content</a>
            <div>
                <a href="{{ route('dashboard') }}" class="btn btn-sm btn-light">Dashboard</a>
            </div>
        </div>
    </nav>

    <main class="py-4">
        <div class="container">
            @include('housingTheme.cms.partials.alerts')
            @yield('content')
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>

