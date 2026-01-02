@extends('housingTheme.layouts.app')

@section('title', $statusMsg . ' Application List for ' . $entityMsg)

@section('content')
    <div class="cms-wrapper">
        <div class="row">
            <div class="col-md-12">
                <div class="cms-card">
                    <div class="cms-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3><i class="fa fa-list me-2"></i> {{ $statusMsg }} Application List for {{ $entityMsg }}</h3>
                                <p class="mb-0">Manage applications</p>
                            </div>
                            <a href="{{ route('dashboard') }}" class="btn btn-light">
                                <i class="fa fa-arrow-left me-2"></i> Back to Dashboard
                            </a>
                        </div>
                    </div>

                    @include('housingTheme.partials.alerts')
                    
                    {{-- Counter Boxes --}}
                    @if($pageStatus == 'action-list')
                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="counter-box p-3 rounded mb-3 position-relative color-box1">
                                <span class="counter">{{ $counts['total'] ?? 0 }}</span>
                                <p>Action List</p>
                                <a href="{{ route('application-list.admin-list', [
                                    'status' => \App\Helpers\UrlEncryptionHelper::encryptUrl($status),
                                    'entity' => \App\Helpers\UrlEncryptionHelper::encryptUrl($entity),
                                    'page_status' => 'action-list'
                                ]) }}" class="badge rounded-pill text-bg-success">View Details</a>
                                <img src="{{ asset('assets/housingTheme/images/allotment-icon.png') }}" class="position-absolute end-0 counter-box-icon top-0" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="counter-box p-3 rounded mb-3 position-relative color-box2">
                                <i class="fa fa-group"></i>
                                <span class="counter">{{ $counts['verified'] ?? 0 }}</span>
                                <p>Verified List</p>
                                @if(isset($verifiedStatus) && $verifiedStatus)
                                    <a href="{{ route('application-list.admin-list', [
                                        'status' => \App\Helpers\UrlEncryptionHelper::encryptUrl($verifiedStatus),
                                        'entity' => \App\Helpers\UrlEncryptionHelper::encryptUrl($entity),
                                        'page_status' => 'verified-list'
                                    ]) }}" class="badge rounded-pill text-bg-success">View Details</a>
                                @endif
                                <img src="{{ asset('assets/housingTheme/images/floor-icon.png') }}" class="position-absolute end-0 counter-box-icon top-0" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="counter-box p-3 rounded mb-3 position-relative color-box3">
                                <i class="fa fa-shopping-cart"></i>
                                <span class="counter">{{ $counts['rejected'] ?? 0 }}</span>
                                <p>Rejected List</p>
                                @if(isset($rejectedStatus) && $rejectedStatus)
                                    <a href="{{ route('application-list.admin-list', [
                                        'status' => \App\Helpers\UrlEncryptionHelper::encryptUrl($rejectedStatus),
                                        'entity' => \App\Helpers\UrlEncryptionHelper::encryptUrl($entity),
                                        'page_status' => 'reject-list'
                                    ]) }}" class="badge rounded-pill text-bg-success">View Details</a>
                                @endif
                                <img src="{{ asset('assets/housingTheme/images/allotment-icon.png') }}" class="position-absolute end-0 counter-box-icon top-0" />
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Application Table --}}
                    <div class="table-responsive mt-4">
                        <table class="table table-list table-striped" id="admin-application-list-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Application No.</th>
                                    @if($entity == 'new-apply' || $entity == 'vs' || $entity == 'cs')
                                        <th>Date of Application</th>
                                        <th>Flat Type</th>
                                        <th>Allotment Reason</th>
                                        <th>Computer Serial No</th>
                                    @endif
                                    @if($pageStatus == 'verified-list' || $pageStatus == 'reject-list')
                                        <th>Approval/Rejection Date</th>
                                        <th>Status</th>
                                    @endif
                                    <th>View</th>
                                    @if($pageStatus == 'action-list')
                                        <th>Action</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($applications as $app)
                                    <tr>
                                        <td>{{ $app['applicant_name'] ?? '-' }}</td>
                                        <td>{{ $app['application_no'] ?? '-' }}</td>
                                        @if($entity == 'new-apply' || $entity == 'vs' || $entity == 'cs')
                                            <td>
                                                @if(isset($app['date_of_application']))
                                                    {{ date('d/m/Y', strtotime($app['date_of_application'])) }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ $app['flat_type'] ?? '-' }}</td>
                                            <td>{{ $app['allotment_category'] ?? 'Not Applicable' }}</td>
                                            <td>{{ $app['computer_serial_no'] ?? 'Not Applicable' }}</td>
                                        @endif
                                        @if($pageStatus == 'verified-list' || $pageStatus == 'reject-list')
                                            <td>
                                                @if(isset($app['approval_or_rejection_date']))
                                                    {{ date('d/m/Y H:i:s', strtotime($app['approval_or_rejection_date'])) }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ $app['status_description'] ?? $app['status'] ?? '-' }}</td>
                                        @endif
                                        <td>
                                            <a href="{{ route('application-detail.admin-view', [
                                                'id' => \App\Helpers\UrlEncryptionHelper::encryptUrl($app['online_application_id']),
                                                'page_status' => $pageStatus,
                                                'status' => \App\Helpers\UrlEncryptionHelper::encryptUrl($status)
                                            ]) }}" 
                                               target="_blank" 
                                               class="btn btn-sm btn-primary">
                                                <i class="fa fa-eye"></i> View
                                            </a>
                                        </td>
                                        @if($pageStatus == 'action-list')
                                            <td>
                                                @php
                                                    $user = session('user');    // added by Subham dt.02/02/2024
                                                    $encryptedUid = \App\Helpers\UrlEncryptionHelper::encryptUrl($user['uid']); // added by Subham dt.02/02/2024
                                                    $encryptedId = \App\Helpers\UrlEncryptionHelper::encryptUrl($app['online_application_id']);
                                                    $encryptedStatus = \App\Helpers\UrlEncryptionHelper::encryptUrl($status);
                                                    $encryptedEntity = \App\Helpers\UrlEncryptionHelper::encryptUrl($entity);
                                                    $encryptedComputerSerial = isset($app['computer_serial_no']) ? \App\Helpers\UrlEncryptionHelper::encryptUrl($app['computer_serial_no']) : '';
                                                    $encryptedFlatType = isset($app['flat_type']) ? \App\Helpers\UrlEncryptionHelper::encryptUrl($app['flat_type']) : ''; // added by Subham dt.02/02/2024
                                                @endphp
                                                @if(isset($verifiedStatus) && $verifiedStatus)
                                                    <form action="{{ route('application-approve.store') }}"
                                                        method="POST"
                                                        class="d-inline"
                                                        onsubmit="return confirm('Are you sure you want to approve this application?')">

                                                        @csrf
                            
                                                        <input type="hidden" name="id" value="{{ $encryptedId }}">
                                                        <input type="hidden" name="status" value="{{ $encryptedStatus }}">
                                                        <input type="hidden" name="entity" value="{{ $encryptedEntity }}">
                                                        <input type="hidden" name="page_status" value="{{ $pageStatus }}">
                                                        <input type="hidden" name="computer_serial_no" value="{{ $encryptedComputerSerial }}">
                                                        <input type="hidden" name="flat_type" value="{{ $encryptedFlatType }}">
                                                        <input type="hidden" name="uid" value="{{ $encryptedUid }}">

                                                        <button type="submit" class="btn btn-sm btn-success me-1">
                                                            <i class="fa fa-check"></i> Accept
                                                        </button>
                                                    </form>
                                                @endif

                                                @if(isset($rejectedStatus) && $rejectedStatus)
                                                    <button type="button" 
                                                            class="btn btn-sm btn-danger"
                                                            onclick="showRejectModal('{{ $encryptedId }}', '{{ \App\Helpers\UrlEncryptionHelper::encryptUrl($rejectedStatus) }}', '{{ $encryptedStatus }}', '{{ $encryptedEntity }}', '{{ $encryptedComputerSerial }}')">
                                                        <i class="fa fa-times"></i> Reject
                                                    </button>
                                                @endif
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ ($entity == 'new-apply' || $entity == 'vs' || $entity == 'cs') ? ($pageStatus == 'action-list' ? 8 : 7) : 4 }}" class="text-center">
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

    {{-- Reject Modal --}}
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Reject Application</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="rejectForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="reject_remarks" class="form-label">Remarks (Optional)</label>
                            <textarea class="form-control" id="reject_remarks" name="remarks" rows="3" placeholder="Enter rejection remarks..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Confirm Reject</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="{{ asset('assets/housingTheme/jquery/jquery.min.js') }}"></script>
<script>
    $(document).ready(function() {
        // Initialize DataTable if available
        if ($.fn.DataTable) {
            $('#admin-application-list-table').DataTable({
                order: [[1, 'asc']],
                pageLength: 25,
                language: {
                    emptyTable: "No applications found!"
                }
            });
        }
    });

    function showRejectModal(id, newStatus, status, entity, computerSerialNo) {
        const form = document.getElementById('rejectForm');
        form.action = '{{ route('reject-application') }}';
        
        // Add hidden fields
        let hiddenFields = form.querySelectorAll('input[type="hidden"]');
        hiddenFields.forEach(field => field.remove());
        
        const fields = [
            { name: 'online_application_id', value: id },
            { name: 'rejected_status', value: newStatus },
            { name: 'status', value: status },
            { name: 'entity', value: entity },
            { name: 'computer_serial_no', value: computerSerialNo }
        ];
        
        fields.forEach(field => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = field.name;
            input.value = field.value;
            form.appendChild(input);
        });
        
        const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
        modal.show();
    }

</script>
@endpush

