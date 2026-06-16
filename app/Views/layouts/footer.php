</main>

<footer class="bg-gray-900 dark:bg-gray-950 text-gray-300 border-t border-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 py-12 lg:py-16">

            <div>
                <div class="flex items-center gap-2 mb-4">
                    <span class="flex items-center justify-center w-9 h-9 bg-gradient-to-br from-primary-500 to-primary-700 rounded-lg shadow-md">
                        <i class="fas fa-bolt text-white text-lg"></i>
                    </span>
                    <span class="text-xl font-extrabold bg-gradient-to-r from-primary-400 to-accent-500 bg-clip-text text-transparent">Celer Market</span>
                </div>
                <p class="text-sm text-gray-400 leading-relaxed mb-4">Ghana's trusted multi-vendor marketplace. Shop from verified sellers across the nation with secure payments and reliable delivery.</p>
                <div class="flex items-center gap-3 mb-4">
                    <span class="flex items-center gap-1.5 text-sm text-gray-400"><i class="fas fa-map-marker-alt text-primary-400"></i>Accra, Ghana</span>
                </div>
                <div class="flex gap-3">
                    <a href="#" class="w-9 h-9 rounded-lg bg-gray-800 hover:bg-primary-700 flex items-center justify-center text-gray-400 hover:text-white transition" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="w-9 h-9 rounded-lg bg-gray-800 hover:bg-primary-700 flex items-center justify-center text-gray-400 hover:text-white transition" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="w-9 h-9 rounded-lg bg-gray-800 hover:bg-primary-700 flex items-center justify-center text-gray-400 hover:text-white transition" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="w-9 h-9 rounded-lg bg-gray-800 hover:bg-primary-700 flex items-center justify-center text-gray-400 hover:text-white transition" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                    <a href="#" class="w-9 h-9 rounded-lg bg-gray-800 hover:bg-primary-700 flex items-center justify-center text-gray-400 hover:text-white transition" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                </div>
            </div>

            <div>
                <h3 class="text-white font-bold text-sm uppercase tracking-wider mb-4">Company</h3>
                <ul class="space-y-2.5">
                    <li><a href="/about" class="text-sm text-gray-400 hover:text-white transition">About Us</a></li>
                    <li><a href="/contact" class="text-sm text-gray-400 hover:text-white transition">Contact Us</a></li>
                    <li><a href="/careers" class="text-sm text-gray-400 hover:text-white transition">Careers</a></li>
                    <li><a href="/blog" class="text-sm text-gray-400 hover:text-white transition">Blog</a></li>
                    <li><a href="/press" class="text-sm text-gray-400 hover:text-white transition">Press & Media</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-white font-bold text-sm uppercase tracking-wider mb-4">Customer Service</h3>
                <ul class="space-y-2.5">
                    <li><a href="/help" class="text-sm text-gray-400 hover:text-white transition">Help Center</a></li>
                    <li><a href="/returns" class="text-sm text-gray-400 hover:text-white transition">Returns & Refunds</a></li>
                    <li><a href="/shipping" class="text-sm text-gray-400 hover:text-white transition">Shipping Info</a></li>
                    <li><a href="/faq" class="text-sm text-gray-400 hover:text-white transition">FAQ</a></li>
                    <li><a href="/privacy" class="text-sm text-gray-400 hover:text-white transition">Privacy Policy</a></li>
                    <li><a href="/terms" class="text-sm text-gray-400 hover:text-white transition">Terms of Service</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-white font-bold text-sm uppercase tracking-wider mb-4">Quick Links</h3>
                <ul class="space-y-2.5">
                    <li><a href="/my-account" class="text-sm text-gray-400 hover:text-white transition">My Account</a></li>
                    <li><a href="/orders" class="text-sm text-gray-400 hover:text-white transition">Track Order</a></li>
                    <li><a href="/wishlist" class="text-sm text-gray-400 hover:text-white transition">Wishlist</a></li>
                    <li><a href="/sell" class="text-sm text-gray-400 hover:text-white transition">Sell on Celer Market</a></li>
                    <li><a href="/become-vendor" class="text-sm text-gray-400 hover:text-white transition">Become a Vendor</a></li>
                </ul>

                <h3 class="text-white font-bold text-sm uppercase tracking-wider mb-4 mt-6">Newsletter</h3>
                <p class="text-sm text-gray-400 mb-3">Get the latest deals and offers.</p>
                <form action="/newsletter/subscribe" method="POST" class="flex gap-2">
                    <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_SESSION['_csrf_token'] ?? '') ?>">
                    <input type="email" name="email" placeholder="Your email" required class="flex-1 px-3 py-2.5 bg-gray-800 border border-gray-700 rounded-lg text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <button type="submit" class="px-4 py-2.5 bg-primary-700 hover:bg-primary-800 text-white text-sm font-medium rounded-lg transition shadow-sm"><i class="fas fa-paper-plane"></i></button>
                </form>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 py-6 border-t border-gray-800">
            <p class="text-sm text-gray-500 text-center sm:text-left">&copy; <?= date('Y') ?> Celer Market. All rights reserved. Ghana's Trusted Marketplace.</p>
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-3 text-gray-500">
                    <span class="text-lg" title="Visa"><i class="fab fa-cc-visa"></i></span>
                    <span class="text-lg" title="Mastercard"><i class="fab fa-cc-mastercard"></i></span>
                    <span class="text-lg" title="PayPal"><i class="fab fa-cc-paypal"></i></span>
                    <span class="text-lg" title="Mobile Money"><i class="fas fa-mobile-alt"></i></span>
                    <span class="text-lg" title="Bank Transfer"><i class="fas fa-university"></i></span>
                </div>
                <span class="text-gray-600">|</span>
                <button id="dark-mode-toggle" class="p-2 rounded-lg bg-gray-800 hover:bg-gray-700 text-gray-400 hover:text-yellow-400 transition" aria-label="Toggle dark mode">
                    <i class="fas fa-moon dark:hidden"></i>
                    <i class="fas fa-sun hidden dark:inline"></i>
                </button>
            </div>
        </div>
    </div>
</footer>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var html = document.documentElement;
        var stored = localStorage.getItem('celer_dark_mode');
        if (stored === 'true') { html.classList.add('dark'); }

        var toggleBtn = document.getElementById('dark-mode-toggle');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', function() {
                html.classList.toggle('dark');
                localStorage.setItem('celer_dark_mode', html.classList.contains('dark'));
            });
        }

        var mobileBtn = document.getElementById('mobile-menu-btn');
        var mobileNav = document.getElementById('mobile-nav');
        if (mobileBtn && mobileNav) {
            mobileBtn.addEventListener('click', function() {
                mobileNav.classList.toggle('hidden');
            });
        }
    });
</script>

<div id="toastContainer" class="fixed bottom-4 right-4 z-[9999] space-y-2 w-[calc(100%-2rem)] sm:max-w-sm"></div>

<script src="<?= $site_url ?? '' ?>/js/app.js"></script>

</body>
</html>
