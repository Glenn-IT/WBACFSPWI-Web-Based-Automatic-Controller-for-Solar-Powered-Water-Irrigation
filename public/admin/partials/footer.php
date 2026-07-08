</main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/app.js"></script>
<script>
(function () {
    const sidebar = document.getElementById('sidebar');
    const toggle = document.getElementById('mobileNavToggle');
    const backdrop = document.getElementById('sidebarBackdrop');
    if (!sidebar || !toggle || !backdrop) return;

    function closeSidebar() {
        sidebar.classList.remove('sidebar-open');
        backdrop.classList.remove('show');
    }

    toggle.addEventListener('click', function () {
        sidebar.classList.toggle('sidebar-open');
        backdrop.classList.toggle('show');
    });
    backdrop.addEventListener('click', closeSidebar);
    sidebar.querySelectorAll('a.nav-link').forEach(link => link.addEventListener('click', closeSidebar));
})();
</script>
</body>
</html>
