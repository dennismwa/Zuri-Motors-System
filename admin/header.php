<?php
if (!defined('ROOT_PATH')) {
    require_once dirname(__DIR__) . '/functions.php';
}
Auth::requireAdmin();

$currentUser = Auth::user();
$companyName = Settings::get('company_name', 'Zuri Motors');
$adminLogo = Settings::get('logo', 'assets/images/ZuriMotors -logo.png');
$unreadChats = getUnreadChatCount();
$newInquiries = countInquiries(['status' => 'new']);
$pendingCars = countCars(['status' => 'pending'] + ['brand_id'=>'','category_id'=>'','condition_type'=>'','fuel_type'=>'','transmission'=>'','location'=>'','min_price'=>'','max_price'=>'','min_year'=>'','max_year'=>'','min_mileage'=>'','max_mileage'=>'']);

// Fix pending cars count - direct query
try {
    $pendingCars = (int)db()->query("SELECT COUNT(*) FROM cars WHERE status='pending'")->fetchColumn();
} catch(Exception $e) { $pendingCars = 0; }

$adminPage = basename($_SERVER['SCRIPT_NAME'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Dashboard' ?> - <?= clean($companyName) ?> Admin</title>
    <link rel="icon" href="<?= BASE_URL ?>/<?= Settings::get('favicon', 'assets/images/favicon.png') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: {
                colors: { primary:{50:'#f0fdf4',100:'#dcfce7',200:'#bbf7d0',300:'#86efac',400:'#4ade80',500:'#22c55e',600:'#16a34a',700:'#15803d',800:'#166534',900:'#14532d'},dark:{50:'#f8fafc',100:'#f1f5f9',200:'#e2e8f0',300:'#cbd5e1',400:'#94a3b8',500:'#64748b',600:'#475569',700:'#334155',800:'#1e293b',900:'#0f172a',950:'#020617'}},
                fontFamily: { sans:['DM Sans','system-ui','sans-serif'] }
            }}
        }
    </script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    <?php if (isset($pageHead)) echo $pageHead; ?>
</head>
<body class="font-sans bg-gray-50 text-gray-800 antialiased">

