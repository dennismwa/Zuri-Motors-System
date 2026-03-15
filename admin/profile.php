<?php
$pageTitle = 'My Profile';
require_once dirname(__DIR__) . '/functions.php';
$user = Auth::user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CSRF::verifyOrDie();
    $data = $_POST; $data['role'] = $user['role']; $data['is_active'] = 1; $data['is_verified'] = $user['is_verified'];
    $result = saveUser($data, Auth::id());
    if ($result['success']) { $_SESSION['user_data'] = null; Flash::success('Profile updated.'); }
    else Flash::error($result['message']);
    redirect(BASE_URL . '/admin/profile');
}
include __DIR__ . '/header.php';
?>
<div class="max-w-2xl">
<div class="bg-white rounded-xl border border-gray-200 p-6">
    <div class="flex items-center gap-4 mb-6">
        <div class="w-16 h-16 rounded-2xl bg-primary-100 text-primary-700 flex items-center justify-center text-2xl font-bold"><?= strtoupper(substr($user['first_name'],0,1).substr($user['last_name'],0,1)) ?></div>
        <div><h3 class="text-lg font-bold text-dark-900"><?= clean($user['first_name'].' '.$user['last_name']) ?></h3><p class="text-sm text-gray-500"><?= clean($user['email']) ?> · <?= ucfirst($user['role']) ?></p></div>
    </div>
    <form method="POST"><?= CSRF::field() ?>
        <div class="grid grid-cols-2 gap-4">
            <div class="form-group"><label class="form-label">First Name</label><input type="text" name="first_name" required class="form-control" value="<?= clean($user['first_name']) ?>"></div>
            <div class="form-group"><label class="form-label">Last Name</label><input type="text" name="last_name" required class="form-control" value="<?= clean($user['last_name']) ?>"></div>
        </div>
        <div class="form-group"><label class="form-label">Email</label><input type="email" name="email" required class="form-control" value="<?= clean($user['email']) ?>"></div>
        <div class="form-group"><label class="form-label">Phone</label><input type="tel" name="phone" class="form-control" value="<?= clean($user['phone']) ?>"></div>
        <div class="form-group"><label class="form-label">New Password</label><input type="password" name="password" class="form-control" placeholder="Leave empty to keep current" minlength="6"></div>
        <button type="submit" class="btn btn-primary"><i data-lucide="save" class="w-4 h-4"></i> Update Profile</button>
    </form>
</div>
</div>
<?php include __DIR__ . '/footer.php'; ?>
