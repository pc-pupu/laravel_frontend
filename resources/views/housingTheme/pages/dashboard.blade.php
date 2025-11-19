@extends('layouts.dashboard-master')
@section('page-header')
	Dashboard
@stop
@section('dashboard-body')
<div class="row mt-5">
   <div class="col-md-12">
   		<div class="counter-box p-3 rounded mb-3 position-relative shadow-sm row">
   			<div class="col-md-9">
   				<h4 class="title-lg">Welcome to e-Allotment of Rental Housing Estate</h4><br>
   				<h6>Designation: {{$designation}}</h6>
         		<h6>Mobile Number: {{$mobile_no}}</h6>
         		<h6>Email: {{$email}}</h6>
   			</div>
   			<div class="col-md-3"><img src="{{asset('/themes/dashboard-theme/images/dashboard-user.jpeg')}}" style="border-radius: 50%;" /></div>
   		</div>
   </div>
</div>
@if($application != null)
<div class="row">
	<h4 class="mt-4">Application List</h4>
	<div class="col-md-9">
		<div class="table-responsive rounded counter-box shadow-sm p-3">
			<table class="table table-list table-striped table-hover data-table table-bordered">
            <thead>
               <tr class="table-primary">
                  <th>Name</th>
                  <th>Application Number</th>
                  <th>Date of Application</th>
                  <th>Status of Application</th>
                  <th>Action</th>
                  <!-- <th>Flat Type</th> -->
                  <!-- <th>View</th> -->
               </tr>
            </thead>
            <tbody>
            	@foreach($application as $application)
               <tr> 
               	@if($application->applicant_name!=null)
                  <td>{{$application->applicant_name}}</td>
                  @else
                  <td>Not Available</td>
                  @endif

                  @if($application->housingApplicantToOfficialDetail->housingOfficialDetailToHousingOnlineApplication->application_no!=null)
                  <td>{{$application->housingApplicantToOfficialDetail->housingOfficialDetailToHousingOnlineApplication->application_no}}</td>
                  @else
                  <td>Not Available</td>
                  @endif

                  @if($application->housingApplicantToOfficialDetail->housingOfficialDetailToHousingOnlineApplication->date_of_application!=null)
                  <td>{{$application->housingApplicantToOfficialDetail->housingOfficialDetailToHousingOnlineApplication->date_of_application}}</td>
                  @else
                  <td>Not Available</td>
                  @endif

                  @if($application->housingApplicantToOfficialDetail->housingOfficialDetailToHousingOnlineApplication->housingOnlineApplicationToHousingAllotmentStatusMaster->status_description!=null)
                  <td>{{$application->housingApplicantToOfficialDetail->housingOfficialDetailToHousingOnlineApplication->housingOnlineApplicationToHousingAllotmentStatusMaster->status_description}}</td>
                  @else
                  <td>Not Available</td>
                  @endif
                  <td><a class="btn btn-success btn-sm" href="{{ url('new-application-view/'.Crypt::encrypt($application->housing_applicant_id)) }}">View</a></td>
               </tr>
              @endforeach
            </tbody>
         </table>
		</div>
	</div>
	<div class="col-md-3">
     <div class="card h-100 notification-box">
        <div class="card-body">
           <div id="carouselExampleCaptions" class="carousel slide h-100" data-bs-ride="carousel">
              <div class="carousel-indicators">
                 <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0"
                    class="active" aria-current="true" aria-label="Slide 1"></button>
                 <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1"
                    aria-label="Slide 2"></button>
              </div>
              <div class="carousel-inner text-center">
             
                 <div class="carousel-item active p-3">
                    <img src="{{asset('/themes/dashboard-theme/images/notification.png')}}" class="w-75" />
                    <!-- <h5>Latest Notification</h5> -->
                    <?php //if($allotment_no != ''){?>
                       <p style="color:red; font-family: Arial, sans-serif; font-size: 16px;">1. Your Offer Letter is generated, download from here!</p>
                       <i class="fa fa-download" style="margin-right: 4px; color:blue;"><?php //echo $redirect_link; // Done by dg 26-12-2024 ?></i>
                    <?php //}else{  ?>
                       <p style="color:red; font-family: Arial, sans-serif; font-size: 16px;">No Latest Notifications</p>
                    <?php //} ?>   
                   
                 </div>
              

                
                 <div class="carousel-item p-3">
                    <img src="{{asset('/themes/dashboard-theme/images/notification.png')}}" class="w-75" />
                    <!-- <h5>Latest Notification</h5> -->
                    <?php //if($license_no != ''){?> 
                       <p style="color:red; font-family: Arial, sans-serif; font-size: 16px;">2. Your License is generated, download from here!</p><i class="fa fa-download" style="margin-right: 4px; color:blue;"><?php //echo $redirect_link_license;  // Done by dg 26-12-2024?></i>
                    <?php //}else{ ?>
                       <p style="color:red; font-family: Arial, sans-serif; font-size: 16px;">No Latest Notifications</p>
                    <?php //} ?>
                    <!-- <button type="button" class="btn btn-secondary btn-sm rounded-pill">Download
                       List</button> -->
                 </div>
              
              </div>
           </div>
        </div>
     </div>
  </div>
</div>
@endif
@stop
<script src="{{ asset('/assets/housingTheme/jquery/jquery.min.js') }}"></script>
<script>
$(document).ready(function(){
	var table = $('.data-table').DataTable({
        //processing: true,
        //serverSide: true,
        //dom: 'Blfrtip',
        paging: true,
        pageLength:10,
        lengthMenu: [[10, 20, -1], [10, 20,'All']],
        /*ajax:{
            url: "{{ url('displayUserList') }}",
            type: "POST",
            data:function(d){
                 d._token= "{{csrf_token()}}"
            }                
        },*/
        // columns: [
        //     {data: 'DT_RowIndex', name: 'DT_RowIndex'},
        //     {data: 'name'},
        //     {data: 'email'},
        //     {data: 'designation'},
        //     {data: 'place_of_posting'},
        //     {data: 'hrms_code'},
        //     {data: 'created_at'},
        //     {data: 'action', orderable: false, searchable: false},
        // ]
    });
});
</script>
