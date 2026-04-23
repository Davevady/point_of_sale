<script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
<script src="{{ asset('assets/js/sb-admin-2.min.js') }}"></script>

<script>
    // load state
    if (localStorage.getItem('sidebar-toggled') === 'true') {
        document.body.classList.add('sidebar-toggled');
        document.querySelector('.sidebar').classList.add('toggled');
    }

    // toggle listener
    document.getElementById('sidebarToggle')?.addEventListener('click', function () {
        const isToggled = document.body.classList.contains('sidebar-toggled');

        localStorage.setItem('sidebar-toggled', !isToggled);
    });
</script>

@stack('scripts')