<?php
$companyName = Settings::get('company_name', 'Zuri Motors');
$logo = Settings::get('logo', 'assets/images/ZuriMotors -logo.png');
$phone = Settings::get('phone', '+254 700 000 000');
$email = Settings::get('email', 'info@zurimotors.com');
$whatsapp = Settings::get('whatsapp', '254700000000');
$address = Settings::get('address', 'Westlands, Nairobi, Kenya');
$enableChat = Settings::get('enable_chat', '1');

$footerBrands = getBrands(true, true);
$footerCategories = getCategories(true);
$socialLinks = [
    'facebook'  => Settings::get('facebook'),
    'twitter'   => Settings::get('twitter'),
    'instagram' => Settings::get('instagram'),
    'youtube'   => Settings::get('youtube'),
    'linkedin'  => Settings::get('linkedin'),
    'tiktok'    => Settings::get('tiktok'),
];
$socialLinks = array_filter($socialLinks);
?>

<!-- ============================================================ -->
<!-- NEWSLETTER SECTION -->
<!-- ============================================================ -->
<section class="bg-dark-900 relative overflow-hidden">
    <div class="absolute inset-0 opacity-5">
        <div class="absolute top-0 right-0 w-96 h-96 bg-primary-500 rounded-full -translate-y-1/2 translate-x-1/2 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-primary-500 rounded-full translate-y-1/2 -translate-x-1/2 blur-3xl"></div>
    </div>
    <div class="max-w-7xl mx-auto px-4 lg:px-6 py-16 relative">
        <div class="flex flex-col lg:flex-row items-center justify-between gap-8">
            <div class="text-center lg:text-left">
                <h3 class="text-2xl lg:text-3xl font-bold text-white mb-2">Stay Updated with <?= clean($companyName) ?></h3>
                <p class="text-white/60">Get the latest deals, new arrivals, and automotive news delivered to your inbox.</p>
            </div>
            <form class="flex w-full max-w-lg" onsubmit="event.preventDefault(); this.querySelector('button').textContent='Subscribed!'; this.querySelector('button').classList.add('bg-primary-700');">
                <input type="email" placeholder="Enter your email address" required
                       class="flex-1 bg-white/10 border border-white/10 text-white placeholder:text-white/40 px-5 py-3.5 rounded-l-xl outline-none focus:border-primary-500 transition">
                <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-semibold px-6 py-3.5 rounded-r-xl transition whitespace-nowrap">
                    Subscribe
                </button>
            </form>
        </div>
    </div>
</section>

