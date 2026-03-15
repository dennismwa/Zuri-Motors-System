<?php
if (!defined('ROOT_PATH')) {
    require_once __DIR__ . '/functions.php';
}
$companyName = Settings::get('company_name', 'Zuri Motors');
http_response_code(404);
$pageSeo = seoMeta('Page Not Found - ' . $companyName, 'The page you\'re looking for could not be found.');
include __DIR__ . '/header.php';
?>

<section class="py-20 bg-gray-50 min-h-[60vh] flex items-center">
    <div class="max-w-xl mx-auto px-4 text-center">
        <div class="text-8xl font-bold text-primary-100 mb-4">404</div>
        <h1 class="text-2xl lg:text-3xl font-bold text-dark-900 mb-3 font-display">Page Not Found</h1>
        <p class="text-gray-500 mb-8">The page you're looking for doesn't exist or has been moved. Let's get you back on track.</p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="<?= BASE_URL ?>/" class="btn btn-primary btn-lg">
                <i data-lucide="home" class="w-4 h-4"></i> Go Home
            </a>
            <a href="<?= BASE_URL ?>/cars" class="btn btn-outline btn-lg">
                <i data-lucide="car" class="w-4 h-4"></i> Browse Cars
            </a>
        </div>
        <div class="mt-10">
            <p class="text-sm text-gray-400 mb-3">Or search for what you need:</p>
            <form action="<?= BASE_URL ?>/cars" method="GET" class="flex gap-2 max-w-md mx-auto">
                <input type="text" name="search" placeholder="Search cars..." class="form-control" required>
                <button type="submit" class="btn btn-primary"><i data-lucide="search" class="w-4 h-4"></i></button>
            </form>
        </div>
    </div>
</section>

<?php include __DIR__ . '/footer.php'; ?>
