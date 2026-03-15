<?php
$pageTitle = 'Add New Car';
require_once dirname(__DIR__) . '/functions.php';
$brands = getBrands(false); $categories = getCategories(false);
$allFeatures = getFeaturesGrouped(false); $locations = getLocations(true);
$car = null; $carFeatureIds = []; $carImages = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CSRF::verifyOrDie();
    $data = [
        'user_id' => $_POST['user_id'] ?: Auth::id(), 'title' => $_POST['title'], 'brand_id' => $_POST['brand_id'],
        'category_id' => $_POST['category_id'], 'model' => $_POST['model'], 'year' => $_POST['year'],
        'mileage' => $_POST['mileage'] ?? 0, 'mileage_unit' => $_POST['mileage_unit'] ?? 'km',
        'fuel_type' => $_POST['fuel_type'], 'transmission' => $_POST['transmission'],
        'engine_size' => $_POST['engine_size'] ?? '', 'horsepower' => $_POST['horsepower'] ?? null,
        'body_type' => $_POST['body_type'] ?? '', 'color' => $_POST['color'] ?? '',
        'interior_color' => $_POST['interior_color'] ?? '', 'doors' => $_POST['doors'] ?? 4,
        'seats' => $_POST['seats'] ?? 5, 'drivetrain' => $_POST['drivetrain'] ?? '',
        'price' => $_POST['price'], 'old_price' => $_POST['old_price'] ?: null,
        'price_negotiable' => isset($_POST['price_negotiable']) ? 1 : 0,
        'condition_type' => $_POST['condition_type'], 'location' => $_POST['location'],
        'description' => $_POST['description'] ?? '', 'excerpt' => $_POST['excerpt'] ?? '',
        'registration_number' => $_POST['registration_number'] ?? '',
        'vin_number' => $_POST['vin_number'] ?? '', 'registration_year' => $_POST['registration_year'] ?: null,
        'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
        'is_offer' => isset($_POST['is_offer']) ? 1 : 0,
        'is_hot_deal' => isset($_POST['is_hot_deal']) ? 1 : 0,
        'offer_label' => $_POST['offer_label'] ?? '',
        'status' => $_POST['status'] ?? 'active',
        'meta_title' => $_POST['meta_title'] ?? '', 'meta_description' => $_POST['meta_description'] ?? '',
        'deposit_amount' => $_POST['deposit_amount'] ?: null,
        'monthly_installment' => $_POST['monthly_installment'] ?: null,
        'installment_months' => $_POST['installment_months'] ?: null,
        'slug' => '',
    ];
    $result = saveCar($data);
    if ($result['success']) {
        $carId = $result['id'];
        // Save features
        db()->prepare("DELETE FROM car_features WHERE car_id = ?")->execute([$carId]);
        foreach ($_POST['features'] ?? [] as $fid) {
            db()->prepare("INSERT INTO car_features (car_id, feature_id) VALUES (?,?)")->execute([$carId, (int)$fid]);
        }
        // Upload images
        if (!empty($_FILES['images']['name'][0])) {
            $uploads = ImageUpload::uploadMultiple($_FILES['images'], CARS_UPLOAD_PATH, 'car');
            foreach ($uploads as $i => $up) {
                if ($up['success']) {
                    $isPrimary = ($i === 0 && empty($carImages)) ? 1 : 0;
                    db()->prepare("INSERT INTO car_images (car_id, image_url, is_primary, sort_order) VALUES (?,?,?,?)")
                        ->execute([$carId, $up['url'], $isPrimary, $i]);
                }
            }
        }
        Flash::success($result['message']);
        redirect(BASE_URL . '/admin/cars');
    } else {
        Flash::error($result['message']);
    }
}

include __DIR__ . '/header.php';
?>

