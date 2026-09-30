# AGENTS.md

Guidance for AI coding agents (Claude Code, Codex, Junie, Cursor) working in this repository.

## What this is

**Fortify** (`Wobqqq.Fortify`) is the core of a security suite for October CMS 3.x/4.x (built and tested against 4.4 on Laravel 12, PHP 8.2+). It:

- hardens the application config at boot (`ConfigService::overrideConfig()`: session cookies, the backend password policy, forced HTTPS, single session) from the settings page **Settings → Fortify**;
- draws a dashboard report widget (`widgets/Fortify.php`) with system checks and three scanners: sensitive files over HTTP, open TCP ports, TLS certificates;
- is the extension point of five free modules, each its own repository and marketplace plugin: Admin IP Access, IP Blocker, Smart IP Blocker, CSP, Input Sanitizer.

This is a **security product installed on production sites**. A bug here locks administrators out, leaks data or silently leaves a site unprotected. Security and safe upgrades come before everything else.

## The self-check gate (run before every commit)

Everything runs in Docker; the host needs no PHP.

```bash
make install        # composer install inside the php container
make code.fix       # composer normalize, rector, php-cs-fixer
make code.check     # validate, normalize --dry-run, audit, php -l, yaml-lint, cs, rector, PHPStan max
make test           # Pest
make test.coverage  # Pest with pcov, fails below 90 %
make ready          # all of the above: fix, check, coverage
```

`make ready` must pass. PHPStan runs at `level: max` with strict rules and **no baseline**: fix the type, never add an ignore. Security advisories reported by `composer audit` are fixed by updating the package, never ignored.

## How the code is laid out

| Directory | Holds |
|-----------|-------|
| `Plugin.php` | Wiring only: console commands, settings page, report widget, event subscribers, the config override. |
| `models/Fortify.php` | The single `SettingModel` record every module shares. Each module keeps its values under its own key: `config`, `tests` (core), `ip_firewall`, `csp`, `input_sanitizer` (modules). |
| `transformers/` | Turn raw settings into typed DTOs. Every value from the settings is untrusted input: validate and normalize it here. |
| `dto/` | `final readonly` value objects. |
| `cache/` | `Cache::remember` wrappers keyed by `BasicCache::cacheKey()`; cleared on the settings `model.afterSave`. |
| `instances/` | Per-request memo (`Singleton`) over the caches. |
| `services/` | The behaviour. Services never read the request: the widget or the middleware passes what they need. |
| `client/` | The only code that opens network connections (HTTP, TCP, TLS). |
| `enums/` | Every shared code: events, permissions, views, actions, modules. Reuse them instead of string literals. |
| `listeners/` | Event subscribers. |
| `widgets/` | The dashboard widget and its partials. |
| `updates/` | `version.yaml` and the update scripts. |

### The contract with the modules (do not break it)

The modules are separate plugins that users update independently, so a site may run a new core with old modules or the other way round. The following are **public API**; renaming or changing their shape breaks installed sites:

- `Wobqqq\Fortify\Models\Fortify` and its keys, `Fortify::get()`/`set()`;
- `Wobqqq\Fortify\Enums\FortifyEvent` names and the arguments they pass by reference;
- `Wobqqq\Fortify\Enums\View`, `WidgetItemColor`;
- `Wobqqq\Fortify\Dto\WidgetGroupItemDto`, `WidgetItemLinkDto`, `WidgetItemButtonDto`;
- `Wobqqq\Fortify\Transformers\FortifyTransformer::widgetGroupItemDto()` / `widgetItemLinkDto()`;
- `Wobqqq\Fortify\Cache\BasicCache` (`TTL`, `cacheKey()`);
- the view names `wobqqq.fortify::denied`, `wobqqq.fortify::bad-request` and the language keys the modules read.

Add to these; do not rename or remove. A module must keep working with every released core version.

## Upgrading installed sites safely

Read the `plugin-upgrades` skill before changing anything that reaches a site that already runs the plugin. In short:

- Every change that ships adds a version to `updates/version.yaml` (the marketplace reads it from `main`; the tag must match).
- A change to what is **stored** (a setting's type or key) comes with an update script in `updates/` that converts the existing records, with a working `down()`, and keeps the site's behaviour unchanged unless the change is the fix.
- A change to what is **cached** (a DTO's shape) bumps `BasicCache::VERSION`, so a new version never unserializes an object the previous version wrote.
- Defaults stay safe: a new protection ships disabled or with a value that cannot lock an administrator out.

## Security rules (always)

Read the `fortify-security` skill for the full checklist. The non-negotiables:

- **Escape every output.** Partials use `e()` for every value; JSON for `data-request-data`; `rel="noopener noreferrer"` on `target="_blank"`. Language strings may contain markup, their replacements may not: escape the replacement.
- **Authorize every handler.** AJAX handlers check `BackendAuth::userHasAccess(Permission::FORTIFY->value)` and dispatch only the `ButtonAction` cases, never a method name taken from the request.
- **Validate every setting** in `Fortify::$rules` (types, lengths, `ip`, `url:http,https`) and again where it is used.
- **The scanners only reach what the administrator listed**, with timeouts, without following redirects, and never with credentials.
- Never log or print secrets, `.env`, `auth.json` or a request's cookies.

## Tests

Pest 4 on Orchestra Testbench (Laravel 12) with the real `october/rain`. The licensed October modules are not installable in CI, so `tests/Stubs/October.php` reproduces the few classes the plugin touches with October 4.4's behaviour (`SettingModel`, `PluginBase`, `ReportWidgetBase`, `PluginManager`, `BackendAuth`, ...). When the plugin starts using another October class or behaviour, add it there, matching October's real signature. Read the `plugin-testing` skill.

## Conventions

- `declare(strict_types=1);` in every PHP file; PSR-12 via php-cs-fixer (`(int)$x` without a space, imported classes).
- Code documents itself: names over comments. A comment explains a non-obvious *why*, in one sentence.
- DTOs are `final readonly`; services and transformers are `final`.
- October patterns over Laravel ones: model validation, YAML forms, `Plugin.php` registration, `lang/en/lang.php` keys (read the `octobercms-*` skills).
- Commits: imperative subject saying what the change does for the site ("Apply the non-alphanumeric password rule"), a body with the why.
