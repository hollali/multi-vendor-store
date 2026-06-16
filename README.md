# Celer Market

**Ghana's Multi-Vendor E-Commerce Platform** — A full-featured marketplace where customers buy from multiple vendors, vendors manage their own stores, and administrators oversee the entire platform.

[![PHP](https://img.shields.io/badge/PHP-8.1+-777BB4?logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-5.7+-4479A1?logo=mysql&logoColor=white)](https://mysql.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind-3-06B6D4?logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
[![Paystack](https://img.shields.io/badge/Paystack-Integration-0BA95B?logo=paystack&logoColor=white)](https://paystack.com)
[![License](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)

---

## Overview

Celer Market is a custom PHP MVC multi-vendor e-commerce platform built for the Ghanaian market. It provides a complete online marketplace experience with three user roles (Customer, Vendor, Admin), Paystack payment integration, and a responsive Tailwind CSS interface with dark mode support.

### Built With
- **PHP 8.1+** — Custom MVC framework (no Laravel, no Composer)
- **MySQL/MariaDB** — Relational database with 22 models
- **Apache** — `mod_rewrite` for clean URLs
- **Tailwind CSS 3** — CDN-based utility-first styling
- **Font Awesome 6** — Icon library
- **Chart.js** — Dashboard analytics charts
- **Paystack** — Payment processing (cards, mobile money, bank transfer)
- **Google Fonts (Inter)** — Typography

### Architecture
- **Custom MVC Framework** — Router, Controller, Model, View, Session, Database, Validator, Middleware
- **PSR-4 Autoloading** — Namespace-based class autoloading
- **Prepared Statements** — SQL injection protection via PDO
- **CSRF Protection** — Token-based form security
- **Dark Mode** — System preference detection with manual toggle, saved to localStorage

---

## Features

### 👤 Customer
| Feature | Details |
|---------|---------|
| **Account** | Register, login, profile management, avatar upload |
| **Browse Products** | Category filters, brand filters, price range, search with suggestions, sort options |
| **Shopping Cart** | Add/remove items, quantity controls, coupon codes |
| **Checkout** | Saved addresses, Paystack payments (cards, MoMo, bank transfer) |
| **Orders** | Order history with status tracking, order details |
| **Wishlist** | Save products for later |
| **Reviews** | Rate and review purchased products |
| **Notifications** | Real-time notification feed |

### 🏪 Vendor
| Feature | Details |
|---------|---------|
| **Store Management** | Store profile, settings, branding |
| **Products** | Full CRUD with images, variants (size/color), SKU, inventory tracking |
| **Orders** | Incoming order management, fulfillment |
| **Coupons** | Create and manage discount codes |
| **Earnings** | Sales analytics dashboard, revenue chart |
| **Withdrawals** | Request payouts, track withdrawal history |
| **Reviews** | View and respond to customer reviews |

### 🔐 Admin
| Feature | Details |
|---------|---------|
| **Dashboard** | Platform-wide analytics with revenue chart |
| **Users** | Manage customers, suspend/activate accounts |
| **Vendors** | Vendor approvals, store management |
| **Products** | Product approval workflow, moderation |
| **Categories & Brands** | Create and manage taxonomy |
| **Orders** | View all platform orders, dispute management |
| **Transactions** | Financial monitoring |
| **Withdrawals** | Process vendor payout requests |
| **Banners** | Homepage carousel management |
| **Settings** | Platform configuration |
| **Notifications** | System-wide notification broadcasting |

---

## Quick Start

### Prerequisites
- PHP 8.1+
- MySQL 5.7+ / MariaDB 10.3+
- Apache with `mod_rewrite`
- Paystack merchant account

### Setup (5 minutes)

```bash
# 1. Clone the repository
git clone https://github.com/hollali/multi-vendor-store.git
cd multi-vendor-store

# 2. Create the database and import schema + seed data
mysql -u root -p -e "CREATE DATABASE celer_market"
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
│   ├── Core/              # MVC framework
│   │   ├── Controller.php # Base controller
│   │   ├── Database.php   # PDO singleton
│   │   ├── Middleware.php # Auth, CSRF, rate limiting
│   │   ├── Model.php      # Query builder base model
│   │   ├── Router.php     # Route dispatcher
│   │   ├── Session.php    # Session abstraction
│   │   └── Validator.php  # Input validation
│   ├── Controllers/       # 10 controllers
│   ├── Models/            # 22 database models
│   └── Views/             # Blade-style PHP templates
│       ├── layouts/       # Header, footer, sidebar
│       ├── auth/          # Login, register, password reset
│       ├── shop/          # Product browsing, search
│       ├── cart/          # Shopping cart
│       ├── checkout/      # Checkout + Paystack
│       ├── customer/      # Customer dashboard (8 views)
│       ├── vendor/        # Vendor dashboard (12 views)
│       ├── admin/         # Admin dashboard (13 views)
│       └── emails/        # Order confirmation email
├── config/                # App, database, Paystack config
├── database/
│   ├── schema.sql         # Full database schema (28 tables)
│   └── seed.sql           # Demo data (users, products, etc.)
├── public/
│   ├── css/style.css      # Custom styles + animations
│   ├── js/app.js          # Frontend interactivity
│   ├── js/dashboard.js    # Dashboard-specific JS
│   └── uploads/           # User-generated content
├── storage/               # Logs and cache
├── .htaccess              # URL rewriting
└── index.php              # Entry point (front controller)
```

---

## API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/products` | Paginated, filterable product list |
| GET | `/api/products/{id}` | Single product details |
| GET | `/api/categories` | All categories |
| GET | `/api/stores` | All verified stores |

---

## Payment Flow

1. Customer adds items to cart → proceeds to checkout
2. Order created with `pending` payment status
3. Customer redirected to Paystack for payment
4. Paystack sends webhook to `/checkout/webhook`
5. App verifies payment → updates order to `paid`
6. Customer confirmed on order page

**Supported**: Credit/Debit cards, Mobile Money (MoMo), Bank Transfer

---

## Security

- **CSRF** — Token on every form
- **XSS** — `htmlspecialchars()` on all output
- **SQL Injection** — Parameterized PDO queries
- **Passwords** — Bcrypt hashing
- **Rate Limiting** — Login attempt throttling
- **Session** — Server-side with secure flags
- **Validation** — Server + client-side form validation

---

## Responsive Design

Fully responsive across all breakpoints:
- **Mobile** — Collapsible sidebar drawer, horizontal-scroll tables, touch-optimized targets
- **Tablet** — Condensed navigation, adaptive grids
- **Desktop** — Full sidebar, multi-column layouts

Built with Tailwind CSS responsive utilities (`sm:`, `md:`, `lg:`, `xl:`, `2xl:`).

---

## Dark Mode

Toggle via the moon/sun icon in the footer. Automatically respects system `prefers-color-scheme`. Preference is persisted in `localStorage`.

---

## License

MIT — See [LICENSE](LICENSE) file.

## Support

For issues and feature requests: support@celermarket.com
