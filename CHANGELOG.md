# Changelog

All notable changes to this project will be documented in this file.

## [3.0] - 2026-03-21
### � Major Release — Version 3.0

A full architectural overhaul. Version 3.0 introduces five major new features, a complete performance rewrite, production-grade security hardening, and a comprehensive test suite — all while remaining **zero external dependencies**.

---

#### ✨ New Features

**Global Multi-File Search**
- New `SearchController` and `LogSearchService` for searching across all log files simultaneously
- Keyword search (case-insensitive, matches message body and stack trace content)
- Filter by log level, date range (`date_from` / `date_to`), or a specific file
- Paginated results sorted newest-first, with source filename shown per result
- Dedicated `LogSearchRequest` Form Request with full validation
- Search views for both GlowStack and LiteFlow themes

**Log Comparison**
- New `CompareController` for side-by-side diff between any two log files
- Three tabs: Only in File A / Only in File B / Shared (matched by level + message fingerprint)
- Per-level count breakdown table and total entry counts for both files
- Guard against selecting the same file twice (null comparison returned)
- Compare views for both themes

**Advanced Export System** *(zero external dependencies)*
- New `ExportController` and `LogExportService`
- Four formats: CSV (UTF-8 BOM), JSON (with `export_info` metadata), Excel (native Office XML), PDF (print-ready HTML, first 1 000 entries)
- Filters on export: specific files, log levels, date range, keyword
- Quick export directly from the log viewer page
- Export files stored temporarily and auto-cleaned after configurable days
- Export format `enabled` flag enforced — `ExportController` checks `config("log-tracker.export.formats.{$format}.enabled")` in both `export()` and `quickExport()`; disabled formats return `403`

**Alert & Notification System**
- New `LogAlertService` with threshold-based monitoring on a rolling time window
- Anti-spam cooldown per file + level via Laravel Cache
- Four notification channels: **Mail**, **Slack webhook**, **Discord webhook**, **Generic HTTP webhook** (POST/GET + custom headers)
- Per-level threshold configuration: `emergency`, `critical`, `alert`, `error`, `warning`
- Auto-registered once-per-minute scheduler entry when `alerts.enabled = true`
- Professional branded HTML alert email with details table, level badge, monospace message block, and footer

**Log Viewer — Copy to Clipboard**
- One-click copy button on every log message (hover-reveal)
- One-click copy button on every stack trace (top-right of expanded panel)
- Clipboard API with `execCommand` fallback for older browsers
- Icon changes to ✓ with green feedback for 2 s, then resets

**Log Viewer — Row Bookmark / Mark**
- Bookmark icon (🔖) per row in the Actions column
- Marked rows highlighted with a gold left-border and subtle background tint
- Bookmarks persisted in `localStorage` (key: `logtracker_marks_{logName}`) — survive page reloads and tab closes
- "Show Marked Only" toggle button in the content header with live count display
- Both GlowStack and LiteFlow themes supported

**Log Viewer — Frequent Entries Panel**
- Collapsible panel showing top 15 most repeated log messages
- Displays: occurrence count, first seen, last seen timestamps
- Colour and icon decorated per log level

**Dashboard Enhancements**
- Dashboard caching via new `dashboard_cache_ttl` config key (default `60` seconds, `0` = disabled); stats served from `Cache::remember()` with dedicated refresh endpoint
- Last 5 recent logs, top 5 error types (by message prefix), top 5 peak error hours
- Live auto-refresh JSON endpoint (`GET /api/dashboard-refresh`) polled by the UI

**Dynamic Footer Version**
- Footer version now resolved at runtime via `Composer\InstalledVersions::getPrettyVersion()`
- No more hardcoded version string — footer always shows the actually installed release
- Falls back to `dev` in path-repository / development environments

**New Artisan Commands**
- `log-tracker:check-alerts` — check all log files against thresholds, dispatch notifications
- `log-tracker:cleanup [--days=7]` — delete temporary export files older than N days
- `log-tracker:theme list|current|set {name}` — manage active theme without editing config

**Laravel 13 Support**
- `composer.json` now declares `illuminate/support: ^10.0|^11.0|^12.0|^13.0`; minimum PHP for L13 is `^8.3`
- CI matrix covers PHP 8.1 – 8.4 × Laravel 10 – 13 (10 jobs) with correct testbench version per release

---

#### 🔒 Security

