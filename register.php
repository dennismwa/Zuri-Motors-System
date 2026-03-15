<?php
require_once __DIR__ . '/functions.php';

if (Auth::check()) redirect(BASE_URL . '/');
if (Settings::get('enable_vendor_registration', '1') !== '1') {
    Flash::error('Registration is currently disabled.');
    redirect(BASE_URL . '/login');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CSRF::verifyOrDie();
    $data = [
        'first_name' => $_POST['first_name'] ?? '', 'last_name' => $_POST['last_name'] ?? '',
        'email' => $_POST['email'] ?? '', 'phone' => $_POST['phone'] ?? '',
        'password' => $_POST['password'] ?? '', 'role' => 'vendor',
        'company_name' => $_POST['company_name'] ?? '', 'city' => $_POST['city'] ?? '',
        'is_active' => 1, 'is_verified' => 0,
    ];

    if (strlen($data['password']) < 6) { $error = 'Password must be at least 6 characters.'; }
    elseif ($data['password'] !== ($_POST['password_confirm'] ?? '')) { $error = 'Passwords do not match.'; }
    elseif (empty($data['first_name']) || empty($data['last_name']) || empty($data['email'])) { $error = 'Please fill in all required fields.'; }
    else {
        $result = saveUser($data);
        if ($result['success']) {
            Auth::login($data['email'], $data['password']);
            Flash::success('Registration successful! Welcome to ' . Settings::get('company_name') . '.');
            redirect(BASE_URL . '/vendor');
        } else { $error = $result['message']; }
    }
}

$companyName = Settings::get('company_name', 'Zuri Motors');
$pageSeo = seoMeta('Register as Dealer - ' . $companyName, 'Create a dealer account to list your vehicles on ' . $companyName);
include __DIR__ . '/header.php';
?>

<section class="py-12 bg-gray-50 min-h-[70vh]">
    <div class="max-w-lg mx-auto px-4">
        <div class="text-center mb-8">
            <div class="w-14 h-14 bg-primary-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i data-lucide="store" class="w-7 h-7 text-white"></i>
            </div>
            <h1 class="text-2xl font-bold text-dark-900 font-display">Become a Dealer</h1>
            <p class="text-sm text-gray-500 mt-1">Register to list and sell vehicles on <?= clean($companyName) ?></p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 p-6 lg:p-8">
            <?php if ($error): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl mb-5 flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-4 h-4 flex-shrink-0"></i> <?= clean($error) ?>
            </div>
            <?php endif; ?>

            <form method="POST">
                <?= CSRF::field() ?>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div class="form-group mb-0">
                        <label class="form-label">First Name <span class="required">*</span></label>
                        <input type="text" name="first_name" required class="form-control" value="<?= clean($_POST['first_name'] ?? '') ?>">
                    </div>
                    <div class="form-group mb-0">
                        <label class="form-label">Last Name <span class="required">*</span></label>
                        <input type="text" name="last_name" required class="form-control" value="<?= clean($_POST['last_name'] ?? '') ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Company / Dealership Name</label>
                    <input type="text" name="company_name" class="form-control" placeholder="Your dealership name" value="<?= clean($_POST['company_name'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Email <span class="required">*</span></label>
                    <input type="email" name="email" required class="form-control" value="<?= clean($_POST['email'] ?? '') ?>">
                </div>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div class="form-group mb-0">
                        <label class="form-label">Phone <span class="required">*</span></label>
                        <input type="tel" name="phone" required class="form-control" value="<?= clean($_POST['phone'] ?? '') ?>">
                    </div>
                    <div class="form-group mb-0">
                        <label class="form-label">City</label>
                        <input type="text" name="city" class="form-control" placeholder="e.g. Nairobi" value="<?= clean($_POST['city'] ?? '') ?>">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div class="form-group mb-0">
                        <label class="form-label">Password <span class="required">*</span></label>
                        <input type="password" name="password" required class="form-control" minlength="6">
                    </div>
                    <div class="form-group mb-0">
                        <label class="form-label">Confirm Password <span class="required">*</span></label>
                        <input type="password" name="password_confirm" required class="form-control">
                    </div>
                </div>
                <label class="form-check mb-5">
                    <input type="checkbox" required> I agree to the <a href="<?= BASE_URL ?>/terms" class="text-primary-600 hover:underline" target="_blank">Terms & Conditions</a>
                </label>
                <button type="submit" class="btn btn-primary btn-lg w-full">
                    <i data-lucide="user-plus" class="w-4 h-4"></i> Create Account
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-500">Already have an account? <a href="<?= BASE_URL ?>/login" class="text-primary-600 hover:text-primary-700 font-semibold">Sign In</a></p>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/footer.php'; ?>
