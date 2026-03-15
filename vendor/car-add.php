<?php
$pageTitle = 'Add New Car';
require_once dirname(__DIR__) . '/functions.php';

$brands = getBrands(true); $categories = getCategories(true);
$allFeatures = getFeaturesGrouped(true); $locations = getLocations(true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CSRF::verifyOrDie();
    $data = $_POST;
    $data['user_id'] = Auth::id();
    $data['status'] = 'pending'; // Vendors submit for approval
    $data['slug'] = '';
    $data['is_featured'] = 0; // Only admin can feature
    $data['price_negotiable'] = isset($_POST['price_negotiable']) ? 1 : 0;
    $data['is_offer'] = isset($_POST['is_offer']) ? 1 : 0;

    $result = saveCar($data);
    if ($result['success']) {
        $carId = $result['id'];
        // Save features
        foreach ($_POST['features'] ?? [] as $fid) {
            db()->prepare("INSERT INTO car_features (car_id, feature_id) VALUES (?,?)")->execute([$carId, (int)$fid]);
        }
        // Upload images
        if (!empty($_FILES['images']['name'][0])) {
            $uploads = ImageUpload::uploadMultiple($_FILES['images'], CARS_UPLOAD_PATH, 'car');
            foreach ($uploads as $i => $up) {
                if ($up['success']) {
                    db()->prepare("INSERT INTO car_images (car_id, image_url, is_primary, sort_order) VALUES (?,?,?,?)")
                        ->execute([$carId, $up['url'], $i === 0 ? 1 : 0, $i]);
                }
            }
        }
        Flash::success('Car submitted for approval! It will be live once reviewed.');
        redirect(BASE_URL . '/vendor/cars');
    } else { Flash::error($result['message']); }
}

include __DIR__ . '/header.php';
?>

<a href="<?= BASE_URL ?>/vendor/cars" class="btn btn-ghost btn-sm mb-4"><i data-lucide="arrow-left" class="w-4 h-4"></i> Back</a>

<form method="POST" enctype="multipart/form-data" class="space-y-6">
    <?= CSRF::field() ?>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="font-bold text-dark-900 mb-4">Vehicle Information</h3>
                <div class="form-group"><label class="form-label">Title *</label><input type="text" name="title" required class="form-control" placeholder="e.g. Toyota Land Cruiser V8 2020"></div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="form-group"><label class="form-label">Brand *</label><select name="brand_id" required class="form-control"><option value="">Select</option><?php foreach($brands as $b): ?><option value="<?= $b['id'] ?>"><?= clean($b['name']) ?></option><?php endforeach; ?></select></div>
                    <div class="form-group"><label class="form-label">Model *</label><input type="text" name="model" required class="form-control"></div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div class="form-group"><label class="form-label">Year *</label><select name="year" required class="form-control"><?php for($y=date('Y')+1;$y>=1990;$y--): ?><option value="<?= $y ?>"><?= $y ?></option><?php endfor; ?></select></div>
                    <div class="form-group"><label class="form-label">Category *</label><select name="category_id" required class="form-control"><option value="">Select</option><?php foreach($categories as $c): ?><option value="<?= $c['id'] ?>"><?= clean($c['name']) ?></option><?php endforeach; ?></select></div>
                    <div class="form-group"><label class="form-label">Condition</label><select name="condition_type" class="form-control"><option value="used">Used</option><option value="new">New</option><option value="certified">Certified</option></select></div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div class="form-group"><label class="form-label">Mileage</label><input type="number" name="mileage" class="form-control" value="0"></div>
                    <div class="form-group"><label class="form-label">Fuel *</label><select name="fuel_type" required class="form-control"><option value="petrol">Petrol</option><option value="diesel">Diesel</option><option value="hybrid">Hybrid</option><option value="electric">Electric</option></select></div>
                    <div class="form-group"><label class="form-label">Transmission *</label><select name="transmission" required class="form-control"><option value="automatic">Automatic</option><option value="manual">Manual</option><option value="cvt">CVT</option></select></div>
                </div>
                <div class="grid grid-cols-4 gap-4">
                    <div class="form-group"><label class="form-label">Engine</label><input type="text" name="engine_size" class="form-control" placeholder="2.0L"></div>
                    <div class="form-group"><label class="form-label">Color</label><input type="text" name="color" class="form-control"></div>
                    <div class="form-group"><label class="form-label">Doors</label><input type="number" name="doors" class="form-control" value="4"></div>
                    <div class="form-group"><label class="form-label">Seats</label><input type="number" name="seats" class="form-control" value="5"></div>
                </div>
                <div class="form-group"><label class="form-label">Location *</label><input type="text" name="location" required class="form-control" list="loc-list"><datalist id="loc-list"><?php foreach($locations as $l): ?><option value="<?= clean($l['name']) ?>"><?php endforeach; ?></datalist></div>
                <div class="form-group"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="5" placeholder="Describe the vehicle..."></textarea></div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="font-bold text-dark-900 mb-4">Features</h3>
                <?php foreach ($allFeatures as $cat => $feats): ?>
                <div class="mb-3"><h4 class="text-xs font-bold text-gray-500 uppercase mb-2"><?= ucfirst($cat) ?></h4>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-1"><?php foreach ($feats as $f): ?><label class="form-check text-xs"><input type="checkbox" name="features[]" value="<?= $f['id'] ?>"> <?= clean($f['name']) ?></label><?php endforeach; ?></div></div>
                <?php endforeach; ?>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="font-bold text-dark-900 mb-4">Photos</h3>
                <input type="file" name="images[]" multiple accept="image/*" class="form-control">
                <p class="text-xs text-gray-400 mt-2">First image = primary. Max <?= Settings::get('max_images_per_car', 20) ?> images, 10MB each.</p>
            </div>
        </div>

        <div class="space-y-5">
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="font-bold text-dark-900 mb-4">Pricing</h3>
                <div class="form-group"><label class="form-label">Price (KSh) *</label><input type="number" name="price" required class="form-control" min="0"></div>
                <div class="form-group"><label class="form-label">Old Price</label><input type="number" name="old_price" class="form-control" min="0"></div>
                <label class="form-check mb-4"><input type="checkbox" name="price_negotiable" value="1"> Negotiable</label>
                <div class="p-3 bg-blue-50 rounded-lg text-xs text-blue-700 mb-4">
                    <i data-lucide="info" class="w-3 h-3 inline"></i> Your listing will be reviewed before going live. This usually takes less than 24 hours.
                </div>
                <button type="submit" class="btn btn-primary w-full btn-lg"><i data-lucide="send" class="w-4 h-4"></i> Submit for Approval</button>
            </div>
        </div>
    </div>
</form>

<?php include __DIR__ . '/footer.php'; ?>
