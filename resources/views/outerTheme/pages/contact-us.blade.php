@extends('outerTheme.layouts.guest') {{-- if you have a main layout --}}

@section('content')
<section class="bg-banner"></section>
<section class="h-75 #mx-auto #p-3" style=" overflow: auto;">
    <div class="services small_pb">
        <div class="container">
            <h2 class="fw-bold text-body-emphasis abt-dept-heading2 poppins-extralight">{{ $link_title }}</h2>
            <div class="row justify-content-center">
                <div class="col-xl-9 col-lg-9">
                    {!! $content_description !!}
                    <div class="row">
                        <p class="text-body-secondary"></p>
                        <div class="col border">
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3684.252607583039!2d88.34062037599892!3d22.569653633076953!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3a0277a200f6df61%3A0xca19a1b2688346e!2sNew%20Secretariat%20Building!5e0!3m2!1sen!2sin!4v1717760081773!5m2!1sen!2sin" width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
