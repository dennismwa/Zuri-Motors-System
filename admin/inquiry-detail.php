<?php
$pageTitle = 'Inquiry Detail';
require_once dirname(__DIR__) . '/functions.php';

$id = (int)($_GET['id'] ?? 0);
$inquiry = getInquiry($id);
if (!$inquiry) { Flash::error('Inquiry not found.'); redirect(BASE_URL . '/admin/inquiries'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CSRF::verifyOrDie();
    if (isset($_POST['update_status'])) {
        saveInquiry(['status'=>$_POST['status'], 'priority'=>$_POST['priority'], 'assigned_to'=>$_POST['assigned_to'] ?: null], $id);
        Flash::success('Inquiry updated.');
    }
    if (isset($_POST['add_note']) && !empty($_POST['note'])) {
        addInquiryNote($id, Auth::id(), $_POST['note']);
        Flash::success('Note added.');
    }
    redirect(BASE_URL . '/admin/inquiry/' . $id);
}

$notes = getInquiryNotes($id);
$staff = getStaffUsers();
include __DIR__ . '/header.php';
?>

<a href="<?= BASE_URL ?>/admin/inquiries" class="btn btn-ghost btn-sm mb-4"><i data-lucide="arrow-left" class="w-4 h-4"></i> Back</a>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-5">
        <!-- Inquiry Info -->
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-dark-900">Inquiry #<?= $id ?></h3>
                <div class="flex gap-2"><?= statusBadge($inquiry['status']) ?> <?= statusBadge($inquiry['priority']) ?></div>
            </div>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div><span class="text-gray-400">Name:</span> <span class="font-semibold text-dark-900"><?= clean($inquiry['name']) ?></span></div>
                <div><span class="text-gray-400">Phone:</span> <a href="tel:<?= $inquiry['phone'] ?>" class="font-semibold text-primary-600"><?= clean($inquiry['phone']) ?></a></div>
                <div><span class="text-gray-400">Email:</span> <span class="font-medium"><?= clean($inquiry['email'] ?: '-') ?></span></div>
                <div><span class="text-gray-400">Type:</span> <?= statusBadge($inquiry['type']) ?></div>
                <div><span class="text-gray-400">Date:</span> <?= formatDate($inquiry['created_at'], 'd M Y H:i') ?></div>
                <div><span class="text-gray-400">Source:</span> <?= clean($inquiry['source']) ?></div>
                <?php if ($inquiry['car_title']): ?>
                <div class="col-span-2"><span class="text-gray-400">Car:</span> <a href="<?= BASE_URL ?>/car/<?= $inquiry['car_slug'] ?>" class="font-semibold text-primary-600" target="_blank"><?= clean($inquiry['car_title']) ?></a> (<?= formatPrice($inquiry['car_price']) ?>)</div>
                <?php endif; ?>
                <?php if ($inquiry['car_model_sell']): ?>
                <div><span class="text-gray-400">Selling:</span> <?= clean($inquiry['car_model_sell']) ?></div>
                <div><span class="text-gray-400">Expected Price:</span> <?= $inquiry['price_expectation'] ? formatPrice($inquiry['price_expectation']) : '-' ?></div>
                <?php endif; ?>
            </div>
            <?php if ($inquiry['message']): ?>
            <div class="mt-4 p-4 bg-gray-50 rounded-lg"><p class="text-sm text-gray-600"><?= nl2br(clean($inquiry['message'])) ?></p></div>
            <?php endif; ?>
            <div class="flex gap-2 mt-4">
                <a href="tel:<?= $inquiry['phone'] ?>" class="btn btn-primary btn-sm"><i data-lucide="phone" class="w-4 h-4"></i> Call</a>
                <a href="<?= whatsappLink('Hi '.$inquiry['name'].', regarding your inquiry...', preg_replace('/[^0-9]/','',$inquiry['phone'])) ?>" target="_blank" class="btn btn-sm bg-[#25D366] text-white hover:bg-[#20BD5A]"><i data-lucide="message-circle" class="w-4 h-4"></i> WhatsApp</a>
                <?php if ($inquiry['email']): ?><a href="mailto:<?= $inquiry['email'] ?>" class="btn btn-outline btn-sm"><i data-lucide="mail" class="w-4 h-4"></i> Email</a><?php endif; ?>
                <a href="<?= BASE_URL ?>/admin/invoice-add?inquiry_id=<?= $id ?>&customer_name=<?= urlencode($inquiry['name']) ?>&customer_phone=<?= urlencode($inquiry['phone']) ?>&car_id=<?= $inquiry['car_id'] ?>" class="btn btn-outline btn-sm"><i data-lucide="file-text" class="w-4 h-4"></i> Create Invoice</a>
            </div>
        </div>

        <!-- Notes -->
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="font-bold text-dark-900 mb-4">Notes (<?= count($notes) ?>)</h3>
            <form method="POST" class="mb-4">
                <?= CSRF::field() ?>
                <textarea name="note" class="form-control mb-2" rows="2" placeholder="Add a note..." required></textarea>
                <button type="submit" name="add_note" class="btn btn-primary btn-sm"><i data-lucide="plus" class="w-4 h-4"></i> Add Note</button>
            </form>
            <div class="space-y-3">
                <?php foreach ($notes as $n): ?>
                <div class="flex gap-3 p-3 bg-gray-50 rounded-lg">
                    <div class="w-8 h-8 rounded-lg bg-primary-100 flex items-center justify-center text-xs font-bold text-primary-700 flex-shrink-0"><?= strtoupper(substr($n['first_name'],0,1)) ?></div>
                    <div><p class="text-sm text-dark-700"><?= nl2br(clean($n['note'])) ?></p><p class="text-xs text-gray-400 mt-1"><?= clean($n['first_name'].' '.$n['last_name']) ?> · <?= timeAgo($n['created_at']) ?></p></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="font-bold text-dark-900 mb-4">Update Status</h3>
            <form method="POST">
                <?= CSRF::field() ?>
                <div class="form-group"><label class="form-label">Status</label><select name="status" class="form-control"><?php foreach(['new','contacted','in_progress','qualified','converted','lost','closed'] as $s): ?><option value="<?= $s ?>" <?= $inquiry['status']===$s?'selected':'' ?>><?= ucfirst(str_replace('_',' ',$s)) ?></option><?php endforeach; ?></select></div>
                <div class="form-group"><label class="form-label">Priority</label><select name="priority" class="form-control"><?php foreach(['low','medium','high','urgent'] as $p): ?><option value="<?= $p ?>" <?= $inquiry['priority']===$p?'selected':'' ?>><?= ucfirst($p) ?></option><?php endforeach; ?></select></div>
                <div class="form-group"><label class="form-label">Assign To</label><select name="assigned_to" class="form-control"><option value="">Unassigned</option><?php foreach($staff as $s): ?><option value="<?= $s['id'] ?>" <?= $inquiry['assigned_to']==$s['id']?'selected':'' ?>><?= clean($s['first_name'].' '.$s['last_name']) ?></option><?php endforeach; ?></select></div>
                <button type="submit" name="update_status" class="btn btn-primary w-full"><i data-lucide="save" class="w-4 h-4"></i> Update</button>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