<!-- ============================================================ -->
<!-- MAIN FOOTER -->
<!-- ============================================================ -->
<footer class="bg-dark-950 text-white/70">
    <div class="max-w-7xl mx-auto px-4 lg:px-6 pt-16 pb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 lg:gap-12">
            
            <!-- Company Info -->
            <div>
                <a href="<?= BASE_URL ?>/" class="flex items-center gap-2.5 mb-5">
                    <?php if ($logo): $logoUrlFooter = str_starts_with($logo, 'http') || str_starts_with($logo, '//') ? $logo : BASE_URL . '/' . str_replace(' ', '%20', ltrim($logo, '/')); ?>
                    <img src="<?= htmlspecialchars($logoUrlFooter) ?>" alt="<?= clean($companyName) ?>" class="h-11 w-auto max-w-[180px] object-contain">
                    <?php else: ?>
                    <div class="w-9 h-9 bg-primary-600 rounded-xl flex items-center justify-center">
                        <i data-lucide="car" class="w-5 h-5 text-white"></i>
                    </div>
                    <?php endif; ?>
                </a>
                <p class="text-sm leading-relaxed mb-6">
                    Your trusted automotive marketplace. We connect buyers with quality vehicles from verified dealers and private sellers across Kenya.
                </p>
                <div class="space-y-3">
                    <a href="tel:<?= str_replace(' ', '', $phone) ?>" class="flex items-center gap-3 text-sm hover:text-primary-400 transition">
                        <div class="w-8 h-8 bg-white/5 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i data-lucide="phone" class="w-4 h-4"></i>
                        </div>
                        <?= clean($phone) ?>
                    </a>
                    <a href="mailto:<?= clean($email) ?>" class="flex items-center gap-3 text-sm hover:text-primary-400 transition">
                        <div class="w-8 h-8 bg-white/5 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i data-lucide="mail" class="w-4 h-4"></i>
                        </div>
                        <?= clean($email) ?>
                    </a>
                    <div class="flex items-center gap-3 text-sm">
                        <div class="w-8 h-8 bg-white/5 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i data-lucide="map-pin" class="w-4 h-4"></i>
                        </div>
                        <?= clean($address) ?>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div>
                <h4 class="text-white font-semibold mb-5">Quick Links</h4>
                <ul class="space-y-2.5">
                    <li><a href="<?= BASE_URL ?>/cars" class="text-sm hover:text-primary-400 transition">Browse Cars</a></li>
                    <li><a href="<?= BASE_URL ?>/cars?condition_type=new" class="text-sm hover:text-primary-400 transition">New Cars</a></li>
                    <li><a href="<?= BASE_URL ?>/cars?condition_type=used" class="text-sm hover:text-primary-400 transition">Used Cars</a></li>
                    <li><a href="<?= BASE_URL ?>/cars?is_offer=1" class="text-sm hover:text-primary-400 transition">Special Offers</a></li>
                    <li><a href="<?= BASE_URL ?>/sell-your-car" class="text-sm hover:text-primary-400 transition">Sell Your Car</a></li>
                    <li><a href="<?= BASE_URL ?>/compare" class="text-sm hover:text-primary-400 transition">Compare Cars</a></li>
                    <li><a href="<?= BASE_URL ?>/about" class="text-sm hover:text-primary-400 transition">About Us</a></li>
                    <li><a href="<?= BASE_URL ?>/contact" class="text-sm hover:text-primary-400 transition">Contact</a></li>
                </ul>
            </div>

            <!-- Categories -->
            <div>
                <h4 class="text-white font-semibold mb-5">Categories</h4>
                <ul class="space-y-2.5">
                    <?php foreach (array_slice($footerCategories, 0, 8) as $cat): ?>
                    <li>
                        <a href="<?= BASE_URL ?>/category/<?= $cat['slug'] ?>" class="text-sm hover:text-primary-400 transition">
                            <?= clean($cat['name']) ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Popular Brands -->
            <div>
                <h4 class="text-white font-semibold mb-5">Popular Brands</h4>
                <ul class="space-y-2.5">
                    <?php foreach (array_slice(array_filter($footerBrands, fn($b) => $b['is_popular']), 0, 8) as $br): ?>
                    <li>
                        <a href="<?= BASE_URL ?>/brand/<?= $br['slug'] ?>" class="text-sm hover:text-primary-400 transition">
                            <?= clean($br['name']) ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <!-- Social Links -->
        <?php if (!empty($socialLinks)): ?>
        <div class="flex items-center justify-center gap-3 mt-12 pt-8 border-t border-white/10">
            <?php 
            $socialIcons = ['facebook'=>'facebook','twitter'=>'twitter','instagram'=>'instagram','youtube'=>'youtube','linkedin'=>'linkedin','tiktok'=>'music'];
            foreach ($socialLinks as $platform => $url): 
                $icon = $socialIcons[$platform] ?? 'link';
            ?>
            <a href="<?= clean($url) ?>" target="_blank" rel="noopener" 
               class="w-10 h-10 bg-white/5 hover:bg-primary-600 rounded-xl flex items-center justify-center text-white/60 hover:text-white transition-all duration-200" 
               title="<?= ucfirst($platform) ?>">
                <i data-lucide="<?= $icon ?>" class="w-4 h-4"></i>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Bottom Bar -->
        <div class="mt-8 pt-8 border-t border-white/10 flex flex-col md:flex-row items-center justify-between gap-4">
            <p class="text-sm text-white/40">
                &copy; <?= date('Y') ?> <?= clean($companyName) ?>. All rights reserved.
            </p>
            <div class="flex items-center gap-6 text-sm text-white/40">
                <a href="<?= BASE_URL ?>/terms" class="hover:text-white/70 transition">Terms & Conditions</a>
                <a href="<?= BASE_URL ?>/privacy" class="hover:text-white/70 transition">Privacy Policy</a>
                <a href="<?= BASE_URL ?>/faq" class="hover:text-white/70 transition">FAQ</a>
            </div>
        </div>
    </div>
</footer>

<!-- ============================================================ -->
<!-- WHATSAPP FLOATING BUTTON -->
<!-- ============================================================ -->
<a href="<?= whatsappLink('Hello! I would like to inquire about your vehicles.') ?>" target="_blank" 
   id="whatsapp-float"
   class="fixed bottom-6 right-6 z-40 w-14 h-14 bg-[#25D366] hover:bg-[#20BD5A] text-white rounded-full shadow-lg hover:shadow-xl flex items-center justify-center transition-all duration-300 hover:scale-110 group"
   title="Chat on WhatsApp">
    <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
    <span class="absolute right-full mr-3 whitespace-nowrap bg-white text-dark-800 text-sm font-medium px-3 py-1.5 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition pointer-events-none">
        Chat with us
    </span>
