<?php
/**
 * Zuri Motors - Car Detail Page
 * Full car listing with gallery, specs, features, financing, contact
 */
require_once __DIR__ . '/functions.php';

$slug = $_GET['slug'] ?? '';
if (!$slug) { header('Location: ' . BASE_URL . '/cars'); exit; }

$car = getCar($slug);
if (!$car || ($car['status'] !== 'active' && !Auth::isAdmin())) {
    http_response_code(404);
    include __DIR__ . '/404.php';
    exit;
}

// Increment views
incrementCarViews($car['id']);

// Fetch related data
$images = getCarImages($car['id']);
$features = getCarFeatures($car['id']);
$similarCars = getSimilarCars($car, 4);
$isFav = Auth::check() ? isFavorite($car['id'], Auth::id()) : false;
$compareIds = getCompareCarIds();
$inCompare = in_array($car['id'], $compareIds);

$companyName = Settings::get('company_name', 'Zuri Motors');
$whatsapp = $car['seller_whatsapp'] ?: Settings::get('whatsapp', '254700000000');
$sellerPhone = $car['seller_phone'] ?: Settings::get('phone');
$sellerName = trim(($car['seller_first_name'] ?? '') . ' ' . ($car['seller_last_name'] ?? ''));
if ($car['seller_company']) $sellerName = $car['seller_company'];

// Group features by category
$featuresGrouped = [];
foreach ($features as $f) {
    $featuresGrouped[$f['feature_category']][] = $f;
}

// Financing calc
$enableFinancing = Settings::get('enable_financing', '1') === '1';
$depositPercent = (float)Settings::get('default_deposit_percent', 20);
$interestRate = (float)Settings::get('interest_rate', 14);
$maxMonths = (int)Settings::get('max_installment_months', 60);
$depositAmount = $car['deposit_amount'] ?: round($car['price'] * $depositPercent / 100);
$loanAmount = $car['price'] - $depositAmount;
$defaultMonths = $car['installment_months'] ?: 36;
$monthlyRate = $interestRate / 100 / 12;
$monthlyPayment = $car['monthly_installment'] ?: ($monthlyRate > 0 ? $loanAmount * ($monthlyRate * pow(1 + $monthlyRate, $defaultMonths)) / (pow(1 + $monthlyRate, $defaultMonths) - 1) : $loanAmount / $defaultMonths);

// Primary image
$primaryImage = '';
foreach ($images as $img) {
    if ($img['is_primary']) { $primaryImage = $img['image_url']; break; }
}
if (!$primaryImage && !empty($images)) $primaryImage = $images[0]['image_url'];
$primaryImageUrl = $primaryImage ? resolveUrl($primaryImage) : BASE_URL . '/assets/images/car-placeholder.jpg';

// SEO
$pageSeo = seoMeta(
    ($car['meta_title'] ?: $car['title'] . ' - ' . $companyName),
    ($car['meta_description'] ?: excerpt(strip_tags($car['description'] ?? ''), 160)),
    $primaryImageUrl,
    BASE_URL . '/car/' . $car['slug']
);

include __DIR__ . '/header.php';
?>

<!-- Structured Data -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Car",
    "name": "<?= clean($car['title']) ?>",
    "brand": {"@type": "Brand", "name": "<?= clean($car['brand_name']) ?>"},
    "model": "<?= clean($car['model']) ?>",
    "vehicleModelDate": "<?= $car['year'] ?>",
    "mileageFromOdometer": {"@type": "QuantitativeValue", "value": "<?= $car['mileage'] ?>", "unitCode": "<?= $car['mileage_unit'] === 'km' ? 'KMT' : 'SMI' ?>"},
    "fuelType": "<?= ucfirst($car['fuel_type']) ?>",
    "vehicleTransmission": "<?= ucfirst($car['transmission']) ?>",
    "color": "<?= clean($car['color'] ?? '') ?>",
    "offers": {
        "@type": "Offer",
        "price": "<?= $car['price'] ?>",
        "priceCurrency": "<?= Settings::get('currency_code', 'KES') ?>",
        "availability": "https://schema.org/InStock",
        "itemCondition": "<?= $car['condition_type'] === 'new' ? 'https://schema.org/NewCondition' : 'https://schema.org/UsedCondition' ?>"
    },
    "image": "<?= $primaryImageUrl ?>"
}
</script>

<!-- Breadcrumb -->
<div class="bg-gray-50 border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 lg:px-6 py-4">
        <?= breadcrumbs([
            ['label' => 'Home', 'url' => BASE_URL . '/'],
            ['label' => 'Cars', 'url' => BASE_URL . '/cars'],
            ['label' => $car['brand_name'], 'url' => BASE_URL . '/brand/' . $car['brand_slug']],
            ['label' => $car['title']]
        ]) ?>
    </div>
