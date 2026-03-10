@extends('outerTheme.layouts.guest')

@section('content')
<section class="bg-banner"></section>
<section class="h-75 #mx-auto #p-3" style="overflow: auto;">
    <div class="services small_pb">
        <div class="container">
            <h2 class="fw-bold text-body-emphasis abt-dept-heading2 poppins-extralight">User Manual</h2>
            <div class="row justify-content-center">
                <div class="col-xl-9 col-lg-9">
                    <table class="table table-list table-striped">
                        <thead>
                            <tr>
                                <th>Serial No.</th>
                                <th>Description</th>
                                <th>Download File</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($manuals as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item['content_title'] ?? $item['link_title'] ?? '-' }}</td>
                                    <td>
                                        @if(!empty($item['file_url']))
                                            <a href="{{ $item['file_url'] }}" target="_blank" rel="noopener noreferrer" class="btn btn-info btn-sm">View</a>
                                        @else
                                            <span class="text-muted small">File not available</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No data found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
