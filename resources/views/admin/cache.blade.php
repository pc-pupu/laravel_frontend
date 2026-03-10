@extends('admin.layouts.app')

@section('title', 'Cache & Optimize')
@section('page-title', 'Cache & Optimize')

@section('content')

<div class="admin-card">
    <div class="admin-card-header">
        <h3>Cache & Optimize</h3>
    </div>
    <div class="admin-card-body">
        <p class="text-muted mb-4">Clear caches and compiled files for <strong>both frontend and backend</strong>. Use after code/config changes or to free space.</p>

        <div class="row g-3">
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-database text-primary"></i> Clear application cache</h5>
                        <p class="card-text small text-muted">Clears the application cache (e.g. cached config/data).</p>
                        <button type="button" class="btn-admin btn-admin-primary btn-cache-action" data-action="cache">
                            <i class="fas fa-broom"></i> Clear cache
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-file-code text-info"></i> Clear view cache</h5>
                        <p class="card-text small text-muted">Removes compiled Blade views so they are recompiled.</p>
                        <button type="button" class="btn-admin btn-admin-primary btn-cache-action" data-action="view">
                            <i class="fas fa-broom"></i> Clear views
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-tachometer-alt text-success"></i> Clear optimize cache</h5>
                        <p class="card-text small text-muted">Clears bootstrap/cache (config, routes, views, cache).</p>
                        <button type="button" class="btn-admin btn-admin-primary btn-cache-action" data-action="optimize">
                            <i class="fas fa-broom"></i> Optimize clear
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-cog text-secondary"></i> Clear config cache</h5>
                        <p class="card-text small text-muted">Removes cached configuration files.</p>
                        <button type="button" class="btn-admin btn-admin-secondary btn-cache-action" data-action="config">
                            <i class="fas fa-broom"></i> Config clear
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-route text-secondary"></i> Clear route cache</h5>
                        <p class="card-text small text-muted">Removes cached route definitions.</p>
                        <button type="button" class="btn-admin btn-admin-secondary btn-cache-action" data-action="route">
                            <i class="fas fa-broom"></i> Route clear
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border border-warning">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-redo text-warning"></i> Clear all</h5>
                        <p class="card-text small text-muted">Runs cache, view, config, route, and optimize clear.</p>
                        <button type="button" class="btn-admin btn-admin-warning btn-cache-action" data-action="all">
                            <i class="fas fa-broom"></i> Clear all
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div id="cache-result" class="mt-4" style="display: none;">
            <div class="alert alert-dismissible fade show" role="alert">
                <strong id="cache-result-title"></strong>
                <pre id="cache-result-body" class="mb-0 mt-2 small"></pre>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
(function() {
    const H = window.adminHelpers;
    const resultEl = document.getElementById('cache-result');
    const resultTitle = document.getElementById('cache-result-title');
    const resultBody = document.getElementById('cache-result-body');

    document.querySelectorAll('.btn-cache-action').forEach(function(btn) {
        btn.addEventListener('click', async function() {
            const action = this.dataset.action;
            const label = this.closest('.card').querySelector('.card-title').textContent.trim();
            this.disabled = true;
            if (this.innerHTML.indexOf('fa-spinner') === -1) {
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Running...';
            }
            try {
                const data = await H.apiFetch('/admin/cache/clear', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: action })
                });
                resultEl.style.display = 'block';
                const alertBox = resultEl.querySelector('.alert');
                alertBox.className = 'alert alert-dismissible fade show ' + (data.status === 'success' ? 'alert-success' : 'alert-warning');
                resultTitle.textContent = data.status === 'success' ? 'Done' : 'Completed with issues';
                resultBody.textContent = (data.results && (data.results.frontend || data.results.backend))
                    ? 'Frontend:\n' + JSON.stringify(data.results.frontend || {}, null, 2) + '\n\nBackend:\n' + JSON.stringify(data.results.backend || {}, null, 2)
                    : JSON.stringify(data.results || data.message, null, 2);
            } catch (err) {
                resultEl.style.display = 'block';
                resultEl.querySelector('.alert').className = 'alert alert-danger alert-dismissible fade show';
                resultTitle.textContent = 'Error';
                resultBody.textContent = err.message || 'Request failed';
            }
            this.disabled = false;
            const origText = action === 'all' ? 'Clear all' : ('Clear ' + (action === 'view' ? 'views' : action));
            this.innerHTML = '<i class="fas fa-broom"></i> ' + origText;
        });
    });
})();
</script>
@endsection