</div>

<section class="py-8 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 lg:px-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- ============================================================ -->
            <!-- LEFT COLUMN - Gallery + Details -->
            <!-- ============================================================ -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Gallery -->
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                    <!-- Main Image -->
                    <div class="gallery-main" id="gallery-main">
                        <img src="<?= $primaryImageUrl ?>" alt="<?= clean($car['title']) ?>" id="main-image" loading="eager">
                        
                        <!-- Nav Arrows -->
                        <?php if (count($images) > 1): ?>
                        <button type="button" class="gallery-nav prev" onclick="galleryNav(-1)">
                            <i data-lucide="chevron-left" class="w-5 h-5"></i>
                        </button>
                        <button type="button" class="gallery-nav next" onclick="galleryNav(1)">
                            <i data-lucide="chevron-right" class="w-5 h-5"></i>
                        </button>
                        <?php endif; ?>

                        <!-- Badges -->
                        <div class="absolute top-4 left-4 flex flex-wrap gap-2 z-10">
                            <?php if ($car['condition_type'] === 'new'): ?><span class="car-badge badge-new">New</span><?php endif; ?>
                            <?php if ($car['condition_type'] === 'used'): ?><span class="car-badge badge-used">Used</span><?php endif; ?>
                            <?php if ($car['condition_type'] === 'certified'): ?><span class="car-badge badge-certified">Certified</span><?php endif; ?>
                            <?php if ($car['is_featured']): ?><span class="car-badge badge-featured">Featured</span><?php endif; ?>
                            <?php if ($car['is_offer']): ?><span class="car-badge badge-offer"><?= $car['offer_label'] ?: 'Special Offer' ?></span><?php endif; ?>
                            <?php if ($car['is_hot_deal']): ?><span class="car-badge badge-hot">Hot Deal</span><?php endif; ?>
                        </div>

                        <!-- Image Counter -->
                        <div class="absolute bottom-4 right-4 bg-dark-900/70 backdrop-blur-sm text-white text-xs font-medium px-3 py-1.5 rounded-lg z-10">
                            <span id="gallery-counter">1</span> / <?= count($images) ?: 1 ?>
                        </div>

                        <!-- See All Photos -->
                        <?php if (count($images) > 1): ?>
                        <button type="button" onclick="openLightbox(0)" class="absolute bottom-4 left-4 bg-dark-900/70 backdrop-blur-sm text-white text-xs font-medium px-3 py-1.5 rounded-lg z-10 flex items-center gap-1.5 hover:bg-dark-900/90 transition">
                            <i data-lucide="grid-2x2" class="w-3.5 h-3.5"></i> See All Photos
                        </button>
                        <?php endif; ?>
                    </div>

                    <!-- Thumbnails -->
                    <?php if (count($images) > 1): ?>
                    <div class="gallery-thumbs px-4 py-3">
                        <?php foreach ($images as $i => $img): ?>
                        <div class="gallery-thumb <?= $i === 0 ? 'active' : '' ?>" onclick="setGalleryImage(<?= $i ?>); openLightbox(<?= $i ?>);" role="button" tabindex="0">
                            <img src="<?= resolveUrl($img['thumbnail_url'] ?: $img['image_url']) ?>" 
                                 alt="<?= clean($img['alt_text'] ?: $car['title'] . ' photo ' . ($i+1)) ?>" loading="lazy">
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Title & Quick Info (Mobile) -->
                <div class="lg:hidden bg-white rounded-2xl border border-gray-200 p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h1 class="text-xl font-bold text-dark-900"><?= clean($car['title']) ?></h1>
                            <div class="flex items-center gap-2 mt-1 text-sm text-gray-500">
                                <i data-lucide="map-pin" class="w-3.5 h-3.5"></i>
                                <span><?= clean($car['location']) ?></span>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xl font-bold text-primary-600"><?= formatPrice($car['price']) ?></p>
                            <?php if ($car['old_price']): ?>
                            <p class="text-sm text-gray-400 line-through"><?= formatPrice($car['old_price']) ?></p>
                            <?php endif; ?>
                            <?php if ($car['price_negotiable']): ?>
                            <span class="text-xs text-amber-600 font-medium">Negotiable</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Key Specifications -->
                <div class="bg-white rounded-2xl border border-gray-200 p-5 lg:p-6">
                    <h2 class="text-lg font-bold text-dark-900 mb-5 flex items-center gap-2">
                        <i data-lucide="info" class="w-5 h-5 text-primary-600"></i> Key Specifications
                    </h2>
                    <div class="spec-grid">
                        <div class="spec-item">
                            <div class="spec-icon"><i data-lucide="calendar" class="w-4 h-4"></i></div>
                            <div><span class="spec-label">Year</span><span class="spec-value"><?= $car['year'] ?></span></div>
                        </div>
                        <div class="spec-item">
                            <div class="spec-icon"><i data-lucide="gauge" class="w-4 h-4"></i></div>
                            <div><span class="spec-label">Mileage</span><span class="spec-value"><?= formatMileage($car['mileage'], $car['mileage_unit']) ?></span></div>
                        </div>
                        <div class="spec-item">
                            <div class="spec-icon"><i data-lucide="fuel" class="w-4 h-4"></i></div>
                            <div><span class="spec-label">Fuel Type</span><span class="spec-value"><?= ucfirst($car['fuel_type']) ?></span></div>
                        </div>
                        <div class="spec-item">
                            <div class="spec-icon"><i data-lucide="settings-2" class="w-4 h-4"></i></div>
                            <div><span class="spec-label">Transmission</span><span class="spec-value"><?= ucfirst(str_replace('_',' ',$car['transmission'])) ?></span></div>
                        </div>
                        <?php if ($car['engine_size']): ?>
                        <div class="spec-item">
                            <div class="spec-icon"><i data-lucide="zap" class="w-4 h-4"></i></div>
                            <div><span class="spec-label">Engine</span><span class="spec-value"><?= clean($car['engine_size']) ?></span></div>
                        </div>
                        <?php endif; ?>
                        <?php if ($car['horsepower']): ?>
                        <div class="spec-item">
                            <div class="spec-icon"><i data-lucide="activity" class="w-4 h-4"></i></div>
                            <div><span class="spec-label">Horsepower</span><span class="spec-value"><?= $car['horsepower'] ?> hp</span></div>
                        </div>
                        <?php endif; ?>
                        <div class="spec-item">
                            <div class="spec-icon"><i data-lucide="car" class="w-4 h-4"></i></div>
                            <div><span class="spec-label">Body Type</span><span class="spec-value"><?= clean($car['category_name']) ?></span></div>
                        </div>
                        <?php if ($car['color']): ?>
                        <div class="spec-item">
                            <div class="spec-icon"><i data-lucide="palette" class="w-4 h-4"></i></div>
                            <div><span class="spec-label">Color</span><span class="spec-value"><?= clean($car['color']) ?></span></div>
                        </div>
                        <?php endif; ?>
                        <?php if ($car['interior_color']): ?>
                        <div class="spec-item">
                            <div class="spec-icon"><i data-lucide="armchair" class="w-4 h-4"></i></div>
                            <div><span class="spec-label">Interior</span><span class="spec-value"><?= clean($car['interior_color']) ?></span></div>
                        </div>
                        <?php endif; ?>
                        <div class="spec-item">
                            <div class="spec-icon"><i data-lucide="door-open" class="w-4 h-4"></i></div>
                            <div><span class="spec-label">Doors</span><span class="spec-value"><?= $car['doors'] ?></span></div>
                        </div>
                        <div class="spec-item">
                            <div class="spec-icon"><i data-lucide="users" class="w-4 h-4"></i></div>
                            <div><span class="spec-label">Seats</span><span class="spec-value"><?= $car['seats'] ?></span></div>
                        </div>
                        <?php if ($car['drivetrain']): ?>
                        <div class="spec-item">
                            <div class="spec-icon"><i data-lucide="settings" class="w-4 h-4"></i></div>
                            <div><span class="spec-label">Drivetrain</span><span class="spec-value"><?= strtoupper($car['drivetrain']) ?></span></div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Description -->
                <?php if ($car['description']): ?>
                <div class="bg-white rounded-2xl border border-gray-200 p-5 lg:p-6">
                    <h2 class="text-lg font-bold text-dark-900 mb-4 flex items-center gap-2">
                        <i data-lucide="file-text" class="w-5 h-5 text-primary-600"></i> Overview
                    </h2>
                    <div class="prose prose-sm max-w-none text-gray-600 leading-relaxed" id="description-content">
                        <?= nl2br(clean($car['description'])) ?>
                    </div>
                    <button onclick="toggleDescription()" id="desc-toggle" class="hidden mt-3 text-sm font-semibold text-primary-600 hover:text-primary-700 transition flex items-center gap-1">
                        Read More <i data-lucide="chevron-down" class="w-4 h-4"></i>
                    </button>
                </div>
                <?php endif; ?>

                <!-- Features -->
                <?php if (!empty($features)): ?>
                <div class="bg-white rounded-2xl border border-gray-200 p-5 lg:p-6">
                    <h2 class="text-lg font-bold text-dark-900 mb-5 flex items-center gap-2">
                        <i data-lucide="check-circle" class="w-5 h-5 text-primary-600"></i> Features & Equipment
                    </h2>
                    <?php foreach ($featuresGrouped as $category => $feats): ?>
                    <div class="mb-5 last:mb-0">
                        <h3 class="text-sm font-bold text-dark-500 uppercase tracking-wide mb-3"><?= ucfirst($category) ?></h3>
                        <div class="flex flex-wrap gap-2">
                            <?php foreach ($feats as $f): ?>
                            <span class="feature-pill">
                                <i data-lucide="<?= $f['icon'] ?: 'check' ?>" class="w-4 h-4"></i>
                                <?= clean($f['name']) ?>
                                <?php if ($f['custom_value']): ?>
                                <span class="text-xs text-gray-400">(<?= clean($f['custom_value']) ?>)</span>
                                <?php endif; ?>
                            </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Financing -->
                <?php if ($enableFinancing): ?>
                <div class="bg-white rounded-2xl border border-gray-200 p-5 lg:p-6">
                    <h2 class="text-lg font-bold text-dark-900 mb-5 flex items-center gap-2">
                        <i data-lucide="calculator" class="w-5 h-5 text-primary-600"></i> Financing Options
                    </h2>
                    <div class="finance-card">
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                            <div class="text-center">
                                <p class="text-xs text-white/50 mb-1">Vehicle Price</p>
                                <p class="text-lg font-bold text-white"><?= formatPrice($car['price']) ?></p>
                            </div>
                            <div class="text-center">
                                <p class="text-xs text-white/50 mb-1">Down Payment</p>
                                <p class="text-lg font-bold text-primary-400"><?= formatPrice($depositAmount) ?></p>
                            </div>
                            <div class="text-center">
                                <p class="text-xs text-white/50 mb-1">Monthly Payment</p>
                                <p class="text-lg font-bold text-white"><?= formatPrice(round($monthlyPayment)) ?></p>
                            </div>
                            <div class="text-center">
                                <p class="text-xs text-white/50 mb-1">Duration</p>
                                <p class="text-lg font-bold text-white"><?= $defaultMonths ?> months</p>
                            </div>
                        </div>
                        <p class="text-xs text-white/40 text-center">*Estimated calculation based on <?= $interestRate ?>% annual interest rate. Actual terms may vary.</p>
                        <div class="flex gap-3 mt-4">
                            <a href="<?= BASE_URL ?>/contact?subject=Financing+for+<?= urlencode($car['title']) ?>" class="btn bg-white text-dark-900 hover:bg-gray-100 flex-1 text-sm">
                                Apply for Financing
                            </a>
                            <a href="<?= whatsappLink('Hi, I\'m interested in financing for ' . $car['title'] . ' (' . formatPrice($car['price']) . '). Can you share the available options?', $whatsapp) ?>" 
                               target="_blank" class="btn bg-white/10 text-white hover:bg-white/20 border border-white/20 flex-1 text-sm">
                                Ask via WhatsApp
                            </a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Location Map Placeholder -->
                <?php if ($car['location']): ?>
                <div class="bg-white rounded-2xl border border-gray-200 p-5 lg:p-6">
                    <h2 class="text-lg font-bold text-dark-900 mb-4 flex items-center gap-2">
                        <i data-lucide="map-pin" class="w-5 h-5 text-primary-600"></i> Location
                    </h2>
                    <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-xl">
                        <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i data-lucide="map-pin" class="w-5 h-5 text-primary-600"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-dark-900"><?= clean($car['location']) ?></p>
                            <p class="text-sm text-gray-500">Vehicle is available for viewing at this location</p>
                        </div>
                        <a href="https://maps.google.com/?q=<?= urlencode($car['location']) ?>" target="_blank" 
                           class="ml-auto btn btn-outline btn-sm flex-shrink-0">
                            <i data-lucide="external-link" class="w-3.5 h-3.5"></i> View Map
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- ============================================================ -->
            <!-- RIGHT COLUMN - Price, Contact, Seller -->
            <!-- ============================================================ -->
            <div class="space-y-5">
                
                <!-- Price Card (Desktop) -->
                <div class="hidden lg:block bg-white rounded-2xl border border-gray-200 p-6 sticky top-24">
                    <div class="mb-5">
                        <h1 class="text-xl font-bold text-dark-900 mb-1"><?= clean($car['title']) ?></h1>
                        <div class="flex items-center gap-2 text-sm text-gray-500">
                            <i data-lucide="map-pin" class="w-3.5 h-3.5"></i>
                            <span><?= clean($car['location']) ?></span>
                        </div>
                    </div>

                    <div class="mb-5 pb-5 border-b border-gray-100">
                        <p class="text-3xl font-bold text-primary-600"><?= formatPrice($car['price']) ?></p>
                        <?php if ($car['old_price']): ?>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-sm text-gray-400 line-through"><?= formatPrice($car['old_price']) ?></span>
                            <?php 
                            $savings = $car['old_price'] - $car['price'];
                            $savingsPercent = round(($savings / $car['old_price']) * 100);
                            ?>
                            <span class="text-xs font-bold text-red-600 bg-red-50 px-2 py-0.5 rounded">Save <?= $savingsPercent ?>%</span>
                        </div>
                        <?php endif; ?>
                        <?php if ($car['price_negotiable']): ?>
                        <span class="inline-flex items-center gap-1 text-xs font-medium text-amber-600 mt-1">
                            <i data-lucide="message-circle" class="w-3 h-3"></i> Price Negotiable
                        </span>
                        <?php endif; ?>
                        <?php if ($enableFinancing): ?>
                        <p class="text-sm text-gray-500 mt-2">
                            From <span class="font-semibold text-dark-700"><?= formatPrice(round($monthlyPayment)) ?></span>/month
                        </p>
                        <?php endif; ?>
                    </div>

                    <!-- Quick Specs -->
                    <div class="grid grid-cols-2 gap-2 mb-5">
                        <div class="text-center bg-gray-50 rounded-lg py-2.5">
                            <p class="text-[11px] text-gray-400 font-medium">Year</p>
                            <p class="text-sm font-bold text-dark-800"><?= $car['year'] ?></p>
                        </div>
                        <div class="text-center bg-gray-50 rounded-lg py-2.5">
                            <p class="text-[11px] text-gray-400 font-medium">Mileage</p>
                            <p class="text-sm font-bold text-dark-800"><?= formatMileage($car['mileage'], $car['mileage_unit']) ?></p>
                        </div>
                        <div class="text-center bg-gray-50 rounded-lg py-2.5">
                            <p class="text-[11px] text-gray-400 font-medium">Fuel</p>
                            <p class="text-sm font-bold text-dark-800"><?= ucfirst($car['fuel_type']) ?></p>
                        </div>
                        <div class="text-center bg-gray-50 rounded-lg py-2.5">
                            <p class="text-[11px] text-gray-400 font-medium">Transmission</p>
                            <p class="text-sm font-bold text-dark-800"><?= ucfirst($car['transmission']) ?></p>
                        </div>
                    </div>

                    <!-- Contact Buttons -->
                    <div class="space-y-2.5 mb-5">
                        <a href="tel:<?= str_replace(' ', '', $sellerPhone) ?>" class="contact-btn contact-btn-call">
                            <i data-lucide="phone" class="w-4 h-4"></i> Call Dealer
                        </a>
                        <a href="<?= whatsappLink('Hi, I\'m interested in this vehicle: ' . $car['title'] . ' (' . formatPrice($car['price']) . '). Is it still available? ' . BASE_URL . '/car/' . $car['slug'], $whatsapp) ?>" 
                           target="_blank" class="contact-btn contact-btn-whatsapp">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            WhatsApp Dealer
                        </a>
                        <button onclick="openInquiryModal()" class="contact-btn contact-btn-outline">
                            <i data-lucide="mail" class="w-4 h-4"></i> Request Test Drive
                        </button>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-2 pt-4 border-t border-gray-100">
                        <button onclick="toggleFavorite(<?= $car['id'] ?>, this)" class="btn btn-ghost btn-sm flex-1 <?= $isFav ? 'text-red-500' : '' ?>" data-fav="<?= $isFav ? '1' : '0' ?>">
                            <i data-lucide="heart" class="w-4 h-4"></i> <?= $isFav ? 'Saved' : 'Save' ?>
                        </button>
                        <button onclick="addToCompare(<?= $car['id'] ?>)" class="btn btn-ghost btn-sm flex-1 <?= $inCompare ? 'text-primary-600' : '' ?>">
                            <i data-lucide="bar-chart-2" class="w-4 h-4"></i> Compare
                        </button>
                        <button onclick="shareCar()" class="btn btn-ghost btn-sm flex-1">
                            <i data-lucide="share-2" class="w-4 h-4"></i> Share
                        </button>
                        <button onclick="window.print()" class="btn btn-ghost btn-sm flex-1">
                            <i data-lucide="printer" class="w-4 h-4"></i> Print
                        </button>
                    </div>
                </div>

                <!-- Seller Info -->
                <div class="bg-white rounded-2xl border border-gray-200 p-5">
                    <h3 class="font-bold text-dark-900 mb-4 flex items-center gap-2">
                        <i data-lucide="store" class="w-4 h-4 text-primary-600"></i> Seller Information
                    </h3>
                    <div class="flex items-center gap-3 mb-4">
                        <?php if ($car['seller_avatar']): ?>
                        <img src="<?= resolveUrl($car['seller_avatar']) ?>" alt="<?= clean($sellerName) ?>" class="w-12 h-12 rounded-xl object-cover">
                        <?php else: ?>
                        <div class="w-12 h-12 rounded-xl bg-primary-100 flex items-center justify-center text-primary-700 font-bold">
                            <?= strtoupper(substr($car['seller_first_name'] ?? 'Z', 0, 1)) ?>
                        </div>
                        <?php endif; ?>
                        <div>
                            <p class="font-semibold text-dark-900"><?= clean($sellerName) ?></p>
                            <p class="text-xs text-gray-500">Verified Dealer</p>
                        </div>
                    </div>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center gap-2 text-gray-600">
                            <i data-lucide="map-pin" class="w-4 h-4 text-gray-400"></i>
                            <?= clean($car['location']) ?>
                        </div>
                        <div class="flex items-center gap-2 text-gray-600">
                            <i data-lucide="clock" class="w-4 h-4 text-gray-400"></i>
                            Listed <?= timeAgo($car['created_at']) ?>
                        </div>
                        <div class="flex items-center gap-2 text-gray-600">
                            <i data-lucide="eye" class="w-4 h-4 text-gray-400"></i>
                            <?= number_format($car['views_count']) ?> views
                        </div>
                    </div>
                </div>

                <!-- Safety Tips -->
                <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5">
                    <h3 class="font-bold text-amber-800 mb-3 flex items-center gap-2 text-sm">
                        <i data-lucide="shield-alert" class="w-4 h-4"></i> Safety Tips
                    </h3>
                    <ul class="text-xs text-amber-700 space-y-2">
                        <li class="flex items-start gap-2"><i data-lucide="check" class="w-3 h-3 mt-0.5 flex-shrink-0"></i> Meet the seller in a safe, public location</li>
                        <li class="flex items-start gap-2"><i data-lucide="check" class="w-3 h-3 mt-0.5 flex-shrink-0"></i> Inspect the vehicle thoroughly before payment</li>
                        <li class="flex items-start gap-2"><i data-lucide="check" class="w-3 h-3 mt-0.5 flex-shrink-0"></i> Verify vehicle documents and ownership</li>
                        <li class="flex items-start gap-2"><i data-lucide="check" class="w-3 h-3 mt-0.5 flex-shrink-0"></i> Never send payment before seeing the vehicle</li>
                        <li class="flex items-start gap-2"><i data-lucide="check" class="w-3 h-3 mt-0.5 flex-shrink-0"></i> Use <?= clean($companyName) ?> secure payment when possible</li>
                    </ul>
                </div>

                <!-- Mobile Contact Buttons (fixed bottom on mobile) -->
                <div class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 p-3 z-40 flex gap-2">
                    <a href="tel:<?= str_replace(' ', '', $sellerPhone) ?>" class="contact-btn contact-btn-call flex-1 py-3">
                        <i data-lucide="phone" class="w-4 h-4"></i> Call
                    </a>
                    <a href="<?= whatsappLink('Hi, I\'m interested in ' . $car['title'] . '. Is it still available?', $whatsapp) ?>" target="_blank" class="contact-btn contact-btn-whatsapp flex-1 py-3">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
                        WhatsApp
                    </a>
                    <button onclick="openInquiryModal()" class="contact-btn contact-btn-outline flex-1 py-3 text-xs">
                        <i data-lucide="mail" class="w-4 h-4"></i> Inquiry
                    </button>
                </div>
            </div>
        </div>

        <!-- ============================================================ -->
        <!-- SIMILAR CARS -->
        <!-- ============================================================ -->
        <?php if (!empty($similarCars)): ?>
        <div class="mt-12">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-dark-900 font-display">Similar Vehicles</h2>
                <a href="<?= BASE_URL ?>/brand/<?= $car['brand_slug'] ?>" class="text-sm font-semibold text-primary-600 hover:text-primary-700 flex items-center gap-1 transition">
                    View More <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <?php foreach ($similarCars as $sim): ?>
                    <?= renderCarCard($sim) ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- ============================================================ -->
