# Changelog

All notable changes to this project will be documented in this file.

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