</a>

<!-- ============================================================ -->
<!-- LIVE CHAT WIDGET -->
<!-- ============================================================ -->
<?php if ($enableChat === '1'): ?>
<div id="chat-widget" class="fixed bottom-6 left-6 z-40">
    <!-- Chat Toggle -->
    <button onclick="toggleChatWidget()" id="chat-toggle-btn"
            class="w-14 h-14 bg-dark-900 hover:bg-dark-800 text-white rounded-full shadow-lg flex items-center justify-center transition-all duration-300 hover:scale-110 relative">
        <i data-lucide="message-square" class="w-6 h-6" id="chat-icon-open"></i>
        <i data-lucide="x" class="w-6 h-6 hidden" id="chat-icon-close"></i>
        <span id="chat-unread-badge" class="hidden absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center animate-pulse">0</span>
    </button>

    <!-- Chat Window -->
    <div id="chat-window" class="hidden absolute bottom-16 left-0 w-[360px] max-h-[500px] bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden flex flex-col">
        <!-- Chat Header -->
        <div class="bg-dark-900 text-white px-5 py-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-primary-600 rounded-xl flex items-center justify-center">
                <i data-lucide="message-square" class="w-5 h-5"></i>
            </div>
            <div class="flex-1">
                <h4 class="font-semibold text-sm"><?= clean($companyName) ?> Support</h4>
                <p class="text-xs text-white/60">We typically reply within minutes</p>
            </div>
            <button onclick="toggleChatWidget()" class="w-8 h-8 rounded-lg hover:bg-white/10 flex items-center justify-center transition">
                <i data-lucide="minus" class="w-4 h-4"></i>
            </button>
        </div>

        <!-- Chat Start Form (before chat begins) -->
        <div id="chat-start-form" class="p-5">
            <p class="text-sm text-dark-600 mb-4">Please provide your details to start chatting:</p>
            <div class="space-y-3">
                <input type="text" id="chat-name" placeholder="Your name" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm outline-none focus:border-primary-500 transition">
                <input type="email" id="chat-email" placeholder="Email address" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm outline-none focus:border-primary-500 transition">
                <button onclick="startChat()" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-2.5 rounded-lg text-sm transition">
                    Start Chat
                </button>
            </div>
        </div>

        <!-- Chat Messages -->
        <div id="chat-messages" class="hidden flex-1 overflow-y-auto p-4 space-y-3 min-h-[280px] max-h-[320px]">
            <div class="chat-msg-system text-center">
                <span class="text-xs text-dark-400 bg-gray-100 px-3 py-1 rounded-full">Chat started</span>
            </div>
        </div>

        <!-- Chat Input -->
        <div id="chat-input-area" class="hidden border-t border-gray-100 p-3 flex items-center gap-2">
            <input type="text" id="chat-message-input" placeholder="Type a message..." 
                   class="flex-1 px-4 py-2.5 bg-gray-50 rounded-lg text-sm outline-none focus:bg-white focus:ring-1 focus:ring-primary-500 transition"
                   onkeyup="if(event.key==='Enter') sendChatMsg()">
            <button onclick="sendChatMsg()" class="w-10 h-10 bg-primary-600 hover:bg-primary-700 text-white rounded-lg flex items-center justify-center transition flex-shrink-0">
                <i data-lucide="send" class="w-4 h-4"></i>
            </button>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- ============================================================ -->
<!-- BACK TO TOP -->
<!-- ============================================================ -->
<button id="back-to-top" onclick="window.scrollTo({top:0,behavior:'smooth'})"
        class="fixed bottom-24 right-6 z-30 w-11 h-11 bg-white shadow-lg border border-gray-200 rounded-xl flex items-center justify-center text-dark-500 hover:text-primary-600 hover:border-primary-300 transition-all duration-300 opacity-0 invisible translate-y-4">
    <i data-lucide="chevron-up" class="w-5 h-5"></i>
</button>

