<?php
$pageTitle = 'Invoices';
require_once dirname(__DIR__) . '/functions.php';

if (isset($_GET['delete'])) { deleteInvoice((int)$_GET['delete']); Flash::success('Invoice deleted.'); redirect(BASE_URL.'/admin/invoices'); }

$filters = ['status'=>$_GET['status']??'', 'search'=>$_GET['search']??''];
$total = countInvoices(array_filter($filters));
$pagination = new Pagination($total, 20);
$invoices = getInvoices(array_filter($filters), $pagination->perPage, $pagination->offset);
include __DIR__ . '/header.php';
?>

<div class="flex items-center justify-between mb-5">
    <form method="GET" class="flex items-end gap-3">
        <input type="text" name="search" value="<?= clean($filters['search']) ?>" class="filter-input text-sm" placeholder="Invoice #, customer...">
        <select name="status" class="filter-select text-sm"><option value="">All</option><?php foreach(['draft','sent','partially_paid','paid','overdue','cancelled'] as $s): ?><option value="<?= $s ?>" <?= $filters['status']===$s?'selected':'' ?>><?= ucfirst(str_replace('_',' ',$s)) ?></option><?php endforeach; ?></select>
        <button type="submit" class="btn btn-primary btn-sm"><i data-lucide="search" class="w-4 h-4"></i></button>
    </form>
    <a href="<?= BASE_URL ?>/admin/invoice-add" class="btn btn-primary btn-sm"><i data-lucide="file-plus" class="w-4 h-4"></i> Create Invoice</a>
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="data-table">
        <thead><tr><th>Invoice #</th><th>Customer</th><th>Car</th><th>Total</th><th>Paid</th><th>Balance</th><th>Status</th><th>Date</th><th class="text-right">Actions</th></tr></thead>
        <tbody>
        <?php if(empty($invoices)): ?><tr><td colspan="9" class="text-center py-8 text-gray-400">No invoices yet</td></tr>
        <?php else: foreach($invoices as $inv): ?>
        <tr>
            <td class="font-semibold text-dark-900 text-sm"><?= $inv['invoice_number'] ?></td>
            <td class="text-sm"><?= clean($inv['customer_name'] ?: '-') ?></td>
            <td class="text-sm text-gray-500"><?= $inv['car_title'] ? clean(excerpt($inv['car_title'],25)) : '-' ?></td>
            <td class="font-semibold text-sm"><?= formatPrice($inv['total_amount']) ?></td>
            <td class="text-sm text-emerald-600"><?= formatPrice($inv['paid_amount']) ?></td>
            <td class="text-sm <?= $inv['balance_due']>0?'text-red-600':'text-gray-400' ?>"><?= formatPrice($inv['balance_due']) ?></td>
            <td><?= statusBadge($inv['status']) ?></td>
            <td class="text-xs text-gray-400"><?= formatDate($inv['created_at'], 'd M Y') ?></td>
            <td><div class="actions justify-end">
                <a href="<?= BASE_URL ?>/admin/invoice/<?= $inv['id'] ?>" class="btn btn-icon btn-sm btn-ghost" title="View"><i data-lucide="eye" class="w-4 h-4"></i></a>
                <a href="<?= BASE_URL ?>/admin/payment-add?invoice_id=<?= $inv['id'] ?>" class="btn btn-icon btn-sm btn-ghost text-emerald-500" title="Add Payment"><i data-lucide="credit-card" class="w-4 h-4"></i></a>
                <button onclick="confirmAction('Delete?','?delete=<?= $inv['id'] ?>')" class="btn btn-icon btn-sm btn-ghost text-red-500"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
            </div></td>
        </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
    <div class="p-4"><?= $pagination->render(BASE_URL.'/admin/invoices', array_filter($filters)) ?></div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