<!-- INQUIRY MODAL -->
<!-- ============================================================ -->
<div id="inquiry-modal" class="modal-overlay" onclick="if(event.target===this) closeInquiryModal()">
    <div class="modal-content w-full max-w-lg p-6 lg:p-8 m-4">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-dark-900">Send Inquiry</h3>
            <button onclick="closeInquiryModal()" class="w-9 h-9 rounded-lg flex items-center justify-center text-gray-400 hover:bg-gray-100 transition">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl mb-5">
            <img src="<?= $primaryImageUrl ?>" alt="" class="w-16 h-12 object-cover rounded-lg">
            <div>
                <p class="text-sm font-semibold text-dark-900"><?= clean($car['title']) ?></p>
                <p class="text-sm font-bold text-primary-600"><?= formatPrice($car['price']) ?></p>
            </div>
        </div>
        <form id="inquiry-form" onsubmit="submitInquiry(event)">
            <input type="hidden" name="car_id" value="<?= $car['id'] ?>">
            <input type="hidden" name="type" value="general">
            <?= CSRF::field() ?>
            <div class="space-y-4">
                <div>
                    <label class="form-label">Your Name <span class="required">*</span></label>
                    <input type="text" name="name" required class="form-control" placeholder="Full name" value="<?= Auth::check() ? clean(Auth::user()['first_name'] . ' ' . Auth::user()['last_name']) : '' ?>">
                </div>
                <div>
                    <label class="form-label">Phone Number <span class="required">*</span></label>
                    <input type="tel" name="phone" required class="form-control" placeholder="+254 700 000 000" value="<?= Auth::check() ? clean(Auth::user()['phone'] ?? '') : '' ?>">
                </div>
                <div>
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="your@email.com" value="<?= Auth::check() ? clean(Auth::user()['email'] ?? '') : '' ?>">
                </div>
                <div>
                    <label class="form-label">Inquiry Type</label>
                    <select name="type" class="form-control">
                        <option value="general">General Inquiry</option>
                        <option value="test_drive">Request Test Drive</option>
                        <option value="price_quote">Price Quote</option>
                        <option value="financing">Financing Inquiry</option>
                        <option value="callback">Request Callback</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Message</label>
                    <textarea name="message" class="form-control" rows="3" placeholder="I'm interested in this vehicle. Is it still available?"><?= 'Hi, I\'m interested in ' . clean($car['title']) . ' listed at ' . formatPrice($car['price']) . '. Is it still available?' ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary w-full btn-lg" id="inquiry-submit-btn">
                    <i data-lucide="send" class="w-4 h-4"></i> Send Inquiry
                </button>
            </div>
        </form>
        <div id="inquiry-success" class="hidden text-center py-8">
            <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="check-circle" class="w-8 h-8 text-primary-600"></i>
            </div>
            <h4 class="text-lg font-bold text-dark-900 mb-2">Inquiry Sent!</h4>
            <p class="text-sm text-gray-500 mb-4">We'll get back to you as soon as possible.</p>
            <a href="<?= whatsappLink('Hi, I just sent an inquiry about ' . $car['title'] . '. Ref: #' . $car['id'], $whatsapp) ?>" target="_blank" class="btn btn-primary btn-sm">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
                Follow Up on WhatsApp
            </a>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- LIGHTBOX -->
