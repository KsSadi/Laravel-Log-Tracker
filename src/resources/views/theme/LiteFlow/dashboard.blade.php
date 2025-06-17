@extends('log-tracker::theme.LiteFlow.layouts.app')

@section('title', 'Dashboard - Log Tracker')

@section('content')
    <!-- Main Content -->
    <div class="container my-4 page-content">
        <!-- Status Indicator -->
        <div class="live-status" id="liveStatus">
            <div class="status-dot"></div>
            <span id="statusText">Connected</span>
            <div class="loading-spinner" id="statusSpinner" style="display: none;"></div>
        </div>

        <!-- Page Header -->
        <div class="page-header">
            <h1>
                <i class="fas fa-chart-line" style="color: var(--primary-color);"></i>
                Analytics Dashboard
            </h1>
            <p>Monitor, analyze, and track your application logs in real-time</p>

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

        <!-- Stats Cards -->
        <div class="stats-overview">
            <div class="stat-card error-card" id="errorCard">
                <div class="stat-card-body">
                    <div class="stat-header">
                        <div class="stat-info">
                            <h6>Error Logs</h6>
                            <div class="stat-number" id="errorCount">{{ number_format(isset($logTypesCount['error']) ? $logTypesCount['error'] : 0) }}</div>
                            <div class="stat-progress">
                                <div class="stat-progress-bar" style="width: {{ $summary['total'] > 0 ? (($logTypesCount['error'] ?? 0) / $summary['total'] * 100) : 0 }}%"></div>
                            </div>
                        </div>
                        <div class="stat-icon">
                            <i class="{{ $logStyles['error']['icon'] }}"></i>
                        </div>
                    </div>
                    <div class="stat-trend">
                        <span>Today's Activity</span>
                        <div class="stat-change">
                            <i class="fas fa-arrow-up"></i>
                            <span id="errorToday">{{ number_format(isset($newLogsToday['error']) ? $newLogsToday['error'] : 0) }}</span>
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
                            <div class="stat-progress">
                                <div class="stat-progress-bar" style="width: {{ $summary['total'] > 0 ? (($logTypesCount['warning'] ?? 0) / $summary['total'] * 100) : 0 }}%"></div>
                            </div>
                        </div>
                        <div class="stat-icon">
                            <i class="{{ $logStyles['warning']['icon'] }}"></i>
                        </div>
                    </div>
                    <div class="stat-trend">
                        <span>Today's Activity</span>
                        <div class="stat-change">
                            <i class="fas fa-arrow-up"></i>
                            <span id="warningToday">{{ number_format(isset($newLogsToday['warning']) ? $newLogsToday['warning'] : 0) }}</span>
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
                            <div class="stat-progress">
                                <div class="stat-progress-bar" style="width: {{ $summary['total'] > 0 ? (($logTypesCount['info'] ?? 0) / $summary['total'] * 100) : 0 }}%"></div>
                            </div>
                        </div>
                        <div class="stat-icon">
                            <i class="{{ $logStyles['info']['icon'] }}"></i>
                        </div>
                    </div>
                    <div class="stat-trend">
                        <span>Today's Activity</span>
                        <div class="stat-change">
                            <i class="fas fa-arrow-up"></i>
                            <span id="infoToday">{{ number_format(isset($newLogsToday['info']) ? $newLogsToday['info'] : 0) }}</span>
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
                            <div class="stat-progress">
                                <div class="stat-progress-bar" style="width: 100%"></div>
                            </div>
                        </div>
                        <div class="stat-icon">
                            <i class="{{ $logStyles['total']['icon'] }}"></i>
                        </div>
                    </div>
                    <div class="stat-trend">
                        <span>Today's Activity</span>
                        <div class="stat-change">
                            <i class="fas fa-arrow-up"></i>
                            <span id="totalToday">{{ number_format($todaysTotalLogs) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
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

        <!-- Analysis Section -->
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
                                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                <i class="fas fa-bug" style="color: var(--danger-color);"></i>
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
                                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                <i class="fas fa-clock" style="color: var(--warning-color);"></i>
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

        <!-- Recent Logs Section -->
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
                        <th style="width: 120px;">Level</th>
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
    </div>
@endsection

@push('scripts')
    <script>
        // Set current year
        document.getElementById("year").textContent = new Date().getFullYear();

        // Global variables for real-time updates
        let activityChart, typesChart;
        let updateInterval;
        let isUpdating = false;

        // Chart Configuration
        Chart.defaults.font.family = '-apple-system, BlinkMacSystemFont, "Segoe UI", system-ui, sans-serif';
        Chart.defaults.font.size = 12;
        Chart.defaults.color = '#6b7280';

        // Initialize Dashboard
        document.addEventListener('DOMContentLoaded', function() {
            initializeCharts();
            startLiveUpdates();
        });

        // Initialize Charts
        function initializeCharts() {
            // Log Types Data
            let logLabels = ['Error', 'Warning', 'Info', 'Debug'];
            let logData = {!! json_encode(array_values($logTypesCount)) !!};

            // Doughnut Chart
            const typesCtx = document.getElementById('logTypesChart').getContext('2d');
            typesChart = new Chart(typesCtx, {
                type: 'doughnut',
                data: {
                    labels: logLabels,
                    datasets: [{
                        data: logData,
                        backgroundColor: [
                            '#dc2626',
                            '#d97706',
                            '#0284c7',
                            '#059669'
                        ],
                        borderWidth: 2,
                        borderColor: '#ffffff'
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
                                padding: 15,
                                usePointStyle: true,
                                font: { size: 12, weight: '500' }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleFont: { size: 14, weight: '600' },
                            bodyFont: { size: 13 },
                            cornerRadius: 6
                        }
                    }
                }
            });

            // Activity Chart Data
            let logDates = {!! json_encode(array_keys($dates)) !!};
            let errorData = {!! json_encode(array_column($dates, 'error')) !!};
            let warningData = {!! json_encode(array_column($dates, 'warning')) !!};
            let infoData = {!! json_encode(array_column($dates, 'info')) !!};

            // Line Chart
            const activityCtx = document.getElementById('logActivityChart').getContext('2d');
            activityChart = new Chart(activityCtx, {
                type: 'line',
                data: {
                    labels: logDates.map(date => {
                        const d = new Date(date);
                        return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                    }),
                    datasets: [
                        {
                            label: 'Errors',
                            data: errorData,
                            borderColor: '#dc2626',
                            backgroundColor: 'rgba(220, 38, 38, 0.1)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true,
                            pointBackgroundColor: '#dc2626',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 4
                        },
                        {
                            label: 'Warnings',
                            data: warningData,
                            borderColor: '#d97706',
                            backgroundColor: 'rgba(217, 119, 6, 0.1)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true,
                            pointBackgroundColor: '#d97706',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 4
                        },
                        {
                            label: 'Info',
                            data: infoData,
                            borderColor: '#0284c7',
                            backgroundColor: 'rgba(2, 132, 199, 0.1)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true,
                            pointBackgroundColor: '#0284c7',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 4
                        }
                    ]
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
                                padding: 15,
                                font: { size: 12, weight: '500' }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleFont: { size: 14, weight: '600' },
                            bodyFont: { size: 13 },
                            cornerRadius: 6
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11 } }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f3f4f6' },
                            ticks: { font: { size: 11 } }
                        }
                    }
                }
            });
        }

        // Start Live Updates
        function startLiveUpdates() {
            // Mock live updates for demo
            setInterval(() => {
                updateStatus();
            }, 5000);
        }

        function updateStatus() {
            const statusText = document.getElementById('statusText');
            const spinner = document.getElementById('statusSpinner');

            statusText.textContent = 'Updating...';
            spinner.style.display = 'inline-block';

            setTimeout(() => {
                statusText.textContent = 'Connected';
                spinner.style.display = 'none';
            }, 1000);
        }

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });
    </script>
@endpush
