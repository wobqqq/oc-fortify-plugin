# Fortify

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
- Password expiration support
- Password length control (4–128 characters)

#### Advanced Security
- Force HTTPS
- Force single session per user

## 📦 Requirements
- PHP 8.2 or higher
- October CMS 3.0 or higher

## 💻 Usage

All configuration and management is handled via the October CMS admin panel.

**Admin Panel:**
Navigate to `Settings -> Fortify` to view security settings and enable/disable features.

**Console Commands:**
- Disable Fortify completely:

```bash
php artisan wobqqq.fortify:config:disable
