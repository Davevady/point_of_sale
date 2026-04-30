@props([
    'title'             => null,
    'subtitle'          => null,
    'paginator'         => null,
    'perPage'           => 10,
    'searchable'        => false,
    'searchPlaceholder' => 'Search...',
    'searchName'        => 'search',
    'searchValue'       => '',
    'searchAction'      => '',
])

<div class="card shadow mb-4 border-0">
    @if ($title || $subtitle || $paginator)
        <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap" style="gap:8px;">
            <div>
                @if ($title)
                    <h6 class="m-0 font-weight-bold text-primary">{{ $title }}</h6>
                @endif

                @if ($subtitle)
                    <small class="text-muted d-block mt-1">{{ $subtitle }}</small>
                @elseif ($paginator)
                    <small class="text-muted d-block mt-1">Total: {{ number_format($paginator->total()) }} data</small>
                @endif
            </div>

            @if ($paginator)
                <div class="d-flex align-items-center" style="gap:6px;">
                    <label class="small text-muted mb-0 font-weight-bold">Tampilkan</label>
                    <select class="form-control form-control-sm per-page-select" style="width:75px;">
                        @foreach([10, 25, 50, 100] as $opt)
                            <option value="{{ $opt }}" {{ (int)$perPage === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>
    @endif

    <div class="card-body">
        @if ($searchable)
            <form action="{{ $searchAction ?: url()->current() }}" method="GET" class="mb-3">
                <div class="justify-content-end row">
                    <div class="col-md-4 col-sm-12 mb-2 mb-md-0">
                        <div class="input-group">
                            <input type="text" name="{{ $searchName }}"
                                class="form-control bg-light border-0 small" placeholder="{{ $searchPlaceholder }}"
                                value="{{ $searchValue }}">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    @if ($searchValue)
                        <div class="col-md-auto col-sm-12">
                            <a href="{{ $searchAction ?: url()->current() }}" class="btn btn-secondary btn-sm">
                                Reset
                            </a>
                        </div>
                    @endif
                </div>
            </form>
        @endif

        @isset($toolbar)
            <div class="mb-3">
                {{ $toolbar }}
            </div>
        @endisset

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" width="100%" cellspacing="0">
                {{ $slot }}
            </table>
        </div>

        @if ($paginator && $paginator->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap" style="gap:8px;">
                <div class="text-muted small">
                    Menampilkan {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }}
                    dari {{ number_format($paginator->total()) }} data
                </div>
                {{ $paginator->links('pagination::bootstrap-4') }}
            </div>
        @elseif ($paginator && $paginator->total() > 0)
            <div class="text-muted small mt-2">
                Menampilkan semua {{ number_format($paginator->total()) }} data
            </div>
        @endif
    </div>
</div>

<script>
    document.querySelectorAll('.per-page-select').forEach(function (select) {
        select.addEventListener('change', function () {
            const url = new URL(window.location.href);
            url.searchParams.set('per_page', this.value);
            url.searchParams.delete('page');
            window.location.href = url.toString();
        });
    });
</script>