<!-- ============================================================ -->
<!-- SCRIPTS -->
<!-- ============================================================ -->
<script src="<?= BASE_URL ?>/assets/js/main.js"></script>
<script>
    // Initialize Lucide icons
    lucide.createIcons();
    
    // Sticky header shadow
    const header = document.getElementById('main-header');
    window.addEventListener('scroll', () => {
        header.classList.toggle('shadow-md', window.scrollY > 10);
    });

    // Back to top button
    const backBtn = document.getElementById('back-to-top');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 500) {
            backBtn.classList.remove('opacity-0', 'invisible', 'translate-y-4');
            backBtn.classList.add('opacity-100', 'visible', 'translate-y-0');
        } else {
            backBtn.classList.add('opacity-0', 'invisible', 'translate-y-4');
            backBtn.classList.remove('opacity-100', 'visible', 'translate-y-0');
        }
    });

    // Mobile menu
    function toggleMobileMenu() {
        const menu = document.getElementById('mobile-menu');
        const iconOpen = document.getElementById('menu-icon-open');
        const iconClose = document.getElementById('menu-icon-close');
        menu.classList.toggle('hidden');
        iconOpen.classList.toggle('hidden');
        iconClose.classList.toggle('hidden');
        document.body.classList.toggle('overflow-hidden');
    }

    // Search overlay
    function toggleSearch() {
        const overlay = document.getElementById('search-overlay');
        overlay.classList.toggle('hidden');
        if (!overlay.classList.contains('hidden')) {
            document.getElementById('search-input').focus();
            document.body.classList.add('overflow-hidden');
        } else {
            document.body.classList.remove('overflow-hidden');
        }
    }

    function handleSearchKeyup(e) {
        if (e.key === 'Enter') {
            const q = document.getElementById('search-input').value.trim();
            if (q) window.location.href = '<?= BASE_URL ?>/cars?search=' + encodeURIComponent(q);
        }
    }

    // Chat widget
    function toggleChatWidget() {
        const win = document.getElementById('chat-window');
        const iconOpen = document.getElementById('chat-icon-open');
        const iconClose = document.getElementById('chat-icon-close');
        win.classList.toggle('hidden');
        iconOpen.classList.toggle('hidden');
        iconClose.classList.toggle('hidden');
    }

    function startChat() {
        const name = document.getElementById('chat-name').value.trim();
        const emailInput = document.getElementById('chat-email').value.trim();
        if (!name) { alert('Please enter your name'); return; }
        
        document.getElementById('chat-start-form').classList.add('hidden');
        document.getElementById('chat-messages').classList.remove('hidden');
        document.getElementById('chat-input-area').classList.remove('hidden');
        
        // Add welcome message
        addChatMessage('agent', `Hello ${name}! Welcome to <?= clean($companyName) ?>. How can we help you today?`);
    }

    function sendChatMsg() {
        const input = document.getElementById('chat-message-input');
        const msg = input.value.trim();
        if (!msg) return;
        
        addChatMessage('visitor', msg);
        input.value = '';
        
        // Send to server
        fetch('<?= BASE_URL ?>/api/chat', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                message: msg,
                session_id: getChatSessionId(),
                name: document.getElementById('chat-name').value,
                email: document.getElementById('chat-email').value
            })
        }).catch(() => {});

        // Auto-reply placeholder
        setTimeout(() => {
            addChatMessage('agent', 'Thank you for your message! Our team will respond shortly. You can also reach us on WhatsApp for faster assistance.');
        }, 1500);
    }

    function addChatMessage(type, text) {
        const container = document.getElementById('chat-messages');
        const div = document.createElement('div');
        if (type === 'visitor') {
            div.className = 'flex justify-end';
            div.innerHTML = `<div class="bg-primary-600 text-white text-sm px-4 py-2.5 rounded-2xl rounded-br-md max-w-[80%]">${escapeHtml(text)}</div>`;
        } else {
            div.className = 'flex justify-start';
            div.innerHTML = `<div class="bg-gray-100 text-dark-700 text-sm px-4 py-2.5 rounded-2xl rounded-bl-md max-w-[80%]">${escapeHtml(text)}</div>`;
        }
        container.appendChild(div);
        container.scrollTop = container.scrollHeight;
    }

    function getChatSessionId() {
        let sid = sessionStorage.getItem('chat_session');
        if (!sid) { sid = 'cs_' + Date.now() + '_' + Math.random().toString(36).substr(2,9); sessionStorage.setItem('chat_session', sid); }
        return sid;
    }

    function escapeHtml(text) {
        const d = document.createElement('div');
        d.textContent = text;
        return d.innerHTML;
    }
</script>

<?php if (isset($pageScripts)) echo $pageScripts; ?>
</body>
</html>
