<?php
if (!defined('ROOT_PATH')) {
    require_once __DIR__ . '/config.php';
    require_once __DIR__ . '/functions.php';
}

$companyName = Settings::get('company_name', 'Zuri Motors');
$phone = Settings::get('phone', '+254 700 000 000');
$email = Settings::get('email', 'info@zurimotors.com');
$whatsapp = Settings::get('whatsapp', '254700000000');
$logo = Settings::get('logo', 'assets/images/ZuriMotors -logo.png');
$enableCompare = Settings::get('enable_compare', '1');
$compareCount = count(getCompareCarIds());

$categories = getCategories(true);
$brands = getBrands(true, true);
$popularBrands = array_filter($brands, fn($b) => $b['is_popular']);
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <?php echo $pageSeo ?? seoMeta(); ?>
    <link rel="icon" href="<?= BASE_URL ?>/<?= Settings::get('favicon', 'assets/images/favicon.png') ?>">
    
    <!-- Preconnect -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { 50:'#f0fdf4',100:'#dcfce7',200:'#bbf7d0',300:'#86efac',400:'#4ade80',500:'#22c55e',600:'#16a34a',700:'#15803d',800:'#166534',900:'#14532d' },
                        accent: { 50:'#fffbeb',100:'#fef3c7',200:'#fde68a',300:'#fcd34d',400:'#fbbf24',500:'#f59e0b',600:'#d97706',700:'#b45309',800:'#92400e',900:'#78350f' },
                        dark: { 50:'#f8fafc',100:'#f1f5f9',200:'#e2e8f0',300:'#cbd5e1',400:'#94a3b8',500:'#64748b',600:'#475569',700:'#334155',800:'#1e293b',900:'#0f172a',950:'#020617' }
                    },
                    fontFamily: {
                        sans: ['DM Sans', 'system-ui', 'sans-serif'],
                        display: ['Playfair Display', 'Georgia', 'serif'],
                    }
                }
            }
        }
    </script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    
    <?php if (isset($pageHead)) echo $pageHead; ?>
</head>
<body class="font-sans text-dark-800 bg-white antialiased">

<!-- ============================================================ -->
<!-- TOP BAR -->
<!-- ============================================================ -->
<div class="topbar bg-dark-900 text-white/80 text-sm hidden lg:block">
    <div class="max-w-7xl mx-auto px-6 flex items-center justify-between h-10">
        <div class="flex items-center gap-6">
            <a href="tel:<?= str_replace(' ', '', $phone) ?>" class="flex items-center gap-1.5 hover:text-primary-400 transition">
                <i data-lucide="phone" class="w-3.5 h-3.5"></i>
                <span><?= clean($phone) ?></span>
            </a>
            <a href="mailto:<?= clean($email) ?>" class="flex items-center gap-1.5 hover:text-primary-400 transition">
                <i data-lucide="mail" class="w-3.5 h-3.5"></i>
                <span><?= clean($email) ?></span>
            </a>
        </div>
        <div class="flex items-center gap-4">
            <a href="<?= whatsappLink('Hello, I am interested in your vehicles.') ?>" target="_blank" class="flex items-center gap-1.5 hover:text-primary-400 transition">
                <i data-lucide="message-circle" class="w-3.5 h-3.5"></i>
                <span>WhatsApp</span>
            </a>
            <span class="w-px h-4 bg-white/20"></span>
            <a href="<?= BASE_URL ?>/about" class="hover:text-primary-400 transition">About</a>
            <a href="<?= BASE_URL ?>/contact" class="hover:text-primary-400 transition">Contact</a>
            <a href="<?= BASE_URL ?>/faq" class="hover:text-primary-400 transition">FAQ</a>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- MAIN NAVIGATION -->
