# Celer Market

**Ghana's Multi-Vendor E-Commerce Platform** — A full-featured marketplace where customers buy from multiple vendors, vendors manage their own stores, and administrators oversee the entire platform.

[![PHP](https://img.shields.io/badge/PHP-8.1+-777BB4?logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-5.7+-4479A1?logo=mysql&logoColor=white)](https://mysql.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind-3-06B6D4?logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
[![Paystack](https://img.shields.io/badge/Paystack-Integration-0BA95B?logo=paystack&logoColor=white)](https://paystack.com)
[![Apache](https://img.shields.io/badge/Apache-mod__rewrite-D22128?logo=apache&logoColor=white)](https://httpd.apache.org)
[![License](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)
[![PRs Welcome](https://img.shields.io/badge/PRs-welcome-brightgreen.svg)](CONTRIBUTING.md)

---

## Overview

Celer Market is a custom PHP MVC multi-vendor e-commerce platform built for the Ghanaian market. It provides a complete online marketplace experience with three user roles (Customer, Vendor, Admin), Paystack payment integration (cards, mobile money, bank transfers), and a responsive Tailwind CSS interface with dark mode support.

The entire application is built on a lightweight custom MVC framework — no heavy frameworks like Laravel or Symfony. The core framework (~650 lines across 7 files) provides routing, database abstraction, session management, input validation, CSRF protection, and role-based middleware.

### Built With

| Technology | Usage |
|------------|-------|
| **PHP 8.1+** | Custom MVC framework (no Laravel, no Composer packages) |
| **MySQL/MariaDB** | Relational database with 22 tables, FULLTEXT search |
| **Apache** | `mod_rewrite` for clean URLs, security headers, caching |
| **Tailwind CSS 3 (CDN)** | Utility-first responsive styling with dark mode |
| **Font Awesome 6** | Icon library |
| **Chart.js** | Dashboard analytics charts |
| **Paystack** | Payment processing (cards, Mobile Money, bank transfer) |
| **Google Fonts (Inter)** | Typography |

### Key Architecture Decisions

- **Zero framework bloat** — entire MVC core is hand-built and minimal
- **PSR-4 autoloading** — namespace-based class autoloading (no Composer dependency)
- **Singleton pattern** — Database (PDO) and Session are singletons
- **Prepared statements** — all database interactions use PDO prepared statements
- **Fluent query builder** — `Model::where(...)->orderBy(...)->limit(...)->get()`
- **Output escaping** — `htmlspecialchars()` on every rendered value
- **Mobile-first responsive** — Tailwind breakpoints from `sm` to `2xl`
- **Dark mode** — system preference detection with manual toggle, persisted in `localStorage`

---

## Features

### 👤 Customer

| Feature | Details |
|---------|---------|
| **Account Management** | Register, login, logout, profile editing, avatar upload, password change |
| **Product Browsing** | Category/brand/price filtering, keyword search with FULLTEXT, multiple sort options |
| **Product Detail** | Image gallery, variant selection (color/size), SKU, stock indicator, related products |
| **Shopping Cart** | Add/remove items, quantity controls, guest cart persisted via session ID, cart merging on login |
| **Coupon Codes** | Percentage and fixed-discount coupons with validation (min order, expiry, usage limit) |
| **Checkout** | Saved addresses, shipping info, order summary, Paystack payment (cards, MoMo, bank transfer) |
| **Order History** | Past orders with status tracking, order details with item breakdown |
| **Wishlist** | Save products for later, toggle with AJAX |
| **Product Reviews** | Rate and review purchased products (1-5 stars), purchase verification |
| **Addresses** | Multiple saved addresses with default selection, CRUD operations |
| **Notifications** | Real-time notification feed, mark-as-read |

### 🏪 Vendor

| Feature | Details |
|---------|---------|
| **Store Management** | Store profile, logo/banner upload, branding, contact info, commission rate |
| **Product Management** | Full CRUD with images (primary + gallery), variants (size, color, etc.), SKU, inventory tracking |
| **Order Fulfillment** | Incoming orders list, status updates (processing → shipped → delivered) |
| **Discount Coupons** | Create percentage or fixed-amount coupons with usage limits and expiry dates |
| **Earnings Dashboard** | Sales analytics, revenue chart, total earnings, pending balance |
| **Withdrawals** | Request payouts with bank details, track withdrawal status |
| **Customer Reviews** | View product reviews, respond to customer feedback |
| **Notifications** | Order notifications, system announcements |

### 🔐 Admin

| Feature | Details |
|---------|---------|
| **Analytics Dashboard** | Platform-wide metrics: users, vendors, orders, revenue, pending withdrawals, best sellers, top vendors |
| **User Management** | View/manage all users, suspend/activate accounts |
| **Vendor Management** | Vendor approvals, store verification, commission rate configuration |
| **Product Moderation** | Product approval/rejection workflow with rejection reason, featured product toggle |
| **Category & Brand Management** | Create, edit, delete categories (hierarchical) and brands with dependency checks |
| **Order Management** | View all platform orders, status updates, dispute management |
| **Transaction Monitoring** | Financial overview: payments, refunds, commissions, withdrawals, payouts |
| **Withdrawal Processing** | Review and process vendor payout requests (approve/reject) |
| **Banner Management** | Homepage carousel image management with sort order |
| **Platform Settings** | Key-value configuration: site info, tax rates, shipping, security, payment keys |
| **Notification Broadcasting** | Send system-wide notifications to all users or role-specific |
| **Profile Management** | Admin profile editing, password change |

---

## Quick Start

### Prerequisites

- PHP 8.1+ with `ext-pdo`, `ext-pdo_mysql`, `ext-mbstring`, `ext-json`
- MySQL 5.7+ / MariaDB 10.3+
- Apache with `mod_rewrite` and `.htaccess` overrides enabled
- Paystack merchant account (for payment processing)

### Setup (5 minutes)

```bash
# 1. Clone the repository
git clone https://github.com/hollali/multi-vendor-store.git
cd multi-vendor-store

# 2. Create the database and import schema + seed data
mysql -u root -p -e "CREATE DATABASE celer_market CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
mysql -u root -p celer_market < database/schema.sql
mysql -u root -p celer_market < database/seed.sql

# 3. Configure environment
cp .env.example .env
# Edit .env with your database credentials and Paystack keys

# 4. Set directory permissions
chmod -R 755 .
chmod -R 777 public/uploads storage

# 5. Ensure Apache mod_rewrite is enabled and .htaccess is allowed
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### Environment Variables

| Variable | Required | Default | Description |
|----------|----------|---------|-------------|
| `APP_NAME` | No | `Celer Market` | Application name |
| `APP_ENV` | No | `production` | Environment (`production`, `development`) |
| `APP_DEBUG` | No | `false` | Enable error detail display |
| `APP_URL` | No | `http://localhost:8000` | Application base URL |
| `DB_HOST` | Yes | `localhost` | Database host |
| `DB_PORT` | Yes | `3306` | Database port |
| `DB_DATABASE` | Yes | `celer_market` | Database name |
| `DB_USERNAME` | Yes | `root` | Database username |
| `DB_PASSWORD` | Yes | `` | Database password |
| `DATABASE_URL` | No* | — | Railway/JawsDB/ClearDB URL (alternative to individual DB vars) |
| `PAYSTACK_PUBLIC_KEY` | Yes | — | Paystack live public key |
| `PAYSTACK_SECRET_KEY` | Yes | — | Paystack live secret key |
| `PAYSTACK_WEBHOOK_SECRET` | Yes | — | Paystack webhook HMAC secret |

*`DATABASE_URL` (or `MYSQL_URL`, `JAWSDB_URL`, `CLEARDB_DATABASE_URL`) can replace individual DB_HOST/DB_PORT/DB_DATABASE/DB_USERNAME/DB_PASSWORD.

### Default Credentials

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@celermarket.com | admin123 |
| Vendor | vendor@celermarket.com | admin123 |
| Customer | customer@celermarket.com | admin123 |

### Webhooks

Configure this URL in your Paystack dashboard:
```
https://your-domain.com/checkout/webhook
```

> **Full installation details** → See [INSTALL.md](INSTALL.md)

---

## Project Structure

```
celer-market/
├── app/
│   ├── autoload.php              # PSR-4 namespace autoloader
│   ├── Core/                     # Custom MVC framework (7 files, ~650 lines)
│   │   ├── Controller.php        # Base controller: render, redirect, JSON, flash messages
│   │   ├── Database.php          # PDO singleton: query, fetch, insert, update, delete, transactions
│   │   ├── Middleware.php        # Auth guards (auth/guest/admin/vendor), CSRF, rate limiting
│   │   ├── Model.php             # Base model: fluent query builder, CRUD, pagination
│   │   ├── Router.php            # Route dispatcher: GET/POST/PUT/DELETE, pattern matching, middleware
│   │   ├── Session.php           # Session singleton: flash messages, user state, regenerate
│   │   └── Validator.php         # Input validation: required, email, numeric, min/max, unique, exists
│   ├── Controllers/              # 10 controllers (50-1100 lines each)
│   │   ├── HomeController.php    # Homepage: featured/latest products, banners, categories
│   │   ├── AuthController.php    # Login, register, logout, forgot/reset password
│   │   ├── ShopController.php    # Product listing, search, filtering, detail, store page
│   │   ├── CartController.php    # Cart CRUD, guest/user cart merge, coupon application
│   │   ├── CheckoutController.php # Paystack checkout, callback, webhook with HMAC verification
│   │   ├── DashboardController.php # Customer dashboard: orders, addresses, profile, reviews
│   │   ├── WishlistController.php  # Wishlist toggle and listing
│   │   ├── VendorController.php    # Vendor dashboard: products, orders, earnings, withdrawals
│   │   ├── AdminController.php     # Admin dashboard: users, vendors, products, categories, etc.
│   │   └── ApiController.php       # REST API: products, categories, stores
│   └── Views/                    # Blade-style PHP templates (45+ views)
│       ├── layouts/              # header.php, footer.php, sidebar.php
│       ├── auth/                 # login.php, register.php, forgot.php, reset.php
│       ├── shop/                 # home.php, index.php, show.php, category.php, search.php, store.php
│       ├── cart/                 # index.php
│       ├── checkout/             # index.php (Paystack integration)
│       ├── customer/             # dashboard, orders, wishlist, addresses, profile, reviews, notifications
│       ├── vendor/               # dashboard, products, orders, coupons, earnings, withdrawals, settings
│       ├── admin/                # dashboard, users, vendors, products, categories, brands, orders, etc.
│       ├── emails/               # order-confirmation.php
│       └── partials/             # Shared partial views
├── config/                       # Application configuration
│   ├── app.php                   # App name, env, debug, URL, timezone (Africa/Accra), currency (GHS)
│   ├── database.php              # MySQL connection with DATABASE_URL parsing (Railway/JawsDB/ClearDB)
│   └── paystack.php              # Public key, secret key, callback URL, webhook secret
├── database/                     # SQL files
│   ├── schema.sql                # Full database schema (22 tables, 443 lines)
│   └── seed.sql                  # Demo data: users, store, categories, brands, products, etc.
├── public/                       # Web-accessible assets
│   ├── css/style.css             # Custom styles + animations (284 lines)
│   ├── js/app.js                 # Frontend interactivity (438 lines)
│   ├── js/dashboard.js           # Dashboard-specific JS (316 lines)
│   ├── images/                   # Static images
│   └── uploads/                  # User-generated content (products, stores, banners)
├── storage/                      # Runtime storage
│   ├── logs/.gitkeep             # Application error logs
│   └── cache/.gitkeep            # Cache directory
├── .env                          # Environment variables (gitignored)
├── .env.example                  # Environment template
├── .gitignore                    # Git exclusion rules
├── .htaccess                     # URL rewriting, security headers, file access control
├── composer.json                 # PHP dependency declaration
├── index.php                     # Front controller / entry point
├── README.md                     # This file
├── INSTALL.md                    # Detailed installation guide
├── ARCHITECTURE.md               # System architecture documentation
└── CONTRIBUTING.md               # Contribution guidelines
```

---

## Database Schema

The platform uses 22 MySQL tables with foreign key constraints, indexes, and a FULLTEXT search index.

| Table | Records | Purpose |
|-------|---------|---------|
| `users` | User accounts | UUID, role (customer/vendor/admin), status, bcrypt password, login rate limiting |
| `password_resets` | Password reset tokens | Token, expiry, used-at tracking |
| `addresses` | User addresses | Multiple per user, default flag, Ghana-specific fields |
| `stores` | Vendor stores | Profile, branding, commission rate, verification status |
| `categories` | Product categories | Hierarchical (parent_id), sort order, active/inactive |
| `brands` | Product brands | Name, slug, logo, active/inactive |
| `products` | Products | Vendor/store ownership, pricing, discounts, inventory, status workflow (draft→pending→approved/rejected), FULLTEXT search |
| `product_images` | Product image gallery | Multiple per product, primary flag, sort order |
| `product_variants` | Variant groups | Color, Size, etc. — SKU, price adjustment, inventory |
| `product_variant_values` | Specific variant values | Red, XL, etc. — price adjustment, inventory |
| `carts` | Shopping carts | Guest (session) and user carts, coupon link, calculated totals |
| `cart_items` | Cart line items | Product, variant, quantity, unit price |
| `wishlists` | User wishlists | Unique per user+product |
| `orders` | Orders | Payment/order status tracking, shipping info, coupon codes |
| `order_items` | Order line items | Vendor attribution, commission calculation, per-item status |
| `payments` | Payment records | Paystack reference, amount, currency, channel, metadata |
| `reviews` | Product reviews | 1-5 rating, title, review body, approval status |
| `coupons` | Discount coupons | Percentage/fixed, min order, usage limit, expiry |
| `withdrawals` | Vendor payouts | Amount, fees, bank details, status workflow |
| `notifications` | User notifications | Type, title, message, JSON data, read/unread |
| `transactions` | Financial records | Payment, refund, commission, withdrawal, payout types |
| `settings` | Platform config | Key-value store with type and group |
| `banners` | Homepage banners | Image, title, subtitle, link, sort order |
| `email_templates` | Email templates | Name, subject, body, variable definitions |
| `vendor_bank_accounts` | Vendor bank details | Bank name, account number/name, default flag |

---

## Routes

### Public Routes

| Method | Route | Handler | Middleware | Description |
|--------|-------|---------|------------|-------------|
| GET | `/`, `/home` | HomeController@index | — | Homepage with featured products, banners, categories |
| GET | `/shop` | ShopController@index | — | Product listing with filters and pagination |
| GET | `/shop/category/{slug}` | ShopController@category | — | Category-filtered products |
| GET | `/shop/search` | ShopController@search | — | Keyword search with filters |
| GET | `/product/{slug}` | ShopController@show | — | Product detail with images, variants, reviews |
| GET | `/store/{slug}` | ShopController@store | — | Vendor store page |
| GET | `/cart` | CartController@index | — | Shopping cart |
| POST | `/cart/add` | CartController@add | — | Add item to cart |
| POST | `/cart/update` | CartController@update | — | Update cart item quantity |
| POST | `/cart/remove` | CartController@remove | — | Remove item from cart |
| POST | `/cart/apply-coupon` | CartController@applyCoupon | — | Apply discount coupon |
| GET | `/api/products` | ApiController@products | — | Product list API |
| GET | `/api/products/{id}` | ApiController@productDetail | — | Product detail API |
| GET | `/api/categories` | ApiController@categories | — | Category list API |
| GET | `/api/stores` | ApiController@stores | — | Store list API |

### Guest Routes

| Method | Route | Handler | Description |
|--------|-------|---------|-------------|
| GET | `/login` | AuthController@loginForm | Login form |
| POST | `/login` | AuthController@login | Login submission |
| GET | `/register` | AuthController@registerForm | Registration form |
| POST | `/register` | AuthController@register | Registration submission |
| GET | `/forgot-password` | AuthController@forgotPasswordForm | Password reset request form |
| POST | `/forgot-password` | AuthController@forgotPassword | Password reset request submission |
| GET | `/reset-password/{token}` | AuthController@resetPasswordForm | Password reset form |
| POST | `/reset-password` | AuthController@resetPassword | Password reset submission |

### Authenticated Routes

| Method | Route | Handler | Description |
|--------|-------|---------|-------------|
| GET | `/logout` | AuthController@logout | Logout |
| GET | `/wishlist` | WishlistController@index | View wishlist |
| POST | `/wishlist/toggle` | WishlistController@toggle | Toggle wishlist item |
| GET | `/checkout` | CheckoutController@index | Checkout page |
| POST | `/checkout/place-order` | CheckoutController@placeOrder | Place order |
| GET | `/dashboard` | DashboardController@index | Customer dashboard |
| GET | `/dashboard/orders` | DashboardController@orders | Order history |
| GET | `/dashboard/orders/{id}` | DashboardController@orderDetail | Order detail |
| GET | `/dashboard/wishlist` | DashboardController@wishlist | Wishlist management |
| GET | `/dashboard/addresses` | DashboardController@addresses | Address management |
| POST | `/dashboard/addresses` | DashboardController@saveAddress | Save address |
| POST | `/dashboard/addresses/delete` | DashboardController@deleteAddress | Delete address |
| GET | `/dashboard/profile` | DashboardController@profile | Edit profile |
| POST | `/dashboard/profile` | DashboardController@updateProfile | Update profile |
| GET | `/dashboard/reviews` | DashboardController@reviews | View reviews |
| POST | `/dashboard/reviews` | DashboardController@submitReview | Submit review |
| GET | `/dashboard/notifications` | DashboardController@notifications | View notifications |
| POST | `/dashboard/notifications/read` | DashboardController@markNotificationRead | Mark notification read |
| GET | `/checkout/callback` | CheckoutController@callback | Paystack callback |
| POST | `/checkout/webhook` | CheckoutController@webhook | Paystack webhook |

### Vendor Routes

All vendor routes require the `vendor` middleware (user must have `role = 'vendor'`).

| Method | Route | Handler | Description |
|--------|-------|---------|-------------|
| GET | `/vendor/dashboard` | VendorController@dashboard | Vendor dashboard with analytics |
| GET | `/vendor/products` | VendorController@products | Product list |
| GET | `/vendor/products/create` | VendorController@createProduct | Create product form |
| POST | `/vendor/products/store` | VendorController@storeProduct | Save new product |
| GET | `/vendor/products/{id}/edit` | VendorController@editProduct | Edit product form |
| POST | `/vendor/products/{id}/update` | VendorController@updateProduct | Update product |
| POST | `/vendor/products/{id}/delete` | VendorController@deleteProduct | Delete product |
| GET | `/vendor/orders` | VendorController@orders | Incoming orders |
| GET | `/vendor/orders/{id}` | VendorController@orderDetail | Order detail |
| POST | `/vendor/orders/{id}/status` | VendorController@updateOrderStatus | Update order status |
| GET | `/vendor/reviews` | VendorController@reviews | Customer reviews |
| GET | `/vendor/coupons` | VendorController@coupons | Coupon management |
| POST | `/vendor/coupons` | VendorController@storeCoupon | Create coupon |
| POST | `/vendor/coupons/{id}/delete` | VendorController@deleteCoupon | Delete coupon |
| GET | `/vendor/earnings` | VendorController@earnings | Earnings dashboard |
| GET | `/vendor/withdrawals` | VendorController@withdrawals | Withdrawal management |
| POST | `/vendor/withdrawals` | VendorController@requestWithdrawal | Request payout |
| GET | `/vendor/store` | VendorController@storeSettings | Store settings |
| POST | `/vendor/store` | VendorController@updateStore | Update store |
| GET | `/vendor/notifications` | VendorController@notifications | Notifications |

### Admin Routes

All admin routes require the `admin` middleware (user must have `role = 'admin'`).

| Method | Route | Handler | Description |
|--------|-------|---------|-------------|
| GET | `/admin/dashboard` | AdminController@dashboard | Analytics dashboard |
| GET | `/admin/users` | AdminController@users | User management |
| POST | `/admin/users/{id}/status` | AdminController@updateUserStatus | Suspend/activate user |
| GET | `/admin/vendors` | AdminController@vendors | Vendor management |
| POST | `/admin/vendors/{id}/verify` | AdminController@verifyVendor | Verify vendor |
| GET | `/admin/products` | AdminController@products | Product moderation |
| POST | `/admin/products/{id}/approve` | AdminController@approveProduct | Approve product |
| POST | `/admin/products/{id}/reject` | AdminController@rejectProduct | Reject product |
| POST | `/admin/products/{id}/featured` | AdminController@toggleFeatured | Toggle featured |
| GET | `/admin/categories` | AdminController@categories | Category management |
| POST | `/admin/categories` | AdminController@storeCategory | Create category |
| POST | `/admin/categories/{id}/delete` | AdminController@deleteCategory | Delete category |
| GET | `/admin/brands` | AdminController@brands | Brand management |
| POST | `/admin/brands` | AdminController@storeBrand | Create brand |
| POST | `/admin/brands/{id}/delete` | AdminController@deleteBrand | Delete brand |
| GET | `/admin/orders` | AdminController@orders | Order management |
| GET | `/admin/orders/{id}` | AdminController@orderDetail | Order detail |
| POST | `/admin/orders/{id}/status` | AdminController@updateOrderStatus | Update order |
| GET | `/admin/transactions` | AdminController@transactions | Transaction log |
| GET | `/admin/withdrawals` | AdminController@withdrawals | Withdrawal processing |
| POST | `/admin/withdrawals/{id}/process` | AdminController@processWithdrawal | Process withdrawal |
| GET | `/admin/banners` | AdminController@banners | Banner management |
| POST | `/admin/banners` | AdminController@storeBanner | Create banner |
| POST | `/admin/banners/{id}/delete` | AdminController@deleteBanner | Delete banner |
| GET | `/admin/settings` | AdminController@settings | Platform settings |
| POST | `/admin/settings` | AdminController@updateSettings | Update settings |
| GET | `/admin/notifications` | AdminController@notifications | Notification broadcast |
| POST | `/admin/notifications/send` | AdminController@sendNotification | Send notification |
| GET | `/admin/profile` | AdminController@profile | Admin profile |
| POST | `/admin/profile` | AdminController@updateProfile | Update profile |
| POST | `/admin/profile/password` | AdminController@updatePassword | Change password |

---

## API Endpoints

| Method | Endpoint | Query Parameters | Description |
|--------|----------|-----------------|-------------|
| GET | `/api/products` | `page`, `per_page`, `category`, `brand`, `search`, `min_price`, `max_price`, `sort` | Paginated, filterable product list |
| GET | `/api/products/{id}` | — | Single product with images, variants, reviews |
| GET | `/api/categories` | — | All active categories with product counts |
| GET | `/api/stores` | — | All verified active stores with product counts |

All API responses are JSON. Products include computed fields: `discount_percent`, `average_rating`, and `primary_image`.

---

## Payment Flow

1. Customer adds items to cart → proceeds to checkout
2. Order created with `payment_status = 'pending'`
3. Paystack checkout initialized via cURL to `https://api.paystack.co/transaction/initialize`
4. Customer redirected to Paystack's hosted payment page
5. Paystack redirects back to `/checkout/callback` on completion
6. Paystack sends webhook POST to `/checkout/webhook` (HMAC-SHA512 signed)
7. App verifies signature, checks amount matches, updates order to `payment_status = 'paid'`
8. Cart cleared, inventory deducted, customer sees order confirmation

**Supported payment methods:** Credit/Debit cards, Mobile Money (MoMo), Bank Transfer

---

## Security

| Measure | Implementation |
|---------|---------------|
| **CSRF Protection** | Token generated per session, required on all POST/PUT/DELETE forms, validated by `Middleware::csrf()` |
| **XSS Prevention** | `htmlspecialchars()` on every user-facing output value |
| **SQL Injection** | 100% parameterized PDO prepared statements — no raw query concatenation |
| **Password Hashing** | `password_hash()` with `PASSWORD_BCRYPT` |
| **Rate Limiting** | Login attempts throttled: max 5 per 15-minute window per session |
| **Session Security** | HTTP-only cookies, SameSite=Lax, session regeneration on login |
| **Input Validation** | Server-side via custom `Validator` class (required, email, numeric, min, max, unique, exists, confirmed) |
| **File Uploads** | Restricted to `jpeg`, `png`, `gif`, `webp` MIME types |
| **Webhook Security** | Paystack webhook signature verified via HMAC-SHA512 against stored secret |
| **Apache Hardening** | `.htaccess` blocks access to `.env`, `.sql`, `.log`, `.md` files and `app/` directory; security headers set |

---

## Responsive Design

Fully responsive across all breakpoints using Tailwind CSS:

- **Mobile** — Collapsible sidebar drawer, horizontal-scroll tables, touch-optimized targets
- **Tablet** — Condensed navigation, adaptive grid layouts
- **Desktop** — Full sidebar, multi-column product grids, dashboard layouts

Built with Tailwind CSS responsive utilities (`sm:`, `md:`, `lg:`, `xl:`, `2xl:`).

---

## Dark Mode

Toggle via the moon/sun icon in the footer. Automatically respects system `prefers-color-scheme` on first visit. Preference is persisted in `localStorage` and applied across all pages.

---

## License

MIT — See [LICENSE](LICENSE) file.

---

## Documentation

| Document | Description |
|----------|-------------|
| [INSTALL.md](INSTALL.md) | Detailed installation and deployment guide |
| [ARCHITECTURE.md](ARCHITECTURE.md) | System architecture, design decisions, and data flow |
| [CONTRIBUTING.md](CONTRIBUTING.md) | Contribution guidelines and development setup |
| [SECURITY.md](SECURITY.md) | Security policies and vulnerability reporting |

---

## Support

For issues, feature requests, and security concerns:
- **GitHub Issues**: [https://github.com/hollali/multi-vendor-store/issues](https://github.com/hollali/multi-vendor-store/issues)
- **Email**: support@celermarket.com
