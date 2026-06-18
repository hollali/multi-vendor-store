# Security Policy

## Supported Versions

| Version | Supported |
|---------|-----------|
| 1.0.x | ✅ Active support |

## Reporting a Vulnerability

We take the security of Celer Market seriously. If you discover a security vulnerability, please follow responsible disclosure:

1. **Do not** open a public GitHub issue
2. **Email**: security@celermarket.com
3. **Include**:
   - Description of the vulnerability
   - Steps to reproduce
   - Affected versions
   - Potential impact
   - Any suggested mitigation (optional)

### Response Timeline

- **24-48 hours**: Initial acknowledgment of your report
- **5-7 days**: Assessment and validation
- **14 days**: Fix development and testing
- **21 days**: Public disclosure (if applicable)

## Security Measures in Place

### Application Level

| Measure | Implementation |
|---------|---------------|
| CSRF Protection | Token-based validation on all POST/PUT/DELETE requests |
| XSS Prevention | `htmlspecialchars()` on all output |
| SQL Injection | 100% parameterized PDO prepared statements |
| Password Storage | Bcrypt via `password_hash()` |
| Session Security | HTTP-only cookies, SameSite=Lax, HTTPS-only |
| Rate Limiting | Login: 5 attempts per 15 minutes per session |
| File Uploads | Restricted to image MIME types only |
| Input Validation | Server-side validation on all inputs |

### Infrastructure Level

| Measure | Implementation |
|---------|---------------|
| `.env` Protection | Blocked via `.htaccess` |
| Directory Access | `app/` directory blocked via `.htaccess` |
| Sensitive Files | `.sql`, `.md`, `.log` files blocked via `.htaccess` |
| Security Headers | `X-Content-Type-Options`, `X-Frame-Options`, `X-XSS-Protection`, `Referrer-Policy` |
| HTTPS | Configurable via `.htaccess` (uncomment to enable) |

### Payment Security

- Paystack webhooks verified via HMAC-SHA512 signature
- Amount verification prevents tampering
- Payment status idempotency checks
- No credit card data stored locally — all payment processing is Paystack-hosted

## Best Practices for Deployment

1. **Set strong passwords** — change all default credentials immediately
2. **Enable HTTPS** — uncomment the HTTPS rewrite rule in `.htaccess`
3. **Set `APP_DEBUG=false`** in production
4. **Use strong Paystack webhook secret** — generate a random 64-character string
5. **Restrict file permissions** — `public/uploads` and `storage` should be the only writable directories
6. **Regular backups** — backup database and `public/uploads/` directory
7. **Keep PHP updated** — use a supported PHP version with security patches
8. **Monitor logs** — regularly check `storage/logs/error.log` for suspicious activity

## Reporting Process

We appreciate the community's help in keeping Celer Market secure. Contributors who report valid security vulnerabilities will be credited in release notes (unless anonymity is requested).
