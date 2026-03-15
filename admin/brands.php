<?php
$pageTitle = 'Brands';
require_once dirname(__DIR__) . '/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CSRF::verifyOrDie();
    $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
    $data = $_POST;
    $data['is_popular'] = isset($_POST['is_popular']) ? 1 : 0;
    $data['is_active'] = isset($_POST['is_active']) ? 1 : 0;
    // Handle logo upload OR URL
    if (!empty($_FILES['logo_file']['name'])) {
        $up = ImageUpload::upload($_FILES['logo_file'], LOGOS_UPLOAD_PATH, 'brand');
        if ($up['success']) $data['logo'] = $up['url'];
    } elseif (!empty($_POST['logo_url'])) {
        $data['logo'] = $_POST['logo_url'];
    }
    saveBrand($data, $id);
    Flash::success($id ? 'Brand updated.' : 'Brand created.');
    redirect(BASE_URL . '/admin/brands');
}
if (isset($_GET['delete'])) { deleteBrand((int)$_GET['delete']); Flash::success('Deleted.'); redirect(BASE_URL . '/admin/brands'); }

$brands = getBrands(false);
$editBrand = isset($_GET['edit']) ? getBrand((int)$_GET['edit']) : null;
include __DIR__ . '/header.php';
?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <table class="data-table">
                <thead><tr><th>Logo</th><th>Brand</th><th>Slug</th><th>Cars</th><th>Popular</th><th>Active</th><th class="text-right">Actions</th></tr></thead>
                <tbody>
                <?php foreach ($brands as $b): 
                    $logoUrl = $b['logo'] ? resolveUrl($b['logo']) : '';
                ?>
                <tr>
                    <td>
                        <?php if ($logoUrl): ?>
                        <img src="<?= $logoUrl ?>" alt="<?= clean($b['name']) ?>" class="h-8 w-auto max-w-[60px] object-contain">
                        <?php else: ?>
                        <span class="text-sm font-bold text-gray-400"><?= strtoupper(substr($b['name'],0,2)) ?></span>
                        <?php endif; ?>
                    </td>
                    <td class="font-semibold text-dark-900"><?= clean($b['name']) ?></td>
                    <td class="text-xs text-gray-400"><?= $b['slug'] ?></td>
                    <td><?= $b['car_count'] ?></td>
                    <td><?= $b['is_popular'] ? '<span class="text-amber-500">★</span>' : '-' ?></td>
                    <td><?= $b['is_active'] ? '<span class="text-emerald-500">●</span>' : '<span class="text-gray-300">●</span>' ?></td>
                    <td><div class="actions justify-end">
                        <a href="?edit=<?= $b['id'] ?>" class="btn btn-icon btn-sm btn-ghost"><i data-lucide="pencil" class="w-4 h-4"></i></a>
                        <button onclick="confirmAction('Delete?','?delete=<?= $b['id'] ?>')" class="btn btn-icon btn-sm btn-ghost text-red-500"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                    </div></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="font-bold text-dark-900 mb-4"><?= $editBrand ? 'Edit' : 'Add' ?> Brand</h3>
            <form method="POST" enctype="multipart/form-data">
                <?= CSRF::field() ?>
                <?php if ($editBrand): ?><input type="hidden" name="id" value="<?= $editBrand['id'] ?>"><?php endif; ?>
                <div class="form-group">
                    <label class="form-label">Name *</label>
                    <input type="text" name="name" required class="form-control" value="<?= clean($editBrand['name'] ?? '') ?>">
                </div>
                
                <!-- Current Logo Preview -->
                <?php if ($editBrand && $editBrand['logo']): ?>
                <div class="form-group">
                    <label class="form-label">Current Logo</label>
                    <div class="p-3 bg-gray-50 rounded-lg flex items-center gap-3">
                        <img src="<?= resolveUrl($editBrand['logo']) ?>" alt="" class="h-10 w-auto max-w-[80px] object-contain">
                        <span class="text-xs text-gray-400 truncate flex-1"><?= clean(excerpt($editBrand['logo'], 50)) ?></span>
                    </div>
                </div>
                <?php endif; ?>

                <div class="form-group">
                    <label class="form-label">Logo Image URL</label>
                    <input type="url" name="logo_url" class="form-control" value="<?= clean($editBrand['logo'] ?? '') ?>" placeholder="https://... or leave empty">
                    <p class="text-xs text-gray-400 mt-1">Paste a URL to an SVG/PNG logo (transparent bg recommended)</p>
                </div>
                <div class="form-group">
                    <label class="form-label">Or Upload Logo File</label>
                    <input type="file" name="logo_file" accept="image/*" class="form-control">
                    <p class="text-xs text-gray-400 mt-1">Upload overrides the URL above</p>
                </div>
                <div class="form-group"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="2"><?= clean($editBrand['description'] ?? '') ?></textarea></div>
                <div class="form-group"><label class="form-label">Sort Order</label><input type="number" name="sort_order" class="form-control" value="<?= $editBrand['sort_order'] ?? 0 ?>"></div>
                <label class="form-check"><input type="checkbox" name="is_popular" value="1" <?= ($editBrand['is_popular'] ?? 0) ? 'checked' : '' ?>> Popular Brand (shown on homepage)</label>
                <label class="form-check mb-4"><input type="checkbox" name="is_active" value="1" <?= ($editBrand['is_active'] ?? 1) ? 'checked' : '' ?>> Active</label>
                <button type="submit" class="btn btn-primary w-full"><i data-lucide="save" class="w-4 h-4"></i> Save</button>
                <?php if ($editBrand): ?><a href="<?= BASE_URL ?>/admin/brands" class="btn btn-ghost w-full mt-2">Cancel</a><?php endif; ?>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
