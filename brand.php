<?php
require_once __DIR__ . '/functions.php';

$slug = $_GET['slug'] ?? '';
if (!$slug) { header('Location: ' . BASE_URL . '/cars'); exit; }

$brand = getBrand($slug);
if (!$brand) { http_response_code(404); include __DIR__ . '/404.php'; exit; }

$companyName = Settings::get('company_name', 'Zuri Motors');
$filters = array_merge($_GET, ['brand_slug' => $slug]);
unset($filters['slug']);

$perPage = (int)Settings::get('cars_per_page', 12);
$totalCars = countCars($filters);
$pagination = new Pagination($totalCars, $perPage);
$cars = getCars($filters, $pagination->perPage, $pagination->offset);

$categories = getCategories(true);

$pageSeo = seoMeta(
    $brand['name'] . ' Cars for Sale - ' . $companyName,
    'Browse ' . number_format($totalCars) . ' ' . $brand['name'] . ' vehicles. Find the best ' . $brand['name'] . ' deals at ' . $companyName . '.'
);
include __DIR__ . '/header.php';
?>

<div class="bg-gray-50 border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 lg:px-6 py-4">
        <?= breadcrumbs([['label'=>'Home','url'=>BASE_URL.'/'],['label'=>'Cars','url'=>BASE_URL.'/cars'],['label'=>$brand['name']]]) ?>
    </div>
</div>

<!-- Brand Header -->
<div class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 lg:px-6 py-8">
        <div class="flex items-center gap-4">
            <?php if ($brand['logo']): ?>
            <img src="<?= BASE_URL ?>/<?= $brand['logo'] ?>" alt="<?= clean($brand['name']) ?>" class="h-14 object-contain">
            <?php else: ?>
            <div class="w-14 h-14 bg-gray-100 rounded-xl flex items-center justify-center text-xl font-bold text-gray-400">
                <?= strtoupper(substr($brand['name'], 0, 2)) ?>
            </div>
            <?php endif; ?>
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-dark-900 font-display"><?= clean($brand['name']) ?></h1>
                <p class="text-sm text-gray-500"><?= number_format($totalCars) ?> vehicles available</p>
            </div>
        </div>
    </div>
</div>

<section class="py-8 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 lg:px-6">
        <div class="flex items-center justify-between mb-6">
            <div class="flex flex-wrap gap-2">
                <a href="<?= BASE_URL ?>/brand/<?= $slug ?>" class="text-xs font-semibold px-3 py-1.5 rounded-full border transition <?= empty($_GET['category_id']) ? 'bg-primary-50 border-primary-300 text-primary-700' : 'border-gray-200 text-gray-600 hover:border-primary-200' ?>">All</a>
                <?php foreach ($categories as $cat): ?>
                <a href="<?= BASE_URL ?>/brand/<?= $slug ?>?category_id=<?= $cat['id'] ?>" class="text-xs font-semibold px-3 py-1.5 rounded-full border transition <?= ($_GET['category_id'] ?? '') == $cat['id'] ? 'bg-primary-50 border-primary-300 text-primary-700' : 'border-gray-200 text-gray-600 hover:border-primary-200' ?>"><?= clean($cat['name']) ?></a>
                <?php endforeach; ?>
            </div>
            <select onchange="location='<?= BASE_URL ?>/brand/<?= $slug ?>?sort='+this.value" class="filter-select text-sm py-2 w-auto">
                <option value="newest" <?= ($_GET['sort'] ?? '') === 'newest' ? 'selected' : '' ?>>Newest</option>
                <option value="price_low" <?= ($_GET['sort'] ?? '') === 'price_low' ? 'selected' : '' ?>>Price: Low</option>
                <option value="price_high" <?= ($_GET['sort'] ?? '') === 'price_high' ? 'selected' : '' ?>>Price: High</option>
            </select>
        </div>

        <?php if (empty($cars)): ?>
            <?= renderEmptyState('No ' . $brand['name'] . ' cars found', 'Check back soon for new listings.', 'car') ?>
        <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            <?php foreach ($cars as $car): echo renderCarCard($car, 'reveal'); endforeach; ?>
        </div>
        <div class="mt-8"><?= $pagination->render(BASE_URL . '/brand/' . $slug, $_GET) ?></div>
        <?php endif; ?>

        <?php if ($brand['description']): ?>
        <div class="mt-12 bg-white rounded-2xl border border-gray-200 p-6">
            <h2 class="text-lg font-bold text-dark-900 mb-3">About <?= clean($brand['name']) ?></h2>
            <p class="text-sm text-gray-600 leading-relaxed"><?= nl2br(clean($brand['description'])) ?></p>
        </div>
        <?php endif; ?>
    </div>
</section>

<script>
const observer = new IntersectionObserver(e => e.forEach(el => { if(el.isIntersecting){el.target.classList.add('visible');observer.unobserve(el.target);}}),{threshold:0.05});
document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
</script>

<?php include __DIR__ . '/footer.php'; ?>
