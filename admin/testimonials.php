<?php
$pageTitle = 'Testimonials';
require_once dirname(__DIR__) . '/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CSRF::verifyOrDie();
    $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
    $isActive = isset($_POST['is_active']) ? 1 : 0;
    if ($id) {
        db()->prepare("UPDATE testimonials SET name=?, role=?, content=?, rating=?, is_active=?, sort_order=? WHERE id=?")->execute([$_POST['name'],$_POST['role']??'',$_POST['content'],$_POST['rating']??5,$isActive,$_POST['sort_order']??0,$id]);
    } else {
        db()->prepare("INSERT INTO testimonials (name,role,content,rating,is_active,sort_order) VALUES (?,?,?,?,?,?)")->execute([$_POST['name'],$_POST['role']??'',$_POST['content'],$_POST['rating']??5,$isActive,$_POST['sort_order']??0]);
    }
    Flash::success('Saved.'); redirect(BASE_URL.'/admin/testimonials');
}
if (isset($_GET['delete'])) { db()->prepare("DELETE FROM testimonials WHERE id=?")->execute([(int)$_GET['delete']]); Flash::success('Deleted.'); redirect(BASE_URL.'/admin/testimonials'); }

$testimonials = getTestimonials(false, 100);
$edit = isset($_GET['edit']) ? (function(){ $s=db()->prepare("SELECT * FROM testimonials WHERE id=?"); $s->execute([(int)$_GET['edit']]); return $s->fetch(); })() : null;
include __DIR__ . '/header.php';
?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="space-y-3">
            <?php foreach($testimonials as $t): ?>
            <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-start gap-4">
                <div class="w-10 h-10 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center font-bold flex-shrink-0"><?= strtoupper(substr($t['name'],0,1)) ?></div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2"><span class="font-semibold text-sm text-dark-900"><?= clean($t['name']) ?></span><span class="text-xs text-gray-400"><?= clean($t['role']) ?></span></div>
                    <p class="text-sm text-gray-600 mt-1">"<?= clean(excerpt($t['content'],120)) ?>"</p>
                    <div class="flex items-center gap-1 mt-1 text-amber-400"><?php for($i=0;$i<$t['rating'];$i++): ?><i data-lucide="star" class="w-3 h-3 fill-amber-400"></i><?php endfor; ?></div>
                </div>
                <div class="flex gap-1 flex-shrink-0">
                    <?= $t['is_active'] ? '<span class="text-emerald-500 text-xs">●</span>' : '<span class="text-gray-300 text-xs">●</span>' ?>
                    <a href="?edit=<?= $t['id'] ?>" class="btn btn-icon btn-sm btn-ghost"><i data-lucide="pencil" class="w-3 h-3"></i></a>
                    <button onclick="confirmAction('Delete?','?delete=<?= $t['id'] ?>')" class="btn btn-icon btn-sm btn-ghost text-red-500"><i data-lucide="trash-2" class="w-3 h-3"></i></button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="font-bold text-dark-900 mb-4"><?= $edit ? 'Edit' : 'Add' ?> Testimonial</h3>
            <form method="POST"><?= CSRF::field() ?>
                <?php if($edit): ?><input type="hidden" name="id" value="<?= $edit['id'] ?>"><?php endif; ?>
                <div class="form-group"><label class="form-label">Name *</label><input type="text" name="name" required class="form-control" value="<?= clean($edit['name']??'') ?>"></div>
                <div class="form-group"><label class="form-label">Role</label><input type="text" name="role" class="form-control" value="<?= clean($edit['role']??'') ?>" placeholder="e.g. Business Owner"></div>
                <div class="form-group"><label class="form-label">Testimonial *</label><textarea name="content" required class="form-control" rows="3"><?= clean($edit['content']??'') ?></textarea></div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="form-group"><label class="form-label">Rating</label><select name="rating" class="form-control"><?php for($i=5;$i>=1;$i--): ?><option value="<?= $i ?>" <?= ($edit['rating']??5)==$i?'selected':'' ?>><?= $i ?> Stars</option><?php endfor; ?></select></div>
                    <div class="form-group"><label class="form-label">Order</label><input type="number" name="sort_order" class="form-control" value="<?= $edit['sort_order']??0 ?>"></div>
                </div>
                <label class="form-check mb-4"><input type="checkbox" name="is_active" value="1" <?= ($edit['is_active']??1)?'checked':'' ?>> Active</label>
                <button type="submit" class="btn btn-primary w-full"><i data-lucide="save" class="w-4 h-4"></i> Save</button>
                <?php if($edit): ?><a href="<?= BASE_URL ?>/admin/testimonials" class="btn btn-ghost w-full mt-2">Cancel</a><?php endif; ?>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
