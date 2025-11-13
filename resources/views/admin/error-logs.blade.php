@extends('admin.layouts.app')

@section('title', 'Error Logs')

@section('page-title', 'Error Logs')

@section('content')
<div class="admin-card">
    <div class="admin-card-header">
        <h3>Error Logs</h3>
        <button class="btn-admin btn-admin-danger btn-admin-sm" onclick="clearAllLogs()">
            <i class="fas fa-trash"></i> Clear All
        </button>
    </div>
    <div class="admin-card-body">
        <div class="row mb-3">
            <div class="col-md-3">
                <select id="filter-level" class="form-select" onchange="loadErrorLogs()">
                    <option value="">All Levels</option>
                    <option value="error">Error</option>
                    <option value="warning">Warning</option>
                    <option value="info">Info</option>
                    <option value="debug">Debug</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="date" id="filter-date-from" class="form-control" onchange="loadErrorLogs()">
            </div>
            <div class="col-md-3">
                <input type="date" id="filter-date-to" class="form-control" onchange="loadErrorLogs()">
            </div>
            <div class="col-md-3">
                <input type="text" id="search-logs" class="form-control" placeholder="Search..." onkeyup="searchLogs()">
            </div>
        </div>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Level</th>
                        <th>Message</th>
                        <th>User</th>
                        <th>URL</th>
                        <th>IP Address</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="logs-table-body">
                    <tr>
                        <td colspan="8" class="text-center">
                            <div class="spinner"></div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div id="logs-pagination"></div>
    </div>
</div>

<!-- Log Detail Modal -->
<div class="modal fade" id="logDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Error Log Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="log-detail-content">
                    <!-- Log details will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-admin btn-admin-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('/assets/admin/js/error-logs.js') }}"></script>
@endsection

