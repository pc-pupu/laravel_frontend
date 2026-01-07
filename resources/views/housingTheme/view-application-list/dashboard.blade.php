@extends('housingTheme.layouts.app')

@section('title', 'View Application List Dashboard')

@section('content')
<div class="cms-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="cms-card">
                <div class="cms-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3><i class="fa fa-dashboard me-2"></i> Application List Dashboard</h3>
                            <p class="mb-0">View application statistics</p>
                        </div>
                        <a href="{{ route('dashboard') }}" class="btn btn-light">
                            <i class="fa fa-arrow-left me-2"></i> Back to Dashboard
                        </a>
                    </div>
                </div>

                @include('housingTheme.partials.alerts')

                <div class="cms-body">
                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="counter-box p-3 rounded mb-3 position-relative color-box1">
                                <span class="counter">{{ $actionCount ?? 0 }}</span>
                                <p>Action List</p>
                                @if($actionCount>0)
                                    <a href="{{ route('view_application', [
                                        'status' => \App\Helpers\UrlEncryptionHelper::encryptUrl($status),
                                        'entity' => \App\Helpers\UrlEncryptionHelper::encryptUrl($url),
                                        'page_status' => 'action-list'
                                    ]) }}" class="badge rounded-pill text-bg-success">View Details</a>
                                    <img src="{{ asset('assets/housingTheme/images/allotment-icon.png') }}" class="position-absolute end-0 counter-box-icon top-0" />
                                @else
                                    <span class="badge rounded-pill text-bg-secondary">No Details Available</span>    
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="counter-box p-3 rounded mb-3 position-relative color-box2">
                                <i class="fa fa-group"></i>
                                <span class="counter">{{ $verifiedCount ?? 0 }}</span>
                                <p>Verified List</p>
                                @if(isset($verifiedStatus) && $verifiedStatus)
                                    <a href="{{ route('view_application', [
                                        'status' => \App\Helpers\UrlEncryptionHelper::encryptUrl($verifiedStatus),
                                        'entity' => \App\Helpers\UrlEncryptionHelper::encryptUrl($url),
                                        'page_status' => 'verified-list'
                                    ]) }}" class="badge rounded-pill text-bg-success">View Details</a>
                                @else
                                    <span class="badge rounded-pill text-bg-secondary">No Details Available</span>    
                                @endif
                                <img src="{{ asset('assets/housingTheme/images/floor-icon.png') }}" class="position-absolute end-0 counter-box-icon top-0" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="counter-box p-3 rounded mb-3 position-relative color-box3">
                                <i class="fa fa-shopping-cart"></i>
                                <span class="counter">{{ $rejectedCount ?? 0 }}</span>
                                <p>Rejected List</p>
                                @if(isset($rejectedStatus) && $rejectedStatus)
                                    <a href="{{ route('view_application', [
                                        'status' => \App\Helpers\UrlEncryptionHelper::encryptUrl($rejectedStatus),
                                        'entity' => \App\Helpers\UrlEncryptionHelper::encryptUrl($url),
                                        'page_status' => 'reject-list'
                                    ]) }}" class="badge rounded-pill text-bg-success">View Details</a>
                                @else
                                    <span class="badge rounded-pill text-bg-secondary">No Details Available</span> 
                                @endif
                                <img src="{{ asset('assets/housingTheme/images/allotment-icon.png') }}" class="position-absolute end-0 counter-box-icon top-0" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

