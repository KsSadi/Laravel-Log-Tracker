@extends('log-tracker::theme.GlowStack.layouts.app')
@section('title', 'Dashboard - Log Tracker')
@push('styles')
    <style>

        /* Modern Dashboard Header */
        .dashboard-header {
            background: var(--primary-gradient);
            border-radius: var(--border-radius);
            padding: 2.5rem 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-soft);
            color: white;
            position: relative;
            overflow: hidden;
        }

        .dashboard-header::before {
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

        .dashboard-header::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -15%;
            width: 150px;
            height: 150px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite reverse;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-30px) rotate(180deg); }
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            z-index: 2;
        }

        .header-left h1 {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .header-left p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 0;
        }

        .header-stats {
            display: flex;
            gap: 2rem;
            text-align: center;
        }

        .header-stat {
            background: rgba(255,255,255,0.15);
            padding: 1rem 1.5rem;
            border-radius: 12px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
            transition: var(--transition);
        }

        .header-stat.updating {
            background: rgba(255,255,255,0.25);
            transform: scale(1.05);
        }

        .header-stat-number {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .header-stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
            font-weight: 500;
        }

        /* Enhanced Stats Cards */
        .stats-overview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 0;
            box-shadow: var(--shadow-soft);
            transition: var(--transition);
            overflow: hidden;
            border: none;
            position: relative;
        }

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-hover);
        }

        .stat-card.updating {
            animation: pulse-update 0.8s ease-in-out;
        }

        @keyframes pulse-update {
            0%, 100% { transform: translateY(-8px) scale(1); }
            50% { transform: translateY(-8px) scale(1.02); }
        }

        .stat-card.error-card {
            background: var(--danger-gradient);
            color: white;
        }

        .stat-card.warning-card {
            background: var(--warning-gradient);
            color: #8b4513;
        }

        .stat-card.info-card {
            background: var(--info-gradient);
            color: white;
        }

        .stat-card.success-card {
            background: var(--success-gradient);
            color: white;
        }

        .stat-card-body {
            padding: 2rem;
            position: relative;
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .stat-info h6 {
            font-size: 0.9rem;
            font-weight: 600;
            opacity: 0.8;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-info .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            line-height: 1;
        }

        .stat-trend {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            font-weight: 500;
            opacity: 0.9;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            backdrop-filter: blur(10px);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 100px;
            height: 100px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            animation: pulse 4s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1) rotate(0deg); opacity: 0.5; }
            50% { transform: scale(1.1) rotate(180deg); opacity: 0.8; }
        }

        /* Charts Section */
        .charts-section {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .chart-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-soft);
            overflow: hidden;
            transition: var(--transition);
            position: relative;
        }

        .chart-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-hover);
        }

        .chart-card.updating::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--success-gradient);
            animation: loading-bar 1s ease-in-out;
        }

        @keyframes loading-bar {
            0% { transform: scaleX(0); transform-origin: left; }
            100% { transform: scaleX(1); transform-origin: left; }
        }

        .chart-header {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            padding: 1.5rem 2rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chart-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #1f2937;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .chart-badge {
            background: var(--success-gradient);
            color: white;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .chart-body {
            padding: 2rem;
            position: relative;
        }

        /* Live Status Indicator */
        .live-status {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: var(--success-gradient);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
            font-size: 0.85rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            box-shadow: var(--shadow-soft);
            z-index: 1000;
            transition: var(--transition);
            transform: translateY(-100px);
            opacity: 0;
        }

        .live-status.active {
            transform: translateY(0);
            opacity: 1;
        }

        .live-status.updating {
            background: var(--warning-gradient);
            color: #8b4513;
            animation: pulse-live 1s ease-in-out;
        }

        @keyframes pulse-live {
            0%, 100% { box-shadow: var(--shadow-soft); }
            50% { box-shadow: 0 15px 35px rgba(246, 161, 146, 0.4); }
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: currentColor;
            animation: blink 2s infinite;
        }

        @keyframes blink {
            0%, 50% { opacity: 1; }
            51%, 100% { opacity: 0.3; }
        }

        /* Error Analysis Section */
        .analysis-section {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-soft);
            margin-bottom: 2rem;
            overflow: hidden;
            position: relative;
        }

        .analysis-section.updating::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--primary-gradient);
            animation: loading-bar 1s ease-in-out;
        }

        .analysis-header {
            background: var(--primary-gradient);
            color: white;
            padding: 1.5rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .analysis-title {
            font-size: 1.3rem;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .live-badge {
            background: rgba(16, 185, 129, 0.9);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            animation: pulse-live-badge 2s ease-in-out infinite;
        }

        @keyframes pulse-live-badge {
            0%, 100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            50% { box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
        }

        .analysis-body {
            padding: 2rem;
        }

        .analysis-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }

        .analysis-card {
            background: #f8fafc;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: var(--transition);
        }

        .analysis-card.updating {
            transform: scale(1.02);
            animation: pulse-analysis 0.8s ease-in-out;
        }

        @keyframes pulse-analysis {
            0%, 100% { transform: scale(1.02); }
            50% { transform: scale(1.04); }
        }

        .analysis-card-header {
            padding: 1rem 1.5rem;
            font-weight: 600;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .analysis-card-header.error-header {
            background: var(--danger-gradient);
            color: white;
        }

        .analysis-card-header.warning-header {
            background: var(--warning-gradient);
            color: #8b4513;
        }

        .analysis-table {
            margin: 0;
        }

        .analysis-table tbody tr {
            border-bottom: 1px solid rgba(0,0,0,0.05);
            transition: var(--transition);
        }

        .analysis-table tbody tr:hover {
            background: rgba(102, 126, 234, 0.05);
        }

        .analysis-table td {
            padding: 1rem 1.5rem;
            vertical-align: middle;
        }

        .error-type {
            font-family: 'JetBrains Mono', 'Fira Code', monospace;
            font-size: 0.9rem;
            color: #374151;
            font-weight: 500;
        }

        .error-count-badge {
            background: var(--danger-gradient);
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .time-range {
            color: #374151;
            font-weight: 500;
        }

        .peak-count-badge {
            background: var(--warning-gradient);
            color: #8b4513;
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        /* Recent Logs Section */
        .recent-logs-section {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-soft);
            overflow: hidden;
            position: relative;
        }

        .recent-logs-section.updating::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--info-gradient);
            animation: loading-bar 1s ease-in-out;
        }

        .logs-section-header {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            padding: 1.5rem 2rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logs-section-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #1f2937;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .logs-actions {
            display: flex;
            gap: 0.75rem;
        }

        .action-btn {
            background: white;
            border: 2px solid #e5e7eb;
            color: #374151;
            padding: 0.6rem 1.2rem;
            border-radius: 25px;
            font-weight: 600;
            text-decoration: none;
            font-size: 0.9rem;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .action-btn.primary {
            background: var(--primary-gradient);
            border-color: transparent;
            color: white;
        }

        .action-btn.success {
            background: var(--success-gradient);
            border-color: transparent;
            color: white;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }

        .logs-table-container {
            padding: 0;
        }

        .logs-table {
            width: 100%;
            margin: 0;
        }

        .logs-table thead th {
            background: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
            padding: 1.2rem 1.5rem;
            font-weight: 600;
            color: #374151;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .logs-table tbody tr {
            border-bottom: 1px solid rgba(0,0,0,0.05);
            transition: var(--transition);
        }

        .logs-table tbody tr:hover {
            background: linear-gradient(135deg, #f0f4ff 0%, #e0f2fe 100%);
        }

        .logs-table tbody tr.new-entry {
            animation: highlight-new 2s ease-in-out;
        }

        @keyframes highlight-new {
            0% { background: rgba(34, 197, 94, 0.2); }
            100% { background: transparent; }
        }

        .logs-table tbody td {
            padding: 1.2rem 1.5rem;
            vertical-align: middle;
        }

        .log-timestamp {
            font-family: 'JetBrains Mono', 'Fira Code', monospace;
            font-size: 0.9rem;
            color: #6b7280;
            font-weight: 500;
        }

        .log-message {
            color: #374151;
            font-size: 0.95rem;
            line-height: 1.5;
            word-break: break-word;
        }

        .log-level-badge {
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            color: white;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            justify-content: center;
            min-width: 100px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem 2rem;
            color: #6b7280;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.3;
        }

        .empty-state h4 {
            color: #374151;
            margin-bottom: 0.5rem;
        }

        /* Loading Spinner */
        .loading-spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: currentColor;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Last Updated Indicator */
        .last-updated {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            backdrop-filter: blur(10px);
            opacity: 0;
            transition: var(--transition);
            z-index: 1000;
        }

        .last-updated.show {
            opacity: 1;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .charts-section {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .header-stats {
                gap: 1rem;
            }

            .header-stat {
                padding: 0.8rem 1rem;
            }
        }

        @media (max-width: 768px) {
            .dashboard-header {
                padding: 1.5rem;
                text-align: center;
            }

            .header-content {
                flex-direction: column;
                gap: 1.5rem;
            }

            .header-stats {
                justify-content: center;
                flex-wrap: wrap;
            }

            .stats-overview {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .analysis-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .logs-section-header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .logs-actions {
                justify-content: center;
            }

            .chart-body {
                padding: 1rem;
            }

            .analysis-body {
                padding: 1rem;
            }

            .live-status {
                position: relative;
                top: auto;
                right: auto;
                margin-bottom: 1rem;
                display: inline-flex;
            }
        }

        @media (max-width: 480px) {
            .dashboard-header {
                padding: 1rem;
            }

            .header-left h1 {
                font-size: 1.8rem;
            }

            .stat-card-body {
                padding: 1.5rem;
            }

            .stat-info .stat-number {
                font-size: 2rem;
            }

            .logs-table thead th,
            .logs-table tbody td {
                padding: 0.8rem;
            }

            .action-btn {
                padding: 0.5rem 1rem;
                font-size: 0.8rem;
            }
        }

        /* Chart Container Styling */
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
    </style>
@endpush

@section('content')
    <!-- Live Status Indicator -->
    <div class="live-status" id="liveStatus">
        <div class="status-dot"></div>
        <span id="statusText">Connected</span>
        <div class="loading-spinner" id="statusSpinner" style="display: none;"></div>
    </div>

    <!-- Last Updated Indicator -->
    <div class="last-updated" id="lastUpdated">
        Last updated: <span id="updateTime">just now</span>
    </div>

    <!-- Modern Dashboard Header -->
    <div class="dashboard-header">
        <div class="header-content">
            <div class="header-left">
                <h1>
                    <i class="fas fa-chart-line"></i>
                    Analytics Dashboard
                </h1>
                <p>Monitor, analyze, and track your application logs in real-time</p>
            </div>
            <div class="header-stats">
                <div class="header-stat" id="headerTotalLogs">
                    <div class="header-stat-number">{{ number_format($summary['total']) }}</div>
                    <div class="header-stat-label">Total Logs</div>
                </div>
                <div class="header-stat" id="headerTodayLogs">
                    <div class="header-stat-number">{{ number_format($todaysTotalLogs) }}</div>
                    <div class="header-stat-label">New Today</div>
                </div>
                <div class="header-stat" id="headerErrorLogs">
                    <div class="header-stat-number">{{ number_format(isset($logTypesCount['error']) ? $logTypesCount['error'] : 0) }}</div>
                    <div class="header-stat-label">Total Errors</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Stats Cards -->
    <div class="stats-overview">
        <div class="stat-card error-card" id="errorCard">
            <div class="stat-card-body">
                <div class="stat-header">
                    <div class="stat-info">
                        <h6>Error Logs</h6>
                        <div class="stat-number" id="errorCount">{{ number_format(isset($logTypesCount['error']) ? $logTypesCount['error'] : 0) }}</div>
                        <div class="stat-trend">
                            <i class="fas fa-arrow-up"></i>
                            <span id="errorToday">{{ number_format(isset($newLogsToday['error']) ? $newLogsToday['error'] : 0) }} new today</span>
                        </div>
                    </div>
                    <div class="stat-icon">
                        <i class="{{ $logStyles['error']['icon'] }}"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="stat-card warning-card" id="warningCard">
            <div class="stat-card-body">
                <div class="stat-header">
                    <div class="stat-info">
                        <h6>Warning Logs</h6>
                        <div class="stat-number" id="warningCount">{{ number_format(isset($logTypesCount['warning']) ? $logTypesCount['warning'] : 0) }}</div>
                        <div class="stat-trend">
                            <i class="fas fa-arrow-up"></i>
                            <span id="warningToday">{{ number_format(isset($newLogsToday['warning']) ? $newLogsToday['warning'] : 0) }} new today</span>
                        </div>
                    </div>
                    <div class="stat-icon">
                        <i class="{{ $logStyles['warning']['icon'] }}"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="stat-card info-card" id="infoCard">
            <div class="stat-card-body">
                <div class="stat-header">
                    <div class="stat-info">
                        <h6>Info Logs</h6>
                        <div class="stat-number" id="infoCount">{{ number_format(isset($logTypesCount['info']) ? $logTypesCount['info'] : 0) }}</div>
                        <div class="stat-trend">
                            <i class="fas fa-arrow-up"></i>
                            <span id="infoToday">{{ number_format(isset($newLogsToday['info']) ? $newLogsToday['info'] : 0) }} new today</span>
                        </div>
                    </div>
                    <div class="stat-icon">
                        <i class="{{ $logStyles['info']['icon'] }}"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="stat-card success-card" id="totalCard">
            <div class="stat-card-body">
                <div class="stat-header">
                    <div class="stat-info">
                        <h6>Total Logs</h6>
                        <div class="stat-number" id="totalCount">{{ number_format($summary['total']) }}</div>
                        <div class="stat-trend">
                            <i class="fas fa-arrow-up"></i>
                            <span id="totalToday">{{ number_format($todaysTotalLogs) }} new today</span>
                        </div>
                    </div>
                    <div class="stat-icon">
                        <i class="{{ $logStyles['total']['icon'] }}"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Charts Section -->
    <div class="charts-section">
        <div class="chart-card" id="activityChartCard">
            <div class="chart-header">
                <h5 class="chart-title">
                    <i class="fas fa-chart-area"></i>
                    Log Activity Timeline
                </h5>
                <div class="chart-badge">Last 7 Days</div>
            </div>
            <div class="chart-body">
                <div class="chart-container">
                    <canvas id="logActivityChart"></canvas>
                </div>
            </div>
        </div>

        <div class="chart-card" id="typesChartCard">
            <div class="chart-header">
                <h5 class="chart-title">
                    <i class="fas fa-chart-pie"></i>
                    Log Distribution
                </h5>
                <div class="chart-badge">Real-time</div>
            </div>
            <div class="chart-body">
                <div class="chart-container">
                    <canvas id="logTypesChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Error Analysis Section -->
    <div class="analysis-section" id="analysisSection">
        <div class="analysis-header">
            <h5 class="analysis-title">
                <i class="fas fa-chart-line"></i>
                Error Pattern Analysis
            </h5>
            <div class="live-badge">
                <div class="loading-spinner"></div>
                Live Insights
            </div>
        </div>
        <div class="analysis-body">
            <div class="analysis-grid">
                <!-- Top Error Types -->
                <div class="analysis-card" id="topErrorsCard">
                    <div class="analysis-card-header error-header">
                        <i class="fas fa-exclamation-triangle"></i>
                        Top 5 Error Types
                    </div>
                    <div class="analysis-table-container">
                        <table class="table analysis-table">
                            <tbody id="topErrorsTable">
                            @forelse($topErrors as $error => $count)
                                <tr>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                                            <i class="fas fa-bug" style="color: #ef4444;"></i>
                                            <span class="error-type">{{ $error }}</span>
                                        </div>
                                    </td>
                                    <td style="text-align: right;">
                                        <span class="error-count-badge">{{ $count }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="empty-state">
                                        <i class="fas fa-smile"></i>
                                        <h4>No Errors Found</h4>
                                        <p>Your application is running smoothly!</p>
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Peak Error Hours -->
                <div class="analysis-card" id="peakHoursCard">
                    <div class="analysis-card-header warning-header">
                        <i class="fas fa-clock"></i>
                        Peak Error Hours
                    </div>
                    <div class="analysis-table-container">
                        <table class="table analysis-table">
                            <tbody id="peakHoursTable">
                            @forelse($topPeakHours as $hour => $count)
                                <tr>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                                            <i class="fas fa-clock" style="color: #f59e0b;"></i>
                                            <span class="time-range">
                                                    {{ \Carbon\Carbon::createFromTime($hour)->format('h:i A') }} -
                                                    {{ \Carbon\Carbon::createFromTime($hour + 1)->format('h:i A') }}
                                                </span>
                                        </div>
                                    </td>
                                    <td style="text-align: right;">
                                        <span class="peak-count-badge">{{ $count }} errors</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="empty-state">
                                        <i class="fas fa-chart-line"></i>
                                        <h4>No Peak Hours</h4>
                                        <p>Error distribution is consistent.</p>
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Recent Logs Section -->
    <div class="recent-logs-section" id="recentLogsSection">
        <div class="logs-section-header">
            <h5 class="logs-section-title">
                <i class="fas fa-history"></i>
                Recent Activity
            </h5>
            <div class="logs-actions">
                <a href="{{route('log-tracker.export.form')}}" class="action-btn primary">
                    <i class="fas fa-download"></i>
                    Export All
                </a>
                <a href="{{route('log-tracker.index')}}" class="action-btn success">
                    <i class="fas fa-list"></i>
                    View All Logs
                </a>
            </div>
        </div>
        <div class="logs-table-container">
            <table class="table logs-table">
                <thead>
                <tr>
                    <th style="width: 200px;">Timestamp</th>
                    <th>Message</th>
                    <th style="width: 140px;">Level</th>
                </tr>
                </thead>
                <tbody id="recentLogsTable">
                @forelse($lastFiveLogs as $log)
                    <tr>
                        <td>
                            <div class="log-timestamp">
                                {{ \Carbon\Carbon::parse($log['timestamp'])->format('j M Y, h:i:s A') }}
                            </div>
                        </td>
                        <td>
                            <div class="log-message">{{ $log['message'] }}</div>
                        </td>
                        <td>
                            <div class="log-level-badge" style="background-color: {{ config('log-tracker.log_levels.' . strtolower($log['level']) . '.color', '#6c757d') }};">
                                <i class="{{ config('log-tracker.log_levels.' . strtolower($log['level']) . '.icon', 'fas fa-circle') }}"></i>
                                {{ ucfirst($log['level']) }}
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <h4>No Recent Logs</h4>
                            <p>No log entries found in the system.</p>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Chart.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

    <script>
        // Global variables for real-time updates
        let activityChart, typesChart;
        let updateInterval;
        let isUpdating = false;
        let lastUpdateTime = new Date();

        // Enhanced Chart Configuration
        Chart.defaults.font.family = '-apple-system, BlinkMacSystemFont, "Segoe UI", system-ui, sans-serif';
        Chart.defaults.font.size = 12;
        Chart.defaults.color = '#374151';

        // Initialize Dashboard
        document.addEventListener('DOMContentLoaded', function() {
            initializeCharts();
            startLiveUpdates();
            showLiveStatus();
        });

        // Initialize Charts
        function initializeCharts() {
            // Dynamic Log Types Data
            const logLevelsConfig = {!! json_encode(config('log-tracker.log_levels')) !!};
            const logTypesData = {!! json_encode($logTypesCount) !!};
            
            // Filter out levels with 0 entries and prepare data
            let chartData = [];
            let chartLabels = [];
            let chartColors = [];
            
            Object.entries(logTypesData).forEach(([level, count]) => {
                if (count > 0 && level !== 'total') {
                    chartData.push(count);
                    chartLabels.push(level.charAt(0).toUpperCase() + level.slice(1));
                    
                    // Get color from config or fallback
                    const levelColor = logLevelsConfig[level]?.color || '#6c757d';
                    chartColors.push(levelColor);
                }
            });

            // Enhanced Doughnut Chart
            const typesCtx = document.getElementById('logTypesChart').getContext('2d');
            typesChart = new Chart(typesCtx, {
                type: 'doughnut',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        data: chartData,
                        backgroundColor: chartColors,
                        borderWidth: 0,
                        hoverOffset: 15
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '60%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true,
                                font: {
                                    size: 12,
                                    weight: '600'
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleFont: { size: 14, weight: '600' },
                            bodyFont: { size: 13 },
                            cornerRadius: 8,
                            displayColors: false
                        }
                    },
                    animation: {
                        animateRotate: true,
                        duration: 1000
                    }
                }
            });

            // Activity Chart Data - Dynamic levels
            const datesData = {!! json_encode($dates) !!};
            const logDates = Object.keys(datesData);
            
            // Create datasets for each log level that has data
            const datasets = [];
            const levelColors = {};
            
            // Get colors for each level
            Object.keys(logLevelsConfig).forEach(level => {
                if (level !== 'total') {
                    levelColors[level] = logLevelsConfig[level]?.color || '#6c757d';
                }
            });
            
            // Create dataset for each level that has at least some data
            Object.keys(logTypesData).forEach(level => {
                if (logTypesData[level] > 0 && level !== 'total') {
                    const levelData = logDates.map(date => datesData[date][level] || 0);
                    const color = levelColors[level];
                    
                    datasets.push({
                        label: level.charAt(0).toUpperCase() + level.slice(1),
                        data: levelData,
                        borderColor: color,
                        backgroundColor: color + '1a', // Add transparency
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: color,
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        pointHoverRadius: 8
                    });
                }
            });

            // Enhanced Line Chart
            const activityCtx = document.getElementById('logActivityChart').getContext('2d');
            activityChart = new Chart(activityCtx, {
                type: 'line',
                data: {
                    labels: logDates.map(date => {
                        const d = new Date(date);
                        return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                    }),
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 20,
                                font: {
                                    size: 12,
                                    weight: '600'
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleFont: { size: 14, weight: '600' },
                            bodyFont: { size: 13 },
                            cornerRadius: 8,
                            displayColors: true,
                            usePointStyle: true
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 11,
                                    weight: '500'
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)',
                                borderDash: [2, 2]
                            },
                            ticks: {
                                font: {
                                    size: 11,
                                    weight: '500'
                                }
                            }
                        }
                    },
                    elements: {
                        point: {
                            hoverBackgroundColor: '#ffffff'
                        }
                    },
                    animation: {
                        duration: 1500,
                        easing: 'easeInOutQuart'
                    }
                }
            });
        }

        // Show Live Status
        function showLiveStatus() {
            const liveStatus = document.getElementById('liveStatus');
            setTimeout(() => {
                liveStatus.classList.add('active');
            }, 500);
        }

        // Start Live Updates
        function startLiveUpdates() {
            // Initial update after 2 seconds
            setTimeout(() => {
                fetchDashboardData();
            }, 2000);

            // Set up interval for every 30 seconds
            updateInterval = setInterval(() => {
                fetchDashboardData();
            }, 30000);

            console.log('🚀 Live updates started! Refreshing every 30 seconds...');
        }

        // Fetch Dashboard Data
        async function fetchDashboardData() {
            if (isUpdating) return;

            isUpdating = true;
            showUpdatingState();

            try {
                const response = await fetch('/log-tracker/api/dashboard-refresh', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                // Update all dashboard sections
                updateHeaderStats(data.summary, data.todaysTotalLogs, data.logTypesCount);
                updateStatsCards(data.logTypesCount, data.newLogsToday, data.summary, data.todaysTotalLogs);
                updateCharts(data.dates, data.logTypesCount);
                updateAnalysisSection(data.topErrors, data.topPeakHours);
                updateRecentLogs(data.lastFiveLogs);

                lastUpdateTime = new Date();
                showSuccessState();
                showLastUpdated();

                console.log('✅ Dashboard updated successfully!', data);

            } catch (error) {
                console.error('❌ Failed to fetch dashboard data:', error);
                showErrorState();
            } finally {
                isUpdating = false;
                hideUpdatingState();
            }
        }

        // Update Header Stats
        function updateHeaderStats(summary, todaysTotalLogs, logTypesCount) {
            const headerTotal = document.getElementById('headerTotalLogs');
            const headerToday = document.getElementById('headerTodayLogs');
            const headerErrors = document.getElementById('headerErrorLogs');

            headerTotal.classList.add('updating');
            headerToday.classList.add('updating');
            headerErrors.classList.add('updating');

            setTimeout(() => {
                headerTotal.querySelector('.header-stat-number').textContent = numberFormat(summary.total);
                headerToday.querySelector('.header-stat-number').textContent = numberFormat(todaysTotalLogs);
                headerErrors.querySelector('.header-stat-number').textContent = numberFormat(logTypesCount.error || 0);

                setTimeout(() => {
                    headerTotal.classList.remove('updating');
                    headerToday.classList.remove('updating');
                    headerErrors.classList.remove('updating');
                }, 800);
            }, 200);
        }

        // Update Stats Cards
        function updateStatsCards(logTypesCount, newLogsToday, summary, todaysTotalLogs) {
            const cards = ['error', 'warning', 'info'];

            cards.forEach(level => {
                const card = document.getElementById(`${level}Card`);
                const countElement = document.getElementById(`${level}Count`);
                const todayElement = document.getElementById(`${level}Today`);

                card.classList.add('updating');

                setTimeout(() => {
                    countElement.textContent = numberFormat(logTypesCount[level] || 0);
                    todayElement.textContent = `${numberFormat(newLogsToday[level] || 0)} new today`;

                    setTimeout(() => {
                        card.classList.remove('updating');
                    }, 800);
                }, 200);
            });

            // Update total card
            const totalCard = document.getElementById('totalCard');
            const totalCount = document.getElementById('totalCount');
            const totalToday = document.getElementById('totalToday');

            totalCard.classList.add('updating');

            setTimeout(() => {
                totalCount.textContent = numberFormat(summary.total);
                totalToday.textContent = `${numberFormat(todaysTotalLogs)} new today`;

                setTimeout(() => {
                    totalCard.classList.remove('updating');
                }, 800);
            }, 200);
        }

        // Update Charts
        function updateCharts(dates, logTypesCount) {
            const activityCard = document.getElementById('activityChartCard');
            const typesCard = document.getElementById('typesChartCard');

            activityCard.classList.add('updating');
            typesCard.classList.add('updating');

            // Update Activity Chart with dynamic levels
            if (activityChart && dates && Object.keys(dates).length > 0) {
                const firstDate = Object.values(dates)[0];
                const availableLevels = Object.keys(firstDate);
                
                // Update each dataset
                activityChart.data.datasets.forEach((dataset, index) => {
                    if (availableLevels[index]) {
                        const level = availableLevels[index];
                        const levelData = Object.values(dates).map(date => date[level] || 0);
                        dataset.data = levelData;
                    }
                });
                
                activityChart.update('active');
            }

            // Update Types Chart with dynamic levels
            if (typesChart && logTypesCount) {
                // Filter out levels with 0 entries
                const filteredData = [];
                const filteredLabels = [];
                
                Object.entries(logTypesCount).forEach(([level, count]) => {
                    if (count > 0 && level !== 'total') {
                        filteredData.push(count);
                        filteredLabels.push(level.charAt(0).toUpperCase() + level.slice(1));
                    }
                });

                typesChart.data.datasets[0].data = filteredData;
                typesChart.data.labels = filteredLabels;
                typesChart.update('active');
            }

            setTimeout(() => {
                activityCard.classList.remove('updating');
                typesCard.classList.remove('updating');
            }, 1000);
        }

        // Update Analysis Section
        function updateAnalysisSection(topErrors, topPeakHours) {
            const analysisSection = document.getElementById('analysisSection');
            const topErrorsCard = document.getElementById('topErrorsCard');
            const peakHoursCard = document.getElementById('peakHoursCard');

            analysisSection.classList.add('updating');
            topErrorsCard.classList.add('updating');
            peakHoursCard.classList.add('updating');

            setTimeout(() => {
                // Update Top Errors Table
                updateTopErrorsTable(topErrors);

                // Update Peak Hours Table
                updatePeakHoursTable(topPeakHours);

                setTimeout(() => {
                    analysisSection.classList.remove('updating');
                    topErrorsCard.classList.remove('updating');
                    peakHoursCard.classList.remove('updating');
                }, 800);
            }, 200);
        }

        // Update Top Errors Table
        function updateTopErrorsTable(topErrors) {
            const tbody = document.getElementById('topErrorsTable');

            if (!topErrors || Object.keys(topErrors).length === 0) {
                tbody.innerHTML = `
                        <tr>
                            <td colspan="2" class="empty-state">
                                <i class="fas fa-smile"></i>
                                <h4>No Errors Found</h4>
                                <p>Your application is running smoothly!</p>
                            </td>
                        </tr>
                    `;
                return;
            }

            let html = '';
            Object.entries(topErrors).slice(0, 5).forEach(([error, count]) => {
                html += `
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <i class="fas fa-bug" style="color: #ef4444;"></i>
                                    <span class="error-type">${error}</span>
                                </div>
                            </td>
                            <td style="text-align: right;">
                                <span class="error-count-badge">${count}</span>
                            </td>
                        </tr>
                    `;
            });

            tbody.innerHTML = html;
        }

        // Update Peak Hours Table
        function updatePeakHoursTable(topPeakHours) {
            const tbody = document.getElementById('peakHoursTable');

            if (!topPeakHours || Object.keys(topPeakHours).length === 0) {
                tbody.innerHTML = `
                        <tr>
                            <td colspan="2" class="empty-state">
                                <i class="fas fa-chart-line"></i>
                                <h4>No Peak Hours</h4>
                                <p>Error distribution is consistent.</p>
                            </td>
                        </tr>
                    `;
                return;
            }

            let html = '';
            Object.entries(topPeakHours).slice(0, 5).forEach(([hour, count]) => {
                const startHour = String(hour).padStart(2, '0') + ':00';
                const endHour = String((parseInt(hour) + 1) % 24).padStart(2, '0') + ':00';

                html += `
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <i class="fas fa-clock" style="color: #f59e0b;"></i>
                                    <span class="time-range">${startHour} - ${endHour}</span>
                                </div>
                            </td>
                            <td style="text-align: right;">
                                <span class="peak-count-badge">${count} errors</span>
                            </td>
                        </tr>
                    `;
            });

            tbody.innerHTML = html;
        }

        // Update Recent Logs
        function updateRecentLogs(recentLogs) {
            const section = document.getElementById('recentLogsSection');
            const tbody = document.getElementById('recentLogsTable');

            section.classList.add('updating');

            setTimeout(() => {
                if (!recentLogs || recentLogs.length === 0) {
                    tbody.innerHTML = `
                            <tr>
                                <td colspan="3" class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <h4>No Recent Logs</h4>
                                    <p>No log entries found in the system.</p>
                                </td>
                            </tr>
                        `;
                } else {
                    let html = '';
                    recentLogs.slice(0, 5).forEach((log, index) => {
                        const levelConfig = getLogLevelConfig(log.level);
                        html += `
                                <tr class="new-entry">
                                    <td>
                                        <div class="log-timestamp">${formatTimestamp(log.timestamp)}</div>
                                    </td>
                                    <td>
                                        <div class="log-message">${log.message}</div>
                                    </td>
                                    <td>
                                        <div class="log-level-badge" style="background-color: ${levelConfig.color};">
                                            <i class="${levelConfig.icon}"></i>
                                            ${log.level.charAt(0).toUpperCase() + log.level.slice(1)}
                                        </div>
                                    </td>
                                </tr>
                            `;
                    });
                    tbody.innerHTML = html;
                }

                setTimeout(() => {
                    section.classList.remove('updating');
                }, 800);
            }, 200);
        }

        // Helper Functions
        function numberFormat(num) {
            return new Intl.NumberFormat().format(num || 0);
        }

        function formatTimestamp(timestamp) {
            const date = new Date(timestamp);
            return date.toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric',
                year: 'numeric',
                hour: 'numeric',
                minute: '2-digit',
                second: '2-digit'
            });
        }

        function getLogLevelConfig(level) {
            // Dynamic config from Laravel config file
            const configs = {!! json_encode(config('log-tracker.log_levels')) !!};
            const levelKey = level.toLowerCase();
            
            if (configs[levelKey]) {
                return {
                    color: configs[levelKey].color,
                    icon: configs[levelKey].icon
                };
            }
            
            // Fallback for unknown levels
            return { color: '#6c757d', icon: 'fas fa-circle' };
        }

        // Status Management
        function showUpdatingState() {
            const status = document.getElementById('liveStatus');
            const statusText = document.getElementById('statusText');
            const spinner = document.getElementById('statusSpinner');

            status.classList.add('updating');
            statusText.textContent = 'Updating...';
            spinner.style.display = 'inline-block';
        }

        function hideUpdatingState() {
            const status = document.getElementById('liveStatus');
            const statusText = document.getElementById('statusText');
            const spinner = document.getElementById('statusSpinner');

            setTimeout(() => {
                status.classList.remove('updating');
                statusText.textContent = 'Connected';
                spinner.style.display = 'none';
            }, 1000);
        }

        function showSuccessState() {
            const status = document.getElementById('liveStatus');
            status.classList.remove('updating');
        }

        function showErrorState() {
            const status = document.getElementById('liveStatus');
            const statusText = document.getElementById('statusText');

            status.classList.remove('updating');
            status.style.background = 'linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%)';
            statusText.textContent = 'Connection Error';

            setTimeout(() => {
                status.style.background = 'var(--success-gradient)';
                statusText.textContent = 'Connected';
            }, 3000);
        }

        function showLastUpdated() {
            const lastUpdated = document.getElementById('lastUpdated');
            const updateTime = document.getElementById('updateTime');

            updateTime.textContent = lastUpdateTime.toLocaleTimeString();
            lastUpdated.classList.add('show');

            setTimeout(() => {
                lastUpdated.classList.remove('show');
            }, 3000);
        }

        // Cleanup on page unload
        window.addEventListener('beforeunload', function() {
            if (updateInterval) {
                clearInterval(updateInterval);
            }
        });

        // Handle visibility change (pause updates when tab is not active)
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                if (updateInterval) {
                    clearInterval(updateInterval);
                }
            } else {
                startLiveUpdates();
            }
        });

    </script>
@endpush



