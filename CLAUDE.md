# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Overview

`omaralalwi/laravel-trash-cleaner` is a Laravel package (auto-discovered) that ships two Artisan commands for cleaning up debug/log files and frontend build artifacts. It is a library — there is no host application here. PHP support spans `^7.4|^8.1|^8.2|^8.3|^8.4`, so avoid syntax/features unavailable on PHP 7.4.

## Commands

```bash
composer test                              # run PHPUnit (vendor/bin/phpunit)
composer test-coverage                     # run with HTML coverage into coverage/
vendor/bin/phpunit --filter SomeTestName   # run a single test
```

Note: the `composer test` scripts and CI reference a `tests/` directory and PSR-4 `Tests\` namespace, but no tests currently exist in the repo. Add them under `tests/` if writing coverage.

The two Artisan commands the package provides (exercised inside a host Laravel app):

```bash
php artisan trash:clean                  # CleanUpDebugTrash — deletes *.json in storage/debugbar & storage/clockwork
php artisan trash:clean-assets           # CleanUpAssets — rm -rf each config cleanup_path
php artisan trash:clean-assets --build   # also runs the configured package manager build commands
php artisan vendor:publish --tag=laravel-trash-cleaner   # publish config to config/laravel-trash-cleaner.php
```

## Architecture

- **`LaravelTrashCleanerServiceProvider`** — the single wiring point. `register()` merges `config/config.php` under the config key `laravel-trash-cleaner`. `boot()` (console-only) registers both commands, publishes config, and registers the scheduled task. The scheduler hook reads `config('laravel-trash-cleaner')` and, when `schedule` is `true`, calls `$schedule->command('trash:clean')->{$config['frequency']}()` — so `frequency` must be a valid `Schedule` method name (`daily`, `hourly`, etc.).

- **Config key vs. published filename mismatch (important):** the config is registered under the key `laravel-trash-cleaner` but the source file is `config/config.php`, and it publishes to `config/laravel-trash-cleaner.php`. `boot()` deliberately `unlink()`s any pre-existing `config/laravel-trash-cleaner.php` before publishing — this is intentional legacy-config cleanup (a previous release used a different filename). Be careful editing this block; it deletes a file in the host app's config dir on every console boot when the file exists.

- **`CleanUpDebugTrash`** (`trash:clean`) — deletes only `.json` files (skips `.gitignore`) in `storage/debugbar` and `storage/clockwork`, drives a progress bar, and reports freed bytes via `formatBytes()`.

- **`CleanUpAssets`** (`trash:clean-assets`) — shells out via Symfony `Process` (300s timeout, cwd = `base_path()`). It `rm -rf`s each entry in `cleanup_paths` (resolved against `base_path()`), then with `--build` runs `{package_manager} {command}` for each entry in `build_commands`. Paths and commands are fully config-driven; `rm`/the package manager must exist on the host (not portable to Windows shells despite CI running on windows-latest).

## Conventions

- Config defaults live in `config/config.php`; all command behavior (paths, package manager, build steps, scheduling) is driven from there — prefer adding options to config over hardcoding in commands.
- Both commands are console-only and registered explicitly in the provider; new commands must be added to the `$this->commands([...])` array.
- Code style is enforced by StyleCI (`.styleci.yml`).