<form method="POST" enctype="multipart/form-data" class="space-y-6">
    <?= CSRF::field() ?>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="font-bold text-dark-900 mb-4">Vehicle Information</h3>
                <div class="form-group">
                    <label class="form-label">Title <span class="required">*</span></label>
                    <input type="text" name="title" required class="form-control" placeholder="e.g. Toyota Land Cruiser V8 2020">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="form-label">Brand <span class="required">*</span></label>
                        <select name="brand_id" required class="form-control">
                            <option value="">Select</option>
                            <?php foreach($brands as $b): ?><option value="<?= $b['id'] ?>"><?= clean($b['name']) ?></option><?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Model <span class="required">*</span></label>
                        <input type="text" name="model" required class="form-control" placeholder="e.g. Land Cruiser V8">
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div class="form-group">
                        <label class="form-label">Year <span class="required">*</span></label>
                        <select name="year" required class="form-control">
                            <?php for($y=date('Y')+1;$y>=1990;$y--): ?><option value="<?= $y ?>"><?= $y ?></option><?php endfor; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Category <span class="required">*</span></label>
                        <select name="category_id" required class="form-control">
                            <option value="">Select</option>
                            <?php foreach($categories as $c): ?><option value="<?= $c['id'] ?>"><?= clean($c['name']) ?></option><?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Condition <span class="required">*</span></label>
                        <select name="condition_type" required class="form-control">
                            <option value="used">Used</option><option value="new">New</option><option value="certified">Certified</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div class="form-group">
                        <label class="form-label">Mileage</label>
                        <input type="number" name="mileage" class="form-control" min="0" value="0">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Fuel Type <span class="required">*</span></label>
                        <select name="fuel_type" required class="form-control">
                            <option value="petrol">Petrol</option><option value="diesel">Diesel</option><option value="hybrid">Hybrid</option><option value="electric">Electric</option><option value="plugin_hybrid">Plug-in Hybrid</option><option value="lpg">LPG</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Transmission <span class="required">*</span></label>
                        <select name="transmission" required class="form-control">
                            <option value="automatic">Automatic</option><option value="manual">Manual</option><option value="cvt">CVT</option><option value="semi_automatic">Semi-Auto</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-4 gap-4">
                    <div class="form-group"><label class="form-label">Engine Size</label><input type="text" name="engine_size" class="form-control" placeholder="e.g. 2.0L"></div>
                    <div class="form-group"><label class="form-label">Horsepower</label><input type="number" name="horsepower" class="form-control"></div>
                    <div class="form-group"><label class="form-label">Doors</label><input type="number" name="doors" class="form-control" value="4" min="1" max="6"></div>
                    <div class="form-group"><label class="form-label">Seats</label><input type="number" name="seats" class="form-control" value="5" min="1" max="12"></div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div class="form-group"><label class="form-label">Color</label><input type="text" name="color" class="form-control" placeholder="e.g. White"></div>
                    <div class="form-group"><label class="form-label">Interior Color</label><input type="text" name="interior_color" class="form-control"></div>
                    <div class="form-group"><label class="form-label">Drivetrain</label>
                        <select name="drivetrain" class="form-control"><option value="">Select</option><option value="fwd">FWD</option><option value="rwd">RWD</option><option value="awd">AWD</option><option value="4wd">4WD</option></select>
                    </div>
                </div>
                <div class="form-group"><label class="form-label">Location <span class="required">*</span></label><input type="text" name="location" required class="form-control" placeholder="e.g. Nairobi" list="locations-list">
                    <datalist id="locations-list"><?php foreach($locations as $l): ?><option value="<?= clean($l['name']) ?>"><?php endforeach; ?></datalist>
                </div>
            </div>

            <!-- Description -->
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="font-bold text-dark-900 mb-4">Description & SEO</h3>
                <div class="form-group"><label class="form-label">Excerpt (Short Description)</label><textarea name="excerpt" class="form-control" rows="2" maxlength="500" placeholder="Brief SEO-friendly description"></textarea></div>
                <div class="form-group"><label class="form-label">Full Description</label><textarea name="description" class="form-control" rows="6" placeholder="Detailed vehicle description..."></textarea></div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="form-group"><label class="form-label">Meta Title</label><input type="text" name="meta_title" class="form-control" placeholder="Auto-generated if empty"></div>
                    <div class="form-group"><label class="form-label">Meta Description</label><input type="text" name="meta_description" class="form-control" placeholder="Auto-generated if empty"></div>
                </div>
            </div>

            <!-- Features -->
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="font-bold text-dark-900 mb-4">Features & Equipment</h3>
                <?php foreach ($allFeatures as $cat => $feats): ?>
                <div class="mb-4">
                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-2"><?= ucfirst($cat) ?></h4>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                        <?php foreach ($feats as $f): ?>
                        <label class="form-check text-xs"><input type="checkbox" name="features[]" value="<?= $f['id'] ?>" <?= in_array($f['id'],$carFeatureIds)?'checked':'' ?>> <?= clean($f['name']) ?></label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Images -->
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="font-bold text-dark-900 mb-4">Photos</h3>
                <input type="file" name="images[]" multiple accept="image/*" class="form-control" id="image-input">
                <p class="text-xs text-gray-400 mt-2">Upload up to <?= Settings::get('max_images_per_car', 20) ?> images. First image will be the primary photo. Max 10MB each.</p>
                <div id="image-preview" class="flex flex-wrap gap-2 mt-3"></div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-5">
            <!-- Publish -->
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="font-bold text-dark-900 mb-4">Publish</h3>
                <div class="form-group"><label class="form-label">Status</label>
                    <select name="status" class="form-control"><option value="active">Active</option><option value="pending">Pending</option><option value="draft">Draft</option></select>
                </div>
                <div class="form-group"><label class="form-label">Seller</label>
                    <select name="user_id" class="form-control">
                        <option value="<?= Auth::id() ?>">Me (<?= clean($currentUser['first_name']) ?>)</option>
                        <?php foreach(getUsers(['role'=>'vendor'],100) as $v): ?>
                        <option value="<?= $v['id'] ?>"><?= clean($v['company_name'] ?: $v['first_name'].' '.$v['last_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-full"><i data-lucide="save" class="w-4 h-4"></i> Save Listing</button>
            </div>

            <!-- Pricing -->
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="font-bold text-dark-900 mb-4">Pricing</h3>
                <div class="form-group"><label class="form-label">Price (KSh) <span class="required">*</span></label><input type="number" name="price" required class="form-control" min="0" step="1000"></div>
                <div class="form-group"><label class="form-label">Old Price (for discount)</label><input type="number" name="old_price" class="form-control" min="0" step="1000"></div>
                <label class="form-check mb-3"><input type="checkbox" name="price_negotiable" value="1"> Price Negotiable</label>
                <div class="form-group"><label class="form-label">Deposit Amount</label><input type="number" name="deposit_amount" class="form-control" min="0"></div>
                <div class="form-group"><label class="form-label">Monthly Installment</label><input type="number" name="monthly_installment" class="form-control" min="0"></div>
                <div class="form-group"><label class="form-label">Installment Months</label><input type="number" name="installment_months" class="form-control" min="0" max="120"></div>
            </div>

            <!-- Badges -->
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="font-bold text-dark-900 mb-4">Badges & Labels</h3>
                <label class="form-check"><input type="checkbox" name="is_featured" value="1"> Featured</label>
                <label class="form-check"><input type="checkbox" name="is_offer" value="1"> Special Offer</label>
                <label class="form-check"><input type="checkbox" name="is_hot_deal" value="1"> Hot Deal</label>
                <div class="form-group mt-3"><label class="form-label">Offer Label</label><input type="text" name="offer_label" class="form-control" placeholder="e.g. Save 20%"></div>
            </div>

            <!-- Registration -->
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="font-bold text-dark-900 mb-4">Registration</h3>
                <div class="form-group"><label class="form-label">Reg. Number</label><input type="text" name="registration_number" class="form-control"></div>
                <div class="form-group"><label class="form-label">VIN</label><input type="text" name="vin_number" class="form-control"></div>
                <div class="form-group"><label class="form-label">Reg. Year</label><input type="number" name="registration_year" class="form-control" min="1990" max="<?= date('Y')+1 ?>"></div>
            </div>
        </div>
    </div>
</form>

<?php
$pageScripts = <<<'JS'
<script>
document.getElementById('image-input').addEventListener('change', function() {
    const preview = document.getElementById('image-preview');
    preview.innerHTML = '';
    Array.from(this.files).forEach((file, i) => {
        const reader = new FileReader();
        reader.onload = (e) => {
            const div = document.createElement('div');
            div.className = 'relative';
            div.innerHTML = `<img src="${e.target.result}" class="w-20 h-16 object-cover rounded-lg border border-gray-200">${i===0?'<span class="absolute -top-1 -left-1 bg-primary-600 text-white text-[9px] px-1.5 py-0.5 rounded font-bold">Primary</span>':''}`;
            preview.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
});
</script>
JS;
include __DIR__ . '/footer.php';
?>
