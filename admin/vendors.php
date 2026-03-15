<?php
$pageTitle = 'Vendors';
require_once dirname(__DIR__) . '/functions.php';
$filters = ['role'=>'vendor', 'search'=>$_GET['search']??''];
$total = countUsers($filters);
$pagination = new Pagination($total, 20);
$vendors = getUsers($filters, $pagination->perPage, $pagination->offset);
include __DIR__ . '/header.php';
?>

<div class="flex items-center justify-between mb-5">
    <form method="GET" class="flex items-end gap-3"><input type="text" name="search" value="<?= clean($filters['search']) ?>" class="filter-input text-sm" placeholder="Search vendors..."><button type="submit" class="btn btn-primary btn-sm"><i data-lucide="search" class="w-4 h-4"></i></button></form>
    <a href="<?= BASE_URL ?>/admin/user-add" class="btn btn-primary btn-sm"><i data-lucide="user-plus" class="w-4 h-4"></i> Add Vendor</a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    <?php foreach($vendors as $v): 
        $carCount = (int)db()->prepare("SELECT COUNT(*) FROM cars WHERE user_id=?")->execute([$v['id']]) ? db()->query("SELECT COUNT(*) FROM cars WHERE user_id={$v['id']}")->fetchColumn() : 0;
    ?>
    <div class="bg-white rounded-xl border border-gray-200 p-5 hover:border-primary-200 hover:shadow-md transition">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 rounded-xl bg-primary-100 text-primary-700 flex items-center justify-center text-lg font-bold"><?= strtoupper(substr($v['first_name'],0,1)) ?></div>
            <div>
                <h3 class="font-bold text-dark-900"><?= clean($v['company_name'] ?: $v['first_name'].' '.$v['last_name']) ?></h3>
                <p class="text-xs text-gray-400"><?= clean($v['email']) ?></p>
            </div>
            <div class="ml-auto"><?= $v['is_active'] ? '<span class="text-emerald-500 text-[10px] font-bold bg-emerald-50 px-2 py-0.5 rounded-full">Active</span>' : '<span class="text-red-500 text-[10px] font-bold bg-red-50 px-2 py-0.5 rounded-full">Inactive</span>' ?></div>
        </div>
        <div class="grid grid-cols-3 gap-2 text-center mb-4">
            <div class="bg-gray-50 rounded-lg py-2"><p class="text-lg font-bold text-dark-900"><?= $carCount ?></p><p class="text-[10px] text-gray-400">Listings</p></div>
            <div class="bg-gray-50 rounded-lg py-2"><p class="text-lg font-bold text-dark-900"><?= $v['city'] ?: '-' ?></p><p class="text-[10px] text-gray-400">City</p></div>
            <div class="bg-gray-50 rounded-lg py-2"><p class="text-lg font-bold text-dark-900"><?= $v['is_verified']?'✓':'✗' ?></p><p class="text-[10px] text-gray-400">Verified</p></div>
        </div>
        <div class="flex gap-2">
            <a href="<?= BASE_URL ?>/admin/user-edit/<?= $v['id'] ?>" class="btn btn-outline btn-sm flex-1"><i data-lucide="pencil" class="w-3 h-3"></i> Edit</a>
            <a href="<?= BASE_URL ?>/admin/cars?user_id=<?= $v['id'] ?>" class="btn btn-ghost btn-sm flex-1"><i data-lucide="car" class="w-3 h-3"></i> Cars</a>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<div class="mt-6"><?= $pagination->render(BASE_URL.'/admin/vendors', ['search'=>$filters['search']]) ?></div>

<?php include __DIR__ . '/footer.php'; ?>
