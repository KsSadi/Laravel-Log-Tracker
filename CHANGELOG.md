# Changelog

All notable changes to this project will be documented in this file.

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


## [1.2.0] - 2025-03-12

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

