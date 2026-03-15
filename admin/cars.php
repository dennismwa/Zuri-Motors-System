<?php
require_once dirname(__DIR__) . '/functions.php';
$pageTitle = 'Car Listings';

// Handle actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    CSRF::verifyOrDie();
    $id = (int)$_GET['id'];
    switch ($_GET['action']) {
        case 'delete': deleteCar($id); Flash::success('Car deleted.'); break;
        case 'activate': updateCarStatus($id, 'active'); Flash::success('Car activated.'); break;
        case 'deactivate': updateCarStatus($id, 'draft'); Flash::success('Car deactivated.'); break;
        case 'sold': updateCarStatus($id, 'sold'); Flash::success('Car marked as sold.'); break;
        case 'feature':
            db()->prepare("UPDATE cars SET is_featured = NOT is_featured WHERE id = ?")->execute([$id]);
            Flash::success('Featured status updated.'); break;
    }
    redirect(BASE_URL . '/admin/cars');
}

$filters = ['search' => $_GET['search'] ?? '', 'brand_id' => $_GET['brand_id'] ?? '', 'category_id' => $_GET['category_id'] ?? ''];
$statusFilter = $_GET['status'] ?? '';
if ($statusFilter) {
    // Override active-only default by querying directly
}

// Direct query for admin (all statuses)
$where = ['1=1']; $params = [];
if ($statusFilter) { $where[] = 'c.status = ?'; $params[] = $statusFilter; }
if ($filters['search']) { $where[] = '(c.title LIKE ? OR c.model LIKE ?)'; $s='%'.$filters['search'].'%'; $params[]=$s; $params[]=$s; }
if ($filters['brand_id']) { $where[] = 'c.brand_id = ?'; $params[] = (int)$filters['brand_id']; }
if ($filters['category_id']) { $where[] = 'c.category_id = ?'; $params[] = (int)$filters['category_id']; }

$whereClause = implode(' AND ', $where);
$totalStmt = db()->prepare("SELECT COUNT(*) FROM cars c WHERE $whereClause");
$totalStmt->execute($params);
$total = (int)$totalStmt->fetchColumn();

$pagination = new Pagination($total, 20);
$sql = "SELECT c.*, b.name AS brand_name, cat.name AS category_name, u.company_name AS seller_company,
        (SELECT image_url FROM car_images WHERE car_id=c.id AND is_primary=1 LIMIT 1) AS primary_image
        FROM cars c LEFT JOIN brands b ON c.brand_id=b.id LEFT JOIN categories cat ON c.category_id=cat.id 
        LEFT JOIN users u ON c.user_id=u.id WHERE $whereClause ORDER BY c.created_at DESC LIMIT {$pagination->perPage} OFFSET {$pagination->offset}";
$stmt = db()->prepare($sql);
$stmt->execute($params);
$cars = $stmt->fetchAll();
$brands = getBrands(false); $categories = getCategories(false);

include __DIR__ . '/header.php';
?>
<div class="flex items-center justify-between mb-5">
    <div>
        <p class="text-sm text-gray-500"><?= number_format($total) ?> total listings</p>
    </div>
    <a href="<?= BASE_URL ?>/admin/car-add" class="btn btn-primary"><i data-lucide="plus" class="w-4 h-4"></i> Add New Car</a>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl border border-gray-200 p-4 mb-5">
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[200px]"><label class="text-xs font-semibold text-gray-500 mb-1 block">Search</label><input type="text" name="search" value="<?= clean($filters['search']) ?>" placeholder="Search cars..." class="filter-input text-sm"></div>
        <div><label class="text-xs font-semibold text-gray-500 mb-1 block">Status</label>
            <select name="status" class="filter-select text-sm"><option value="">All Status</option>
            <?php foreach(['active','pending','sold','reserved','draft','archived'] as $s): ?>
            <option value="<?=$s?>" <?=$statusFilter===$s?'selected':''?>><?=ucfirst($s)?></option>
            <?php endforeach; ?></select></div>
        <div><label class="text-xs font-semibold text-gray-500 mb-1 block">Brand</label>
            <select name="brand_id" class="filter-select text-sm"><option value="">All Brands</option>
            <?php foreach($brands as $b): ?><option value="<?=$b['id']?>" <?=$filters['brand_id']==$b['id']?'selected':''?>><?=clean($b['name'])?></option><?php endforeach; ?></select></div>
        <button type="submit" class="btn btn-primary btn-sm"><i data-lucide="search" class="w-4 h-4"></i> Filter</button>
        <a href="<?= BASE_URL ?>/admin/cars" class="btn btn-ghost btn-sm">Reset</a>
    </form>
