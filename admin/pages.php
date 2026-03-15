<?php
$pageTitle = 'Manage Pages';
require_once dirname(__DIR__) . '/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CSRF::verifyOrDie();
    savePage($_POST, (int)$_POST['id']);
    Flash::success('Page updated.'); redirect(BASE_URL . '/admin/pages');
}

$pages = db()->query("SELECT * FROM pages ORDER BY id")->fetchAll();
$editPage = isset($_GET['edit']) ? (function(){ $s=db()->prepare("SELECT * FROM pages WHERE id=?"); $s->execute([(int)$_GET['edit']]); return $s->fetch(); })() : null;
include __DIR__ . '/header.php';
?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div>
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <?php foreach($pages as $p): ?>
            <a href="?edit=<?= $p['id'] ?>" class="flex items-center justify-between px-4 py-3 border-b border-gray-100 hover:bg-gray-50 transition <?= ($editPage['id'] ?? 0)==$p['id'] ? 'bg-primary-50 border-l-2 border-l-primary-600' : '' ?>">
                <div><p class="text-sm font-semibold text-dark-900"><?= clean($p['title']) ?></p><p class="text-xs text-gray-400">/<?= $p['slug'] ?></p></div>
                <?= $p['is_active'] ? '<span class="text-emerald-500 text-[10px]">●</span>' : '<span class="text-gray-300 text-[10px]">●</span>' ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="lg:col-span-2">
        <?php if ($editPage): ?>
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-dark-900 mb-4">Edit: <?= clean($editPage['title']) ?></h3>
            <form method="POST"><?= CSRF::field() ?>
                <input type="hidden" name="id" value="<?= $editPage['id'] ?>">
                <div class="form-group"><label class="form-label">Title</label><input type="text" name="title" required class="form-control" value="<?= clean($editPage['title']) ?>"></div>
                <div class="form-group"><label class="form-label">Content (HTML allowed)</label><textarea name="content" class="form-control font-mono text-sm" rows="15"><?= clean($editPage['content']) ?></textarea></div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="form-group"><label class="form-label">Meta Title</label><input type="text" name="meta_title" class="form-control" value="<?= clean($editPage['meta_title']) ?>"></div>
                    <div class="form-group"><label class="form-label">Meta Description</label><input type="text" name="meta_description" class="form-control" value="<?= clean($editPage['meta_description']) ?>"></div>
                </div>
                <label class="form-check mb-4"><input type="checkbox" name="is_active" value="1" <?= $editPage['is_active']?'checked':'' ?>> Active</label>
                <div class="flex gap-2"><button type="submit" class="btn btn-primary"><i data-lucide="save" class="w-4 h-4"></i> Save Page</button><a href="<?= BASE_URL ?>/<?= $editPage['slug'] ?>" target="_blank" class="btn btn-outline"><i data-lucide="external-link" class="w-4 h-4"></i> View</a></div>
            </form>
        </div>
        <?php else: ?>
        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center"><p class="text-gray-400">Select a page from the left to edit it.</p></div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
