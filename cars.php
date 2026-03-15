<?php
/**
 * Zuri Motors - Car Listings Page
 * Advanced filtering, sorting, grid/list view
 */
require_once __DIR__ . '/functions.php';

$companyName = Settings::get('company_name', 'Zuri Motors');

// Collect filters from query string
$filters = [
    'brand_id'       => $_GET['brand_id'] ?? '',
    'category_id'    => $_GET['category_id'] ?? '',
    'condition_type'  => $_GET['condition_type'] ?? '',
    'fuel_type'      => $_GET['fuel_type'] ?? '',
    'transmission'   => $_GET['transmission'] ?? '',
    'location'       => $_GET['location'] ?? '',
    'min_price'      => $_GET['min_price'] ?? '',
    'max_price'      => $_GET['max_price'] ?? '',
    'min_year'       => $_GET['min_year'] ?? '',
    'max_year'       => $_GET['max_year'] ?? '',
    'min_mileage'    => $_GET['min_mileage'] ?? '',
    'max_mileage'    => $_GET['max_mileage'] ?? '',
    'is_featured'    => $_GET['is_featured'] ?? '',
    'is_offer'       => $_GET['is_offer'] ?? '',
    'is_hot_deal'    => $_GET['is_hot_deal'] ?? '',
    'search'         => $_GET['search'] ?? '',
    'sort'           => $_GET['sort'] ?? 'newest',
    'body_type'      => $_GET['body_type'] ?? '',
    'color'          => $_GET['color'] ?? '',
    'drivetrain'     => $_GET['drivetrain'] ?? '',
];

// Clean empty values for query
$activeFilters = array_filter($filters, fn($v) => $v !== '');

$perPage = (int)Settings::get('cars_per_page', 12);
$totalCars = countCars($activeFilters);
$pagination = new Pagination($totalCars, $perPage);
$cars = getCars($activeFilters, $pagination->perPage, $pagination->offset, 'c.created_at DESC');

// Data for filters
$brands = getBrands(true, true);
$categories = getCategories(true);
$locations = getLocations(true);

// Build SEO title
$seoTitle = 'Browse Cars';
if (!empty($filters['condition_type'])) $seoTitle = ucfirst($filters['condition_type']) . ' Cars for Sale';
if (!empty($filters['search'])) $seoTitle = 'Search Results: ' . clean($filters['search']);
if (!empty($filters['is_featured'])) $seoTitle = 'Featured Cars';
if (!empty($filters['is_offer'])) $seoTitle = 'Special Offers';
if (!empty($filters['is_hot_deal'])) $seoTitle = 'Hot Deals';

$pageSeo = seoMeta(
    $seoTitle . ' - ' . $companyName,
    'Browse ' . number_format($totalCars) . ' quality vehicles. Find the best deals on used and new cars at ' . $companyName . '.'
);

// Years for filter
$currentYear = (int)date('Y');
$years = range($currentYear, $currentYear - 30);

include __DIR__ . '/header.php';
?>

<!-- Breadcrumb -->
<div class="bg-gray-50 border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 lg:px-6 py-4">
        <?= breadcrumbs([
            ['label' => 'Home', 'url' => BASE_URL . '/'],
            ['label' => $seoTitle]
        ]) ?>
    </div>
</div>

