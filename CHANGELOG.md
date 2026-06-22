# Changelog

All notable changes to `laravel-trash-cleaner` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## 1.0.4 - 2025-06-30

### Fixed
- Force-delete the previously published config file before re-publishing, fixing a stale/renamed config file (`config/laravel-trash-cleaner.php`) left behind by older versions.

### Changed
- Documentation updates.

## 1.0.3 - 2025-06-28

### Added
- Broadened PHP support to `^7.4 | ^8.1 | ^8.2 | ^8.3 | ^8.4`.

### Changed
- General package enhancements.

## 1.0.2 - 2025-06-28

### Added
- New `trash:clean-assets` command to remove compiled views, the Vite cache and `public/build`, with an optional `--build` flag to rebuild frontend assets.
- Config-driven `cleanup_paths`, `package_manager` and `build_commands` options.

### Changed
- Service provider updates and general clean up.

## 1.0.1 - 2024-12-17

### Fixed
- Service provider registration of the cleanup service.

### Changed
- README and screenshot updates.

## 1.0.0 - 2024-06-08

### Added
- Initial release with the `trash:clean` command to delete Debugbar and Clockwork debug files from `storage/`, with a progress bar and freed-space report.
- Optional scheduled cleanup via the `schedule` and `frequency` config options.
