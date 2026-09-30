---
name: plugin-testing
description: "How this plugin is tested without a licensed October CMS install. Use when writing or changing a test, adding a class October provides to tests/Stubs/October.php, or when a test fails only in the suite (leaking statics between tests)."
license: MIT
---

# Testing the plugin

## The harness

- Pest 4 on Orchestra Testbench 10 (Laravel 12) and the real `october/rain` from Packagist.
- October's modules (`system`, `backend`, `dashboard`) are licensed and not installable in CI. `tests/Stubs/October.php` stands in for the classes the plugin touches, **with October 4.4's signatures and behaviour**: `PluginBase`, `PluginManager`, `UpdateManager`, `SettingsManager`, `SettingModel` (in-memory record, real model events), `Backend\Models\User` (real table in SQLite), `Backend` and `BackendAuth` facades, `ReportWidgetBase` (renders the real partials), the `Form` widget.
- `tests/TestCase.php` boots the plugin (`register()` + `boot()`) like October does, registers the language namespace and resets October's statics after each test (`ExtensionContainer::clearExtensions()`, `flushEventListeners()`), so every test starts from a fresh plugin.

## Rules

- Test behaviour an administrator or visitor sees: the config October ends up with, the HTML the widget renders, what a check reports. Not private methods.
- A security rule is a test: an unescaped value, a missing permission, an unlisted scan target, an invalid setting.
- No test reaches the network: HTTP goes through a Guzzle handler (`fakeSensitiveFileResponses()`), TCP through local sockets (`tests/Support/Sockets.php`), TLS through a replaced client bound in the container.
- An update script is tested against a `system_settings` table: old shape in, new shape out, and `down()`.
- When the plugin starts using another October class or method, add it to the stub **with the real signature** (read it in an October 4.4 install's `modules/`) and only the behaviour the plugin relies on.
- Coverage stays at 90 % or more (`make test.coverage`).