<!-- ============================================================ -->
<div id="lightbox" class="lightbox-overlay" onclick="if(event.target===this) closeLightbox()">
    <button type="button" onclick="lightboxNav(-1)" class="absolute left-4 top-1/2 -translate-y-1/2 w-12 h-12 bg-white/10 hover:bg-white/20 text-white rounded-full flex items-center justify-center transition z-10">
        <i data-lucide="chevron-left" class="w-6 h-6"></i>
    </button>
    <img src="" alt="" id="lightbox-image" class="max-w-[90vw] max-h-[85vh] object-contain">
    <button type="button" onclick="lightboxNav(1)" class="absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 bg-white/10 hover:bg-white/20 text-white rounded-full flex items-center justify-center transition z-10">
        <i data-lucide="chevron-right" class="w-6 h-6"></i>
    </button>
    <button type="button" onclick="closeLightbox()" class="absolute top-4 right-4 w-10 h-10 bg-white/10 hover:bg-white/20 text-white rounded-full flex items-center justify-center transition z-10">
        <i data-lucide="x" class="w-5 h-5"></i>
    </button>
    <div class="absolute bottom-4 left-1/2 -translate-x-1/2 text-white text-sm bg-black/40 px-3 py-1 rounded-full">
        <span id="lightbox-counter"></span>
    </div>
