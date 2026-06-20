# Celer Market — Architecture Guide

## Overview

The Middle Man is built on a custom PHP MVC framework — no third-party PHP frameworks like Laravel or Symfony. This architecture was chosen to keep the application lightweight, fully understandable, and free from framework lock-in. The entire core framework is approximately 650 lines of PHP across 7 files.

---

## Architecture Philosophy

1. **Minimalism** — Only the essential abstractions needed for an MVC web application
2. **Transparency** — Every line of framework code is in the `app/Core/` directory; no magic
3. **Security by default** — CSRF tokens on every form, prepared statements everywhere, output always escaped
4. **Cloud-native** — Built for easy deployment on Railway, Heroku, and other PaaS platforms with DATABASE_URL support

---

## Request Lifecycle

```
Browser/Apache
    │
    ▼
.htaccess (URL rewriting)
    │
    ▼
index.php (Front Controller)
    │
    ├── Load .env environment variables
    ├── Import server-provided env vars (Railway/Heroku)
    ├── Start Session singleton
    ├── Register routes (Router::get/post)
    │
    ▼
Router::resolve($uri, $method)
    │
    ├── Match URI pattern against registered routes
    ├── Run middleware (if any):
    │   ├── auth       → redirect to /login if not authenticated
    │   ├── guest      → redirect to /dashboard if authenticated
    │   ├── admin      → 403 if role !== 'admin'
    │   ├── vendor     → redirect if role !== 'vendor'
    │   └── csrf       → 419 if token mismatch
    │
    ▼
Controller::action($params)
    │
    ├── Validate input (Validator)
    ├── Interact with Models (Database via PDO)
    ├── Business logic
    │
    ▼
Controller::renderView($view, $data)
    │
    ├── extract($data) → make variables available to view
    ├── ob_start() + require(view.php) → capture output
    ├── Rewrite relative URLs (for subdirectory deployments)
    │
    ▼
HTML response sent to browser
```

---

## Core Framework Components

### 1. Router (`app/Core/Router.php`)

The router maps HTTP method + URI patterns to controller actions.

**Key design:**

- Routes registered via fluent methods: `$router->get('/path', 'Controller@action', 'middleware')`
- URI patterns support `{param}` placeholders → extracted as named regex groups
- Middleware is a string name → mapped to a static method on `Middleware` class
- Returns 404 JSON if no route matches
- Controller dispatched via `ControllerClass::method()` with positional parameters

**Pattern matching:** `{slug}` becomes `(?P<slug>[^/]+)` regex, ensuring one URI segment per parameter.

### 2. Controller (`app/Core/Controller.php`)

Base controller providing shared functionality to all 10 application controllers.

**Provided methods:**
| Method | Purpose |
|--------|---------|
| `renderView($view, $data)` | Render a PHP view template with extracted variables |
| `renderJSON($data, $status)` | Return JSON response with status code |
| `redirect($url)` | HTTP redirect with base path prefix |
| `redirectWith($url, $message, $type)` | Redirect with flash message |
| `getRequestBody()` | Parse JSON request body |
| `getSettings()` | Load all settings from database |
| `formatPrice($amount)` | Format as GH₵ currency |
| `isPost()` | Check if request method is POST |

**View rendering:**

- `extract($data)` makes variables available in the view scope
- Output buffering captures the rendered HTML
- A URL-rewriting pass prepends `$basePath` to relative URLs (for subdirectory deployments like XAMPP/LAMPP)
- CSRF helpers (`csrf_field()`, `csrf_meta()`) injected into every view

### 3. Model (`app/Core/Model.php`)

Base model with an active-record-style query builder and static CRUD methods.

**Static CRUD:**
| Method | Description |
|--------|-------------|
| `Model::all()` | Fetch all rows, ordered by id DESC |
| `Model::find($id)` | Find by primary key |
| `Model::findBy($column, $value)` | Find by arbitrary column |
| `Model::create($data)` | Insert row, returns ID (filters fillable) |
| `Model::update($id, $data)` | Update row (filters fillable) |
| `Model::delete($id)` | Delete row |
| `Model::paginate($perPage, $page)` | Paginate with total/lastPage/from/to |

