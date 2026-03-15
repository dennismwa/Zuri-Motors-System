<?php
require_once __DIR__ . '/functions.php';
if (Auth::check()) {
    $role = Auth::role();
    if ($role === 'admin' || $role === 'staff') redirect(BASE_URL . '/admin');
    elseif ($role === 'vendor') redirect(BASE_URL . '/vendor');
    else redirect(BASE_URL . '/');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CSRF::verifyOrDie();
    $result = Auth::login($_POST['email'] ?? '', $_POST['password'] ?? '');
    if ($result['success']) {
        $redirect = $_SESSION['redirect_after_login'] ?? null;
        unset($_SESSION['redirect_after_login']);
        $role = $result['user']['role'];
        if ($redirect) redirect(BASE_URL . $redirect);
        elseif ($role === 'admin' || $role === 'staff') redirect(BASE_URL . '/admin');
        elseif ($role === 'vendor') redirect(BASE_URL . '/vendor');
        else redirect(BASE_URL . '/');
    }
    $error = $result['message'];
}

$companyName = Settings::get('company_name', 'Zuri Motors');
$pageSeo = seoMeta('Sign In - ' . $companyName);
include __DIR__ . '/header.php';
?>
<section class="py-16 bg-gray-50 min-h-[70vh] flex items-center">
<div class="max-w-md mx-auto px-4 w-full">
    <div class="text-center mb-8">
        <div class="w-14 h-14 bg-primary-600 rounded-2xl flex items-center justify-center mx-auto mb-4"><i data-lucide="car" class="w-7 h-7 text-white"></i></div>
        <h1 class="text-2xl font-bold text-dark-900">Welcome Back</h1>
        <p class="text-sm text-gray-500 mt-1">Sign in to your <?= clean($companyName) ?> account</p>
    </div>
    <?php if ($error): ?>
    <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-5 flex items-center gap-3 text-sm text-red-700">
        <i data-lucide="alert-circle" class="w-5 h-5 flex-shrink-0"></i> <?= clean($error) ?>
    </div>
    <?php endif; ?>
    <div class="bg-white rounded-2xl border border-gray-200 p-6 lg:p-8">
        <form method="POST">
            <?= CSRF::field() ?>
            <div class="mb-4">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" required class="form-control" placeholder="your@email.com" value="<?= clean($_POST['email'] ?? '') ?>" autofocus>
            </div>
            <div class="mb-4">
                <div class="flex items-center justify-between mb-1">
                    <label class="form-label mb-0">Password</label>
                    <a href="<?= BASE_URL ?>/forgot-password" class="text-xs text-primary-600 hover:text-primary-700 font-medium">Forgot password?</a>
                </div>
                <input type="password" name="password" required class="form-control" placeholder="Enter your password">
            </div>
            <label class="form-check mb-5"><input type="checkbox" name="remember"> Remember me</label>
            <button type="submit" class="btn btn-primary btn-lg w-full">Sign In</button>
        </form>
    </div>
    <?php if (Settings::get('enable_vendor_registration', '1') === '1'): ?>
    <p class="text-center text-sm text-gray-500 mt-4">
        Want to sell cars? <a href="<?= BASE_URL ?>/register" class="text-primary-600 hover:text-primary-700 font-semibold">Create a vendor account</a>
    </p>
    <?php endif; ?>
</div>
</section>
<?php include __DIR__ . '/footer.php'; ?>
