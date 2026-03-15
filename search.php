<?php
/**
 * Zuri Motors - Search Results Page
 * Redirects to cars.php with search parameter
 */
require_once __DIR__ . '/functions.php';

$query = $_GET['q'] ?? $_GET['search'] ?? '';
if ($query) {
    header('Location: ' . BASE_URL . '/cars?search=' . urlencode($query));
    exit;
}

$pageSeo = seoMeta('Search Cars - ' . Settings::get('company_name'));
include __DIR__ . '/header.php';
?>

<section class="py-20 bg-gray-50 min-h-screen">
    <div class="max-w-2xl mx-auto px-4 text-center">
        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <i data-lucide="search" class="w-10 h-10 text-gray-400"></i>
        </div>
        <h1 class="text-2xl font-bold text-dark-900 mb-4">Search Our Vehicles</h1>
        <form action="<?= BASE_URL ?>/cars" method="GET" class="flex gap-2 max-w-lg mx-auto">
            <input type="text" name="search" placeholder="Search by name, brand, model..." class="form-control text-lg" autofocus required>
            <button type="submit" class="btn btn-primary btn-lg"><i data-lucide="search" class="w-5 h-5"></i></button>
        </form>
        <div class="mt-6 flex flex-wrap gap-2 justify-center">
            <span class="text-sm text-gray-400">Popular:</span>
            <?php foreach (['Toyota', 'Mercedes', 'BMW', 'SUV', 'Under 2M'] as $term): ?>
            <a href="<?= BASE_URL ?>/cars?search=<?= urlencode($term) ?>" class="text-sm bg-white border border-gray-200 text-gray-600 px-4 py-1.5 rounded-full hover:border-primary-300 hover:text-primary-600 transition"><?= $term ?></a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include __DIR__ . '/footer.php'; ?>
