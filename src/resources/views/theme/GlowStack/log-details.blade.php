@extends('log-tracker::theme.GlowStack.layouts.app')

@section('title', 'Log Details - Log Tracker')

@push('styles')
    <style>

        /* Modern Breadcrumb */
        .breadcrumb-container {
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(10px);
            border-radius: 50px;
            padding: 0.75rem 1.5rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(255,255,255,0.2);
            box-shadow: var(--shadow-soft);
        }

        .breadcrumb {
            margin-bottom: 0;
            background: none;
            padding: 0;
        }

        .breadcrumb-item a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }

        .breadcrumb-item a:hover {
            color: #4f46e5;
        }

        .breadcrumb-item.active {
            color: #2d3748;
            font-weight: 600;
        }

        /* Enhanced Header */
        .page-header {
            background: var(--primary-gradient);
            border-radius: var(--border-radius);
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-soft);
            color: white;
            position: relative;
            overflow: visible;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: -30%;
            right: -15%;
            width: 150px;
            height: 150px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .header-left {
            flex: 1;
            min-width: 300px;
        }

        .header-title {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .header-subtitle {
            opacity: 0.9;
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }

        .header-meta {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .header-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            align-items: center;
            overflow: visible;
            position: relative;
        }

        .action-btn {
            background: rgba(255,255,255,0.2);
            border: 2px solid rgba(255,255,255,0.3);
            color: white;
            padding: 0.6rem 1rem;
            border-radius: 25px;
            font-weight: 600;
            text-decoration: none;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.4rem;
            backdrop-filter: blur(10px);
            font-size: 0.9rem;
            white-space: nowrap;
            justify-content: center;
        }

        .action-btn:hover {
            background: rgba(255,255,255,0.3);
            border-color: rgba(255,255,255,0.5);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }

        .action-btn.success:hover {
            background: #10b981;
            border-color: #10b981;
        }

        .action-btn.warning:hover {
            background: #f59e0b;
            border-color: #f59e0b;
        }

        .action-btn.danger:hover {
            background: #dc3545;
            border-color: #dc3545;
        }

        /* Export Dropdown Styles */
        .export-dropdown {
            position: relative;
            display: inline-block;
            overflow: visible;
        }

        .export-menu {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(0, 0, 0, 0.1);
            min-width: 140px;
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(10px);
            max-height: 300px;
            overflow: visible;
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
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-bottom: none;
            border-right: none;
            transform: rotate(45deg);
            z-index: -1;
        }

        .export-menu a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: #374151;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            border-radius: 8px;
            margin: 0.25rem;
        }

        .export-menu a:first-child {
            margin-top: 0.5rem;
        }

        .export-menu a:last-child {
            margin-bottom: 0.5rem;
        }

        .export-menu a:hover {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            transform: translateX(4px);
        }

        .export-menu a i {
            width: 16px;
            font-size: 0.9rem;
            opacity: 0.8;
        }

        .export-menu a:hover i {
            opacity: 1;
        }

        /* Main Layout */
        .main-layout {
            display: flex;
            flex-direction: column;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        /* Enhanced Filters Section with Counts */
        .filters-section {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-soft);
            margin-bottom: 1.5rem;
        }

        .filters-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 12px 12px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .filters-title {
            font-weight: 600;
            color: white;
            margin: 0;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .total-entries {
            background: rgba(255,255,255,0.2);
            padding: 0.4rem 0.8rem;
            border-radius: 15px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .collapse-btn {
            background: rgba(255,255,255,0.2);
            border: 2px solid rgba(255,255,255,0.3);
            color: white;
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
            background: rgba(255,255,255,0.3);
            border-color: rgba(255,255,255,0.5);
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
            gap: 0.8rem;
            padding: 0.8rem 1.2rem;
            background: #f8fafc;
            border-radius: 25px;
            transition: var(--transition);
            cursor: pointer;
            border: 2px solid #e2e8f0;
            font-size: 0.9rem;
            min-width: 140px;
            justify-content: space-between;
        }

        .level-switch:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
            transform: translateY(-1px);
        }

        .level-switch.active {
            background: rgba(102, 126, 234, 0.1);
            border-color: rgba(102, 126, 234, 0.4);
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.2);
        }

        .level-info {
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .level-icon {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.7rem;
        }

        .level-name {
            font-weight: 600;
            color: #374151;
            font-size: 0.85rem;
        }

        .level-count {
            background: #667eea;
            color: white;
            padding: 0.2rem 0.6rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            min-width: 25px;
            text-align: center;
        }

        .switch-toggle {
            position: relative;
            width: 36px;
            height: 18px;
            background: #cbd5e1;
            border-radius: 18px;
            transition: var(--transition);
            cursor: pointer;
            margin-left: 0.5rem;
        }

        .switch-toggle.active {
            background: #667eea;
        }

        .switch-toggle::after {
            content: '';
            position: absolute;
            top: 2px;
            left: 2px;
            width: 14px;
            height: 14px;
            background: white;
            border-radius: 50%;
            transition: var(--transition);
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .switch-toggle.active::after {
            transform: translateX(18px);
        }

        /* Main Content */
        .content-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-soft);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            width: 100%;
        }

        .content-header {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            padding: 1.5rem 2rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .content-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #1f2937;
            margin: 0;
        }

        .content-badge {
            background: var(--success-gradient);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .search-bar {
            padding: 1.5rem 2rem;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            position: relative;
        }

        .search-bar-input {
            width: 100%;
            padding: 1rem 1.5rem 1rem 3rem;
            border: 2px solid #e5e7eb;
            border-radius: 50px;
            font-size: 1rem;
            transition: var(--transition);
            background: white;
        }

        .search-bar-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .search-bar .search-icon {
            position: absolute;
            left: 1.5rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 1.1rem;
            z-index: 10;
            pointer-events: none;
        }

        /* Log Table */
        .log-container {
            position: relative;
            max-height: 110vh;
            overflow: auto;
            background: var(--dark-gradient);
        }

        .log-table {
            width: 100%;
            border-collapse: collapse;
        }

        .log-table th {
            background: rgba(255, 255, 255, 0.08);
            color: #cbd5e1;
            font-weight: 600;
            padding: 1rem 1.5rem;
            text-align: left;
            position: sticky;
            top: 0;
            z-index: 10;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .log-row {
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            transition: var(--transition);
        }

        .log-row:hover {
            background: rgba(255, 255, 255, 0.02);
        }

        .log-row td {
            padding: 1.25rem 1.5rem;
            vertical-align: middle;
            color: #e2e8f0;
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
            letter-spacing: 0.5px;
            color: white;
            min-width: 80px;
            justify-content: center;
        }

        .timestamp {
            color: #94a3b8;
            font-family: 'JetBrains Mono', 'Fira Code', monospace;
            font-size: 0.85rem;
            white-space: nowrap;
        }

        .log-message {
            font-family: 'JetBrains Mono', 'Fira Code', monospace;
            font-size: 0.9rem;
            line-height: 1.5;
            word-break: break-word;
            color: #f1f5f9;
        }

        .stack-btn {
            background: var(--success-gradient);
            border: none;
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
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }

        .stack-trace-row {
            background: #1e293b !important;
        }

        .stack-trace-row td {
            padding: 0 !important;
        }

        .stack-trace {
            background: #0f172a;
            margin: 1rem;
            padding: 1.5rem;
            border-radius: 10px;
            font-family: 'JetBrains Mono', 'Fira Code', monospace;
            font-size: 0.8rem;
            line-height: 1.6;
            color: #cbd5e1;
            white-space: pre-wrap;
            word-break: break-word;
            overflow-x: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
            max-width: 100%;
        }

        .no-data-row {
            text-align: center;
            padding: 3rem 2rem;
            color: #64748b;
            font-style: italic;
        }

        .no-data-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .level-switches {
                gap: 0.75rem;
            }

            .level-switch {
                min-width: 120px;
                padding: 0.6rem 1rem;
            }
        }

        @media (max-width: 1024px) {
            .header-actions {
                gap: 0.4rem;
            }

            .action-btn {
                padding: 0.5rem 0.8rem;
                font-size: 0.8rem;
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

            .header-actions {
                justify-content: center;
                flex-wrap: wrap;
            }

            .action-btn {
                flex: 1;
                min-width: 80px;
                max-width: 120px;
            }

            .filters-body {
                padding: 1rem;
            }

            .level-switches {
                gap: 0.5rem;
            }

            .level-switch {
                min-width: 100px;
                padding: 0.5rem 0.8rem;
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
                border-radius: 25px;
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
                min-width: 50px;
                padding: 0.6rem;
            }

            .level-switches {
                flex-direction: column;
                align-items: stretch;
            }

            .level-switch {
                justify-content: space-between;
                min-width: auto;
            }

            /* Export menu responsive */
            .export-menu {
                right: -10px;
                min-width: 120px;
            }

            .export-menu::before {
                right: 20px;
            }

            .export-menu a {
                padding: 0.6rem 0.8rem;
                font-size: 0.8rem;
            }
        }

        /* Custom Scrollbar */
        .log-container::-webkit-scrollbar {
            width: 8px;
        }

        .log-container::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        .log-container::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 4px;
        }

        .log-container::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }

        /* Loading States */
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        .spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: #ffffff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Pagination Styles */
        .pagination-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: var(--border-radius);
            margin-top: 1rem;
            box-shadow: var(--shadow-soft);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .pagination-info {
            font-size: 0.9rem;
            color: #6b7280;
            font-weight: 500;
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
            color: #6b7280;
            text-decoration: none;
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(229, 231, 235, 0.8);
            border-radius: 0.375rem;
            transition: all 0.2s ease;
            min-width: 2.5rem;
            height: 2.5rem;
        }

        .pagination .page-link:hover {
            background: var(--primary-gradient);
            color: white;
            border-color: transparent;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }

        .pagination .page-item.active .page-link {
            background: var(--primary-gradient);
            color: white;
            border-color: transparent;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }

        .pagination .page-item.disabled .page-link {
            color: #d1d5db;
            background: rgba(249, 250, 251, 0.8);
            border-color: rgba(229, 231, 235, 0.5);
            cursor: not-allowed;
        }

        .pagination .page-item.disabled .page-link:hover {
            transform: none;
            box-shadow: none;
            background: rgba(249, 250, 251, 0.8);
            color: #d1d5db;
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
    <!-- Modern Breadcrumb -->
    <div class="breadcrumb-container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('log-tracker.dashboard')}}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{route('log-tracker.index')}}">Log Files</a></li>
                <li class="breadcrumb-item active">{{ $logName }}</li>
            </ol>
        </nav>
    </div>

    {{-- File Size Error Alert --}}
    @if(isset($error))
        <div class="alert alert-danger mb-4" style="background: linear-gradient(135deg, #fecaca 0%, #fca5a5 100%); border: 1px solid #f87171; border-radius: 12px; padding: 1.5rem;">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle me-3" style="font-size: 1.5rem; color: #dc2626;"></i>
                <div>
                    <h5 class="mb-1" style="color: #991b1b; font-weight: 600;">File Too Large</h5>
                    <p class="mb-2" style="color: #7f1d1d;">{{ $error }}</p>
                    @if(isset($file_size_mb) && isset($max_size_mb))
                        <small style="color: #7f1d1d;">
                            <strong>Current file size:</strong> {{ round($file_size_mb, 2) }} MB<br>
                            <strong>Maximum allowed:</strong> {{ $max_size_mb }} MB<br>
                            <strong>Tip:</strong> You can increase the limit in config/log-tracker.php
                        </small>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Enhanced Header -->
    <div class="page-header">
        <div class="header-content">
            <div class="header-left">
                <h1 class="header-title">
                    <i class="fas fa-file-code"></i>
                    {{ $logName }}
                </h1>
                <p class="header-subtitle">
                    Analyze and explore log entries with advanced filtering and export options
                </p>
                <div class="header-meta">
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
                </div>
            </div>

            <div class="header-actions">
                <a href="{{ url()->previous() }}" class="action-btn">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back</span>
                </a>

                <a href="{{ route('log-tracker.download', ['logName' => $logName]) }}" class="action-btn success">
                    <i class="fas fa-download"></i>
                    <span>Download</span>
                </a>

                <div class="export-dropdown">
                    <button class="action-btn warning" onclick="toggleExportMenu(event)">
                        <i class="fas fa-share-alt"></i>
                        <span>Export</span>
                        <i class="fas fa-chevron-down" style="margin-left: 0.25rem; font-size: 0.7rem;"></i>
                    </button>
                    <div class="export-menu" id="exportMenu">
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
                    </div>
                </div>

                <form action="{{ route('log-tracker.delete', ['logName' => $logName]) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="action-btn danger" onclick="return confirm('Are you sure you want to delete this log file?')">
                        <i class="fas fa-trash"></i>
                        <span>Delete</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Main Layout -->
    <div class="main-layout">
        <!-- Enhanced Filters Section with Counts -->
        <div class="filters-section">
            <div class="filters-header">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <h5 class="filters-title">
                        <i class="fas fa-filter"></i>
                        Smart Filters
                    </h5>
                    <div class="total-entries">
                        {{ number_format(count($entries)) }} Total Entries
                    </div>
                </div>
                <button class="collapse-btn" onclick="toggleFilters()">
                    <i class="fas fa-chevron-up" id="collapseIcon"></i>
                </button>
            </div>
            <div class="filters-body" id="filtersBody">
                <div class="filters-content">
                    <div class="level-switches">
                        @foreach($logLevels as $level => $config)
                            <div class="level-switch active log-filter" data-level="{{ $level }}">
                                <div class="level-info">
                                    <div class="level-icon" style="background-color: {{ $config['color'] }};">
                                        <i class="{{ $config['icon'] }}"></i>
                                    </div>
                                    <span class="level-name">{{ ucfirst($level) }}</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <div class="level-count" style="background-color: {{ $config['color'] }};">
                                        {{ $counts[$level] ?? 0 }}
                                    </div>
                                    <div class="switch-toggle active"></div>
                                </div>
                                <input type="checkbox" class="d-none level-checkbox" value="{{ $level }}" checked>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="content-card">
            <div class="content-header">
                <h5 class="content-title">Log Entries</h5>
                <div class="content-badge" id="entriesCount">{{ number_format(count($entries)) }} entries</div>
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
                    @foreach ($entries as $index => $log)
                        <tr class="log-row" data-level="{{ strtolower($log['level']) }}" data-index="{{ $index }}">
                            <td>
                                <div class="log-level-badge" style="background-color: {{ $log['color'] }};">
                                    <i class="{{ $log['icon'] }}"></i>
                                    {{ strtoupper($log['level']) }}
                                </div>
                            </td>
                            <td>
                                <div class="timestamp">{{ $log['timestamp'] }}</div>
                            </td>
                            <td>
                                <div class="log-message">{{ $log['message'] }}</div>
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
                    @endforeach

                    <!-- No Data Row -->
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
            noDataRow.style.display = (visibleCount === 0) ? '' : 'none';
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
                // Not enough space on the right, align to left edge of button
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
                    // Also close export menu on Escape
                    const exportMenu = document.getElementById('exportMenu');
                    if (exportMenu) {
                        exportMenu.classList.remove('show');
                    }
                }
            });
        });
    </script>
@endpush

