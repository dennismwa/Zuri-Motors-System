<?php
$pageTitle = 'My Listings';
require_once dirname(__DIR__) . '/functions.php';

$vendorId = Auth::id();

if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    // Verify ownership
    $car = getCar($id);
    if ($car && (int)$car['user_id'] === $vendorId) {
        switch ($_GET['action']) {
            case 'delete': deleteCar($id); Flash::success('Car deleted.'); break;
            case 'deactivate': updateCarStatus($id, 'draft'); Flash::success('Car deactivated.'); break;
            case 'activate': updateCarStatus($id, 'pending'); Flash::success('Car submitted for approval.'); break;
        }
    }
    redirect(BASE_URL . '/vendor/cars');
}

$search = $_GET['search'] ?? '';
$statusFilter = $_GET['status'] ?? '';
$where = ['c.user_id = ?']; $params = [$vendorId];
if ($statusFilter) { $where[] = 'c.status = ?'; $params[] = $statusFilter; }
if ($search) { $where[] = '(c.title LIKE ? OR c.model LIKE ?)'; $s='%'.$search.'%'; $params[]=$s; $params[]=$s; }
$whereClause = implode(' AND ', $where);

$stmt = db()->prepare("SELECT COUNT(*) FROM cars c WHERE {$whereClause}"); $stmt->execute($params); $total = (int)$stmt->fetchColumn();
$pagination = new Pagination($total, 12);

$sql = "SELECT c.*, b.name AS brand_name, (SELECT image_url FROM car_images WHERE car_id=c.id AND is_primary=1 LIMIT 1) AS thumb
        FROM cars c LEFT JOIN brands b ON c.brand_id=b.id WHERE {$whereClause} ORDER BY c.created_at DESC LIMIT {$pagination->perPage} OFFSET {$pagination->offset}";
$stmt = db()->prepare($sql); $stmt->execute($params); $cars = $stmt->fetchAll();

include __DIR__ . '/header.php';
?>

<div class="flex items-center justify-between mb-5">
    <form method="GET" class="flex items-end gap-3">
        <input type="text" name="search" value="<?= clean($search) ?>" class="filter-input text-sm" placeholder="Search...">
        <select name="status" class="filter-select text-sm"><option value="">All Status</option>
            <?php foreach(['active','pending','sold','draft'] as $s): ?><option value="<?= $s ?>" <?= $statusFilter===$s?'selected':'' ?>><?= ucfirst($s) ?></option><?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary btn-sm"><i data-lucide="search" class="w-4 h-4"></i></button>
    </form>
    <a href="<?= BASE_URL ?>/vendor/car-add" class="btn btn-primary btn-sm"><i data-lucide="plus" class="w-4 h-4"></i> Add Car</a>
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="data-table">
        <thead><tr><th>Car</th><th>Price</th><th>Status</th><th>Views</th><th>Inquiries</th><th>Date</th><th class="text-right">Actions</th></tr></thead>
        <tbody>
        <?php if (empty($cars)): ?>
        <tr><td colspan="7" class="text-center py-8 text-gray-400">No listings yet. <a href="<?= BASE_URL ?>/vendor/car-add" class="text-primary-600 hover:underline">Add your first car</a></td></tr>
        <?php else: foreach ($cars as $car):
            $thumb = $car['thumb'] ? BASE_URL.'/'.ltrim($car['thumb'],'/') : BASE_URL.'/assets/images/car-placeholder.jpg';
        ?>
        <tr>
            <td>
                <div class="flex items-center gap-3">
                    <img src="<?= $thumb ?>" alt="" class="w-14 h-10 object-cover rounded-lg">
                    <div><p class="text-sm font-semibold text-dark-900"><?= clean($car['title']) ?></p><p class="text-xs text-gray-400"><?= clean($car['brand_name']) ?> · <?= $car['year'] ?></p></div>
                </div>
            </td>
            <td class="font-semibold text-sm"><?= formatPrice($car['price']) ?></td>
            <td><?= statusBadge($car['status']) ?></td>
            <td class="text-sm text-gray-500"><?= number_format($car['views_count']) ?></td>
            <td class="text-sm text-gray-500"><?= $car['inquiries_count'] ?></td>
            <td class="text-xs text-gray-400"><?= formatDate($car['created_at'], 'd M Y') ?></td>
            <td>
                <div class="actions justify-end">
                    <a href="<?= BASE_URL ?>/car/<?= $car['slug'] ?>" target="_blank" class="btn btn-icon btn-sm btn-ghost" title="View"><i data-lucide="eye" class="w-4 h-4"></i></a>
                    <a href="<?= BASE_URL ?>/vendor/car-edit/<?= $car['id'] ?>" class="btn btn-icon btn-sm btn-ghost" title="Edit"><i data-lucide="pencil" class="w-4 h-4"></i></a>
                    <?php if ($car['status'] === 'draft'): ?>
                    <a href="?action=activate&id=<?= $car['id'] ?>" class="btn btn-icon btn-sm btn-ghost text-emerald-500" title="Submit for Approval"><i data-lucide="send" class="w-4 h-4"></i></a>
                    <?php elseif ($car['status'] === 'active'): ?>
                    <a href="?action=deactivate&id=<?= $car['id'] ?>" class="btn btn-icon btn-sm btn-ghost text-amber-500" title="Deactivate"><i data-lucide="pause" class="w-4 h-4"></i></a>
                    <?php endif; ?>
                    <button onclick="confirmAction('Delete this car?','?action=delete&id=<?= $car['id'] ?>')" class="btn btn-icon btn-sm btn-ghost text-red-500" title="Delete"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                </div>
            </td>
        </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
    <div class="p-4"><?= $pagination->render(BASE_URL . '/vendor/cars', array_filter(['search'=>$search,'status'=>$statusFilter])) ?></div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
