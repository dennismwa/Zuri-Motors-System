<?php
$pageTitle = 'Features';
require_once dirname(__DIR__) . '/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CSRF::verifyOrDie();
    $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
    $data = $_POST; $data['is_active'] = isset($_POST['is_active']) ? 1 : 0;
    saveFeature($data, $id);
    Flash::success($id ? 'Feature updated.' : 'Feature created.');
    redirect(BASE_URL . '/admin/features');
}
if (isset($_GET['delete'])) { deleteFeature((int)$_GET['delete']); Flash::success('Deleted.'); redirect(BASE_URL . '/admin/features'); }

$features = getFeatures(false);
$editFeature = isset($_GET['edit']) ? (function() { $stmt = db()->prepare("SELECT * FROM features WHERE id=?"); $stmt->execute([(int)$_GET['edit']]); return $stmt->fetch(); })() : null;
$featureCategories = ['comfort','technology','safety','exterior','performance'];
include __DIR__ . '/header.php';
?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <table class="data-table">
                <thead><tr><th>Feature</th><th>Icon</th><th>Category</th><th>Active</th><th class="text-right">Actions</th></tr></thead>
                <tbody>
                <?php foreach ($features as $f): ?>
                <tr>
                    <td class="font-semibold text-dark-900 text-sm"><?= clean($f['name']) ?></td>
                    <td><i data-lucide="<?= $f['icon'] ?: 'check' ?>" class="w-4 h-4"></i> <span class="text-xs text-gray-400"><?= $f['icon'] ?></span></td>
                    <td><?= statusBadge($f['category']) ?></td>
                    <td><?= $f['is_active'] ? '<span class="text-emerald-500">●</span>' : '<span class="text-gray-300">●</span>' ?></td>
                    <td><div class="actions justify-end">
                        <a href="?edit=<?= $f['id'] ?>" class="btn btn-icon btn-sm btn-ghost"><i data-lucide="pencil" class="w-4 h-4"></i></a>
                        <button onclick="confirmAction('Delete?','?delete=<?= $f['id'] ?>')" class="btn btn-icon btn-sm btn-ghost text-red-500"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                    </div></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="font-bold text-dark-900 mb-4"><?= $editFeature ? 'Edit' : 'Add' ?> Feature</h3>
            <form method="POST">
                <?= CSRF::field() ?>
                <?php if ($editFeature): ?><input type="hidden" name="id" value="<?= $editFeature['id'] ?>"><?php endif; ?>
                <div class="form-group"><label class="form-label">Name *</label><input type="text" name="name" required class="form-control" value="<?= clean($editFeature['name'] ?? '') ?>"></div>
                <div class="form-group"><label class="form-label">Icon (Lucide name)</label><input type="text" name="icon" class="form-control" value="<?= clean($editFeature['icon'] ?? 'check') ?>" placeholder="e.g. wind, bluetooth"></div>
                <div class="form-group"><label class="form-label">Category</label>
                    <select name="category" class="form-control">
                        <?php foreach($featureCategories as $fc): ?><option value="<?= $fc ?>" <?= ($editFeature['category'] ?? '')===$fc?'selected':'' ?>><?= ucfirst($fc) ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group"><label class="form-label">Sort Order</label><input type="number" name="sort_order" class="form-control" value="<?= $editFeature['sort_order'] ?? 0 ?>"></div>
                <label class="form-check mb-4"><input type="checkbox" name="is_active" value="1" <?= ($editFeature['is_active'] ?? 1) ? 'checked' : '' ?>> Active</label>
                <button type="submit" class="btn btn-primary w-full"><i data-lucide="save" class="w-4 h-4"></i> Save</button>
                <?php if ($editFeature): ?><a href="<?= BASE_URL ?>/admin/features" class="btn btn-ghost w-full mt-2">Cancel</a><?php endif; ?>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
