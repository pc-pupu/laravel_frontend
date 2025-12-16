{{-- Estate Treasury Mapping Form (Merged Create/Edit) --}}
@include('commonForm.estate-treasury-fields', [
    'mapping' => $mapping ?? [],
    'estates' => $estates ?? [],
    'treasuries' => $treasuries ?? []
])

