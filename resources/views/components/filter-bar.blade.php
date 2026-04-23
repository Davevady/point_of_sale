@props([
    'action' => '',
])

<div class="card shadow mb-4 border-0 filter-bar">
    <div class="card-body">
        <form method="GET" action="{{ $action ?: url()->current() }}">
            <div class="row align-items-end">
                {{ $slot }}

                <div class="col-md-auto col-sm-12 mt-3 mt-md-0">
                    <button type="submit" class="btn btn-primary btn-sm">
                        Filter
                    </button>

                    <a href="{{ $action ?: url()->current() }}" class="btn btn-secondary btn-sm">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>