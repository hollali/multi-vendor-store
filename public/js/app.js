(function () {
    'use strict';

    /* Dark mode toggle */
    function initDarkMode() {
        const saved = localStorage.getItem('darkMode');
        const toggle = document.getElementById('darkModeToggle');

        if (saved === 'true' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
            localStorage.setItem('darkMode', 'true');
        } else {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('darkMode', 'false');
        }

        if (toggle) {
            toggle.addEventListener('click', function () {
                const isDark = document.documentElement.classList.toggle('dark');
                localStorage.setItem('darkMode', isDark);
            });
        }
    }

    /* Mobile sidebar */
    function initMobileSidebar() {
        const toggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');

        if (toggle && sidebar) {
            toggle.addEventListener('click', function () {
                sidebar.classList.toggle('-translate-x-full');
                if (overlay) overlay.classList.toggle('hidden');
            });
        }

        if (overlay) {
            overlay.addEventListener('click', function () {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            });
        }
    }

    /* Flash messages auto-dismiss */
    function initFlashMessages() {
        document.querySelectorAll('.flash-message').forEach(function (el) {
            setTimeout(function () {
                el.style.transition = 'opacity 0.5s ease';
                el.style.opacity = '0';
                setTimeout(function () { el.remove(); }, 500);
            }, 5000);
        });
    }

    /* Quantity increment/decrement */
    function initQuantityControls() {
        document.querySelectorAll('.qty-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const input = this.parentElement.querySelector('.qty-input');
                if (!input) return;
                let val = parseInt(input.value) || 1;
                const min = parseInt(input.min) || 1;
                const max = parseInt(input.max) || 999;

                if (this.dataset.action === 'increment' && val < max) {
                    val++;
                } else if (this.dataset.action === 'decrement' && val > min) {
                    val--;
                }

                input.value = val;
                input.dispatchEvent(new Event('change'));
            });
        });
    }

    /* Add to cart AJAX */
    function initAddToCart() {
        document.querySelectorAll('.add-to-cart').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                const productId = this.dataset.productId;
                const quantity = this.dataset.quantity || 1;
                const variantId = this.dataset.variantId || null;

                const formData = new FormData();
                formData.append('product_id', productId);
                formData.append('quantity', quantity);
                if (variantId) formData.append('variant_id', variantId);

                fetch('/cart/add', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                })
                    .then(function (res) { return res.json(); })
                    .then(function (data) {
                        if (data.success) {
                            updateCartBadge(data.cart_count);
                            showToast('Item added to cart!', 'success');
                        } else {
                            showToast(data.message || 'Failed to add item', 'error');
                        }
                    })
                    .catch(function () {
                        showToast('An error occurred', 'error');
                    });
            });
        });
    }

    function updateCartBadge(count) {
        const badge = document.getElementById('cart-badge');
        if (badge) {
            badge.textContent = count;
            badge.classList.remove('hidden');
        }
    }

    /* Wishlist toggle */
    function initWishlistToggle() {
        document.querySelectorAll('.wishlist-toggle').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                const productId = this.dataset.productId;

                fetch('/wishlist/toggle', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ product_id: productId })
                })
                    .then(function (res) { return res.json(); })
                    .then(function (data) {
                        if (data.success) {
                            this.classList.toggle('active');
                            const icon = this.querySelector('i, svg');
                            if (icon) {
                                icon.classList.toggle('text-red-500');
                                icon.classList.toggle('text-gray-400');
                            }
                            showToast(data.message, 'success');
                        }
                    }.bind(this))
                    .catch(function () {
                        showToast('Failed to update wishlist', 'error');
                    });
            });
        });
    }

    /* Image gallery */
    function initImageGallery() {
        const mainImage = document.getElementById('mainImage');
        const thumbs = document.querySelectorAll('.gallery-thumb');

        thumbs.forEach(function (thumb) {
            thumb.addEventListener('click', function () {
                const src = this.dataset.image;
                if (src && mainImage) {
                    mainImage.src = src;
                    thumbs.forEach(function (t) { t.classList.remove('border-blue-500'); });
                    this.classList.add('border-blue-500');
                }
            });
        });
    }

    /* Price range filter */
    function initPriceRange() {
        const minSlider = document.getElementById('minPrice');
        const maxSlider = document.getElementById('maxPrice');
        const minDisplay = document.getElementById('minPriceDisplay');
        const maxDisplay = document.getElementById('maxPriceDisplay');

        if (!minSlider || !maxSlider) return;

        function updateDisplay() {
            const minVal = parseInt(minSlider.value);
            const maxVal = parseInt(maxSlider.value);

            if (minVal > maxVal) {
                if (this === minSlider) {
                    minSlider.value = maxVal;
                } else {
                    maxSlider.value = minVal;
                }
            }

            if (minDisplay) minDisplay.textContent = 'GH₵' + minSlider.value;
            if (maxDisplay) maxDisplay.textContent = 'GH₵' + maxSlider.value;
        }

        minSlider.addEventListener('input', updateDisplay);
        maxSlider.addEventListener('input', updateDisplay);
        updateDisplay();
    }

    /* Search suggestions */
    function initSearchSuggestions() {
        const searchInput = document.getElementById('searchInput');
        const suggestions = document.getElementById('searchSuggestions');

        if (!searchInput) return;

        let debounceTimer;

        searchInput.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            const query = this.value.trim();

            if (query.length < 2) {
                if (suggestions) suggestions.classList.add('hidden');
                return;
            }

            debounceTimer = setTimeout(function () {
                fetch('/api/products?search=' + encodeURIComponent(query) + '&per_page=5')
                    .then(function (res) { return res.json(); })
                    .then(function (data) {
                        if (!suggestions) return;
                        suggestions.innerHTML = '';
                        if (data.data && data.data.length) {
                            data.data.forEach(function (p) {
                                const a = document.createElement('a');
                                a.href = '/product/' + p.slug;
                                a.className = 'block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm';
                                a.textContent = p.name;
                                suggestions.appendChild(a);
                            });
                            suggestions.classList.remove('hidden');
                        } else {
                            suggestions.classList.add('hidden');
                        }
                    })
                    .catch(function () {
                        if (suggestions) suggestions.classList.add('hidden');
                    });
            }, 300);
        });

        document.addEventListener('click', function (e) {
            if (suggestions && !searchInput.contains(e.target) && !suggestions.contains(e.target)) {
                suggestions.classList.add('hidden');
            }
        });
    }

    /* Form validation */
    function initFormValidation() {
        document.querySelectorAll('form[data-validate]').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                let valid = true;
                const errors = [];

                this.querySelectorAll('[required]').forEach(function (field) {
                    field.classList.remove('border-red-500', 'border-green-500');
                    const errorEl = field.parentElement.querySelector('.field-error');
                    if (errorEl) errorEl.remove();

                    if (!field.value.trim()) {
                        valid = false;
                        field.classList.add('border-red-500');
                        const msg = field.dataset.error || field.name + ' is required';
                        errors.push(msg);
                        showFieldError(field, msg);
                    } else {
                        field.classList.add('border-green-500');
                    }
                });

                const emailFields = this.querySelectorAll('input[type="email"]');
                emailFields.forEach(function (field) {
                    if (field.value.trim() && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(field.value)) {
                        valid = false;
                        field.classList.add('border-red-500');
                        showFieldError(field, 'Invalid email address');
                    }
                });

                const password = this.querySelector('input[name="password"]');
                const confirm = this.querySelector('input[name="password_confirm"]');
                if (password && confirm && confirm.value) {
                    if (password.value !== confirm.value) {
                        valid = false;
                        confirm.classList.add('border-red-500');
                        showFieldError(confirm, 'Passwords do not match');
                    }
                }

                if (!valid) {
                    e.preventDefault();
                    showToast(errors[0], 'error');
                }
            });
        });
    }

    function showFieldError(field, message) {
        const div = document.createElement('p');
        div.className = 'field-error text-red-500 text-xs mt-1';
        div.textContent = message;
        field.parentElement.appendChild(div);
    }

    /* Confirm dialogs */
    function initConfirmDialogs() {
        document.querySelectorAll('[data-confirm]').forEach(function (el) {
            el.addEventListener('click', function (e) {
                if (!confirm(this.dataset.confirm || 'Are you sure?')) {
                    e.preventDefault();
                }
            });
        });
    }

    /* Toast notification system */
    function showToast(message, type) {
        type = type || 'info';
        const container = document.getElementById('toastContainer');
        if (!container) return;

        const toast = document.createElement('div');
        toast.className = 'toast toast-' + type + ' show';
        toast.textContent = message;

        container.appendChild(toast);

        setTimeout(function () {
            toast.classList.remove('show');
            setTimeout(function () { toast.remove(); }, 400);
        }, 4000);
    }

    /* Paystack payment handler */
    function payWithPaystack(email, amount, reference, callback) {
        if (typeof PaystackPop === 'undefined') {
            showToast('Paystack is not loaded', 'error');
            return;
        }

        const handler = PaystackPop.setup({
            key: document.body.dataset.paystackKey || '',
            email: email,
            amount: amount * 100,
            ref: reference,
            currency: 'GHS',
            callback: function (response) {
                if (typeof callback === 'function') {
                    callback(response.reference);
                }
            },
            onClose: function () {
                showToast('Payment window closed', 'warning');
            }
        });

        handler.openIframe();
    }

    /* Copy to clipboard */
    function copyToClipboard(text) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(function () {
                showToast('Copied to clipboard!', 'success');
            }).catch(function () {
                fallbackCopy(text);
            });
        } else {
            fallbackCopy(text);
        }
    }

    function fallbackCopy(text) {
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        textarea.select();
        try {
            document.execCommand('copy');
            showToast('Copied to clipboard!', 'success');
        } catch (e) {
            showToast('Failed to copy', 'error');
        }
        document.body.removeChild(textarea);
    }

    /* Smooth scroll for anchor links */
    function initSmoothScroll() {
        document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
            anchor.addEventListener('click', function (e) {
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
    }

    /* Init all */
    function init() {
        // initDarkMode(); // handled by inline script in footer
        initMobileSidebar();
        initFlashMessages();
        initQuantityControls();
        initAddToCart();
        initWishlistToggle();
        initImageGallery();
        initPriceRange();
        initSearchSuggestions();
        initFormValidation();
        initConfirmDialogs();
        initSmoothScroll();

        document.querySelectorAll('[data-copy]').forEach(function (el) {
            el.addEventListener('click', function () {
                copyToClipboard(this.dataset.copy);
            });
        });

        window.showToast = showToast;
        window.payWithPaystack = payWithPaystack;
        window.copyToClipboard = copyToClipboard;
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
