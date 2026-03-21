<br>
<a href="https://github.com/KsSadi/Laravel-Log-Tracker">
<img style="width: 100%; max-width: 100%;" alt="Log Tracker Laravel Package" src="/image/log_tracker_banner.png">
</a>

# 📜 Laravel Log Tracker

Laravel Log Tracker is a powerful, zero-dependency package for viewing, analyzing, searching, comparing, and exporting your Laravel application logs — with a beautiful dual-theme UI, real-time dashboard, alert notifications, and full PHPUnit test coverage.

![GitHub Repo stars](https://img.shields.io/github/stars/KsSadi/Laravel-Log-Tracker.svg)
[![Downloads](https://img.shields.io/packagist/dt/kssadi/log-tracker)](https://packagist.org/packages/kssadi/log-tracker)
![GitHub license](https://img.shields.io/github/license/KsSadi/Laravel-Log-Tracker.svg)
![Packagist Version](https://img.shields.io/packagist/v/kssadi/log-tracker.svg)
![GitHub top language](https://img.shields.io/github/languages/top/KsSadi/Laravel-Log-Tracker.svg)
[![Codecov](https://codecov.io/gh/KsSadi/Laravel-Log-Tracker/branch/main/graph/badge.svg)](https://codecov.io/gh/KsSadi/Laravel-Log-Tracker)

<div align="center">

### 🌐 [Official Website & Documentation](https://kssadi.github.io/Laravel-Log-Tracker/)

</div>

---

## Table of Contents

- [Requirements](#️-requirements)
- [Installation](#-installation)
- [Configuration](#️-configuration)
- [Features](#-features)
  - [Dashboard](#-dashboard)
  - [Log File Management](#-log-file-management)
  - [Log Viewer](#-log-viewer)
  - [Global Search](#-global-search)
  - [Log Comparison](#️-log-comparison)
  - [Export](#-export)
  - [Alerts & Notifications](#-alerts--notifications)
  - [Themes](#-themes)
  - [Artisan Commands](#️-artisan-commands)
- [Routes Reference](#️-routes-reference)
- [Testing](#-testing)
- [Screenshots](#-screenshots)
- [Changelog](#-changelog)
- [Support](#-support)
- [Author](#-author)
- [License](#-license)

---

## 🖥️ Requirements

| Laravel | PHP |
|---------|-----|
| 13.x | 8.3 – 8.5 |
| 12.x | 8.1 – 8.4 |
| 11.x | 8.1 – 8.3 |
| 10.x | 8.1 – 8.2 |

---

## 📦 Installation

**1. Install via Composer:**

```bash
composer require kssadi/log-tracker
```

**2. Publish the configuration file:**

```bash
php artisan vendor:publish --provider="Kssadi\LogTracker\LogTrackerServiceProvider" --tag="config"
```

**3. (Optional) Publish views to customise them:**

```bash
php artisan vendor:publish --provider="Kssadi\LogTracker\LogTrackerServiceProvider" --tag="views"
```

---

## ⚙️ Configuration

After publishing, edit `config/log-tracker.php`:

```php
return [
    // Base URL: https://your-domain.com/log-tracker
    'route_prefix' => 'log-tracker',

    // Middleware applied to all routes
    'middleware' => ['web', 'auth'],

    // UI theme: 'GlowStack' (dark, modern) | 'LiteFlow' (light, minimal)
    'theme' => 'GlowStack',

    // Entries shown per page in the log viewer
    'log_per_page' => 50,

    // Log files shown per page on the file listing
    'log_files_per_page' => 10,

    // Dashboard stats cache TTL in seconds (0 = disabled)
    'dashboard_cache_ttl' => 60,

    // Maximum log file size (MB) that can be processed
    'max_file_size' => 50,

    // Allow log file deletion via the UI
    'allow_delete' => false,

    // Allow log file download
    'allow_download' => true,

    // Log level display configuration (color + icon)
    'log_levels' => [
        'emergency' => ['color' => '#b91c1c', 'icon' => 'fas fa-exclamation-circle'],
        'critical'  => ['color' => '#b91c1c', 'icon' => 'fas fa-exclamation-triangle'],
        'alert'     => ['color' => '#b91c1c', 'icon' => 'fas fa-exclamation-circle'],
        'error'     => ['color' => '#dc2626', 'icon' => 'fas fa-times-circle'],
        'warning'   => ['color' => '#d97706', 'icon' => 'fas fa-exclamation-triangle'],
        'notice'    => ['color' => '#b59c1c', 'icon' => 'fas fa-info-circle'],
        'info'      => ['color' => '#0284c7', 'icon' => 'fas fa-info-circle'],
        'debug'     => ['color' => '#059669', 'icon' => 'fas fa-bug'],
    ],

    // Export settings (no external dependencies required)
    'export' => [
        'enabled' => true,
        'formats' => [
            'csv'   => ['enabled' => true],
            'json'  => ['enabled' => true],
            'excel' => ['enabled' => true],
            'pdf'   => ['enabled' => true],
        ],
        'limits' => [
            'max_entries'      => 50000,
            'max_file_size_mb' => 50,
            'timeout_seconds'  => 300,
        ],
        'storage' => [
            'cleanup_after_days' => 7,
        ],
    ],

    // Alert / notification settings
    'alerts' => [
        'enabled'          => false,
        'window_minutes'   => 60,
        'cooldown_minutes' => 15,

        'thresholds' => [
            'emergency' => 1,
            'critical'  => 1,
            'alert'     => 1,
            'error'     => 50,
            'warning'   => 100,
        ],

        'channels' => [
            'mail'    => ['enabled' => false, 'to' => env('LOG_TRACKER_ALERT_MAIL', 'admin@example.com')],
            'slack'   => ['enabled' => false, 'webhook_url' => env('LOG_TRACKER_SLACK_WEBHOOK', '')],
            'discord' => ['enabled' => false, 'webhook_url' => env('LOG_TRACKER_DISCORD_WEBHOOK', '')],
            'webhook' => [
                'enabled' => false,
                'url'     => env('LOG_TRACKER_WEBHOOK_URL', ''),
                'method'  => 'POST',
                'headers' => [],
            ],
        ],
    ],
];
```

### Recommended `.env` settings

```env
LOG_CHANNEL=daily
LOG_LEVEL=debug

# Alert channels (when alerts.enabled = true)
LOG_TRACKER_ALERT_MAIL=admin@example.com
LOG_TRACKER_SLACK_WEBHOOK=https://hooks.slack.com/...
LOG_TRACKER_DISCORD_WEBHOOK=https://discord.com/api/webhooks/...
LOG_TRACKER_WEBHOOK_URL=https://your-endpoint.com/hook
```

### Recommended log channel config (`config/logging.php`)

```php
'channels' => [
    'daily' => [
        'driver' => 'daily',
        'path'   => storage_path('logs/laravel.log'),
        'level'  => env('LOG_LEVEL', 'debug'),
        'days'   => 14,
    ],
],
```

---

## 🚀 Features

### 📊 Dashboard

**URL:** `/log-tracker`

The main dashboard aggregates data from all log files into a single at-a-glance view:

- **Summary counters** — total entries and per-level breakdown
- **Today's activity** — entries logged today, broken down by level
- **7-day trend** — date-keyed table of the past week's activity
- **Last 5 recent logs** — most recent log entries across all files
- **Top error types** — most frequent error message prefixes
- **Peak hours** — hour-of-day histogram showing when errors peak
- **Live auto-refresh** — JSON endpoint (`/api/dashboard-refresh`) polled by the UI without a full page reload

---

### 📁 Log File Management

**URL:** `/log-tracker/log-file`

- Paginated file listing with human-readable names (`laravel-2025-06-01.log` → `01 June 2025`)
- File size displayed beside each entry
- Per-file level breakdown counts (how many errors, warnings, etc.)
- Actions per file: **View**, **Download**, **Delete** (if `allow_delete = true`)

---

### 📋 Log Viewer

**URL:** `/log-tracker/{filename}`

A full-featured viewer for a single log file:

| Feature | Detail |
|---------|--------|
| **Level filter** | Toggle individual log levels on/off |
| **Real-time search** | Instant keyword filter across message and timestamp |
| **Pagination** | Configurable entries per page |
| **Stack trace viewer** | Expand/collapse per entry with syntax highlighting — frame numbers gold, file paths teal, line numbers red |
| **Copy to Clipboard** | One-click copy for any log message and for full stack traces; icon changes to ✓ for 2 s visual feedback |
| **Row Bookmark** | Bookmark any row with 🔖; row highlighted in gold, bookmarks stored in `localStorage` and survive page reloads |
| **Show Marked Only** | Toggle button in the header (shows count) to display only bookmarked rows |
| **Frequent Entries panel** | Collapsible table of the top 15 repeated messages with occurrence count, first seen, and last seen timestamps |
| **Clear log** | Wipe file contents without deleting the file (requires `allow_delete = true`) |
| **Quick export** | Export current file in any supported format directly from the viewer |

---

### 🔎 Global Search

**URL:** `/log-tracker/search`

Search across **all** log files simultaneously without opening each one:

- **Keyword search** — case-insensitive, matches message body and stack trace content
- **Level filter** — narrow results to a specific log level
- **Date range** — `date_from` and `date_to` filters
- **File filter** — restrict the search to a specific log file
- **Paginated results** — newest-first ordering, source filename shown per result
- Results decorated with per-level colour coding and icons

---

### ⚖️ Log Comparison

**URL:** `/log-tracker/compare`

Select any two log files for a full side-by-side content diff:

| Tab | Description |
|-----|-------------|
| **Only in File A** | Entries that exist only in the first file |
| **Only in File B** | Entries that exist only in the second file |
| **Shared** | Entries present in both files (matched by level + message fingerprint) |

Additional information displayed:

- Per-level count breakdown table for each file
- Total entry counts for both files
- Guard against selecting the same file twice (null comparison shown)

---

### 📤 Export

**URL:** `/log-tracker/export`  
**Quick export:** `/log-tracker/export/{filename}/{format}`

Export filtered log data — **no external package dependencies required**:

| Format | Extension | Notes |
|--------|-----------|-------|
| CSV | `.csv` | UTF-8 BOM — opens correctly in Excel |
| JSON | `.json` | Includes `export_info` metadata (generated_at, filters, exported_by) |
| Excel | `.xls` | Native Office Open XML format — no PHPSpreadsheet needed |
| PDF | `.pdf` | Print-ready HTML report (capped at first 1 000 entries) |

**Available filters:** specific files, log levels, date range, keyword.

Export files are stored temporarily and automatically deleted after `cleanup_after_days` days.

---

### 🔔 Alerts & Notifications

The alert system monitors log files on a rolling time window and triggers notifications when entry counts for any level exceed configured thresholds.

```php
'alerts' => [
    'enabled'          => true,
    'window_minutes'   => 60,     // rolling look-back window
    'cooldown_minutes' => 15,     // minimum gap before re-alerting for same file + level

    'thresholds' => [
        'emergency' => 1,
        'critical'  => 1,
        'error'     => 50,
        'warning'   => 100,
    ],

    'channels' => [
        'mail'    => ['enabled' => true,  'to' => 'admin@example.com'],
        'slack'   => ['enabled' => true,  'webhook_url' => env('LOG_TRACKER_SLACK_WEBHOOK')],
        'discord' => ['enabled' => false, 'webhook_url' => env('LOG_TRACKER_DISCORD_WEBHOOK')],
        'webhook' => [
            'enabled' => false,
            'url'     => env('LOG_TRACKER_WEBHOOK_URL'),
            'method'  => 'POST',
            'headers' => ['Authorization' => 'Bearer token'],
        ],
    ],
],
```

**Run manually:**

```bash
php artisan log-tracker:check-alerts
```

**Auto-schedule** — when `alerts.enabled = true` the package registers a once-per-minute scheduler entry automatically. Ensure your scheduler cron is active:

```bash
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

#### Notification Channels

| Channel | How to enable |
|---------|--------------|
| **Mail** | Set `enabled = true`, set `to` to recipient email; uses the application's default mailer |
| **Slack** | Create an [Incoming Webhook](https://api.slack.com/apps) in your Slack app and paste the URL |
| **Discord** | Server Settings → Integrations → Webhooks → copy URL |
| **Webhook** | Any HTTP endpoint — POST or GET, custom headers supported |

---

### 🎨 Themes

Two production-ready themes are bundled. Switching theme requires no cache clear.

| Theme | Style | Best for |
|-------|-------|----------|
| **GlowStack** | Dark, colorful, gradient-based | Modern dashboards, low-light environments |
| **LiteFlow** | Light, minimal, clean | High-contrast readability, print-friendly |

![Log Tracker Theme](/image/log_tracker_log_theme.png)

**Set theme in config:**

```php
'theme' => 'GlowStack', // or 'LiteFlow'
```

**Custom themes** — add a new folder under `resources/views/vendor/log-tracker/theme/YourTheme/` containing `dashboard.blade.php`, `logs.blade.php`, `log-details.blade.php`, `search.blade.php`, `compare.blade.php`, and `export.blade.php`. The package discovers it automatically.

---

### 🛠️ Artisan Commands

| Command | Description |
|---------|-------------|
| `log-tracker:check-alerts` | Check all log files against thresholds and send configured notifications |
| `log-tracker:cleanup [--days=7]` | Delete temporary export files older than N days |
| `log-tracker:theme list` | List all available themes |
| `log-tracker:theme current` | Show the currently active theme |
| `log-tracker:theme set {name}` | Switch to the specified theme |

---

## 🗺️ Routes Reference

All routes are grouped under the configured `route_prefix` (default: `log-tracker`) with the configured middleware.

| Method | URI | Route name | Description |
|--------|-----|------------|-------------|
| GET | `/log-tracker` | `log-tracker.dashboard` | Main dashboard |
| GET | `/log-tracker/api/dashboard-refresh` | `log-tracker.api.dashboard.refresh` | Live dashboard JSON refresh |
| GET | `/log-tracker/log-file` | `log-tracker.index` | Log file listing |
| GET | `/log-tracker/search` | `log-tracker.search` | Global multi-file search |
| GET | `/log-tracker/compare` | `log-tracker.compare` | Side-by-side file comparison |
| GET | `/log-tracker/export` | `log-tracker.export.form` | Export form |
| POST | `/log-tracker/export` | `log-tracker.export` | Trigger export download |
| GET | `/log-tracker/export/{logName}/{format}` | `log-tracker.export.quick` | Quick export from viewer |
| GET | `/log-tracker/download/{logName}` | `log-tracker.download` | Download raw log file |
| POST | `/log-tracker/delete/{logName}` | `log-tracker.delete` | Delete log file |
| POST | `/log-tracker/clear/{logName}` | `log-tracker.clear` | Clear log file contents |
| GET | `/log-tracker/{logName}` | `log-tracker.show` | View log entries |

---

## 🧪 Testing

The package ships with a comprehensive PHPUnit test suite covering controllers, services, and Artisan commands. Tests run standalone via [`orchestra/testbench`](https://github.com/orchestral/testbench) — no parent Laravel project required.

**Clone the repository and install dev dependencies:**

```bash
git clone https://github.com/KsSadi/Laravel-Log-Tracker.git
cd Laravel-Log-Tracker
composer install
```

**Run all tests:**

```bash
./vendor/bin/phpunit
```

**Run by suite:**

```bash
./vendor/bin/phpunit --testsuite Feature
./vendor/bin/phpunit --testsuite Unit
```

**Run a single test file:**

```bash
./vendor/bin/phpunit tests/Feature/CompareControllerTest.php
```

### Coverage Summary

| Suite | Test File | What it covers |
|-------|-----------|----------------|
| Feature | `LogFileControllerTest` | Index, show, download, delete, clear, pagination, path traversal guard |
| Feature | `DashboardControllerTest` | Stats structure, 7-day dates, live refresh JSON endpoint, cache TTL behaviour |
| Feature | `SearchControllerTest` | Keyword, level, date range, cross-file search, pagination |
| Feature | `CompareControllerTest` | Shared/unique detection, level counts, same-file guard |
| Feature | `ExportControllerTest` | CSV, JSON, Excel, PDF output format + validation |
| Feature | `LogFrequencyTest` | Duplicate message detection, count sorting, first/last seen |
| Feature | `CheckLogAlertsCommandTest` | Threshold breach detection, cooldown, notification dispatch |
| Feature | `CleanupCommandTest` | Old export file removal by age |
| Feature | `ThemeCommandTest` | List, current, set theme commands |
| Feature | `ServiceProviderTest` | Container binding, facade, config publishing |
| Unit | `LogParserServiceTest` | Log parsing, pagination, stack trace line detection, correct stack trace entry assignment |
| Unit | `LogExportServiceTest` | CSV/JSON/Excel/PDF generation with filter combinations |
| Unit | `ThemeManagerTest` | Theme discovery, fallback behaviour, view path resolution |

**Current baseline: 177 tests, 461 assertions — all passing.**

---

## 📸 Screenshots

![Dashboard](/image/log_tracker_dashboard.png)

![Log File List](/image/log_tracker_log_file.png)

![Log Viewer](/image/log_tracker_log_file_view.png)

![Theme Comparison](/image/log_tracker_log_theme.png)

---

## 📝 Changelog

See [CHANGELOG.md](CHANGELOG.md) for a full release history and upgrade notes.

---

## 🆘 Support

| Channel | Link |
|---------|------|
| 🐛 Bug reports | [GitHub Issues](https://github.com/KsSadi/Laravel-Log-Tracker/issues) |
| 💬 Feature requests | [GitHub Discussions](https://github.com/KsSadi/Laravel-Log-Tracker/discussions) |
| 📖 Documentation | [Official Site](https://kssadi.github.io/Laravel-Log-Tracker/) |
| 💌 Email | [mdsadi4@gmail.com](mailto:mdsadi4@gmail.com) |
| ☕ Buy me a coffee | [![Buy Me a Coffee](https://img.shields.io/badge/-Buy%20Me%20a%20Coffee-orange?style=for-the-badge&logo=buy-me-a-coffee&logoColor=white)](https://www.buymeacoffee.com/kssadi) |

---

## 👤 Author

<div align="center">

**Khaled Saifullah Sadi**  
*Full Stack Developer · Laravel Enthusiast*

[![Email](https://img.shields.io/badge/Email-mdsadi4%40gmail.com-red?style=for-the-badge&logo=gmail&logoColor=white)](mailto:mdsadi4@gmail.com)
[![LinkedIn](https://img.shields.io/badge/LinkedIn-kssadi-blue?style=for-the-badge&logo=linkedin&logoColor=white)](https://www.linkedin.com/in/kssadi/)
[![GitHub](https://img.shields.io/badge/GitHub-KsSadi-black?style=for-the-badge&logo=github&logoColor=white)](https://github.com/KsSadi)

</div>

---

## ❤️ Supported by Business Automation Ltd

<div align="center">

Maintained by [Sadi](https://github.com/KsSadi) and supported by  
**[Business Automation Ltd](https://ba-systems.com)** — building enterprise-grade e-governance and automation solutions across Bangladesh.

<a href="https://ba-systems.com">
  <img src="https://ba-systems.com/png/BA.png" alt="Business Automation Ltd" width="160">
</a>

</div>

---

## 📄 License

Released under the [MIT License](https://opensource.org/licenses/MIT).

```
MIT License

Copyright (c) 2025 Khaled Saifullah Sadi

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```

---

<div align="center">

**⭐ If Laravel Log Tracker saves you time, please give it a star on GitHub! ⭐**

*Made with ❤️ for the Laravel Community*

*Copyright © 2025 [Khaled Saifullah Sadi](https://github.com/KsSadi). All rights reserved.*

</div>
