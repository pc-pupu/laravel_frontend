# Blade Components Refactoring Guide

## Overview
This document describes the reusable Blade components created to eliminate duplicate code across the Laravel frontend views.

## Created Components

### 1. `form-wrapper.blade.php`
**Location:** `resources/views/housingTheme/components/form-wrapper.blade.php`

**Purpose:** Reusable wrapper for create/edit form views with consistent header, error handling, and layout.

**Usage:**
```blade
@extends('housingTheme.layouts.app')
@section('title', 'Add Item')

@section('content')
@include('housingTheme.components.form-wrapper', [
    'title' => 'Add New Item',
    'description' => 'Create new item entry',
    'backRoute' => 'item.index',
    'backText' => 'Back to List',
    'icon' => 'fa-plus-circle',
    'isEdit' => false,
    'showInfoAlert' => true,
    'infoAlertText' => 'Optional info message',
])

@push('form-content')
<form method="POST" action="{{ route('item.store') }}">
    @csrf
    {{-- Form fields here --}}
    
    @include('housingTheme.components.form-footer', [
        'backRoute' => 'item.index',
        'submitText' => 'Submit',
    ])
</form>
@endpush

@endsection
```

**Parameters:**
- `title` (required): Page title
- `description` (optional): Description text
- `backRoute` (optional): Route name for back button
- `backText` (optional): Back button text (default: "Back")
- `icon` (optional): FontAwesome icon class (default: fa-plus-circle for create, fa-edit for edit)
- `isEdit` (optional): Boolean, true for edit mode
- `showInfoAlert` (optional): Boolean, show info alert
- `infoAlertText` (optional): Info alert message
- `showWarning` (optional): Boolean, show warning alert
- `warningText` (optional): Warning alert message

### 2. `form-footer.blade.php`
**Location:** `resources/views/housingTheme/components/form-footer.blade.php`

**Purpose:** Reusable form footer with Cancel and Submit buttons.

**Usage:**
```blade
@include('housingTheme.components.form-footer', [
    'backRoute' => 'item.index',
    'cancelText' => 'Cancel',
    'submitText' => 'Submit',
    'submitButtonClass' => 'btn-submit',
    'submitButtonPadding' => '4',
])
```

**Parameters:**
- `backRoute` (optional): Route name for cancel/back button
- `cancelText` (optional): Cancel button text (default: "Cancel")
- `submitText` (optional): Submit button text (default: "Submit")
- `submitButtonClass` (optional): CSS class for submit button (default: "btn-submit")
- `submitButtonPadding` (optional): Padding value (default: "4")

### 3. `list-wrapper.blade.php`
**Location:** `resources/views/housingTheme/components/list-wrapper.blade.php`

**Purpose:** Reusable wrapper for list/index views with consistent header, search, and layout.

**Usage:**
```blade
@extends('housingTheme.layouts.app')
@section('title', 'Item List')

@section('content')
@include('housingTheme.components.list-wrapper', [
    'title' => 'Item List',
    'description' => 'Manage items',
    'icon' => 'fa-users',
    'addRoute' => 'item.create',
    'addText' => 'Add New',
    'searchRoute' => 'item.search',
    'searchFormRoute' => 'item.index',
    'searchLabel' => 'Search by Name',
])

@push('list-content')
<div class="table-container">
    {{-- Table content here --}}
</div>
@endpush

@endsection
```

**Parameters:**
- `title` (required): Page title
- `description` (optional): Description text
- `icon` (optional): FontAwesome icon class (default: "fa-list")
- `addRoute` (optional): Route name for add button
- `addText` (optional): Add button text (default: "Add New")
- `searchRoute` (optional): Route name for search button
- `searchFormRoute` (optional): Route name for search form action
- `searchLabel` (optional): Search input label
- `customActions` (optional): Array of custom action buttons

## Refactored Views

### Completed:
1. ✅ `existing-applicant/create.blade.php`
2. ✅ `existing-applicant/edit.blade.php`

### To Be Refactored:
1. `existing-applicant-vs-cs/create.blade.php`
2. `existing-applicant-vs-cs/edit.blade.php`
3. `estate-treasury-mapping/create.blade.php`
4. `estate-treasury-mapping/edit.blade.php`
5. `existing-occupant/create.blade.php`
6. `existing-occupant/edit.blade.php`
7. `existing-applicant/index.blade.php`
8. `existing-occupant/index.blade.php`
9. `estate-treasury-mapping/index.blade.php`

## Benefits

1. **DRY Principle**: Eliminated duplicate code across multiple views
2. **Consistency**: All forms and lists now have consistent UI/UX
3. **Maintainability**: Changes to layout/structure only need to be made in one place
4. **Readability**: Views are cleaner and more focused on their specific content
5. **Reusability**: Components can be easily reused for new features

## Migration Guide

### For Create/Edit Views:

**Before:**
```blade
<div class="cms-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="cms-card">
                <div class="cms-header">
                    <!-- Header code -->
                </div>
                <!-- Error handling -->
                <!-- Form -->
            </div>
        </div>
    </div>
</div>
```

**After:**
```blade
@include('housingTheme.components.form-wrapper', [...])
@push('form-content')
<!-- Form content -->
@endpush
```

### For List Views:

**Before:**
```blade
<div class="cms-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="cms-card">
                <div class="cms-header">
                    <!-- Header code -->
                </div>
                <!-- Search form -->
                <!-- Table -->
            </div>
        </div>
    </div>
</div>
```

**After:**
```blade
@include('housingTheme.components.list-wrapper', [...])
@push('list-content')
<!-- Table content -->
@endpush
```

## Next Steps

1. Refactor remaining create/edit views to use `form-wrapper` component
2. Refactor remaining index views to use `list-wrapper` component
3. Test all refactored views to ensure functionality is preserved
4. Update documentation as needed

