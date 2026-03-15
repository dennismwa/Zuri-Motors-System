<?php
$pageTitle = 'My Inquiries';
require_once dirname(__DIR__) . '/functions.php';

$vendorId = Auth::id();
$filters = ['vendor_id' => $vendorId, 'status' => $_GET['status'] ?? '', 'search' => $_GET['search'] ?? ''];
$total = countInquiries(array_filter($filters));
$pagination = new Pagination($total, 20);
$inquiries = getInquiries(array_filter($filters), $pagination->perPage, $pagination->offset);

include __DIR__ . '/header.php';
?>

<div class="flex items-center justify-between mb-5">
    <form method="GET" class="flex items-end gap-3">
        <input type="text" name="search" value="<?= clean($filters['search']) ?>" class="filter-input text-sm" placeholder="Search...">
        <select name="status" class="filter-select text-sm"><option value="">All</option><?php foreach(['new','contacted','in_progress','qualified','converted','lost'] as $s): ?><option value="<?= $s ?>" <?= $filters['status']===$s?'selected':'' ?>><?= ucfirst(str_replace('_',' ',$s)) ?></option><?php endforeach; ?></select>
        <button type="submit" class="btn btn-primary btn-sm"><i data-lucide="search" class="w-4 h-4"></i></button>
    </form>
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="data-table">
        <thead><tr><th>Contact</th><th>Type</th><th>Car</th><th>Status</th><th>Date</th><th class="text-right">Actions</th></tr></thead>
        <tbody>
        <?php if(empty($inquiries)): ?><tr><td colspan="6" class="text-center py-8 text-gray-400">No inquiries yet</td></tr>
        <?php else: foreach($inquiries as $inq): ?>
        <tr>
            <td><p class="text-sm font-semibold text-dark-900"><?= clean($inq['name']) ?></p><p class="text-xs text-gray-400"><?= clean($inq['phone']) ?> <?= $inq['email'] ? '· '.clean($inq['email']) : '' ?></p></td>
            <td><?= statusBadge($inq['type']) ?></td>
            <td class="text-sm"><?= $inq['car_title'] ? clean(excerpt($inq['car_title'],30)) : '-' ?></td>
            <td><?= statusBadge($inq['status']) ?></td>
            <td class="text-xs text-gray-400"><?= timeAgo($inq['created_at']) ?></td>
            <td><div class="actions justify-end">
                <a href="tel:<?= $inq['phone'] ?>" class="btn btn-icon btn-sm btn-ghost text-blue-500" title="Call"><i data-lucide="phone" class="w-4 h-4"></i></a>
                <a href="<?= whatsappLink('Hi '.$inq['name'].', regarding your inquiry about '.($inq['car_title']??'our vehicles').'...', preg_replace('/[^0-9]/','',$inq['phone'])) ?>" target="_blank" class="btn btn-icon btn-sm btn-ghost text-emerald-500" title="WhatsApp"><i data-lucide="message-circle" class="w-4 h-4"></i></a>
                <?php if($inq['email']): ?><a href="mailto:<?= $inq['email'] ?>" class="btn btn-icon btn-sm btn-ghost" title="Email"><i data-lucide="mail" class="w-4 h-4"></i></a><?php endif; ?>
            </div></td>
        </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
    <div class="p-4"><?= $pagination->render(BASE_URL.'/vendor/inquiries', array_filter(['status'=>$filters['status'],'search'=>$filters['search']])) ?></div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
