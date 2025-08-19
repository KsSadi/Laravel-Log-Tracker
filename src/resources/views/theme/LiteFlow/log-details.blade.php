@extends('log-tracker::theme.LiteFlow.layouts.app')

@section('title', 'Log Details - Log Tracker')

@push('styles')
    <style>

        /* Modern Breadcrumb */
        /* Modern Breadcrumb */
        .breadcrumb-container {
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: var(--border-radius);
            padding: 0.75rem 1.5rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-sm);
        }

        .breadcrumb {
            margin-bottom: 0;
            background: none;
            padding: 0;
        }

        .breadcrumb-item a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }

        .breadcrumb-item a:hover {
            color: #1d4ed8;
        }

        .breadcrumb-item.active {
            color: var(--gray-700);
            font-weight: 600;
        }

        /* Enhanced Header */
        .page-header {
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-sm);
            position: relative;
            overflow: visible;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color) 0%, var(--info-color) 100%);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 1.5rem;
        }

        .header-left {
            flex: 1;
            min-width: 300px;
        }

        .header-title {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            color: var(--gray-900);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .header-title i {
            color: var(--primary-color);
        }

        .header-subtitle {
            color: var(--gray-600);
            font-size: 1rem;
            margin-bottom: 1rem;
            line-height: 1.5;
        }

        .header-meta {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            font-size: 0.875rem;
            color: var(--gray-600);
            flex-wrap: wrap;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--gray-100);
            padding: 0.5rem 1rem;
            border-radius: var(--border-radius);
            border: 1px solid var(--gray-200);
        }

        .meta-item i {
            color: var(--primary-color);
        }

        .header-actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            align-items: flex-start;
            position: relative;
            overflow: visible;
            z-index: 2;
        }

        .action-btn {
            background: white;
            border: 1px solid var(--gray-300);
            color: var(--gray-700);
            padding: 0.75rem 1.25rem;
            border-radius: var(--border-radius);
            font-weight: 600;
            text-decoration: none;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            white-space: nowrap;
            cursor: pointer;
        }

        .action-btn:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow);
            text-decoration: none;
        }

        .action-btn.success {
            background-color: var(--success-color);
            border-color: var(--success-color);
            color: white;
        }

        .action-btn.success:hover {
            background-color: #047857;
            color: white;
        }

        .action-btn.warning {
            background-color: var(--warning-color);
            border-color: var(--warning-color);
            color: white;
        }

        .action-btn.warning:hover {
            background-color: #b45309;
            color: white;
        }

        .action-btn.danger {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
            color: white;
        }

        .action-btn.danger:hover {
            background-color: #b91c1c;
            color: white;
        }

        /* Export Dropdown */
        .export-dropdown {
            position: relative;
            display: inline-block;
        }

        .export-menu {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-md);
            min-width: 160px;
            z-index: 1050;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: var(--transition);
        }

        .export-menu.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .export-menu::before {
            content: '';
            position: absolute;
            top: -6px;
            right: 16px;
            width: 12px;
            height: 12px;
            background: white;
            border-left: 1px solid var(--gray-200);
            border-top: 1px solid var(--gray-200);
            transform: rotate(45deg);
        }

        .export-menu a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: var(--gray-700);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.875rem;
            transition: var(--transition);
        }

        .export-menu a:hover {
            background-color: var(--gray-50);
            color: var(--gray-900);
        }

        .export-menu a i {
            width: 16px;
            font-size: 0.875rem;
            color: var(--gray-500);
        }

        /* Filters Section */
        .filters-section {
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: 12px;
            box-shadow: var(--shadow-sm);
            margin-bottom: 2rem;
        }

        .filters-header {
            background-color: var(--gray-50);
            color: var(--gray-900);
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--gray-200);
            border-radius: 12px 12px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .filters-title {
            font-weight: 600;
            margin: 0;
            font-size: 1.125rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .filters-title i {
            color: var(--primary-color);
        }

        .total-entries {
            background-color: var(--primary-color);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .collapse-btn {
            background: white;
            border: 1px solid var(--gray-300);
            color: var(--gray-600);
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
        }

        .collapse-btn:hover {
            background-color: var(--gray-100);
            color: var(--gray-700);
        }

        .filters-body {
            padding: 1.5rem;
        }

        .filters-content {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .level-switches {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            justify-content: center;
        }

        .level-switch {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.5rem;
            background: var(--gray-50);
            border: 1px solid var(--gray-200);
            border-radius: var(--border-radius);
            transition: var(--transition);
            cursor: pointer;
            font-size: 0.875rem;
            min-width: 160px;
            justify-content: space-between;
        }

        .level-switch:hover {
            background-color: var(--gray-100);
            border-color: var(--gray-300);
            transform: translateY(-1px);
        }

        .level-switch.active {
            background-color: #dbeafe;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .level-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .level-icon {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.75rem;
        }

        .level-name {
            font-weight: 600;
            color: var(--gray-700);
        }

        .level-count {
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            min-width: 30px;
            text-align: center;
        }

        .switch-toggle {
            position: relative;
            width: 40px;
            height: 20px;
            background-color: var(--gray-300);
            border-radius: 20px;
            transition: var(--transition);
            cursor: pointer;
        }

        .switch-toggle.active {
            background-color: var(--primary-color);
        }

        .switch-toggle::after {
            content: '';
            position: absolute;
            top: 2px;
            left: 2px;
            width: 16px;
            height: 16px;
            background: white;
            border-radius: 50%;
            transition: var(--transition);
            box-shadow: var(--shadow-sm);
        }

        .switch-toggle.active::after {
            transform: translateX(20px);
        }

        /* Content Card */
        .content-card {
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: 12px;
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }

        .content-header {
            background-color: var(--gray-50);
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .content-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--gray-900);
            margin: 0;
        }

        .content-badge {
            background-color: var(--primary-color);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .search-bar {
            padding: 1.5rem;
            background-color: var(--gray-50);
            border-bottom: 1px solid var(--gray-200);
        }

        .search-bar-input {
            width: 100%;
            padding: 1rem 1.5rem 1rem 3rem;
            border: 1px solid var(--gray-300);
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: var(--transition);
            background: white;
        }

        .search-bar-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .search-bar .search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-400);
            font-size: 1.1rem;
            pointer-events: none;
        }

        /* Log Table */
        .log-container {
            position: relative;
            max-height: 70vh;
            overflow: auto;
            background: var(--gray-900);
        }

        .log-table {
            width: 100%;
            border-collapse: collapse;
        }

        .log-table th {
            background-color: var(--gray-800);
            color: var(--gray-200);
            font-weight: 600;
            padding: 1rem 1.5rem;
            text-align: left;
            position: sticky;
            top: 0;
            z-index: 10;
            border-bottom: 1px solid var(--gray-700);
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .log-row {
            border-bottom: 1px solid var(--gray-700);
            transition: var(--transition);
        }

        .log-row:hover {
            background-color: var(--gray-800);
        }

        .log-row td {
            padding: 1rem 1.5rem;
            vertical-align: middle;
            color: var(--gray-200);
        }

        .log-level-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: white;
            min-width: 90px;
            justify-content: center;
        }

        .timestamp {
            color: var(--gray-400);
            font-family: 'SF Mono', Monaco, 'Cascadia Code', 'Roboto Mono', Consolas, 'Courier New', monospace;
            font-size: 0.875rem;
            white-space: nowrap;
        }

        .log-message {
            font-family: 'SF Mono', Monaco, 'Cascadia Code', 'Roboto Mono', Consolas, 'Courier New', monospace;
            font-size: 0.875rem;
            line-height: 1.5;
            word-break: break-word;
            color: var(--gray-100);
        }

        .stack-btn {
            background-color: var(--primary-color);
            border: 1px solid var(--primary-color);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .stack-btn:hover {
            background-color: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: var(--shadow);
        }

        .stack-trace-row {
            background-color: var(--gray-800) !important;
        }

        .stack-trace-row td {
            padding: 0 !important;
        }

        .stack-trace {
            background-color: var(--gray-900);
            margin: 1rem;
            padding: 1.5rem;
            border-radius: var(--border-radius);
            font-family: 'SF Mono', Monaco, 'Cascadia Code', 'Roboto Mono', Consolas, 'Courier New', monospace;
            font-size: 0.8rem;
            line-height: 1.6;
            color: var(--gray-300);
            white-space: pre-wrap;
            word-break: break-word;
            overflow-x: auto;
            border: 1px solid var(--gray-700);
        }

        .no-data-row {
            text-align: center;
            padding: 3rem 2rem;
            color: var(--gray-500);
        }

        .no-data-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: var(--gray-400);
        }

        .no-data-row h4 {
            color: var(--gray-700);
            margin-bottom: 0.5rem;
        }

        .no-data-row p {
            color: var(--gray-500);
            margin: 0;
        }

        /* Footer */
        .footer {
            background-color: white;
            border-top: 1px solid var(--gray-200);
            margin-top: 3rem;
            padding: 1.5rem 0;
        }

        .footer-content {
            text-align: center;
            color: var(--gray-600);
            font-size: 0.875rem;
        }

        .footer-brand {
            color: var(--primary-color);
            font-weight: 600;
            text-decoration: none;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .header-actions {
                gap: 0.5rem;
            }

            .action-btn {
                padding: 0.6rem 1rem;
                font-size: 0.8rem;
            }

            .level-switches {
                gap: 0.75rem;
            }

            .level-switch {
                min-width: 140px;
                padding: 0.75rem 1rem;
            }
        }

        @media (max-width: 768px) {
            .page-header {
                padding: 1.5rem;
            }

            .header-content {
                flex-direction: column;
                align-items: stretch;
                gap: 1.5rem;
            }

            .header-left {
                min-width: auto;
            }

            .header-title {
                font-size: 1.5rem;
            }

            .header-meta {
                justify-content: flex-start;
                gap: 1rem;
            }

            .header-actions {
                justify-content: center;
                flex-wrap: wrap;
            }

            .filters-body {
                padding: 1rem;
            }

            .level-switches {
                gap: 0.5rem;
            }

            .level-switch {
                min-width: 120px;
                padding: 0.75rem;
                font-size: 0.8rem;
            }

            .log-table th,
            .log-row td {
                padding: 0.75rem;
            }

            .log-table th:nth-child(2),
            .log-row td:nth-child(2) {
                display: none;
            }

            .log-message {
                font-size: 0.8rem;
            }
        }

        @media (max-width: 480px) {
            .breadcrumb-container {
                padding: 0.5rem 1rem;
            }

            .page-header {
                padding: 1rem;
            }

            .header-meta {
                flex-direction: column;
                gap: 0.5rem;
                align-items: flex-start;
            }

            .action-btn span {
                display: none;
            }

            .action-btn {
                min-width: 44px;
                padding: 0.75rem;
                justify-content: center;
            }

            .level-switches {
                flex-direction: column;
                align-items: stretch;
            }

            .level-switch {
                min-width: auto;
                justify-content: space-between;
            }

            .export-menu {
                right: -10px;
                min-width: 120px;
            }

            .export-menu::before {
                right: 20px;
            }
        }

        /* Custom Scrollbar */
        .log-container::-webkit-scrollbar {
            width: 8px;
        }

        .log-container::-webkit-scrollbar-track {
            background: var(--gray-800);
        }

        .log-container::-webkit-scrollbar-thumb {
            background: var(--gray-600);
            border-radius: 4px;
        }

        .log-container::-webkit-scrollbar-thumb:hover {
            background: var(--gray-500);
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .page-content {
            animation: fadeIn 0.3s ease-out;
        }

        /* Pagination Styles */
        .pagination-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem;
            background: white;
            border-radius: var(--border-radius);
            margin-top: 1.5rem;
            box-shadow: var(--shadow);
            border: 1px solid var(--gray-200);
        }

        .pagination-info {
            font-size: 0.875rem;
            color: var(--gray-600);
            font-weight: 500;
            background: var(--gray-100);
            padding: 0.5rem 1rem;
            border-radius: var(--border-radius);
        }

        .pagination-nav .pagination {
            margin: 0;
            padding: 0;
            display: flex;
            list-style: none;
            gap: 0.25rem;
        }

        .pagination .page-item {
            display: flex;
        }

        .pagination .page-link {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--gray-700);
            text-decoration: none;
            background: white;
            border: 1px solid var(--gray-300);
            border-radius: var(--border-radius);
            transition: all 0.2s ease;
            min-width: 2.5rem;
            height: 2.5rem;
        }

        .pagination .page-link:hover {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .pagination .page-item.active .page-link {
            background: var(--primary-color) !important;
            color: white !important;
            border-color: var(--primary-color) !important;
            font-weight: 700 !important;
            box-shadow: 0 4px 14px 0 rgba(37, 99, 235, 0.4) !important;
            transform: translateY(-2px);
        }

        .pagination .page-item.active .page-link:hover {
            background: #1d4ed8 !important;
            border-color: #1d4ed8 !important;
            transform: translateY(-2px);
        }

        .pagination .page-item.disabled .page-link {
            color: var(--gray-400);
            background: var(--gray-100);
            border-color: var(--gray-200);
            cursor: not-allowed;
        }

        .pagination .page-item.disabled .page-link:hover {
            transform: none;
            box-shadow: none;
            background: var(--gray-100);
            color: var(--gray-400);
            border-color: var(--gray-200);
        }

        @media (max-width: 768px) {
            .pagination-container {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .pagination .page-link {
                padding: 0.375rem 0.5rem;
                min-width: 2rem;
                height: 2rem;
                font-size: 0.75rem;
            }
        }
    </style>
@endpush

@section('content')
    <!-- Main Content -->
    <div class="container my-4 page-content">
        <!-- Modern Breadcrumb -->
        <div class="breadcrumb-container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    @if(Route::has('log-tracker.dashboard'))
                        <li class="breadcrumb-item"><a href="{{ route('log-tracker.dashboard') }}">Dashboard</a></li>
                    @else
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    @endif
                    @if(Route::has('log-tracker.index'))
                        <li class="breadcrumb-item"><a href="{{ route('log-tracker.index') }}">Log Files</a></li>
                    @else
                        <li class="breadcrumb-item"><a href="#">Log Files</a></li>
                    @endif
                    <li class="breadcrumb-item active">{{ $logName ?? 'Log Details' }}</li>
                </ol>
            </nav>
        </div>

        <!-- Enhanced Header -->
        <div class="page-header">
            <div class="header-content">
                <div class="header-left">
                    <h1 class="header-title">
                        <i class="fas fa-file-code"></i>
                        {{ $logName ?? 'Log File' }}
                    </h1>
                    <p class="header-subtitle">
                        Analyze and explore log entries with advanced filtering and export options
                    </p>
                    <div class="header-meta">
                        @if(isset($logName) && file_exists(storage_path('logs/' . $logName)))
                            <div class="meta-item">
                                <i class="fas fa-hdd"></i>
                                <span>{{ round(filesize(storage_path('logs/' . $logName)) / 1024, 2) }} KB</span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-calendar"></i>
                                <span>{{ date('M d, Y', filemtime(storage_path('logs/' . $logName))) }}</span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-clock"></i>
                                <span>{{ date('h:i A', filemtime(storage_path('logs/' . $logName))) }}</span>
                            </div>
                        @else
                            <div class="meta-item">
                                <i class="fas fa-info-circle"></i>
                                <span>File information unavailable</span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="header-actions">
                    <a href="{{ url()->previous() }}" class="action-btn">
                        <i class="fas fa-arrow-left"></i>
                        <span>Back</span>
                    </a>

                    @if(Route::has('log-tracker.download') && isset($logName))
                        <a href="{{ route('log-tracker.download', ['logName' => $logName]) }}" class="action-btn success">
                            <i class="fas fa-download"></i>
                            <span>Download</span>
                        </a>
                    @else
                        <a href="#" class="action-btn success">
                            <i class="fas fa-download"></i>
                            <span>Download</span>
                        </a>
                    @endif

                    <div class="export-dropdown">
                        <button class="action-btn warning" onclick="toggleExportMenu(event)">
                            <i class="fas fa-share-alt"></i>
                            <span>Export</span>
                            <i class="fas fa-chevron-down" style="margin-left: 0.25rem; font-size: 0.7rem;"></i>
                        </button>
                        <div class="export-menu" id="exportMenu">
                            @if(Route::has('log-tracker.export.quick') && isset($logName))
                                <a href="{{ route('log-tracker.export.quick', [$logName, 'csv']) }}">
                                    <i class="fas fa-table"></i>CSV
                                </a>
                                <a href="{{ route('log-tracker.export.quick', [$logName, 'json']) }}">
                                    <i class="fas fa-code"></i>JSON
                                </a>
                                <a href="{{ route('log-tracker.export.quick', [$logName, 'excel']) }}">
                                    <i class="fas fa-file-excel"></i>Excel
                                </a>
                                <a href="{{ route('log-tracker.export.quick', [$logName, 'pdf']) }}">
                                    <i class="fas fa-file-pdf"></i>PDF
                                </a>
                            @else
                                <a href="#">
                                    <i class="fas fa-table"></i>CSV
                                </a>
                                <a href="#">
                                    <i class="fas fa-code"></i>JSON
                                </a>
                                <a href="#">
                                    <i class="fas fa-file-excel"></i>Excel
                                </a>
                                <a href="#">
                                    <i class="fas fa-file-pdf"></i>PDF
                                </a>
                            @endif
                        </div>
                    </div>

                    @if(Route::has('log-tracker.delete') && config('log-tracker.allow_delete', false) && isset($logName))
                        <form action="{{ route('log-tracker.delete', ['logName' => $logName]) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="action-btn danger" onclick="return confirm('Are you sure you want to delete this log file?')">
                                <i class="fas fa-trash"></i>
                                <span>Delete</span>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="filters-section">
            <div class="filters-header">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <h5 class="filters-title">
                        <i class="fas fa-filter"></i>
                        Smart Filters
                    </h5>
                    <div class="total-entries">
                        {{ number_format(count($entries ?? [])) }} Total Entries
                    </div>
                </div>
                <button class="collapse-btn" onclick="toggleFilters()">
                    <i class="fas fa-chevron-up" id="collapseIcon"></i>
                </button>
            </div>
            <div class="filters-body" id="filtersBody">
                <div class="filters-content">
                    <div class="level-switches">
                        @forelse($logLevels ?? [] as $level => $config)
                            <div class="level-switch active log-filter" data-level="{{ $level }}">
                                <div class="level-info">
                                    <div class="level-icon" style="background-color: {{ $config['color'] ?? '#6b7280' }};">
                                        <i class="{{ $config['icon'] ?? 'fas fa-circle' }}"></i>
                                    </div>
                                    <span class="level-name">{{ ucfirst($level) }}</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <div class="level-count" style="background-color: {{ $config['color'] ?? '#6b7280' }};">
                                        {{ isset($counts[$level]) ? $counts[$level] : 0 }}
                                    </div>
                                    <div class="switch-toggle active"></div>
                                </div>
                                <input type="checkbox" class="d-none level-checkbox" value="{{ $level }}" checked>
                            </div>
                        @empty
                            <div class="level-switch active log-filter" data-level="error">
                                <div class="level-info">
                                    <div class="level-icon" style="background-color: #dc2626;">
                                        <i class="fas fa-exclamation-circle"></i>
                                    </div>
                                    <span class="level-name">Error</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <div class="level-count" style="background-color: #dc2626;">0</div>
                                    <div class="switch-toggle active"></div>
                                </div>
                                <input type="checkbox" class="d-none level-checkbox" value="error" checked>
                            </div>
                            <div class="level-switch active log-filter" data-level="warning">
                                <div class="level-info">
                                    <div class="level-icon" style="background-color: #d97706;">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </div>
                                    <span class="level-name">Warning</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <div class="level-count" style="background-color: #d97706;">0</div>
                                    <div class="switch-toggle active"></div>
                                </div>
                                <input type="checkbox" class="d-none level-checkbox" value="warning" checked>
                            </div>
                            <div class="level-switch active log-filter" data-level="info">
                                <div class="level-info">
                                    <div class="level-icon" style="background-color: #0284c7;">
                                        <i class="fas fa-info-circle"></i>
                                    </div>
                                    <span class="level-name">Info</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <div class="level-count" style="background-color: #0284c7;">0</div>
                                    <div class="switch-toggle active"></div>
                                </div>
                                <input type="checkbox" class="d-none level-checkbox" value="info" checked>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="content-card">
            <div class="content-header">
                <h5 class="content-title">Log Entries</h5>
                <div class="content-badge" id="entriesCount">{{ number_format(count($entries ?? [])) }} entries</div>
            </div>

            <div class="search-bar">
                <div style="position: relative;">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="search-bar-input log-search" placeholder="Search through log messages, timestamps, and more..." id="mainSearchInput">
                </div>
            </div>

            <div class="log-container">
                <table class="log-table">
                    <thead>
                    <tr>
                        <th style="width: 120px;">Level</th>
                        <th style="width: 180px;">Timestamp</th>
                        <th>Message</th>
                        <th style="width: 100px;">Actions</th>
                    </tr>
                    </thead>
                    <tbody id="logTableBody">
                    @forelse($entries ?? [] as $index => $log)
                        <tr class="log-row" data-level="{{ strtolower($log['level'] ?? 'info') }}" data-index="{{ $index }}">
                            <td>
                                <div class="log-level-badge" style="background-color: {{ $log['color'] ?? '#6b7280' }};">
                                    <i class="{{ $log['icon'] ?? 'fas fa-circle' }}"></i>
                                    {{ strtoupper($log['level'] ?? 'INFO') }}
                                </div>
                            </td>
                            <td>
                                <div class="timestamp">{{ $log['timestamp'] ?? 'N/A' }}</div>
                            </td>
                            <td>
                                <div class="log-message">{{ $log['message'] ?? 'No message' }}</div>
                            </td>
                            <td>
                                @if (!empty($log['stack']))
                                    <button class="stack-btn" onclick="toggleStackTrace({{ $index }})">
                                        <i class="fas fa-code"></i>
                                        <span>Stack</span>
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @if (!empty($log['stack']))
                            <tr id="stacktrace-{{ $index }}" class="stack-trace-row d-none">
                                <td colspan="4">
                                    <pre class="stack-trace">{{ $log['stack'] }}</pre>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr class="no-data-row">
                            <td colspan="4">
                                <div class="no-data-icon">
                                    <i class="fas fa-inbox"></i>
                                </div>
                                <h4>No Log Entries Found</h4>
                                <p>This log file appears to be empty or has no readable entries.</p>
                            </td>
                        </tr>
                    @endforelse

                    <!-- No Data Row for Search Results -->
                    <tr id="noDataRow" class="no-data-row" style="display: none;">
                        <td colspan="4">
                            <div class="no-data-icon">
                                <i class="fas fa-search-minus"></i>
                            </div>
                            <h4>No matching logs found</h4>
                            <p>Try adjusting your search criteria or filters</p>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination Controls -->
            @if(isset($pagination) && $pagination['last_page'] > 1)
            <div class="pagination-container">
                <div class="pagination-info">
                    <span>Showing {{ $pagination['from'] }} to {{ $pagination['to'] }} of {{ number_format($pagination['total']) }} entries</span>
                </div>
                
                <nav class="pagination-nav">
                    <ul class="pagination">
                        {{-- Previous Page Link --}}
                        @if($pagination['current_page'] > 1)
                            <li class="page-item">
                                <a class="page-link" href="{{ route('log-tracker.show', ['logName' => $logName, 'page' => $pagination['current_page'] - 1]) }}">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                        @else
                            <li class="page-item disabled">
                                <span class="page-link">
                                    <i class="fas fa-chevron-left"></i>
                                </span>
                            </li>
                        @endif

                        {{-- Pagination Elements --}}
                        @php
                            $start = max(1, $pagination['current_page'] - 2);
                            $end = min($pagination['last_page'], $pagination['current_page'] + 2);
                        @endphp

                        @if($start > 1)
                            <li class="page-item">
                                <a class="page-link" href="{{ route('log-tracker.show', ['logName' => $logName, 'page' => 1]) }}">1</a>
                            </li>
                            @if($start > 2)
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            @endif
                        @endif

                        @for($i = $start; $i <= $end; $i++)
                            @if($i == $pagination['current_page'])
                                <li class="page-item active">
                                    <span class="page-link">{{ $i }}</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ route('log-tracker.show', ['logName' => $logName, 'page' => $i]) }}">{{ $i }}</a>
                                </li>
                            @endif
                        @endfor

                        @if($end < $pagination['last_page'])
                            @if($end < $pagination['last_page'] - 1)
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            @endif
                            <li class="page-item">
                                <a class="page-link" href="{{ route('log-tracker.show', ['logName' => $logName, 'page' => $pagination['last_page']]) }}">{{ $pagination['last_page'] }}</a>
                            </li>
                        @endif

                        {{-- Next Page Link --}}
                        @if($pagination['current_page'] < $pagination['last_page'])
                            <li class="page-item">
                                <a class="page-link" href="{{ route('log-tracker.show', ['logName' => $logName, 'page' => $pagination['current_page'] + 1]) }}">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        @else
                            <li class="page-item disabled">
                                <span class="page-link">
                                    <i class="fas fa-chevron-right"></i>
                                </span>
                            </li>
                        @endif
                    </ul>
                </nav>
            </div>
            @endif
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        // Simple and working filter function
        function filterLogs() {
            const activeLevels = Array.from(document.querySelectorAll('.level-checkbox:checked')).map(cb => cb.value);
            const searchTerm = document.getElementById('mainSearchInput').value.toLowerCase();
            const logRows = document.querySelectorAll('.log-row');
            let visibleCount = 0;

            logRows.forEach(row => {
                const level = row.getAttribute('data-level');
                const text = row.textContent.toLowerCase();
                let showRow = activeLevels.includes(level);

                if (searchTerm && showRow) {
                    showRow = text.includes(searchTerm);
                }

                const stackRow = document.getElementById(`stacktrace-${row.dataset.index}`);

                if (showRow) {
                    row.style.display = '';
                    visibleCount++;

                    if (stackRow) {
                        if (!stackRow.classList.contains('d-none')) {
                            stackRow.style.display = '';
                        } else {
                            stackRow.style.display = 'none';
                        }
                    }
                } else {
                    row.style.display = 'none';
                    if (stackRow) {
                        stackRow.style.display = 'none';
                    }
                }
            });

            const badge = document.getElementById('entriesCount');
            badge.textContent = `${visibleCount.toLocaleString()} ${visibleCount === 1 ? 'entry' : 'entries'}`;

            const noDataRow = document.getElementById('noDataRow');
            const originalNoData = document.querySelector('.no-data-row:not(#noDataRow)');

            // Only show no data row if we have entries to filter but none are visible
            if (logRows.length > 0 && visibleCount === 0) {
                noDataRow.style.display = '';
                if (originalNoData) originalNoData.style.display = 'none';
            } else {
                noDataRow.style.display = 'none';
                if (originalNoData && logRows.length === 0) {
                    originalNoData.style.display = '';
                } else if (originalNoData) {
                    originalNoData.style.display = 'none';
                }
            }
        }

        function toggleLevel(levelSwitch) {
            const checkbox = levelSwitch.querySelector('.level-checkbox');
            const toggle = levelSwitch.querySelector('.switch-toggle');

            checkbox.checked = !checkbox.checked;

            if (checkbox.checked) {
                levelSwitch.classList.add('active');
                toggle.classList.add('active');
            } else {
                levelSwitch.classList.remove('active');
                toggle.classList.remove('active');
            }

            filterLogs();
        }

        function toggleStackTrace(index) {
            const stackRow = document.getElementById(`stacktrace-${index}`);
            const mainRow = document.querySelector(`[data-index="${index}"]`);

            if (!stackRow || !mainRow || mainRow.style.display === 'none') {
                return;
            }

            stackRow.classList.toggle('d-none');

            if (stackRow.classList.contains('d-none')) {
                stackRow.style.display = 'none';
            } else {
                stackRow.style.display = '';
            }
        }

        function toggleFilters() {
            const body = document.getElementById('filtersBody');
            const icon = document.getElementById('collapseIcon');

            if (body.style.display === 'none') {
                body.style.display = 'block';
                icon.className = 'fas fa-chevron-up';
            } else {
                body.style.display = 'none';
                icon.className = 'fas fa-chevron-down';
            }
        }

        function toggleExportMenu(event) {
            event.stopPropagation();
            const menu = document.getElementById('exportMenu');
            const button = event.currentTarget;

            if (menu.classList.contains('show')) {
                menu.classList.remove('show');
                return;
            }

            // Position the menu
            const rect = button.getBoundingClientRect();
            const menuWidth = 140;
            const viewportWidth = window.innerWidth;

            // Reset positioning
            menu.style.right = '0';
            menu.style.left = 'auto';

            // Check if menu would go off screen
            if (rect.right < menuWidth) {
                menu.style.right = 'auto';
                menu.style.left = '0';
            }

            menu.classList.add('show');
        }

        // Close export menu when clicking outside
        document.addEventListener('click', function(event) {
            const exportDropdown = document.querySelector('.export-dropdown');
            const exportMenu = document.getElementById('exportMenu');

            if (exportMenu && !exportDropdown.contains(event.target)) {
                exportMenu.classList.remove('show');
            }
        });

        // Initialize when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.level-switch').forEach(levelSwitch => {
                levelSwitch.addEventListener('click', function() {
                    toggleLevel(this);
                });
            });

            const mainSearchInput = document.getElementById('mainSearchInput');
            mainSearchInput.addEventListener('input', function() {
                filterLogs();
            });

            document.getElementById('noDataRow').style.display = 'none';

            document.addEventListener('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                    e.preventDefault();
                    document.getElementById('mainSearchInput').focus();
                }

                if (e.key === 'Escape') {
                    mainSearchInput.value = '';
                    filterLogs();
                    const exportMenu = document.getElementById('exportMenu');
                    if (exportMenu) {
                        exportMenu.classList.remove('show');
                    }
                }
            });
        });
    </script>
@endpush
