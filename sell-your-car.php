<?php
require_once __DIR__ . '/functions.php';
$companyName = Settings::get('company_name', 'Zuri Motors');
$whatsapp = Settings::get('whatsapp', '254700000000');
$brands = getBrands(true, true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CSRF::verifyOrDie();
    $data = [
        'type' => 'sell_car', 'name' => $_POST['name'] ?? '', 'email' => $_POST['email'] ?? '',
        'phone' => $_POST['phone'] ?? '', 'message' => $_POST['message'] ?? '',
        'car_model_sell' => ($_POST['brand'] ?? '') . ' ' . ($_POST['model'] ?? ''),
        'price_expectation' => $_POST['price_expectation'] ?? null,
        'year_sell' => $_POST['year'] ?? null, 'source' => 'sell_page'
    ];
    $result = saveInquiry($data);
    if ($result['success']) {
        // Build WhatsApp message
        $waMsg = "New Car Sell Request:\n";
        $waMsg .= "Name: {$data['name']}\nPhone: {$data['phone']}\n";
        $waMsg .= "Vehicle: {$data['car_model_sell']} ({$data['year_sell']})\n";
        $waMsg .= "Price: " . formatPrice($data['price_expectation']) . "\n";
        if ($data['message']) $waMsg .= "Notes: {$data['message']}";
        Flash::success('Request submitted! We\'ll contact you soon.');
        // Redirect to WhatsApp
        if (!empty($_POST['send_whatsapp'])) {
            header('Location: ' . whatsappLink($waMsg, $whatsapp));
            exit;
        }
    } else { Flash::error('Failed to submit. Please try again.'); }
    redirect(BASE_URL . '/sell-your-car');
}

$pageSeo = seoMeta('Sell Your Car - ' . $companyName, 'Get the best value for your vehicle with ' . $companyName);
include __DIR__ . '/header.php';
?>

<div class="bg-gray-50 border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 lg:px-6 py-4">
        <?= breadcrumbs([['label' => 'Home', 'url' => BASE_URL . '/'], ['label' => 'Sell Your Car']]) ?>
    </div>
</div>

<!-- Hero -->
<section class="bg-gradient-to-br from-primary-600 to-primary-800 py-16 relative overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 right-0 w-[400px] h-[400px] bg-white rounded-full -translate-y-1/2 translate-x-1/3 blur-3xl"></div>
    </div>
    <div class="max-w-7xl mx-auto px-4 lg:px-6 relative">
        <div class="max-w-2xl">
            <h1 class="text-3xl lg:text-4xl font-bold text-white font-display mb-4">Sell Your Car Fast</h1>
            <p class="text-lg text-white/80">Get the best value for your vehicle. Fill in the form below and our team will contact you with an offer.</p>
        </div>
    </div>
</section>

<section class="py-12 bg-gray-50">
    <div class="max-w-3xl mx-auto px-4 lg:px-6">
        <div class="bg-white rounded-2xl border border-gray-200 p-6 lg:p-8">
            <h2 class="text-xl font-bold text-dark-900 mb-2">Vehicle Details</h2>
            <p class="text-sm text-gray-500 mb-6">Provide your car details and we'll get back to you with a valuation.</p>

            <form method="POST">
                <?= CSRF::field() ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div class="form-group mb-0">
                        <label class="form-label">Your Name <span class="required">*</span></label>
                        <input type="text" name="name" required class="form-control" placeholder="Full name">
                    </div>
                    <div class="form-group mb-0">
                        <label class="form-label">Phone <span class="required">*</span></label>
                        <input type="tel" name="phone" required class="form-control" placeholder="+254 700 000 000">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="your@email.com">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div class="form-group mb-0">
                        <label class="form-label">Brand <span class="required">*</span></label>
                        <select name="brand" required class="form-control">
                            <option value="">Select Brand</option>
                            <?php foreach ($brands as $b): ?>
                            <option value="<?= clean($b['name']) ?>"><?= clean($b['name']) ?></option>
                            <?php endforeach; ?>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="form-group mb-0">
                        <label class="form-label">Model <span class="required">*</span></label>
                        <input type="text" name="model" required class="form-control" placeholder="e.g. Land Cruiser V8">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div class="form-group mb-0">
                        <label class="form-label">Year <span class="required">*</span></label>
                        <select name="year" required class="form-control">
                            <option value="">Select Year</option>
                            <?php for ($y = date('Y') + 1; $y >= 1990; $y--): ?>
                            <option value="<?= $y ?>"><?= $y ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="form-group mb-0">
                        <label class="form-label">Expected Price (KSh) <span class="required">*</span></label>
                        <input type="number" name="price_expectation" required class="form-control" placeholder="e.g. 2500000" min="0" step="10000">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Additional Details</label>
                    <textarea name="message" class="form-control" rows="4" placeholder="Mileage, condition, any modifications, reason for selling..."></textarea>
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <button type="submit" class="btn btn-primary btn-lg flex-1">
                        <i data-lucide="send" class="w-4 h-4"></i> Submit Request
                    </button>
                    <button type="submit" name="send_whatsapp" value="1" class="btn btn-lg bg-[#25D366] hover:bg-[#20BD5A] text-white flex-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
                        Submit & Chat on WhatsApp
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<?php include __DIR__ . '/footer.php'; ?>
