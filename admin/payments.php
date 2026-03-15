<?php
$pageTitle = 'Payments';
require_once dirname(__DIR__) . '/functions.php';

$filters = ['status'=>$_GET['status']??'', 'search'=>$_GET['search']??''];
$total = countPayments(array_filter($filters));
$pagination = new Pagination($total, 20);
$payments = getPayments(array_filter($filters), $pagination->perPage, $pagination->offset);
include __DIR__ . '/header.php';
?>

<div class="flex items-center justify-between mb-5">
    <form method="GET" class="flex items-end gap-3">
        <input type="text" name="search" value="<?= clean($filters['search']) ?>" class="filter-input text-sm" placeholder="Transaction ID, reference...">
        <select name="status" class="filter-select text-sm"><option value="">All</option><?php foreach(['pending','completed','failed','refunded'] as $s): ?><option value="<?= $s ?>" <?= $filters['status']===$s?'selected':'' ?>><?= ucfirst($s) ?></option><?php endforeach; ?></select>
        <button type="submit" class="btn btn-primary btn-sm"><i data-lucide="search" class="w-4 h-4"></i></button>
    </form>
    <a href="<?= BASE_URL ?>/admin/payment-add" class="btn btn-primary btn-sm"><i data-lucide="plus" class="w-4 h-4"></i> Record Payment</a>
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="data-table">
        <thead><tr><th>Date</th><th>Invoice</th><th>Customer</th><th>Method</th><th>Amount</th><th>Reference</th><th>Status</th><th>Recorded By</th></tr></thead>
        <tbody>
        <?php if(empty($payments)): ?><tr><td colspan="8" class="text-center py-8 text-gray-400">No payments yet</td></tr>
        <?php else: foreach($payments as $p): ?>
        <tr>
            <td class="text-sm"><?= formatDate($p['payment_date'], 'd M Y') ?></td>
            <td class="text-sm"><?= $p['invoice_number'] ? '<a href="'.BASE_URL.'/admin/invoice/'.($p['invoice_id'] ?? '').'" class="text-primary-600 hover:underline">'.$p['invoice_number'].'</a>' : '-' ?></td>
            <td class="text-sm"><?= clean($p['customer_name'] ?? '-') ?></td>
            <td class="text-sm"><span class="inline-flex items-center gap-1"><i data-lucide="<?= $p['method_icon'] ?? 'credit-card' ?>" class="w-3 h-3"></i> <?= clean($p['method_name'] ?? '-') ?></span></td>
            <td class="font-bold text-sm text-dark-900"><?= formatPrice($p['amount']) ?></td>
            <td class="text-xs text-gray-400"><?= clean($p['reference_number'] ?: $p['transaction_id'] ?: '-') ?></td>
            <td><?= statusBadge($p['status']) ?></td>
            <td class="text-xs text-gray-400"><?= clean(($p['recorder_first']??'').' '.($p['recorder_last']??'')) ?></td>
        </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
    <div class="p-4"><?= $pagination->render(BASE_URL.'/admin/payments', array_filter($filters)) ?></div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
