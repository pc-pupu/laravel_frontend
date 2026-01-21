@extends('housingTheme.layouts.app')

@section('title', 'View Allotment Information')

@section('content')
    <div class="cms-wrapper">
        <div class="row">
            <div class="col-md-12">
                <div class="cms-card">
                    <div class="cms-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3><i class="fa fa-list me-2"></i> Add Block</h3>
                                <p class="mb-0">Add new block in RHE</p>
                            </div>
                            <a href="{{ route('dashboard') }}" class="btn btn-light">
                                <i class="fa fa-arrow-left me-2"></i> Back to Dashboard
                            </a>
                        </div> 
                    </div>
                    <div class="cms-body">
                        <form method="POST" action="{{ route('blocks.store') }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="block_name" class="form-label">Block Name</label>
                                        <input type="text" name="block_name" id="block_name" class="form-control" placeholder="Enter Block Name" required>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Add Block</button>
                        </form>
                        @if(session('success'))
                            <div class="alert alert-success mt-3">
                                {{ session('success') }}
                            </div>
                        @endif 
                    </div>
                </div>
            </div>
        </div>
    </div>  
@endsection


                




