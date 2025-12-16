@extends('housingTheme.layouts.app')

@section('title', 'Online Application')

@php
    $applications = [
        'new-apply'     => 'New Allotment',
        'vs'            => 'Floor Shifting',
        'cs'            => 'Category Shifting',
        'new_license'   => 'New Licence',
        'vs_licence'    => 'Floor Shifting Licence',
        'cs_licence'    => 'Category Shifting Licence',
        'renew_license' => 'Renew Licence',
    ];

    $statusColors = [
        'draft'  => 'warning',
        'reject' => 'danger',
    ];

    $legend = [
        'primary' => 'Currently Selected',
        'info'    => 'Not Applied',
        'warning' => 'Save as Draft',
        'success' => 'Applied',
        'danger'  => 'Reject',
    ];

    $selected = $selected ?? 'new-apply';
    $statuses = $statuses ?? [];
@endphp

@section('content')
<div class="cms-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="cms-card">
                <div class="cms-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3><i class="fa fa-list me-2"></i> Online Application</h3>
                            <p class="mb-0">Select an application type to proceed</p>
                        </div>
                        <a href="{{ route('dashboard') }}" class="btn btn-light">
                            <i class="fa fa-arrow-left me-2"></i> Back to Dashboard
                        </a>
                    </div>
                </div>

                <div class="cms-body">
                    <div class="row gy-3">
                        @foreach($applications as $path => $label)
                            @php
                                $statusData = $statuses[$path] ?? [];
                                $applied = $statusData['applied'] ?? false;
                                $status = $statusData['status'] ?? null;

                                if ($path === $selected) {
                                    $color = 'primary';
                                } elseif (!$applied) {
                                    $color = 'info';
                                } elseif ($status === 'draft') {
                                    $color = 'warning';
                                } elseif ($status === 'reject') {
                                    $color = 'danger';
                                } else {
                                    $color = 'success';
                                }

                                $encrypted = \App\Helpers\UrlEncryptionHelper::encryptUrl($path);
                            @endphp
                            <div class="col-md-4">
                                <a href="{{ route('online_application', ['url' => $encrypted]) }}"
                                   class="btn btn-{{ $color }} w-100 text-start shadow-sm">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold">{{ $label }}</span>
                                        @if($applied && !empty($status))
                                            <small class="text-dark text-opacity-75">{{ ucfirst(str_replace('_', ' ', $status)) }}</small>
                                        @else
                                            <small class="text-dark text-opacity-50">Not Applied</small>
                                        @endif
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>

                    <hr class="my-4">

                    @php
                        $targetUrl = $selected;
                    @endphp
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">
                                @switch($selected)
                                    @case('new-apply') Go to Application page for New Allotment @break
                                    @case('vs') Go to Application page for Floor Shifting @break
                                    @case('cs') Go to Application page for Category Shifting @break
                                    @case('new_license') Go to Application page for New Licence @break
                                    @case('vs_licence') Go to Application page for Floor Shifting Licence @break
                                    @case('cs_licence') Go to Application page for Category Shifting Licence @break
                                    @case('renew_license') Go to Application page for Renew Licence @break
                                    @default Go to Application page
                                @endswitch
                            </h5>
                            <p class="mb-0 text-muted">Selected: {{ $applications[$selected] ?? 'Application' }}</p>
                        </div>
                        <a href="{{ url($targetUrl) }}" target="_blank" class="btn btn-outline-primary">
                            Proceed <i class="fa fa-external-link ms-2"></i>
                        </a>
                    </div>

                    <hr class="my-4">

                    <div class="row g-3">
                        @foreach($legend as $color => $text)
                            <div class="col-md-3 d-flex align-items-center">
                                <span class="badge bg-{{ $color }} me-2" style="width:24px;height:16px;">&nbsp;</span>
                                <span>{{ $text }}.</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

