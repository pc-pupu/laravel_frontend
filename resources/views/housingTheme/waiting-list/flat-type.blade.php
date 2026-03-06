@extends('housingTheme.layouts.app')

@section('title', 'Flat Type Wise Waiting List')

@section('content')
<div class="cms-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="cms-card">
                <div class="cms-header d-flex justify-content-between align-items-center">
                    <div>
                        <h3><i class="fa fa-list-ol me-2"></i> Flat Type Wise Waiting List</h3>
                        <p class="mb-0">View waiting list of applications approved by Housing Approver, grouped by flat type.</p>
                    </div>
                    <a href="{{ route('dashboard') }}" class="btn btn-light">
                        <i class="fa fa-arrow-left me-2"></i> Back to Dashboard
                    </a>
                </div>

                @include('housingTheme.partials.alerts')

                @if(!empty($error))
                    <div class="alert alert-danger">{{ $error }}</div>
                @endif

                <div class="cms-body">
                    <form method="GET" action="{{ url('flat_type_waiting_list') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <select name="flat_type_id" id="flat_type_id" class="form-select">
                                        <option value="0">- Select Flat Type -</option>
                                        @foreach($flatTypes as $id => $name)
                                            <option value="{{ $id }}" {{ (int)$selectedFlatTypeId === (int)$id ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="flat_type_id">Flat Type</label>
                                </div>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn bg-primary btn-sm px-5 rounded-pill text-white fw-bolder">
                                    <i class="fa fa-search me-2"></i> View List
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        @if(!empty($rows) && count($rows) > 0)
                            <table class="table table-list table-striped table-hover table-bordered">
                                <thead>
                                    <tr class="table-primary">
                                        <th>Waiting No.</th>
                                        <th>Applicant Name</th>
                                        <th>Application No.</th>
                                        <th>Flat Type</th>
                                        <th>Allotment Category</th>
                                        <th>Grade Pay</th>
                                        <th>Computer Serial No.</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rows as $row)
                                        <tr>
                                            <td>{{ $row['waiting_no'] }}</td>
                                            <td>{{ $row['applicant_name'] }}</td>
                                            <td>{{ $row['application_no'] }}</td>
                                            <td>{{ $row['flat_type'] }}</td>
                                            <td>{{ $row['allotment_category'] ?? 'N/A' }}</td>
                                            <td>{{ $row['grade_pay'] ?? 'N/A' }}</td>
                                            <td>{{ $row['computer_serial_no'] }}</td>
                                            <td>
                                                @if(!empty($row['encrypted_online_application_id']))
                                                    <a href="{{ url('/view-application-details/' . $row['encrypted_online_application_id']) }}"
                                                       class="btn btn-outline-primary btn-sm px-4 rounded-pill fw-bolder">
                                                        View Application
                                                    </a>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <table class="datatable_no_data_found table table-list">
                                <tr class="tr_no_data_found">
                                    <th class="th_no_data_found"></th>
                                </tr>
                                <tr class="tr_no_data_found">
                                    <td class="td_no_data_found">
                                        @if((int)$selectedFlatTypeId === 0)
                                            Please select a flat type to view the waiting list.
                                        @else
                                            No data found for the selected flat type.
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

