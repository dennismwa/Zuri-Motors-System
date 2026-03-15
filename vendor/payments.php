<?php
$pageTitle = 'My Payments';
require_once dirname(__DIR__) . '/functions.php';
$filters = ['customer_id' => Auth::id()];
$total = countPayments($filters);
$pagination = new Pagination($total, 20);
$payments = getPayments($filters, $pagination->perPage, $pagination->offset);
include __DIR__ . '/header.php';
?>
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="data-table">
        <thead><tr><th>Date</th><th>Invoice</th><th>Method</th><th>Amount</th><th>Reference</th><th>Status</th></tr></thead>
        <tbody>
        <?php if(empty($payments)): ?><tr><td colspan="6" class="text-center py-8 text-gray-400">No payments recorded</td></tr>
        <?php else: foreach($payments as $p): ?>
        <tr>
            <td class="text-sm"><?= formatDate($p['payment_date'],'d M Y') ?></td>
            <td class="text-sm"><?= $p['invoice_number'] ?? '-' ?></td>
            <td class="text-sm"><?= clean($p['method_name'] ?? '-') ?></td>
            <td class="font-bold text-sm"><?= formatPrice($p['amount']) ?></td>
            <td class="text-xs text-gray-400"><?= clean($p['reference_number'] ?: $p['transaction_id'] ?: '-') ?></td>
            <td><?= statusBadge($p['status']) ?></td>
        </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
    <div class="p-4"><?= $pagination->render(BASE_URL.'/vendor/payments', []) ?></div>
</div>
<?php include __DIR__ . '/footer.php'; ?>
