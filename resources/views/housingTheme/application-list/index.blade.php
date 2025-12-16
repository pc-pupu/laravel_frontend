@extends('housingTheme.layouts.app')

@section('title', 'Application List')

@section('content')
<div class="cms-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="cms-card">
                <div class="cms-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3><i class="fa fa-list me-2"></i> My Applications</h3>
                            <p class="mb-0">View all your submitted applications</p>
                        </div>
                        <a href="{{ route('dashboard') }}" class="btn btn-light">
                            <i class="fa fa-arrow-left me-2"></i> Back to Dashboard
                        </a>
                    </div>
                </div>

                @include('housingTheme.partials.alerts')

                <div class="table-responsive">
                    <table class="table table-list table-striped" id="application-list-table">
                        <thead>
                            <tr>
                                <th>Application Type</th>
                                <th>Application No.</th>
                                <th>Date of Application</th>
                                <th>Status</th>
                                <th>Verification Date</th>
                                <th>View Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($applications as $app)
                                <tr>
                                    <td>
                                        @if($app['application_type'] == 'new-apply')
                                            Application for New Allotment
                                        @elseif($app['application_type'] == 'vs')
                                            Application for Vertical Shifting
                                        @elseif($app['application_type'] == 'cs')
                                            Application for Category Shifting
                                        @elseif($app['application_type'] == 'license')
                                            Application for License
                                        @else
                                            Application for {{ ucfirst($app['application_type']) }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($app['status'] == 'draft')
                                            -
                                        @else
                                            {{ $app['application_no'] }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($app['date_of_application'])
                                            {{ date('d/m/Y', strtotime($app['date_of_application'])) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $statusDescription = $app['status_description'] ?? $app['status'];
                                        @endphp
                                        {{ $statusDescription }}
                                    </td>
                                    <td>
                                        @if($app['date_of_verified'])
                                            {{ date('d/m/Y', strtotime($app['date_of_verified'])) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($app['status'] != 'draft')
                                            <a href="{{ route('application.view', ['id' => \App\Helpers\UrlEncryptionHelper::encryptUrl($app['online_application_id'])]) }}" 
                                               target="_blank" 
                                               class="btn btn-sm btn-primary">
                                                <i class="fa fa-eye"></i> View
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">
                                        <div class="py-4">
                                            <p class="text-muted">No applications found!</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTable if available
        if ($.fn.DataTable) {
            $('#application-list-table').DataTable({
                order: [[2, 'desc']], // Sort by date of application
                pageLength: 10,
                language: {
                    emptyTable: "No applications found!"
                }
            });
        }
    });
</script>
@endpush

