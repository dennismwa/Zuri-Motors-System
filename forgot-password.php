<?php
require_once __DIR__ . '/functions.php';

if (Auth::check()) redirect(BASE_URL . '/');

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CSRF::verifyOrDie();
    $email = $_POST['email'] ?? '';
    
    $stmt = db()->prepare("SELECT id, first_name FROM users WHERE email = ? AND is_active = 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user) {
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        db()->prepare("UPDATE users SET reset_token = ?, reset_token_expires = ? WHERE id = ?")->execute([$token, $expires, $user['id']]);
        // In production, send email with reset link
        // For now, just show success
        $success = true;
        Auth::logActivity('password_reset_request', 'user', $user['id'], 'Password reset requested for: ' . $email);
    } else {
        // Don't reveal if email exists
        $success = true;
    }
}

$companyName = Settings::get('company_name', 'Zuri Motors');
$pageSeo = seoMeta('Forgot Password - ' . $companyName);
include __DIR__ . '/header.php';
?>

<section class="py-16 bg-gray-50 min-h-[70vh] flex items-center">
    <div class="max-w-md mx-auto px-4 w-full">
        <div class="text-center mb-8">
            <div class="w-14 h-14 bg-amber-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i data-lucide="key" class="w-7 h-7 text-amber-600"></i>
            </div>
            <h1 class="text-2xl font-bold text-dark-900 font-display">Forgot Password?</h1>
            <p class="text-sm text-gray-500 mt-1">Enter your email and we'll send you reset instructions</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 p-6 lg:p-8">
            <?php if ($success): ?>
            <div class="text-center py-4">
                <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="mail-check" class="w-8 h-8 text-primary-600"></i>
                </div>
                <h3 class="font-bold text-dark-900 mb-2">Check Your Email</h3>
                <p class="text-sm text-gray-500 mb-4">If an account exists with that email, we've sent password reset instructions.</p>
                <a href="<?= BASE_URL ?>/login" class="btn btn-primary">Back to Sign In</a>
            </div>
            <?php else: ?>
            <form method="POST">
                <?= CSRF::field() ?>
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" required class="form-control" placeholder="your@email.com" autofocus>
                </div>
                <button type="submit" class="btn btn-primary btn-lg w-full mb-4">
                    <i data-lucide="send" class="w-4 h-4"></i> Send Reset Link
                </button>
                <div class="text-center">
                    <a href="<?= BASE_URL ?>/login" class="text-sm text-gray-500 hover:text-primary-600 transition">← Back to Sign In</a>
                </div>
            </form>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include __DIR__ . '/footer.php'; ?>
