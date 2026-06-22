# Laravel Trash Cleaner

<p align="center">
  <a href="https://github.com/omaralalwi/laravel-trash-cleaner" target="_blank">
    <img src="https://raw.githubusercontent.com/omaralalwi/laravel-trash-cleaner/master/public/images/laravel-trash-cleaner.jpg" alt="Laravel Trash Cleaner">
  </a>
</p>

<p align="center">
  <a href="https://packagist.org/packages/omaralalwi/laravel-trash-cleaner"><img src="https://img.shields.io/packagist/v/omaralalwi/laravel-trash-cleaner.svg?style=flat-square" alt="Latest Version on Packagist"></a>
  <a href="https://packagist.org/packages/omaralalwi/laravel-trash-cleaner"><img src="https://img.shields.io/packagist/dt/omaralalwi/laravel-trash-cleaner.svg?style=flat-square" alt="Total Downloads"></a>
  <a href="https://packagist.org/packages/omaralalwi/laravel-trash-cleaner"><img src="https://img.shields.io/packagist/php-v/omaralalwi/laravel-trash-cleaner.svg?style=flat-square" alt="PHP Version"></a>
  <a href="LICENSE.md"><img src="https://img.shields.io/packagist/l/omaralalwi/laravel-trash-cleaner.svg?style=flat-square" alt="License"></a>
</p>

**Laravel Trash Cleaner** is a lightweight and powerful utility package that helps you keep your Laravel application clean and performant by:

* 🧹 Deleting debug files (Debugbar, Clockwork) from `storage/`.
* ⚡ Clearing compiled view caches and frontend build directories.
* 🛠️ Optionally rebuilding frontend assets using tools like `npm`, `yarn`, or `pnpm`.
* ⏰ Optionally scheduling automatic cleanup via Laravel's scheduler.

