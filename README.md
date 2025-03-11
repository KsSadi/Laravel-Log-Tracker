<br>
<a href="https://github.com/KsSadi/Laravel-Log-Tracker">
<img style="width: 100%; max-width: 100%;" alt="Log Tracker Laravel Package" src="/image/log-tracker-banner.png" >
</a>

# **📜 Laravel Log Tracker**
<hr>

Laravel Log Tracker is a powerful, user-friendly package for tracking, analyzing, and managing application logs effortlessly. It provides a real-time dashboard with filtering, insights, and visualization of your log files.

![GitHub Repo stars](https://img.shields.io/github/stars/KsSadi/Laravel-Log-Tracker.svg)
[![Downloads](https://img.shields.io/packagist/dt/kssadi/Laravel-Log-Tracker)](https://packagist.org/packages/kssadi/Laravel-Log-Tracker)
![GitHub license](https://img.shields.io/github/license/KsSadi/Laravel-Log-Tracker.svg)
![GitHub top language](https://img.shields.io/github/languages/top/KsSadi/Laravel-Log-Tracker.svg)
![Packagist Version](https://img.shields.io/packagist/v/kssadi/Laravel-Log-Tracker.svg)



## 🚀 Features
✅ **Interactive Dashboard** – View log statistics with charts & analytics.  
✅ **Error Pattern Analysis** – Identify top error types and peak error hours.  
✅ **Log Filtering & Searching** – Filter logs by level, date, or keywords.  
✅ **Log File Management** – View, download, and delete log files easily.  
✅ **Customizable Configuration** – Define log levels, colors, and icons.
# Table of Contents

- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Author](#author)
- [Contributing](#contributing)
- [License](#license)


## Supported PHP and Laravel Versions

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

# Installation

1. ### **Install the package via Composer:**

   ```bash
   composer require kssadi/log-tracker
2. ### **Publish the configuration file:**

   ```bash
   php artisan vendor:publish --tag=log-tracker-config
   ```
   This will publish the `log-tracker.php` configuration file to your `config` directory.



# Configuration
After publishing the config, you can modify `config/log-tracker.php` file

```php
return [
    'route_prefix' => 'log-tracker',
    'middleware' => ['web', 'auth'],
    'log_levels' => [
        'emergency' => ['color' => '#DC143C', 'icon' => 'fas fa-skull-crossbones'], 
        'alert'     => ['color' => '#FF0000', 'icon' => 'fas fa-bell'],             
        'critical'  => ['color' => '#FF4500', 'icon' => 'fas fa-exclamation-triangle'], 
        'error'     => ['color' => '#FF6347', 'icon' => 'fas fa-exclamation-circle'],   
        'warning'   => ['color' => '#FFA500', 'icon' => 'fas fa-exclamation-triangle'], 
        'notice'    => ['color' => '#32CD32', 'icon' => 'fas fa-info-circle'],         
        'info'      => ['color' => '#1E90FF', 'icon' => 'fas fa-info-circle'],         
        'debug'     => ['color' => '#696969', 'icon' => 'fas fa-bug'],                 
        'total'     => ['color' => '#008000', 'icon' => 'fas fa-file-alt'],            
    ],

    'per_page' => 50,
    'max_file_size' => 50,
    'allow_delete' => false,
    'allow_download' => true,
];


```
#  Environment Configuration

Add the following environment variables to your `.env` file:

```bash
LOG_CHANNEL=daily  # Recommended for structured logging
LOG_LEVEL=debug    # Set the minimum log level to be recorded
```

# **Log Channel Configuration**

Update your `config/logging.php` file to use the `daily` log channel:

```php
'daily' => [
    'driver' => 'daily',
    'path' => storage_path('logs/laravel.log'),
    'level' => env('LOG_LEVEL', 'debug'),
    'days' => 30, // Keep logs for the last 30 days
],
```

# Usage

### Access the Log Dashboard

To access the log tracker dashboard, navigate to `/log-tracker` in your browser. You can filter logs by level, date, or search for specific keywords.


http://your-app.test/log-tracker


### 📄 View Log Files

http://your-app.test/log-tracker/log-file

### 🔍 Search & Filter Logs

- Search logs by keywords.
- Filter logs by level (Error, Warning, Info, Debug).
- Filter logs by date (Last hour, Today, Last 7 days, Custom range).

### 📥 Download Log Files

Click the Download button from a log file.

### 🗑 Delete Log Files

Click the Delete button to delete a log file.

# Screenshots

![Log Tracker Dashboard](/image/log-tracker-dashboard.png)

![Log Tracker Log File](/image/log-tracker-log-file.png)

![Log Tracker Log File](/image/log-tracker-log-file-view.png)

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


# Author

**Name:** Khaled Saifullah Sadi  
**Email:** [mdsadi4@gmail.com](mailto:mdsadi4@gmail.com) <br>

[![Buy Me a Coffee](https://img.shields.io/badge/-Buy%20Me%20a%20Coffee-orange?style=flat&logo=buy-me-a-coffee)](https://www.buymeacoffee.com/kssadi)



# License

This package is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).

# 📝 Changelog

Check out the [CHANGELOG](CHANGELOG.md) for the latest updates and features.

# 🤝 Support

Need help? Open an Issue or Discussion on GitHub!
If you like this project, ⭐️ Star it on GitHub.

### Social Handles

<p align="center">

<a href="https://www.linkedin.com/in/kssadi/" target="_blank"><img alt="LinkedIn" title="LinkedIn" src="https://img.shields.io/badge/LinkedIn-%230077B5.svg?&style=for-the-badge&logo=linkedin&logoColor=white"/>
</a>
<a href="https://facebook.com.com/mdsadi100" target="_blank"><img alt="Facebook" title="Facebook" src="https://img.shields.io/badge/-Facebook-1DA1F2?style=for-the-badge&logo=facebook&logoColor=white"/>
<a href="https://insta.com/Ks.Sadi" target="_blank"><img alt="Instagram" title="Instagram" src="https://img.shields.io/badge/-Instagram-C13584?style=for-the-badge&logo=instagram&logoColor=white"/>
<a href="https://twitter.com/Ks.Sadi" target="_blank"><img alt="Twitter" title="Twitter" src="https://img.shields.io/badge/-Twitter-1DA1F2?style=for-the-badge&logo=twitter&logoColor=white"/>
</a>
<a href="https://github.com/KsSadi" target="_blank"><img alt="Github" title="Github" src="https://img.shields.io/badge/github-%23323330.svg?&style=for-the-badge&logo=github&logoColor=%23F7DF1E"/>
</a>

</p>

---

Copyright 2025 [Khaled Saifullah Sadi]()


