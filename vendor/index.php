<?php
$pageTitle = 'Vendor Dashboard';
require_once dirname(__DIR__) . '/functions.php';

$vendorId = Auth::id();
$stats = getDashboardStats($vendorId);
$recentCars = getCars(['user_id' => $vendorId], 5, 0, 'c.created_at DESC');
$recentInquiries = getInquiries(['vendor_id' => $vendorId], 5);

include __DIR__ . '/header.php';
?>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="stat-card">
        <div class="stat-icon bg-blue-100"><i data-lucide="car" class="w-5 h-5 text-blue-600"></i></div>
        <div><div class="stat-value"><?= number_format($stats['cars']['total_cars'] ?? 0) ?></div><div class="stat-label">My Listings</div>
            <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded"><?= $stats['cars']['active_cars'] ?? 0 ?> active</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-emerald-100"><i data-lucide="inbox" class="w-5 h-5 text-emerald-600"></i></div>
        <div><div class="stat-value"><?= number_format($stats['inquiries']['total_inquiries'] ?? 0) ?></div><div class="stat-label">Inquiries</div>
            <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded"><?= $stats['inquiries']['new_inquiries'] ?? 0 ?> new</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-purple-100"><i data-lucide="eye" class="w-5 h-5 text-purple-600"></i></div>
        <div><div class="stat-value"><?= number_format($stats['cars']['total_views'] ?? 0) ?></div><div class="stat-label">Total Views</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-amber-100"><i data-lucide="trending-up" class="w-5 h-5 text-amber-600"></i></div>
        <div><div class="stat-value"><?= number_format($stats['cars']['sold_cars'] ?? 0) ?></div><div class="stat-label">Cars Sold</div></div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Recent Listings -->
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="flex items-center justify-between p-5 border-b border-gray-100">
            <h3 class="font-bold text-dark-900">My Recent Listings</h3>
            <a href="<?= BASE_URL ?>/vendor/cars" class="text-xs text-primary-600 font-semibold hover:underline">View All</a>
        </div>
        <div class="divide-y divide-gray-100">
            <?php if (empty($recentCars)): ?>
            <div class="p-8 text-center">
                <p class="text-gray-400 text-sm mb-3">No listings yet</p>
                <a href="<?= BASE_URL ?>/vendor/car-add" class="btn btn-primary btn-sm"><i data-lucide="plus" class="w-4 h-4"></i> Add Your First Car</a>
            </div>
            <?php else: foreach ($recentCars as $car):
                $img = $car['primary_image'] ? BASE_URL.'/'.ltrim($car['primary_image'],'/') : BASE_URL.'/assets/images/car-placeholder.jpg';
            ?>
            <div class="flex items-center gap-3 p-4">
                <img src="<?= $img ?>" alt="" class="w-14 h-10 object-cover rounded-lg flex-shrink-0">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-dark-900 truncate"><?= clean($car['title']) ?></p>
                    <p class="text-xs text-gray-400"><?= formatPrice($car['price']) ?> · <?= $car['views_count'] ?> views</p>
                </div>
                <?= statusBadge($car['status']) ?>
            </div>
            <?php endforeach; endif; ?>
        </div>
    </div>

    <!-- Recent Inquiries -->
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="flex items-center justify-between p-5 border-b border-gray-100">
            <h3 class="font-bold text-dark-900">Recent Inquiries</h3>
            <a href="<?= BASE_URL ?>/vendor/inquiries" class="text-xs text-primary-600 font-semibold hover:underline">View All</a>
        </div>
        <div class="divide-y divide-gray-100">
            <?php if (empty($recentInquiries)): ?>
            <p class="p-8 text-center text-gray-400 text-sm">No inquiries yet</p>
            <?php else: foreach ($recentInquiries as $inq): ?>
            <div class="flex items-center gap-3 p-4">
                <div class="w-9 h-9 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i data-lucide="user" class="w-4 h-4 text-blue-600"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-dark-800"><?= clean($inq['name']) ?></p>
                    <p class="text-xs text-gray-400"><?= clean($inq['car_title'] ?? $inq['type']) ?> · <?= timeAgo($inq['created_at']) ?></p>
                </div>
                <div class="flex items-center gap-2">
                    <?= statusBadge($inq['status']) ?>
                    <a href="<?= whatsappLink('Hi '.$inq['name'].', regarding your inquiry...', preg_replace('/[^0-9]/','',$inq['phone'])) ?>" target="_blank" class="btn btn-icon btn-sm btn-ghost text-emerald-500"><i data-lucide="message-circle" class="w-4 h-4"></i></a>
                </div>
            </div>
            <?php endforeach; endif; ?>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-3">
    <a href="<?= BASE_URL ?>/vendor/car-add" class="bg-white rounded-xl border border-gray-200 p-5 text-center hover:border-primary-200 hover:shadow-md transition">
        <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center mx-auto mb-3"><i data-lucide="plus" class="w-6 h-6 text-primary-600"></i></div>
        <span class="text-sm font-semibold text-dark-900">Add New Car</span>
    </a>
    <a href="<?= BASE_URL ?>/vendor/inquiries?status=new" class="bg-white rounded-xl border border-gray-200 p-5 text-center hover:border-primary-200 hover:shadow-md transition">
        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mx-auto mb-3"><i data-lucide="inbox" class="w-6 h-6 text-blue-600"></i></div>
        <span class="text-sm font-semibold text-dark-900">New Inquiries</span>
    </a>
    <a href="<?= BASE_URL ?>/vendor/reports" class="bg-white rounded-xl border border-gray-200 p-5 text-center hover:border-primary-200 hover:shadow-md transition">
        <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mx-auto mb-3"><i data-lucide="bar-chart-3" class="w-6 h-6 text-purple-600"></i></div>
        <span class="text-sm font-semibold text-dark-900">View Reports</span>
    </a>
    <a href="<?= BASE_URL ?>/vendor/profile" class="bg-white rounded-xl border border-gray-200 p-5 text-center hover:border-primary-200 hover:shadow-md transition">
        <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center mx-auto mb-3"><i data-lucide="settings" class="w-6 h-6 text-amber-600"></i></div>
        <span class="text-sm font-semibold text-dark-900">My Profile</span>
    </a>
</div>

<?php include __DIR__ . '/footer.php'; ?>
