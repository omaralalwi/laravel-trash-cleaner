# Laravel Trash Cleaner

<p align="center">
  <a href="https://github.com/omaralalwi/laravel-trash-cleaner" target="_blank">
    <img src="https://raw.githubusercontent.com/omaralalwi/laravel-trash-cleaner/master/public/images/laravel-trash-cleaner.jpg" alt="Laravel Trash Cleaner">
  </a>
</p>

**Laravel Trash Cleaner** is a lightweight and powerful utility package that helps you keep your Laravel application clean and performant by:

* 🧹 Deleting debug and log files (Clockwork, Debugbar).
* ⚡ Clearing compiled view caches and frontend build directories.
* 🛠️ Optionally rebuilding frontend assets using tools like `npm`, `yarn`, or `pnpm`.

![Trash Cleaner Screenshot](https://raw.githubusercontent.com/omaralalwi/laravel-trash-cleaner/master/public/images/trash-screen-shot.png)

---

## 🚀 Installation

Install the package via Composer:

```bash
composer require omaralalwi/laravel-trash-cleaner
```

### 🔧 Publish Configuration

Optionally, publish the configuration file to customize paths and build settings:

```bash
php artisan vendor:publish --tag=laravel-trash-cleaner
```

---

## 🧹 Usage

### 🔸 Clean Debug Files

Cleans out `storage/debugbar` and `storage/clockwork` folders with a progress bar:

```bash
php artisan trash:clean
```

### 🔸 Clean Asset Folders

Removes frontend-related build caches and compiled view files based on your config:

```bash
php artisan trash:clean-assets
```

### 🔸 Clean + Rebuild Frontend (Optional)

Use the `--build` flag to also run your frontend build steps (`npm install && npm run build` or equivalent):

```bash
php artisan trash:clean-assets --build
```

This is ideal for resetting the build process after switching branches, clearing corrupted caches, or deploying updates.

---

### customize Assets paths &  commnds
> **Note:** You can fully customize the asset cleanup paths and build commands in the configuration file. For example, if you're using **`pnpm`** instead of the default **`npm`**, make sure to update the config key to `'package_manager' => 'pnpm'`.

---

## ⏰ Scheduling Automatic Cleanup

To automate cleanup using Laravel's scheduler:

1. Enable it in your config:

```php
'schedule' => true,
'frequency' => 'daily',
```

2. Ensure Laravel's scheduler is running via cron:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

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

