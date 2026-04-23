@props([
    'name',
    'label' => null,
    'placeholder' => 'Pilih produk',
    'options' => [],
    'value' => null,
])

@php
    $uid = 'sps_' . md5($name . uniqid('', true));
    $hasValue = $value !== null && $value !== '';
    $selectedLabel = $placeholder;
    if ($hasValue) {
        foreach ($options as $opt) {
            if ((string) $opt->id === (string) $value) {
                $selectedLabel = $opt->name . ' - ' . ($opt->category->name ?? '-') . ' - Rp ' . number_format($opt->price, 0, ',', '.');
                break;
            }
        }
    }
@endphp

<div class="form-group mb-0">
    @if ($label)
        <label class="small text-muted font-weight-bold">{{ $label }}</label>
    @endif

    <div class="searchable-select-wrapper" id="{{ $uid }}" style="position: relative;">
        <input type="hidden" name="{{ $name }}" id="{{ $uid }}_val" value="{{ $value ?? '' }}">

        <div class="form-control d-flex justify-content-between align-items-center searchable-select-trigger"
             id="{{ $uid }}_trigger">
            <span id="{{ $uid }}_display" class="{{ $hasValue ? '' : 'text-muted' }}">{{ $selectedLabel }}</span>
            <i class="fas fa-chevron-down fa-xs text-muted ml-2" style="flex-shrink: 0;"></i>
        </div>

        <div class="searchable-select-dropdown border rounded bg-white shadow-sm"
             id="{{ $uid }}_dropdown"
             style="display: none; position: absolute; z-index: 1050; width: 100%; top: calc(100% + 2px); left: 0;">
            <div class="p-2 border-bottom">
                <input type="text" class="form-control form-control-sm" id="{{ $uid }}_search" placeholder="Cari...">
            </div>
            <ul class="list-unstyled mb-0" id="{{ $uid }}_list" style="max-height: 220px; overflow-y: auto;">
                <li class="px-3 py-2 searchable-select-opt text-muted"
                    data-value="" data-label="{{ $placeholder }}">{{ $placeholder }}</li>
                @foreach ($options as $opt)
                    @php
                        $optLabel = $opt->name . ' - ' . ($opt->category->name ?? '-') . ' - Rp ' . number_format($opt->price, 0, ',', '.');
                    @endphp
                    <li class="px-3 py-2 searchable-select-opt"
                        data-value="{{ $opt->id }}"
                        data-label="{{ $optLabel }}">{{ $optLabel }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>

<script>
(function () {
    if (!window._spsReady) {
        window._spsReady = true;

        window.initSearchableSelect = function (uid) {
            var wrapper  = document.getElementById(uid);
            var trigger  = document.getElementById(uid + '_trigger');
            var dropdown = document.getElementById(uid + '_dropdown');
            var search   = document.getElementById(uid + '_search');
            var list     = document.getElementById(uid + '_list');
            var valInput = document.getElementById(uid + '_val');
            var display  = document.getElementById(uid + '_display');
            if (!wrapper) return;

            trigger.addEventListener('click', function (e) {
                e.stopPropagation();
                var isOpen = dropdown.style.display !== 'none';
                document.querySelectorAll('.searchable-select-dropdown').forEach(function (d) {
                    d.style.display = 'none';
                });
                if (!isOpen) {
                    dropdown.style.display = 'block';
                    search.value = '';
                    filterOpts('');
                    search.focus();
                }
            });

            search.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') dropdown.style.display = 'none';
            });

            search.addEventListener('input', function () {
                filterOpts(this.value.trim().toLowerCase());
            });

            list.addEventListener('click', function (e) {
                var opt = e.target.closest('.searchable-select-opt');
                if (!opt) return;
                valInput.value = opt.dataset.value;
                display.textContent = opt.dataset.label;
                display.classList.toggle('text-muted', !opt.dataset.value);
                dropdown.style.display = 'none';
            });

            function filterOpts(q) {
                list.querySelectorAll('.searchable-select-opt').forEach(function (li) {
                    li.style.display = (!q || li.dataset.label.toLowerCase().includes(q)) ? '' : 'none';
                });
            }
        };

        document.addEventListener('click', function () {
            document.querySelectorAll('.searchable-select-dropdown').forEach(function (d) {
                d.style.display = 'none';
            });
        });
    }

    window.initSearchableSelect('{{ $uid }}');
})();
</script>
