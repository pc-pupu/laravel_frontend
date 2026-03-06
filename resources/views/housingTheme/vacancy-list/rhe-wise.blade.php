@extends('housingTheme.layouts.app')

@section('title', 'RHE Flat Vacancy List (RHE Wise)')

@section('content')
<div class="cms-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="cms-card">
                <div class="cms-header d-flex justify-content-between align-items-center">
                    <div>
                        <h3><i class="fa fa-building-o me-2"></i> RHE Flat Vacancy List (RHE Wise)</h3>
                        <p class="mb-0">View vacancy list of flats for a specific RHE and flat type.</p>
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
                    <form method="GET" action="{{ url('rhe_vacancy_list') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <select name="estate_id" id="estate_id" class="form-select" required>
                                        <option value="0">- Select RHE -</option>
                                        @foreach($rheList as $id => $name)
                                            <option value="{{ $id }}" {{ (int)$selectedEstateId === (int)$id ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="estate_id">Name of the RHE</label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <select name="flat_type_id" id="flat_type_id" class="form-select" required>
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
                                    <i class="fa fa-search me-2"></i> Show Vacancy
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        @if(!empty($rows) && count($rows) > 0)
                            <table class="table table-list table-striped table-hover table-bordered">
                                <thead>
                                    <tr class="table-primary">
                                        <th>Sl. No.</th>
                                        <th>Name of the RHE</th>
                                        <th>Estate Address</th>
                                        <th>Flat Type</th>
                                        <th>No. of Vacant Flat(s)</th>
                                        <th>Flat No.</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rows as $index => $row)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $row['estate_name'] }}</td>
                                            <td>{{ $row['estate_address'] }}</td>
                                            <td>{{ $row['flat_type'] }}</td>
                                            <td>{{ $row['no_of_vacant_flats'] }}</td>
                                            <td>{{ $row['flat_list'] }}</td>
                                        </tr>
                                    @endforeach
                                    <tr class="table-secondary">
                                        <td colspan="4" class="text-end"><strong>Total No. of Vacant Flats:</strong></td>
                                        <td colspan="2"><strong>{{ $totalVacantFlats }}</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        @else
                            <table class="datatable_no_data_found table table-list">
                                <tr class="tr_no_data_found">
                                    <th class="th_no_data_found"></th>
                                </tr>
                                <tr class="tr_no_data_found">
                                    <td class="td_no_data_found">
                                        @if((int)$selectedEstateId === 0 || (int)$selectedFlatTypeId === 0)
                                            Please select both RHE and Flat Type to view vacancy list.
                                        @else
                                            No data found for the selected criteria.
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

