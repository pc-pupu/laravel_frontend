@extends('admin.layouts.app')

@section('title', 'Error Logs')
@section('page-title', 'Error Logs')

@section('content')

<div class="admin-card">
    <div class="admin-card-header">
        <h3>Error Logs</h3>

        <div class="d-flex gap-2 flex-wrap">
            <button class="btn-admin btn-admin-danger btn-admin-sm" onclick="clearAllLogs()">
                <i class="fas fa-trash"></i> Clear All
            </button>
        </div>
    </div>

    <div class="admin-card-body">

        {{-- Clear by time --}}
        <div class="card mb-4 border border-warning">
            <div class="card-header bg-light py-2">
                <strong><i class="fas fa-broom"></i> Clear errors by time</strong>
            </div>
            <div class="card-body py-3">
                <div class="row align-items-end g-2">
                    <div class="col-auto">
                        <label class="form-label small mb-0">Older than</label>
                        <select id="clear-older-than-days" class="form-select form-select-sm" style="width: auto;">
                            <option value="">— Select —</option>
                            <option value="1">1 day</option>
                            <option value="7">7 days</option>
                            <option value="30">30 days</option>
                            <option value="90">90 days</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <span class="text-muted small">or</span>
                    </div>
                    <div class="col-auto">
                        <label class="form-label small mb-0">From date</label>
                        <input type="date" id="clear-date-from" class="form-control form-control-sm">
                    </div>
                    <div class="col-auto">
                        <label class="form-label small mb-0">To date</label>
                        <input type="date" id="clear-date-to" class="form-control form-control-sm">
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn-admin btn-admin-warning btn-admin-sm" onclick="clearByTime()">
                            <i class="fas fa-broom"></i> Clear by time
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="row mb-3">

            <div class="col-md-3 mb-2">
                <select id="filter-level"
                        class="form-select"
                        onchange="loadErrorLogs()">
                    <option value="">All Levels</option>
                    <option value="error">Error</option>
                    <option value="warning">Warning</option>
                    <option value="info">Info</option>
                    <option value="debug">Debug</option>
                </select>
            </div>

            <div class="col-md-3 mb-2">
                <input type="date"
                       id="filter-date-from"
                       class="form-control"
                       onchange="loadErrorLogs()">
            </div>

            <div class="col-md-3 mb-2">
                <input type="date"
                       id="filter-date-to"
                       class="form-control"
                       onchange="loadErrorLogs()">
            </div>

            <div class="col-md-3 mb-2">
                <input type="text"
                       id="search-logs"
                       class="form-control"
                       placeholder="Search..."
                       onkeyup="searchLogs()">
            </div>

        </div>

        {{-- Logs Table --}}
        <div class="table-responsive">
            <table class="admin-table" data-disable-listing-tools="1">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Level</th>
                        <th>Error type</th>
                        <th>Message</th>
                        <th>User</th>
                        <th>Time</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>

                <tbody id="logs-table-body">
                    <tr>
                        <td colspan="7" class="text-center">
                            <div class="spinner"></div>
                        </td>
                    </tr>
                </tbody>

            </table>
        </div>

        {{-- Pagination --}}
        <div id="logs-pagination" class="mt-3"></div>

    </div>
</div>


<!-- ===================================== -->
<!-- Log Detail Modal -->
<!-- ===================================== -->
<div class="modal fade" id="logDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Error Log Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div id="log-detail-content">
                    <!-- Dynamically loaded -->
                </div>
            </div>

            <div class="modal-footer">
                <button type="button"
                        class="btn-admin btn-admin-secondary"
                        data-bs-dismiss="modal">
                    Close
                </button>
            </div>

        </div>
    </div>
</div>

@endsection


{{-- ============================ --}}
{{-- Correct Script Order --}}
{{-- ============================ --}}
@section('scripts')
    {{-- <script src="{{ asset('/assets/admin/js/admin.js') }}"></script> --}}
    <script src="{{ asset('/assets/admin/js/error-logs.js') }}"></script>
@endsection
