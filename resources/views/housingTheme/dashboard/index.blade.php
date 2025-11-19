@extends('housingTheme.layouts.app')

@section('title', 'Dashboard')
@section('page-header', 'Dashboard')

@section('content')
<div class="dashboard-wrapper p-4">
    <div class="dashboard-welcome-card mb-4">
        <div class="dashboard-welcome-text">
            <h2>Welcome to e-Allotment of Rental Housing Estate</h2>
            <div class="dashboard-user-details mt-3">
                <p><strong>Name:</strong> {{ $user['name'] ?? 'N/A' }}</p>
                <p><strong>Designation:</strong> {{ $user['designation'] ?? 'N/A' }}</p>
                <p><strong>Office:</strong> Housing Department</p>
                <p><strong>Mobile Number:</strong> {{ $user['mobile'] ?? 'N/A' }}</p>
                <p><strong>Email:</strong> {{ $user['mail'] ?? $user['email'] ?? 'N/A' }}</p>
            </div>
        </div>
        <img src="{{ asset('/themes/dashboard-theme/images/profile_icon.png') }}"
             alt="Dashboard Illustration"
             class="dashboard-illustration">
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="dashboard-stat-card" style="background: linear-gradient(135deg, #5a67d8, #805ad5);">
                <div class="dashboard-stat-value">{{ number_format($stats['existing_with_hrms']) }}</div>
                <div class="dashboard-stat-label">Existing Occupant (with HRMS)</div>
                <a href="{{ route('existing-occupant.with-hrms') }}" class="btn dashboard-btn-view">View Details</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="dashboard-stat-card" style="background: linear-gradient(135deg, #38b2ac, #4299e1);">
                <div class="dashboard-stat-value">{{ number_format($stats['existing_without_hrms']) }}</div>
                <div class="dashboard-stat-label">Existing Occupant (without HRMS)</div>
                <a href="{{ route('existing-occupant.without-hrms') }}" class="btn dashboard-btn-view">View Details</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="dashboard-stat-card" style="background: linear-gradient(135deg, #ed64a6, #f56565);">
                <div class="dashboard-stat-value">{{ number_format($stats['cms_items']) }}</div>
                <div class="dashboard-stat-label">Total CMS Records</div>
                <a href="{{ route('cms-content.index') }}" class="btn dashboard-btn-view">View Details</a>
            </div>
        </div>
    </div>
</div>
@endsection

