<?php
$pageTitle = 'Edit User';
require_once dirname(__DIR__) . '/functions.php';
$id = (int)($_GET['id'] ?? 0); $user = getUser($id);
if (!$user) { Flash::error('User not found.'); redirect(BASE_URL.'/admin/users'); }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CSRF::verifyOrDie();
    $data = $_POST; $data['is_active'] = isset($_POST['is_active'])?1:0; $data['is_verified'] = isset($_POST['is_verified'])?1:0;
    $result = saveUser($data, $id);
    if ($result['success']) { Flash::success('User updated.'); redirect(BASE_URL.'/admin/user-edit/'.$id); }
    else Flash::error($result['message']);
}
include __DIR__ . '/header.php';
?>
<a href="<?= BASE_URL ?>/admin/users" class="btn btn-ghost btn-sm mb-4"><i data-lucide="arrow-left" class="w-4 h-4"></i> Back</a>
<div class="max-w-2xl">
<div class="bg-white rounded-xl border border-gray-200 p-6">
    <form method="POST"><?= CSRF::field() ?>
        <div class="grid grid-cols-2 gap-4">
            <div class="form-group"><label class="form-label">First Name *</label><input type="text" name="first_name" required class="form-control" value="<?= clean($user['first_name']) ?>"></div>
            <div class="form-group"><label class="form-label">Last Name *</label><input type="text" name="last_name" required class="form-control" value="<?= clean($user['last_name']) ?>"></div>
        </div>
        <div class="form-group"><label class="form-label">Email *</label><input type="email" name="email" required class="form-control" value="<?= clean($user['email']) ?>"></div>
        <div class="grid grid-cols-2 gap-4">
            <div class="form-group"><label class="form-label">Phone</label><input type="tel" name="phone" class="form-control" value="<?= clean($user['phone']) ?>"></div>
            <div class="form-group"><label class="form-label">Role</label><select name="role" class="form-control"><?php foreach(['customer','vendor','staff','admin'] as $r): ?><option value="<?= $r ?>" <?= $user['role']===$r?'selected':'' ?>><?= ucfirst($r) ?></option><?php endforeach; ?></select></div>
        </div>
        <div class="form-group"><label class="form-label">Company</label><input type="text" name="company_name" class="form-control" value="<?= clean($user['company_name']) ?>"></div>
        <div class="grid grid-cols-2 gap-4">
            <div class="form-group"><label class="form-label">New Password</label><input type="password" name="password" class="form-control" placeholder="Leave empty to keep current"></div>
            <div class="form-group"><label class="form-label">City</label><input type="text" name="city" class="form-control" value="<?= clean($user['city']) ?>"></div>
        </div>
        <div class="form-group"><label class="form-label">WhatsApp</label><input type="text" name="whatsapp" class="form-control" value="<?= clean($user['whatsapp']) ?>"></div>
        <div class="form-group"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2"><?= clean($user['notes']) ?></textarea></div>
        <div class="flex gap-4"><label class="form-check"><input type="checkbox" name="is_active" value="1" <?= $user['is_active']?'checked':'' ?>> Active</label><label class="form-check"><input type="checkbox" name="is_verified" value="1" <?= $user['is_verified']?'checked':'' ?>> Verified</label></div>
        <div class="text-xs text-gray-400 mt-3">Joined: <?= formatDate($user['created_at']) ?> · Last login: <?= $user['last_login'] ? formatDate($user['last_login'], 'd M Y H:i') : 'Never' ?> · Logins: <?= $user['login_count'] ?></div>
        <button type="submit" class="btn btn-primary mt-4"><i data-lucide="save" class="w-4 h-4"></i> Update User</button>
    </form>
</div>
</div>
<?php include __DIR__ . '/footer.php'; ?>
