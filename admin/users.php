<?php
$pageTitle = 'Users';
require_once dirname(__DIR__) . '/functions.php';

if (isset($_GET['delete'])) { deleteUser((int)$_GET['delete']); Flash::success('User deleted.'); redirect(BASE_URL.'/admin/users'); }
if (isset($_GET['toggle'])) { $uid=(int)$_GET['toggle']; $u=getUser($uid); if($u) db()->prepare("UPDATE users SET is_active=? WHERE id=?")->execute([$u['is_active']?0:1,$uid]); Flash::success('Status updated.'); redirect(BASE_URL.'/admin/users'); }

$filters = ['role'=>$_GET['role']??'', 'search'=>$_GET['search']??''];
$total = countUsers(array_filter($filters));
$pagination = new Pagination($total, 20);
$users = getUsers(array_filter($filters), $pagination->perPage, $pagination->offset);
include __DIR__ . '/header.php';
?>

<div class="flex items-center justify-between mb-5">
    <form method="GET" class="flex items-end gap-3">
        <div><input type="text" name="search" value="<?= clean($filters['search']) ?>" class="filter-input text-sm" placeholder="Search users..."></div>
        <div><select name="role" class="filter-select text-sm"><option value="">All Roles</option><option value="admin" <?= $filters['role']==='admin'?'selected':'' ?>>Admin</option><option value="staff" <?= $filters['role']==='staff'?'selected':'' ?>>Staff</option><option value="vendor" <?= $filters['role']==='vendor'?'selected':'' ?>>Vendor</option><option value="customer" <?= $filters['role']==='customer'?'selected':'' ?>>Customer</option></select></div>
        <button type="submit" class="btn btn-primary btn-sm"><i data-lucide="search" class="w-4 h-4"></i></button>
    </form>
    <a href="<?= BASE_URL ?>/admin/user-add" class="btn btn-primary btn-sm"><i data-lucide="user-plus" class="w-4 h-4"></i> Add User</a>
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="data-table">
        <thead><tr><th>User</th><th>Email</th><th>Role</th><th>Company</th><th>Active</th><th>Joined</th><th class="text-right">Actions</th></tr></thead>
        <tbody>
        <?php foreach($users as $u): ?>
        <tr>
            <td><div class="flex items-center gap-2"><div class="w-8 h-8 rounded-lg bg-primary-100 text-primary-700 flex items-center justify-center text-xs font-bold"><?= strtoupper(substr($u['first_name'],0,1).substr($u['last_name'],0,1)) ?></div><span class="font-semibold text-sm"><?= clean($u['first_name'].' '.$u['last_name']) ?></span></div></td>
            <td class="text-sm"><?= clean($u['email']) ?></td>
            <td><?= statusBadge($u['role']) ?></td>
            <td class="text-sm text-gray-500"><?= clean($u['company_name'] ?: '-') ?></td>
            <td><?= $u['is_active'] ? '<span class="text-emerald-500 text-xs font-bold">Active</span>' : '<span class="text-red-500 text-xs font-bold">Inactive</span>' ?></td>
            <td class="text-xs text-gray-400"><?= formatDate($u['created_at'], 'd M Y') ?></td>
            <td><div class="actions justify-end">
                <a href="<?= BASE_URL ?>/admin/user-edit/<?= $u['id'] ?>" class="btn btn-icon btn-sm btn-ghost"><i data-lucide="pencil" class="w-4 h-4"></i></a>
                <a href="?toggle=<?= $u['id'] ?>" class="btn btn-icon btn-sm btn-ghost" title="Toggle Active"><i data-lucide="<?= $u['is_active']?'pause':'play' ?>" class="w-4 h-4"></i></a>
                <?php if($u['id'] != 1): ?><button onclick="confirmAction('Delete user?','?delete=<?= $u['id'] ?>')" class="btn btn-icon btn-sm btn-ghost text-red-500"><i data-lucide="trash-2" class="w-4 h-4"></i></button><?php endif; ?>
            </div></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <div class="p-4"><?= $pagination->render(BASE_URL.'/admin/users', array_filter($filters)) ?></div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
