<?php
require_once __DIR__ . '/functions.php';
$companyName = Settings::get('company_name', 'Zuri Motors');
$page = getPage('about');
$pageSeo = seoMeta('About Us - ' . $companyName, 'Learn about ' . $companyName . ', your trusted automotive marketplace in Kenya.');
include __DIR__ . '/header.php';
?>

<div class="bg-gray-50 border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 lg:px-6 py-4">
        <?= breadcrumbs([['label' => 'Home', 'url' => BASE_URL . '/'], ['label' => 'About Us']]) ?>
    </div>
</div>

<!-- Hero -->
<section class="bg-gradient-to-br from-dark-900 to-dark-950 py-20 relative overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-primary-500 rounded-full -translate-y-1/2 translate-x-1/3 blur-3xl"></div>
    </div>
    <div class="max-w-7xl mx-auto px-4 lg:px-6 relative">
        <div class="max-w-2xl">
            <span class="section-badge text-primary-400"><i data-lucide="info" class="w-4 h-4"></i> About Us</span>
            <h1 class="text-3xl lg:text-5xl font-bold text-white font-display mt-2 mb-4">Driving Excellence in Every Deal</h1>
            <p class="text-lg text-white/60 leading-relaxed">
                <?= clean($companyName) ?> is Kenya's premier automotive marketplace, connecting buyers with quality vehicles from trusted dealers and private sellers since our founding.
            </p>
        </div>
    </div>
</section>

<!-- Stats -->
<section class="py-12 bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 lg:px-6">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
            <?php
            $totalCars = countCars();
            $totalBrands = count(getBrands(true));
            ?>
            <div class="text-center">
                <p class="text-3xl lg:text-4xl font-bold text-primary-600"><?= number_format($totalCars) ?>+</p>
                <p class="text-sm text-gray-500 mt-1">Vehicles Listed</p>
            </div>
            <div class="text-center">
                <p class="text-3xl lg:text-4xl font-bold text-primary-600"><?= $totalBrands ?>+</p>
                <p class="text-sm text-gray-500 mt-1">Top Brands</p>
            </div>
            <div class="text-center">
                <p class="text-3xl lg:text-4xl font-bold text-primary-600">5,000+</p>
                <p class="text-sm text-gray-500 mt-1">Happy Customers</p>
            </div>
            <div class="text-center">
                <p class="text-3xl lg:text-4xl font-bold text-primary-600">10+</p>
                <p class="text-sm text-gray-500 mt-1">Years Experience</p>
            </div>
        </div>
    </div>
</section>

<!-- Content -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 lg:px-6">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div>
                <span class="section-badge"><i data-lucide="target" class="w-4 h-4"></i> Our Mission</span>
                <h2 class="section-title mt-2 mb-4">Making Car Buying Simple & Transparent</h2>
                <?php if ($page && $page['content']): ?>
                    <div class="prose prose-sm text-gray-600 leading-relaxed"><?= $page['content'] ?></div>
                <?php else: ?>
                    <p class="text-gray-600 leading-relaxed mb-4">At <?= clean($companyName) ?>, we believe everyone deserves a reliable vehicle at a fair price. Our platform brings together verified dealers and quality vehicles, making it easy to find, compare, and purchase your next car with confidence.</p>
                    <p class="text-gray-600 leading-relaxed mb-4">We rigorously vet every listing on our platform, ensuring our customers have access to accurate information and trustworthy sellers. Our team of automotive experts is available around the clock to guide you through every step of the process.</p>
                    <p class="text-gray-600 leading-relaxed">Whether you're buying your first car, upgrading your ride, or selling a vehicle, <?= clean($companyName) ?> provides the tools, transparency, and support you need to make the best decision.</p>
                <?php endif; ?>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-primary-50 rounded-2xl p-6">
                    <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center mb-4">
                        <i data-lucide="shield-check" class="w-6 h-6 text-primary-600"></i>
                    </div>
                    <h3 class="font-bold text-dark-900 mb-2">Verified Listings</h3>
                    <p class="text-sm text-gray-600">Every vehicle inspected and verified by experts.</p>
                </div>
                <div class="bg-blue-50 rounded-2xl p-6 mt-8">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-4">
                        <i data-lucide="heart-handshake" class="w-6 h-6 text-blue-600"></i>
                    </div>
                    <h3 class="font-bold text-dark-900 mb-2">Customer First</h3>
                    <p class="text-sm text-gray-600">Dedicated support for buyers and sellers alike.</p>
                </div>
                <div class="bg-amber-50 rounded-2xl p-6">
                    <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center mb-4">
                        <i data-lucide="trending-up" class="w-6 h-6 text-amber-600"></i>
                    </div>
                    <h3 class="font-bold text-dark-900 mb-2">Best Value</h3>
                    <p class="text-sm text-gray-600">Competitive pricing with flexible financing options.</p>
                </div>
                <div class="bg-emerald-50 rounded-2xl p-6 mt-8">
                    <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-4">
                        <i data-lucide="clock" class="w-6 h-6 text-emerald-600"></i>
                    </div>
                    <h3 class="font-bold text-dark-900 mb-2">Fast Process</h3>
                    <p class="text-sm text-gray-600">Streamlined buying and selling experience.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-16 bg-primary-600">
    <div class="max-w-4xl mx-auto px-4 lg:px-6 text-center">
        <h2 class="text-2xl lg:text-3xl font-bold text-white font-display mb-4">Ready to Find Your Perfect Car?</h2>
        <p class="text-white/70 mb-8">Browse thousands of quality vehicles from trusted dealers across Kenya.</p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="<?= BASE_URL ?>/cars" class="btn btn-lg bg-white text-primary-700 hover:bg-gray-50 font-bold">Browse Cars</a>
            <a href="<?= BASE_URL ?>/contact" class="btn btn-lg bg-white/10 text-white hover:bg-white/20 border border-white/20">Contact Us</a>
        </div>
    </div>
</section>

<?php include __DIR__ . '/footer.php'; ?>
