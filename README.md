# Fortify

[![CI](https://github.com/wobqqq/oc-fortify-plugin/actions/workflows/ci.yml/badge.svg)](https://github.com/wobqqq/oc-fortify-plugin/actions/workflows/ci.yml)
[![October CMS](https://img.shields.io/badge/October%20CMS-3.x%20%7C%204.x-e24848)](https://octobercms.com/plugin/wobqqq-fortify)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777bb4)](composer.json)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%20max-brightgreen)](phpstan.neon.dist)
[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE.md)

**Fortify** is a comprehensive security suite for October CMS that helps you harden your application, monitor vulnerabilities, and enforce best security practices.

It provides system diagnostics, configuration hardening tools, and integrates seamlessly with additional Fortify extensions.

## 📊 Security Dashboard Widget

Fortify includes a built-in dashboard widget that gives you a real-time overview of your system’s security status.

- Highlights critical vulnerabilities and misconfigurations
- Provides quick access to all security checks and tools
- Helps you identify and fix issues in one place

This widget acts as a central hub, allowing you to monitor and manage your application's security at a glance.

## 🚀 Features

### 🔍 System Security Checks
- **Application debug mode is disabled**
  Ensures your application is not exposing sensitive debug information.

- **Production environment validation**
  Confirms that your application is running in a secure production mode.

- **Admin panel URI check**
  Warns if `/admin` is used, as it is commonly targeted by bots.

- **Superuser accounts check**
  Detects if the number of superusers exceeds recommended limits.

- **Outdated administrator accounts detection**
  Identifies inactive or outdated admin users.

- **Sensitive usernames detection**
  Detects unsafe usernames like `admin`.

- **Pending software updates**
  Alerts about available system and plugin updates.

### 🛡️ Security Scanners
- **Sensitive files checker**
  Scans for publicly accessible sensitive files.

- **Sensitive TCP ports checker**
  Detects open ports that may expose services.

- **SSL certificate checker**
  Validates SSL certificate configuration and expiration.

### 🧩 Integrated Modules
Fortify works with additional extensions:

- [Admin IP Access](https://octobercms.com/plugin/wobqqq-fortifyadminipaccess)
- [IP Blocker](https://octobercms.com/plugin/wobqqq-fortifyipblocker)
- [Smart IP Blocker](https://octobercms.com/plugin/wobqqq-fortifysmartipblocker)
- [CSP](https://octobercms.com/plugin/wobqqq-fortifycsp)
- [Input Sanitizer](https://octobercms.com/plugin/wobqqq-fortifyinputsanitizer)

Each module extends Fortify with additional protection layers.

### ⚙️ Security Configuration

#### Cookies & Sessions
- Same-Site Cookies
- Session Lifetime control
- HTTPS-only cookies
- HTTP-only cookies
- Session encryption

#### Authentication & Password Policies
- Allow self-service password reset
- Require uppercase letters (A–Z)
- Require lowercase letters (a–z)
- Require numbers
- Require non-alphabetic characters
- Password expiration after a number of days
- Password length control (4–128 characters)

#### Advanced Security
- Force HTTPS
- Force single session per user

## 📦 Requirements
- PHP 8.2 or higher
- October CMS 3.x or 4.x

## 💻 Usage

All configuration and management is handled via the October CMS admin panel.

**Admin Panel:**
Navigate to `Settings -> Fortify` to view security settings and enable/disable features.

**Console Commands:**
- Disable Fortify completely:

```bash
php artisan wobqqq.fortify:config:disable
```

## ⬆️ Upgrading

- **1.0.3** — password expiration is now a number of days (`0` turns it off). The previous switch never expired a password (October compared the days with `true`); the new setting starts at `0`, so nothing changes on update: set the number of days to start using it. The non-alphanumeric password rule is now applied to administrators' passwords.

## 🔒 Security

Please report a vulnerability privately, as described in [SECURITY.md](SECURITY.md).

## 🛠️ Development

The toolchain runs in Docker, the host needs nothing but `docker` and `make`:

```bash
make install        # composer install
make code.fix       # composer normalize, Rector, PHP CS Fixer
make code.check     # composer validate/audit, php -l, YAML lint, PHP CS Fixer, Rector, PHPStan (level max)
make test.coverage  # Pest with coverage (90 % minimum)
make ready          # everything above
```

Every pull request runs the same checks on GitHub Actions, plus a syntax check on PHP 8.2. Pushing a tag that matches the last version in `updates/version.yaml` releases it to the October CMS marketplace once CI has passed.
