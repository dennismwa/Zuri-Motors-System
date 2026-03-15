<?php
$pageTitle = 'Create Invoice';
require_once dirname(__DIR__) . '/functions.php';

$prefill = ['customer_name'=>$_GET['customer_name']??'','customer_phone'=>$_GET['customer_phone']??'','car_id'=>$_GET['car_id']??'','inquiry_id'=>$_GET['inquiry_id']??''];
$prefillCar = $prefill['car_id'] ? getCar((int)$prefill['car_id']) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CSRF::verifyOrDie();
    $items = [];
    foreach ($_POST['item_desc'] ?? [] as $i => $desc) {
        if (empty($desc)) continue;
        $qty = (float)($_POST['item_qty'][$i] ?? 1);
        $unitPrice = (float)($_POST['item_price'][$i] ?? 0);
        $items[] = ['description'=>$desc, 'quantity'=>$qty, 'unit_price'=>$unitPrice, 'total_price'=>$qty*$unitPrice];
    }
    $subtotal = array_sum(array_column($items, 'total_price'));
    $tax = (float)($_POST['tax_amount'] ?? 0);
    $discount = (float)($_POST['discount_amount'] ?? 0);
    $total = $subtotal + $tax - $discount;

    $data = array_merge($_POST, ['subtotal'=>$subtotal, 'total_amount'=>$total]);
    $result = saveInvoice($data, $items);
    if ($result['success']) { Flash::success('Invoice created.'); redirect(BASE_URL.'/admin/invoice/'.$result['id']); }
    else Flash::error($result['message']);
}

include __DIR__ . '/header.php';
?>

<a href="<?= BASE_URL ?>/admin/invoices" class="btn btn-ghost btn-sm mb-4"><i data-lucide="arrow-left" class="w-4 h-4"></i> Back</a>

<form method="POST" class="space-y-5">
    <?= CSRF::field() ?>
    <input type="hidden" name="inquiry_id" value="<?= clean($prefill['inquiry_id']) ?>">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-5">
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="font-bold text-dark-900 mb-4">Customer Details</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="form-group"><label class="form-label">Customer Name *</label><input type="text" name="customer_name" required class="form-control" value="<?= clean($prefill['customer_name']) ?>"></div>
                    <div class="form-group"><label class="form-label">Phone</label><input type="text" name="customer_phone" class="form-control" value="<?= clean($prefill['customer_phone']) ?>"></div>
                    <div class="form-group"><label class="form-label">Email</label><input type="email" name="customer_email" class="form-control"></div>
                    <div class="form-group"><label class="form-label">Address</label><input type="text" name="customer_address" class="form-control"></div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="font-bold text-dark-900 mb-4">Line Items</h3>
                <div id="invoice-items">
                    <div class="grid grid-cols-12 gap-2 mb-2 text-xs font-semibold text-gray-500"><div class="col-span-6">Description</div><div class="col-span-2">Qty</div><div class="col-span-3">Unit Price</div><div class="col-span-1"></div></div>
                    <div class="invoice-item grid grid-cols-12 gap-2 mb-2">
                        <input type="text" name="item_desc[]" class="form-control text-sm col-span-6" placeholder="Vehicle purchase" value="<?= $prefillCar ? clean($prefillCar['title']) : '' ?>" required>
                        <input type="number" name="item_qty[]" class="form-control text-sm col-span-2" value="1" min="1" step="1">
                        <input type="number" name="item_price[]" class="form-control text-sm col-span-3" value="<?= $prefillCar ? $prefillCar['price'] : '' ?>" min="0" step="100" required>
                        <button type="button" onclick="this.parentElement.remove()" class="btn btn-icon btn-sm btn-ghost text-red-500 col-span-1"><i data-lucide="x" class="w-4 h-4"></i></button>
                    </div>
                </div>
                <button type="button" onclick="addInvoiceItem()" class="btn btn-ghost btn-sm mt-2"><i data-lucide="plus" class="w-4 h-4"></i> Add Item</button>
            </div>
        </div>

        <div class="space-y-5">
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="font-bold text-dark-900 mb-4">Invoice Settings</h3>
                <div class="form-group"><label class="form-label">Car (optional)</label>
                    <select name="car_id" class="form-control"><option value="">None</option>
                    <?php if ($prefillCar): ?><option value="<?= $prefillCar['id'] ?>" selected><?= clean($prefillCar['title']) ?></option><?php endif; ?>
                    </select>
                </div>
                <div class="form-group"><label class="form-label">Status</label><select name="status" class="form-control"><option value="draft">Draft</option><option value="sent">Sent</option></select></div>
                <div class="form-group"><label class="form-label">Due Date</label><input type="date" name="due_date" class="form-control" value="<?= date('Y-m-d', strtotime('+30 days')) ?>"></div>
                <div class="form-group"><label class="form-label">Tax Amount</label><input type="number" name="tax_amount" class="form-control" value="0" min="0"></div>
                <div class="form-group"><label class="form-label">Discount</label><input type="number" name="discount_amount" class="form-control" value="0" min="0"></div>
                <div class="form-group"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
                <button type="submit" class="btn btn-primary w-full"><i data-lucide="save" class="w-4 h-4"></i> Create Invoice</button>
            </div>
        </div>
    </div>
</form>

<?php $pageScripts = <<<'JS'
<script>
function addInvoiceItem() {
    const container = document.getElementById('invoice-items');
    const row = document.createElement('div');
    row.className = 'invoice-item grid grid-cols-12 gap-2 mb-2';
    row.innerHTML = '<input type="text" name="item_desc[]" class="form-control text-sm col-span-6" placeholder="Description"><input type="number" name="item_qty[]" class="form-control text-sm col-span-2" value="1" min="1"><input type="number" name="item_price[]" class="form-control text-sm col-span-3" value="0" min="0" step="100"><button type="button" onclick="this.parentElement.remove()" class="btn btn-icon btn-sm btn-ghost text-red-500 col-span-1"><i data-lucide="x" class="w-4 h-4"></i></button>';
    container.appendChild(row);
    lucide.createIcons();
}
</script>
JS;
include __DIR__ . '/footer.php'; ?>
