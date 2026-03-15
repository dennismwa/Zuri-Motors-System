<?php
require_once dirname(__DIR__) . '/functions.php';
$id = (int)($_GET['id'] ?? 0);
$car = getCar($id);
if (!$car) { Flash::error('Car not found.'); redirect(BASE_URL.'/admin/cars'); }
$pageTitle = 'Edit: ' . $car['title'];
$brands = getBrands(false); $categories = getCategories(false); $allFeatures = getFeaturesGrouped(false); $locations = getLocations();
$carImages = getCarImages($id); $carFeatureIds = array_column(getCarFeatures($id), 'feature_id');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CSRF::verifyOrDie();
    $data = $_POST; $data['user_id'] = $data['user_id'] ?: $car['user_id'];
    $result = saveCar($data, $id);
    if ($result['success']) {
        if (!empty($_FILES['images']['name'][0])) {
            $st = db()->prepare("SELECT 1 FROM car_images WHERE car_id=? AND is_primary=1 LIMIT 1"); $st->execute([$id]); $setPrimaryNext = !(bool)$st->fetchColumn();
            $uploads = ImageUpload::uploadMultiple($_FILES['images'], CARS_UPLOAD_PATH, 'car');
            $maxSort = db()->prepare("SELECT MAX(sort_order) FROM car_images WHERE car_id=?"); $maxSort->execute([$id]); $sort=(int)$maxSort->fetchColumn();
            foreach ($uploads as $i => $u) {
                if ($u['success']) {
                    $sort++;
                    $isPrimary = $setPrimaryNext ? 1 : 0;
                    if ($setPrimaryNext) $setPrimaryNext = false;
                    db()->prepare("INSERT INTO car_images (car_id,image_url,is_primary,sort_order) VALUES (?,?,?,?)")->execute([$id,$u['url'],$isPrimary,$sort]);
                }
            }
        }
        // Update features
        db()->prepare("DELETE FROM car_features WHERE car_id=?")->execute([$id]);
        if (!empty($_POST['features'])) { foreach ($_POST['features'] as $fid) { db()->prepare("INSERT IGNORE INTO car_features (car_id,feature_id) VALUES (?,?)")->execute([$id,(int)$fid]); } }
        Flash::success('Car updated successfully.');
        redirect(BASE_URL.'/admin/car-edit/'.$id);
    }
    Flash::error($result['message']);
}

// Handle image deletion
if (isset($_GET['delete_image'])) {
    $imgId = (int)$_GET['delete_image'];
    $img = db()->prepare("SELECT * FROM car_images WHERE id=? AND car_id=?"); $img->execute([$imgId,$id]); $img=$img->fetch();
    if ($img) { ImageUpload::delete($img['image_url']); db()->prepare("DELETE FROM car_images WHERE id=?")->execute([$imgId]); Flash::success('Image deleted.'); }
    redirect(BASE_URL.'/admin/car-edit/'.$id);
}
if (isset($_GET['primary_image'])) {
    $imgId = (int)$_GET['primary_image'];
    db()->prepare("UPDATE car_images SET is_primary=0 WHERE car_id=?")->execute([$id]);
    db()->prepare("UPDATE car_images SET is_primary=1 WHERE id=? AND car_id=?")->execute([$imgId,$id]);
    Flash::success('Primary image updated.'); redirect(BASE_URL.'/admin/car-edit/'.$id);
}

include __DIR__ . '/header.php';
?>
<div class="flex items-center gap-3 mb-5">
    <a href="<?=BASE_URL?>/admin/cars" class="btn btn-ghost btn-sm"><i data-lucide="arrow-left" class="w-4 h-4"></i> Back</a>
    <a href="<?=BASE_URL?>/car/<?=$car['slug']?>" target="_blank" class="btn btn-outline btn-sm"><i data-lucide="external-link" class="w-4 h-4"></i> View Listing</a>
    <?= statusBadge($car['status']) ?>
