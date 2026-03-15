<?php
require_once __DIR__ . '/functions.php';
$companyName = Settings::get('company_name', 'Zuri Motors');
$page = getPage('privacy');
$pageSeo = seoMeta('Privacy Policy - ' . $companyName, 'Read how ' . $companyName . ' handles your personal data.');
include __DIR__ . '/header.php';
?>

<div class="bg-gray-50 border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 lg:px-6 py-4">
        <?= breadcrumbs([['label'=>'Home','url'=>BASE_URL.'/'],['label'=>'Privacy Policy']]) ?>
    </div>
</div>

<section class="py-12 bg-gray-50">
    <div class="max-w-3xl mx-auto px-4 lg:px-6">
        <div class="bg-white rounded-2xl border border-gray-200 p-6 lg:p-10">
            <h1 class="text-2xl lg:text-3xl font-bold text-dark-900 font-display mb-2">Privacy Policy</h1>
            <p class="text-sm text-gray-400 mb-8">Last updated: <?= date('F Y') ?></p>

            <?php if ($page && $page['content']): ?>
                <div class="prose prose-sm max-w-none text-gray-600"><?= $page['content'] ?></div>
            <?php else: ?>
                <div class="prose prose-sm max-w-none text-gray-600 space-y-6">
                    <p><?= clean($companyName) ?> is committed to protecting your privacy. This policy explains how we collect, use, and safeguard your personal information.</p>
                    
                    <h2 class="text-lg font-bold text-dark-900">Information We Collect</h2>
                    <p>We collect information you provide when creating an account, submitting inquiries, or listing vehicles. This includes your name, email, phone number, and vehicle details. We also collect usage data such as IP address and browser type.</p>

                    <h2 class="text-lg font-bold text-dark-900">How We Use Your Information</h2>
                    <p>We use your information to provide our services, facilitate communication between buyers and sellers, improve our platform, send relevant notifications, and ensure the security of our marketplace.</p>

                    <h2 class="text-lg font-bold text-dark-900">Data Sharing</h2>
                    <p>We do not sell your personal data. We may share information with sellers when you make an inquiry, payment processors for transactions, and law enforcement when required by law.</p>

                    <h2 class="text-lg font-bold text-dark-900">Data Security</h2>
                    <p>We implement appropriate security measures to protect your personal data, including encryption, secure servers, and regular security assessments.</p>

                    <h2 class="text-lg font-bold text-dark-900">Cookies</h2>
                    <p>We use cookies to improve your experience, remember preferences, and analyze site usage. You can manage cookie preferences through your browser settings.</p>

                    <h2 class="text-lg font-bold text-dark-900">Your Rights</h2>
                    <p>You have the right to access, correct, or delete your personal data. Contact us at <a href="mailto:<?= Settings::get('email') ?>" class="text-primary-600 hover:underline"><?= Settings::get('email') ?></a> for any data-related requests.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include __DIR__ . '/footer.php'; ?>
