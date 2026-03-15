<?php
require_once __DIR__ . '/functions.php';

$slug = $_GET['slug'] ?? '';
if (!$slug) { header('Location: ' . BASE_URL . '/cars'); exit; }

$category = getCategory($slug);
if (!$category) { http_response_code(404); include __DIR__ . '/404.php'; exit; }

$companyName = Settings::get('company_name', 'Zuri Motors');
$filters = array_merge($_GET, ['category_slug' => $slug]);
unset($filters['slug']);

$perPage = (int)Settings::get('cars_per_page', 12);
$totalCars = countCars($filters);
$pagination = new Pagination($totalCars, $perPage);
$cars = getCars($filters, $pagination->perPage, $pagination->offset);

$brands = getBrands(true, true);
$locations = getLocations(true);

$pageSeo = seoMeta(
    $category['name'] . ' Cars for Sale - ' . $companyName,
    'Browse ' . number_format($totalCars) . ' ' . $category['name'] . ' vehicles for sale at ' . $companyName . '. Find the best deals on quality ' . strtolower($category['name']) . ' cars.'
);
include __DIR__ . '/header.php';
?>

<div class="bg-gray-50 border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 lg:px-6 py-4">
        <?= breadcrumbs([['label'=>'Home','url'=>BASE_URL.'/'],['label'=>'Cars','url'=>BASE_URL.'/cars'],['label'=>$category['name']]]) ?>
    </div>
</div>

<section class="py-8 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 lg:px-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-dark-900 font-display"><?= clean($category['name']) ?> Cars</h1>
                <p class="text-sm text-gray-500 mt-1"><?= number_format($totalCars) ?> vehicles found</p>
            </div>
            <select onchange="location='<?= BASE_URL ?>/category/<?= $slug ?>?sort='+this.value" class="filter-select text-sm py-2 w-auto">
                <option value="newest" <?= ($_GET['sort'] ?? '') === 'newest' ? 'selected' : '' ?>>Newest</option>
                <option value="price_low" <?= ($_GET['sort'] ?? '') === 'price_low' ? 'selected' : '' ?>>Price: Low to High</option>
                <option value="price_high" <?= ($_GET['sort'] ?? '') === 'price_high' ? 'selected' : '' ?>>Price: High to Low</option>
            </select>
        </div>

        <?php if (empty($cars)): ?>
            <?= renderEmptyState('No ' . $category['name'] . ' cars found', 'Check back soon for new listings.', 'car') ?>
        <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            <?php foreach ($cars as $car): echo renderCarCard($car, 'reveal'); endforeach; ?>
        </div>
        <div class="mt-8"><?= $pagination->render(BASE_URL . '/category/' . $slug, $_GET) ?></div>
        <?php endif; ?>

        <?php if ($category['description']): ?>
        <div class="mt-12 bg-white rounded-2xl border border-gray-200 p-6">
            <h2 class="text-lg font-bold text-dark-900 mb-3">About <?= clean($category['name']) ?> Vehicles</h2>
            <p class="text-sm text-gray-600 leading-relaxed"><?= nl2br(clean($category['description'])) ?></p>
        </div>
        <?php endif; ?>
    </div>
</section>

<script>
const observer = new IntersectionObserver(e => e.forEach(el => { if(el.isIntersecting){el.target.classList.add('visible');observer.unobserve(el.target);}}),{threshold:0.05});
document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
</script>

<?php include __DIR__ . '/footer.php'; ?>