![Trash Cleaner Screenshot](https://raw.githubusercontent.com/omaralalwi/laravel-trash-cleaner/master/public/images/trash-screen-shot.png)


![Trash Assets Cleaner Screenshot](https://raw.githubusercontent.com/omaralalwi/laravel-trash-cleaner/master/public/images/trash-assets-screen-shot.png)

---

## 📋 Table of Contents

- [Requirements](#-requirements)
- [Installation](#-installation)
- [Usage](#-usage)
- [Configuration](#-configuration)
- [Scheduling Automatic Cleanup](#-scheduling-automatic-cleanup)
- [Contributing](#-contributing)
- [Security](#️-security)
- [License](#-license)
- [Helpful Open Source Packages](#-helpful-open-source-packages)

---

## ✅ Requirements

- **PHP** `7.4` or `8.1+` (`^7.4 | ^8.1 | ^8.2 | ^8.3 | ^8.4`)
- **Laravel** `8.x` or higher
- A **Unix-like environment** (Linux/macOS) for `trash:clean-assets`, which uses `rm` and your Node package manager. See the [note below](#-clean-asset-folders).

---

## 🚀 Installation

Install the package via Composer:

```bash
composer require omaralalwi/laravel-trash-cleaner
```

### 🔧 Publish Configuration

Optionally, publish the configuration file to customize paths and build settings (see [Configuration](#-configuration)):

```bash
php artisan vendor:publish --tag=laravel-trash-cleaner
```

---

## 🧹 Usage

### 🔸 Clean Debug Files

Deletes the `.json` debug files from the `storage/debugbar` and `storage/clockwork` folders with a progress bar, and reports how much disk space was freed:

```bash
php artisan trash:clean
```

### 🔸 Clean Asset Folders

Removes frontend-related build caches and compiled view files based on your config (see [`cleanup_paths`](#-configuration)):

```bash
php artisan trash:clean-assets
```

> **Note:** `trash:clean-assets` runs `rm -rf` and your Node package manager under the hood, so it is intended for Unix-like environments (Linux/macOS). It is not supported on native Windows shells.

### 🔸 Clean + Rebuild Frontend (Optional)

Use the `--build` flag to also run your frontend build steps (`npm install && npm run build` or equivalent):

```bash
php artisan trash:clean-assets --build
```

This is ideal for resetting the build process after switching branches, clearing corrupted caches, or deploying updates.

---

---

## ⚙️ Configuration

Publish the config file (if you haven't already):

```bash
php artisan vendor:publish --tag=laravel-trash-cleaner
```

This creates `config/laravel-trash-cleaner.php`:

```php
return [

    // Enable Laravel scheduler integration for `trash:clean` (see Scheduling below).
    'schedule' => false,

    // How often the scheduled cleanup runs. Must be a valid Laravel Schedule
    // frequency method, e.g. 'daily', 'hourly', 'everyFifteenMinutes', 'weekly'.
    'frequency' => 'daily',

    // Paths removed by `trash:clean-assets` (relative to the project root, glob supported).
    'cleanup_paths' => [
        'storage/framework/views/*',
        'public/build',
        'node_modules/.vite',
    ],

    // Node package manager used by the `--build` flag: "npm", "pnpm", or "yarn".
    'package_manager' => 'npm',

    // Commands run (per entry) when `--build` is passed, prefixed by the package manager above.
    // e.g. with npm: `npm install` then `npm run build`.
    'build_commands' => [
        'install',
        'run build',
    ],
];
```

| Key | Type | Default | Description |
| --- | --- | --- | --- |
| `schedule` | `bool` | `false` | Auto-schedule `trash:clean` via Laravel's scheduler. |
| `frequency` | `string` | `'daily'` | Any Laravel `Schedule` frequency method name. |
| `cleanup_paths` | `array` | see above | Paths deleted by `trash:clean-assets`. |
| `package_manager` | `string` | `'npm'` | Node package manager for the `--build` step. |
| `build_commands` | `array` | `['install', 'run build']` | Commands appended to the package manager when building. |

> **Tip:** If you use **`pnpm`** instead of the default **`npm`**, set `'package_manager' => 'pnpm'`.

---

## ⏰ Scheduling Automatic Cleanup

To automate cleanup using Laravel's scheduler:

1. Enable it in your config:

```php
'schedule' => true,
'frequency' => 'daily', // any Laravel Schedule frequency method, e.g. 'hourly', 'weekly'
```

2. Ensure Laravel's scheduler is running via cron:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

> **Note:** Only `trash:clean` (debug files) is auto-scheduled. To schedule `trash:clean-assets` as well, register it manually in your application's `routes/console.php` (or `app/Console/Kernel.php`):
>
> ```php
> Schedule::command('trash:clean-assets')->weekly();
> ```

---

## 🤝 Contributing

Contributions are welcome! Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

---

## 🛡️ Security

If you discover any security vulnerabilities, please contact: [omaralwi2010@gmail.com](mailto:omaralwi2010@gmail.com)

---

## 📄 License

Licensed under the [MIT License](LICENSE.md).

---


## 📚 Helpful Open Source Packages

- <a href="https://github.com/omaralalwi/lexi-translate"><img src="https://raw.githubusercontent.com/omaralalwi/lexi-translate/master/public/images/lexi-translate-banner.jpg" width="26" height="26" style="border-radius:13px;" alt="lexi translate" /> Lexi Translate </a> simplify managing translations for multilingual Eloquent models with power of morph relationships and caching .

- <a href="https://github.com/omaralalwi/Gpdf"><img src="https://raw.githubusercontent.com/omaralalwi/Gpdf/master/public/images/gpdf-banner-bg.jpg" width="26" height="26" style="border-radius:13px;" alt="laravel Taxify" /> Gpdf </a> Open Source HTML to PDF converter for PHP & Laravel Applications, supports Arabic content out-of-the-box and other languages..

- <a href="https://github.com/omaralalwi/laravel-taxify"><img src="https://raw.githubusercontent.com/omaralalwi/laravel-taxify/master/public/images/taxify.jpg" width="26" height="26" style="border-radius:13px;" alt="laravel Taxify" /> **laravel Taxify** </a> Laravel Taxify provides a set of helper functions and classes to simplify tax (VAT) calculations within Laravel applications.

- <a href="https://github.com/omaralalwi/laravel-deployer"><img src="https://raw.githubusercontent.com/omaralalwi/laravel-deployer/master/public/images/deployer.jpg" width="26" height="26" style="border-radius:13px;" alt="laravel Deployer" /> **laravel Deployer** </a> Streamlined Deployment for Laravel and Node.js apps, with Zero-Downtime and various environments and branches.

- <a href="https://github.com/omaralalwi/laravel-time-craft"><img src="https://raw.githubusercontent.com/omaralalwi/laravel-time-craft/master/public/images/laravel-time-craft.jpg" width="26" height="26" style="border-radius:13px;" alt="laravel Trash Cleaner" /> **laravel Time Craft** </a>simple trait and helper functions that allow you, Effortlessly manage date and time queries in Laravel apps.

- <a href="https://github.com/omaralalwi/laravel-startkit"><img src="https://raw.githubusercontent.com/omaralalwi/laravel-startkit/master/public/screenshots/backend-rtl.png" width="26" height="26" style="border-radius:13px;" alt="Laravel Startkit" /> **Laravel Startkit** </a>  Laravel Admin Dashboard, Admin Template with Frontend Template, for scalable Laravel projects.

