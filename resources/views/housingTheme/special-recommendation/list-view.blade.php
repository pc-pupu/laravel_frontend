@extends('housingTheme.layouts.app')

@section('title', 'Edit Special Recommendation List')

@section('content')
<div class="cms-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="cms-card">
                <div class="cms-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3><i class="fa fa-edit me-2"></i> Edit Special Recommendation List</h3>
                            <p class="mb-0">Drag applicant's name up or down to edit the list</p>
                        </div>
                        <a href="{{ route('dashboard') }}" class="btn btn-light">
                            <i class="fa fa-arrow-left me-2"></i> Back to Dashboard
                        </a>
                    </div>
                </div>

                @include('housingTheme.partials.alerts')

                <div class="mb-3">
                    <span style="font-size:18px; color:blue;">*Drag applicant's name up or down to edit the list</span>
                </div>

                @if(count($applications) > 0)
                    <form method="POST" action="{{ route('special-recommendation.update-priority') }}" id="priority-form">
                        @csrf
                        <input type="hidden" name="online_application_ids" id="online_app_ids" value="">

                        <div class="table-responsive" style="overflow-x: visible;">
                            <table class="table table-list table-striped" id="sortable">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Application No.</th>
                                        <th>Date of Application</th>
                                        <th>Computer Serial NO.</th>
                                        <th>Allotment Reason</th>
                                        <th>Flat Type</th>
                                        <th>Approval/Rejection Date</th>
                                        <th>Priority Order</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($applications as $index => $app)
                                        <tr data-id="{{ $app['online_application_id'] }}" data-priority="{{ $app['priority_order'] ?? ($index + 1) }}">
                                            <td>{{ $app['applicant_name'] ?? 'N/A' }}</td>
                                            <td>{{ $app['application_no'] ?? 'N/A' }}</td>
                                            <td>{{ $app['date_of_application'] ? date('d/m/Y', strtotime($app['date_of_application'])) : 'N/A' }}</td>
                                            <td>{{ $app['computer_serial_no'] ?? 'N/A' }}</td>
                                            <td>{{ $app['allotment_category'] ?? 'N/A' }}</td>
                                            <td>{{ $app['flat_type'] ?? 'N/A' }}</td>
                                            <td>{{ $app['date_of_verified'] ? date('d/m/Y', strtotime($app['date_of_verified'])) : 'N/A' }}</td>
                                            <td class="priority-order-cell">{{ $app['priority_order'] ?? ($index + 1) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-6">
                                <button type="submit" class="btn bg-primary btn-sm px-5 rounded-pill text-white fw-bolder" id="savebtn">
                                    <i class="fa fa-save me-2"></i> Submit
                                </button>
                            </div>
                        </div>
                    </form>
                @else
                    <div class="table-responsive">
                        <table class="datatable_no_data_found table table-list">
                            <tr class="tr_no_data_found">
                                <th class="th_no_data_found"></th>
                            </tr>
                            <tr class="tr_no_data_found">
                                <td class="td_no_data_found">No data found!</td>
                            </tr>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/housingTheme/css/jquery-ui.css') }}">
<style>
    #sortable tbody tr {
        cursor: move;
    }
    #sortable tbody tr.ui-sortable-helper {
        background-color: #e3f2fd;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        opacity: 0.9;
    }
    #sortable tbody tr.ui-sortable-placeholder {
        height: 50px;
        background-color: #f5f5f5;
        border: 2px dashed #2196F3;
        visibility: visible !important;
        display: table-row !important;
    }
    #sortable tbody tr:hover {
        background-color: #f5f5f5;
    }
    .priority-order-cell {
        font-weight: bold;
        color: #2196F3;
    }
</style>
@endpush

@push('scripts')
<script>
    // Load jQuery UI and then initialize
    (function() {
        var script = document.createElement('script');
        script.src = "{{ asset('assets/housingTheme/jquery/jquery-ui.min.js') }}";
        script.onload = function() {
            jQuery(document).ready(function($) {
                initSortable();
            });
        };
        script.onerror = function() {
            console.error('Failed to load jQuery UI');
            alert('Failed to load jQuery UI library. Please refresh the page.');
        };
        document.head.appendChild(script);
        
        function initSortable() {
            if (typeof jQuery === 'undefined' || typeof jQuery.fn === 'undefined' || typeof jQuery.fn.sortable === 'undefined') {
                console.error('jQuery UI Sortable plugin is not available');
                setTimeout(function() {
                    if (typeof jQuery !== 'undefined' && typeof jQuery.fn !== 'undefined' && typeof jQuery.fn.sortable !== 'undefined') {
                        initSortable();
                    } else {
                        alert('Drag and drop functionality is not available. Please refresh the page.');
                    }
                }, 500);
                return;
            }

            var $ = jQuery;
            var $sortableTable = $("#sortable tbody");
            
            if ($sortableTable.length === 0) {
                console.error('Table #sortable tbody not found');
                return;
            }
            
            var $rows = $sortableTable.find("tr");
            if ($rows.length === 0) {
                console.error('No rows found in table');
                return;
            }

            // Initialize sortable
            $sortableTable.sortable({
                items: "> tr",
                helper: function(e, tr) {
                    var $originals = tr.children();
                    var $helper = tr.clone();
                    $helper.children().each(function(index) {
                        $(this).width($originals.eq(index).width());
                    });
                    $helper.css({
                        'display': 'table-row',
                        'background-color': '#e3f2fd'
                    });
                    return $helper;
                },
                placeholder: "ui-sortable-placeholder",
                cursor: "move",
                opacity: 0.8,
                tolerance: "pointer",
                axis: "y",
                containment: "parent",
                start: function(event, ui) {
                    ui.placeholder.height(ui.item.height());
                    ui.placeholder.css('display', 'table-row');
                },
                change: function(event, ui) {
                    updatePriorityOrderDisplay();
                },
                update: function(event, ui) {
                    updateOrder();
                    updatePriorityOrderDisplay();
                }
            });
            
            // Disable text selection while dragging
            $sortableTable.disableSelection();

            function updateOrder() {
                var ids = [];
                $("#sortable tbody tr").each(function() {
                    var id = $(this).data('id');
                    if (id) {
                        ids.push(id);
                    }
                });
                $("#online_app_ids").val(ids.join(','));
            }

            function updatePriorityOrderDisplay() {
                $("#sortable tbody tr").each(function(index) {
                    var newPriority = index + 1;
                    $(this).find('.priority-order-cell').text(newPriority);
                    $(this).data('priority', newPriority);
                });
            }

            // Initialize on page load
            updateOrder();
            updatePriorityOrderDisplay();
            
            console.log('Sortable initialized successfully');
        }
    })();
</script>
@endpush
