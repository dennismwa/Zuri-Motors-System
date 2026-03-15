<?php
$pageTitle = 'Reports & Analytics';
require_once dirname(__DIR__) . '/functions.php';

$stats = getDashboardStats();
$monthlyStats = getMonthlyStats(12);
$topBrands = db()->query("SELECT b.name, COUNT(*) as cnt FROM cars c JOIN brands b ON c.brand_id=b.id WHERE c.status='active' GROUP BY b.name ORDER BY cnt DESC LIMIT 10")->fetchAll();
$topLocations = db()->query("SELECT location, COUNT(*) as cnt FROM cars WHERE status='active' AND location IS NOT NULL GROUP BY location ORDER BY cnt DESC LIMIT 10")->fetchAll();

include __DIR__ . '/header.php';
?>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="stat-card"><div class="stat-icon bg-blue-100"><i data-lucide="car" class="w-5 h-5 text-blue-600"></i></div><div><div class="stat-value"><?= number_format($stats['cars']['active_cars'] ?? 0) ?></div><div class="stat-label">Active Listings</div></div></div>
    <div class="stat-card"><div class="stat-icon bg-emerald-100"><i data-lucide="trending-up" class="w-5 h-5 text-emerald-600"></i></div><div><div class="stat-value"><?= number_format($stats['cars']['sold_cars'] ?? 0) ?></div><div class="stat-label">Cars Sold</div></div></div>
    <div class="stat-card"><div class="stat-icon bg-purple-100"><i data-lucide="banknote" class="w-5 h-5 text-purple-600"></i></div><div><div class="stat-value"><?= formatPrice($stats['revenue']['total_revenue'] ?? 0) ?></div><div class="stat-label">Revenue</div></div></div>
    <div class="stat-card"><div class="stat-icon bg-amber-100"><i data-lucide="eye" class="w-5 h-5 text-amber-600"></i></div><div><div class="stat-value"><?= number_format($stats['cars']['total_views'] ?? 0) ?></div><div class="stat-label">Total Views</div></div></div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded-xl border border-gray-200 p-5"><h3 class="font-bold text-dark-900 mb-4">Monthly Trends</h3><canvas id="trendsChart" height="150"></canvas></div>
    <div class="bg-white rounded-xl border border-gray-200 p-5"><h3 class="font-bold text-dark-900 mb-4">Revenue by Month</h3><canvas id="revenueChart" height="150"></canvas></div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <h3 class="font-bold text-dark-900 mb-4">Top Brands</h3>
        <?php foreach($topBrands as $b): $pct = $stats['cars']['active_cars'] > 0 ? round($b['cnt'] / $stats['cars']['active_cars'] * 100) : 0; ?>
        <div class="flex items-center gap-3 mb-3"><span class="text-sm font-medium text-dark-700 w-28"><?= clean($b['name']) ?></span><div class="flex-1 bg-gray-100 rounded-full h-2"><div class="bg-primary-500 h-2 rounded-full" style="width:<?= $pct ?>%"></div></div><span class="text-xs text-gray-500 w-10 text-right"><?= $b['cnt'] ?></span></div>
        <?php endforeach; ?>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <h3 class="font-bold text-dark-900 mb-4">Top Locations</h3>
        <?php foreach($topLocations as $l): $pct = $stats['cars']['active_cars'] > 0 ? round($l['cnt'] / $stats['cars']['active_cars'] * 100) : 0; ?>
        <div class="flex items-center gap-3 mb-3"><span class="text-sm font-medium text-dark-700 w-28"><?= clean($l['location']) ?></span><div class="flex-1 bg-gray-100 rounded-full h-2"><div class="bg-blue-500 h-2 rounded-full" style="width:<?= $pct ?>%"></div></div><span class="text-xs text-gray-500 w-10 text-right"><?= $l['cnt'] ?></span></div>
        <?php endforeach; ?>
    </div>
</div>

<?php
$labels = json_encode(array_column($monthlyStats,'label'));
$cars = json_encode(array_column($monthlyStats,'cars'));
$inquiries = json_encode(array_column($monthlyStats,'inquiries'));
$revenue = json_encode(array_column($monthlyStats,'revenue'));
$pageScripts = <<<JS
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('trendsChart'),{type:'line',data:{labels:{$labels},datasets:[{label:'Cars',data:{$cars},borderColor:'#16a34a',backgroundColor:'rgba(22,163,74,0.1)',fill:true,tension:0.4},{label:'Inquiries',data:{$inquiries},borderColor:'#3b82f6',backgroundColor:'rgba(59,130,246,0.1)',fill:true,tension:0.4}]},options:{responsive:true,plugins:{legend:{position:'bottom'}},scales:{y:{beginAtZero:true}}}});
new Chart(document.getElementById('revenueChart'),{type:'bar',data:{labels:{$labels},datasets:[{label:'Revenue (KSh)',data:{$revenue},backgroundColor:'#8b5cf6',borderRadius:6}]},options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}});
</script>
JS;
include __DIR__ . '/footer.php'; ?>
