<?php
if (!defined('ROOT_PATH')) {
    require_once dirname(__DIR__) . '/functions.php';
}
Auth::requireVendor();

$currentUser = Auth::user();
$companyName = Settings::get('company_name', 'Zuri Motors');
$vendorId = Auth::id();
$vendorNewInquiries = countInquiries(['vendor_id' => $vendorId, 'status' => 'new']);

$vendorPage = basename($_SERVER['SCRIPT_NAME'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Dashboard' ?> - <?= clean($companyName) ?> Vendor</title>
    <link rel="icon" href="<?= BASE_URL ?>/<?= Settings::get('favicon', 'assets/images/favicon.png') ?>">
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

<aside class="admin-sidebar" id="admin-sidebar">
    <div class="p-5 border-b border-white/10">
        <a href="<?= BASE_URL ?>/vendor" class="flex items-center gap-2.5">
            <div class="w-9 h-9 bg-primary-600 rounded-xl flex items-center justify-center">
                <i data-lucide="store" class="w-5 h-5 text-white"></i>
            </div>
            <div>
                <span class="text-sm font-bold text-white block leading-tight"><?= clean($currentUser['company_name'] ?: $currentUser['first_name']) ?></span>
                <span class="text-[10px] text-white/40 uppercase">Vendor Panel</span>
            </div>
        </a>
    </div>

    <nav class="py-4">
        <div class="admin-sidebar-section">Overview</div>
        <a href="<?= BASE_URL ?>/vendor" class="admin-sidebar-link <?= $vendorPage === 'index' ? 'active' : '' ?>">
            <i data-lucide="layout-dashboard" class="w-4 h-4"></i> Dashboard
        </a>

        <div class="admin-sidebar-section">Inventory</div>
        <a href="<?= BASE_URL ?>/vendor/cars" class="admin-sidebar-link <?= $vendorPage === 'cars' ? 'active' : '' ?>">
            <i data-lucide="car" class="w-4 h-4"></i> My Listings
        </a>
        <a href="<?= BASE_URL ?>/vendor/car-add" class="admin-sidebar-link <?= $vendorPage === 'car-add' ? 'active' : '' ?>">
            <i data-lucide="plus-circle" class="w-4 h-4"></i> Add New Car
        </a>

        <div class="admin-sidebar-section">Sales</div>
        <a href="<?= BASE_URL ?>/vendor/inquiries" class="admin-sidebar-link <?= $vendorPage === 'inquiries' ? 'active' : '' ?>">
            <i data-lucide="inbox" class="w-4 h-4"></i> Inquiries
            <?php if ($vendorNewInquiries > 0): ?><span class="badge"><?= $vendorNewInquiries ?></span><?php endif; ?>
        </a>
        <a href="<?= BASE_URL ?>/vendor/invoices" class="admin-sidebar-link <?= $vendorPage === 'invoices' ? 'active' : '' ?>">
            <i data-lucide="file-text" class="w-4 h-4"></i> Invoices
        </a>
        <a href="<?= BASE_URL ?>/vendor/payments" class="admin-sidebar-link <?= $vendorPage === 'payments' ? 'active' : '' ?>">
            <i data-lucide="credit-card" class="w-4 h-4"></i> Payments
        </a>

        <div class="admin-sidebar-section">Analytics</div>
        <a href="<?= BASE_URL ?>/vendor/reports" class="admin-sidebar-link <?= $vendorPage === 'reports' ? 'active' : '' ?>">
            <i data-lucide="bar-chart-3" class="w-4 h-4"></i> Reports
        </a>

        <div class="admin-sidebar-section">Account</div>
        <a href="<?= BASE_URL ?>/vendor/profile" class="admin-sidebar-link <?= $vendorPage === 'profile' ? 'active' : '' ?>">
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

<div class="admin-main">
    <div class="admin-topbar">
        <div class="flex items-center gap-3">
            <button onclick="document.getElementById('admin-sidebar').classList.toggle('open')" class="lg:hidden w-9 h-9 rounded-lg flex items-center justify-center hover:bg-gray-100 transition">
                <i data-lucide="menu" class="w-5 h-5"></i>
            </button>
            <h2 class="text-lg font-bold text-dark-900"><?= $pageTitle ?? 'Dashboard' ?></h2>
        </div>
        <div class="flex items-center gap-2 pl-2">
            <div class="w-8 h-8 rounded-lg bg-primary-100 text-primary-700 flex items-center justify-center text-xs font-bold">
                <?= strtoupper(substr($currentUser['first_name'], 0, 1) . substr($currentUser['last_name'], 0, 1)) ?>
            </div>
            <div class="hidden sm:block">
                <p class="text-sm font-semibold text-dark-800 leading-tight"><?= clean($currentUser['first_name']) ?></p>
                <p class="text-[10px] text-gray-400 uppercase">Vendor</p>
            </div>
        </div>
    </div>
    <div class="admin-content">
        <?= Flash::render() ?>
