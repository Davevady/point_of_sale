@props([
    'name',
    'label' => null,
    'placeholder' => '',
    'value' => '',
])

<div class="form-group mb-0">
    @if($label)
        <label for="{{ $name }}" class="small text-muted font-weight-bold">{{ $label }}</label>
    @endif

    <input
        type="text"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ $value }}"
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge(['class' => 'form-control']) }}
    >
</div>