- **Wildcard route constrained** — `{logName}` route parameter now has `->where('logName', '.+\.log')`, preventing path traversal via URL
- **`allow_download` config enforced** — `download()` now checks `config('log-tracker.allow_download')` and aborts `403` when disabled
- **Defence-in-depth `basename()` in parser** — `LogParserService::loadEntries()` applies `basename()` to the log name, blocking path traversal regardless of caller sanitization
- **GlowStack view `file_exists()` guard** — `filesize()` / `filemtime()` calls in `log-details.blade.php` now wrapped in `@if(file_exists(...))` to prevent PHP errors on deleted files
- **ExportController `readonly`** — injected `LogExportService` dependency declared `readonly`

---

#### 🐛 Bug Fixes

- **Stack trace assignment** — `array_reverse()` was incorrectly applied to raw file lines before parsing, causing stack traces to attach to the wrong entry with reversed frame order. Lines are now parsed in forward order and only the finished entries array is reversed.
- **Export format flag ignored** — disabled export formats are now properly rejected with `403`.
- **Bare mail alert view** — replaced plain `{{ $body }}` with a full branded HTML email template.

---

#### 🚀 Performance

- **Eliminated double file parse in log viewer** — `calculateFrequency()` now accepts pre-loaded entries; the log file is read only once per request instead of twice
- **Fast level count on file listing** — replaced full entry-struct parse with a single `preg_match_all()` scan of raw file content, avoiding all entry allocation
- **Bounded `recentLogs` buffer in dashboard** — adaptive sort+trim every 200 entries keeps memory usage constant regardless of total log volume
- **Combined stack trace detection regexes** — reduced from 17 individual `preg_match()` calls per line to 2 combined alternation patterns

---

#### 🧪 Testing

- Full PHPUnit test suite: **177 tests, 461 assertions — all passing**
- Feature tests: `LogFileControllerTest`, `DashboardControllerTest`, `SearchControllerTest`, `CompareControllerTest`, `ExportControllerTest`, `LogFrequencyTest`, `CheckLogAlertsCommandTest`, `CleanupCommandTest`, `ThemeCommandTest`, `ServiceProviderTest`, `LogAlertServiceTest`
- Unit tests: `LogParserServiceTest`, `LogExportServiceTest`, `ThemeManagerTest`
- Uses `orchestra/testbench` — no parent Laravel project needed
- CI coverage job (push-only) uploads to Codecov; badge added to README
- **CONTRIBUTING.md** — setup, testing, Pint formatting, and PR guidelines

---

#### 🗺️ New Routes

| Method | URI | Description |
|--------|-----|-------------|
| GET | `/log-tracker/search` | Global multi-file search |
| GET | `/log-tracker/compare` | Side-by-side file comparison |
| GET | `/log-tracker/export` | Export form |
| POST | `/log-tracker/export` | Trigger export download |
| GET | `/log-tracker/export/{logName}/{format}` | Quick export from viewer |
| GET | `/log-tracker/api/dashboard-refresh` | Live dashboard JSON refresh |

---

## [2.3] - 2025-09-29
### 🧹 Log Management & UI Polish Release - Version 2.3
#### ✨ Added
- **Clear Log Functionality**: Implemented comprehensive log clearing system with safety confirmations
  - **Selective Clearing**: Clear individual log files or all logs at once
  - **Confirmation Dialogs**: Added safety prompts to prevent accidental data loss
  - **Admin Authorization**: Integrated with existing authorization middleware for secure log management
- **Enhanced Footer Design**: Complete footer redesign with improved visual appeal and functionality
  - **Professional Layout**: Modern footer design with better spacing and typography
  - **Responsive Design**: Optimized footer for all device sizes and screen resolutions
  - **Theme Consistency**: Unified footer styling across both GlowStack and LiteFlow themes

#### 🔧 Improved
- **User Experience**: Enhanced overall interface polish with refined visual elements
- **Navigation Flow**: Streamlined log management workflow with intuitive clear log controls
- **Visual Consistency**: Improved design coherence across all UI components

#### 🐛 Fixed
- **Footer Responsiveness**: Resolved footer layout issues on mobile and tablet devices
- **Theme Integration**: Ensured clear log functionality works seamlessly across both available themes

## [2.2] - 2025-09-09
### 🎨 UI/UX Enhancement Release - Version 2.2
#### ✨ Added
- **Client-Side Pagination**: Implemented full client-side pagination system for instant navigation without server requests
- **Full-File Search**: Enhanced search functionality to work across all log entries, not just current page
- **Dynamic Pagination Configuration**: Added fully configurable pagination system through `config/log-tracker.php`
- **Interactive Hover Tooltips**: Added detailed mouse hover effects on all log level stat badges showing exact counts and breakdowns
- **Enhanced Log Level Display**: Improved Smart Filters to dynamically show only log levels with actual entries (count > 0)
- **Responsive 4-Column Layout**: Optimized log list table for better mobile and tablet compatibility