</div>

<?php
$imageUrls = [];
foreach ($images as $img) {
    $imageUrls[] = resolveUrl($img['image_url']);
}
if (empty($imageUrls)) {
    $imageUrls[] = $primaryImageUrl;
}
$galleryImagesJson = json_encode($imageUrls);
$pageScripts = <<<JS
<script>
// Gallery - use JSON so URLs with quotes/special chars don't break the script
const galleryImages = {$galleryImagesJson};
let currentImage = 0;

function setGalleryImage(idx) {
    if (!Array.isArray(galleryImages) || galleryImages.length === 0) return;
    if (idx < 0 || idx >= galleryImages.length) return;
    currentImage = idx;
    document.getElementById('main-image').src = galleryImages[idx];
    document.getElementById('gallery-counter').textContent = idx + 1;
    // Update thumb active state
    document.querySelectorAll('.gallery-thumb').forEach((t, i) => {
        t.classList.toggle('active', i === idx);
    });
}

function galleryNav(dir) {
    if (!Array.isArray(galleryImages) || galleryImages.length === 0) return;
    let next = currentImage + dir;
    if (next < 0) next = galleryImages.length - 1;
    if (next >= galleryImages.length) next = 0;
    setGalleryImage(next);
}
window.setGalleryImage = setGalleryImage;
window.galleryNav = galleryNav;

