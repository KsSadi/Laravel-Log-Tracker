<br>
<a href="https://github.com/KsSadi/Laravel-Log-Tracker">
<img style="width: 100%; max-width: 100%;" alt="Log Tracker Laravel Package" src="/image/log-tracker-banner.png" >
</a>

# **📜 Laravel Log Tracker**
<hr>

Laravel Log Tracker is a powerful, user-friendly package for tracking, analyzing, and managing application logs effortlessly. It provides a real-time dashboard with filtering, insights, and visualization of your log files.

![GitHub Repo stars](https://img.shields.io/github/stars/KsSadi/Laravel-Log-Tracker.svg)
[![Downloads](https://img.shields.io/packagist/dt/kssadi/log-tracker)](https://packagist.org/packages/kssadi/log-tracker)
![GitHub license](https://img.shields.io/github/license/KsSadi/Laravel-Log-Tracker.svg)
![GitHub top language](https://img.shields.io/github/languages/top/KsSadi/Laravel-Log-Tracker.svg)
![Packagist Version](https://img.shields.io/packagist/v/kssadi/log-tracker.svg)



## 🚀 **Key Features**
✅ **Interactive Dashboard** – Comprehensive log analytics with charts and real-time insights  
✅ **Dual Theme System** – Choose between **LiteFlow** (minimal) and **GlowStack** (modern) themes  
✅ **Zero-Dependency Exports** – Export logs in CSV, Excel, PDF, JSON without external libraries  
✅ **Error Pattern Analysis** – Identify top error types and peak error hours  
✅ **Advanced Filtering** – Filter logs by level, date range, or custom keywords  
✅ **Log File Management** – View, download, delete, and manage log files effortlessly  
✅ **Real-time Statistics** – Live log counts and performance metrics  
✅ **Stack Trace Viewer** – Detailed error stack traces with syntax highlighting  
✅ **Responsive Design** – Works perfectly on desktop, tablet, and mobile devices  
✅ **Customizable Configuration** – Tailor log levels, colors, icons, and behavior to your needs  
✅ **Performance Optimized** – Handles large log files efficiently with pagination  
✅ **Secure Access** – Built-in authentication and authorization middleware
# Table of Contents

- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Themes](#themes)
- [Author](#author)
- [Contributing](#contributing)


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
    'per_page' => 50,
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

---

## **Themes**

Laravel Log Tracker offers **two distinct themes** to match your workflow and preferences:

### **Available Themes**

| Theme | Description | Features |
|-------|-------------|----------|
| 🌊 **LiteFlow** | Minimal, clean log view with streamlined interface | Clean design, fast loading, excellent readability |
| ✨ **GlowStack** | Modern, colorful, enhanced visual log view | Rich colors, advanced styling, enhanced user experience |

### **Theme Configuration**

Set your preferred theme in `config/log-tracker.php`:

```php
'theme' => 'GlowStack', // Options: 'LiteFlow', 'GlowStack'
```

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

![Log Tracker Dashboard](/image/log-tracker-dashboard.png)

![Log Tracker Log File](/image/log-tracker-log-file.png)

![Log Tracker Log File](/image/log-tracker-log-file-view.png)


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

# Contributing

We welcome contributions! Follow these steps to get started:

### 1️⃣ Fork the Repository
Click the **Fork** button on the top-right of this repository to create your copy.

### 2️⃣ Clone Your Fork
Run the following command to clone the repository to your local machine:

```sh
git clone https://github.com/your-username/Laravel-Log-Tracker.git
cd Laravel-Log-Tracker
```

### 3️⃣ Create a New Branch
Before making changes, create a new branch:

```sh
git checkout -b my-new-feature
```

### 4️⃣ Make Your Changes & Commit
After making your modifications, commit your changes:

```sh
git add .
git commit -m "Added feature: real-time log monitoring"
```

### 5️⃣  Push to GitHub & Create a Pull Request
Push your changes to GitHub:

```sh
git push origin my-new-feature
```

Now, go to your forked repository on GitHub and click "New Pull Request" to submit your changes for review.

### **🐛 Bug Reports**
Found a bug? Please report it with:
- Laravel and PHP versions
- Steps to reproduce
- Expected vs actual behavior
- Error messages or logs

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