**Fluent query builder:**

```php
Product::where('category_id', $catId)
    ->where('is_active', 1)
    ->orderBy('created_at', 'DESC')
    ->limit(10)
    ->get();
```

**Fillable protection:** Each model defines `$fillable` — only listed columns are mass-assignable via `create()` and `update()`.

### 4. Database (`app/Core/Database.php`)

PDO singleton wrapper providing a clean query interface.

**Methods:**
| Method | Description |
|--------|-------------|
| `query($sql, $params)` | Execute statement, return PDOStatement |
| `fetch($sql, $params)` | Fetch single row as stdClass |
| `fetchAll($sql, $params)` | Fetch all rows as array of stdClass |
| `insert($table, $data)` | INSERT with named placeholders, returns lastInsertId |
| `update($table, $data, $where, $params)` | UPDATE with WHERE clause |
| `delete($table, $where, $params)` | DELETE with WHERE clause |
| `beginTransaction()` / `commit()` / `rollback()` | Transaction control |

**Connection configuration:** Reads from environment variables with DATABASE_URL parsing for cloud platforms (Railway, JawsDB, ClearDB). Sets UTF8MB4 charset, exception error mode, object fetch mode, and native prepares.

### 5. Session (`app/Core/Session.php`)

Singleton session manager.

**Key features:**

- Secure cookie params: HTTP-only, SameSite=Lax, HTTPS-only when applicable
- Flash messages: set once, retrieved once (`getFlash` auto-deletes)
- User state: `setUser()`, `getUser()`, `isAuthenticated()`, `getUserRole()`
- Session regeneration on login

### 6. Middleware (`app/Core/Middleware.php`)

Request filters executed before controller actions.

| Middleware        | Behavior                                                                            |
| ----------------- | ----------------------------------------------------------------------------------- |
| `auth`            | Redirects to /login if not authenticated; returns 401 for AJAX                      |
| `guest`           | Redirects to /dashboard if already authenticated                                    |
| `admin`           | Returns 403 (or redirects) if role !== 'admin'                                      |
| `vendor`          | Redirects to /dashboard if role !== 'vendor'                                        |
| `customer`        | Redirects if role !== 'customer'                                                    |
| `csrf`            | Validates CSRF token from POST body or X-CSRF-TOKEN header; returns 419 on mismatch |
| `rateLimit($key)` | Max 5 attempts per 15 minutes per session key                                       |

### 7. Validator (`app/Core/Validator.php`)

Lightweight input validation with pipe-delimited rule syntax.

**Supported rules:** `required`, `email`, `numeric`, `integer`, `min:N`, `max:N`, `confirmed`, `unique:table,column,ignoreId`, `exists:table,column`

**Sanitization helpers:** `sanitize()`, `sanitizeEmail()`, `sanitizeString()` — static methods for input cleaning.

---

## Application Controllers

### HomeController

Fetches featured products, latest products, active categories, active banners, and trending products for the homepage.

### AuthController

Handles registration with validation, login with rate limiting, logout with session destruction, and password reset flow (forgot → email token → reset).

### ShopController

Product browsing with category, brand, and price range filters; keyword search via MySQL FULLTEXT index; sorting (newest, price, name, rating); product detail page with image gallery, variant selection, reviews, and related products; vendor store page.

### CartController

Session-based cart for guests, database cart for logged-in users; cart merging on login; AJAX add/update/remove; coupon validation and application.

### CheckoutController

Complete Paystack integration:

- Transaction initialization via cURL to Paystack API
- Order creation with pending status
- Callback verification
- Webhook handling with HMAC-SHA512 signature verification
- Amount mismatch detection (anti-tampering)
- Inventory deduction on successful payment
- Cart clearing on successful payment

### DashboardController

Customer dashboard: order history with pagination, order details, wishlist management, address CRUD, profile editing, review submission (with purchase verification), notification management.

### WishlistController

Toggle products in wishlist with AJAX support; duplicate detection.