<section class="py-8 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 lg:px-6">
        
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-dark-900 font-display"><?= $seoTitle ?></h1>
                <p class="text-sm text-gray-500 mt-1"><?= number_format($totalCars) ?> vehicles found</p>
            </div>
            <div class="flex items-center gap-3">
                <!-- Sort -->
                <select id="sort-select" onchange="applySort(this.value)" class="filter-select text-sm py-2 w-auto">
                    <option value="newest" <?= $filters['sort'] === 'newest' ? 'selected' : '' ?>>Newest First</option>
                    <option value="oldest" <?= $filters['sort'] === 'oldest' ? 'selected' : '' ?>>Oldest First</option>
                    <option value="price_low" <?= $filters['sort'] === 'price_low' ? 'selected' : '' ?>>Price: Low to High</option>
                    <option value="price_high" <?= $filters['sort'] === 'price_high' ? 'selected' : '' ?>>Price: High to Low</option>
                    <option value="year_new" <?= $filters['sort'] === 'year_new' ? 'selected' : '' ?>>Year: Newest</option>
                    <option value="mileage" <?= $filters['sort'] === 'mileage' ? 'selected' : '' ?>>Mileage: Low to High</option>
                    <option value="popular" <?= $filters['sort'] === 'popular' ? 'selected' : '' ?>>Most Popular</option>
                </select>
                <!-- View Toggle -->
                <div class="hidden sm:flex items-center border border-gray-200 rounded-lg overflow-hidden">
                    <button onclick="setView('grid')" id="view-grid-btn" class="view-toggle-btn active" title="Grid View">
                        <i data-lucide="grid-3x3" class="w-4 h-4"></i>
                    </button>
                    <button onclick="setView('list')" id="view-list-btn" class="view-toggle-btn" title="List View">
                        <i data-lucide="list" class="w-4 h-4"></i>
                    </button>
                </div>
                <!-- Mobile Filter Toggle -->
                <button onclick="toggleFilters()" class="lg:hidden btn btn-outline btn-sm">
                    <i data-lucide="sliders-horizontal" class="w-4 h-4"></i> Filters
                </button>
            </div>
        </div>

        <!-- Active Filters -->
        <?php 
        $displayFilters = array_filter($activeFilters, fn($k) => !in_array($k, ['sort','page']), ARRAY_FILTER_USE_KEY);
        if (!empty($displayFilters)): 
        ?>
        <div class="flex flex-wrap items-center gap-2 mb-5">
            <span class="text-xs font-semibold text-gray-500 uppercase">Active Filters:</span>
            <?php foreach ($displayFilters as $key => $val): 
                $label = ucwords(str_replace('_', ' ', $key)) . ': ';
                // Resolve display names
                if ($key === 'brand_id') {
                    $b = getBrand((int)$val);
                    $label = 'Brand: '; $val = $b ? $b['name'] : $val;
                } elseif ($key === 'category_id') {
                    $c = getCategory((int)$val);
                    $label = 'Category: '; $val = $c ? $c['name'] : $val;
                } elseif (in_array($key, ['min_price','max_price'])) {
                    $val = formatPrice($val, true);
                } elseif ($key === 'is_featured') { $label = ''; $val = 'Featured'; }
                elseif ($key === 'is_offer') { $label = ''; $val = 'Special Offers'; }
                elseif ($key === 'is_hot_deal') { $label = ''; $val = 'Hot Deals'; }
            ?>
            <span class="inline-flex items-center gap-1.5 bg-primary-50 text-primary-700 text-xs font-medium px-3 py-1.5 rounded-full">
                <?= $label ?><?= clean(ucfirst($val)) ?>
                <a href="<?= '?' . http_build_query(array_diff_key($activeFilters, [$key => ''])) ?>" class="hover:text-red-600 transition">
                    <i data-lucide="x" class="w-3 h-3"></i>
                </a>
            </span>
            <?php endforeach; ?>
            <a href="<?= BASE_URL ?>/cars" class="text-xs text-red-600 hover:text-red-700 font-medium ml-2">Clear All</a>
        </div>
        <?php endif; ?>

        <div class="flex gap-6">
            
            <!-- ============================================================ -->
            <!-- FILTERS SIDEBAR -->
            <!-- ============================================================ -->
            <aside id="filters-sidebar" class="hidden lg:block w-[280px] flex-shrink-0">
                <form method="GET" action="<?= BASE_URL ?>/cars" id="filter-form" class="bg-white rounded-2xl border border-gray-200 p-5 sticky top-24">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="font-bold text-dark-900 flex items-center gap-2">
                            <i data-lucide="sliders-horizontal" class="w-4 h-4"></i> Filters
                        </h3>
                        <a href="<?= BASE_URL ?>/cars" class="text-xs text-red-500 hover:text-red-600 font-medium">Reset</a>
                    </div>

                    <!-- Search -->
                    <div class="filter-group">
                        <label class="filter-label">Search</label>
                        <div class="relative">
                            <input type="text" name="search" value="<?= clean($filters['search']) ?>" placeholder="Search cars..." class="filter-input pr-9">
                            <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute right-3 top-1/2 -translate-y-1/2"></i>
                        </div>
                    </div>

                    <!-- Condition -->
                    <div class="filter-group">
                        <label class="filter-label">Condition</label>
                        <div class="flex flex-wrap gap-2">
                            <?php foreach ([''=>'All','used'=>'Used','new'=>'New','certified'=>'Certified'] as $v => $l): ?>
                            <label class="inline-flex items-center gap-1.5 text-xs font-medium cursor-pointer px-3 py-1.5 rounded-full border transition
                                <?= $filters['condition_type'] === $v ? 'bg-primary-50 border-primary-300 text-primary-700' : 'border-gray-200 text-gray-600 hover:border-primary-200' ?>">
                                <input type="radio" name="condition_type" value="<?= $v ?>" <?= $filters['condition_type'] === $v ? 'checked' : '' ?> class="hidden" onchange="this.form.submit()">
                                <?= $l ?>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Brand -->
                    <div class="filter-group">
                        <label class="filter-label">Brand</label>
                        <select name="brand_id" class="filter-select" onchange="this.form.submit()">
                            <option value="">All Brands</option>
                            <?php foreach ($brands as $b): ?>
                            <option value="<?= $b['id'] ?>" <?= $filters['brand_id'] == $b['id'] ? 'selected' : '' ?>><?= clean($b['name']) ?> (<?= $b['car_count'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Category -->
                    <div class="filter-group">
                        <label class="filter-label">Vehicle Type</label>
                        <select name="category_id" class="filter-select" onchange="this.form.submit()">
                            <option value="">All Types</option>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= $filters['category_id'] == $cat['id'] ? 'selected' : '' ?>><?= clean($cat['name']) ?> (<?= $cat['car_count'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Price Range -->
                    <div class="filter-group">
                        <label class="filter-label">Price Range (KSh)</label>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="number" name="min_price" value="<?= clean($filters['min_price']) ?>" placeholder="Min" class="filter-input text-xs" min="0" step="50000">
                            <input type="number" name="max_price" value="<?= clean($filters['max_price']) ?>" placeholder="Max" class="filter-input text-xs" min="0" step="50000">
                        </div>
                    </div>

                    <!-- Year -->
                    <div class="filter-group">
                        <label class="filter-label">Year</label>
                        <div class="grid grid-cols-2 gap-2">
                            <select name="min_year" class="filter-select text-xs">
                                <option value="">From</option>
                                <?php foreach ($years as $y): ?>
                                <option value="<?= $y ?>" <?= $filters['min_year'] == $y ? 'selected' : '' ?>><?= $y ?></option>
                                <?php endforeach; ?>
                            </select>
                            <select name="max_year" class="filter-select text-xs">
                                <option value="">To</option>
                                <?php foreach ($years as $y): ?>
                                <option value="<?= $y ?>" <?= $filters['max_year'] == $y ? 'selected' : '' ?>><?= $y ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Mileage -->
                    <div class="filter-group">
                        <label class="filter-label">Mileage (km)</label>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="number" name="min_mileage" value="<?= clean($filters['min_mileage']) ?>" placeholder="Min" class="filter-input text-xs" min="0" step="5000">
                            <input type="number" name="max_mileage" value="<?= clean($filters['max_mileage']) ?>" placeholder="Max" class="filter-input text-xs" min="0" step="5000">
                        </div>
                    </div>

                    <!-- Fuel Type -->
                    <div class="filter-group">
                        <label class="filter-label">Fuel Type</label>
                        <select name="fuel_type" class="filter-select" onchange="this.form.submit()">
                            <option value="">All Fuel Types</option>
                            <?php foreach (['petrol'=>'Petrol','diesel'=>'Diesel','hybrid'=>'Hybrid','electric'=>'Electric','plugin_hybrid'=>'Plug-in Hybrid','lpg'=>'LPG'] as $v=>$l): ?>
                            <option value="<?= $v ?>" <?= $filters['fuel_type'] === $v ? 'selected' : '' ?>><?= $l ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Transmission -->
                    <div class="filter-group">
                        <label class="filter-label">Transmission</label>
                        <select name="transmission" class="filter-select" onchange="this.form.submit()">
                            <option value="">All</option>
                            <?php foreach (['automatic'=>'Automatic','manual'=>'Manual','cvt'=>'CVT','semi_automatic'=>'Semi-Automatic'] as $v=>$l): ?>
                            <option value="<?= $v ?>" <?= $filters['transmission'] === $v ? 'selected' : '' ?>><?= $l ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Drivetrain -->
                    <div class="filter-group">
                        <label class="filter-label">Drivetrain</label>
                        <select name="drivetrain" class="filter-select" onchange="this.form.submit()">
                            <option value="">All</option>
                            <?php foreach (['fwd'=>'FWD','rwd'=>'RWD','awd'=>'AWD','4wd'=>'4WD'] as $v=>$l): ?>
                            <option value="<?= $v ?>" <?= $filters['drivetrain'] === $v ? 'selected' : '' ?>><?= $l ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Location -->
                    <div class="filter-group">
                        <label class="filter-label">Location</label>
                        <select name="location" class="filter-select" onchange="this.form.submit()">
                            <option value="">All Locations</option>
                            <?php foreach ($locations as $loc): ?>
                            <option value="<?= clean($loc['name']) ?>" <?= $filters['location'] === $loc['name'] ? 'selected' : '' ?>><?= clean($loc['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Special -->
                    <div class="filter-group">
                        <label class="filter-label">Special</label>
                        <div class="space-y-2">
                            <label class="form-check">
                                <input type="checkbox" name="is_featured" value="1" <?= $filters['is_featured'] ? 'checked' : '' ?> onchange="this.form.submit()">
                                Featured Cars
                            </label>
                            <label class="form-check">
                                <input type="checkbox" name="is_offer" value="1" <?= $filters['is_offer'] ? 'checked' : '' ?> onchange="this.form.submit()">
                                Special Offers
                            </label>
                            <label class="form-check">
                                <input type="checkbox" name="is_hot_deal" value="1" <?= $filters['is_hot_deal'] ? 'checked' : '' ?> onchange="this.form.submit()">
                                Hot Deals
                            </label>
                        </div>
                    </div>

                    <!-- Hidden sort -->
                    <input type="hidden" name="sort" value="<?= clean($filters['sort']) ?>">

                    <button type="submit" class="btn btn-primary w-full mt-2">
                        <i data-lucide="search" class="w-4 h-4"></i> Apply Filters
                    </button>
                </form>
            </aside>

            <!-- ============================================================ -->
            <!-- LISTINGS GRID -->
            <!-- ============================================================ -->
            <div class="flex-1 min-w-0">
                <?php if (empty($cars)): ?>
                    <?= renderEmptyState(
                        'No cars found',
                        'Try adjusting your filters or search criteria to find what you\'re looking for.',
                        'car'
                    ) ?>
                <?php else: ?>
                    <!-- Grid View -->
                    <div id="cars-grid" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
                        <?php foreach ($cars as $car): ?>
                            <?= renderCarCard($car, 'reveal') ?>
                        <?php endforeach; ?>
                    </div>

                    <!-- List View (hidden by default) -->
                    <div id="cars-list" class="hidden space-y-4">
                        <?php foreach ($cars as $car): 
                            $image = $car['primary_image'] ? resolveUrl($car['primary_image']) : BASE_URL . '/assets/images/car-placeholder.jpg';
                            $url = BASE_URL . '/car/' . $car['slug'];
                        ?>
                        <article class="car-card flex flex-col sm:flex-row overflow-hidden reveal">
                            <div class="sm:w-72 flex-shrink-0 relative">
                                <a href="<?= $url ?>">
                                    <img src="<?= $image ?>" alt="<?= clean($car['title']) ?>" loading="lazy" class="w-full h-48 sm:h-full object-cover">
                                </a>
                                <div class="car-badges">
                                    <?php if ($car['condition_type'] === 'new'): ?><span class="car-badge badge-new">New</span><?php endif; ?>
                                    <?php if ($car['condition_type'] === 'used'): ?><span class="car-badge badge-used">Used</span><?php endif; ?>
                                    <?php if ($car['is_featured']): ?><span class="car-badge badge-featured">Featured</span><?php endif; ?>
                                    <?php if ($car['is_offer']): ?><span class="car-badge badge-offer"><?= $car['offer_label'] ?: 'Offer' ?></span><?php endif; ?>
                                </div>
                            </div>
                            <div class="flex-1 p-5 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-start justify-between gap-4 mb-2">
                                        <h3 class="text-lg font-bold text-dark-900">
                                            <a href="<?= $url ?>" class="hover:text-primary-600 transition"><?= clean($car['title']) ?></a>
                                        </h3>
                                        <div class="text-right flex-shrink-0">
                                            <p class="text-lg font-bold text-primary-600"><?= formatPrice($car['price']) ?></p>
                                            <?php if ($car['old_price']): ?>
                                            <p class="text-sm text-gray-400 line-through"><?= formatPrice($car['old_price']) ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <p class="text-sm text-gray-500 mb-3 line-clamp-2"><?= clean($car['excerpt'] ?: excerpt($car['description'] ?? '', 200)) ?></p>
                                    <div class="flex items-center gap-2 text-xs text-gray-400 mb-3">
                                        <i data-lucide="map-pin" class="w-3.5 h-3.5"></i>
                                        <span><?= clean($car['location']) ?></span>
                                        <span class="mx-1">·</span>
                                        <span><?= clean($car['brand_name']) ?></span>
                                        <span class="mx-1">·</span>
                                        <span><?= clean($car['category_name']) ?></span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex flex-wrap gap-2">
                                        <span class="inline-flex items-center gap-1 text-xs font-medium text-gray-500 bg-gray-100 px-2.5 py-1 rounded-md">
                                            <i data-lucide="calendar" class="w-3 h-3"></i> <?= $car['year'] ?>
                                        </span>
                                        <span class="inline-flex items-center gap-1 text-xs font-medium text-gray-500 bg-gray-100 px-2.5 py-1 rounded-md">
                                            <i data-lucide="gauge" class="w-3 h-3"></i> <?= formatMileage($car['mileage'], $car['mileage_unit']) ?>
                                        </span>
                                        <span class="inline-flex items-center gap-1 text-xs font-medium text-gray-500 bg-gray-100 px-2.5 py-1 rounded-md">
                                            <i data-lucide="fuel" class="w-3 h-3"></i> <?= ucfirst($car['fuel_type']) ?>
                                        </span>
                                        <span class="inline-flex items-center gap-1 text-xs font-medium text-gray-500 bg-gray-100 px-2.5 py-1 rounded-md">
                                            <i data-lucide="settings-2" class="w-3 h-3"></i> <?= ucfirst($car['transmission']) ?>
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2 flex-shrink-0 ml-4">
                                        <button onclick="addToCompare(<?= $car['id'] ?>)" class="btn btn-icon btn-sm btn-ghost" title="Compare">
                                            <i data-lucide="bar-chart-2" class="w-4 h-4"></i>
                                        </button>
                                        <button onclick="toggleFavorite(<?= $car['id'] ?>, this)" class="btn btn-icon btn-sm btn-ghost" title="Save">
                                            <i data-lucide="heart" class="w-4 h-4"></i>
                                        </button>
                                        <a href="<?= $url ?>" class="btn btn-primary btn-sm">View Details</a>
                                    </div>
                                </div>
                            </div>
                        </article>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8">
                        <?= $pagination->render(BASE_URL . '/cars', $activeFilters) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Mobile Filter Overlay -->
<div id="mobile-filter-overlay" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-dark-900/50 backdrop-blur-sm" onclick="toggleFilters()"></div>
    <div class="absolute right-0 top-0 bottom-0 w-[340px] max-w-[90vw] bg-white shadow-2xl overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-gray-100 px-5 py-4 flex items-center justify-between z-10">
            <h3 class="font-bold text-dark-900 flex items-center gap-2">
                <i data-lucide="sliders-horizontal" class="w-4 h-4"></i> Filters
            </h3>
            <button onclick="toggleFilters()" class="w-9 h-9 rounded-lg flex items-center justify-center text-gray-400 hover:bg-gray-100">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="p-5" id="mobile-filter-content">
            <!-- Cloned filter form injected via JS -->
        </div>
    </div>
</div>

<?php
$pageScripts = <<<'JS'
<script>
// Sort
function applySort(val) {
    const url = new URL(window.location);
    url.searchParams.set('sort', val);
    url.searchParams.delete('page');
    window.location = url.toString();
}

// View toggle
function setView(mode) {
    const grid = document.getElementById('cars-grid');
    const list = document.getElementById('cars-list');
    const gridBtn = document.getElementById('view-grid-btn');
    const listBtn = document.getElementById('view-list-btn');
    
    if (mode === 'list') {
        grid.classList.add('hidden');
        list.classList.remove('hidden');
        gridBtn.classList.remove('active');
        listBtn.classList.add('active');
    } else {
        grid.classList.remove('hidden');
        list.classList.add('hidden');
        gridBtn.classList.add('active');
        listBtn.classList.remove('active');
    }
    localStorage.setItem('car_view', mode);
    lucide.createIcons();
}

// Restore saved view
const savedView = localStorage.getItem('car_view');
if (savedView === 'list') setView('list');

// Mobile filters
function toggleFilters() {
    const overlay = document.getElementById('mobile-filter-overlay');
    const isHidden = overlay.classList.contains('hidden');
    
    if (isHidden) {
        // Clone desktop filter form
        const desktopForm = document.getElementById('filter-form');
        const mobileContainer = document.getElementById('mobile-filter-content');
        if (desktopForm && mobileContainer.children.length === 0) {
            const clone = desktopForm.cloneNode(true);
            clone.id = 'mobile-filter-form';
            mobileContainer.appendChild(clone);
            lucide.createIcons();
        }
    }
    
    overlay.classList.toggle('hidden');
    document.body.classList.toggle('overflow-hidden');
}

// Reveal animations
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('visible');
            observer.unobserve(entry.target);
        }
    });
}, { threshold: 0.05 });
document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
</script>
<style>
.view-toggle-btn {
    padding: 8px 10px;
    color: var(--dark-400);
    transition: all 200ms;
    cursor: pointer;
    background: none;
    border: none;
}
.view-toggle-btn.active {
    color: var(--primary);
    background: var(--primary-light);
}
.view-toggle-btn:hover { color: var(--primary); }
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
JS;

include __DIR__ . '/footer.php';
?>
