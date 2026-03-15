    </div><!-- /admin-content -->
</div><!-- /admin-main -->

<script>
    lucide.createIcons();
    // Auto-dismiss flash messages
    document.querySelectorAll('.flash-message').forEach(msg => {
        setTimeout(() => { msg.style.opacity='0'; msg.style.transition='opacity 300ms'; setTimeout(()=>msg.remove(),300); }, 5000);
    });
    // Sidebar mobile close on outside click
    document.addEventListener('click', (e) => {
        const sidebar = document.getElementById('admin-sidebar');
        if (sidebar.classList.contains('open') && !sidebar.contains(e.target) && !e.target.closest('[onclick*="admin-sidebar"]')) {
            sidebar.classList.remove('open');
        }
    });
    const BASE_URL = '<?= BASE_URL ?>';

    function confirmAction(msg, url) { if(confirm(msg)) window.location.href = url; }
</script>
<?php if (isset($pageScripts)) echo $pageScripts; ?>
</body>
</html>
