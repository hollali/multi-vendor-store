# Celer Market - Installation Guide

Ghana's Multi-Vendor E-Commerce Platform

## Requirements

- PHP 8.1 or higher
- MySQL 5.7+ or MariaDB 10.3+
- Apache with mod_rewrite enabled (or Nginx)
- Composer (optional, for package management)
- Paystack merchant account (for payment processing)

## Quick Installation

### 1. Clone or Upload Files

Upload all project files to your web server document root (e.g., `/var/www/html/`).

### 2. Create Database

Access phpMyAdmin or MySQL CLI and run:

```sql
CREATE DATABASE IF NOT EXISTS celer_market CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Then import the schema:

```bash
mysql -u root -p celer_market < database/schema.sql
```

Then import seed data:

```bash
mysql -u root -p celer_market < database/seed.sql
```

### 3. Configure Environment

Edit `.env` file in the project root:

```env
APP_NAME="Celer Market"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://your-domain.com

DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=celer_market
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

PAYSTACK_PUBLIC_KEY=pk_live_xxxxxxxxxxxxxxxxx
PAYSTACK_SECRET_KEY=sk_live_xxxxxxxxxxxxxxxxx
```

### 4. Set Permissions

```bash
chmod -R 755 /path/to/celer-market
chmod -R 777 /path/to/celer-market/public/uploads
chmod -R 777 /path/to/celer-market/storage
```

### 5. Configure Apache

Ensure `.htaccess` files are enabled. In your Apache virtual host:

```apache
<Directory /var/www/html>
    AllowOverride All
</Directory>
```

Enable mod_rewrite:

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### 6. Configure Paystack Webhook

In your Paystack dashboard, set the webhook URL to:

```
https://your-domain.com/checkout/webhook
```

### 7. Access the Application

- **Homepage**: http://your-domain.com
- **Admin Login**: http://your-domain.com/login

### Default Login Credentials

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@celermarket.com | admin123 |
| Vendor | vendor@celermarket.com | admin123 |
| Customer | customer@celermarket.com | admin123 |

## Directory Structure

```
celer-market/
├── app/
│   ├── autoload.php          # PSR-4 autoloader
│   ├── core/                 # MVC framework classes
│   │   ├── Controller.php    # Base controller
│   │   ├── Database.php      # PDO database singleton
│   │   ├── Middleware.php     # Auth, CSRF, rate limiting
│   │   ├── Model.php         # Base model with query builder
│   │   ├── Router.php        # Route dispatcher
│   │   ├── Session.php       # Session management
│   │   └── Validator.php     # Input validation
│   ├── controllers/          # Application controllers
│   ├── models/               # Database models (22)
│   └── views/                # Blade-style PHP views
│       ├── layouts/          # Header, footer, sidebar
│       ├── auth/             # Login, register, password reset
│       ├── shop/             # Product browsing, search
│       ├── cart/             # Shopping cart
│       ├── checkout/         # Checkout & Paystack
│       ├── customer/         # Customer dashboard
│       ├── vendor/           # Vendor dashboard
│       └── admin/            # Admin dashboard
├── config/                   # Configuration files
├── database/
│   ├── schema.sql            # Complete database schema
│   └── seed.sql              # Sample data
├── public/
│   ├── css/style.css         # Custom styles
│   ├── js/
│   │   ├── app.js            # Frontend JavaScript
│   │   └── dashboard.js      # Dashboard JavaScript
│   └── uploads/              # User uploaded files
├── storage/                  # Logs and cache
├── .env                      # Environment configuration
├── .htaccess                 # Apache rewrite rules
└── index.php                 # Application entry point
```

## Features

### Customer
- User registration & login
- Product browsing & search
- Shopping cart with coupon support
- Secure checkout via Paystack
- Order tracking & history
- Product reviews & ratings
- Wishlist management
- Address management
- Profile management

### Vendor
- Store management
- Product CRUD with images & variants
- Inventory management
- Discount coupons
- Order management & fulfillment
- Customer reviews
- Sales analytics & earnings
- Withdrawal requests
- Store settings

### Admin
- Dashboard with analytics
- User & vendor management
- Product approval workflow
- Category & brand management
- Order management & disputes
- Transaction monitoring
- Withdrawal processing
- Banner management
- Site settings
- Notification system

## API Endpoints

| Endpoint | Method | Description |
|----------|--------|-------------|
| `/api/products` | GET | List products (paginated, filterable) |
| `/api/products/{id}` | GET | Product details |
| `/api/categories` | GET | List categories |
| `/api/stores` | GET | List verified stores |

## Security

- CSRF protection on all forms
- XSS prevention via output sanitization
- SQL injection prevention via prepared statements
- Password hashing with bcrypt
- Rate limiting on login attempts
- Secure session management
- Input validation & sanitization

## Paystack Integration

The application uses Paystack's standard checkout flow:

1. Customer adds items to cart and proceeds to checkout
2. Order is created with 'pending' payment status
3. Customer is redirected to Paystack for payment
4. Paystack sends webhook to `/checkout/webhook`
5. Application verifies payment and updates order status
6. Customer is redirected to order confirmation page

## Dark Mode

Toggle dark mode using the sun/moon icon in the footer. Preference is saved in localStorage.

## Support

For issues and feature requests, please contact: support@celermarket.com