<!-- Admin Sidebar -->
<aside class="admin-sidebar" id="admin-sidebar">
    <div class="p-5 border-b border-white/10">
        <a href="<?= BASE_URL ?>/admin" class="flex items-center gap-2.5">
            <?php if ($adminLogo): $adminLogoUrl = str_starts_with($adminLogo, 'http') || str_starts_with($adminLogo, '//') ? $adminLogo : BASE_URL . '/' . str_replace(' ', '%20', ltrim($adminLogo, '/')); ?>
            <img src="<?= htmlspecialchars($adminLogoUrl) ?>" alt="<?= clean($companyName) ?>" class="h-9 w-auto max-w-[140px] object-contain">
            <?php else: ?>
            <div class="w-9 h-9 bg-primary-600 rounded-xl flex items-center justify-center">
                <i data-lucide="car" class="w-5 h-5 text-white"></i>
            </div>
            <span class="text-lg font-bold text-white tracking-tight"><?= clean($companyName) ?></span>
            <?php endif; ?>
        </a>
    </div>

    <nav class="py-4 overflow-y-auto" style="height:calc(100vh - 80px)">
        <div class="admin-sidebar-section">Main</div>
        <a href="<?= BASE_URL ?>/admin" class="admin-sidebar-link <?= $adminPage === 'index' ? 'active' : '' ?>">
            <i data-lucide="layout-dashboard" class="w-4 h-4"></i> Dashboard
        </a>

        <div class="admin-sidebar-section">Inventory</div>
        <a href="<?= BASE_URL ?>/admin/cars" class="admin-sidebar-link <?= $adminPage === 'cars' ? 'active' : '' ?>">
            <i data-lucide="car" class="w-4 h-4"></i> Car Listings
            <?php if ($pendingCars > 0): ?><span class="badge"><?= $pendingCars ?></span><?php endif; ?>
        </a>
        <a href="<?= BASE_URL ?>/admin/car-add" class="admin-sidebar-link <?= $adminPage === 'car-add' ? 'active' : '' ?>">
            <i data-lucide="plus-circle" class="w-4 h-4"></i> Add New Car
        </a>
        <a href="<?= BASE_URL ?>/admin/categories" class="admin-sidebar-link <?= $adminPage === 'categories' ? 'active' : '' ?>">
            <i data-lucide="grid-3x3" class="w-4 h-4"></i> Categories
        </a>
        <a href="<?= BASE_URL ?>/admin/brands" class="admin-sidebar-link <?= $adminPage === 'brands' ? 'active' : '' ?>">
            <i data-lucide="award" class="w-4 h-4"></i> Brands
        </a>
        <a href="<?= BASE_URL ?>/admin/features" class="admin-sidebar-link <?= $adminPage === 'features' ? 'active' : '' ?>">
            <i data-lucide="list-checks" class="w-4 h-4"></i> Features
        </a>

        <div class="admin-sidebar-section">Sales & Leads</div>
        <a href="<?= BASE_URL ?>/admin/inquiries" class="admin-sidebar-link <?= $adminPage === 'inquiries' || $adminPage === 'inquiry-detail' ? 'active' : '' ?>">
            <i data-lucide="inbox" class="w-4 h-4"></i> Inquiries / Leads
            <?php if ($newInquiries > 0): ?><span class="badge"><?= $newInquiries ?></span><?php endif; ?>
        </a>
        <a href="<?= BASE_URL ?>/admin/invoices" class="admin-sidebar-link <?= in_array($adminPage, ['invoices','invoice-add','invoice-detail']) ? 'active' : '' ?>">
            <i data-lucide="file-text" class="w-4 h-4"></i> Invoices
        </a>
        <a href="<?= BASE_URL ?>/admin/payments" class="admin-sidebar-link <?= in_array($adminPage, ['payments','payment-add']) ? 'active' : '' ?>">
            <i data-lucide="credit-card" class="w-4 h-4"></i> Payments
        </a>

        <div class="admin-sidebar-section">People</div>
        <a href="<?= BASE_URL ?>/admin/users" class="admin-sidebar-link <?= in_array($adminPage, ['users','user-add','user-edit']) ? 'active' : '' ?>">
            <i data-lucide="users" class="w-4 h-4"></i> Users
        </a>
        <a href="<?= BASE_URL ?>/admin/vendors" class="admin-sidebar-link <?= $adminPage === 'vendors' ? 'active' : '' ?>">
            <i data-lucide="store" class="w-4 h-4"></i> Vendors
        </a>

        <div class="admin-sidebar-section">Content</div>
        <a href="<?= BASE_URL ?>/admin/testimonials" class="admin-sidebar-link <?= $adminPage === 'testimonials' ? 'active' : '' ?>">
            <i data-lucide="message-square-quote" class="w-4 h-4"></i> Testimonials
        </a>
        <a href="<?= BASE_URL ?>/admin/pages" class="admin-sidebar-link <?= $adminPage === 'pages' ? 'active' : '' ?>">
            <i data-lucide="file" class="w-4 h-4"></i> Pages
        </a>
        <a href="<?= BASE_URL ?>/admin/chat" class="admin-sidebar-link <?= $adminPage === 'chat' ? 'active' : '' ?>">
            <i data-lucide="message-circle" class="w-4 h-4"></i> Chat Support
            <?php if ($unreadChats > 0): ?><span class="badge"><?= $unreadChats ?></span><?php endif; ?>
        </a>

        <div class="admin-sidebar-section">Analytics</div>
        <a href="<?= BASE_URL ?>/admin/reports" class="admin-sidebar-link <?= $adminPage === 'reports' ? 'active' : '' ?>">
            <i data-lucide="bar-chart-3" class="w-4 h-4"></i> Reports
        </a>

        <div class="admin-sidebar-section">System</div>
        <a href="<?= BASE_URL ?>/admin/settings" class="admin-sidebar-link <?= $adminPage === 'settings' ? 'active' : '' ?>">
            <i data-lucide="settings" class="w-4 h-4"></i> Settings
        </a>
        <a href="<?= BASE_URL ?>/admin/profile" class="admin-sidebar-link <?= $adminPage === 'profile' ? 'active' : '' ?>">
            <i data-lucide="user-circle" class="w-4 h-4"></i> My Profile
        </a>
        <a href="<?= BASE_URL ?>/" class="admin-sidebar-link" target="_blank">
            <i data-lucide="external-link" class="w-4 h-4"></i> View Website
        </a>
        <a href="<?= BASE_URL ?>/logout" class="admin-sidebar-link text-red-400 hover:text-red-300">
            <i data-lucide="log-out" class="w-4 h-4"></i> Sign Out
        </a>
    </nav>
