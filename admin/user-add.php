<?php
$pageTitle = 'Add User';
require_once dirname(__DIR__) . '/functions.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CSRF::verifyOrDie();
    $result = saveUser($_POST);
    if ($result['success']) { Flash::success('User created.'); redirect(BASE_URL.'/admin/users'); }
    else Flash::error($result['message']);
}
include __DIR__ . '/header.php';
?>
<a href="<?= BASE_URL ?>/admin/users" class="btn btn-ghost btn-sm mb-4"><i data-lucide="arrow-left" class="w-4 h-4"></i> Back</a>
<div class="max-w-2xl">
<div class="bg-white rounded-xl border border-gray-200 p-6">
    <form method="POST"><?= CSRF::field() ?>
        <div class="grid grid-cols-2 gap-4">
            <div class="form-group"><label class="form-label">First Name *</label><input type="text" name="first_name" required class="form-control"></div>
            <div class="form-group"><label class="form-label">Last Name *</label><input type="text" name="last_name" required class="form-control"></div>
        </div>
        <div class="form-group"><label class="form-label">Email *</label><input type="email" name="email" required class="form-control"></div>
        <div class="grid grid-cols-2 gap-4">
            <div class="form-group"><label class="form-label">Phone</label><input type="tel" name="phone" class="form-control"></div>
            <div class="form-group"><label class="form-label">Role *</label><select name="role" class="form-control"><option value="customer">Customer</option><option value="vendor">Vendor</option><option value="staff">Staff</option><option value="admin">Admin</option></select></div>
        </div>
        <div class="form-group"><label class="form-label">Company</label><input type="text" name="company_name" class="form-control"></div>
        <div class="grid grid-cols-2 gap-4">
            <div class="form-group"><label class="form-label">Password *</label><input type="password" name="password" required class="form-control" minlength="6"></div>
            <div class="form-group"><label class="form-label">City</label><input type="text" name="city" class="form-control"></div>
        </div>
        <div class="form-group"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
        <div class="flex gap-2"><label class="form-check"><input type="checkbox" name="is_active" value="1" checked> Active</label><label class="form-check"><input type="checkbox" name="is_verified" value="1"> Verified</label></div>
        <button type="submit" class="btn btn-primary mt-4"><i data-lucide="save" class="w-4 h-4"></i> Create User</button>
    </form>
</div>
</div>
<?php include __DIR__ . '/footer.php'; ?>
