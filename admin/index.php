<?php
$pageTitle = 'Dashboard';
require_once dirname(__DIR__) . '/functions.php';
include __DIR__ . '/header.php';

$stats = getDashboardStats();
$recentActivity = getRecentActivity(10);
$recentInquiries = getInquiries([], 5);
$monthlyStats = getMonthlyStats(6);
$recentCars = getCars([], 5, 0, 'c.created_at DESC');
?>

<!-- Stats Cards -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="stat-card">
        <div class="stat-icon bg-blue-100"><i data-lucide="car" class="w-5 h-5 text-blue-600"></i></div>
        <div>
            <div class="stat-value"><?= number_format($stats['cars']['total_cars'] ?? 0) ?></div>
            <div class="stat-label">Total Cars</div>
            <div class="flex gap-2 mt-1">
                <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded"><?= $stats['cars']['active_cars'] ?? 0 ?> active</span>
                <span class="text-[10px] font-bold text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded"><?= $stats['cars']['pending_cars'] ?? 0 ?> pending</span>
            </div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-emerald-100"><i data-lucide="inbox" class="w-5 h-5 text-emerald-600"></i></div>
        <div>
            <div class="stat-value"><?= number_format($stats['inquiries']['total_inquiries'] ?? 0) ?></div>
            <div class="stat-label">Total Inquiries</div>
            <div class="flex gap-2 mt-1">
                <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded"><?= $stats['inquiries']['new_inquiries'] ?? 0 ?> new</span>
            </div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-purple-100"><i data-lucide="banknote" class="w-5 h-5 text-purple-600"></i></div>
        <div>
            <div class="stat-value"><?= formatPrice($stats['revenue']['total_revenue'] ?? 0) ?></div>
            <div class="stat-label">Total Revenue</div>
            <div class="mt-1"><span class="text-[10px] font-bold text-purple-600 bg-purple-50 px-1.5 py-0.5 rounded"><?= $stats['revenue']['total_payments'] ?? 0 ?> payments</span></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-amber-100"><i data-lucide="users" class="w-5 h-5 text-amber-600"></i></div>
        <div>
            <div class="stat-value"><?= number_format($stats['users']['total_users'] ?? 0) ?></div>
            <div class="stat-label">Total Users</div>
            <div class="flex gap-2 mt-1">
                <span class="text-[10px] font-bold text-dark-600 bg-gray-100 px-1.5 py-0.5 rounded"><?= $stats['users']['total_vendors'] ?? 0 ?> vendors</span>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Chart -->
    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 p-5">
        <h3 class="font-bold text-dark-900 mb-4">Overview (Last 6 Months)</h3>
        <canvas id="overviewChart" height="120"></canvas>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <h3 class="font-bold text-dark-900 mb-4">Quick Actions</h3>
        <div class="space-y-2">
            <a href="<?= BASE_URL ?>/admin/car-add" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition text-sm font-medium text-dark-700">
                <div class="w-9 h-9 bg-primary-100 rounded-lg flex items-center justify-center"><i data-lucide="plus" class="w-4 h-4 text-primary-600"></i></div> Add New Car
            </a>
            <a href="<?= BASE_URL ?>/admin/inquiries?status=new" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition text-sm font-medium text-dark-700">
                <div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center"><i data-lucide="inbox" class="w-4 h-4 text-blue-600"></i></div> View New Inquiries
            </a>
            <a href="<?= BASE_URL ?>/admin/invoice-add" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition text-sm font-medium text-dark-700">
                <div class="w-9 h-9 bg-purple-100 rounded-lg flex items-center justify-center"><i data-lucide="file-plus" class="w-4 h-4 text-purple-600"></i></div> Create Invoice
            </a>
            <a href="<?= BASE_URL ?>/admin/payment-add" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition text-sm font-medium text-dark-700">
                <div class="w-9 h-9 bg-emerald-100 rounded-lg flex items-center justify-center"><i data-lucide="credit-card" class="w-4 h-4 text-emerald-600"></i></div> Record Payment
            </a>
            <a href="<?= BASE_URL ?>/admin/user-add" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition text-sm font-medium text-dark-700">
                <div class="w-9 h-9 bg-amber-100 rounded-lg flex items-center justify-center"><i data-lucide="user-plus" class="w-4 h-4 text-amber-600"></i></div> Add User
            </a>
            <a href="<?= BASE_URL ?>/admin/settings" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition text-sm font-medium text-dark-700">
                <div class="w-9 h-9 bg-gray-100 rounded-lg flex items-center justify-center"><i data-lucide="settings" class="w-4 h-4 text-gray-600"></i></div> Site Settings
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Recent Inquiries -->
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="flex items-center justify-between p-5 border-b border-gray-100">
            <h3 class="font-bold text-dark-900">Recent Inquiries</h3>
            <a href="<?= BASE_URL ?>/admin/inquiries" class="text-xs text-primary-600 font-semibold hover:underline">View All</a>
        </div>
        <div class="divide-y divide-gray-100">
            <?php if (empty($recentInquiries)): ?>
            <p class="p-5 text-sm text-gray-400 text-center">No inquiries yet</p>
            <?php else: foreach ($recentInquiries as $inq): ?>
            <a href="<?= BASE_URL ?>/admin/inquiry/<?= $inq['id'] ?>" class="flex items-center gap-3 p-4 hover:bg-gray-50 transition">
                <div class="w-9 h-9 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i data-lucide="user" class="w-4 h-4 text-blue-600"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-dark-800 truncate"><?= clean($inq['name']) ?></p>
                    <p class="text-xs text-gray-400 truncate"><?= clean($inq['car_title'] ?? $inq['type']) ?> · <?= timeAgo($inq['created_at']) ?></p>
                </div>
                <div class="flex-shrink-0"><?= statusBadge($inq['status']) ?></div>
            </a>
            <?php endforeach; endif; ?>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="flex items-center justify-between p-5 border-b border-gray-100">
            <h3 class="font-bold text-dark-900">Recent Activity</h3>
        </div>
        <div class="divide-y divide-gray-100 max-h-[400px] overflow-y-auto">
            <?php if (empty($recentActivity)): ?>
            <p class="p-5 text-sm text-gray-400 text-center">No activity yet</p>
            <?php else: foreach ($recentActivity as $act): ?>
            <div class="flex items-start gap-3 p-4">
                <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                    <i data-lucide="activity" class="w-3.5 h-3.5 text-gray-500"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-dark-700">
                        <span class="font-semibold"><?= clean(($act['first_name'] ?? '') . ' ' . ($act['last_name'] ?? '')) ?></span>
                        <?= clean($act['description'] ?? $act['action']) ?>
                    </p>
                    <p class="text-xs text-gray-400 mt-0.5"><?= timeAgo($act['created_at']) ?></p>
                </div>
            </div>
            <?php endforeach; endif; ?>
        </div>
    </div>
</div>

<?php
$chartLabels = json_encode(array_column($monthlyStats, 'label'));
$chartCars = json_encode(array_column($monthlyStats, 'cars'));
$chartInquiries = json_encode(array_column($monthlyStats, 'inquiries'));

$pageScripts = <<<JS
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('overviewChart'), {
    type: 'bar',
    data: {
        labels: {$chartLabels},
        datasets: [
            { label: 'New Cars', data: {$chartCars}, backgroundColor: '#16a34a', borderRadius: 6, barPercentage: 0.6 },
            { label: 'Inquiries', data: {$chartInquiries}, backgroundColor: '#3b82f6', borderRadius: 6, barPercentage: 0.6 }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, padding: 20, font: { size: 12 } } } },
        scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 11 } } }, x: { grid: { display: false }, ticks: { font: { size: 11 } } } }
    }
});
</script>
JS;

include __DIR__ . '/footer.php';
?>
