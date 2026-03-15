<?php
$pageTitle = 'Edit Car';
require_once dirname(__DIR__) . '/functions.php';

$id = (int)($_GET['id'] ?? 0);
$car = getCar($id);
if (!$car || (int)$car['user_id'] !== Auth::id()) { Flash::error('Car not found or access denied.'); redirect(BASE_URL . '/vendor/cars'); }

$brands = getBrands(true); $categories = getCategories(true);
$allFeatures = getFeaturesGrouped(true); $locations = getLocations(true);
$carImages = getCarImages($id);
$carFeatureIds = array_column(getCarFeatures($id), 'feature_id');

if (isset($_GET['delete_image'])) {
    $imgId = (int)$_GET['delete_image'];
    $stmt = db()->prepare("SELECT image_url FROM car_images WHERE id=? AND car_id=?"); $stmt->execute([$imgId, $id]);
    $img = $stmt->fetch();
    if ($img) { ImageUpload::delete($img['image_url']); db()->prepare("DELETE FROM car_images WHERE id=?")->execute([$imgId]); }
    redirect(BASE_URL . '/vendor/car-edit/' . $id);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CSRF::verifyOrDie();
    $data = $_POST;
    $data['user_id'] = Auth::id();
    $data['price_negotiable'] = isset($_POST['price_negotiable']) ? 1 : 0;
    // Keep current status unless it was draft
    if ($car['status'] === 'draft') $data['status'] = 'pending';
    else $data['status'] = $car['status'];

    $result = saveCar($data, $id);
    if ($result['success']) {
        db()->prepare("DELETE FROM car_features WHERE car_id = ?")->execute([$id]);
        foreach ($_POST['features'] ?? [] as $fid) {
            db()->prepare("INSERT INTO car_features (car_id, feature_id) VALUES (?,?)")->execute([$id, (int)$fid]);
        }
        if (!empty($_FILES['images']['name'][0])) {
            $uploads = ImageUpload::uploadMultiple($_FILES['images'], CARS_UPLOAD_PATH, 'car');
            foreach ($uploads as $i => $up) {
                if ($up['success']) db()->prepare("INSERT INTO car_images (car_id, image_url, sort_order) VALUES (?,?,?)")->execute([$id, $up['url'], count($carImages)+$i]);
            }
        }
        Flash::success('Car updated.');
        redirect(BASE_URL . '/vendor/car-edit/' . $id);
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
                <div class="form-group"><label class="form-label">Title *</label><input type="text" name="title" required class="form-control" value="<?= clean($car['title']) ?>"></div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="form-group"><label class="form-label">Brand</label><select name="brand_id" required class="form-control"><?php foreach($brands as $b): ?><option value="<?= $b['id'] ?>" <?= $car['brand_id']==$b['id']?'selected':'' ?>><?= clean($b['name']) ?></option><?php endforeach; ?></select></div>
                    <div class="form-group"><label class="form-label">Model</label><input type="text" name="model" required class="form-control" value="<?= clean($car['model']) ?>"></div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div class="form-group"><label class="form-label">Year</label><select name="year" class="form-control"><?php for($y=date('Y')+1;$y>=1990;$y--): ?><option value="<?= $y ?>" <?= $car['year']==$y?'selected':'' ?>><?= $y ?></option><?php endfor; ?></select></div>
                    <div class="form-group"><label class="form-label">Category</label><select name="category_id" required class="form-control"><?php foreach($categories as $c): ?><option value="<?= $c['id'] ?>" <?= $car['category_id']==$c['id']?'selected':'' ?>><?= clean($c['name']) ?></option><?php endforeach; ?></select></div>
                    <div class="form-group"><label class="form-label">Condition</label><select name="condition_type" class="form-control"><?php foreach(['used','new','certified'] as $ct): ?><option value="<?= $ct ?>" <?= $car['condition_type']===$ct?'selected':'' ?>><?= ucfirst($ct) ?></option><?php endforeach; ?></select></div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div class="form-group"><label class="form-label">Mileage</label><input type="number" name="mileage" class="form-control" value="<?= $car['mileage'] ?>"></div>
                    <div class="form-group"><label class="form-label">Fuel</label><select name="fuel_type" class="form-control"><?php foreach(['petrol','diesel','hybrid','electric'] as $ft): ?><option value="<?= $ft ?>" <?= $car['fuel_type']===$ft?'selected':'' ?>><?= ucfirst($ft) ?></option><?php endforeach; ?></select></div>
                    <div class="form-group"><label class="form-label">Transmission</label><select name="transmission" class="form-control"><?php foreach(['automatic','manual','cvt'] as $t): ?><option value="<?= $t ?>" <?= $car['transmission']===$t?'selected':'' ?>><?= ucfirst($t) ?></option><?php endforeach; ?></select></div>
                </div>
                <div class="grid grid-cols-4 gap-4">
                    <div class="form-group"><label class="form-label">Engine</label><input type="text" name="engine_size" class="form-control" value="<?= clean($car['engine_size']) ?>"></div>
                    <div class="form-group"><label class="form-label">Color</label><input type="text" name="color" class="form-control" value="<?= clean($car['color']) ?>"></div>
                    <div class="form-group"><label class="form-label">Doors</label><input type="number" name="doors" class="form-control" value="<?= $car['doors'] ?>"></div>
                    <div class="form-group"><label class="form-label">Seats</label><input type="number" name="seats" class="form-control" value="<?= $car['seats'] ?>"></div>
                </div>
                <div class="form-group"><label class="form-label">Location</label><input type="text" name="location" required class="form-control" value="<?= clean($car['location']) ?>"></div>
                <div class="form-group"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="5"><?= clean($car['description']) ?></textarea></div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="font-bold text-dark-900 mb-4">Features</h3>
                <?php foreach ($allFeatures as $cat => $feats): ?>
                <div class="mb-3"><h4 class="text-xs font-bold text-gray-500 uppercase mb-2"><?= ucfirst($cat) ?></h4>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-1"><?php foreach ($feats as $f): ?><label class="form-check text-xs"><input type="checkbox" name="features[]" value="<?= $f['id'] ?>" <?= in_array($f['id'],$carFeatureIds)?'checked':'' ?>> <?= clean($f['name']) ?></label><?php endforeach; ?></div></div>
                <?php endforeach; ?>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="font-bold text-dark-900 mb-4">Photos</h3>
                <div class="flex flex-wrap gap-3 mb-4">
                    <?php foreach ($carImages as $img): ?>
                    <div class="relative group">
                        <img src="<?= BASE_URL.'/'.ltrim($img['image_url'],'/') ?>" class="w-24 h-18 object-cover rounded-lg border <?= $img['is_primary']?'border-primary-500 ring-2 ring-primary-200':'border-gray-200' ?>">
                        <a href="?id=<?= $id ?>&delete_image=<?= $img['id'] ?>" class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition" onclick="return confirm('Delete?')"><i data-lucide="x" class="w-3 h-3"></i></a>
                        <?php if ($img['is_primary']): ?><span class="absolute -top-1 -left-1 bg-primary-600 text-white text-[8px] px-1 rounded font-bold">Primary</span><?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <input type="file" name="images[]" multiple accept="image/*" class="form-control">
            </div>
        </div>

        <div class="space-y-5">
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-bold text-dark-900">Status</h3>
                    <?= statusBadge($car['status']) ?>
                </div>
                <p class="text-xs text-gray-400 mb-4">Created: <?= formatDate($car['created_at']) ?> · Views: <?= number_format($car['views_count']) ?></p>
                <div class="form-group"><label class="form-label">Price (KSh) *</label><input type="number" name="price" required class="form-control" value="<?= $car['price'] ?>"></div>
                <div class="form-group"><label class="form-label">Old Price</label><input type="number" name="old_price" class="form-control" value="<?= $car['old_price'] ?>"></div>
                <label class="form-check mb-4"><input type="checkbox" name="price_negotiable" value="1" <?= $car['price_negotiable']?'checked':'' ?>> Negotiable</label>
                <button type="submit" class="btn btn-primary w-full"><i data-lucide="save" class="w-4 h-4"></i> Update Listing</button>
            </div>
        </div>
    </div>
</form>

<?php include __DIR__ . '/footer.php'; ?>
