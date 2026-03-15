<?php
$pageTitle = 'Invoice Detail';
require_once dirname(__DIR__) . '/functions.php';

$id = (int)($_GET['id'] ?? 0);
$invoice = getInvoice($id);
if (!$invoice) { Flash::error('Invoice not found.'); redirect(BASE_URL.'/admin/invoices'); }
$items = getInvoiceItems($id);
$payments = getPayments(['invoice_id'=>$id], 50);
$companyName = Settings::get('company_name','Zuri Motors');

// Handle status update
if (isset($_GET['status'])) {
    db()->prepare("UPDATE invoices SET status=? WHERE id=?")->execute([$_GET['status'], $id]);
    Flash::success('Status updated.'); redirect(BASE_URL.'/admin/invoice/'.$id);
}

include __DIR__ . '/header.php';
?>

<div class="flex items-center justify-between mb-5">
    <a href="<?= BASE_URL ?>/admin/invoices" class="btn btn-ghost btn-sm"><i data-lucide="arrow-left" class="w-4 h-4"></i> Back</a>
    <div class="flex gap-2">
        <a href="<?= BASE_URL ?>/admin/payment-add?invoice_id=<?= $id ?>" class="btn btn-primary btn-sm"><i data-lucide="credit-card" class="w-4 h-4"></i> Add Payment</a>
        <button onclick="window.print()" class="btn btn-outline btn-sm"><i data-lucide="printer" class="w-4 h-4"></i> Print</button>
        <select onchange="if(this.value) location='?id=<?= $id ?>&status='+this.value" class="filter-select text-sm py-1.5 w-auto">
            <option value="">Change Status</option>
            <?php foreach(['draft','sent','paid','cancelled'] as $s): ?><option value="<?= $s ?>"><?= ucfirst($s) ?></option><?php endforeach; ?>
        </select>
    </div>
</div>

<div class="invoice-wrapper bg-white rounded-xl border border-gray-200" id="invoice-print">
    <div class="invoice-header">
        <div>
            <h1 class="text-2xl font-bold text-dark-900"><?= clean($companyName) ?></h1>
            <p class="text-sm text-gray-500"><?= clean(Settings::get('address')) ?></p>
            <p class="text-sm text-gray-500"><?= clean(Settings::get('phone')) ?> · <?= clean(Settings::get('email')) ?></p>
        </div>
        <div class="text-right">
            <h2 class="text-xl font-bold text-dark-900">INVOICE</h2>
            <p class="text-lg font-bold text-primary-600"><?= $invoice['invoice_number'] ?></p>
            <p class="text-sm text-gray-500">Date: <?= formatDate($invoice['created_at'], 'd M Y') ?></p>
            <?php if ($invoice['due_date']): ?><p class="text-sm text-gray-500">Due: <?= formatDate($invoice['due_date'], 'd M Y') ?></p><?php endif; ?>
            <div class="mt-2"><?= statusBadge($invoice['status']) ?></div>
        </div>
    </div>

    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
        <h4 class="text-xs font-bold text-gray-500 uppercase mb-2">Bill To:</h4>
        <p class="font-semibold text-dark-900"><?= clean($invoice['customer_name']) ?></p>
        <?php if ($invoice['customer_email']): ?><p class="text-sm text-gray-600"><?= clean($invoice['customer_email']) ?></p><?php endif; ?>
        <?php if ($invoice['customer_phone']): ?><p class="text-sm text-gray-600"><?= clean($invoice['customer_phone']) ?></p><?php endif; ?>
        <?php if ($invoice['customer_address']): ?><p class="text-sm text-gray-600"><?= clean($invoice['customer_address']) ?></p><?php endif; ?>
    </div>

    <?php if ($invoice['car_title']): ?>
    <p class="text-sm text-gray-500 mb-4">Vehicle: <strong class="text-dark-900"><?= clean($invoice['car_title']) ?></strong></p>
    <?php endif; ?>

    <table class="invoice-table">
        <thead><tr><th>#</th><th>Description</th><th class="text-right">Qty</th><th class="text-right">Unit Price</th><th class="text-right">Total</th></tr></thead>
        <tbody>
        <?php foreach ($items as $i => $item): ?>
        <tr>
            <td><?= $i + 1 ?></td>
            <td><?= clean($item['description']) ?></td>
            <td class="text-right"><?= $item['quantity'] ?></td>
            <td class="text-right"><?= formatPrice($item['unit_price']) ?></td>
            <td class="text-right font-semibold"><?= formatPrice($item['total_price']) ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <table class="invoice-totals mt-4">
        <tr><td class="text-gray-500">Subtotal</td><td class="text-right font-semibold"><?= formatPrice($invoice['subtotal']) ?></td></tr>
        <?php if ($invoice['tax_amount'] > 0): ?><tr><td class="text-gray-500">Tax</td><td class="text-right"><?= formatPrice($invoice['tax_amount']) ?></td></tr><?php endif; ?>
        <?php if ($invoice['discount_amount'] > 0): ?><tr><td class="text-gray-500">Discount</td><td class="text-right text-red-600">-<?= formatPrice($invoice['discount_amount']) ?></td></tr><?php endif; ?>
        <tr class="total"><td>Total</td><td class="text-right"><?= formatPrice($invoice['total_amount']) ?></td></tr>
        <tr><td class="text-emerald-600">Paid</td><td class="text-right text-emerald-600"><?= formatPrice($invoice['paid_amount']) ?></td></tr>
        <tr><td class="text-red-600 font-bold">Balance Due</td><td class="text-right text-red-600 font-bold"><?= formatPrice($invoice['balance_due']) ?></td></tr>
    </table>

    <?php if ($invoice['notes']): ?>
    <div class="mt-6 p-4 bg-gray-50 rounded-lg"><h4 class="text-xs font-bold text-gray-500 uppercase mb-1">Notes:</h4><p class="text-sm text-gray-600"><?= nl2br(clean($invoice['notes'])) ?></p></div>
    <?php endif; ?>
</div>

<!-- Payment History -->
<?php if (!empty($payments)): ?>
<div class="bg-white rounded-xl border border-gray-200 p-5 mt-5">
    <h3 class="font-bold text-dark-900 mb-4">Payment History</h3>
    <table class="data-table">
        <thead><tr><th>Date</th><th>Method</th><th>Reference</th><th>Amount</th><th>Status</th><th>Recorded By</th></tr></thead>
        <tbody>
        <?php foreach($payments as $p): ?>
        <tr>
            <td class="text-sm"><?= formatDate($p['payment_date'], 'd M Y') ?></td>
            <td class="text-sm"><?= clean($p['method_name'] ?? '-') ?></td>
            <td class="text-xs text-gray-500"><?= clean($p['reference_number'] ?: $p['transaction_id'] ?: '-') ?></td>
            <td class="font-semibold text-sm"><?= formatPrice($p['amount']) ?></td>
            <td><?= statusBadge($p['status']) ?></td>
            <td class="text-xs text-gray-400"><?= clean(($p['recorder_first'] ?? '') . ' ' . ($p['recorder_last'] ?? '')) ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php include __DIR__ . '/footer.php'; ?>