// Lightbox
let lightboxIndex = 0;

function openLightbox(idx) {
    lightboxIndex = idx !== undefined ? idx : currentImage;
    document.getElementById('lightbox-image').src = galleryImages[lightboxIndex];
    document.getElementById('lightbox-counter').textContent = (lightboxIndex + 1) + ' / ' + galleryImages.length;
    document.getElementById('lightbox').classList.add('active');
    document.body.classList.add('overflow-hidden');
}

function closeLightbox() {
    document.getElementById('lightbox').classList.remove('active');
    document.body.classList.remove('overflow-hidden');
}

function lightboxNav(dir) {
    lightboxIndex += dir;
    if (lightboxIndex < 0) lightboxIndex = galleryImages.length - 1;
    if (lightboxIndex >= galleryImages.length) lightboxIndex = 0;
    document.getElementById('lightbox-image').src = galleryImages[lightboxIndex];
    document.getElementById('lightbox-counter').textContent = (lightboxIndex + 1) + ' / ' + galleryImages.length;
}
window.openLightbox = openLightbox;
window.closeLightbox = closeLightbox;
window.lightboxNav = lightboxNav;

// Keyboard nav
document.addEventListener('keydown', (e) => {
    if (document.getElementById('lightbox').classList.contains('active')) {
        if (e.key === 'ArrowLeft') lightboxNav(-1);
        if (e.key === 'ArrowRight') lightboxNav(1);
        if (e.key === 'Escape') closeLightbox();
    }
});

