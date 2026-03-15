<?php
$pageTitle = 'My Invoices';
require_once dirname(__DIR__) . '/functions.php';
$filters = ['vendor_id' => Auth::id(), 'status' => $_GET['status'] ?? ''];
$total = countInvoices(array_filter($filters));
$pagination = new Pagination($total, 20);
$invoices = getInvoices(array_filter($filters), $pagination->perPage, $pagination->offset);
include __DIR__ . '/header.php';
?>
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="data-table">
        <thead><tr><th>Invoice #</th><th>Customer</th><th>Car</th><th>Total</th><th>Paid</th><th>Balance</th><th>Status</th><th>Date</th></tr></thead>
        <tbody>
        <?php if(empty($invoices)): ?><tr><td colspan="8" class="text-center py-8 text-gray-400">No invoices yet</td></tr>
        <?php else: foreach($invoices as $inv): ?>
        <tr>
            <td class="font-semibold text-sm"><?= $inv['invoice_number'] ?></td>
            <td class="text-sm"><?= clean($inv['customer_name'] ?: '-') ?></td>
            <td class="text-sm text-gray-500"><?= $inv['car_title'] ? clean(excerpt($inv['car_title'],25)) : '-' ?></td>
            <td class="font-semibold text-sm"><?= formatPrice($inv['total_amount']) ?></td>
            <td class="text-sm text-emerald-600"><?= formatPrice($inv['paid_amount']) ?></td>
            <td class="text-sm <?= $inv['balance_due']>0?'text-red-600':'text-gray-400' ?>"><?= formatPrice($inv['balance_due']) ?></td>
            <td><?= statusBadge($inv['status']) ?></td>
            <td class="text-xs text-gray-400"><?= formatDate($inv['created_at'],'d M Y') ?></td>
        </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
    <div class="p-4"><?= $pagination->render(BASE_URL.'/vendor/invoices', array_filter(['status'=>$filters['status']])) ?></div>
</div>
<?php include __DIR__ . '/footer.php'; ?>
