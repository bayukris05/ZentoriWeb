</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const toggle = document.getElementById('sidebarToggle');
        const mainContent = document.getElementById('mainContent');

        if (window.innerWidth <= 768) {
            sidebar.classList.toggle('active');
            toggle.classList.toggle('active');
        } else {
            sidebar.classList.toggle('collapsed');
            toggle.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        }
    }

    window.addEventListener('resize', function() {
        const sidebar = document.getElementById('sidebar');
        const toggle = document.getElementById('sidebarToggle');
        const mainContent = document.getElementById('mainContent');

        if (window.innerWidth > 768) {
            sidebar.classList.remove('active');
            toggle.classList.remove('active');
        } else {
            sidebar.classList.remove('collapsed');
            toggle.classList.remove('collapsed');
            mainContent.classList.remove('expanded');
        }
    });
</script>
</body>

</html>