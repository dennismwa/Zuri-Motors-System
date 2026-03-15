<?php
require_once __DIR__ . '/functions.php';
$companyName = Settings::get('company_name', 'Zuri Motors');
$page = getPage('terms');
$pageSeo = seoMeta('Terms & Conditions - ' . $companyName, 'Read the terms and conditions for using ' . $companyName);
include __DIR__ . '/header.php';
?>

<div class="bg-gray-50 border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 lg:px-6 py-4">
        <?= breadcrumbs([['label'=>'Home','url'=>BASE_URL.'/'],['label'=>'Terms & Conditions']]) ?>
    </div>
</div>

<section class="py-12 bg-gray-50">
    <div class="max-w-3xl mx-auto px-4 lg:px-6">
        <div class="bg-white rounded-2xl border border-gray-200 p-6 lg:p-10">
            <h1 class="text-2xl lg:text-3xl font-bold text-dark-900 font-display mb-2">Terms & Conditions</h1>
            <p class="text-sm text-gray-400 mb-8">Last updated: <?= date('F Y') ?></p>

            <?php if ($page && $page['content']): ?>
                <div class="prose prose-sm max-w-none text-gray-600"><?= $page['content'] ?></div>
            <?php else: ?>
                <div class="prose prose-sm max-w-none text-gray-600 space-y-6">
                    <p>Welcome to <?= clean($companyName) ?>. These Terms and Conditions govern your use of our website and services. By accessing or using our platform, you agree to comply with these terms.</p>
                    
                    <h2 class="text-lg font-bold text-dark-900">1. Use of Service</h2>
                    <p>Our platform provides an automotive marketplace connecting buyers and sellers. You must be at least 18 years of age to use our services. You are responsible for maintaining the confidentiality of your account.</p>

                    <h2 class="text-lg font-bold text-dark-900">2. Listings & Content</h2>
                    <p>Sellers are responsible for the accuracy of their listings. <?= clean($companyName) ?> reserves the right to remove any listing that violates our policies or contains inaccurate information.</p>

                    <h2 class="text-lg font-bold text-dark-900">3. Transactions</h2>
                    <p><?= clean($companyName) ?> facilitates connections between buyers and sellers. We are not a party to any transaction between users unless explicitly stated. All transactions are conducted at the parties' own risk.</p>

                    <h2 class="text-lg font-bold text-dark-900">4. Payments</h2>
                    <p>Payment terms are agreed between the buyer and seller. <?= clean($companyName) ?> may offer payment facilitation services but is not responsible for disputes between parties.</p>

                    <h2 class="text-lg font-bold text-dark-900">5. Liability</h2>
                    <p><?= clean($companyName) ?> provides this platform "as is" and makes no warranties regarding the vehicles listed. We recommend independent inspection of all vehicles before purchase.</p>

                    <h2 class="text-lg font-bold text-dark-900">6. Privacy</h2>
                    <p>Your use of our platform is also governed by our <a href="<?= BASE_URL ?>/privacy" class="text-primary-600 hover:underline">Privacy Policy</a>.</p>

                    <h2 class="text-lg font-bold text-dark-900">7. Contact</h2>
                    <p>For questions about these terms, contact us at <a href="mailto:<?= Settings::get('email') ?>" class="text-primary-600 hover:underline"><?= Settings::get('email') ?></a>.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include __DIR__ . '/footer.php'; ?>
