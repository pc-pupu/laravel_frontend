@extends('housingTheme.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row">
   <div class="col-md-12">
      <div class="counter-box p-3 rounded mb-3 position-relative shadow-sm row">
         <div class="col-md-9">
            <h4 class="title-lg">Welcome to e-Allotment of Rental Housing Estate</h4><br>
            <h6>Name: {{ $output['user_info']['applicantName'] ?? 'N/A' }}</h6> 
            <h6>Designation: {{ !empty($output['user_info']['applicantDesignation']) ? $output['user_info']['applicantDesignation'] : 'Data not found' }}</h6>  
            <h6>Office: {{ !empty($output['user_info']['officeName']) ? $output['user_info']['officeName'] : 'Data not found' }}</h6>
            <h6>Mobile Number: {{ !empty($output['user_info']['mobileNo']) ? $output['user_info']['mobileNo'] : 'Mobile No. not found' }}</h6>
            <h6>Email: {{ $output['user_info']['email'] ?? 'N/A' }}</h6>
            @if(isset($output['user_status']) && $output['user_status'] == 'offer_letter_cancel')
               <p style="color:red;">** Your offer letter has been marked as inactive by the system due to non-acceptance within the 15-day timeline. Please contact your Sub-Divisional Asst. Engineer within 5 days to request an offer letter extension. Otherwise, Your application will be cancelled 15 days after the offer letter becomes inactive.</p>
            @endif
            @if(isset($output['user_status']) && $output['user_status'] == 'license_cancel')
               <p style="color:red;">** The license has been Inactive due to the failure to receive the possession letter within 15 days. Please contact your sub-divisonal Exec. Engineer within 5 days to request for license extension otherwise, your application will be considered cancelled.</p>
            @endif
         </div>
         <div class="col-md-3">
            <img src="{{ asset('assets/housingTheme/images/dashboard-user.jpeg') }}" style="border-radius: 50%;" alt="Dashboard User" />
         </div>
      </div>
   </div>
   @php
      $output['new-apply'] = isset($output['new-apply']) ? $output['new-apply'] : 0;
      $output['vs'] = isset($output['vs']) ? $output['vs'] : 0;
      $output['cs'] = isset($output['cs']) ? $output['cs'] : 0;
      $output['allotted-apply'] = isset($output['allotted-apply']) ? $output['allotted-apply'] : 0;
      $output['allotted-vs'] = isset($output['allotted-vs']) ? $output['allotted-vs'] : 0;
      $output['allotted-cs'] = isset($output['allotted-cs']) ? $output['allotted-cs'] : 0;
      $output['all-applications'] = isset($output['all-applications']) ? $output['all-applications'] : 0;
      $output['all-license'] = isset($output['all-license']) ? $output['all-license'] : 0;
      $output['all-exsting-occupant'] = isset($output['all-exsting-occupant']) ? $output['all-exsting-occupant'] : 0;
      $output['auto-cancellation'] = isset($output['auto-cancellation']) ? $output['auto-cancellation'] : 0;
      $output['special-recommendation-list-data'] = isset($output['special-recommendation-list-data']) ? $output['special-recommendation-list-data'] : 0;
      $userRole = $output['user_role'] ?? 0;
   @endphp

   {{-- DDO Dashboard (Role 11) --}}
   @if($userRole == 11)
      @php
         $ddo_status = 'applied';
         $allotted_ddo_status = 'applicant_acceptance';
      @endphp
      <div class="col-md-12">
         <div class="row">
            <div class="col-md-4">
               <div class="counter-box p-3 rounded mb-3 position-relative shadow-sm color-box1">
                  <span class="counter">{{ $output['new-apply'] }}</span>
                  <p>New Application</p>
                  <a href="{{ url('/view_application_list/' . \App\Helpers\UrlEncryptionHelper::encryptUrl($ddo_status) . '/' . \App\Helpers\UrlEncryptionHelper::encryptUrl('new-apply') . '/action-list') }}" class="badge rounded-pill text-bg-success">View Details</a>
                  <img src="{{ asset('assets/housingTheme/images/icon-allotment.png') }}" class="position-absolute end-0 counter-box-icon top-0 mt-2 me-2" alt="Allotment Icon" />
               </div>
            </div>
            <div class="col-md-4">
               <div class="counter-box p-3 rounded mb-3 position-relative shadow-sm color-box2">
                  <span class="counter">{{ $output['vs'] }}</span>
                  <p>Vertical Shifting Application</p>
                  <a href="{{ url('/view_application_list/' . \App\Helpers\UrlEncryptionHelper::encryptUrl($ddo_status) . '/' . \App\Helpers\UrlEncryptionHelper::encryptUrl('vs') . '/action-list') }}" class="badge rounded-pill text-bg-success">View Details</a>
                  <img src="{{ asset('assets/housingTheme/images/icon-floor.png') }}" class="position-absolute end-0 counter-box-icon top-0 mt-2 me-2" alt="Floor Icon" />
               </div>
            </div>
            <div class="col-md-4">
               <div class="counter-box p-3 rounded mb-3 position-relative shadow-sm color-box3">
                  <span class="counter">{{ $output['cs'] }}</span>
                  <p>Category Shifting Application</p>
                  <a href="{{ url('/view_application_list/' . \App\Helpers\UrlEncryptionHelper::encryptUrl($ddo_status) . '/' . \App\Helpers\UrlEncryptionHelper::encryptUrl('cs') . '/action-list') }}" class="badge rounded-pill text-bg-success">View Details</a>
                  <img src="{{ asset('assets/housingTheme/images/icon-category.png') }}" class="position-absolute end-0 counter-box-icon top-0 mt-2 me-2" alt="Category Icon" />
               </div>
            </div>
            <div class="col-md-4">
               <div class="counter-box p-3 rounded mb-3 position-relative shadow-sm color-box1">
                  <span class="counter">{{ $output['allotted-apply'] }}</span>
                  <p>Allotted New Application</p>
                  <a href="{{ url('/view_application_list/' . \App\Helpers\UrlEncryptionHelper::encryptUrl($allotted_ddo_status) . '/' . \App\Helpers\UrlEncryptionHelper::encryptUrl('new-apply') . '/action-list') }}" class="badge rounded-pill text-bg-success">View Details</a>
                  <img src="{{ asset('assets/housingTheme/images/icon-allotment.png') }}" class="position-absolute end-0 counter-box-icon top-0 mt-2 me-2" alt="Allotment Icon" />
               </div>
            </div>
            <div class="col-md-4">
               <div class="counter-box p-3 rounded mb-3 position-relative shadow-sm color-box2">
                  <span class="counter">{{ $output['allotted-vs'] }}</span>
                  <p>Allotted VS Application</p>
                  <a href="{{ url('/view_application_list/' . \App\Helpers\UrlEncryptionHelper::encryptUrl($allotted_ddo_status) . '/' . \App\Helpers\UrlEncryptionHelper::encryptUrl('vs') . '/action-list') }}" class="badge rounded-pill text-bg-success">View Details</a>
                  <img src="{{ asset('assets/housingTheme/images/icon-floor.png') }}" class="position-absolute end-0 counter-box-icon top-0 mt-2 me-2" alt="Floor Icon" />
               </div>
            </div>
            <div class="col-md-4">
               <div class="counter-box p-3 rounded mb-3 position-relative shadow-sm color-box3">
                  <span class="counter">{{ $output['allotted-cs'] }}</span>
                  <p>Allotted CS Application</p>
                  <a href="{{ url('/view_application_list/' . \App\Helpers\UrlEncryptionHelper::encryptUrl($allotted_ddo_status) . '/' . \App\Helpers\UrlEncryptionHelper::encryptUrl('cs') . '/action-list') }}" class="badge rounded-pill text-bg-success">View Details</a>
                  <img src="{{ asset('assets/housingTheme/images/icon-category.png') }}" class="position-absolute end-0 counter-box-icon top-0 mt-2 me-2" alt="Category Icon" />
               </div>
            </div>
         </div>
      </div>
   @endif

   {{-- Housing Supervisor Dashboard (Role 10) --}}
   @if($userRole == 10)
      @php
         $osd_status = 'ddo_verified_1';
         $allotted_osd_status = 'ddo_verified_2';
      @endphp
      <div class="col-md-12">
         <div class="row">
            <div class="col-md-4">
               <div class="counter-box p-3 rounded mb-3 position-relative shadow-sm color-box1">
                  <span class="counter">{{ $output['new-apply'] }}</span>
                  <p>New Application</p>
                  <a href="{{ url('/view_application_list/' . \App\Helpers\UrlEncryptionHelper::encryptUrl($osd_status) . '/' . \App\Helpers\UrlEncryptionHelper::encryptUrl('new-apply') . '/action-list') }}" class="badge rounded-pill text-bg-success">View Details</a>
                  <img src="{{ asset('assets/housingTheme/images/icon-allotment.png') }}" class="position-absolute end-0 counter-box-icon top-0 mt-2 me-2" alt="Allotment Icon" />
               </div>
            </div>
            <div class="col-md-4">
               <div class="counter-box p-3 rounded mb-3 position-relative shadow-sm color-box2">
                  <span class="counter">{{ $output['vs'] }}</span>
                  <p>Vertical Shifting Application</p>
                  <a href="{{ url('/view_application_list/' . \App\Helpers\UrlEncryptionHelper::encryptUrl($osd_status) . '/' . \App\Helpers\UrlEncryptionHelper::encryptUrl('vs') . '/action-list') }}" class="badge rounded-pill text-bg-success">View Details</a>
                  <img src="{{ asset('assets/housingTheme/images/icon-floor.png') }}" class="position-absolute end-0 counter-box-icon top-0 mt-2 me-2" alt="Floor Icon" />
               </div>
            </div>
            <div class="col-md-4">
               <div class="counter-box p-3 rounded mb-3 position-relative shadow-sm color-box3">
                  <span class="counter">{{ $output['cs'] }}</span>
                  <p>Category Shifting Application</p>
                  <a href="{{ url('/view_application_list/' . \App\Helpers\UrlEncryptionHelper::encryptUrl($osd_status) . '/' . \App\Helpers\UrlEncryptionHelper::encryptUrl('cs') . '/action-list') }}" class="badge rounded-pill text-bg-success">View Details</a>
                  <img src="{{ asset('assets/housingTheme/images/icon-category.png') }}" class="position-absolute end-0 counter-box-icon top-0 mt-2 me-2" alt="Category Icon" />
               </div>
            </div>
            <div class="col-md-4">
               <div class="counter-box p-3 rounded mb-3 position-relative shadow-sm color-box1">
                  <span class="counter">{{ $output['allotted-apply'] }}</span>
                  <p>Allotted New Application</p>
                  <a href="{{ url('/view_application_list/' . \App\Helpers\UrlEncryptionHelper::encryptUrl($allotted_osd_status) . '/' . \App\Helpers\UrlEncryptionHelper::encryptUrl('new-apply') . '/action-list') }}" class="badge rounded-pill text-bg-success">View Details</a>
                  <img src="{{ asset('assets/housingTheme/images/icon-allotment.png') }}" class="position-absolute end-0 counter-box-icon top-0 mt-2 me-2" alt="Allotment Icon" />
               </div>
            </div>
            <div class="col-md-4">
               <div class="counter-box p-3 rounded mb-3 position-relative shadow-sm color-box2">
                  <span class="counter">{{ $output['allotted-vs'] }}</span>
                  <p>Allotted VS Application</p>
                  <a href="{{ url('/view_application_list/' . \App\Helpers\UrlEncryptionHelper::encryptUrl($allotted_osd_status) . '/' . \App\Helpers\UrlEncryptionHelper::encryptUrl('vs') . '/action-list') }}" class="badge rounded-pill text-bg-success">View Details</a>
                  <img src="{{ asset('assets/housingTheme/images/icon-floor.png') }}" class="position-absolute end-0 counter-box-icon top-0 mt-2 me-2" alt="Floor Icon" />
               </div>
            </div>
            <div class="col-md-4">
               <div class="counter-box p-3 rounded mb-3 position-relative shadow-sm color-box3">
                  <span class="counter">{{ $output['allotted-cs'] }}</span>
                  <p>Allotted CS Application</p>
                  <a href="{{ url('/view_application_list/' . \App\Helpers\UrlEncryptionHelper::encryptUrl($allotted_osd_status) . '/' . \App\Helpers\UrlEncryptionHelper::encryptUrl('cs') . '/action-list') }}" class="badge rounded-pill text-bg-success">View Details</a>
                  <img src="{{ asset('assets/housingTheme/images/icon-category.png') }}" class="position-absolute end-0 counter-box-icon top-0 mt-2 me-2" alt="Category Icon" />
               </div>
            </div>
         </div>
      </div>
   @endif

   {{-- Housing Approver Dashboard (Role 13) --}}
   @if($userRole == 13)
      @php
         $approver_status = 'housing_sup_approved_1';
         $allotted_approver_status = 'housing_sup_approved_2';
      @endphp
      <div class="col-md-12">
         <div class="row">
            <div class="col-md-4">
               <div class="counter-box p-3 rounded mb-3 position-relative shadow-sm color-box1">
                  <span class="counter">{{ $output['new-apply'] }}</span>
                  <p>New Application</p>
                  <a href="{{ url('/view_application_list/' . \App\Helpers\UrlEncryptionHelper::encryptUrl($approver_status) . '/' . \App\Helpers\UrlEncryptionHelper::encryptUrl('new-apply') . '/action-list') }}" class="badge rounded-pill text-bg-success">View Details</a>
                  <img src="{{ asset('assets/housingTheme/images/icon-allotment.png') }}" class="position-absolute end-0 counter-box-icon top-0 mt-2 me-2" alt="Allotment Icon" />
               </div>
            </div>
            <div class="col-md-4">
               <div class="counter-box p-3 rounded mb-3 position-relative shadow-sm color-box2">
                  <span class="counter">{{ $output['vs'] }}</span>
                  <p>Vertical Shifting Application</p>
                  <a href="{{ url('/view_application_list/' . \App\Helpers\UrlEncryptionHelper::encryptUrl($approver_status) . '/' . \App\Helpers\UrlEncryptionHelper::encryptUrl('vs') . '/action-list') }}" class="badge rounded-pill text-bg-success">View Details</a>
                  <img src="{{ asset('assets/housingTheme/images/icon-floor.png') }}" class="position-absolute end-0 counter-box-icon top-0 mt-2 me-2" alt="Floor Icon" />
               </div>
            </div>
            <div class="col-md-4">
               <div class="counter-box p-3 rounded mb-3 position-relative shadow-sm color-box3">
                  <span class="counter">{{ $output['cs'] }}</span>
                  <p>Category Shifting Application</p>
                  <a href="{{ url('/view_application_list/' . \App\Helpers\UrlEncryptionHelper::encryptUrl($approver_status) . '/' . \App\Helpers\UrlEncryptionHelper::encryptUrl('cs') . '/action-list') }}" class="badge rounded-pill text-bg-success">View Details</a>
                  <img src="{{ asset('assets/housingTheme/images/icon-category.png') }}" class="position-absolute end-0 counter-box-icon top-0 mt-2 me-2" alt="Category Icon" />
               </div>
            </div>
            <div class="col-md-4">
               <div class="counter-box p-3 rounded mb-3 position-relative shadow-sm color-box1">
                  <span class="counter">{{ $output['allotted-apply'] }}</span>
                  <p>Allotted New Application</p>
                  <a href="{{ url('/view_application_list/' . \App\Helpers\UrlEncryptionHelper::encryptUrl($allotted_approver_status) . '/' . \App\Helpers\UrlEncryptionHelper::encryptUrl('new-apply') . '/action-list') }}" class="badge rounded-pill text-bg-success">View Details</a>
                  <img src="{{ asset('assets/housingTheme/images/icon-allotment.png') }}" class="position-absolute end-0 counter-box-icon top-0 mt-2 me-2" alt="Allotment Icon" />
               </div>
            </div>
            <div class="col-md-4">
               <div class="counter-box p-3 rounded mb-3 position-relative shadow-sm color-box2">
                  <span class="counter">{{ $output['allotted-vs'] }}</span>
                  <p>Allotted VS Application</p>
                  <a href="{{ url('/view_application_list/' . \App\Helpers\UrlEncryptionHelper::encryptUrl($allotted_approver_status) . '/' . \App\Helpers\UrlEncryptionHelper::encryptUrl('vs') . '/action-list') }}" class="badge rounded-pill text-bg-success">View Details</a>
                  <img src="{{ asset('assets/housingTheme/images/icon-floor.png') }}" class="position-absolute end-0 counter-box-icon top-0 mt-2 me-2" alt="Floor Icon" />
               </div>
            </div>
            <div class="col-md-4">
               <div class="counter-box p-3 rounded mb-3 position-relative shadow-sm color-box3">
                  <span class="counter">{{ $output['allotted-cs'] }}</span>
                  <p>Allotted CS Application</p>
                  <a href="{{ url('/view_application_list/' . \App\Helpers\UrlEncryptionHelper::encryptUrl($allotted_approver_status) . '/' . \App\Helpers\UrlEncryptionHelper::encryptUrl('cs') . '/action-list') }}" class="badge rounded-pill text-bg-success">View Details</a>
                  <img src="{{ asset('assets/housingTheme/images/icon-category.png') }}" class="position-absolute end-0 counter-box-icon top-0 mt-2 me-2" alt="Category Icon" />
               </div>
            </div>
         </div>
      </div>
   @endif

   {{-- Sub-division Dashboard (Role 7) --}}
   @if($userRole == 7)
      <div class="col-md-12">
         <div class="row">
            <div class="col-md-4">
               <div class="counter-box p-3 rounded mb-3 position-relative shadow-sm color-box1 w-100">
                  <span class="counter">{{ $output['all-exsting-occupant'] }}</span>
                  <p>Existing Occupant (with HRMS)</p>
                  <a href="{{ url('/view-occupant-list/') }}" class="badge rounded-pill text-bg-success">View Details</a>
                  <img src="{{ asset('assets/housingTheme/images/icon-allotment.png') }}" class="position-absolute end-0 counter-box-icon top-0 mt-2 me-2" alt="Allotment Icon" />
               </div>
            </div>
            <div class="col-md-4">
               <div class="counter-box p-3 rounded mb-3 position-relative shadow-sm color-box1 w-100">
                  <span class="counter">{{ $output['existing_occupant_data'] ?? 0 }}</span>
                  <p>Existing Occupant (without HRMS)</p>
                  <a href="{{ url('/rhewise_occupant_draft_list/') }}" class="badge rounded-pill text-bg-success">View Details</a>
                  <img src="{{ asset('assets/housingTheme/images/icon-allotment.png') }}" class="position-absolute end-0 counter-box-icon top-0 mt-2 me-2" alt="Allotment Icon" />
               </div>
            </div>
            <div class="col-md-4">
               <div class="counter-box p-3 rounded mb-3 position-relative shadow-sm color-box2 w-100">
                  <span class="counter">{{ $output['auto-cancellation'] }}</span>
                  <p>Auto Cancelled Offer Letters / Licenses</p>
                  <a href="{{ url('/auto-cancellation-list/') }}" class="badge rounded-pill text-bg-success">View Details</a>
                  <img src="{{ asset('assets/housingTheme/images/icon-floor.png') }}" class="position-absolute end-0 counter-box-icon top-0 mt-2 me-2" alt="Floor Icon" />
               </div>
            </div>
         </div>
      </div>
   @endif

   {{-- Division Dashboard (Role 8) --}}
   @if($userRole == 8)
      <div class="col-md-12">
         <div class="row">
            <div class="col-md-6">
               <div class="counter-box p-3 rounded mb-3 position-relative shadow-sm color-box1 w-100">
                  <span class="counter">{{ $output['all-exsting-occupant'] }}</span>
                  <p>Existing Occupant</p>
                  <a href="{{ url('/view-occupant-list/') }}" class="badge rounded-pill text-bg-success">View Details</a>
                  <img src="{{ asset('assets/housingTheme/images/icon-allotment.png') }}" class="position-absolute end-0 counter-box-icon top-0 mt-2 me-2" alt="Allotment Icon" />
               </div>
            </div>
         </div>
      </div>
   @endif

   {{-- Housing Official Dashboard (Role 6) --}}
   @if($userRole == 6)
      <div class="col-md-12">
         <div class="row">
            <div class="col-md-4">
               <div class="row">
                  <div class="col-md-12">
                     <div class="counter-box p-3 rounded mb-3 position-relative shadow-sm color-box4">
                        <span class="counter">{{ $output['all-license'] }}</span>
                        <p>Pending License Generation</p>
                        <a href="{{ url('/generate-license/') }}" class="badge rounded-pill text-bg-success">View Details</a>
                        <img src="{{ asset('assets/housingTheme/images/icon-licence.png') }}" class="position-absolute end-0 counter-box-icon top-0 mt-2 me-2" alt="License Icon" />
                     </div>
                  </div>
               </div>
            </div>
            <div class="col-md-8">
               <div class="row">
                  <div class="table-responsive rounded counter-box shadow-sm p-3">
                     <table class="table table-list table-striped table-hover table-bordered">
                        <thead>
                           <tr class="table-primary">
                              <th>Flat Type</th>
                              <th>No. of Waitlisted Applications(For New Allotment)
                                 <a href="{{ url('/flat_type_waiting_list/') }}" class="badge rounded-pill text-bg-success">View Details</a>
                              </th>
                           </tr>
                        </thead>
                        <tbody>
                           <tr>
                              <td><b>A</b></td>
                              <td>{{ $output['flatTypeCounts']['A'] ?? 0 }}</td>
                           </tr>
                           <tr>
                              <td><b>B</b></td>
                              <td>{{ $output['flatTypeCounts']['B'] ?? 0 }}</td>
                           </tr>
                           <tr>
                              <td><b>C</b></td>
                              <td>{{ $output['flatTypeCounts']['C'] ?? 0 }}</td>
                           </tr>
                           <tr>
                              <td><b>D</b></td>
                              <td>{{ $output['flatTypeCounts']['D'] ?? 0 }}</td>
                           </tr>
                           <tr>
                              <td><b>A+</b></td>
                              <td>{{ $output['flatTypeCounts']['A+'] ?? 0 }}</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
               </div>
            </div>
         </div>
      </div>
   @endif

   {{-- Head of Department Dashboard (Role 17) --}}
   @if($userRole == 17)
      <div class="col-md-12">
         <div class="row">
            <div class="col-md-6">
               <div class="counter-box p-3 rounded mb-3 position-relative shadow-sm color-box1 w-100">
                  <span class="counter">{{ $output['all-applications'] }}</span>
                  <p>All Pending Applications for Approval</p>
                  <a href="{{ url('/allotment_list_approve/') }}" class="badge rounded-pill text-bg-success">View Details</a>
                  <img src="{{ asset('assets/housingTheme/images/icon-allotment.png') }}" class="position-absolute end-0 counter-box-icon top-0 mt-2 me-2" alt="Allotment Icon" />
               </div>
            </div>
            <div class="col-md-6">
               <div class="counter-box p-3 rounded mb-3 position-relative shadow-sm color-box2 w-100">
                  <span class="counter">{{ $output['special-recommendation-list-data'] }}</span>
                  <p>Special Recommendation</p>
                  <a href="{{ url('/special-recommended-list/') }}" class="badge rounded-pill text-bg-success">View Details</a>
                  <img src="{{ asset('assets/housingTheme/images/icon-floor.png') }}" class="position-absolute end-0 counter-box-icon top-0 mt-2 me-2" alt="Floor Icon" />
               </div>
            </div>
         </div>
      </div>
   @endif

   {{-- Applicant Dashboard (Roles 4, 5) --}}
   @if(in_array($userRole, [4, 5]))
      @php
         $allotment_no = isset($output['fetch_current_status']->allotment_no) ? $output['fetch_current_status']->allotment_no : '';
         $license_no = isset($output['fetch_license_status']->online_application_id) ? $output['fetch_license_status']->online_application_id : '';
         
         // Generate redirect links based on allotment number prefix
         $redirect_link = '';
         if (!empty($allotment_no)) {
            if (strpos($allotment_no, 'NAL') !== false) {
               $redirect_link = '<a href="' . url('/allotment_detail_pdf_gen/' . \App\Helpers\UrlEncryptionHelper::encryptUrl($output['fetch_current_status']->online_application_id) . '/' . \App\Helpers\UrlEncryptionHelper::encryptUrl($allotment_no)) . '"> Download Now</a>';
            } elseif (strpos($allotment_no, 'VSAL') !== false) {
               $redirect_link = '<a href="' . url('/vs_allotment_detail_pdf_gen/' . \App\Helpers\UrlEncryptionHelper::encryptUrl($output['fetch_current_status']->online_application_id) . '/' . \App\Helpers\UrlEncryptionHelper::encryptUrl($allotment_no)) . '"> Download Now</a>';
            } elseif (strpos($allotment_no, 'CSAL') !== false) {
               $redirect_link = '<a href="' . url('/cs_allotment_detail_pdf_gen/' . \App\Helpers\UrlEncryptionHelper::encryptUrl($output['fetch_current_status']->online_application_id) . '/' . \App\Helpers\UrlEncryptionHelper::encryptUrl($allotment_no)) . '"> Download Now</a>';
            }
         }
         
         $redirect_link_license = '';
         if (!empty($license_no)) {
            $redirect_link_license = '<a href="' . url('/download_licence_pdf/' . \App\Helpers\UrlEncryptionHelper::encryptUrl($license_no) . '/') . '"> Download Now</a>';
         }
      @endphp

      <div class="row">
         <h4 class="mt-4">Application List</h4>
         <div class="col-md-9">
            <div class="table-responsive rounded counter-box shadow-sm p-3">
               @if(!empty($output['all-application-data']) && count($output['all-application-data']) > 0)
                  <table class="table table-list table-striped table-hover table-bordered">
                     <thead>
                        <tr class="table-primary">
                           <th>Name</th>
                           <th>Application Number</th>
                           <th>Date of Application</th>
                           <th>Status of Application</th>
                           <th>Action</th>
                        </tr>
                     </thead>
                     <tbody>
                        @foreach($output['all-application-data'] as $application)
                        <tr>
                           <td><b>{{ $application->applicant_name }}</b></td>
                           <td>{{ $application->application_no }}</td>
                           <td>{{ !empty($application->date_of_application) ? \Carbon\Carbon::parse($application->date_of_application)->format('d-m-Y') : 'N/A' }}</td>
                           <td>{{ $application->status_description }}</td>
                           <td>
                              <a href="{{ url('/view-application/' . \App\Helpers\UrlEncryptionHelper::encryptUrl($application->online_application_id)) }}" class="btn btn-outline-primary btn-sm px-5 rounded-pill fw-bolder">View</a>
                           </td>
                        </tr>
                        @endforeach
                     </tbody>
                  </table>
               @else
                  <tr>No Application Data Found</tr>
               @endif
            </div>
         </div>
         <div class="col-md-3">
            <div class="card h-100 notification-box">
               <div class="card-body">
                  <div id="carouselExampleCaptions" class="carousel slide h-100" data-bs-ride="carousel">
                     <div class="carousel-indicators">
                        <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                        <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1" aria-label="Slide 2"></button>
                     </div>
                     <div class="carousel-inner text-center">
                        <div class="carousel-item active p-3">
                           <img src="{{ asset('assets/housingTheme/images/notification.png') }}" class="w-75" alt="Notification" />
                           @if($allotment_no != '')
                              <p style="color:red; font-family: Arial, sans-serif; font-size: 16px;">1. Your Offer Letter is generated, download from here!</p>
                              <i class="fa fa-download" style="margin-right: 4px; color:blue;">{!! $redirect_link !!}</i>
                           @else
                              <p style="color:red; font-family: Arial, sans-serif; font-size: 16px;">No Latest Notifications</p>
                           @endif
                        </div>
                        <div class="carousel-item p-3">
                           <img src="{{ asset('assets/housingTheme/images/notification.png') }}" class="w-75" alt="Notification" />
                           @if($license_no != '')
                              <p style="color:red; font-family: Arial, sans-serif; font-size: 16px;">2. Your License is generated, download from here!</p>
                              <i class="fa fa-download" style="margin-right: 4px; color:blue;">{!! $redirect_link_license !!}</i>
                           @else
                              <p style="color:red; font-family: Arial, sans-serif; font-size: 16px;">No Latest Notifications</p>
                           @endif
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   @endif
</div>
@endsection
