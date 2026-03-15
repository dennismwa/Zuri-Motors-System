<?php
/**
 * Admin Login Page - /admin/login
 * This file handles the admin login separately from the frontend login
 */
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/functions.php';

// Already logged in? Redirect to dashboard
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
        $role = $result['user']['role'];
        if ($role === 'admin' || $role === 'staff') redirect(BASE_URL . '/admin');
        elseif ($role === 'vendor') redirect(BASE_URL . '/vendor');
        else { Flash::error('Access denied. Admin only.'); Auth::logout(); redirect(BASE_URL . '/admin/login'); }
    } else {
        $error = $result['message'];
    }
}

$companyName = Settings::get('company_name', 'Zuri Motors');
$adminLoginLogo = Settings::get('logo', 'assets/images/ZuriMotors -logo.png');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - <?= clean($companyName) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { primary:{600:'#16a34a',700:'#15803d'} }, fontFamily: { sans:['DM Sans','system-ui','sans-serif'] } } } }
    </script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
</head>
<body class="font-sans bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <?php if ($adminLoginLogo): $adminLoginLogoUrl = str_starts_with($adminLoginLogo, 'http') || str_starts_with($adminLoginLogo, '//') ? $adminLoginLogo : BASE_URL . '/' . str_replace(' ', '%20', ltrim($adminLoginLogo, '/')); ?>
            <img src="<?= htmlspecialchars($adminLoginLogoUrl) ?>" alt="<?= clean($companyName) ?>" class="h-14 w-auto max-w-[180px] mx-auto mb-4 object-contain">
            <?php else: ?>
            <div class="w-16 h-16 bg-primary-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-primary-600/30">
                <i data-lucide="car" class="w-8 h-8 text-white"></i>
            </div>
            <?php endif; ?>
            <h1 class="text-2xl font-bold text-white"><?= clean($companyName) ?></h1>
            <p class="text-sm text-slate-400 mt-1">Admin Dashboard</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <h2 class="text-xl font-bold text-slate-900 mb-1">Welcome back</h2>
            <p class="text-sm text-slate-500 mb-6">Sign in to your admin account</p>

            <?php if ($error): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl mb-5 flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-4 h-4 flex-shrink-0"></i>
                <?= clean($error) ?>
            </div>
            <?php endif; ?>

            <?= Flash::render() ?>

            <form method="POST" action="">
                <?= CSRF::field() ?>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Email Address</label>
                    <div class="relative">
                        <i data-lucide="mail" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                        <input type="email" name="email" required 
                               class="w-full pl-10 pr-4 py-3 border border-slate-200 rounded-xl text-sm focus:border-primary-600 focus:ring-2 focus:ring-primary-600/10 outline-none transition"
                               placeholder="admin@zurimotors.com" value="<?= clean($_POST['email'] ?? '') ?>" autofocus>
                    </div>
                </div>
                <div class="mb-4">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-sm font-semibold text-slate-700">Password</label>
                        <a href="<?= BASE_URL ?>/forgot-password" class="text-xs text-primary-600 hover:text-primary-700 font-medium">Forgot?</a>
                    </div>
                    <div class="relative">
                        <i data-lucide="lock" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                        <input type="password" name="password" required id="pw"
                               class="w-full pl-10 pr-10 py-3 border border-slate-200 rounded-xl text-sm focus:border-primary-600 focus:ring-2 focus:ring-primary-600/10 outline-none transition"
                               placeholder="Enter password">
                        <button type="button" onclick="let p=document.getElementById('pw');p.type=p.type==='password'?'text':'password'" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
                <div class="flex items-center mb-6">
                    <label class="flex items-center gap-2 text-sm text-slate-600 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 accent-primary-600 rounded"> Remember me
                    </label>
                </div>
                <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3 rounded-xl transition-all flex items-center justify-center gap-2 shadow-lg shadow-primary-600/20">
                    <i data-lucide="log-in" class="w-4 h-4"></i> Sign In
                </button>
            </form>
        </div>

        <p class="text-center text-sm text-slate-500 mt-6">
            <a href="<?= BASE_URL ?>/" class="hover:text-white transition">← Back to website</a>
        </p>
    </div>
    <script>lucide.createIcons();</script>
</body>
</html>
