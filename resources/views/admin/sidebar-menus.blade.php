@extends('admin.layouts.app')

@section('title', 'Sidebar Menus Management')
@section('page-title', 'Sidebar Menus Management')

@section('content')

<div class="admin-card">
    <div class="admin-card-header">
        <h3>Sidebar Menus</h3>
        <button class="btn-admin btn-admin-primary btn-admin-sm"
                data-bs-toggle="modal"
                data-bs-target="#menuModal"
                onclick="openMenuModal()">
            <i class="fas fa-plus"></i> Add Menu
        </button>
    </div>

    <div class="admin-card-body">

        {{-- Search --}}
        <div class="mb-3">
            <input type="text"
                   id="search-menus"
                   class="form-control"
                   placeholder="Search menus..."
                   onkeyup="searchMenus()">
        </div>

        {{-- Menus Table --}}
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Menu Name</th>
                        <th>Route/URL</th>
                        <th>Icon</th>
                        <th>Parent</th>
                        <th>Order</th>
                        <th>Roles</th>
                        <th>Status</th>
                        <th style="width: 160px;">Actions</th>
                    </tr>
                </thead>
                <tbody id="menus-table-body">
                    <tr>
                        <td colspan="9" class="text-center">
                            <div class="spinner"></div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
</div>


<!-- =============================== -->
<!-- Menu Modal -->
<!-- =============================== -->
<div class="modal fade" id="menuModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="menuModalTitle">Add Menu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <form id="menuForm">
                    <input type="hidden" id="menu-id" name="id">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-2">
                                <label class="form-label">Menu Name *</label>
                                <input type="text" id="menu-name" name="menu_name"
                                       class="form-control" required
                                       oninput="clearFieldError('menu-name')">
                                <div class="invalid-feedback" id="menu-name-error"></div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-2">
                                <label class="form-label">Icon Class</label>
                                <input type="text" id="menu-icon" name="icon_class"
                                       class="form-control"
                                       placeholder="fa fa-home fa-lg"
                                       oninput="clearFieldError('menu-icon')">
                                <small class="text-muted">e.g., fa fa-home fa-lg</small>
                                <div class="invalid-feedback" id="menu-icon-error"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-2">
                                <label class="form-label">Route Name</label>
                                <input type="text" id="menu-route" name="route_name"
                                       class="form-control"
                                       placeholder="dashboard"
                                       oninput="clearFieldError('menu-route')">
                                <small class="text-muted">Laravel route name (optional)</small>
                                <div class="invalid-feedback" id="menu-route-error"></div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-2">
                                <label class="form-label">URL</label>
                                <input type="text" id="menu-url" name="url"
                                       class="form-control"
                                       placeholder="/dashboard or https://example.com"
                                       oninput="clearFieldError('menu-url')">
                                <small class="text-muted">Direct URL (if route not used)</small>
                                <div class="invalid-feedback" id="menu-url-error"></div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-2">
                        <label class="form-label">Route Pattern (for parameter inputs)</label>
                        <input type="text" id="menu-route-pattern" 
                               class="form-control"
                               placeholder="e.g., view_application_list/{status}/{url}/{page_status}"
                               oninput="updateRouteParamsFromPattern()">
                        <small class="text-muted">
                            Enter route pattern to generate parameter input fields.<br>
                            Example: <code>view_application_list/{status}/{url}/{page_status}</code><br>
                            <strong>Note:</strong> This is only for generating input fields. Make sure Route Name above matches this route.
                        </small>
                        <div class="invalid-feedback" id="menu-route-pattern-error"></div>
                    </div>
                    
                    <div class="form-group mb-2">
                        <label class="form-label">Route Parameter Values</label>
                        <div id="route-params-inputs" class="mb-2">
                            <!-- Dynamic inputs will be added here based on pattern -->
                        </div>
                        <textarea id="menu-route-params" name="route_params"
                                  class="form-control" rows="3"
                                  placeholder='{"status": "applied", "url": "new-apply", "page_status": "action-list"}'
                                  oninput="clearFieldError('menu-route-params')"
                                  style="display: none;"></textarea>
                        <small class="text-muted">
                            Values will be automatically encrypted when generating menu URLs.<br>
                            Example: <code>status: applied</code> will become <code>lr2xuMiemw%3D%3D</code> in URL.
                        </small>
                        <div class="invalid-feedback" id="menu-route-params-error"></div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-2">
                                <label class="form-label">Parent Menu</label>
                                <select id="menu-parent" name="parent_id" class="form-control"
                                        onchange="clearFieldError('menu-parent')">
                                    <option value="">-- None (Top Level) --</option>
                                </select>
                                <div class="invalid-feedback" id="menu-parent-error"></div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-2">
                                <label class="form-label">Order No</label>
                                <input type="number" id="menu-order" name="order_no"
                                       class="form-control" value="0" min="0"
                                       oninput="clearFieldError('menu-order')">
                                <small class="text-muted">Lower numbers appear first</small>
                                <div class="invalid-feedback" id="menu-order-error"></div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-2">
                        <label class="form-label">Assign to Roles *</label>
                        <div id="roles-list"
                             style="max-height: 200px; overflow-y: auto;
                                    border: 1px solid #dee2e6;
                                    padding: 15px; border-radius: 5px;">
                            <!-- Loaded dynamically -->
                        </div>
                        <div class="invalid-feedback d-block" id="roles-error"></div>
                    </div>

                    <div class="form-group mb-2">
                        <label class="form-label">Status</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="is_active" id="menu-active-1" value="1" checked>
                                <label class="form-check-label" for="menu-active-1">Active</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="is_active" id="menu-active-0" value="0">
                                <label class="form-check-label" for="menu-active-0">Inactive</label>
                            </div>
                        </div>
                    </div>

                </form>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn-admin btn-admin-secondary"
                        data-bs-dismiss="modal">Cancel</button>

                <button type="button" class="btn-admin btn-admin-primary"
                        onclick="saveMenu()">
                    Save Menu
                </button>
            </div>

        </div>
    </div>
</div>

@endsection


{{-- ============================ --}}
{{-- Scripts --}}
{{-- ============================ --}}
@section('scripts')
    <script src="{{ asset('/assets/admin/js/sidebar-menus.js') }}"></script>
@endsection

