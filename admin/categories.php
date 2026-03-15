<?php
$pageTitle = 'Categories';
require_once dirname(__DIR__) . '/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CSRF::verifyOrDie();
    $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
    $data = $_POST;
    $data['is_active'] = isset($_POST['is_active']) ? 1 : 0;
    // Handle image upload OR URL
    if (!empty($_FILES['image_file']['name'])) {
        $up = ImageUpload::upload($_FILES['image_file'], UPLOAD_PATH . '/categories', 'cat');
        if ($up['success']) $data['image'] = $up['url'];
    } elseif (!empty($_POST['image_url'])) {
        $data['image'] = $_POST['image_url'];
    }
    $result = saveCategory($data, $id);
    Flash::success($id ? 'Category updated.' : 'Category created.');
    redirect(BASE_URL . '/admin/categories');
}
if (isset($_GET['delete'])) {
    deleteCategory((int)$_GET['delete']); Flash::success('Deleted.');
    redirect(BASE_URL . '/admin/categories');
}

$categories = getCategories(false);
$editCat = isset($_GET['edit']) ? getCategory((int)$_GET['edit']) : null;
include __DIR__ . '/header.php';
?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <table class="data-table">
                <thead><tr><th>Image</th><th>Name</th><th>Slug</th><th>Icon</th><th>Cars</th><th>Active</th><th class="text-right">Actions</th></tr></thead>
                <tbody>
                <?php foreach ($categories as $c): 
                    $catImg = $c['image'] ? resolveUrl($c['image']) : '';
                ?>
                <tr>
                    <td>
                        <?php if ($catImg): ?>
                        <img src="<?= $catImg ?>" alt="<?= clean($c['name']) ?>" class="h-8 w-8 object-contain">
                        <?php else: ?>
                        <i data-lucide="<?= $c['icon'] ?: 'car' ?>" class="w-5 h-5 text-gray-400"></i>
                        <?php endif; ?>
                    </td>
                    <td class="font-semibold text-dark-900"><?= clean($c['name']) ?></td>
                    <td class="text-xs text-gray-400"><?= $c['slug'] ?></td>
                    <td class="text-xs text-gray-400"><?= $c['icon'] ?: '-' ?></td>
                    <td><?= $c['car_count'] ?></td>
                    <td><?= $c['is_active'] ? '<span class="text-emerald-500">●</span>' : '<span class="text-gray-300">●</span>' ?></td>
                    <td><div class="actions justify-end">
                        <a href="?edit=<?= $c['id'] ?>" class="btn btn-icon btn-sm btn-ghost"><i data-lucide="pencil" class="w-4 h-4"></i></a>
                        <button onclick="confirmAction('Delete?','?delete=<?= $c['id'] ?>')" class="btn btn-icon btn-sm btn-ghost text-red-500"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                    </div></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="font-bold text-dark-900 mb-4"><?= $editCat ? 'Edit' : 'Add' ?> Category</h3>
            <form method="POST" enctype="multipart/form-data">
                <?= CSRF::field() ?>
                <?php if ($editCat): ?><input type="hidden" name="id" value="<?= $editCat['id'] ?>"><?php endif; ?>
                <div class="form-group">
                    <label class="form-label">Name *</label>
                    <input type="text" name="name" required class="form-control" value="<?= clean($editCat['name'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Icon (Lucide name, fallback)</label>
                    <input type="text" name="icon" class="form-control" value="<?= clean($editCat['icon'] ?? '') ?>" placeholder="e.g. car, truck, zap">
                    <p class="text-xs text-gray-400 mt-1">Used only if no image is set. <a href="https://lucide.dev/icons/" target="_blank" class="text-primary-600">Browse icons</a></p>
                </div>

                <!-- Current Image Preview -->
                <?php if ($editCat && $editCat['image']): ?>
                <div class="form-group">
                    <label class="form-label">Current Image</label>
                    <div class="p-3 bg-gray-50 rounded-lg flex items-center gap-3">
                        <img src="<?= resolveUrl($editCat['image']) ?>" alt="" class="h-10 w-10 object-contain">
                        <span class="text-xs text-gray-400 truncate flex-1"><?= clean(excerpt($editCat['image'], 50)) ?></span>
                    </div>
                </div>
                <?php endif; ?>

                <div class="form-group">
                    <label class="form-label">Category Image URL</label>
                    <input type="url" name="image_url" class="form-control" value="<?= clean($editCat['image'] ?? '') ?>" placeholder="https://... PNG with transparent bg">
                    <p class="text-xs text-gray-400 mt-1">Small icon/silhouette image, transparent background</p>
                </div>
                <div class="form-group">
                    <label class="form-label">Or Upload Image</label>
                    <input type="file" name="image_file" accept="image/*" class="form-control">
                </div>
                <div class="form-group"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="2"><?= clean($editCat['description'] ?? '') ?></textarea></div>
                <div class="form-group"><label class="form-label">Sort Order</label><input type="number" name="sort_order" class="form-control" value="<?= $editCat['sort_order'] ?? 0 ?>"></div>
                <label class="form-check mb-4"><input type="checkbox" name="is_active" value="1" <?= ($editCat['is_active'] ?? 1) ? 'checked' : '' ?>> Active</label>
                <button type="submit" class="btn btn-primary w-full"><i data-lucide="save" class="w-4 h-4"></i> Save</button>
                <?php if ($editCat): ?><a href="<?= BASE_URL ?>/admin/categories" class="btn btn-ghost w-full mt-2">Cancel</a><?php endif; ?>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
