<?php
require_once __DIR__ . '/functions.php';
$companyName = Settings::get('company_name', 'Zuri Motors');
$page = getPage('faq');
$pageSeo = seoMeta('FAQ - ' . $companyName, 'Find answers to frequently asked questions about buying and selling cars at ' . $companyName);
include __DIR__ . '/header.php';

$faqs = [
    ['q' => 'How do I buy a car on ' . $companyName . '?', 'a' => 'Browse our listings, find a car you like, and contact the seller via phone, WhatsApp, or our inquiry form. Schedule a viewing, inspect the vehicle, and complete the purchase with our secure process.'],
    ['q' => 'Are the vehicles inspected?', 'a' => 'Yes, all featured vehicles go through a verification process. We recommend you also do an independent inspection before purchase for your peace of mind.'],
    ['q' => 'How do I sell my car?', 'a' => 'Click "Sell Your Car" in the menu, fill in your vehicle details, and our team will contact you with a valuation. You can also reach us on WhatsApp for faster service.'],
    ['q' => 'Do you offer financing?', 'a' => 'Yes, we partner with leading financial institutions to offer competitive financing options. Use our payment calculator to estimate monthly payments, then apply through our platform.'],
    ['q' => 'What payment methods do you accept?', 'a' => 'We accept M-Pesa, bank transfers, cheques, and cash payments. Credit card payments are available for deposits. All transactions are secured and documented.'],
    ['q' => 'Can I test drive before buying?', 'a' => 'Absolutely! We encourage test drives for all vehicles. Use the "Request Test Drive" button on any listing page to schedule a convenient time.'],
    ['q' => 'What if I find an issue after purchase?', 'a' => 'We stand behind the quality of vehicles listed on our platform. Contact our support team immediately if you discover any undisclosed issues. Select vehicles come with warranty coverage.'],
    ['q' => 'How do I contact support?', 'a' => 'You can reach us via phone, email, WhatsApp, or our live chat. Our team is available Monday to Saturday, 8am to 6pm, and via WhatsApp 24/7.'],
];
?>

<div class="bg-gray-50 border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 lg:px-6 py-4">
        <?= breadcrumbs([['label'=>'Home','url'=>BASE_URL.'/'],['label'=>'FAQ']]) ?>
    </div>
</div>

<section class="py-12 bg-gray-50">
    <div class="max-w-3xl mx-auto px-4 lg:px-6">
        <div class="text-center mb-10">
            <span class="section-badge"><i data-lucide="help-circle" class="w-4 h-4"></i> FAQ</span>
            <h1 class="section-title mt-2">Frequently Asked Questions</h1>
            <p class="section-subtitle mx-auto">Everything you need to know about buying and selling on <?= clean($companyName) ?></p>
        </div>

        <div class="space-y-3">
            <?php foreach ($faqs as $i => $faq): ?>
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden faq-item">
                <button onclick="toggleFaq(this)" class="w-full px-6 py-4 text-left flex items-center justify-between gap-4 hover:bg-gray-50 transition">
                    <span class="font-semibold text-dark-900 text-sm"><?= clean($faq['q']) ?></span>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400 flex-shrink-0 faq-icon transition-transform"></i>
                </button>
                <div class="faq-answer hidden px-6 pb-4">
                    <p class="text-sm text-gray-600 leading-relaxed"><?= clean($faq['a']) ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-10 bg-white rounded-2xl border border-gray-200 p-8">
            <h3 class="font-bold text-dark-900 mb-2">Still have questions?</h3>
            <p class="text-sm text-gray-500 mb-4">Our team is happy to help.</p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="<?= BASE_URL ?>/contact" class="btn btn-primary"><i data-lucide="mail" class="w-4 h-4"></i> Contact Us</a>
                <a href="<?= whatsappLink('Hi, I have a question...') ?>" target="_blank" class="btn bg-[#25D366] text-white hover:bg-[#20BD5A]">
                    <i data-lucide="message-circle" class="w-4 h-4"></i> WhatsApp
                </a>
            </div>
        </div>
    </div>
</section>

<script>
function toggleFaq(btn) {
    const item = btn.parentElement;
    const answer = item.querySelector('.faq-answer');
    const icon = item.querySelector('.faq-icon');
    const isOpen = !answer.classList.contains('hidden');
    // Close all
    document.querySelectorAll('.faq-answer').forEach(a => a.classList.add('hidden'));
    document.querySelectorAll('.faq-icon').forEach(i => i.style.transform = 'rotate(0deg)');
    if (!isOpen) {
        answer.classList.remove('hidden');
        icon.style.transform = 'rotate(180deg)';
    }
}
</script>

<?php include __DIR__ . '/footer.php'; ?>
