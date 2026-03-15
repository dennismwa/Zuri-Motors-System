<?php
$pageTitle = 'Record Payment';
require_once dirname(__DIR__) . '/functions.php';

$invoiceId = $_GET['invoice_id'] ?? '';
$prefillInvoice = $invoiceId ? getInvoice((int)$invoiceId) : null;
$paymentMethods = getPaymentMethods(true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CSRF::verifyOrDie();
    $result = savePayment($_POST);
    if ($result['success']) { Flash::success('Payment recorded successfully.'); redirect(BASE_URL.'/admin/payments'); }
    else Flash::error($result['message']);
}
include __DIR__ . '/header.php';
?>

<a href="<?= BASE_URL ?>/admin/payments" class="btn btn-ghost btn-sm mb-4"><i data-lucide="arrow-left" class="w-4 h-4"></i> Back</a>

<div class="max-w-2xl">
<div class="bg-white rounded-xl border border-gray-200 p-6">
    <h3 class="font-bold text-dark-900 mb-4">Record Payment</h3>
    <?php if ($prefillInvoice): ?>
    <div class="p-3 bg-blue-50 rounded-lg mb-4 text-sm"><strong>Invoice:</strong> <?= $prefillInvoice['invoice_number'] ?> · <strong>Customer:</strong> <?= clean($prefillInvoice['customer_name']) ?> · <strong>Balance:</strong> <?= formatPrice($prefillInvoice['balance_due']) ?></div>
    <?php endif; ?>
    <form method="POST">
        <?= CSRF::field() ?>
        <input type="hidden" name="invoice_id" value="<?= $invoiceId ?>">
        <div class="grid grid-cols-2 gap-4">
            <div class="form-group"><label class="form-label">Amount (KSh) *</label><input type="number" name="amount" required class="form-control" min="1" step="100" value="<?= $prefillInvoice ? $prefillInvoice['balance_due'] : '' ?>"></div>
            <div class="form-group"><label class="form-label">Payment Date *</label><input type="date" name="payment_date" required class="form-control" value="<?= date('Y-m-d') ?>"></div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div class="form-group"><label class="form-label">Payment Method</label><select name="payment_method_id" class="form-control"><option value="">Select</option><?php foreach($paymentMethods as $m): ?><option value="<?= $m['id'] ?>"><?= clean($m['name']) ?></option><?php endforeach; ?></select></div>
            <div class="form-group"><label class="form-label">Status</label><select name="status" class="form-control"><option value="completed">Completed</option><option value="pending">Pending</option></select></div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div class="form-group"><label class="form-label">Transaction ID</label><input type="text" name="transaction_id" class="form-control" placeholder="e.g. MPESA code"></div>
            <div class="form-group"><label class="form-label">Reference Number</label><input type="text" name="reference_number" class="form-control"></div>
        </div>
        <div class="form-group"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
        <button type="submit" class="btn btn-primary"><i data-lucide="save" class="w-4 h-4"></i> Record Payment</button>
    </form>
</div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
