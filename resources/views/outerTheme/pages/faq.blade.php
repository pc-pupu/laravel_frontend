@extends('outerTheme.layouts.guest') {{-- if you have a main layout --}}

@section('content')
<section class="bg-banner"></section>
<section class="h-75 #mx-auto #p-3" style=" overflow: auto;">
    <div class="services small_pb">
        <div class="container">
            <h2 class="fw-bold text-body-emphasis abt-dept-heading2 poppins-extralight">Frequenty Asked Questions</h2>
            <div class="row justify-content-center">
                <div class="col-xl-9 col-lg-9">
                    <div class="accordion accordion-flush" id="accordionFlushExample">
                        
                    @forelse ($faqs as $key => $item)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="flush-heading{{ $key }}">
                                <button class="accordion-button {{ $key !== 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse"                                data-bs-target="#flush-collapse{{ $key }}" aria-expanded="{{ $key === 0 ? 'true' : 'false' }}" aria-controls="flush-collapse{{ $key }}">
                                    <b>
                                        <i>
                                            <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m10 16 4-4-4-4"/>
                                            </svg>
                                        </i>
                                        {{ strtoupper($item['link_title']) }}
                                    </b>
                                </button>
                            </h2>
                            <div id="flush-collapse{{ $key }}"
                                class="accordion-collapse collapse {{ $key === 0 ? 'show' : '' }}"
                                aria-labelledby="flush-heading{{ $key }}"
                                data-bs-parent="#accordionFlushExample">
                                <div class="accordion-body">
                                    {{ $item['content_description'] }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center p-3">
                            <strong>No Data Found</strong>
                        </div>
                    @endforelse
                    
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
