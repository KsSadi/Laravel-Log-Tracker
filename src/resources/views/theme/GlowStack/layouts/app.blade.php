<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Log Tracker')</title>
    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --warning-gradient: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
            --danger-gradient: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);
            --info-gradient: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%);
            --dark-gradient: linear-gradient(135deg, #2d3748 0%, #1a202c 100%);
            --shadow-soft: 0 10px 25px rgba(0,0,0,0.08);
            --shadow-hover: 0 15px 35px rgba(0,0,0,0.15);
            --border-radius: 15px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
            overflow-x: hidden;
        }

        /* Modern Navigation */
        .modern-navbar {
            background: var(--primary-gradient);
            backdrop-filter: blur(20px);
            border: none;
            box-shadow: var(--shadow-soft);
            padding: 1rem 0;
            position: relative;
            overflow: hidden;
        }

        .modern-navbar::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 200px;
            height: 200px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
            animation: float-nav 10s ease-in-out infinite;
        }

        .modern-navbar::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -5%;
            width: 150px;
            height: 150px;
            background: rgba(255,255,255,0.03);
            border-radius: 50%;
            animation: float-nav 8s ease-in-out infinite reverse;
        }

        @keyframes float-nav {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        .navbar-container {
            position: relative;
            z-index: 10;
        }

        .modern-navbar-brand {
            color: white !important;
            font-weight: 700 !important;
            font-size: 1.4rem;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: var(--transition);
            padding: 0.5rem 0;
        }

        .modern-navbar-brand:hover {
            color: rgba(255,255,255,0.9) !important;
            transform: translateY(-2px);
        }

        .brand-icon {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
        }

        .navbar-toggler {
            border: 2px solid rgba(255,255,255,0.3);
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border-radius: 8px;
            padding: 0.5rem;
            transition: var(--transition);
        }

        .navbar-toggler:hover {
            background: rgba(255,255,255,0.2);
            border-color: rgba(255,255,255,0.5);
            transform: scale(1.05);
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.8%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='m4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        .modern-nav-menu {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .modern-nav-item {
            margin: 0;
        }

        .modern-nav-link {
            color: rgba(255,255,255,0.8) !important;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            padding: 0.75rem 1.25rem;
            border-radius: 25px;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            position: relative;
            background: transparent;
            border: 2px solid transparent;
            backdrop-filter: blur(10px);
        }

        .modern-nav-link:hover {
            color: white !important;
            background: rgba(255,255,255,0.15);
            border-color: rgba(255,255,255,0.2);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }

        .modern-nav-link.active {
            color: white !important;
            background: rgba(255,255,255,0.2);
            border-color: rgba(255,255,255,0.3);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .modern-nav-link i {
            font-size: 0.9rem;
            opacity: 0.9;
        }


        /* Modern Header */
        .page-header {
            background: var(--primary-gradient);
            border-radius: var(--border-radius);
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-soft);
            color: white;
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 200px;
            height: 200px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            animation: float 8s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-30px) rotate(180deg); }
        }

        .page-header h1 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .page-header p {
            opacity: 0.9;
            margin-bottom: 0;
        }


        /* Legacy card styles for backward compatibility */
        .card {
            transition: transform 0.3s ease;
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-soft);
            background: white;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }

        .card-header {
            border-radius: 12px 12px 0 0 !important;
            font-weight: 600;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }

        .stats-card {
            border-left: 4px solid;
        }

        .stats-icon {
            font-size: 2rem;
            opacity: 0.8;
        }

        .error-card {
            border-left-color: #dc3545;
        }

        .warning-card {
            border-left-color: #ffc107;
        }

        .info-card {
            border-left-color: #0d6efd;
        }

        .success-card {
            border-left-color: #198754;
        }

        .log-content {
            font-family: "JetBrains Mono", "Fira Code", "Courier New", monospace;
            font-size: 0.875rem;
            background: var(--dark-gradient);
            color: #f8f9fa;
            padding: 1.5rem;
            border-radius: var(--border-radius);
            height: 65vh;
            overflow-y: auto;
            white-space: pre-wrap;
            box-shadow: var(--shadow-soft);
        }

        .log-line {
            margin: 0;
            padding: 2px 0;
            transition: var(--transition);
        }

        .log-line:hover {
            background: rgba(255,255,255,0.05);
            padding-left: 10px;
            border-left: 2px solid rgba(255,255,255,0.2);
        }

        .log-error {
            color: #ff6b9d;
        }

        .log-warning {
            color: #ffd166;
        }

        .log-info {
            color: #74b9ff;
        }

        .log-debug {
            color: #00cec9;
        }

        .log-line .timestamp {
            color: #adb5bd;
            margin-right: 10px;
            font-weight: 500;
        }

        .log-tools {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-soft);
            border: 1px solid rgba(0,0,0,0.05);
        }

        .log-meta {
            border-left: 3px solid #667eea;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border-radius: 10px;
            padding: 1rem;
        }

        .log-meta-item {
            display: flex;
            margin-bottom: 0.5rem;
            align-items: center;
        }

        .log-meta-label {
            width: 120px;
            font-weight: 600;
            color: #374151;
        }

        .filter-box {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-soft);
            border: 1px solid rgba(0,0,0,0.05);
        }

        /* Clean Minimal Footer */
        .modern-footer {
            background: #2d3748;
            color: white;
            margin-top: 4rem;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        .footer-content {
            padding: 1.5rem 0;
            text-align: center;
        }

        .footer-brand {
            color: #4facfe;
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }

        .footer-link {
            color: #4facfe;
            text-decoration: none;
            transition: var(--transition);
        }

        .footer-link:hover {
            color: #00f2fe;
            text-decoration: none;
        }

        .footer-text {
            color: rgba(255,255,255,0.8);
            font-size: 0.9rem;
            margin: 0;
        }

        .footer-tagline {
            color: rgba(255,255,255,0.6);
            font-size: 0.85rem;
            margin: 0;
        }

        .coffee-button {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-size: 0.875rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 107, 53, 0.3);
            margin-top: 0.75rem;
        }

        .coffee-button:hover {
            color: white;
            text-decoration: none;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 107, 53, 0.4);
        }

        .coffee-button i {
            font-size: 1rem;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .modern-navbar {
                padding: 0.75rem 0;
            }

            .modern-navbar-brand {
                font-size: 1.2rem;
            }

            .brand-icon {
                width: 35px;
                height: 35px;
                font-size: 1rem;
            }

            .modern-nav-link {
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 480px) {
            .modern-nav-menu {
                flex-direction: column;
                gap: 0.25rem;
                width: 100%;
                margin-top: 1rem;
            }

            .modern-nav-link {
                width: 100%;
                justify-content: center;
                padding: 0.75rem;
            }
        }

        /* Smooth page transitions */
        .page-content {
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar:horizontal {
            display: none;
        }

        ::-webkit-scrollbar-track {
            background: rgba(0,0,0,0.05);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(102, 126, 234, 0.3);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(102, 126, 234, 0.5);
        }
    </style>
    @stack('styles')
</head>
<body>
<!-- Modern Navigation -->
<nav class="navbar navbar-expand-lg modern-navbar">
    <div class="container navbar-container">
        <a class="modern-navbar-brand" href="{{ route('log-tracker.dashboard') }}">
            <div class="brand-icon">
                <i class="fa-brands fa-searchengin"></i>
            </div>
            <span>Log Tracker</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto modern-nav-menu">
                <li class="nav-item modern-nav-item">
                    <a class="modern-nav-link {{ request()->routeIs('log-tracker.dashboard') ? 'active' : '' }}"
                       href="{{ route('log-tracker.dashboard') }}">
                        <i class="fas fa-chart-line"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item modern-nav-item">
                    <a class="modern-nav-link {{ request()->routeIs('log-tracker.index') ? 'active' : '' }}"
                       href="{{ route('log-tracker.index') }}">
                        <i class="fas fa-list"></i>
                        <span>Log Files</span>
                    </a>
                </li>
                <li class="nav-item modern-nav-item">
                    <a class="modern-nav-link {{ request()->routeIs('log-tracker.export.form') ? 'active' : '' }}"
                       href="{{ route('log-tracker.export.form') }}">
                        <i class="fas fa-download"></i>
                        <span>Export Logs</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="container my-4 page-content">
    @yield('content')
</div>

<!-- Clean Minimal Footer -->
<footer class="modern-footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-brand">
                <i class="fa-brands fa-searchengin"></i> LogTracker <v2 class="3">v2.3</v2>
            </div>
            <p class="footer-text">
                © <span id="year"></span>
                <a href="https://github.com/KsSadi/Laravel-Log-Tracker" class="footer-link" target="_blank">
                    LogTracker
                </a>
                Efficient logging, effortless insights.
            </p>
            <a href="https://www.buymeacoffee.com/kssadi" class="coffee-button" target="_blank">
                <i class="fas fa-coffee"></i>
                <span>Buy me a coffee</span>
            </a>
        </div>
    </div>
</footer>

<!-- JavaScript to Auto-Update Year -->
<script>
    document.getElementById("year").textContent = new Date().getFullYear();
</script>

<!-- Bootstrap 5.3.3 JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>

@stack('scripts')

<script>
    // Get log file name from URL parameter
    const urlParams = new URLSearchParams(window.location.search);
    const logId = urlParams.get('id');

    if (logId) {
        const logFileNameElement = document.getElementById('logFileName');
        const logFileBreadcrumbElement = document.getElementById('logFileBreadcrumb');

        if (logFileNameElement) logFileNameElement.textContent = logId;
        if (logFileBreadcrumbElement) logFileBreadcrumbElement.textContent = logId;
        document.title = "Log Viewer - " + logId;
    }

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Add loading animation for navigation links
    document.querySelectorAll('.modern-nav-link').forEach(link => {
        link.addEventListener('click', function(e) {
            if (this.href !== window.location.href) {
                this.style.opacity = '0.7';
                this.innerHTML += ' <i class="fas fa-spinner fa-spin ms-2"></i>';
            }
        });
    });

    // Enhance page transitions
    document.addEventListener('DOMContentLoaded', function() {
        // Add staggered animation to cards if they exist
        const cards = document.querySelectorAll('.card');
        cards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.1}s`;
            card.classList.add('animate-card');
        });
    });

    // Add CSS for card animations
    const cardAnimationStyle = document.createElement('style');
    cardAnimationStyle.textContent = `
    .animate-card {
        animation: slideInUp 0.6s ease-out forwards;
        opacity: 0;
        transform: translateY(30px);
    }
    @keyframes slideInUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
`;
    document.head.appendChild(cardAnimationStyle);

</script>


</body>
</html>
