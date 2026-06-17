@props(['status', 'type' => null])

@php
    $value = $status instanceof \BackedEnum ? $status->value : $status;
    $label = $status instanceof \BackedEnum ? $status->label() : ucfirst(str_replace('_', ' ', $value));
    $class = match($value) {
        'active', 'approved', 'completed', 'job_completed' => 'badge-brand',
        'declined', 'inactive', 'revision_limit_reached' => 'badge-rose',
        'pending', 'draft', 'in_review', 'review_submitted' => 'badge-amber',
        default => 'badge-muted',
    };
@endphp

<span {{ $attributes->merge(['class' => "badge $class"]) }}>
    <span class="badge-dot"></span>
    {{ $label }}
</span>
