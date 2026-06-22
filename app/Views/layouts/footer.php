</main>

<footer class="bg-gray-900 dark:bg-gray-950 text-gray-300 border-t border-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-12 py-12 lg:py-16">

            <div class="sm:col-span-2 lg:col-span-1">
                <div class="flex items-center gap-2 mb-4">
                    <img src="/uploads/logos/logo1.png" alt="<?= htmlspecialchars($site_name ?? 'Celer Market') ?>" class="h-9 w-auto">
                    <span class="text-xl font-extrabold text-primary-400"><?= htmlspecialchars($site_name ?? 'Celer Market') ?></span>
                </div>
                <p class="text-sm text-gray-400 leading-relaxed mb-4">Ghana's trusted multi-vendor marketplace. Shop from verified sellers across the nation with secure payments and reliable delivery.</p>
                <div class="flex items-center gap-3 mb-4">
                    <span class="flex items-center gap-1.5 text-sm text-gray-400"><i class="fas fa-map-marker-alt text-primary-400"></i>Accra, Ghana</span>
                    <span class="text-gray-600">|</span>
                    <span class="flex items-center gap-1.5 text-sm text-gray-400"><i class="fas fa-envelope text-primary-400"></i>support@celermarket.com</span>
                </div>
                <div class="flex gap-2.5">
                    <a href="#" class="w-9 h-9 rounded-xl bg-gray-800 hover:bg-primary-700 flex items-center justify-center text-gray-400 hover:text-white transition-all duration-200 shadow-sm hover:shadow-md" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="w-9 h-9 rounded-xl bg-gray-800 hover:bg-primary-700 flex items-center justify-center text-gray-400 hover:text-white transition-all duration-200 shadow-sm hover:shadow-md" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="w-9 h-9 rounded-xl bg-gray-800 hover:bg-primary-700 flex items-center justify-center text-gray-400 hover:text-white transition-all duration-200 shadow-sm hover:shadow-md" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="w-9 h-9 rounded-xl bg-gray-800 hover:bg-primary-700 flex items-center justify-center text-gray-400 hover:text-white transition-all duration-200 shadow-sm hover:shadow-md" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                    <a href="#" class="w-9 h-9 rounded-xl bg-gray-800 hover:bg-primary-700 flex items-center justify-center text-gray-400 hover:text-white transition-all duration-200 shadow-sm hover:shadow-md" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                </div>
            </div>

            <div>
                <h3 class="text-white font-bold text-sm uppercase tracking-wider mb-4">Company</h3>
                <ul class="space-y-2.5">
                    <li><a href="<?= $site_url ?? '' ?>/about" class="text-sm text-gray-400 hover:text-white transition flex items-center gap-2 group"><span class="w-0 group-hover:w-2 h-[2px] bg-primary-400 transition-all duration-200"></span>About Us</a></li>
                    <li><a href="<?= $site_url ?? '' ?>/contact" class="text-sm text-gray-400 hover:text-white transition flex items-center gap-2 group"><span class="w-0 group-hover:w-2 h-[2px] bg-primary-400 transition-all duration-200"></span>Contact Us</a></li>
                    <li><a href="<?= $site_url ?? '' ?>/careers" class="text-sm text-gray-400 hover:text-white transition flex items-center gap-2 group"><span class="w-0 group-hover:w-2 h-[2px] bg-primary-400 transition-all duration-200"></span>Careers</a></li>
                    <li><a href="<?= $site_url ?? '' ?>/blog" class="text-sm text-gray-400 hover:text-white transition flex items-center gap-2 group"><span class="w-0 group-hover:w-2 h-[2px] bg-primary-400 transition-all duration-200"></span>Blog</a></li>
                    <li><a href="<?= $site_url ?? '' ?>/press" class="text-sm text-gray-400 hover:text-white transition flex items-center gap-2 group"><span class="w-0 group-hover:w-2 h-[2px] bg-primary-400 transition-all duration-200"></span>Press & Media</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-white font-bold text-sm uppercase tracking-wider mb-4">Customer Service</h3>
                <ul class="space-y-2.5">
                    <li><a href="<?= $site_url ?? '' ?>/help" class="text-sm text-gray-400 hover:text-white transition flex items-center gap-2 group"><span class="w-0 group-hover:w-2 h-[2px] bg-primary-400 transition-all duration-200"></span>Help Center</a></li>
                    <li><a href="<?= $site_url ?? '' ?>/returns" class="text-sm text-gray-400 hover:text-white transition flex items-center gap-2 group"><span class="w-0 group-hover:w-2 h-[2px] bg-primary-400 transition-all duration-200"></span>Returns & Refunds</a></li>
                    <li><a href="<?= $site_url ?? '' ?>/shipping-info" class="text-sm text-gray-400 hover:text-white transition flex items-center gap-2 group"><span class="w-0 group-hover:w-2 h-[2px] bg-primary-400 transition-all duration-200"></span>Shipping Info</a></li>
                    <li><a href="<?= $site_url ?? '' ?>/faq" class="text-sm text-gray-400 hover:text-white transition flex items-center gap-2 group"><span class="w-0 group-hover:w-2 h-[2px] bg-primary-400 transition-all duration-200"></span>FAQ</a></li>
                    <li><a href="<?= $site_url ?? '' ?>/privacy" class="text-sm text-gray-400 hover:text-white transition flex items-center gap-2 group"><span class="w-0 group-hover:w-2 h-[2px] bg-primary-400 transition-all duration-200"></span>Privacy Policy</a></li>
                    <li><a href="<?= $site_url ?? '' ?>/terms" class="text-sm text-gray-400 hover:text-white transition flex items-center gap-2 group"><span class="w-0 group-hover:w-2 h-[2px] bg-primary-400 transition-all duration-200"></span>Terms of Service</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-white font-bold text-sm uppercase tracking-wider mb-4">Quick Links</h3>
                <ul class="space-y-2.5">
                    <li><a href="<?= $site_url ?? '' ?>/my-account" class="text-sm text-gray-400 hover:text-white transition flex items-center gap-2 group"><span class="w-0 group-hover:w-2 h-[2px] bg-primary-400 transition-all duration-200"></span>My Account</a></li>
                    <li><a href="<?= $site_url ?? '' ?>/orders" class="text-sm text-gray-400 hover:text-white transition flex items-center gap-2 group"><span class="w-0 group-hover:w-2 h-[2px] bg-primary-400 transition-all duration-200"></span>Track Order</a></li>
                    <li><a href="<?= $site_url ?? '' ?>/wishlist" class="text-sm text-gray-400 hover:text-white transition flex items-center gap-2 group"><span class="w-0 group-hover:w-2 h-[2px] bg-primary-400 transition-all duration-200"></span>Wishlist</a></li>
                    <li><a href="<?= $site_url ?? '' ?>/sell" class="text-sm text-gray-400 hover:text-white transition flex items-center gap-2 group"><span class="w-0 group-hover:w-2 h-[2px] bg-primary-400 transition-all duration-200"></span>Sell on <?= htmlspecialchars($site_name ?? 'Celer Market') ?></a></li>
                    <li><a href="<?= $site_url ?? '' ?>/become-vendor" class="text-sm text-gray-400 hover:text-white transition flex items-center gap-2 group"><span class="w-0 group-hover:w-2 h-[2px] bg-primary-400 transition-all duration-200"></span>Become a Vendor</a></li>
                </ul>

                <h3 class="text-white font-bold text-sm uppercase tracking-wider mb-4 mt-6">Newsletter</h3>
                <p class="text-sm text-gray-400 mb-3 leading-relaxed">Get the latest deals, offers, and updates delivered to your inbox.</p>
                <form action="<?= $site_url ?? '' ?>/newsletter/subscribe" method="POST" class="flex gap-2">
                    <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_SESSION['_csrf_token'] ?? '') ?>">
                    <input type="email" name="email" placeholder="Your email address" required class="flex-1 min-w-0 px-3.5 py-2.5 bg-gray-800 border border-gray-700 rounded-xl text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                    <button type="submit" class="px-4 py-2.5 bg-primary-700 hover:bg-primary-800 text-white text-sm font-medium rounded-xl transition shadow-sm hover:shadow-md flex items-center gap-1.5">
                        <i class="fas fa-paper-plane"></i>
                        <span class="hidden sm:inline">Subscribe</span>
                    </button>
                </form>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 py-6 border-t border-gray-800">
            <p class="text-sm text-gray-500 text-center sm:text-left">&copy; <?= date('Y') ?> <?= htmlspecialchars($site_name ?? 'Celer Market') ?>. All rights reserved. Ghana's Trusted Marketplace.</p>
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-3 text-gray-500">
                    <span class="text-lg hover:text-gray-300 transition" title="Visa"><i class="fab fa-cc-visa"></i></span>
                    <span class="text-lg hover:text-gray-300 transition" title="Mastercard"><i class="fab fa-cc-mastercard"></i></span>
                    <span class="text-lg hover:text-gray-300 transition" title="PayPal"><i class="fab fa-cc-paypal"></i></span>
                    <span class="text-lg hover:text-gray-300 transition" title="Mobile Money"><i class="fas fa-mobile-alt"></i></span>
                    <span class="text-lg hover:text-gray-300 transition" title="Bank Transfer"><i class="fas fa-university"></i></span>
                </div>
                <span class="text-gray-700 hidden sm:block">|</span>
                <button type="button" class="dark-toggle-btn p-2.5 rounded-xl bg-gray-800 hover:bg-gray-700 text-gray-400 hover:text-yellow-400 transition-all duration-200 shadow-sm hover:shadow-md" aria-label="Toggle dark mode">
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

        document.querySelectorAll('.dark-toggle-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                html.classList.toggle('dark');
                localStorage.setItem('celer_dark_mode', html.classList.contains('dark'));
            });
        });

        var mobileBtn = document.getElementById('mobile-menu-btn');
        var mobileNav = document.getElementById('mobile-nav');
        if (mobileBtn && mobileNav) {
            mobileBtn.addEventListener('click', function() {
                mobileNav.classList.toggle('hidden');
            });
        }

        var countryBtns = document.querySelectorAll('[data-geo="country"]');
        var currencyBtns = document.querySelectorAll('[data-geo="currency"]');
        var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        countryBtns.forEach(function(btn) {
            btn.addEventListener('click', function() {
                var code = this.getAttribute('data-code');
                fetch('<?= $site_url ?? '' ?>/geo/set-country', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'code=' + encodeURIComponent(code) + '&_csrf_token=' + encodeURIComponent(csrfToken)
                }).then(function(r) { return r.json(); }).then(function(d) {
                    if (d.success) location.reload();
                });
            });
        });

        currencyBtns.forEach(function(btn) {
            btn.addEventListener('click', function() {
                var code = this.getAttribute('data-code');
                fetch('<?= $site_url ?? '' ?>/geo/set-currency', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'code=' + encodeURIComponent(code) + '&_csrf_token=' + encodeURIComponent(csrfToken)
                }).then(function(r) { return r.json(); }).then(function(d) {
                    if (d.success) location.reload();
                });
            });
        });
    });
</script>

<div id="toastContainer" class="fixed bottom-4 right-4 z-[9999] space-y-2 w-[calc(100%-2rem)] sm:max-w-sm"></div>

<script src="<?= $site_url ?? '' ?>/js/app.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var heroSwiper = new Swiper('.hero-swiper', {
        loop: true,
        autoplay: { delay: 5000, disableOnInteraction: false },
        pagination: { el: '.swiper-pagination', clickable: true },
        navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
        effect: 'fade',
        fadeEffect: { crossFade: true },
        speed: 800,
        autoHeight: false,
        grabCursor: true,
    });

    var trendingSwiper = new Swiper('.trending-swiper', {
        slidesPerView: 1.5,
        spaceBetween: 16,
        loop: true,
        autoplay: { delay: 4000, disableOnInteraction: false },
        navigation: { nextEl: '.trending-swiper .swiper-button-next', prevEl: '.trending-swiper .swiper-button-prev' },
        grabCursor: true,
        breakpoints: {
            480: { slidesPerView: 2, spaceBetween: 16 },
            768: { slidesPerView: 3, spaceBetween: 20 },
            1024: { slidesPerView: 4, spaceBetween: 24 },
        },
    });
});
</script>

</body>
</html>