// Click main image to open lightbox
var mainImageEl = document.getElementById('main-image');
if (mainImageEl) mainImageEl.addEventListener('click', function() { openLightbox(currentImage); });

// Description toggle
const descContent = document.getElementById('description-content');
const descToggle = document.getElementById('desc-toggle');
if (descContent && descContent.scrollHeight > 200) {
    descContent.style.maxHeight = '200px';
    descContent.style.overflow = 'hidden';
    descToggle.classList.remove('hidden');
}

function toggleDescription() {
    if (descContent.style.maxHeight === '200px') {
        descContent.style.maxHeight = descContent.scrollHeight + 'px';
        descToggle.innerHTML = 'Read Less <i data-lucide="chevron-up" class="w-4 h-4"></i>';
    } else {
        descContent.style.maxHeight = '200px';
        descToggle.innerHTML = 'Read More <i data-lucide="chevron-down" class="w-4 h-4"></i>';
    }
    lucide.createIcons();
}

// Inquiry Modal
function openInquiryModal() {
    document.getElementById('inquiry-modal').classList.add('active');
    document.body.classList.add('overflow-hidden');
}

function closeInquiryModal() {
    document.getElementById('inquiry-modal').classList.remove('active');
    document.body.classList.remove('overflow-hidden');
}

function submitInquiry(e) {
    e.preventDefault();
    const form = e.target;
    const btn = document.getElementById('inquiry-submit-btn');
    btn.disabled = true;
    btn.innerHTML = '<i data-lucide="loader" class="w-4 h-4 animate-spin"></i> Sending...';
    
    fetch(BASE_URL + '/api/inquiry', {
        method: 'POST',
        body: new FormData(form)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            form.classList.add('hidden');
            document.getElementById('inquiry-success').classList.remove('hidden');
            lucide.createIcons();
        } else {
            alert(data.message || 'Failed to send inquiry. Please try again.');
            btn.disabled = false;
            btn.innerHTML = '<i data-lucide="send" class="w-4 h-4"></i> Send Inquiry';
            lucide.createIcons();
        }
    })
    .catch(() => {
        alert('Network error. Please try again.');
        btn.disabled = false;
        btn.innerHTML = '<i data-lucide="send" class="w-4 h-4"></i> Send Inquiry';
        lucide.createIcons();
    });
}

// Share
function shareCar() {
    const data = {
        title: document.querySelector('h1').textContent,
        url: window.location.href
    };
    if (navigator.share) {
        navigator.share(data);
    } else {
        navigator.clipboard.writeText(data.url);
        alert('Link copied to clipboard!');
    }
}

const BASE_URL = '<?= BASE_URL ?>';
</script>
JS;

include __DIR__ . '/footer.php';
?>