</div>
<form method="POST" enctype="multipart/form-data" class="space-y-6">
<?= CSRF::field() ?>
<div class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="font-bold text-dark-900 mb-4">Basic Information</h3>
            <div class="mb-4"><label class="form-label">Title</label><input type="text" name="title" required class="form-control" value="<?=clean($car['title'])?>"></div>
            <div class="grid sm:grid-cols-3 gap-4 mb-4">
                <div><label class="form-label">Brand</label><select name="brand_id" required class="form-control"><?php foreach($brands as $b): ?><option value="<?=$b['id']?>" <?=$car['brand_id']==$b['id']?'selected':''?>><?=clean($b['name'])?></option><?php endforeach; ?></select></div>
                <div><label class="form-label">Model</label><input type="text" name="model" required class="form-control" value="<?=clean($car['model'])?>"></div>
                <div><label class="form-label">Category</label><select name="category_id" required class="form-control"><?php foreach($categories as $c): ?><option value="<?=$c['id']?>" <?=$car['category_id']==$c['id']?'selected':''?>><?=clean($c['name'])?></option><?php endforeach; ?></select></div>
            </div>
            <div class="grid sm:grid-cols-4 gap-4 mb-4">
                <div><label class="form-label">Year</label><select name="year" class="form-control"><?php for($y=date('Y')+1;$y>=1990;$y--): ?><option value="<?=$y?>" <?=$car['year']==$y?'selected':''?>><?=$y?></option><?php endfor; ?></select></div>
                <div><label class="form-label">Mileage</label><input type="number" name="mileage" class="form-control" value="<?=$car['mileage']?>"></div>
                <div><label class="form-label">Condition</label><select name="condition_type" class="form-control"><?php foreach(['used','new','certified'] as $ct): ?><option value="<?=$ct?>" <?=$car['condition_type']===$ct?'selected':''?>><?=ucfirst($ct)?></option><?php endforeach; ?></select></div>
                <div><label class="form-label">Fuel</label><select name="fuel_type" class="form-control"><?php foreach(['petrol','diesel','hybrid','electric','plugin_hybrid','lpg'] as $ft): ?><option value="<?=$ft?>" <?=$car['fuel_type']===$ft?'selected':''?>><?=ucfirst(str_replace('_',' ',$ft))?></option><?php endforeach; ?></select></div>
            </div>
            <div class="grid sm:grid-cols-4 gap-4 mb-4">
                <div><label class="form-label">Transmission</label><select name="transmission" class="form-control"><?php foreach(['automatic','manual','cvt','semi_automatic'] as $t): ?><option value="<?=$t?>" <?=$car['transmission']===$t?'selected':''?>><?=ucfirst(str_replace('_',' ',$t))?></option><?php endforeach; ?></select></div>
                <div><label class="form-label">Engine</label><input type="text" name="engine_size" class="form-control" value="<?=clean($car['engine_size']??'')?>"></div>
                <div><label class="form-label">Color</label><input type="text" name="color" class="form-control" value="<?=clean($car['color']??'')?>"></div>
                <div><label class="form-label">Location</label><input type="text" name="location" class="form-control" value="<?=clean($car['location']??'')?>"></div>
            </div>
            <div class="grid sm:grid-cols-4 gap-4">
                <div><label class="form-label">Doors</label><input type="number" name="doors" class="form-control" value="<?=$car['doors']?>"></div>
                <div><label class="form-label">Seats</label><input type="number" name="seats" class="form-control" value="<?=$car['seats']?>"></div>
                <div><label class="form-label">HP</label><input type="number" name="horsepower" class="form-control" value="<?=$car['horsepower']??''?>"></div>
                <div><label class="form-label">Drivetrain</label><select name="drivetrain" class="form-control"><?php foreach(['fwd','rwd','awd','4wd'] as $d): ?><option value="<?=$d?>" <?=($car['drivetrain']??'')===$d?'selected':''?>><?=strtoupper($d)?></option><?php endforeach; ?></select></div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="font-bold text-dark-900 mb-4">Description</h3>
            <textarea name="description" class="form-control" rows="6"><?=clean($car['description']??'')?></textarea>
        </div>
        <!-- Current Images -->
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="font-bold text-dark-900 mb-4">Images (<?=count($carImages)?>)</h3>
            <div class="grid grid-cols-4 sm:grid-cols-6 gap-3 mb-4">
                <?php foreach($carImages as $img): ?>
                <div class="relative group rounded-lg overflow-hidden aspect-video bg-gray-100">
                    <img src="<?=resolveUrl($img['image_url'])?>" class="w-full h-full object-cover">
                    <?php if($img['is_primary']): ?><span class="absolute top-1 left-1 bg-primary-600 text-white text-[9px] font-bold px-1.5 py-0.5 rounded">Primary</span><?php endif; ?>
                    <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 flex items-center justify-center gap-1 transition">
                        <?php if(!$img['is_primary']): ?><a href="?id=<?=$id?>&primary_image=<?=$img['id']?>" class="w-7 h-7 bg-white rounded-full flex items-center justify-center text-xs" title="Set Primary"><i data-lucide="star" class="w-3 h-3"></i></a><?php endif; ?>
                        <a href="?id=<?=$id?>&delete_image=<?=$img['id']?>" onclick="return confirm('Delete?')" class="w-7 h-7 bg-red-500 text-white rounded-full flex items-center justify-center" title="Delete"><i data-lucide="trash-2" class="w-3 h-3"></i></a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <input type="file" name="images[]" multiple accept="image/*" class="form-control text-sm">
            <p class="text-xs text-gray-400 mt-1">Add more images. Max 10MB each.</p>
        </div>
        <!-- Features -->
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="font-bold text-dark-900 mb-4">Features</h3>
            <?php foreach ($allFeatures as $cat => $feats): ?>
            <div class="mb-4"><h4 class="text-xs font-bold text-gray-500 uppercase mb-2"><?=ucfirst($cat)?></h4>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-1">
                <?php foreach($feats as $f): ?>
                <label class="form-check text-xs"><input type="checkbox" name="features[]" value="<?=$f['id']?>" <?=in_array($f['id'],$carFeatureIds)?'checked':''?>> <?=clean($f['name'])?></label>
                <?php endforeach; ?>
            </div></div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="space-y-6">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="font-bold text-dark-900 mb-4">Pricing</h3>
            <div class="mb-4"><label class="form-label">Price (KSh)</label><input type="number" name="price" required class="form-control" value="<?=$car['price']?>"></div>
            <div class="mb-4"><label class="form-label">Old Price</label><input type="number" name="old_price" class="form-control" value="<?=$car['old_price']??''?>"></div>
            <label class="form-check mb-4"><input type="checkbox" name="price_negotiable" value="1" <?=$car['price_negotiable']?'checked':''?>> Negotiable</label>
            <div class="mb-3"><label class="form-label">Deposit</label><input type="number" name="deposit_amount" class="form-control" value="<?=$car['deposit_amount']??''?>"></div>
            <div class="mb-3"><label class="form-label">Monthly Payment</label><input type="number" name="monthly_installment" class="form-control" value="<?=$car['monthly_installment']??''?>"></div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="font-bold text-dark-900 mb-4">Publishing</h3>
            <div class="mb-4"><label class="form-label">Status</label><select name="status" class="form-control"><?php foreach(['active','pending','sold','reserved','draft','archived'] as $s): ?><option value="<?=$s?>" <?=$car['status']===$s?'selected':''?>><?=ucfirst($s)?></option><?php endforeach; ?></select></div>
            <label class="form-check mb-2"><input type="checkbox" name="is_featured" value="1" <?=$car['is_featured']?'checked':''?>> Featured</label>
            <label class="form-check mb-2"><input type="checkbox" name="is_offer" value="1" <?=$car['is_offer']?'checked':''?>> Special Offer</label>
            <label class="form-check mb-3"><input type="checkbox" name="is_hot_deal" value="1" <?=$car['is_hot_deal']?'checked':''?>> Hot Deal</label>
            <div class="mb-4"><label class="form-label">Offer Label</label><input type="text" name="offer_label" class="form-control" value="<?=clean($car['offer_label']??'')?>"></div>
        </div>
        <button type="submit" class="btn btn-primary btn-lg w-full"><i data-lucide="save" class="w-5 h-5"></i> Update Listing</button>
    </div>
</div>
</form>
<?php include __DIR__ . '/footer.php'; ?>