### VendorController (largest controller, ~970 lines)

Vendor dashboard: product CRUD with image upload and variant management, order management with per-item status updates, review viewing, coupon CRUD, earnings analytics, withdrawal requests, store profile/branding, notifications.

### AdminController (largest controller, ~1100 lines)

Platform administration: analytics dashboard with revenue chart, user management, vendor verification, product approval workflow (pending→approved/rejected), category CRUD, brand CRUD, order management, transaction log, withdrawal processing, banner management, platform settings (key-value), notification broadcasting, profile management.

### ApiController

RESTful API: paginated product listing, product detail, category listing, store listing — all returning JSON.

---

## Models

Each of the 22 models extends `App\Core\Model` and defines:

- `$table` — database table name
- `$primaryKey` — default `'id'`
- `$fillable` — mass-assignable columns

**Key model methods (defined on individual models):**

| Model          | Methods                                                                                                                                    |
| -------------- | ------------------------------------------------------------------------------------------------------------------------------------------ |
| `User`         | `findByEmail()`, `isAdmin()`, `isVendor()`, `getStore()`, `getAddresses()`                                                                 |
| `Product`      | `scopeActive()`, `scopeFeatured()`, `search()`, `getPrice()`, `getDiscountPercent()`, `getAverageRating()`, `getImages()`, `getVariants()` |
| `Order`        | `generateOrderNumber()`, `scopeByUser()`, `scopeByStatus()`, `getItems()`, `getPayment()`                                                  |
| `Store`        | `scopeActive()`, `scopeVerified()`, `getRating()`, `getProductCount()`                                                                     |
| `Cart`         | `getCartForUser()`, `calculateTotals()`, `addItem()`, `removeItem()`, `mergeGuestCart()`                                                   |
| `Category`     | `getBreadcrumbs()`, `scopeParents()`, `children()`, `getProductCount()`                                                                    |
| `Coupon`       | `findByCode()`, `calculateDiscount()`, `isValid()`, `incrementUsed()`                                                                      |
| `Review`       | `scopeApproved()`, `scopeByProduct()`, `getAverageRating()`                                                                                |
| `Notification` | `scopeUnread()`, `markAsRead()`, `createForUser()`                                                                                         |
| `Setting`      | `getValue()`, `setValue()`, `getByGroup()`                                                                                                 |
| `Address`      | `scopeByUser()`, `scopeDefault()`                                                                                                          |
| `Wishlist`     | `toggle()`, `isInWishlist()`, `getUserWishlist()`                                                                                          |
| `Payment`      | `scopeByReference()`                                                                                                                       |

---

## Views

Views are plain PHP files (no template engine) organized in a Blade-like directory structure under `app/Views/`.

**Layout system:**

- `layouts/header.php` — HTML head, Tailwind/FontAwesome CDN, navigation bar, search, cart badge, user menu, mobile hamburger, flash messages
- `layouts/footer.php` — Footer with links, newsletter form, dark mode toggle, social icons
- `layouts/sidebar.php` — Role-based sidebar (Customer, Vendor, Admin specific navigation)

**Accessibility helpers injected by Controller:**
| Variable | Type | Description |
|----------|------|-------------|
| `$user` | object\|null | Current authenticated user data |
| `$settings` | array | Platform settings key-value pairs |
| `$csrf_field()` | callable | Returns hidden input with CSRF token |
| `$csrf_meta()` | callable | Returns meta tag with CSRF token |
| `$flash()` | callable | Returns array of flash messages by type |
| `$currency_symbol` | string | `GH₵` |
| `$site_name` | string | `Celer Market` |
| `$url($path)` | callable | Generates absolute URL with base path prefix |

---

## Paystack Integration Flow

