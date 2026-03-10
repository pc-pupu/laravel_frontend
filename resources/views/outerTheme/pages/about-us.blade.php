@extends('outerTheme.layouts.guest') 

@section('content')
<section class="bg-banner"></section>
<section class="h-75 #mx-auto #p-3" style=" overflow: auto;">
    <div class="services small_pb">
        <div class="container">
            <h2 class="fw-bold text-body-emphasis abt-dept-heading2 poppins-extralight">{{ $link_title }}</h2>
            <div class="row justify-content-center">
                <div class="col-xl-9 col-lg-9">
                    {!! $content_description !!}
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
