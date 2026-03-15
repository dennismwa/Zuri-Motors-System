<?php
/**
 * Zuri Motors - Authentication Handler
 * Handles logout and other auth actions
 */
require_once __DIR__ . '/functions.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'logout':
        Auth::logout();
        Flash::success('You have been signed out successfully.');
        redirect(BASE_URL . '/');
        break;

    case 'reset_password':
        $token = $_GET['token'] ?? '';
        if (empty($token)) { redirect(BASE_URL . '/login'); }

        $stmt = db()->prepare("SELECT id, email FROM users WHERE reset_token = ? AND reset_token_expires > NOW() AND is_active = 1");
        $stmt->execute([$token]);
        $user = $stmt->fetch();

        if (!$user) {
            Flash::error('Invalid or expired reset link. Please request a new one.');
            redirect(BASE_URL . '/forgot-password');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            CSRF::verifyOrDie();
            $password = $_POST['password'] ?? '';
            $confirm = $_POST['password_confirm'] ?? '';

            if (strlen($password) < 6) {
                Flash::error('Password must be at least 6 characters.');
            } elseif ($password !== $confirm) {
                Flash::error('Passwords do not match.');
            } else {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                db()->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expires = NULL WHERE id = ?")
                    ->execute([$hashed, $user['id']]);
                Auth::logActivity('password_reset', 'user', $user['id'], 'Password was reset');
                Flash::success('Password reset successfully. Please sign in with your new password.');
                redirect(BASE_URL . '/login');
            }
        }

        $companyName = Settings::get('company_name', 'Zuri Motors');
        $pageSeo = seoMeta('Reset Password - ' . $companyName);
        include __DIR__ . '/header.php';
        ?>
        <section class="py-16 bg-gray-50 min-h-[70vh] flex items-center">
            <div class="max-w-md mx-auto px-4 w-full">
                <div class="text-center mb-8">
                    <h1 class="text-2xl font-bold text-dark-900 font-display">Reset Your Password</h1>
                    <p class="text-sm text-gray-500 mt-1">Enter your new password for <?= clean($user['email']) ?></p>
                </div>
                <div class="bg-white rounded-2xl border border-gray-200 p-6 lg:p-8">
                    <form method="POST">
                        <?= CSRF::field() ?>
                        <div class="form-group">
                            <label class="form-label">New Password</label>
                            <input type="password" name="password" required class="form-control" minlength="6" placeholder="At least 6 characters">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirm" required class="form-control" placeholder="Re-enter your password">
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-full">
                            <i data-lucide="key" class="w-4 h-4"></i> Reset Password
                        </button>
                    </form>
                </div>
            </div>
        </section>
        <?php
        include __DIR__ . '/footer.php';
        break;

    default:
        redirect(BASE_URL . '/login');
}
