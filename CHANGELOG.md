# Changelog

All notable changes to this project will be documented in this file.

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