</aside>

<!-- Admin Main -->
<div class="admin-main">
    <!-- Top Bar -->
    <div class="admin-topbar">
        <div class="flex items-center gap-3">
            <button onclick="document.getElementById('admin-sidebar').classList.toggle('open')" class="lg:hidden w-9 h-9 rounded-lg flex items-center justify-center hover:bg-gray-100 transition">
                <i data-lucide="menu" class="w-5 h-5"></i>
            </button>
            <h2 class="text-lg font-bold text-dark-900"><?= $pageTitle ?? 'Dashboard' ?></h2>
        </div>
        <div class="flex items-center gap-2">
            <!-- Notifications -->
            <div class="relative group">
                <button class="w-9 h-9 rounded-lg flex items-center justify-center hover:bg-gray-100 transition relative">
                    <i data-lucide="bell" class="w-5 h-5 text-gray-500"></i>
                    <?php if ($newInquiries + $unreadChats > 0): ?>
                    <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                    <?php endif; ?>
                </button>
                <div class="absolute right-0 top-full mt-2 w-72 bg-white rounded-xl shadow-xl border border-gray-100 py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50">
                    <div class="px-4 py-2 border-b border-gray-100">
                        <p class="text-sm font-bold text-dark-900">Notifications</p>
                    </div>
                    <?php if ($newInquiries > 0): ?>
                    <a href="<?= BASE_URL ?>/admin/inquiries?status=new" class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 transition">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center"><i data-lucide="inbox" class="w-4 h-4 text-blue-600"></i></div>
                        <div><p class="text-sm font-medium text-dark-800"><?= $newInquiries ?> new inquiries</p><p class="text-xs text-gray-400">Awaiting response</p></div>
                    </a>
                    <?php endif; ?>
                    <?php if ($pendingCars > 0): ?>
                    <a href="<?= BASE_URL ?>/admin/cars?status=pending" class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 transition">
                        <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center"><i data-lucide="clock" class="w-4 h-4 text-amber-600"></i></div>
                        <div><p class="text-sm font-medium text-dark-800"><?= $pendingCars ?> pending listings</p><p class="text-xs text-gray-400">Need approval</p></div>
                    </a>
                    <?php endif; ?>
                    <?php if ($unreadChats > 0): ?>
                    <a href="<?= BASE_URL ?>/admin/chat" class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 transition">
                        <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center"><i data-lucide="message-circle" class="w-4 h-4 text-emerald-600"></i></div>
                        <div><p class="text-sm font-medium text-dark-800"><?= $unreadChats ?> unread chats</p><p class="text-xs text-gray-400">Customer messages</p></div>
                    </a>
                    <?php endif; ?>
                    <?php if ($newInquiries + $unreadChats + $pendingCars === 0): ?>
                    <p class="px-4 py-4 text-sm text-gray-400 text-center">No new notifications</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- User -->
            <div class="flex items-center gap-2 pl-2 border-l border-gray-200 ml-1">
                <div class="w-8 h-8 rounded-lg bg-primary-100 text-primary-700 flex items-center justify-center text-xs font-bold">
                    <?= strtoupper(substr($currentUser['first_name'], 0, 1) . substr($currentUser['last_name'], 0, 1)) ?>
                </div>
                <div class="hidden sm:block">
                    <p class="text-sm font-semibold text-dark-800 leading-tight"><?= clean($currentUser['first_name']) ?></p>
                    <p class="text-[10px] text-gray-400 uppercase"><?= $currentUser['role'] ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="admin-content">
        <?= Flash::render() ?>
