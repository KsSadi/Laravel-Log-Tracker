@extends('log-tracker::theme.GlowStack.layouts.app')

@section('title', 'Log List - Log Tracker')

@push('styles')
    <style>


        /* Enhanced Filter Section */
        .filters-section {
            background: white;
            border-radius: var(--border-radius);
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-soft);
            border: 0;
        }

        .filter-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f0f0f0;
        }

        .filter-title {
            display: flex;
            align-items: center;
            font-weight: 600;
            color: #2d3748;
            font-size: 1.2rem;
        }

        .filter-title i {
            margin-right: 0.5rem;
            color: #667eea;
        }

        .filter-controls {
            display: grid;
            grid-template-columns: 2fr 1fr auto;
            gap: 1rem;
            align-items: end;
        }

        .search-box {
            position: relative;
        }

        .search-box i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            z-index: 2;
        }

        .search-input {
            padding: 0.75rem 1rem 0.75rem 2.5rem;
            border: 2px solid #e5e7eb;
            border-radius: 50px;
            font-size: 0.95rem;
            transition: var(--transition);
            width: 100%;
            background: #f9fafb;
        }

        .search-input:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .date-filter {
            padding: 0.75rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 50px;
            font-size: 0.95rem;
            transition: var(--transition);
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg width='12' height='8' viewBox='0 0 12 8' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1L6 6L11 1' stroke='%23000' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 12px;
        }

        .date-filter:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .apply-btn {
            background: var(--success-gradient);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            white-space: nowrap;
        }

        .apply-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }

        /* Stats Overview */
        .stats-overview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            text-align: center;
            box-shadow: var(--shadow-soft);
            transition: var(--transition);
            border: 1px solid rgba(255,255,255,0.8);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-label {
            color: #6B7280;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.8rem;
        }

        /* Modern Log Cards */
        .logs-container {
            background: white;
            border-radius: var(--border-radius);
            padding: 0;
            box-shadow: var(--shadow-soft);
            overflow: visible;
        }

        .logs-header {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            padding: 1.0rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e2e8f0;
        }

        .logs-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #1f2937;
        }

        .file-count-badge {
            background: var(--success-gradient);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .logs-grid {
            display: grid;
            gap: 0;
            padding-bottom: 100px;

        }

        .logs-header-row {
            display: grid;
            grid-template-columns: 2fr repeat(4, 1fr) 1fr 1.5fr;
            align-items: center;
            padding: 1rem 2rem;
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            border-bottom: 2px solid #e2e8f0;
            font-weight: 600;
            color: #475569;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .header-cell {
            text-align: center;
        }

        .header-cell:first-child {
            text-align: left;
        }

        .header-cell:last-child {
            text-align: center;
        }

        .log-card {
            display: grid;
            grid-template-columns: 2fr repeat(4, 1fr) 1fr 1.5fr;
            align-items: center;
            padding: 1.5rem 2rem;
            border-bottom: 1px solid #f1f5f9;
            transition: var(--transition);
            cursor: pointer;
            overflow: visible;
            position: relative;
            z-index: 1;
        }

        .log-card:hover {
            background: linear-gradient(135deg, #f0f4ff 0%, #e0f2fe 100%);
            transform: translateX(5px);
        }

        .log-card:last-child {
            border-bottom: none;
        }
        /* While open, the card should float above others */
        .log-card.active-dropdown {
            z-index: 1001;
        }

        /* File error styling */
        .log-card.file-error {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border-left: 4px solid #dc2626;
        }

        .log-card.file-error:hover {
            background: linear-gradient(135deg, #fecaca 0%, #fca5a5 100%);
        }

        /* Add bottom spacing to avoid collision when dropdown expands */
        .logs-grid {
            padding-bottom: 150px;
        }

        .log-info {
            display: flex;
            flex-direction: column;
        }

        .log-filename {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 0.25rem;
            font-size: 1rem;
        }

        .log-date {
            color: #6b7280;
            font-size: 0.85rem;
        }

        .log-stat {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.25rem;
        }

        .stat-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 45px;
            height: 30px;
            border-radius: 15px;
            font-weight: 600;
            font-size: 0.8rem;
            color: white;
        }

        .stat-badge.total { background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); }
        .stat-badge.error { background: linear-gradient(135deg, #ef4444 0%, #f87171 100%); }
        .stat-badge.warning { background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%); }
        .stat-badge.info { background: linear-gradient(135deg, #3b82f6 0%, #60a5fa 100%); }

        .stat-label {
            font-size: 0.7rem;
            color: #6b7280;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .file-size {
            color: #6b7280;
            font-weight: 500;
            font-size: 0.9rem;
            text-align: center;
        }

        /* Enhanced Actions */
        .log-actions {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
        }

        .action-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 2px solid;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            transition: var(--transition);
            cursor: pointer;
            position: relative;
            overflow: hidden;
            text-decoration: none;
        }

        .action-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            transition: left 0.5s;
        }

        .action-btn:hover::before {
            left: 100%;
        }

        .action-btn.view {
            border-color: #3b82f6;
            color: #3b82f6;
            background: rgba(59, 130, 246, 0.1);
        }

        .action-btn.view:hover {
            background: #3b82f6;
            color: white;
            transform: scale(1.1);
        }

        .action-btn.download {
            border-color: #10b981;
            color: #10b981;
            background: rgba(16, 185, 129, 0.1);
        }

        .action-btn.download:hover {
            background: #10b981;
            color: white;
            transform: scale(1.1);
        }

        .action-btn.delete {
            border-color: #ef4444;
            color: #ef4444;
            background: rgba(239, 68, 68, 0.1);
        }

        .action-btn.delete:hover {
            background: #ef4444;
            color: white;
            transform: scale(1.1);
        }

        /* Export Dropdown - Fixed positioning */
        .export-dropdown {
            position: relative;
        }

        .export-toggle {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 2px solid #8b5cf6;
            color: #8b5cf6;
            background: rgba(139, 92, 246, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
        }

        .export-toggle:hover {
            background: #8b5cf6;
            color: white;
            transform: scale(1.1);
        }

        .export-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border-radius: 10px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            padding: 0.75rem 0;
            min-width: 160px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: var(--transition);
            border: 1px solid rgba(0,0,0,0.1);
            margin-top: 5px;
            z-index: 1000;

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
            right: 18px;
            width: 12px;
            height: 12px;
            background: white;
            border-left: 1px solid rgba(0,0,0,0.1);
            border-top: 1px solid rgba(0,0,0,0.1);
            transform: rotate(45deg);
        }

        .export-menu a {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.25rem;
            color: #374151;
            text-decoration: none;
            transition: var(--transition);
            font-size: 0.9rem;
            font-weight: 500;
        }

        .export-menu a:hover {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            color: #1f2937;
        }

        .export-menu i {
            margin-right: 0.75rem;
            width: 16px;
            font-size: 0.85rem;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #6b7280;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.3;
        }

        /* Success/Error Messages */
        .alert {
            border-radius: var(--border-radius);
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            border: none;
            font-weight: 500;
        }

        .alert-success {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
        }

        .alert-danger {
            background: linear-gradient(135deg, #fecaca 0%, #fca5a5 100%);
            color: #991b1b;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .filter-controls {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .logs-header-row,
            .log-card {
                grid-template-columns: 1fr;
                gap: 1rem;
                text-align: left;
            }

            .logs-header-row {
                display: none;
            }

            .log-actions {
                justify-content: flex-start;
            }

            .log-stat {
                text-align: left;
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
                padding: 0.5rem;
                background: rgba(0,0,0,0.02);
                border-radius: 8px;
            }

            .stat-badge {
                min-width: 35px;
                height: 25px;
                font-size: 0.75rem;
            }

            .stat-label {
                font-size: 0.8rem;
                text-transform: none;
                letter-spacing: normal;
                color: #374151;
                font-weight: 600;
            }
        }

        @media (max-width: 768px) {
            .page-header {
                padding: 1.5rem;
                text-align: center;
            }

            .filters-section {
                padding: 1.5rem;
            }

            .stats-overview {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
            }

            .logs-header {
                padding: 1rem;
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .log-card {
                padding: 1rem;
            }

            .action-btn {
                width: 32px;
                height: 32px;
                font-size: 0.8rem;
            }
        }

        @media (max-width: 480px) {
            .stats-overview {
                grid-template-columns: 1fr;
            }

            .log-actions {
                gap: 0.25rem;
            }

            .action-btn {
                width: 30px;
                height: 30px;
            }
        }

        /* Pagination Styling */
        .pagination {
            margin: 0;
            gap: 8px;
            justify-content: center;
            align-items: center;
        }

        .pagination .page-item {
            margin: 0;
        }

        .pagination .page-item .page-link {
            border: 2px solid #e5e7eb;
            color: #6b7280;
            padding: 12px 16px;
            margin: 0;
            border-radius: 10px;
            transition: var(--transition);
            text-decoration: none;
            font-weight: 600;
            background: white;
            min-width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        .pagination .page-item.active .page-link {
            background: var(--primary-gradient);
            border-color: transparent;
            color: white;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
            transform: translateY(-1px);
        }

        .pagination .page-item.disabled .page-link {
            color: #d1d5db;
            background-color: #f9fafb;
            border-color: #f3f4f6;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .pagination .page-item:not(.disabled):not(.active) .page-link:hover {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: transparent;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(102, 126, 234, 0.4);
        }

        /* Pagination container styling */
        .pagination-container {
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: var(--shadow-soft);
            border: 1px solid #f1f5f9;
            margin-top: 20px;
        }

        /* Mobile responsive pagination */
        @media (max-width: 768px) {
            .pagination-container {
                padding: 16px;
            }
            
            .pagination .page-item .page-link {
                padding: 10px 12px;
                min-width: 40px;
                height: 40px;
                font-size: 13px;
            }
            
            .pagination {
                gap: 4px;
                flex-wrap: wrap;
            }
            
            .pagination .page-item:first-child .page-link,
            .pagination .page-item:last-child .page-link {
                padding: 10px 8px;
                font-size: 12px;
            }
        }

        @media (max-width: 480px) {
            .pagination .page-item .page-link {
                padding: 8px 10px;
                min-width: 36px;
                height: 36px;
                font-size: 12px;
            }
            
            .pagination-info {
                font-size: 12px;
            }
        }
    </style>
@endpush

@section('content')

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
        </div>
    @endif

    <!-- Modern Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1><i class="fas fa-list me-3"></i>Log Files Management</h1>
                <p>Monitor, analyze, and manage your application logs efficiently</p>
            </div>
            <div class="col-md-4 text-end">
                <div style="font-size: 4rem; opacity: 0.2;">
                    <i class="fas fa-server"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Filter Section -->
    <div class="filters-section">
        <div class="filter-header">
            <div class="filter-title">
                <i class="fas fa-filter"></i>
                Smart Filters
            </div>
            <a href="{{ route('log-tracker.export.form') }}" class="btn btn-outline-primary">
                <i class="fas fa-download me-2"></i>Bulk Export
            </a>
        </div>

        <div class="filter-controls">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchLogFiles" class="search-input" placeholder="Search log files by name or date...">
            </div>

            <select class="date-filter" id="dateFilter">
                <option value="all">All Time</option>
                <option value="today">Today</option>
                <option value="last7">Last 7 Days</option>
                <option value="last30">Last 30 Days</option>
                <option value="custom">Custom Range</option>
            </select>

            <button class="apply-btn" onclick="applyFilters()">
                <i class="fas fa-search"></i>
                <span>Apply</span>
            </button>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="stats-overview">
        <div class="stat-card">
            <div class="stat-number">{{ $totalFiles }}</div>
            <div class="stat-label">Total Files</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ number_format(array_sum(array_column($counts, 'total'))) }}</div>
            <div class="stat-label">Total Logs</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ number_format(array_sum(array_column($counts, 'error'))) }}</div>
            <div class="stat-label">Total Errors</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ round(array_sum(array_map(function($file) { return file_exists(storage_path('logs/' . $file)) ? filesize(storage_path('logs/' . $file)) : 0; }, $logFiles)) / 1024 / 1024, 2) }}MB</div>
            <div class="stat-label">Total Size</div>
        </div>
    </div>

    <!-- Modern Log Cards -->
    <div class="logs-container">
        <div class="logs-header">
            <div class="logs-title">Log Files</div>
            <div class="file-count-badge" id="fileCountBadge">{{ $totalFiles }} files</div>
        </div>

        <div class="logs-grid" id="logsGrid">
            <!-- Header Row -->
            <div class="logs-header-row">
                <div class="header-cell">File Information</div>
                <div class="header-cell">Total</div>
                <div class="header-cell">Critical</div>
                <div class="header-cell">Errors</div>
                <div class="header-cell">Others</div>
                <div class="header-cell">Size</div>
                <div class="header-cell">Actions</div>
            </div>

            @forelse($paginatedFiles as $file)
                <div class="log-card {{ isset($counts[$file]['file_error']) ? 'file-error' : '' }}" data-log-date="{{ $file }}" data-search-text="{{ strtolower($file) }}" @if(isset($counts[$file]['file_error'])) title="Error: {{ $counts[$file]['file_error'] }}" @endif>
                    <div class="log-info">
                        <div class="log-filename">{{ isset($formattedFileNames[$file]) ? $formattedFileNames[$file] : $file }}</div>
                        <div class="log-date">
                            @php
                                $dateString = 'Unknown date';
                                if (preg_match('/laravel-(\d{4})-(\d{2})-(\d{2})\.log/', $file, $matches)) {
                                    try {
                                        $dateString = \Carbon\Carbon::createFromFormat('Y-m-d', $matches[1] . '-' . $matches[2] . '-' . $matches[3])->format('M d, Y');
                                    } catch (Exception $e) {
                                        $dateString = 'Invalid date';
                                    }
                                } elseif ($file === 'laravel.log') {
                                    $dateString = 'Current log';
                                }
                            @endphp
                            {{ $dateString }}
                        </div>
                    </div>

                    <div class="log-stat">
                        @if(isset($counts[$file]['file_error']))
                            <div class="stat-badge" style="background: {{ config('log-tracker.log_levels.error.color', '#dc2626') }}; color: white; font-size: 0.7rem; padding: 4px 8px;">
                                <i class="fas fa-exclamation-triangle"></i> TOO LARGE
                            </div>
                            <div class="stat-label" style="color: {{ config('log-tracker.log_levels.error.color', '#dc2626') }};">File Error</div>
                        @else
                            <div class="stat-badge" 
                                 style="background: linear-gradient(135deg, {{ config('log-tracker.log_levels.total.color', '#6366f1') }} 0%, {{ config('log-tracker.log_levels.total.color', '#6366f1') }}cc 100%); color: white;"
                                 title="Total Logs: {{ $counts[$file]['total'] ?? 0 }}">
                                {{ $counts[$file]['total'] ?? 0 }}
                            </div>
                            <div class="stat-label">Total</div>
                        @endif
                    </div>

                    <div class="log-stat">
                        @if(isset($counts[$file]['file_error']))
                            <div class="stat-badge" style="background: {{ config('log-tracker.log_levels.error.color', '#dc2626') }}; opacity: 0.5; color: white;">-</div>
                            <div class="stat-label">Critical</div>
                        @else
                            <div class="stat-badge" 
                                 style="background: linear-gradient(135deg, {{ config('log-tracker.log_levels.critical.color', '#b91c1c') }} 0%, {{ config('log-tracker.log_levels.critical.color', '#b91c1c') }}cc 100%); color: white;"
                                 title="Critical Logs: {{ $counts[$file]['critical'] ?? 0 }}">
                                {{ $counts[$file]['critical'] ?? 0 }}
                            </div>
                            <div class="stat-label">Critical</div>
                        @endif
                    </div>

                    <div class="log-stat">
                        @if(isset($counts[$file]['file_error']))
                            <div class="stat-badge" style="background: {{ config('log-tracker.log_levels.error.color', '#dc2626') }}; opacity: 0.5; color: white;">-</div>
                            <div class="stat-label">Errors</div>
                        @else
                            <div class="stat-badge" 
                                 style="background: linear-gradient(135deg, {{ config('log-tracker.log_levels.error.color', '#dc2626') }} 0%, {{ config('log-tracker.log_levels.error.color', '#dc2626') }}cc 100%); color: white;"
                                 title="Error Logs: {{ $counts[$file]['error'] ?? 0 }}">
                                {{ $counts[$file]['error'] ?? 0 }}
                            </div>
                            <div class="stat-label">Errors</div>
                        @endif
                    </div>

                    <div class="log-stat">
                        @if(isset($counts[$file]['file_error']))
                            <div class="stat-badge" style="background: {{ config('log-tracker.log_levels.info.color', '#3b82f6') }}; opacity: 0.5; color: white;">-</div>
                            <div class="stat-label">Others</div>
                        @else
                            <div class="stat-badge" 
                                 style="background: linear-gradient(135deg, {{ config('log-tracker.log_levels.info.color', '#3b82f6') }} 0%, {{ config('log-tracker.log_levels.info.color', '#3b82f6') }}cc 100%); color: white;"
                                 title="Other Logs: {{ $counts[$file]['other'] ?? 0 }} (Emergency: {{ $counts[$file]['emergency'] ?? 0 }}, Alert: {{ $counts[$file]['alert'] ?? 0 }}, Warning: {{ $counts[$file]['warning'] ?? 0 }}, Notice: {{ $counts[$file]['notice'] ?? 0 }}, Info: {{ $counts[$file]['info'] ?? 0 }}, Debug: {{ $counts[$file]['debug'] ?? 0 }})">
                                {{ $counts[$file]['other'] ?? 0 }}
                            </div>
                            <div class="stat-label">Others</div>
                        @endif
                    </div>

                    <div class="file-size">{{ $fileSizes[$file] ?? 'Unknown' }}</div>

                    <div class="log-actions">
                        <a href="{{ route('log-tracker.show', ['logName' => $file]) }}"
                           class="action-btn view"
                           title="View Details">
                            <i class="fas fa-eye"></i>
                        </a>

                        <a href="{{ route('log-tracker.download', ['logName' => $file]) }}"
                           class="action-btn download"
                           title="Download">
                            <i class="fas fa-download"></i>
                        </a>

                        <div class="export-dropdown">
                            <button class="export-toggle" onclick="toggleExportMenu(this)" title="Export Options">
                                <i class="fas fa-share-alt"></i>
                            </button>
                            <div class="export-menu">
                                <a href="{{ route('log-tracker.export.quick', [$file, 'csv']) }}">
                                    <i class="fas fa-table"></i>CSV
                                </a>
                                <a href="{{ route('log-tracker.export.quick', [$file, 'json']) }}">
                                    <i class="fas fa-code"></i>JSON
                                </a>
                                <a href="{{ route('log-tracker.export.quick', [$file, 'excel']) }}">
                                    <i class="fas fa-file-excel"></i>Excel
                                </a>
                                <a href="{{ route('log-tracker.export.quick', [$file, 'pdf']) }}">
                                    <i class="fas fa-file-pdf"></i>PDF
                                </a>
                            </div>
                        </div>

                        <form action="{{ route('log-tracker.delete', ['logName' => $file]) }}"
                              method="POST"
                              style="display: inline;">
                            @csrf
                            <button type="button"
                                    class="action-btn delete"
                                    title="Delete"
                                    onclick="confirmDelete(this, '{{ $file }}')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3>No Log Files Found</h3>
                    <p>There are no log files to display at the moment.</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination Controls --}}
        @if(($pagination['last_page'] ?? 1) > 1)
            <div class="pagination-container">
                <nav aria-label="Log file pagination">
                    <ul class="pagination">
                        <li class="page-item {{ $pagination['current_page'] == 1 ? 'disabled' : '' }}">
                            <a class="page-link" href="?page={{ $pagination['current_page'] - 1 }}">
                                <i class="fas fa-chevron-left me-1"></i>Previous
                            </a>
                        </li>
                        @for($i = 1; $i <= $pagination['last_page']; $i++)
                            <li class="page-item {{ $pagination['current_page'] == $i ? 'active' : '' }}">
                                <a class="page-link" href="?page={{ $i }}">{{ $i }}</a>
                            </li>
                        @endfor
                        <li class="page-item {{ $pagination['current_page'] == $pagination['last_page'] ? 'disabled' : '' }}">
                            <a class="page-link" href="?page={{ $pagination['current_page'] + 1 }}">
                                Next<i class="fas fa-chevron-right ms-1"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
                <div class="pagination-info text-center mt-3">
                    <small class="text-muted">
                        Showing {{ $pagination['from'] ?? 0 }} to {{ $pagination['to'] ?? 0 }} of {{ $pagination['total'] ?? 0 }} files
                    </small>
                </div>
            </div>
        @endif
    </div>

    <!-- Custom Date Range Modal -->
    <div class="modal fade" id="dateRangeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.15);">
                <div class="modal-header" style="border-bottom: 1px solid #f0f0f0;">
                    <h5 class="modal-title">
                        <i class="fas fa-calendar-alt me-2 text-primary"></i>Select Date Range
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="startDate" class="form-label">Start Date</label>
                            <input type="date" id="startDate" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="endDate" class="form-label">End Date</label>
                            <input type="date" id="endDate" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #f0f0f0;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="applyCustomDateRange()">Apply Filter</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        class LogFilesManager {
            constructor() {
                this.init();
            }

            init() {
                this.bindEvents();
                this.setupRealtimeSearch();
            }

            bindEvents() {
                document.getElementById('searchLogFiles').addEventListener('input', () => this.performSearch());
                document.getElementById('dateFilter').addEventListener('change', () => this.handleDateFilter());
                document.addEventListener('click', (e) => this.closeExportMenus(e));
                document.addEventListener('keydown', (e) => this.handleKeyboard(e));
            }

            setupRealtimeSearch() {
                let timeout;
                document.getElementById('searchLogFiles').addEventListener('input', () => {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => this.performSearch(), 300);
                });
            }

            performSearch() {
                const searchTerm = document.getElementById('searchLogFiles').value.toLowerCase();
                const dateFilter = document.getElementById('dateFilter').value;
                const logCards = document.querySelectorAll('.log-card');
                let visibleCount = 0;

                logCards.forEach(card => {
                    const searchText = card.dataset.searchText;
                    const logDate = card.dataset.logDate;

                    let showCard = true;

                    if (searchTerm && !searchText.includes(searchTerm)) {
                        showCard = false;
                    }

                    if (!this.passesDateFilter(logDate, dateFilter)) {
                        showCard = false;
                    }

                    if (showCard) {
                        card.style.display = 'grid';
                        card.style.animation = 'fadeIn 0.3s ease-in-out';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                this.updateFileCount(visibleCount);
                this.toggleEmptyState(visibleCount === 0);
            }

            passesDateFilter(logFileName, filter) {
                if (filter === 'all') return true;

                const match = logFileName.match(/laravel-(\d{4})-(\d{2})-(\d{2})\.log/);
                if (!match) return true;

                const fileDate = new Date(`${match[1]}-${match[2]}-${match[3]}`);
                const today = new Date();
                today.setHours(0, 0, 0, 0);

                switch (filter) {
                    case 'today':
                        return fileDate.toDateString() === today.toDateString();
                    case 'last7':
                        const last7Days = new Date(today);
                        last7Days.setDate(today.getDate() - 7);
                        return fileDate >= last7Days;
                    case 'last30':
                        const last30Days = new Date(today);
                        last30Days.setDate(today.getDate() - 30);
                        return fileDate >= last30Days;
                    case 'custom':
                        const startDate = document.getElementById('startDate').value;
                        const endDate = document.getElementById('endDate').value;
                        if (!startDate || !endDate) return true;
                        return fileDate >= new Date(startDate) && fileDate <= new Date(endDate);
                    default:
                        return true;
                }
            }

            handleDateFilter() {
                const dateFilter = document.getElementById('dateFilter').value;

                if (dateFilter === 'custom') {
                    const modal = new bootstrap.Modal(document.getElementById('dateRangeModal'));
                    modal.show();
                } else {
                    this.performSearch();
                }
            }

            updateFileCount(count) {
                const badge = document.getElementById('fileCountBadge');
                badge.textContent = `${count} ${count === 1 ? 'file' : 'files'}`;
                badge.style.animation = 'pulse 0.3s ease-in-out';
            }

            toggleEmptyState(show) {
                const grid = document.getElementById('logsGrid');
                let emptyState = grid.querySelector('.empty-state');

                if (show && !emptyState) {
                    emptyState = document.createElement('div');
                    emptyState.className = 'empty-state';
                    emptyState.innerHTML = `
                            <i class="fas fa-search"></i>
                            <h3>No Files Found</h3>
                            <p>Try adjusting your search criteria.</p>
                        `;
                    grid.appendChild(emptyState);
                } else if (!show && emptyState) {
                    emptyState.remove();
                }
            }

            closeExportMenus(event) {
                if (!event.target.closest('.export-dropdown')) {
                    document.querySelectorAll('.export-menu').forEach(menu => {
                        menu.classList.remove('show');
                        menu.style.right = '0';
                        menu.style.left = 'auto';
                        menu.style.top = '100%';
                        menu.style.bottom = 'auto';
                        menu.style.marginTop = '5px';
                        menu.style.marginBottom = '0';
                    });
                    document.querySelectorAll('.log-card').forEach(card => card.classList.remove('active-dropdown'));
                }
            }

            handleKeyboard(event) {
                if ((event.ctrlKey || event.metaKey) && event.key === 'f') {
                    event.preventDefault();
                    document.getElementById('searchLogFiles').focus();
                }

                if (event.key === 'Escape') {
                    document.getElementById('searchLogFiles').value = '';
                    this.performSearch();
                }
            }

            showNotification(message, type = 'success') {
                const notification = document.createElement('div');
                notification.className = `alert alert-${type} position-fixed`;
                notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px; border-radius: 10px; box-shadow: 0 10px 25px rgba(0,0,0,0.2);';
                notification.innerHTML = `
                        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
                        ${message}
                    `;

                document.body.appendChild(notification);

                setTimeout(() => {
                    notification.style.animation = 'slideOutRight 0.3s ease-in-out forwards';
                    setTimeout(() => notification.remove(), 300);
                }, 4000);
            }
        }

        // Global functions
        function toggleExportMenu(button) {
            const menu = button.nextElementSibling;
            const card = button.closest('.log-card');
            const isOpen = menu.classList.contains('show');

            // Close all other export menus
            document.querySelectorAll('.export-menu').forEach(m => m.classList.remove('show'));
            document.querySelectorAll('.log-card').forEach(c => c.classList.remove('active-dropdown'));

            if (!isOpen) {
                menu.classList.add('show');
                card.classList.add('active-dropdown');

                setTimeout(() => {
                    const rect = menu.getBoundingClientRect();
                    const windowWidth = window.innerWidth;

                    // If right edge overflows, align left instead
                    if (rect.right > windowWidth - 10) {
                        menu.style.right = '0';
                        menu.style.left = 'auto';
                    }

                    // If bottom edge overflows, show menu upwards
                    if (rect.bottom > window.innerHeight - 20) {
                        menu.style.top = 'auto';
                        menu.style.bottom = '100%';
                        menu.style.marginTop = '0';
                        menu.style.marginBottom = '5px';
                    }
                }, 10);
            }
        }


        function applyFilters() {
            logManager.performSearch();
        }

        function applyCustomDateRange() {
            const modal = bootstrap.Modal.getInstance(document.getElementById('dateRangeModal'));
            modal.hide();
            logManager.performSearch();
        }

        function confirmDelete(button, fileName) {
            // Show confirmation dialog
            if (confirm(`Are you sure you want to delete "${fileName}"?\n\nThis action cannot be undone.`)) {
                // User clicked OK - show loading state
                const icon = button.querySelector('i');
                if (icon) {
                    icon.className = 'fas fa-spinner fa-spin';
                }
                button.disabled = true;

                // Submit the form
                const form = button.closest('form');
                form.submit();
            }
            // If user clicks Cancel, nothing happens
        }

        // Initialize
        let logManager;
        document.addEventListener('DOMContentLoaded', function() {
            logManager = new LogFilesManager();

            document.querySelectorAll('.export-menu a').forEach(link => {
                link.addEventListener('click', function() {
                    const icon = this.querySelector('i');
                    const originalClass = icon.className;
                    icon.className = 'fas fa-spinner fa-spin';

                    setTimeout(() => {
                        icon.className = originalClass;
                    }, 2000);
                });
            });
        });

        // Add CSS animations
        const style = document.createElement('style');
        style.textContent = `
                @keyframes fadeIn {
                    from { opacity: 0; transform: translateY(10px); }
                    to { opacity: 1; transform: translateY(0); }
                }

                @keyframes pulse {
                    0%, 100% { transform: scale(1); }
                    50% { transform: scale(1.05); }
                }

                @keyframes slideOutRight {
                    to { transform: translateX(100%); opacity: 0; }
                }
            `;
        document.head.appendChild(style);
    </script>
@endpush
