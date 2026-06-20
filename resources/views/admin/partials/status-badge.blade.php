@props(['status', 'type' => null])

@php
    $value = $status instanceof \BackedEnum ? $status->value : $status;
    $label = $status instanceof \BackedEnum ? $status->label() : ucfirst(str_replace('_', ' ', $value));
    $class = match($value) {
        'active', 'approved', 'completed', 'job_completed' => 'bg-green-50 text-green-700',
        'declined', 'inactive', 'revision_limit_reached'   => 'bg-red-50 text-red-600',
        'in_review', 'review_submitted'                    => 'bg-blue-50 text-blue-600',
        'pending', 'draft'                                 => 'bg-amber-50 text-amber-600',
        default => 'bg-neutral-100 text-neutral-600',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex text-[10px] font-semibold px-1.5 py-0.5 rounded $class"]) }}>
    {{ $label }}
</span>
