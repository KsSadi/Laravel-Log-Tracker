<br>
<a href="https://github.com/KsSadi/Laravel-Log-Tracker">
<img style="width: 100%; max-width: 100%;" alt="Log Tracker Laravel Package" src="/image/log_tracker_banner.png" >
</a>

# **📜 Laravel Log Tracker**
<hr>

Laravel Log Tracker is a powerful, user-friendly package for tracking, analyzing, and managing application logs effortlessly. It provides a real-time dashboard with filtering, insights, and visualization of your log files.

![GitHub Repo stars](https://img.shields.io/github/stars/KsSadi/Laravel-Log-Tracker.svg)
[![Downloads](https://img.shields.io/packagist/dt/kssadi/log-tracker)](https://packagist.org/packages/kssadi/log-tracker)
![GitHub license](https://img.shields.io/github/license/KsSadi/Laravel-Log-Tracker.svg)
![GitHub top language](https://img.shields.io/github/languages/top/KsSadi/Laravel-Log-Tracker.svg)
![Packagist Version](https://img.shields.io/packagist/v/kssadi/log-tracker.svg)

<div align="center">

### 🌐 [Official Website & Documentation](https://kssadi.github.io/Laravel-Log-Tracker/)

*Explore features, screenshots, themes, export formats, and full documentation on the official site.*

</div>

---

<div align="center">

### 🏢 Proudly Supported by Business Automation Ltd

*This open-source package is maintained by **[Sadi](https://github.com/KsSadi)** and supported by **[Business Automation Ltd](https://ba-systems.com)***  
*Building enterprise-grade e-governance and automation solutions across Bangladesh*

</div>

---

## 🚀 **Key Features**
✅ **Interactive Dashboard** – Comprehensive log analytics with charts and real-time insights  
✅ **Dual Theme System** – Choose between **LiteFlow** (minimal) and **GlowStack** (modern) themes  
✅ **Zero-Dependency Exports** – Export logs in CSV, Excel, PDF, JSON without external libraries  
✅ **Error Pattern Analysis** – Identify top error types and peak error hours  
✅ **Advanced Filtering** – Filter logs by level, date range, or custom keywords  
✅ **Log File Management** – View, download, delete, and manage log files effortlessly  
✅ **Smart File Pagination** – Efficiently handle large numbers of log files with pagination   
✅ **Dynamic Log Levels** – Laravel log levels with dynamic colors and icons  
✅ **Max File Size Protection** – Intelligent file size checking to prevent memory issues  
✅ **Real-time Statistics** – Live log counts and performance metrics  
✅ **Stack Trace Viewer** – Detailed error stack traces with syntax highlighting  
✅ **Responsive Design** – Works perfectly on desktop, tablet, and mobile devices  
✅ **Customizable Configuration** – Tailor log levels, colors, icons, and behavior to your needs  
✅ **Performance Optimized** – Handles large log files efficiently with enhanced pagination 
✅ **Secure Access** – Built-in authentication and authorization middleware
# Table of Contents

- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Themes](#themes)
- [Author](#author)


## 🖥️ **System Requirements**

### **Supported PHP and Laravel Versions**

| Laravel Version | Supported PHP Versions |
|------|------------------------|
| 12.x | 8.1 - 8.4              |
| 11.x | 8.1 - 8.3              |
| 10.x | 8.0 - 8.2              |
| 9.x  | 8.0 - 8.1              |
| 8.x  | 7.3 - 8.0              |
| 7.x  | 7.2 - 7.4              |
| 6.x  | 7.2 - 7.4              |
| 5.8  | 7.1 - 7.3              |
| 5.7  | 7.1 - 7.2              |
| 5.6  | 7.0 - 7.2              |
| 5.5  | 7.0 - 7.2              |

## ✅ Checked Versions

| Laravel Version | PHP Version | Status |
|-----------------|-------------|--------|
| 12.x            | 8.2, 8.4    | ✅ Working |
| 11.x            | 8.2, 8.3    | ✅ Working |
| 10.x            | 8.1, 8.2    | ✅ Working |
| 9.x             | 8.0, 8.1    | ⚠️ Not Tested |
| 8.x             | 7.4, 8.0    | ⚠️ Not Tested |

> **Note:** If you encounter issues with a specific version, feel free to report them in the [issues](https://github.com/KsSadi/Laravel-Log-Tracker/issues) section.


# Installation

1. ### **Install the package via Composer:**

   ```bash
   composer require kssadi/log-tracker
2. ### **Publish the configuration file:**

   ```bash
   php artisan vendor:publish --provider="Kssadi\LogTracker\LogTrackerServiceProvider" --tag="config"
   ```
   This will publish the `log-tracker.php` configuration file to your `config` directory.

---

# Configuration

### **Basic Configuration**

After publishing the config, customize your `config/log-tracker.php` file:

```php
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Route Configuration
    |--------------------------------------------------------------------------
    */
    'route_prefix' => 'log-tracker',
    'middleware' => ['web', 'auth'],

    /*
    |--------------------------------------------------------------------------
    | Theme Selection
    |--------------------------------------------------------------------------
    */
    'theme' => 'GlowStack', // Options: 'LiteFlow', 'GlowStack'

    /*
    |--------------------------------------------------------------------------
    | Display Settings
    |--------------------------------------------------------------------------
    */
    'log_per_page' => 50,
    'log_files_per_page' => 10,
    'max_file_size' => 50, // MB

    /*
    |--------------------------------------------------------------------------
    | Feature Permissions
    |--------------------------------------------------------------------------
    */
    'allow_delete' => true,
    'allow_download' => true,

    /*
    |--------------------------------------------------------------------------
    | Export Configuration
    |--------------------------------------------------------------------------
    */
    'export' => [
        'enabled' => true,
        'formats' => [
            'csv' => [
                'enabled' => true,
                'description' => 'Excel-compatible CSV format'
            ],
            'json' => [
                'enabled' => true,
                'description' => 'Structured JSON with metadata'
            ],
            'excel' => [
                'enabled' => true,
                'description' => 'Native Excel XML format'
            ],
            'pdf' => [
                'enabled' => true,
                'description' => 'Print-ready HTML report'
            ],
        ],
        'limits' => [
            'max_entries' => 50000,
            'max_file_size_mb' => 50,
            'timeout_seconds' => 300,
        ],
        'storage' => [
            'cleanup_after_days' => 7,
        ],
    ],
];
```

##  Environment Configuration
Add the following environment variables to your `.env` file:

```php
LOG_CHANNEL=daily  # Recommended for structured logging
LOG_LEVEL=debug    # Set the minimum log level to be recorded

```


## **Log Channel Configuration**

Update your `config/logging.php` file to use the `daily` log channel:

```php
'daily' => [
    'driver' => 'daily',
    'path' => storage_path('logs/laravel.log'),
    'level' => env('LOG_LEVEL', 'debug'),
    'days' => 30, // Keep logs for the last 30 days
],
```
## **Usage**

### **🏠 Dashboard Access**

Navigate to the main dashboard to get an overview of your logs:

```
https://your-domain.com/log-tracker
```

**Dashboard Features:**
- Real-time log statistics
- Error pattern analysis
- Peak error hours visualization
- Recent log entries
- Daily log trends (last 7 days)

### **📁 Log File Management**

View and manage individual log files:

```
https://your-domain.com/log-tracker/log-file
```

**Available Actions:**
- 👁️ **View** - Browse log entries with pagination
- 📥 **Download** - Download original log files
- 📊 **Export** - Export logs in various formats
- 🗑️ **Delete** - Remove log files (if enabled)

### **🔍 Advanced Filtering**

**Filter by Log Level:**
- Emergency, Alert, Critical
- Error, Warning, Notice
- Info, Debug

**Filter by Time:**
- Last hour
- Today
- Last 7 days
- Custom date range

**Search Features:**
- Keyword search in log messages
- Multiple search terms support

### **📝 Generating Log Examples**

To test and populate your logs with different types of entries, you can use Laravel's built-in `Log` facade. Here are some practical examples:

```php
<?php

use Illuminate\Support\Facades\Log;

// Basic log levels
Log::info('User logged in successfully', ['user_id' => 123]);
Log::warning('Disk space is running low', ['disk_usage' => '85%']);
Log::error('Payment processing failed', ['order_id' => 'ORD-12345', 'error' => 'Gateway timeout']);

// Emergency and Critical logs
Log::emergency('Database connection lost completely');
Log::critical('Memory limit exceeded', ['memory_usage' => '512MB']);
Log::alert('Security breach detected', ['ip_address' => '192.168.1.100']);

// Debug and Notice logs
Log::debug('API response received', ['response_time' => '250ms', 'endpoint' => '/api/users']);
Log::notice('User password changed', ['user_id' => 456]);

// Logs with context data
Log::error('File upload failed', [
    'file_name' => 'document.pdf',
    'file_size' => '5MB',
    'user_id' => 789,
    'error_code' => 'UPLOAD_TIMEOUT'
]);

// Exception logging
try {
    // Some risky operation
    throw new \Exception('Sample exception for testing');
} catch (\Exception $e) {
    Log::error('Exception caught: ' . $e->getMessage(), [
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);
}
```

**📊 View Generated Logs:**
After running these examples in your Laravel application (via tinker, controllers, or artisan commands), visit your log tracker dashboard to see them categorized and displayed beautifully!

---

## **Themes**

Laravel Log Tracker offers **two distinct themes** to match your workflow and preferences:

### **Available Themes**

| Theme | Description | Features |
|-------|-------------|----------|
| 🌊 **LiteFlow** | Minimal, clean log view with streamlined interface | Clean design, fast loading, excellent readability |
| ✨ **GlowStack** | Modern, colorful, enhanced visual log view | Rich colors, advanced styling, enhanced user experience |

![Log Tracker Theme](/image/log_tracker_log_theme.png)


### **Theme Configuration**

Set your preferred theme in `config/log-tracker.php`:

```php
'theme' => 'GlowStack', // Options: 'LiteFlow', 'GlowStack'
```

### **🎨 Theme Management via Artisan Commands**

Laravel Log Tracker provides convenient Artisan commands to manage themes without editing configuration files:

#### **List Available Themes**
```bash
php artisan log-tracker:theme list
```
**Output:**
```
Available Log Tracker Themes:

  • GlowStack
  • LiteFlow ← Current
```

#### **Check Current Theme**
```bash
php artisan log-tracker:theme current
```
**Output:**
```
Current Theme: LiteFlow
```

#### **Switch Theme**
```bash
# Switch to GlowStack theme
php artisan log-tracker:theme set GlowStack

# Switch to LiteFlow theme  
php artisan log-tracker:theme set LiteFlow
```

**Example Usage:**
```bash
# Check what themes are available
php artisan log-tracker:theme list

# Switch to the modern GlowStack theme
php artisan log-tracker:theme set GlowStack

# Verify the change
php artisan log-tracker:theme current
```

**💡 Pro Tip:** Theme changes take effect immediately - no cache clearing or server restart required!

### **Theme Features**

**🌊 LiteFlow Theme:**
- Minimalist design philosophy
- Faster page loads with reduced styling
- Perfect for high-frequency log monitoring
- Clean, distraction-free interface
- Optimized for performance

**✨ GlowStack Theme (Default):**
- Modern, vibrant color scheme
- Enhanced visual hierarchy
- Rich animations and transitions
- Advanced styling elements
- Improved user engagement


---

## 📤 **Export Features**

Export your logs in multiple formats for external analysis and reporting with **zero external dependencies**:

### **Supported Export Formats**

| Format | Extension | Description | Features |
|--------|-----------|-------------|----------|
| 📊 **CSV** | `.csv` | Excel-compatible CSV format | Universal compatibility, easy data analysis |
| 📈 **Excel** | `.xlsx` | Native Excel XML format | Rich formatting, ready for spreadsheet analysis |
| 📄 **PDF** | `.pdf` | Print-ready HTML report | Professional reports, easy sharing |
| 🔗 **JSON** | `.json` | Structured JSON with metadata | API integration, programmatic processing |

### **Export Configuration**

Configure export settings in `config/log-tracker.php`:

```php
'export' => [
    'enabled' => true,
    'formats' => [
        'csv' => [
            'enabled' => true,
            'description' => 'Excel-compatible CSV format'
        ],
        'json' => [
            'enabled' => true,
            'description' => 'Structured JSON with metadata'
        ],
        'excel' => [
            'enabled' => true,
            'description' => 'Native Excel XML format'
        ],
        'pdf' => [
            'enabled' => true,
            'description' => 'Print-ready HTML report'
        ],
    ],
    'limits' => [
        'max_entries' => 50000,        // Maximum records per export
        'max_file_size_mb' => 50,      // Max file size for processing
        'timeout_seconds' => 300,       // Export timeout
    ],
    'storage' => [
        'cleanup_after_days' => 7,     // Auto-cleanup exported files
    ],
],
```

### **Export Features**

✅ **No External Dependencies** - All export formats work out of the box  
✅ **Large File Support** - Handle up to 50,000 log entries per export  
✅ **Smart Filtering** - Export only filtered/searched results  
✅ **Auto Cleanup** - Exported files are automatically cleaned up after 7 days  
✅ **Format Flexibility** - Enable/disable specific export formats as needed

### **How to Export**

1. Navigate to any log file view
2. Click the **"Export"** button
3. Select your preferred format
4. Choose date range and filters
5. Download the generated file

---

# Screenshots

![Log Tracker Dashboard](/image/log_tracker_dashboard.png)

![Log Tracker Log File](/image/log_tracker_log_file.png)

![Log Tracker Log File](/image/log_tracker_log_file_view.png)


## 📚 **API Documentation**

### **Available Routes**

```php
// Dashboard
GET /log-tracker

// Log file list
GET /log-tracker/log-file

// View specific log file
GET /log-tracker/{logName}

// Download log file
GET /log-tracker/download/{logName}

// Export log file
POST /log-tracker/export/{logName}

// Delete log file (if enabled)
POST /log-tracker/delete/{logName}
```

### **Middleware**

Default middleware can be customized in configuration:

```php
'middleware' => ['web', 'auth'],
```

---

# 📝 Changelog

Check out the [CHANGELOG](CHANGELOG.md) for the latest updates and features.



## 🆘 **Support**

### **📞 Get Help**

- 🐛 **Bug Reports**: [GitHub Issues](https://github.com/KsSadi/Laravel-Log-Tracker/issues)
- 💬 **Feature Requests**: [GitHub Discussions](https://github.com/KsSadi/Laravel-Log-Tracker/discussions)
- 📖 **Documentation**: [Wiki](https://github.com/KsSadi/Laravel-Log-Tracker/wiki)
- 💌 **Email Support**: [mdsadi4@gmail.com](mailto:mdsadi4@gmail.com)

### **⭐ Show Your Support**

If Laravel Log Tracker helps your project:
- ⭐ **Star the repository** on GitHub
- 🍵 **Buy me a coffee** to fuel development
- 📢 **Share** with the Laravel community
- 📝 **Write a review** or blog post

[![Buy Me a Coffee](https://img.shields.io/badge/-Buy%20Me%20a%20Coffee-orange?style=for-the-badge&logo=buy-me-a-coffee&logoColor=white)](https://www.buymeacoffee.com/kssadi)

---

## ❤️ Supported by Business Automation Ltd

<div align="center">

This package is maintained by [Sadi](https://github.com/KsSadi) and supported by  
**[Business Automation Ltd](https://ba-systems.com)** — building large-scale e-gov & enterprise solutions in Bangladesh.

<a href="https://ba-systems.com">
  <img src="https://ba-systems.com/png/BA.png" alt="Business Automation Ltd" width="180">
</a>

</div>

---

## **Author**

<div align="center">

**Khaled Saifullah Sadi**  
*Full Stack Developer & Laravel Enthusiast*

[![Email](https://img.shields.io/badge/Email-mdsadi4%40gmail.com-red?style=for-the-badge&logo=gmail&logoColor=white)](mailto:mdsadi4@gmail.com)
[![LinkedIn](https://img.shields.io/badge/LinkedIn-kssadi-blue?style=for-the-badge&logo=linkedin&logoColor=white)](https://www.linkedin.com/in/kssadi/)
[![GitHub](https://img.shields.io/badge/GitHub-KsSadi-black?style=for-the-badge&logo=github&logoColor=white)](https://github.com/KsSadi)

</div>

### **🌐 Connect With Me**

<p align="center">
<a href="https://www.linkedin.com/in/kssadi/" target="_blank">
<img alt="LinkedIn" src="https://img.shields.io/badge/LinkedIn-%230077B5.svg?&style=for-the-badge&logo=linkedin&logoColor=white"/>
</a>
<a href="https://facebook.com/mdsadi100" target="_blank">
<img alt="Facebook" src="https://img.shields.io/badge/-Facebook-1877F2?style=for-the-badge&logo=facebook&logoColor=white"/>
</a>
<a href="https://instagram.com/Ks.Sadi" target="_blank">
<img alt="Instagram" src="https://img.shields.io/badge/-Instagram-E4405F?style=for-the-badge&logo=instagram&logoColor=white"/>
</a>
<a href="https://x.com/mdsadi4" target="_blank">
<img alt="Twitter" src="https://img.shields.io/badge/-Twitter-1DA1F2?style=for-the-badge&logo=twitter&logoColor=white"/>
</a>
</p>

---

## 📄 **License**

This package is open-source software licensed under the **[MIT License](https://opensource.org/licenses/MIT)**.

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
```

---

<div align="center">

**⭐ If you find this package useful, please consider giving it a star! ⭐**

**Made with ❤️ for the Laravel Community**

---

*Copyright © 2025 [Khaled Saifullah Sadi](https://github.com/KsSadi). All rights reserved.*

</div>
