@php
    $isEdit = !empty($content);
@endphp

<div class="row g-3">
    <div class="col-md-4">
        <div class="form-floating">
            <select class="form-select @error('content_type') is-invalid @enderror" id="content_type" name="content_type" required>
                <option value="">Select Type</option>
                @foreach ($contentTypes as $value => $label)
                    <option value="{{ $value }}" @selected(old('content_type', $content['content_type'] ?? '') === $value)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            <label for="content_type" class="required">Content Type</label>
            @error('content_type')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-floating">
            <input type="text" class="form-control @error('link_title') is-invalid @enderror" id="link_title" name="link_title"
                value="{{ old('link_title', $content['link_title'] ?? '') }}" placeholder="Link Title">
            <label for="link_title">Link Title</label>
            @error('link_title')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-floating">
            <input type="text" class="form-control text-uppercase @error('content_title') is-invalid @enderror" id="content_title" name="content_title"
                value="{{ old('content_title', $content['content_title'] ?? '') }}" placeholder="Content Title" required>
            <label for="content_title" class="required">Content Title</label>
            @error('content_title')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-12">
        <div class="form-floating">
            <textarea class="form-control @error('content_description') is-invalid @enderror" placeholder="Content Description" id="content_description" name="content_description" style="height: 150px" required>{{ old('content_description', $content['content_description'] ?? '') }}</textarea>
            <label for="content_description" class="required">Content Description</label>
            @error('content_description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-floating">
            <input type="number" min="1" class="form-control @error('order_no') is-invalid @enderror" id="order_no" name="order_no"
                value="{{ old('order_no', $content['order_no'] ?? $nextOrder ?? 1) }}" placeholder="Order No." required>
            <label for="order_no" class="required">Order No.</label>
            @error('order_no')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-floating">
            <input type="date" class="form-control @error('date_of_notification') is-invalid @enderror" id="date_of_notification" name="date_of_notification"
                value="{{ old('date_of_notification', $content['date_of_notification'] ?? now()->format('Y-m-d')) }}" required>
            <label for="date_of_notification" class="required">Date of Notification</label>
            @error('date_of_notification')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-floating">
            <input type="text" class="form-control @error('meta_keyword') is-invalid @enderror" id="meta_keyword" name="meta_keyword"
                value="{{ old('meta_keyword', $content['meta_keyword'] ?? '') }}" placeholder="Meta Keyword">
            <label for="meta_keyword">Meta Keyword</label>
            @error('meta_keyword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-12">
        <div class="form-floating">
            <textarea class="form-control @error('meta_description') is-invalid @enderror" placeholder="Meta Description" id="meta_description" name="meta_description" style="height: 120px">{{ old('meta_description', $content['meta_description'] ?? '') }}</textarea>
            <label for="meta_description">Meta Description</label>
            @error('meta_description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-4">
        <label class="form-label required d-block">Activation Status</label>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="is_active" id="active_yes" value="1"
                @checked(old('is_active', $content['is_active'] ?? 1) == 1)>
            <label class="form-check-label" for="active_yes">Active</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="is_active" id="active_no" value="0"
                @checked(old('is_active', $content['is_active'] ?? 1) == 0)>
            <label class="form-check-label" for="active_no">Inactive</label>
        </div>
        @error('is_active')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 d-flex align-items-center">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" value="1" id="is_new" name="is_new"
                @checked(old('is_new', $content['is_new'] ?? 0))>
            <label class="form-check-label fw-semibold" for="is_new">
                Is New
            </label>
        </div>
    </div>

    <div class="col-md-4">
        <label for="content_file_upload" class="form-label">Upload Content File (PDF &lt;= 1 MB)</label>
        <input class="form-control @error('content_file_upload') is-invalid @enderror" type="file" id="content_file_upload" name="content_file_upload" accept="application/pdf">
        <div id="fileUploadError" class="invalid-feedback d-block d-none"></div>
        @error('content_file_upload')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        @if ($isEdit && !empty($content['file_url']))
            <small class="d-block mt-2">
                Current file:
                <a href="{{ $content['file_url'] }}" target="_blank">View existing file</a>
            </small>
        @endif
    </div>
</div>

