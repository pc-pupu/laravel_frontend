@extends('housingTheme.layouts.app')

@section('title', 'User Tagging Requests')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h3 class="mb-4">User Tagging Requests</h3>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error') || isset($error))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') ?? $error }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    @if(count($data) > 0)
                    <div class="table-responsive flatWiseUser">
                        <table class="table table-striped table-bordered">
                            <thead class="table-primary">
                                <tr>
                                    <th>Sl. No.</th>
                                    <th>Estate Name</th>
                                    <th>Flat Info</th>
                                    <th>HRMS ID</th>
                                    <th>Occupant Name</th>
                                    <th>Status</th>
                                    <th>Remarks</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item['estate_name'] }}</td>
                                    <td>
                                        Block - {{ $item['block_name'] }},<br>
                                        Flat Type - {{ $item['flat_type'] }},<br>
                                        Floor - {{ $item['floor'] }},<br>
                                        Flat No. - {{ $item['flat_no'] }}
                                    </td>
                                    <td>{{ $item['hrms_id'] }}</td>
                                    <td>{{ $item['applicant_name'] }}</td>
                                    <td>
                                        @if($item['flag'] == 'pending')
                                            <span class="badge bg-warning">{{ ucwords($item['flag']) }}</span>
                                        @else
                                            <span class="badge bg-success">{{ ucwords($item['flag']) }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $item['remarks'] ?? '-' }}</td>
                                    <td>
                                        <a href="{{ route('user-tagging.flat-wise-user-details', \App\Helpers\UrlEncryptionHelper::encryptUrl($item['flat_id'])) }}" 
                                            class="btn btn-sm btn-success rounded-pill px-3">Details</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-info text-center">
                        No data found!
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

