<?php
$pageTitle = 'My Reports';
require_once dirname(__DIR__) . '/functions.php';
$vendorId = Auth::id();
$stats = getDashboardStats($vendorId);

// Vendor-specific monthly stats
$monthlyStats = [];
for ($i = 5; $i >= 0; $i--) {
    $d = date('Y-m', strtotime("-{$i} months"));
    $ms = $d.'-01'; $me = date('Y-m-t', strtotime($ms));
    $stmt = db()->prepare("SELECT COUNT(*) FROM cars WHERE user_id=? AND created_at BETWEEN ? AND ?"); $stmt->execute([$vendorId,$ms,$me.' 23:59:59']); $cc=(int)$stmt->fetchColumn();
    $stmt = db()->prepare("SELECT COUNT(*) FROM inquiries i JOIN cars c ON i.car_id=c.id WHERE c.user_id=? AND i.created_at BETWEEN ? AND ?"); $stmt->execute([$vendorId,$ms,$me.' 23:59:59']); $ic=(int)$stmt->fetchColumn();
    $monthlyStats[] = ['label'=>date('M Y',strtotime($ms)),'cars'=>$cc,'inquiries'=>$ic];
}

include __DIR__ . '/header.php';
?>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="stat-card"><div class="stat-icon bg-blue-100"><i data-lucide="car" class="w-5 h-5 text-blue-600"></i></div><div><div class="stat-value"><?= $stats['cars']['active_cars'] ?? 0 ?></div><div class="stat-label">Active Listings</div></div></div>
    <div class="stat-card"><div class="stat-icon bg-emerald-100"><i data-lucide="inbox" class="w-5 h-5 text-emerald-600"></i></div><div><div class="stat-value"><?= $stats['inquiries']['total_inquiries'] ?? 0 ?></div><div class="stat-label">Total Inquiries</div></div></div>
    <div class="stat-card"><div class="stat-icon bg-purple-100"><i data-lucide="eye" class="w-5 h-5 text-purple-600"></i></div><div><div class="stat-value"><?= number_format($stats['cars']['total_views'] ?? 0) ?></div><div class="stat-label">Total Views</div></div></div>
    <div class="stat-card"><div class="stat-icon bg-amber-100"><i data-lucide="check-circle" class="w-5 h-5 text-amber-600"></i></div><div><div class="stat-value"><?= $stats['cars']['sold_cars'] ?? 0 ?></div><div class="stat-label">Sold</div></div></div>
</div>

<div class="bg-white rounded-xl border border-gray-200 p-5">
    <h3 class="font-bold text-dark-900 mb-4">Monthly Overview</h3>
    <canvas id="vendorChart" height="120"></canvas>
</div>

<?php
$labels = json_encode(array_column($monthlyStats,'label'));
$cars = json_encode(array_column($monthlyStats,'cars'));
$inqs = json_encode(array_column($monthlyStats,'inquiries'));
$pageScripts = <<<JS
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('vendorChart'),{type:'bar',data:{labels:{$labels},datasets:[{label:'Listings',data:{$cars},backgroundColor:'#16a34a',borderRadius:6},{label:'Inquiries',data:{$inqs},backgroundColor:'#3b82f6',borderRadius:6}]},options:{responsive:true,plugins:{legend:{position:'bottom'}},scales:{y:{beginAtZero:true}}}});
</script>
JS;
include __DIR__ . '/footer.php'; ?>