</div>

<!-- Table -->
<div class="bg-white rounded-xl border border-gray-200 overflow-x-auto">
    <table class="data-table">
        <thead><tr>
            <th>Car</th><th>Price</th><th>Brand</th><th>Category</th><th>Status</th><th>Featured</th><th>Views</th><th>Date</th><th>Actions</th>
        </tr></thead>
        <tbody>
        <?php foreach ($cars as $c):
            $img = $c['primary_image'] ? resolveUrl($c['primary_image']) : BASE_URL.'/assets/images/car-placeholder.jpg';
            $csrf = CSRF::generate();
        ?>
        <tr>
            <td><div class="flex items-center gap-3"><img src="<?=$img?>" class="w-14 h-10 rounded-lg object-cover" alt=""><div><p class="text-sm font-semibold text-dark-900 max-w-[200px] truncate"><?=clean($c['title'])?></p><p class="text-xs text-gray-400"><?=$c['year']?> · <?=formatMileage($c['mileage'],$c['mileage_unit']??'km')?></p></div></div></td>
            <td class="font-semibold text-dark-900"><?=formatPrice($c['price'])?></td>
            <td><?=clean($c['brand_name'])?></td>
            <td><?=clean($c['category_name'])?></td>
            <td><?=statusBadge($c['status'])?></td>
            <td><?=$c['is_featured']?'<i data-lucide="star" class="w-4 h-4 text-amber-500 fill-amber-500"></i>':'<i data-lucide="star" class="w-4 h-4 text-gray-300"></i>'?></td>
            <td class="text-sm text-gray-500"><?=number_format($c['views_count'])?></td>
            <td class="text-xs text-gray-400"><?=formatDate($c['created_at'],'M d, Y')?></td>
            <td>
                <div class="actions">
                    <a href="<?=BASE_URL?>/car/<?=$c['slug']?>" target="_blank" class="btn btn-icon btn-sm btn-ghost" title="View"><i data-lucide="eye" class="w-4 h-4"></i></a>
                    <a href="<?=BASE_URL?>/admin/car-edit/<?=$c['id']?>" class="btn btn-icon btn-sm btn-ghost" title="Edit"><i data-lucide="pencil" class="w-4 h-4"></i></a>
                    <a href="<?=BASE_URL?>/admin/cars?action=feature&id=<?=$c['id']?>&csrf_token=<?=$csrf?>" class="btn btn-icon btn-sm btn-ghost" title="Toggle Featured"><i data-lucide="star" class="w-4 h-4"></i></a>
                    <?php if($c['status']==='active'): ?>
                    <a href="<?=BASE_URL?>/admin/cars?action=sold&id=<?=$c['id']?>&csrf_token=<?=$csrf?>" class="btn btn-icon btn-sm btn-ghost text-blue-600" title="Mark Sold"><i data-lucide="check-circle" class="w-4 h-4"></i></a>
                    <?php elseif($c['status']==='pending'||$c['status']==='draft'): ?>
                    <a href="<?=BASE_URL?>/admin/cars?action=activate&id=<?=$c['id']?>&csrf_token=<?=$csrf?>" class="btn btn-icon btn-sm btn-ghost text-green-600" title="Activate"><i data-lucide="check" class="w-4 h-4"></i></a>
                    <?php endif; ?>
                    <a href="<?=BASE_URL?>/admin/cars?action=delete&id=<?=$c['id']?>&csrf_token=<?=$csrf?>" onclick="return confirm('Delete this car?')" class="btn btn-icon btn-sm btn-ghost text-red-600" title="Delete"><i data-lucide="trash-2" class="w-4 h-4"></i></a>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if(empty($cars)): ?><tr><td colspan="9" class="text-center py-8 text-gray-400">No cars found</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>
<div class="mt-5"><?= $pagination->render(BASE_URL.'/admin/cars', array_filter($_GET)) ?></div>
<?php include __DIR__ . '/footer.php'; ?>