```
Customer clicks "Place Order"
    │
    ▼
CheckoutController@placeOrder
    ├── Validates cart, addresses, coupon
    ├── Creates order (payment_status = 'pending')
    ├── Creates order_items with commission calculation
    │
    ▼
Paystack API: POST /transaction/initialize
    ├── Sends: amount, email, callback_url, metadata(order_id)
    ├── Receives: authorization_url, access_code, reference
    │
    ▼
Redirect customer to Paystack payment page
    │
    ├── Customer pays via card/MoMo/bank transfer
    │
    ▼
Paystack redirects to /checkout/callback
    ├── CheckoutController@callback
    │   ├── Verifies transaction via Paystack API
    │   └── Renders order confirmation
    │
    ▼
Paystack sends POST /checkout/webhook
    ├── CheckoutController@webhook
    │   ├── Verifies HMAC-SHA512 signature
    │   ├── Checks amount matches order total
    │   ├── Updates order to paid
    │   ├── Deducts inventory
    │   ├── Clears cart
    │   └── Creates notifications
```

**Security measures:**

- Webhook signature verified with `hash_hmac('sha512', ...)` against stored secret
- Amount comparison prevents tampering (compares Paystack amount vs order total)
- Only processes webhooks for known references
- Idempotent — checks payment status before updating

---

## Cart System Design

The cart supports two modes:

1. **Guest cart** — Identified by `session_id`, stored in `carts` table. Cart persists across browser sessions via PHP session cookie.
2. **User cart** — Identified by `user_id`, stored in `carts` table. One active cart per user.

**Cart merging:** When a guest logs in, any guest cart items are merged into the user's existing cart. Duplicate products have their quantities combined.

**Cart totals:** Calculated on every cart change: subtotal, discount (from coupon), tax (from settings), shipping, and total.

---

## Product Approval Workflow

```
Vendor creates product (status = 'draft')
    │
    ▼
Vendor submits for approval (status = 'pending')
    │
    ▼
Admin reviews in /admin/products
    │
    ├── Approve → status = 'approved', is_approved = 1, is_active = 1
    │
    └── Reject → status = 'rejected', rejection_reason stored
            │
            Vendor can edit and resubmit
```

---

## Commission System

- Each store has a `commission_rate` (decimal percent, default 10.00%)
- When an order is placed, `order_items` records `commission_rate`, `commission_amount`, and `vendor_earnings`
- `commission_amount = unit_price * quantity * (commission_rate / 100)`
- `vendor_earnings = total_price - commission_amount`
- Withdrawals: vendors request payouts of accumulated earnings; admin processes them

---

## Environment Configuration

The application supports multiple sources for configuration, checked in order:

1. `.env` file (loaded manually in `index.php` with `putenv()` and `$_ENV`)
2. `$_SERVER` variables (set by PHP-FPM/Apache)
3. `$_ENV` variables
4. `getenv()` (already set)
5. Cloud platform DATABASE_URL parsing (Railway `MYSQL_URL`, JawsDB `JAWSDB_URL`, ClearDB `CLEARDB_DATABASE_URL`)

This layered approach ensures compatibility across local development, shared hosting, and PaaS platforms.

---

## File Uploads

Uploaded files are stored in `public/uploads/` organized by type:

- `public/uploads/products/` — Product images
- `public/uploads/stores/` — Store logos and banners
- `public/uploads/banners/` — Homepage banners
- `public/uploads/avatars/` — User profile pictures

Allowed MIME types: `image/jpeg`, `image/png`, `image/gif`, `image/webp`. Files are renamed with timestamps to prevent conflicts.

---

## Error Handling

- Development: `APP_DEBUG=true` shows detailed error messages
- Production: `APP_DEBUG=false` returns generic JSON error response
- All errors logged to `storage/logs/error.log`
- Database connection failures include debugging info (host, port, database, error) when in debug mode

---

## Cloud Platform Deployment

The application is designed for easy deployment on:

- **Railway** — Automatic DATABASE_URL detection via `MYSQL_URL` env var
- **Heroku** — ClearDB / JawsDB addon compatibility via `CLEARDB_DATABASE_URL` / `JAWSDB_URL`
- **Shared hosting** — Standard `.env` file configuration

The `.htaccess` file handles URL rewriting, security headers, file access control, and caching headers — no Apache virtual host configuration needed beyond enabling `mod_rewrite` and `AllowOverride All`.
