<?php
$pageTitle = 'Inquiries / Leads';
require_once dirname(__DIR__) . '/functions.php';

$filters = ['status'=>$_GET['status']??'', 'type'=>$_GET['type']??'', 'assigned_to'=>$_GET['assigned_to']??'', 'search'=>$_GET['search']??'', 'priority'=>$_GET['priority']??''];
$total = countInquiries(array_filter($filters));
$pagination = new Pagination($total, 20);
$inquiries = getInquiries(array_filter($filters), $pagination->perPage, $pagination->offset);
$staff = getStaffUsers();
include __DIR__ . '/header.php';
?>

<div class="bg-white rounded-xl border border-gray-200 p-4 mb-5">
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[160px]"><label class="text-xs font-semibold text-gray-500 mb-1 block">Search</label><input type="text" name="search" value="<?= clean($filters['search']) ?>" class="filter-input text-sm" placeholder="Name, email, phone..."></div>
        <div><label class="text-xs font-semibold text-gray-500 mb-1 block">Status</label><select name="status" class="filter-select text-sm"><option value="">All</option><?php foreach(['new','contacted','in_progress','qualified','converted','lost','closed'] as $s): ?><option value="<?= $s ?>" <?= $filters['status']===$s?'selected':'' ?>><?= ucfirst(str_replace('_',' ',$s)) ?></option><?php endforeach; ?></select></div>
        <div><label class="text-xs font-semibold text-gray-500 mb-1 block">Type</label><select name="type" class="filter-select text-sm"><option value="">All</option><?php foreach(['general','test_drive','price_quote','sell_car','financing','callback'] as $t): ?><option value="<?= $t ?>" <?= $filters['type']===$t?'selected':'' ?>><?= ucfirst(str_replace('_',' ',$t)) ?></option><?php endforeach; ?></select></div>
        <div><label class="text-xs font-semibold text-gray-500 mb-1 block">Priority</label><select name="priority" class="filter-select text-sm"><option value="">All</option><?php foreach(['urgent','high','medium','low'] as $p): ?><option value="<?= $p ?>" <?= $filters['priority']===$p?'selected':'' ?>><?= ucfirst($p) ?></option><?php endforeach; ?></select></div>
        <div><label class="text-xs font-semibold text-gray-500 mb-1 block">Assigned</label><select name="assigned_to" class="filter-select text-sm"><option value="">All</option><?php foreach($staff as $s): ?><option value="<?= $s['id'] ?>" <?= $filters['assigned_to']==$s['id']?'selected':'' ?>><?= clean($s['first_name'].' '.$s['last_name']) ?></option><?php endforeach; ?></select></div>
        <button type="submit" class="btn btn-primary btn-sm"><i data-lucide="search" class="w-4 h-4"></i></button>
        <a href="<?= BASE_URL ?>/admin/inquiries" class="btn btn-ghost btn-sm">Reset</a>
    </form>
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead><tr><th>Contact</th><th>Type</th><th>Car</th><th>Priority</th><th>Assigned</th><th>Status</th><th>Date</th><th class="text-right">Actions</th></tr></thead>
            <tbody>
            <?php if(empty($inquiries)): ?><tr><td colspan="8" class="text-center py-8 text-gray-400">No inquiries found</td></tr>
            <?php else: foreach($inquiries as $inq): ?>
            <tr>
                <td><p class="text-sm font-semibold text-dark-900"><?= clean($inq['name']) ?></p><p class="text-xs text-gray-400"><?= clean($inq['phone']) ?></p></td>
                <td><?= statusBadge($inq['type']) ?></td>
                <td class="text-sm"><?= $inq['car_title'] ? '<a href="'.BASE_URL.'/car/'.$inq['car_slug'].'" class="text-primary-600 hover:underline" target="_blank">'.clean(excerpt($inq['car_title'],30)).'</a>' : '-' ?></td>
                <td><?= statusBadge($inq['priority']) ?></td>
                <td class="text-xs"><?= $inq['assigned_first'] ? clean($inq['assigned_first'].' '.$inq['assigned_last']) : '<span class="text-gray-300">Unassigned</span>' ?></td>
                <td><?= statusBadge($inq['status']) ?></td>
                <td class="text-xs text-gray-400"><?= timeAgo($inq['created_at']) ?></td>
                <td><div class="actions justify-end">
                    <a href="<?= BASE_URL ?>/admin/inquiry/<?= $inq['id'] ?>" class="btn btn-icon btn-sm btn-ghost" title="View"><i data-lucide="eye" class="w-4 h-4"></i></a>
                    <a href="<?= whatsappLink('Hi '.$inq['name'].', regarding your inquiry at '.Settings::get('company_name').'...', preg_replace('/[^0-9]/','',$inq['phone'])) ?>" target="_blank" class="btn btn-icon btn-sm btn-ghost text-emerald-500" title="WhatsApp"><i data-lucide="message-circle" class="w-4 h-4"></i></a>
                </div></td>
            </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
    <div class="p-4"><?= $pagination->render(BASE_URL . '/admin/inquiries', array_filter($filters)) ?></div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
