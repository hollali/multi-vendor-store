# Contributing to Celer Market

Thank you for considering contributing to Celer Market! This document outlines the guidelines and processes for contributing.

---

## Table of Contents

- [Code of Conduct](#code-of-conduct)
- [Getting Started](#getting-started)
- [Development Setup](#development-setup)
- [Coding Standards](#coding-standards)
- [Pull Request Process](#pull-request-process)
- [Commit Guidelines](#commit-guidelines)
- [Testing](#testing)
- [Reporting Issues](#reporting-issues)

---

## Code of Conduct

By participating in this project, you agree to:
- Be respectful and inclusive in all interactions
- Focus on constructive feedback and collaboration
- Accept responsibility for mistakes and work toward resolution
- Prioritize the well-being of the community

---

## Getting Started

1. **Fork the repository** on GitHub
2. **Clone your fork:**
   ```bash
   git clone https://github.com/your-username/multi-vendor-store.git
   cd multi-vendor-store
   ```
3. **Set up the development environment** (see [Development Setup](#development-setup) below)
4. **Create a feature branch:**
   ```bash
   git checkout -b feature/your-feature-name
   ```

---

## Development Setup

### Prerequisites

- PHP 8.1+
- MySQL 5.7+ / MariaDB 10.3+
- Apache with `mod_rewrite` enabled
- Composer (for autoloader optimization, optional)

### Local Development

1. **Create the database:**
   ```bash
   mysql -u root -p -e "CREATE DATABASE celer_market CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
   ```

2. **Import schema and seed data:**
   ```bash
   mysql -u root -p celer_market < database/schema.sql
   mysql -u root -p celer_market < database/seed.sql
   ```

3. **Configure environment:**
   ```bash
   cp .env.example .env
   ```
   Edit `.env` with your local database credentials.

4. **Set permissions:**
   ```bash
   chmod -R 755 .
   chmod -R 777 public/uploads storage
   ```

5. **Start PHP built-in server (alternative to Apache):**
   ```bash
   php -S localhost:8000
   ```

### Default Development Credentials

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@celermarket.com | admin123 |
| Vendor | vendor@celermarket.com | admin123 |
| Customer | customer@celermarket.com | admin123 |

---

## Coding Standards

### PHP

- **PHP 8.1+ features are encouraged**: named arguments, match expressions, readonly properties, etc.
- **Namespaces**: All application code under `App\` namespace, matching directory structure
- **PSR-4 autoloading**: Directory structure mirrors namespace hierarchy
- **No trailing whitespace** on any line
- **Unix line endings** (LF)
- **One class per file**
- **Type hints**: Use typed properties, parameter types, and return types wherever possible

### Naming Conventions

| Element | Convention | Example |
|---------|-----------|---------|
| Classes | PascalCase | `ProductController`, `User` |
| Methods | camelCase | `getUser()`, `findByEmail()` |
| Properties | camelCase | `$currentUser`, `$queryBuilder` |
| Variables | camelCase | `$orderTotal`, `$productName` |
| Constants | UPPER_SNAKE_CASE | `MAX_LOGIN_ATTEMPTS` |
| Database tables | snake_case, plural | `order_items`, `product_images` |
| Database columns | snake_case | `is_featured`, `commission_rate` |
| Routes | kebab-case | `/reset-password/{token}`, `/shop/category/{slug}` |
| Views | kebab-case | `order-detail.php`, `product-form.php` |

### PHP Code Style

- Opening `{` on same line as control structures
- Spaces around operators (`=`, `=>`, `.`, `==`, etc.)
- Spaces after control structure keywords (`if `, `foreach `, `while `)
- One blank line between methods
- No closing `?>` tag in pure PHP files

### SQL

- Uppercase SQL keywords (`SELECT`, `INSERT`, `WHERE`, `JOIN`)
- Lowercase table and column names (snake_case)
- Prepared statements with named placeholders (`:id`, `:value`)
- Never concatenate user input into SQL strings

### Views (PHP Templates)

- Minimize logic in views — use controller to prepare data
- Always escape output: `<?= htmlspecialchars($variable) ?>`
- Use PHP's alternative syntax for control structures in templates: `<?php if (condition): ?> ... <?php endif; ?>`
- One PHP block per file section rather than many small open/close blocks

### Frontend

- **Tailwind CSS**: Use utility classes directly in HTML; avoid custom CSS where possible
- **JavaScript**: ES6+ syntax, `const`/`let` instead of `var`, arrow functions
- **No inline JS event handlers** — use `addEventListener` in `app.js`
- **AJAX**: Use `fetch()` API with JSON headers and CSRF token

---

## Pull Request Process

1. **Create an issue** first describing the bug or feature (unless trivial)
2. **Fork and branch** from `main`
3. **Make your changes** following the coding standards
4. **Write or update tests** if applicable
5. **Update documentation** (README, inline docs) if your change affects usage
6. **Run a syntax check:**
   ```bash
   php -l app/Core/Database.php
   # ... repeat for all changed files
   ```
7. **Commit your changes** following commit guidelines
8. **Push to your fork** and submit a pull request to `main`
9. **In the PR description**, reference the issue number and describe:
   - What the change does
   - Why it's needed
   - How it was tested

### PR Review Criteria

- Code follows project coding standards
- No security regressions (SQL injection, XSS, CSRF)
- Backward compatible where possible
- Proper error handling
- No dead code or debug artifacts (`var_dump`, `dd()`, `print_r`)

---

## Commit Guidelines

We follow [Conventional Commits](https://www.conventionalcommits.org/):

```
<type>(<scope>): <short description>

[optional body]
```

### Types

| Type | Usage |
|------|-------|
| `feat` | A new feature |
| `fix` | A bug fix |
| `docs` | Documentation only changes |
| `style` | Code style changes (formatting, missing semicolons, etc.) |
| `refactor` | Code change that neither fixes a bug nor adds a feature |
| `perf` | Performance improvement |
| `test` | Adding or updating tests |
| `chore` | Build process, dependencies, admin tasks |
| `security` | Security fix |

### Examples

```
feat(vendor): add bulk product export CSV
fix(cart): correct coupon discount calculation for fixed amounts
docs(api): document pagination query parameters
security(auth): add rate limiting to login endpoint
```

---

## Testing

The project currently has no formal test suite. When contributing:

1. **Manually test** your changes in the browser
2. **Test edge cases**: empty states, invalid input, boundary values
3. **Test across roles**: verify behavior for customer, vendor, and admin
4. **Test error paths**: invalid data, expired sessions, network failures
5. **Check the error log**: `storage/logs/error.log` for any new warnings/errors

If you add a significant feature, please include manual test steps in your PR description.

---

## Reporting Issues

### Bug Reports

When reporting bugs, please include:

- **Environment**: PHP version, MySQL version, web server
- **Steps to reproduce**: exact steps to trigger the bug
- **Expected behavior**: what should happen
- **Actual behavior**: what actually happens
- **Screenshots/error messages**: if applicable
- **Relevant code**: if you can identify the problematic code

### Feature Requests

Feature requests should include:

- **Use case**: what problem does this solve?
- **Proposed solution**: how should it work?
- **Alternative solutions**: any other approaches considered
- **Priority**: how critical is this feature?

---

## Security Vulnerabilities

**Do not open public issues for security vulnerabilities.** Instead, email security@celermarket.com or see [SECURITY.md](SECURITY.md) for responsible disclosure.

---

## Questions?

- Open a [GitHub Discussion](https://github.com/hollali/multi-vendor-store/discussions)
- Email: support@celermarket.com

---

Thank you for contributing to Celer Market!
