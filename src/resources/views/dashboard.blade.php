@extends('log-tracker::layouts.app')

@section('content')
    <div class="row mb-4">
        <div class="col">
            <h2 class="mb-4"><i class="fas fa-chart-line me-2"></i>Dashboard</h2>
            <!-- Stats Cards -->
            <div class="row g-3 mb-4">
                @foreach(['error', 'warning', 'info'] as $level)
                    <div class="col-md-3">
                        <div class="card stats-card h-100" style="border-left: 4px solid {{ $logStyles[$level]['color'] }};">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-0">{{ ucfirst($level) }} Logs</h6>
                                        <h3 class="mt-2 mb-0">{{ number_format(isset($logTypesCount[$level]) ? $logTypesCount[$level] : 0) }}</h3>
                                        <p class="mb-0" style="color: {{ $logStyles[$level]['color'] }};">
                                            <i class="fas fa-arrow-up me-1"></i>{{ number_format(isset($newLogsToday[$level]) ? $newLogsToday[$level] : 0) }} new today
                                        </p>
                                    </div>
                                    <div class="stats-icon" style="color: {{ $logStyles[$level]['color'] }};">
                                        <i class="{{ $logStyles[$level]['icon'] }}"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- Total Logs Card --}}
                <div class="col-md-3">
                    <div class="card stats-card success-card h-100" style="border-left: 4px solid {{ $logStyles['total']['color'] }};">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-0">Total Logs</h6>
                                    <h3 class="mt-2 mb-0">{{ number_format($summary['total']) }}</h3>
                                    <p class="text-success mb-0">
                                        <i class="fas fa-arrow-up me-1"></i>{{ number_format($todaysTotalLogs) }} new today
                                    </p>
                                </div>
                                <div class="stats-icon" style="color: {{ $logStyles['total']['color'] }};">
                                    <i class="{{ $logStyles['total']['icon'] }}"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Charts -->
            <div class="row g-3">
                <!-- Log Activity Chart -->
                <!-- Log Activity Chart -->
                <div class="col-md-8">
                    <div class="card h-100">
                        <div class="card-header bg-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Log Activity (Last 7 Days)</h5>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="logActivityChart" height="250"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Pass Data from Laravel to JavaScript -->


                <!-- Log Types Distribution -->
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Log Types Distribution</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="logTypesChart" height="250"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-4 shadow-lg">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i> Error Pattern Analysis</h5>
                    <span class="badge bg-success">Live Insights</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Top 5 Error Types -->
                        <div class="col-md-6">
                            <div class="card shadow-sm">
                                <div class="card-header bg-danger text-white">
                                    <h6 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i> Top 5 Error Types</h6>
                                </div>
                                <div class="card-body p-0">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                        <tr>
                                            <th>Error Type</th>
                                            <th>Count</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($topErrors as $error => $count)
                                            <tr>
                                                <td><i class="fas fa-bug text-danger me-2"></i> {{ $error }}</td>
                                                <td><span class="badge bg-danger">{{ $count }}</span></td>
                                            </tr>
                                        @endforeach
                                        @if(empty($topErrors))
                                            <tr>
                                                <td colspan="2" class="text-center text-muted">No errors found.</td>
                                            </tr>
                                        @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Peak Error Hours -->
                        <div class="col-md-6">
                            <div class="card shadow-sm">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="mb-0"><i class="fas fa-clock me-2"></i> Peak Error Hours</h6>
                                </div>
                                <div class="card-body p-0">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                        <tr>
                                            <th>Time Range</th>
                                            <th>Errors</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($topPeakHours as $hour => $count)
                                            <tr>
                                                <td>
                                                    <i class="fas fa-clock text-warning me-2"></i>
                                                    {{ \Carbon\Carbon::createFromTime($hour)->format('h:i A') }} -
                                                    {{ \Carbon\Carbon::createFromTime($hour + 1)->format('h:i A') }}
                                                </td>
                                                <td><span class="badge bg-warning text-dark">{{ $count }} errors</span></td>
                                            </tr>
                                        @endforeach
                                        @if(empty($topPeakHours))
                                            <tr>
                                                <td colspan="2" class="text-center text-muted">No peak error hours detected.</td>
                                            </tr>
                                        @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <!-- Recent Logs -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Recent Logs</h5>
                            <a href="{{route('log-tracker.index')}}" class="btn btn-sm btn-outline-success">View All Logs</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                    <tr>
                                        <th>Timestamp</th>
                                        <th>Message</th>
                                        <th>Status</th>
                                    </tr>
                                    </thead>
                                    <tbody id="recentLogsTable">
                                    @foreach($lastFiveLogs as $log)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($log['timestamp'])->format('j M Y, h:i:s A') }}</td>
                                            <td>{{ $log['message'] }}</td>
                                            <td>
                                       <span class="badge" style="background-color: {{ config('log-tracker.log_levels.' . strtolower($log['level']) . '.color', '#6c757d') }};">
                                            <i class="{{ config('log-tracker.log_levels.' . strtolower($log['level']) . '.icon', 'fas fa-circle') }}"></i>
                                            {{ ucfirst($log['level']) }}
                                        </span>

                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        <!-- Chart.js -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

        <!-- Pass Data from Laravel to JavaScript -->
        <script>
            let logLabels = ['Error', 'Warning', 'Info', 'Debug'];
            let logData = {!! json_encode(array_values($logTypesCount)) !!}; // Log counts from Laravel
            let logColors = ['#dc3545', '#ffc107', '#0d6efd', '#198754'];


            // Initialize Chart.js for Log Types Distribution
            const typesCtx = document.getElementById('logTypesChart').getContext('2d');
            const typesChart = new Chart(typesCtx, {
                type: 'doughnut',
                data: {
                    labels: logLabels,
                    datasets: [{
                        data: logData,
                        backgroundColor: logColors,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        </script>
        <script>
            let logDates = {!! json_encode(array_keys($dates)) !!}; // Last 7 days dates
            let errorData = {!! json_encode(array_column($dates, 'error')) !!};
            let warningData = {!! json_encode(array_column($dates, 'warning')) !!};
            let infoData = {!! json_encode(array_column($dates, 'info')) !!};

            // Initialize Chart.js
            const activityCtx = document.getElementById('logActivityChart').getContext('2d');
            const activityChart = new Chart(activityCtx, {
                type: 'line',
                data: {
                    labels: logDates, // Dynamic last 7 days
                    datasets: [
                        {
                            label: 'Errors',
                            data: errorData,
                            borderColor: '#dc3545',
                            backgroundColor: 'rgba(220, 53, 69, 0.1)',
                            tension: 0.3,
                            fill: true
                        },
                        {
                            label: 'Warnings',
                            data: warningData,
                            borderColor: '#ffc107',
                            backgroundColor: 'rgba(255, 193, 7, 0.1)',
                            tension: 0.3,
                            fill: true
                        },
                        {
                            label: 'Info',
                            data: infoData,
                            borderColor: '#0d6efd',
                            backgroundColor: 'rgba(13, 110, 253, 0.1)',
                            tension: 0.3,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top'
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        </script>
    @endpush

@endsection