<!-- ============================================================ -->
<header id="main-header" class="sticky top-0 z-50 bg-white border-b border-gray-100 transition-shadow duration-300">
    <div class="max-w-7xl mx-auto px-4 lg:px-6">
        <div class="flex items-center justify-between h-16 lg:h-20">
            
            <!-- Logo -->
            <a href="<?= BASE_URL ?>/" class="flex items-center gap-2.5 flex-shrink-0">
                <?php if ($logo): $logoUrl = str_starts_with($logo, 'http') || str_starts_with($logo, '//') ? $logo : BASE_URL . '/' . str_replace(' ', '%20', ltrim($logo, '/')); ?>
                <img src="<?= htmlspecialchars($logoUrl) ?>" alt="<?= clean($companyName) ?>" class="h-11 w-auto max-w-[180px] object-contain object-left">
                <?php else: ?>
                <div class="w-9 h-9 bg-primary-600 rounded-xl flex items-center justify-center">
                    <i data-lucide="car" class="w-5 h-5 text-white"></i>
                </div>
                <?php endif; ?>
            </a>

            <!-- Desktop Navigation -->
            <nav class="hidden lg:flex items-center gap-1">
                <a href="<?= BASE_URL ?>/" class="nav-link <?= currentPath() === '/' ? 'active' : '' ?>">Home</a>
                
                <!-- Cars Dropdown -->
                <div class="nav-dropdown-wrapper group">
                    <a href="<?= BASE_URL ?>/cars" class="nav-link flex items-center gap-1 <?= isActivePage('/cars') || isActivePage('/car/') ? 'active' : '' ?>">
                        Cars
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5 transition-transform group-hover:rotate-180"></i>
                    </a>
                    <div class="nav-mega-dropdown">
                        <div class="max-w-7xl mx-auto px-6 py-8">
                            <div class="grid grid-cols-4 gap-8">
                                <!-- By Category -->
                                <div>
                                    <h4 class="text-xs font-bold text-dark-400 uppercase tracking-wider mb-4">By Category</h4>
                                    <ul class="space-y-2">
                                        <?php foreach (array_slice($categories, 0, 8) as $cat): ?>
                                        <li>
                                            <a href="<?= BASE_URL ?>/category/<?= $cat['slug'] ?>" class="text-sm text-dark-600 hover:text-primary-600 transition flex items-center gap-2">
                                                <i data-lucide="<?= $cat['icon'] ?: 'car' ?>" class="w-4 h-4"></i>
                                                <?= clean($cat['name']) ?>
                                                <span class="text-xs text-dark-400">(<?= $cat['car_count'] ?>)</span>
                                            </a>
                                        </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <!-- By Brand -->
                                <div>
                                    <h4 class="text-xs font-bold text-dark-400 uppercase tracking-wider mb-4">Popular Brands</h4>
                                    <ul class="space-y-2">
                                        <?php foreach (array_slice($popularBrands, 0, 8) as $br): ?>
                                        <li>
                                            <a href="<?= BASE_URL ?>/brand/<?= $br['slug'] ?>" class="text-sm text-dark-600 hover:text-primary-600 transition">
                                                <?= clean($br['name']) ?>
                                                <span class="text-xs text-dark-400">(<?= $br['car_count'] ?>)</span>
                                            </a>
                                        </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <!-- By Condition -->
                                <div>
                                    <h4 class="text-xs font-bold text-dark-400 uppercase tracking-wider mb-4">By Condition</h4>
                                    <ul class="space-y-2">
                                        <li><a href="<?= BASE_URL ?>/cars?condition_type=used" class="text-sm text-dark-600 hover:text-primary-600 transition">Used Cars</a></li>
                                        <li><a href="<?= BASE_URL ?>/cars?condition_type=new" class="text-sm text-dark-600 hover:text-primary-600 transition">New Cars</a></li>
                                        <li><a href="<?= BASE_URL ?>/cars?condition_type=certified" class="text-sm text-dark-600 hover:text-primary-600 transition">Certified Pre-Owned</a></li>
                                        <li><a href="<?= BASE_URL ?>/cars?is_featured=1" class="text-sm text-dark-600 hover:text-primary-600 transition">Featured Cars</a></li>
                                        <li><a href="<?= BASE_URL ?>/cars?is_offer=1" class="text-sm text-dark-600 hover:text-primary-600 transition">Special Offers</a></li>
                                        <li><a href="<?= BASE_URL ?>/cars?is_hot_deal=1" class="text-sm text-dark-600 hover:text-primary-600 transition">Hot Deals</a></li>
                                    </ul>
                                </div>
                                <!-- Promo Card -->
                                <div class="bg-gradient-to-br from-dark-900 to-dark-800 rounded-2xl p-6 text-white">
                                    <span class="text-xs font-semibold text-primary-400 uppercase tracking-wider">Looking to sell?</span>
                                    <h4 class="text-lg font-bold mt-2 mb-2">Sell Your Car Fast</h4>
                                    <p class="text-sm text-white/70 mb-4">Get the best value for your vehicle with our trusted marketplace.</p>
                                    <a href="<?= BASE_URL ?>/sell-your-car" class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                                        Get Started
                                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <a href="<?= BASE_URL ?>/about" class="nav-link <?= isActivePage('/about') ? 'active' : '' ?>">About</a>
                <a href="<?= BASE_URL ?>/contact" class="nav-link <?= isActivePage('/contact') ? 'active' : '' ?>">Contact</a>
            </nav>

            <!-- Right Actions -->
            <div class="flex items-center gap-2">
                <!-- Search Toggle -->
                <button onclick="toggleSearch()" class="w-10 h-10 rounded-xl flex items-center justify-center text-dark-500 hover:bg-gray-100 transition" title="Search">
                    <i data-lucide="search" class="w-5 h-5"></i>
                </button>

                <?php if ($enableCompare === '1'): ?>
                <!-- Compare -->
                <a href="<?= BASE_URL ?>/compare" class="w-10 h-10 rounded-xl flex items-center justify-center text-dark-500 hover:bg-gray-100 transition relative" title="Compare">
                    <i data-lucide="bar-chart-2" class="w-5 h-5"></i>
                    <?php if ($compareCount > 0): ?>
                    <span id="compare-count" class="absolute -top-0.5 -right-0.5 w-5 h-5 bg-primary-600 text-white text-[10px] font-bold rounded-full flex items-center justify-center"><?= $compareCount ?></span>
                    <?php endif; ?>
                </a>
                <?php endif; ?>

                <?php if (Auth::check()): ?>
                    <?php $currentUser = Auth::user(); ?>
                    <!-- User Menu -->
                    <div class="relative group">
                        <button class="flex items-center gap-2 h-10 pl-2 pr-3 rounded-xl hover:bg-gray-100 transition">
                            <div class="w-7 h-7 rounded-lg bg-primary-100 text-primary-700 flex items-center justify-center text-xs font-bold">
                                <?= strtoupper(substr($currentUser['first_name'], 0, 1) . substr($currentUser['last_name'], 0, 1)) ?>
                            </div>
                            <span class="hidden lg:inline text-sm font-medium text-dark-700"><?= clean($currentUser['first_name']) ?></span>
                            <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-dark-400 hidden lg:inline"></i>
                        </button>
                        <div class="absolute right-0 top-full mt-2 w-56 bg-white rounded-xl shadow-xl border border-gray-100 py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                            <div class="px-4 py-2 border-b border-gray-100">
                                <p class="text-sm font-semibold text-dark-900"><?= clean($currentUser['first_name'] . ' ' . $currentUser['last_name']) ?></p>
                                <p class="text-xs text-dark-400"><?= clean($currentUser['email']) ?></p>
                            </div>
                            <?php if (Auth::isAdmin() || Auth::isStaff()): ?>
                            <a href="<?= BASE_URL ?>/admin" class="flex items-center gap-2.5 px-4 py-2 text-sm text-dark-600 hover:bg-gray-50 hover:text-primary-600 transition">
                                <i data-lucide="layout-dashboard" class="w-4 h-4"></i> Admin Dashboard
                            </a>
                            <?php endif; ?>
                            <?php if (Auth::isVendor()): ?>
                            <a href="<?= BASE_URL ?>/vendor" class="flex items-center gap-2.5 px-4 py-2 text-sm text-dark-600 hover:bg-gray-50 hover:text-primary-600 transition">
                                <i data-lucide="layout-dashboard" class="w-4 h-4"></i> Vendor Dashboard
                            </a>
                            <?php endif; ?>
                            <a href="<?= BASE_URL ?>/admin/profile" class="flex items-center gap-2.5 px-4 py-2 text-sm text-dark-600 hover:bg-gray-50 hover:text-primary-600 transition">
                                <i data-lucide="user" class="w-4 h-4"></i> My Profile
                            </a>
                            <div class="border-t border-gray-100 mt-1 pt-1">
                                <a href="<?= BASE_URL ?>/logout" class="flex items-center gap-2.5 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">
                                    <i data-lucide="log-out" class="w-4 h-4"></i> Sign Out
                                </a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>/login" class="hidden lg:flex items-center gap-1.5 text-sm font-medium text-dark-600 hover:text-primary-600 transition px-3 py-2">
                        <i data-lucide="user" class="w-4 h-4"></i>
                        Sign In
                    </a>
                <?php endif; ?>

                <!-- Add Listing CTA -->
                <a href="<?= BASE_URL ?>/sell-your-car" class="hidden lg:inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-all duration-200 shadow-sm hover:shadow-md">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Sell Your Car
                </a>

                <!-- Mobile Menu Toggle -->
                <button id="mobile-menu-btn" onclick="toggleMobileMenu()" class="lg:hidden w-10 h-10 rounded-xl flex items-center justify-center text-dark-700 hover:bg-gray-100 transition">
                    <i data-lucide="menu" class="w-5 h-5" id="menu-icon-open"></i>
                    <i data-lucide="x" class="w-5 h-5 hidden" id="menu-icon-close"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- SEARCH OVERLAY -->
    <!-- ============================================================ -->
    <div id="search-overlay" class="hidden fixed inset-0 z-[60] bg-dark-900/60 backdrop-blur-sm">
        <div class="absolute top-0 left-0 right-0 bg-white shadow-2xl">
            <div class="max-w-4xl mx-auto px-6 py-8">
                <div class="flex items-center gap-4">
                    <i data-lucide="search" class="w-6 h-6 text-dark-400 flex-shrink-0"></i>
                    <input type="text" id="search-input" placeholder="Search cars by name, brand, model..." 
                           class="flex-1 text-xl text-dark-800 placeholder:text-dark-300 outline-none bg-transparent"
                           onkeyup="handleSearchKeyup(event)" autocomplete="off">
                    <button onclick="toggleSearch()" class="w-10 h-10 rounded-xl flex items-center justify-center text-dark-400 hover:bg-gray-100 transition">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <div id="search-results" class="mt-6 hidden">
                    <div id="search-results-inner"></div>
                </div>
                <div class="mt-4 flex flex-wrap gap-2">
                    <span class="text-xs text-dark-400">Popular:</span>
                    <a href="<?= BASE_URL ?>/cars?search=toyota" class="text-xs bg-gray-100 text-dark-600 px-3 py-1 rounded-full hover:bg-primary-50 hover:text-primary-700 transition">Toyota</a>
                    <a href="<?= BASE_URL ?>/cars?search=mercedes" class="text-xs bg-gray-100 text-dark-600 px-3 py-1 rounded-full hover:bg-primary-50 hover:text-primary-700 transition">Mercedes-Benz</a>
                    <a href="<?= BASE_URL ?>/cars?search=bmw" class="text-xs bg-gray-100 text-dark-600 px-3 py-1 rounded-full hover:bg-primary-50 hover:text-primary-700 transition">BMW</a>
                    <a href="<?= BASE_URL ?>/category/suv" class="text-xs bg-gray-100 text-dark-600 px-3 py-1 rounded-full hover:bg-primary-50 hover:text-primary-700 transition">SUV</a>
                    <a href="<?= BASE_URL ?>/cars?condition_type=new" class="text-xs bg-gray-100 text-dark-600 px-3 py-1 rounded-full hover:bg-primary-50 hover:text-primary-700 transition">New Cars</a>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- MOBILE MENU -->
    <!-- ============================================================ -->
    <div id="mobile-menu" class="lg:hidden hidden fixed inset-0 z-[55] bg-dark-900/50 backdrop-blur-sm">
        <div class="absolute right-0 top-0 bottom-0 w-[320px] bg-white shadow-2xl overflow-y-auto">
            <div class="p-5">
                <!-- Close -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-2">
                        <?php if ($logo): $logoUrlMobile = str_starts_with($logo, 'http') || str_starts_with($logo, '//') ? $logo : BASE_URL . '/' . str_replace(' ', '%20', ltrim($logo, '/')); ?>
                        <img src="<?= htmlspecialchars($logoUrlMobile) ?>" alt="<?= clean($companyName) ?>" class="h-10 w-auto max-w-[160px] object-contain">
                        <?php else: ?>
                        <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center">
                            <i data-lucide="car" class="w-4 h-4 text-white"></i>
                        </div>
                        <?php endif; ?>
                    </div>
                    <button onclick="toggleMobileMenu()" class="w-9 h-9 rounded-lg flex items-center justify-center text-dark-400 hover:bg-gray-100">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <!-- Mobile Nav Links -->
                <nav class="space-y-1">
                    <a href="<?= BASE_URL ?>/" class="mobile-nav-link <?= currentPath() === '/' ? 'active' : '' ?>">
                        <i data-lucide="home" class="w-5 h-5"></i> Home
                    </a>
                    <a href="<?= BASE_URL ?>/cars" class="mobile-nav-link <?= isActivePage('/cars') ? 'active' : '' ?>">
                        <i data-lucide="car" class="w-5 h-5"></i> All Cars
                    </a>
                    
                    <!-- Categories Accordion -->
                    <div class="mobile-accordion">
                        <button onclick="this.parentElement.classList.toggle('open')" class="mobile-nav-link w-full flex justify-between">
                            <span class="flex items-center gap-3"><i data-lucide="grid-3x3" class="w-5 h-5"></i> Categories</span>
                            <i data-lucide="chevron-down" class="w-4 h-4 transition-transform"></i>
                        </button>
                        <div class="mobile-accordion-content">
                            <?php foreach ($categories as $cat): ?>
                            <a href="<?= BASE_URL ?>/category/<?= $cat['slug'] ?>" class="block px-12 py-2 text-sm text-dark-500 hover:text-primary-600 transition"><?= clean($cat['name']) ?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Brands Accordion -->
                    <div class="mobile-accordion">
                        <button onclick="this.parentElement.classList.toggle('open')" class="mobile-nav-link w-full flex justify-between">
                            <span class="flex items-center gap-3"><i data-lucide="award" class="w-5 h-5"></i> Brands</span>
                            <i data-lucide="chevron-down" class="w-4 h-4 transition-transform"></i>
                        </button>
                        <div class="mobile-accordion-content">
                            <?php foreach (array_slice($popularBrands, 0, 10) as $br): ?>
                            <a href="<?= BASE_URL ?>/brand/<?= $br['slug'] ?>" class="block px-12 py-2 text-sm text-dark-500 hover:text-primary-600 transition"><?= clean($br['name']) ?></a>
                            <?php endforeach; ?>
                            <a href="<?= BASE_URL ?>/cars" class="block px-12 py-2 text-sm text-primary-600 font-medium">View All Brands →</a>
                        </div>
                    </div>

                    <a href="<?= BASE_URL ?>/compare" class="mobile-nav-link">
                        <i data-lucide="bar-chart-2" class="w-5 h-5"></i> Compare
                        <?php if ($compareCount > 0): ?>
                        <span class="ml-auto w-5 h-5 bg-primary-600 text-white text-[10px] font-bold rounded-full flex items-center justify-center"><?= $compareCount ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="<?= BASE_URL ?>/about" class="mobile-nav-link <?= isActivePage('/about') ? 'active' : '' ?>">
                        <i data-lucide="info" class="w-5 h-5"></i> About Us
                    </a>
                    <a href="<?= BASE_URL ?>/contact" class="mobile-nav-link <?= isActivePage('/contact') ? 'active' : '' ?>">
                        <i data-lucide="phone" class="w-5 h-5"></i> Contact
                    </a>
                    <a href="<?= BASE_URL ?>/faq" class="mobile-nav-link">
                        <i data-lucide="help-circle" class="w-5 h-5"></i> FAQ
                    </a>
                </nav>

                <!-- Mobile CTA -->
                <div class="mt-6 space-y-3">
                    <a href="<?= BASE_URL ?>/sell-your-car" class="flex items-center justify-center gap-2 w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3 rounded-xl transition">
                        <i data-lucide="plus" class="w-4 h-4"></i> Sell Your Car
                    </a>
                    <?php if (!Auth::check()): ?>
                    <a href="<?= BASE_URL ?>/login" class="flex items-center justify-center gap-2 w-full border-2 border-dark-200 text-dark-700 font-semibold py-3 rounded-xl hover:bg-gray-50 transition">
                        <i data-lucide="user" class="w-4 h-4"></i> Sign In
                    </a>
                    <?php endif; ?>
                </div>

                <!-- Mobile Contact -->
                <div class="mt-8 pt-6 border-t border-gray-100 space-y-3">
                    <a href="tel:<?= str_replace(' ', '', $phone) ?>" class="flex items-center gap-3 text-sm text-dark-500 hover:text-primary-600 transition">
                        <i data-lucide="phone" class="w-4 h-4"></i> <?= clean($phone) ?>
                    </a>
                    <a href="mailto:<?= clean($email) ?>" class="flex items-center gap-3 text-sm text-dark-500 hover:text-primary-600 transition">
                        <i data-lucide="mail" class="w-4 h-4"></i> <?= clean($email) ?>
                    </a>
                    <a href="<?= whatsappLink() ?>" target="_blank" class="flex items-center gap-3 text-sm text-dark-500 hover:text-primary-600 transition">
                        <i data-lucide="message-circle" class="w-4 h-4"></i> WhatsApp Chat
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Flash Messages -->
<div class="max-w-7xl mx-auto px-4 lg:px-6 mt-2">
    <?= Flash::render() ?>
</div>
