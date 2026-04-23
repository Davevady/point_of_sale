<!DOCTYPE html>
<html lang="en">
<head>
    @include('partials.head')
</head>

<body id="page-top">
    <div id="wrapper">

        @include('partials.sidebar')

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">

                @include('partials.topbar')

                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>

            @include('partials.footer')
        </div>
    </div>

    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    @include('partials.logout-modal')
    @include('partials.scripts')
</body>
</html>