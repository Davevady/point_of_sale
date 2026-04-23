@props([
    'title' => null,
    'subtitle' => null,
    'searchable' => false,
    'searchPlaceholder' => 'Search...',
    'searchName' => 'search',
    'searchValue' => '',
    'searchAction' => '',
])

<div class="card shadow mb-4 border-0">
    @if ($title || $subtitle)
        <div class="card-header py-3">
            @if ($title)
                <h6 class="m-0 font-weight-bold text-primary">{{ $title }}</h6>
            @endif

            @if ($subtitle)
                <small class="text-muted d-block mt-1">{{ $subtitle }}</small>
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
    </div>
</div>
