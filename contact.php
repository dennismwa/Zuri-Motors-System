<?php
require_once __DIR__ . '/functions.php';
$companyName = Settings::get('company_name', 'Zuri Motors');
$phone = Settings::get('phone'); $email = Settings::get('email');
$address = Settings::get('address'); $mapEmbed = Settings::get('google_maps_embed');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CSRF::verifyOrDie();
    $result = saveInquiry([
        'type' => 'general', 'name' => $_POST['name'] ?? '', 'email' => $_POST['email'] ?? '',
        'phone' => $_POST['phone'] ?? '', 'message' => $_POST['message'] ?? '', 'source' => 'contact_page'
    ]);
    if ($result['success']) { Flash::success('Message sent successfully! We\'ll get back to you soon.'); }
    else { Flash::error('Failed to send message. Please try again.'); }
    redirect(BASE_URL . '/contact');
}

$pageSeo = seoMeta('Contact Us - ' . $companyName, 'Get in touch with ' . $companyName . '. Call, email, or visit us.');
include __DIR__ . '/header.php';
?>

<div class="bg-gray-50 border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 lg:px-6 py-4">
        <?= breadcrumbs([['label' => 'Home', 'url' => BASE_URL . '/'], ['label' => 'Contact Us']]) ?>
    </div>
</div>

<section class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 lg:px-6">
        <div class="text-center mb-12">
            <span class="section-badge"><i data-lucide="phone" class="w-4 h-4"></i> Get in Touch</span>
            <h1 class="section-title mt-2">Contact Us</h1>
            <p class="section-subtitle mx-auto">Have a question or need assistance? We're here to help.</p>
        </div>

        <!-- Contact Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-12">
            <div class="bg-white rounded-2xl border border-gray-200 p-6 text-center hover:border-primary-200 hover:shadow-lg transition-all">
                <div class="w-14 h-14 bg-primary-50 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="phone" class="w-6 h-6 text-primary-600"></i>
                </div>
                <h3 class="font-bold text-dark-900 mb-1">Call Us</h3>
                <a href="tel:<?= str_replace(' ', '', $phone) ?>" class="text-sm text-primary-600 hover:underline"><?= clean($phone) ?></a>
            </div>
            <div class="bg-white rounded-2xl border border-gray-200 p-6 text-center hover:border-primary-200 hover:shadow-lg transition-all">
                <div class="w-14 h-14 bg-blue-50 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="mail" class="w-6 h-6 text-blue-600"></i>
                </div>
                <h3 class="font-bold text-dark-900 mb-1">Email Us</h3>
                <a href="mailto:<?= clean($email) ?>" class="text-sm text-primary-600 hover:underline"><?= clean($email) ?></a>
            </div>
            <div class="bg-white rounded-2xl border border-gray-200 p-6 text-center hover:border-primary-200 hover:shadow-lg transition-all">
                <div class="w-14 h-14 bg-emerald-50 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="message-circle" class="w-6 h-6 text-emerald-600"></i>
                </div>
                <h3 class="font-bold text-dark-900 mb-1">WhatsApp</h3>
                <a href="<?= whatsappLink() ?>" target="_blank" class="text-sm text-primary-600 hover:underline">Chat Now</a>
            </div>
            <div class="bg-white rounded-2xl border border-gray-200 p-6 text-center hover:border-primary-200 hover:shadow-lg transition-all">
                <div class="w-14 h-14 bg-amber-50 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="map-pin" class="w-6 h-6 text-amber-600"></i>
                </div>
                <h3 class="font-bold text-dark-900 mb-1">Visit Us</h3>
                <p class="text-sm text-gray-500"><?= clean($address) ?></p>
            </div>
        </div>

        <div class="grid lg:grid-cols-2 gap-8">
            <!-- Form -->
            <div class="bg-white rounded-2xl border border-gray-200 p-6 lg:p-8">
                <h2 class="text-xl font-bold text-dark-900 mb-6">Send us a Message</h2>
                <form method="POST">
                    <?= CSRF::field() ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div class="form-group mb-0">
                            <label class="form-label">Full Name <span class="required">*</span></label>
                            <input type="text" name="name" required class="form-control" placeholder="Your name">
                        </div>
                        <div class="form-group mb-0">
                            <label class="form-label">Phone <span class="required">*</span></label>
                            <input type="tel" name="phone" required class="form-control" placeholder="+254 700 000 000">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="your@email.com">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Subject</label>
                        <input type="text" name="subject" class="form-control" placeholder="How can we help?" value="<?= clean($_GET['subject'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Message <span class="required">*</span></label>
                        <textarea name="message" required class="form-control" rows="5" placeholder="Your message..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg w-full">
                        <i data-lucide="send" class="w-4 h-4"></i> Send Message
                    </button>
                </form>
            </div>

            <!-- Map -->
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                <?php if ($mapEmbed): ?>
                    <iframe src="<?= clean($mapEmbed) ?>" width="100%" height="100%" style="min-height:480px;border:0;" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                <?php else: ?>
                    <div class="flex items-center justify-center h-full bg-gray-100 min-h-[480px]">
                        <div class="text-center p-8">
                            <i data-lucide="map" class="w-12 h-12 text-gray-300 mx-auto mb-4"></i>
                            <p class="text-gray-500">Map will be displayed here</p>
                            <p class="text-sm text-gray-400 mt-1"><?= clean($address) ?></p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/footer.php'; ?>
