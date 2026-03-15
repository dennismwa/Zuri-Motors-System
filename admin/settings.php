<?php
$pageTitle = 'Site Settings';
require_once dirname(__DIR__) . '/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CSRF::verifyOrDie();
    $group = $_POST['group'] ?? 'general';
    unset($_POST['csrf_token'], $_POST['group']);
    foreach ($_POST as $key => $value) {
        Settings::set($key, $value, $group);
    }
    // Handle payment methods
    if (isset($_POST['pm_name'])) {
        foreach ($_POST['pm_name'] as $pmId => $pmName) {
            $isActive = isset($_POST['pm_active'][$pmId]) ? 1 : 0;
            db()->prepare("UPDATE payment_methods SET name=?, is_active=? WHERE id=?")->execute([$pmName, $isActive, $pmId]);
        }
    }
    Settings::clearCache();
    Flash::success('Settings saved successfully.');
    redirect(BASE_URL . '/admin/settings#' . $group);
}

$groups = ['general'=>'General','contact'=>'Contact','social'=>'Social Media','seo'=>'SEO','finance'=>'Finance','features'=>'Features','homepage'=>'Homepage','email'=>'Email'];
$paymentMethods = getPaymentMethods(false);
include __DIR__ . '/header.php';
?>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    <!-- Tabs -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl border border-gray-200 p-3 sticky top-24">
            <nav class="space-y-1">
                <?php foreach($groups as $key=>$label): ?>
                <a href="#<?= $key ?>" onclick="showSettingsTab('<?= $key ?>')" id="tab-<?= $key ?>" class="settings-tab flex items-center gap-2 px-3 py-2 text-sm rounded-lg transition text-gray-600 hover:bg-gray-50">
                    <?= $label ?>
                </a>
                <?php endforeach; ?>
                <a href="#payments" onclick="showSettingsTab('payments')" id="tab-payments" class="settings-tab flex items-center gap-2 px-3 py-2 text-sm rounded-lg transition text-gray-600 hover:bg-gray-50">Payment Methods</a>
            </nav>
        </div>
    </div>

    <div class="lg:col-span-3">
        <?php foreach($groups as $key=>$label): ?>
        <div class="settings-panel hidden" id="panel-<?= $key ?>">
            <form method="POST" class="bg-white rounded-xl border border-gray-200 p-6">
                <?= CSRF::field() ?>
                <input type="hidden" name="group" value="<?= $key ?>">
                <h3 class="text-lg font-bold text-dark-900 mb-5"><?= $label ?> Settings</h3>
                
                <?php
                $settings = Settings::getByGroup($key);
                foreach ($settings as $sKey => $sVal):
                    $labelText = ucwords(str_replace('_', ' ', $sKey));
                    $isTextarea = in_array($sKey, ['meta_description','meta_keywords','hero_subtitle','google_maps_embed']);
                ?>
                <div class="form-group">
                    <label class="form-label"><?= $labelText ?></label>
                    <?php if ($isTextarea): ?>
                    <textarea name="<?= $sKey ?>" class="form-control" rows="3"><?= clean($sVal) ?></textarea>
                    <?php elseif (in_array($sKey, ['maintenance_mode','enable_tax','enable_financing','enable_chat','enable_compare','enable_favorites','enable_vendor_registration'])): ?>
                    <select name="<?= $sKey ?>" class="form-control">
                        <option value="1" <?= $sVal==='1'?'selected':'' ?>>Enabled</option>
                        <option value="0" <?= $sVal==='0'?'selected':'' ?>>Disabled</option>
                    </select>
                    <?php else: ?>
                    <input type="text" name="<?= $sKey ?>" class="form-control" value="<?= clean($sVal) ?>">
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
                
                <button type="submit" class="btn btn-primary"><i data-lucide="save" class="w-4 h-4"></i> Save <?= $label ?> Settings</button>
            </form>
        </div>
        <?php endforeach; ?>

        <!-- Payment Methods -->
        <div class="settings-panel hidden" id="panel-payments">
            <form method="POST" class="bg-white rounded-xl border border-gray-200 p-6">
                <?= CSRF::field() ?>
                <input type="hidden" name="group" value="finance">
                <h3 class="text-lg font-bold text-dark-900 mb-5">Payment Methods</h3>
                <div class="space-y-3">
                    <?php foreach($paymentMethods as $pm): ?>
                    <div class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg">
                        <i data-lucide="<?= $pm['icon'] ?: 'credit-card' ?>" class="w-5 h-5 text-gray-500"></i>
                        <input type="text" name="pm_name[<?= $pm['id'] ?>]" class="form-control flex-1 py-1.5" value="<?= clean($pm['name']) ?>">
                        <label class="form-check text-xs whitespace-nowrap"><input type="checkbox" name="pm_active[<?= $pm['id'] ?>]" value="1" <?= $pm['is_active']?'checked':'' ?>> Active</label>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button type="submit" class="btn btn-primary mt-4"><i data-lucide="save" class="w-4 h-4"></i> Save Payment Methods</button>
            </form>
        </div>
    </div>
</div>

<?php $pageScripts = <<<'JS'
<script>
function showSettingsTab(key) {
    document.querySelectorAll('.settings-panel').forEach(p => p.classList.add('hidden'));
    document.querySelectorAll('.settings-tab').forEach(t => t.classList.remove('bg-primary-50','text-primary-700','font-semibold'));
    const panel = document.getElementById('panel-' + key);
    const tab = document.getElementById('tab-' + key);
    if (panel) panel.classList.remove('hidden');
    if (tab) tab.classList.add('bg-primary-50', 'text-primary-700', 'font-semibold');
}
showSettingsTab(location.hash.replace('#','') || 'general');
window.addEventListener('hashchange', () => showSettingsTab(location.hash.replace('#','')));
</script>
JS;
include __DIR__ . '/footer.php'; ?>