#### 🔧 Improved
- **Pagination System**: Complete overhaul from server-side to client-side pagination for better performance:
  - **Instant Navigation**: Page changes happen instantly without server round-trips
  - **Config-Driven**: Pagination per-page values fully controlled by `log_per_page` config setting
  - **Search Integration**: Search works across entire file, pagination adapts to filtered results
  - **Memory Efficient**: JavaScript handles large datasets efficiently with optimized DOM manipulation
- **Log Level Categorization**: Restructured log display into professional 4-column layout:
  - **Total**: Overall log count with hover details
  - **Critical**: Critical logs only with individual count
  - **Errors**: Error logs only with individual count  
  - **Others**: Combined Emergency, Alert, Warning, Notice, Info, Debug levels with detailed breakdown on hover
- **Smart Filters Enhancement**: Modified controller logic to show all 9 configured log levels but display only those with entries > 0
- **Cross-Theme Consistency**: Applied uniform improvements across both GlowStack and LiteFlow themes
- **Mobile Responsiveness**: Fixed responsive design issues while maintaining professional log categorization

#### 🐛 Fixed
- **LiteFlow Theme Simplification**: Removed duplicate search bars and pagination controls for cleaner interface
- **Pagination Controls**: Cleaned up dual pagination systems, optimized for single search and single pagination
- **JavaScript Event Handling**: Fixed search input synchronization and pagination button generation
- **Responsive Design Issues**: Resolved table layout breaking on mobile devices caused by too many columns
- **Log Level Display Logic**: Fixed Smart Filters showing hardcoded 4 levels instead of dynamic configuration-based levels
- **Theme Consistency**: Ensured both themes display identical functionality and layout structure

#### 🚀 Performance Enhancements
- **Zero Server Requests**: Pagination and search now work entirely client-side for instant responses
- **Optimized Rendering**: Efficient DOM manipulation for large log files
- **Memory Management**: Smart row visibility management to handle thousands of log entries
- **Keyboard Shortcuts**: Added Ctrl+F for search focus and Escape for clearing search/closing menus

## [2.1] - 2025-08-20
### 🚀 Performance & Bug Fix Release - Version 2.1
#### ✨ Added
- **Log File Pagination**: Improved performance with paginated log file listing for better handling of large numbers of log files
- **Enhanced Log Pagination**: Streamlined log entry pagination system for faster navigation
- **Max File Size Checking**: Added intelligent file size validation to prevent memory issues with oversized log files
- **Dynamic Laravel Log Levels**: Implemented dynamic color and icon system for Laravel log levels with improved visual feedback

#### 🐛 Fixed
- **HTML Syntax Error**: Resolved syntax error in GlowStack theme that was breaking the UI
- **Memory Optimization**: Enhanced code efficiency for better performance with large log files
- **UI Rendering Issues**: Improved template rendering and fixed display problems

#### 🔧 Improved
- **Code Efficiency**: Significantly optimized codebase for better performance and maintainability
- **Error Handling**: Enhanced error handling for large files and edge cases
- **User Experience**: Improved overall user interface responsiveness and reliability

## [2.0] - 2025-06-17
### 🎉 Major Release - Version 2.0
#### ✨ Added
- **Dual Theme System**: LiteFlow (minimal) and GlowStack (modern) themes
- **Zero-Dependency Export**: CSV, Excel, PDF, JSON export without external libraries
- **Enhanced Export Configuration**: Detailed format settings and limits

#### 🐛 Fixed
- Enhanced UI/UX improvements across both themes

## [1.4] - 2025-05-21

### Fixed
- UI & UX Enhancements
- Bug fixed.

## [1.3.0] - 2025-04-20

### Fixed
- Bug fixed.

## [1.2.0] - 2025-03-17

### Fixed
- Solved stack trace issue where **stack trace was not displayed** for certain log entries.


## [1.1.0] - 2025-03-12

### Added
- **PHP 5.6+ Compatibility**
    - Updated syntax and function usage to support **PHP 5.6 and later**.
    - Ensured compatibility with older Laravel versions while maintaining modern standards.

- **Enhanced Filtering System**
    - Implemented **"No logs found"** message when no logs match the search/filter criteria.
    - Improved search functionality for **faster and more accurate filtering**.

- **UI & UX Enhancements**
    - **Log Level Filtering Improvement**
        - Redesigned log level filter switches for better user experience.
        - Users can now dynamically toggle log levels in a **more intuitive way**.
   
### Fixed
- Resolved an issue where **log filtering was not applying correctly** when using multiple search inputs.
- **Performance optimizations** in log parsing and rendering.
- Removed unnecessary **inline PHP code** in Blade files for **cleaner templates**.


## [1.0.0] - 2025-03-11
### Added
- Initial release of Laravel Log Tracker.
