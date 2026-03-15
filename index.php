<?php
/**
 * Zuri Motors - Homepage
 * SEO-optimized one-page automotive landing page
 */
require_once __DIR__ . '/functions.php';

$companyName = Settings::get('company_name', 'Zuri Motors');
$heroTitle = Settings::get('hero_title', 'Find the Perfect Car for Your Lifestyle');
$heroSubtitle = Settings::get('hero_subtitle', 'Browse thousands of quality vehicles from trusted dealers across Kenya');
$heroImage = Settings::get('hero_image', 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=1920&q=80');

// Promo images (editable in Admin > Settings > Homepage)
$promo1Image = Settings::get('promo1_image', 'https://images.unsplash.com/photo-1549317661-bd32c8ce0afa?w=900&q=80');
$promo2Image = Settings::get('promo2_image', 'https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8?w=900&q=80');
$whyUsImage = Settings::get('why_us_image', 'https://images.unsplash.com/photo-1580273916550-e323be2ae537?w=900&q=80');
$sellCtaImage = Settings::get('sell_cta_image', 'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=900&q=80');

// Fetch data
$categories = getCategories(true);
$brands = getBrands(true, true);
$featuredCars = getCars(['is_featured' => 1], 8);
$latestCars = getCars([], 8, 0, 'c.created_at DESC');
$offerCars = getCars(['is_offer' => 1], 4);
$hotDeals = getCars(['is_hot_deal' => 1], 4);
$testimonials = getTestimonials(true, 6);
$locations = getLocations(true);

$totalCars = countCars();
$totalBrands = count($brands);

$pageSeo = seoMeta(
    $companyName . ' - Buy & Sell Quality Cars in Kenya',
    'Find the best deals on used and new cars in Kenya. ' . $companyName . ' is your trusted automotive marketplace for quality vehicles.',
);

include __DIR__ . '/header.php';
?>

<!-- ============================================================ -->
<!-- HERO SECTION -->
<!-- ============================================================ -->
<section class="hero-section" id="hero">
    <img src="<?= resolveUrl($heroImage) ?>" alt="<?= clean($companyName) ?> - Premium Automotive Marketplace" class="hero-bg" loading="eager">
    
    <!-- Decorative elements -->
    <div class="absolute inset-0 z-[1]">
        <div class="absolute top-20 left-10 w-72 h-72 bg-primary-500/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 right-10 w-96 h-96 bg-primary-500/5 rounded-full blur-3xl"></div>
    </div>

    <div class="hero-content w-full">
        <div class="max-w-7xl mx-auto px-4 lg:px-6 py-20 lg:py-28">
            <div class="max-w-3xl">
                <!-- Trust badges -->
                <div class="flex flex-wrap items-center gap-4 mb-6">
                    <span class="flex items-center gap-2 text-sm text-emerald-400 font-medium">
                        <i data-lucide="check-circle" class="w-4 h-4"></i> Trusted by thousands
                    </span>
                    <span class="flex items-center gap-2 text-sm text-emerald-400 font-medium">
                        <i data-lucide="shield-check" class="w-4 h-4"></i> Verified dealers
                    </span>
                    <span class="flex items-center gap-2 text-sm text-emerald-400 font-medium">
                        <i data-lucide="headphones" class="w-4 h-4"></i> 24/7 Support
                    </span>
                </div>

                <h1 class="text-4xl lg:text-6xl font-bold text-white leading-tight mb-4 font-display">
                    <?= clean($heroTitle) ?>
                </h1>
                <p class="text-lg text-white/70 mb-8 max-w-xl leading-relaxed">
                    <?= clean($heroSubtitle) ?>
                </p>

                <!-- Stats -->
                <div class="flex flex-wrap items-center gap-8 mb-10">
                    <div>
                        <span class="text-3xl font-bold text-white"><?= number_format($totalCars) ?>+</span>
                        <p class="text-sm text-white/50">Vehicles Listed</p>
                    </div>
                    <div class="w-px h-10 bg-white/20"></div>
                    <div>
                        <span class="text-3xl font-bold text-white"><?= $totalBrands ?>+</span>
                        <p class="text-sm text-white/50">Top Brands</p>
                    </div>
                    <div class="w-px h-10 bg-white/20"></div>
                    <div>
                        <span class="text-3xl font-bold text-white">100%</span>
                        <p class="text-sm text-white/50">Verified Listings</p>
                    </div>
                </div>
            </div>

            <!-- Search Form -->
            <div class="max-w-4xl">
                <div class="hero-search-form">
                    <div class="hero-search-tabs">
                        <button class="hero-search-tab active" onclick="switchTab(this, 'all')">All Cars</button>
                        <button class="hero-search-tab" onclick="switchTab(this, 'new')">New Cars</button>
                        <button class="hero-search-tab" onclick="switchTab(this, 'used')">Used Cars</button>
                    </div>
                    <form action="<?= BASE_URL ?>/cars" method="GET" id="hero-search-form">
                        <input type="hidden" name="condition_type" id="search-condition" value="">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                            <div>
                                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1 block">Brand</label>
                                <select name="brand_id" class="filter-select">
                                    <option value="">All Brands</option>
                                    <?php foreach ($brands as $b): ?>
                                    <option value="<?= $b['id'] ?>"><?= clean($b['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1 block">Price Range</label>
                                <select name="max_price" class="filter-select">
                                    <option value="">Any Price</option>
                                    <option value="500000">Under KSh 500,000</option>
                                    <option value="1000000">Under KSh 1,000,000</option>
                                    <option value="2000000">Under KSh 2,000,000</option>
                                    <option value="3000000">Under KSh 3,000,000</option>
                                    <option value="5000000">Under KSh 5,000,000</option>
                                    <option value="10000000">Under KSh 10,000,000</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1 block">Location</label>
                                <select name="location" class="filter-select">
                                    <option value="">All Locations</option>
                                    <?php foreach ($locations as $loc): ?>
                                    <option value="<?= clean($loc['name']) ?>"><?= clean($loc['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1 block">Vehicle Type</label>
                                <select name="category_id" class="filter-select">
                                    <option value="">All Types</option>
                                    <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= clean($cat['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-3 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg flex-1">
                                <i data-lucide="search" class="w-5 h-5"></i>
                                Search Cars
                            </button>
                            <a href="<?= BASE_URL ?>/sell-your-car" class="btn btn-dark btn-lg">
                                <i data-lucide="tag" class="w-5 h-5"></i>
                                Sell Your Car
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================ -->
<!-- PREMIUM BRANDS -->
<!-- ============================================================ -->
<section class="py-16 bg-white" id="brands">
    <div class="max-w-7xl mx-auto px-4 lg:px-6">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-xl font-bold text-dark-900">Premium Brands</h2>
                <p class="text-sm text-gray-500 mt-1">Discover vehicles from the world's finest manufacturers</p>
            </div>
            <a href="<?= BASE_URL ?>/cars" class="text-sm font-semibold text-primary-600 hover:text-primary-700 flex items-center gap-1 transition">
                View All <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>
        <div class="grid grid-cols-4 sm:grid-cols-6 lg:grid-cols-12 gap-3">
            <?php foreach (array_slice(array_filter($brands, fn($b) => $b['is_popular']), 0, 12) as $b): 
                $brandImg = $b['logo'] ? (str_starts_with($b['logo'], 'http') ? $b['logo'] : BASE_URL . '/' . $b['logo']) : '';
            ?>
            <a href="<?= BASE_URL ?>/brand/<?= $b['slug'] ?>" class="brand-card">
                <?php if ($brandImg): ?>
                    <img src="<?= $brandImg ?>" alt="<?= clean($b['name']) ?>" loading="lazy" class="max-h-10 max-w-[80px] object-contain">
                <?php else: ?>
                    <span class="text-base font-bold text-dark-400"><?= strtoupper(substr($b['name'], 0, 2)) ?></span>
                <?php endif; ?>
                <span><?= clean($b['name']) ?></span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============================================================ -->
<!-- CATEGORIES -->
<!-- ============================================================ -->
<section class="py-16 bg-gray-50" id="categories">
    <div class="max-w-7xl mx-auto px-4 lg:px-6">
        <div class="section-header text-center">
            <span class="section-badge"><i data-lucide="grid-3x3" class="w-4 h-4"></i> Browse by Type</span>
            <h2 class="section-title">Explore Vehicle Categories</h2>
            <p class="section-subtitle mx-auto">Find your perfect vehicle by browsing our extensive range of categories</p>
        </div>
        <div class="grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-6 gap-4">
            <?php foreach (array_slice($categories, 0, 12) as $cat): 
                $catImg = $cat['image'] ? resolveUrl($cat['image']) : '';
            ?>
            <a href="<?= BASE_URL ?>/category/<?= $cat['slug'] ?>" class="category-card reveal">
                <div class="category-icon">
                    <?php if ($catImg): ?>
                        <img src="<?= $catImg ?>" alt="<?= clean($cat['name']) ?>" loading="lazy">
                    <?php else: ?>
                        <i data-lucide="<?= $cat['icon'] ?: 'car' ?>"></i>
                    <?php endif; ?>
                </div>
                <div>
                    <span class="category-name"><?= clean($cat['name']) ?></span>
                    <span class="category-count"><?= $cat['car_count'] ?> cars</span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============================================================ -->
<!-- FEATURED CARS -->
<!-- ============================================================ -->
<?php if (!empty($featuredCars)): ?>
<section class="py-16 bg-white" id="featured">
    <div class="max-w-7xl mx-auto px-4 lg:px-6">
        <div class="flex items-end justify-between mb-8">
            <div class="section-header mb-0">
                <span class="section-badge"><i data-lucide="star" class="w-4 h-4"></i> Hand-Picked for You</span>
                <h2 class="section-title">Featured Vehicles</h2>
                <p class="section-subtitle">Premium selections curated by our automotive experts</p>
            </div>
            <a href="<?= BASE_URL ?>/cars?is_featured=1" class="hidden sm:flex btn btn-outline btn-sm">
                View All Featured <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <?php foreach ($featuredCars as $car): ?>
                <?= renderCarCard($car, 'reveal') ?>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-8 sm:hidden">
            <a href="<?= BASE_URL ?>/cars?is_featured=1" class="btn btn-outline">View All Featured</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============================================================ -->
<!-- PROMO BANNER 1 -->
<!-- ============================================================ -->
<section class="py-8">
    <div class="max-w-7xl mx-auto px-4 lg:px-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="promo-banner">
                <img src="<?= resolveUrl($promo1Image) ?>" alt="Special Offers" loading="lazy">
                <div class="promo-content">
                    <span class="text-xs font-bold text-primary-400 uppercase tracking-wider">Limited Time</span>
                    <h3 class="text-2xl lg:text-3xl font-bold text-white mt-2 mb-3 font-display">Best Deals This Month</h3>
                    <p class="text-white/70 text-sm mb-5">Save up to 20% on selected pre-owned vehicles. Quality assured with warranty.</p>
                    <a href="<?= BASE_URL ?>/cars?is_offer=1" class="btn btn-primary">
                        View Offers <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>
            <div class="promo-banner">
                <img src="<?= resolveUrl($promo2Image) ?>" alt="Sell Your Car" loading="lazy">
                <div class="promo-content">
                    <span class="text-xs font-bold text-accent-400 uppercase tracking-wider">Sell with us</span>
                    <h3 class="text-2xl lg:text-3xl font-bold text-white mt-2 mb-3 font-display">Get Top Value for Your Car</h3>
                    <p class="text-white/70 text-sm mb-5">List your vehicle with <?= clean($companyName) ?> and reach thousands of verified buyers.</p>
                    <a href="<?= BASE_URL ?>/sell-your-car" class="btn btn-primary">
                        Start Selling <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================ -->
<!-- LATEST CARS -->
<!-- ============================================================ -->
<?php if (!empty($latestCars)): ?>
<section class="py-16 bg-white" id="latest">
    <div class="max-w-7xl mx-auto px-4 lg:px-6">
        <div class="flex items-end justify-between mb-8">
            <div class="section-header mb-0">
                <span class="section-badge"><i data-lucide="clock" class="w-4 h-4"></i> Just Arrived</span>
                <h2 class="section-title">Latest Additions</h2>
                <p class="section-subtitle">Fresh listings added to our marketplace</p>
            </div>
            <a href="<?= BASE_URL ?>/cars?sort=newest" class="hidden sm:flex btn btn-outline btn-sm">
                View All <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <?php foreach ($latestCars as $car): ?>
                <?= renderCarCard($car, 'reveal') ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============================================================ -->
<!-- HOT DEALS -->
<!-- ============================================================ -->
<?php if (!empty($hotDeals)): ?>
<section class="py-16 bg-gradient-to-br from-dark-900 to-dark-950" id="deals">
    <div class="max-w-7xl mx-auto px-4 lg:px-6">
        <div class="flex items-end justify-between mb-8">
            <div class="section-header mb-0">
                <span class="section-badge text-red-400"><i data-lucide="flame" class="w-4 h-4"></i> Don't Miss Out</span>
                <h2 class="section-title text-white">Hot Deals</h2>
                <p class="section-subtitle text-white/50">Limited-time offers on premium vehicles</p>
            </div>
            <a href="<?= BASE_URL ?>/cars?is_hot_deal=1" class="hidden sm:flex btn btn-outline border-white/20 text-white hover:bg-white/10 btn-sm">
                View All <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <?php foreach ($hotDeals as $car): ?>
                <?= renderCarCard($car) ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============================================================ -->
<!-- WHY CHOOSE US -->
<!-- ============================================================ -->
<section class="py-20 bg-white" id="why-us">
    <div class="max-w-7xl mx-auto px-4 lg:px-6">
        <div class="section-header text-center">
            <span class="section-badge"><i data-lucide="award" class="w-4 h-4"></i> Why Choose Us</span>
            <h2 class="section-title">The <?= clean($companyName) ?> Advantage</h2>
            <p class="section-subtitle mx-auto">We're committed to making your car buying experience exceptional</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="text-center p-6 rounded-2xl border border-gray-100 hover:border-primary-200 hover:shadow-lg transition-all duration-300 reveal">
                <div class="w-16 h-16 bg-primary-50 rounded-2xl flex items-center justify-center mx-auto mb-5">
                    <i data-lucide="shield-check" class="w-8 h-8 text-primary-600"></i>
                </div>
                <h3 class="text-lg font-bold text-dark-900 mb-2">Verified Listings</h3>
                <p class="text-sm text-gray-500 leading-relaxed">Every vehicle is inspected and verified by our team of automotive experts for your peace of mind.</p>
            </div>
            <div class="text-center p-6 rounded-2xl border border-gray-100 hover:border-primary-200 hover:shadow-lg transition-all duration-300 reveal stagger-1">
                <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-5">
                    <i data-lucide="wallet" class="w-8 h-8 text-blue-600"></i>
                </div>
                <h3 class="text-lg font-bold text-dark-900 mb-2">Flexible Financing</h3>
                <p class="text-sm text-gray-500 leading-relaxed">Affordable payment plans and financing options tailored to fit your budget and lifestyle.</p>
            </div>
            <div class="text-center p-6 rounded-2xl border border-gray-100 hover:border-primary-200 hover:shadow-lg transition-all duration-300 reveal stagger-2">
                <div class="w-16 h-16 bg-amber-50 rounded-2xl flex items-center justify-center mx-auto mb-5">
                    <i data-lucide="headphones" class="w-8 h-8 text-amber-600"></i>
                </div>
                <h3 class="text-lg font-bold text-dark-900 mb-2">24/7 Support</h3>
                <p class="text-sm text-gray-500 leading-relaxed">Our dedicated support team is always available to assist you through every step of the process.</p>
            </div>
            <div class="text-center p-6 rounded-2xl border border-gray-100 hover:border-primary-200 hover:shadow-lg transition-all duration-300 reveal stagger-3">
                <div class="w-16 h-16 bg-emerald-50 rounded-2xl flex items-center justify-center mx-auto mb-5">
                    <i data-lucide="refresh-cw" class="w-8 h-8 text-emerald-600"></i>
                </div>
                <h3 class="text-lg font-bold text-dark-900 mb-2">Easy Trade-In</h3>
                <p class="text-sm text-gray-500 leading-relaxed">Upgrade your ride with our hassle-free trade-in program. Get the best value for your current car.</p>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================ -->
<!-- SELL YOUR CAR CTA -->
<!-- ============================================================ -->
<section class="py-20 bg-gradient-to-br from-primary-600 to-primary-800 relative overflow-hidden">
    <div class="absolute inset-0">
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-white/5 rounded-full -translate-y-1/2 translate-x-1/3"></div>
        <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-white/5 rounded-full translate-y-1/2 -translate-x-1/3"></div>
    </div>
    <div class="max-w-7xl mx-auto px-4 lg:px-6 relative">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div>
                <span class="inline-flex items-center gap-2 text-sm font-semibold text-white/80 bg-white/10 px-4 py-1.5 rounded-full mb-6">
                    <i data-lucide="trending-up" class="w-4 h-4"></i> Sell Fast, Earn More
                </span>
                <h2 class="text-3xl lg:text-4xl font-bold text-white mb-4 font-display">Want to Sell Your Car?</h2>
                <p class="text-lg text-white/80 mb-8 leading-relaxed">
                    Get the best price for your vehicle. Our marketplace connects you with thousands of verified buyers looking for quality cars just like yours.
                </p>
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="<?= BASE_URL ?>/sell-your-car" class="btn btn-lg bg-white text-primary-700 hover:bg-gray-50 font-bold shadow-lg">
                        <i data-lucide="tag" class="w-5 h-5"></i> List Your Car Now
                    </a>
                    <a href="<?= whatsappLink('Hi, I would like to sell my car.') ?>" target="_blank" class="btn btn-lg bg-white/10 text-white hover:bg-white/20 border border-white/20">
                        <i data-lucide="message-circle" class="w-5 h-5"></i> Chat on WhatsApp
                    </a>
                </div>
            </div>
            <div class="hidden lg:grid grid-cols-2 gap-4">
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 text-white">
                    <i data-lucide="camera" class="w-8 h-8 mb-3 text-white/80"></i>
                    <h4 class="font-bold mb-1">Upload Photos</h4>
                    <p class="text-sm text-white/60">Take clear photos of your vehicle to attract more buyers.</p>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 text-white mt-8">
                    <i data-lucide="file-text" class="w-8 h-8 mb-3 text-white/80"></i>
                    <h4 class="font-bold mb-1">Add Details</h4>
                    <p class="text-sm text-white/60">Provide accurate details about your car's condition and features.</p>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 text-white">
                    <i data-lucide="users" class="w-8 h-8 mb-3 text-white/80"></i>
                    <h4 class="font-bold mb-1">Get Offers</h4>
                    <p class="text-sm text-white/60">Receive inquiries from interested and verified buyers.</p>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 text-white mt-8">
                    <i data-lucide="banknote" class="w-8 h-8 mb-3 text-white/80"></i>
                    <h4 class="font-bold mb-1">Get Paid</h4>
                    <p class="text-sm text-white/60">Close the deal and get paid securely through our platform.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================ -->
<!-- TESTIMONIALS -->
<!-- ============================================================ -->
<?php if (!empty($testimonials)): ?>
<section class="py-20 bg-gray-50" id="testimonials">
    <div class="max-w-7xl mx-auto px-4 lg:px-6">
        <div class="section-header text-center">
            <span class="section-badge"><i data-lucide="message-square" class="w-4 h-4"></i> Testimonials</span>
            <h2 class="section-title">What Our Customers Say</h2>
            <p class="section-subtitle mx-auto">Real experiences from real customers who found their perfect vehicles with us</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($testimonials as $t): ?>
            <div class="testimonial-card reveal">
                <div class="testimonial-stars">
                    <?php for ($i = 0; $i < 5; $i++): ?>
                    <i data-lucide="star" class="w-4 h-4 <?= $i < $t['rating'] ? 'fill-amber-400' : 'text-gray-300' ?>"></i>
                    <?php endfor; ?>
                </div>
                <p class="testimonial-text">"<?= clean($t['content']) ?>"</p>
                <div class="testimonial-author">
                    <?php if ($t['avatar']): ?>
                    <img src="<?= BASE_URL ?>/<?= $t['avatar'] ?>" alt="<?= clean($t['name']) ?>" class="testimonial-avatar" loading="lazy">
                    <?php else: ?>
                    <div class="testimonial-avatar flex items-center justify-center bg-primary-100 text-primary-700 font-bold text-sm">
                        <?= strtoupper(substr($t['name'], 0, 2)) ?>
                    </div>
                    <?php endif; ?>
                    <div>
                        <p class="text-sm font-semibold text-dark-900"><?= clean($t['name']) ?></p>
                        <?php if ($t['role']): ?>
                        <p class="text-xs text-gray-500"><?= clean($t['role']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============================================================ -->
<!-- FINANCING CALCULATOR -->
<!-- ============================================================ -->
<?php if (Settings::get('enable_financing', '1') === '1'): ?>
<section class="py-20 bg-white" id="calculator">
    <div class="max-w-7xl mx-auto px-4 lg:px-6">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div>
                <div class="section-header">
                    <span class="section-badge"><i data-lucide="calculator" class="w-4 h-4"></i> Financing</span>
                    <h2 class="section-title">Calculate Your Payment</h2>
                    <p class="section-subtitle">Use our calculator to estimate your monthly payments and plan your budget</p>
                </div>
                <div class="space-y-5">
                    <div>
                        <label class="form-label">Vehicle Price (KSh)</label>
                        <input type="number" id="calc-price" value="2500000" min="100000" step="50000" class="form-control" onchange="calculatePayment()">
                    </div>
                    <div>
                        <label class="form-label">Down Payment (KSh)</label>
                        <input type="number" id="calc-deposit" value="500000" min="0" step="50000" class="form-control" onchange="calculatePayment()">
                    </div>
                    <div>
                        <label class="form-label">Loan Period (Months)</label>
                        <select id="calc-months" class="form-control" onchange="calculatePayment()">
                            <option value="12">12 Months</option>
                            <option value="24">24 Months</option>
                            <option value="36" selected>36 Months</option>
                            <option value="48">48 Months</option>
                            <option value="60">60 Months</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Interest Rate (% per year)</label>
                        <input type="number" id="calc-rate" value="<?= Settings::get('interest_rate', '14') ?>" min="0" max="50" step="0.5" class="form-control" onchange="calculatePayment()">
                    </div>
                </div>
            </div>
            <div>
                <div class="finance-card text-center">
                    <p class="text-sm text-white/60 mb-2">Estimated Monthly Payment</p>
                    <p class="text-5xl font-bold text-white mb-1" id="calc-monthly">KSh 0</p>
                    <p class="text-sm text-white/40 mb-8">per month</p>
                    <div class="grid grid-cols-2 gap-4 text-left">
                        <div class="bg-white/10 rounded-xl p-4">
                            <p class="text-xs text-white/50">Vehicle Price</p>
                            <p class="text-lg font-bold text-white" id="calc-display-price">KSh 2,500,000</p>
                        </div>
                        <div class="bg-white/10 rounded-xl p-4">
                            <p class="text-xs text-white/50">Down Payment</p>
                            <p class="text-lg font-bold text-white" id="calc-display-deposit">KSh 500,000</p>
                        </div>
                        <div class="bg-white/10 rounded-xl p-4">
                            <p class="text-xs text-white/50">Loan Amount</p>
                            <p class="text-lg font-bold text-white" id="calc-display-loan">KSh 2,000,000</p>
                        </div>
                        <div class="bg-white/10 rounded-xl p-4">
                            <p class="text-xs text-white/50">Total Interest</p>
                            <p class="text-lg font-bold text-white" id="calc-display-interest">KSh 0</p>
                        </div>
                    </div>
                    <a href="<?= BASE_URL ?>/contact" class="btn bg-white text-dark-900 hover:bg-gray-100 w-full mt-6 font-bold">
                        Apply for Financing
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<script>
// Hero search tabs
function switchTab(el, type) {
    document.querySelectorAll('.hero-search-tab').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
    const conditionInput = document.getElementById('search-condition');
    conditionInput.value = type === 'all' ? '' : type;
}

// Financing calculator
function calculatePayment() {
    const price = parseFloat(document.getElementById('calc-price').value) || 0;
    const deposit = parseFloat(document.getElementById('calc-deposit').value) || 0;
    const months = parseInt(document.getElementById('calc-months').value) || 36;
    const annualRate = parseFloat(document.getElementById('calc-rate').value) || 14;
    
    const loan = Math.max(0, price - deposit);
    const monthlyRate = annualRate / 100 / 12;
    
    let monthly = 0;
    let totalInterest = 0;
    
    if (monthlyRate > 0 && months > 0 && loan > 0) {
        monthly = loan * (monthlyRate * Math.pow(1 + monthlyRate, months)) / (Math.pow(1 + monthlyRate, months) - 1);
        totalInterest = (monthly * months) - loan;
    } else if (months > 0) {
        monthly = loan / months;
    }
    
    const fmt = (n) => 'KSh ' + Math.round(n).toLocaleString();
    document.getElementById('calc-monthly').textContent = fmt(monthly);
    document.getElementById('calc-display-price').textContent = fmt(price);
    document.getElementById('calc-display-deposit').textContent = fmt(deposit);
    document.getElementById('calc-display-loan').textContent = fmt(loan);
    document.getElementById('calc-display-interest').textContent = fmt(totalInterest);
}

// Run on load
calculatePayment();

// Reveal animations on scroll
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('visible');
            observer.unobserve(entry.target);
        }
    });
}, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
</script>

<?php include __DIR__ . '/footer.php'; ?>
