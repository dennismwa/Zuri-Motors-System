    </div>
</div>
<script>
    lucide.createIcons();
    document.querySelectorAll('.flash-message').forEach(msg => { setTimeout(() => { msg.style.opacity='0'; msg.style.transition='opacity 300ms'; setTimeout(()=>msg.remove(),300); }, 5000); });
    document.addEventListener('click', (e) => { const s=document.getElementById('admin-sidebar'); if(s.classList.contains('open')&&!s.contains(e.target)&&!e.target.closest('[onclick*="admin-sidebar"]'))s.classList.remove('open'); });
    const BASE_URL = '<?= BASE_URL ?>';
    function confirmAction(msg, url) { if(confirm(msg)) window.location.href = url; }
</script>
<?php if (isset($pageScripts)) echo $pageScripts; ?>
</body>
</html>
