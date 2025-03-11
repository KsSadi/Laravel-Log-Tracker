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
        body {
            background-color: #f8f9fa;
        }
        .navbar-brand {
            font-weight: 700;
        }
        .card {
            transition: transform 0.3s ease;
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card-header {
            border-radius: 10px 10px 0 0 !important;
            font-weight: 600;
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
            font-family: "Courier New", monospace;
            font-size: 0.875rem;
            background-color: #212529;
            color: #f8f9fa;
            padding: 1rem;
            border-radius: 0.5rem;
            height: 65vh;
            overflow-y: auto;
            white-space: pre-wrap;
        }
        .log-line {
            margin: 0;
            padding: 2px 0;
        }
        .log-error {
            color: #ff6b6b;
        }
        .log-warning {
            color: #ffd166;
        }
        .log-info {
            color: #63c5da;
        }
        .log-debug {
            color: #90be6d;
        }
        .log-line .timestamp {
            color: #adb5bd;
            margin-right: 10px;
        }
        .log-tools {
            background-color: white;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        .log-meta {
            border-left: 3px solid #0d6efd;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .log-meta-item {
            display: flex;
            margin-bottom: 0.25rem;
        }
        .log-meta-label {
            width: 120px;
            font-weight: 600;
        }
        .filter-box {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        #customDateRange input {
            width: 100%;
        }
        .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.7); /* Light color */
            transition: all 0.3s ease-in-out; /* Smooth hover effect */
        }

        .navbar-nav .nav-link:hover, .navbar-nav .nav-link.active {
            color: #0dc8ac; /* Bootstrap 'info' color (cyan) */
            background: rgba(255, 255, 255, 0.1); /* Subtle background */
            border-radius: 5px;
        }

        .navbar-brand {
            transition: color 0.3s ease-in-out;
        }

        .navbar-brand:hover {
            color: #038975 !important; /* A brighter hover effect */
        }


    </style>
    @stack('styles')
</head>
<body>
<!-- Navigation -->
<nav class="navbar navbar-expand-lg bg-dark navbar-dark shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('log-tracker.dashboard') }}">
            <i class="fa-brands fa-searchengin"></i> Log Tracker
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link fw-semibold px-3 {{ request()->routeIs('log-tracker.dashboard') ? 'active' : '' }}"
                       href="{{ route('log-tracker.dashboard') }}">
                        <i class="fas fa-chart-line me-1"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold px-3 {{ request()->routeIs('log-tracker.index') ? 'active' : '' }}"
                       href="{{ route('log-tracker.index') }}">
                        <i class="fas fa-list me-1"></i> Log Files
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="container my-4">
   @yield('content')
</div>

<!-- Footer -->
<footer class="bg-dark text-white text-center py-3 mt-5">
    <div class="container">
        <p class="mb-1">
           <span class="text-info"> <i class="fa-brands fa-searchengin"></i> <strong> LogTracker v1.0</strong></span>
        <p class="mb-0">
            © <span id="year"></span> <a href="https://github.com/KsSadi/Laravel-Log-Tracker" class="text-success"  target="_blank"> LogTracker</a> – Efficient logging, effortless insights.
        </p>
    </div>
</footer>


<!-- JavaScript to Auto-Update Year -->
<script>
    document.getElementById("year").textContent = new Date().getFullYear();
</script>




<!-- Bootstrap 5.3.3 JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>

<script>
    // Get log file name from URL parameter
    const urlParams = new URLSearchParams(window.location.search);
    const logId = urlParams.get('id');

    if (logId) {
        document.getElementById('logFileName').textContent = logId;
        document.getElementById('logFileBreadcrumb').textContent = logId;
        document.title = "Log Viewer - " + logId;
    }
</script>

@stack('scripts')
</body>
</html>
