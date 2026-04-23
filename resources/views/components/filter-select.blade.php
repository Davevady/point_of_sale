@props([
    'name',
    'label' => null,
    'placeholder' => 'Pilih opsi',
    'options' => [],
    'value' => null,
    'optionValue' => 'id',
    'optionLabel' => 'name',
])

<div class="form-group mb-0">
    @if($label)
        <label for="{{ $name }}" class="small text-muted font-weight-bold">{{ $label }}</label>
    @endif

    <select
        name="{{ $name }}"
        id="{{ $name }}"
        data-placeholder="{{ $placeholder }}"
        {{ $attributes->merge(['class' => 'form-control filter-select']) }}
    >
        <option value="">{{ $placeholder }}</option>

        @foreach($options as $option)
            <option
                value="{{ data_get($option, $optionValue) }}"
                {{ (string) $value === (string) data_get($option, $optionValue) ? 'selected' : '' }}
            >
                {{ data_get($option, $optionLabel) }}
            </option>
        @endforeach
    </select>
</div>