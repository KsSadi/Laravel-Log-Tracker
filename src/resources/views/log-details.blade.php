@extends('log-tracker::layouts.app')

@section('content')
    @push('styles')
        <style>
            :root {
                --primary: #6366f1;
                --primary-hover: #4f46e5;
                --secondary: #475569;
                --danger: #ef4444;
                --warning: #f59e0b;
                --info: #3b82f6;
                --success: #10b981;
                --dark: #1e293b;
                --light: #f8fafc;
            }

            body {
                background-color: #f1f5f9;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            }


            .stat-card .stat-value {
                font-size: 1.5rem;
                font-weight: 600;
                color: var(--primary);
            }

            .stat-card .stat-label {
                color: var(--secondary);
                font-size: 0.875rem;
            }

            .search-box input {
                padding: 0.75rem 1rem 0.75rem 2.5rem;
                border-radius: 8px;
                border: 1px solid #cbd5e1;
                width: 100%;
                font-size: 0.95rem;
                transition: all 0.2s;
            }

            .search-box input:focus {
                outline: none;
                border-color: var(--primary);
                box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
            }


            .log-viewer {
                max-height: 600px;
                overflow: auto;
                background-color: var(--dark);
                color: var(--light);
                border-bottom-left-radius: 12px;
                border-bottom-right-radius: 12px;
            }

            .log-table {
                width: 100%;
            }

            .log-table th {
                background-color: rgba(255, 255, 255, 0.05);
                padding: 0.75rem 1rem;
                font-weight: 500;
                text-align: left;
                color: #94a3b8;
                position: sticky;
                top: 0;
                z-index: 10;
            }

            .log-table td {
                padding: 1rem;
                border-bottom: 1px solid rgba(255, 255, 255, 0.05);
                vertical-align: middle;
            }

            .log-level {
                padding: 0.35rem 0.75rem;
                border-radius: 4px;
                font-weight: 600;
                font-size: 0.75rem;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .timestamp {
                color: #94a3b8;
                font-size: 0.85rem;
            }

            .stack-btn {
                background-color: var(--success);
                color: white;
                border: none;
                padding: 0.35rem 0.75rem;
                border-radius: 4px;
                font-size: 0.75rem;
                cursor: pointer;
                transition: all 0.2s;
                display: inline-flex;
                align-items: center;
                gap: 0.35rem;
            }

            .stack-btn:hover {
                background-color: #005c3e;
            }

            .stack-trace {
                background-color: #334155;
                padding: 1rem;
                margin: 0;
                border-radius: 6px;
                white-space: pre-wrap;
                font-family: monospace;
                font-size: 0.85rem;
                color: #e2e8f0;
                overflow-x: auto;
            }

            .log-message {
                font-family: monospace;
                word-break: break-word;
            }

            @media (max-width: 768px) {
                .log-stats {
                    flex-direction: column;
                    gap: 0.75rem;
                }

                .stat-card {
                    min-width: unset;
                    width: 100%;
                }

                .log-table th:nth-child(2),
                .log-table td:nth-child(2) {
                    display: none;
                }
            }
        </style>

    @endpush

    <div class="row mb-3">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('log-tracker.dashboard')}}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{route('log-tracker.index')}}">Log Files</a></li>
                    <li class="breadcrumb-item active" id="logFileBreadcrumb">{{$logName}}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">
                    <i class="fas fa-file-alt me-2"></i>
                    <span id="logFileName">{{ $logName }}</span>
                </h2>
                <div>
                    <!-- Back Button -->
                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left me-1"></i>Back
                    </a>

                    <!-- Download Log Button -->
                    <a href="{{ route('log-tracker.download', ['logName' => $logName]) }}" class="btn btn-outline-success me-2">
                        <i class="fas fa-download me-1"></i>Download
                    </a>

                    <!-- Delete Log Button with Confirmation -->
                    <form action="{{ route('log-tracker.delete', ['logName' => $logName]) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Are you sure you want to delete this log file?');">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="fas fa-trash me-1"></i>Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-md-3">
            <!-- Log Metadata -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i> Log Metadata</h5>
                </div>
                <div class="card-body">
                    <div class="log-meta p-3">

                        <!-- Total Entries -->
                        <div class="log-meta-item d-flex justify-content-between align-items-center">
                            <div class="fw-semibold text-muted"><i class="fas fa-list me-2"></i> Total Entries:</div>
                            <div class="fw-bold">{{ count($entries) }}</div>
                        </div>

                        <!-- Log File Size -->
                        <div class="log-meta-item d-flex justify-content-between align-items-center">
                            <div class="fw-semibold text-muted"><i class="fas fa-file-alt me-2"></i> Log Size:</div>
                            <div class="fw-bold">
                                {{ round(filesize(storage_path('logs/' . $logName)) / 1024, 2) }} KB
                            </div>
                        </div>

                        <hr>

                        <!-- Dynamic Log Counts with Colors & Icons -->
                        @foreach($logLevels as $level => $data)
                            <div class="log-meta-item d-flex justify-content-between align-items-center">
                                <div class="fw-semibold text-muted">
                                    <i class="{{ $data['icon'] }} me-2" style="color: {{ $data['color'] }}"></i>
                                    {{ ucfirst($level) }} Logs:
                                </div>
                                <div>
                        <span class="badge px-3 py-2" style="background-color: {{ $data['color'] }};">
                            {{isset($counts[$level]) ? $counts[$level] : 0}}
                        </span>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>




            <!-- Log Filters -->
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Filters</h5>
                    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                        <i class="fas fa-chevron-up"></i>
                    </button>
                </div>
                <div class="card-body collapse show" id="filterCollapse">
                    <div class="mb-3">
                        <label class="form-label">Log Levels</label>
                        <div class="d-flex flex-column gap-2">
                            @foreach($logLevels as $level => $config)
                                <div class="form-check form-switch">
                                    <input class="form-check-input log-filter" type="checkbox" id="{{ $level }}Check" value="{{ $level }}" checked>
                                    <label class="form-check-label fw-bold" for="{{ $level }}Check" style="color: {{ $config['color'] }};">
                                        <i class="{{ $config['icon'] }}"></i> {{ ucfirst($level) }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Search in logs</label>
                        <input type="text" class="form-control form-control-sm log-search" placeholder="Search term...">
                    </div>
                </div>
            </div>

        </div>

        <div class="col-md-9">
            <!-- Log Content -->
            <div class="card">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Log Content</h5>
                        <div>
                            <span class="badge bg-success">{{ round(filesize(storage_path('logs/' . $logName)) / 1024, 2) }} KB</span>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <!-- Log Tools Bar -->
                    <div class="log-tools p-2">
                        <div class="card p-3 mb-3">
                            <input type="text" class="form-control log-search" placeholder="Search logs..." onkeyup="filterLogs()">
                        </div>

                    </div>

                    <!-- Log Content Table -->

                    <div class="log-viewer">
                        <table class="log-table">
                            <thead>
                            <tr>
                                <th width="100">Level</th>
                                <th width="150">Time</th>
                                <th>Message</th>
                                <th width="80">Actions</th>
                            </tr>
                            </thead>
                            <tbody id="logTable">
                            @foreach ($entries as $index => $log)
                                <tr data-level="{{ strtolower($log['level']) }}">
                                    <td>
                                        <span class="log-level badge" style="background-color: {{ $log['color'] }};">
                                            <i class="{{ $log['icon'] }}"></i> {{ strtoupper($log['level']) }}
                                        </span>
                                    </td>


                                    <td><span class="timestamp">{{ $log['timestamp'] }}</span></td>
                                    <td class="log-message">{{ $log['message'] }}</td>
                                    <td>
                                        @if (!empty($log['stack']))
                                            <button class="stack-btn" onclick="toggleStackTrace({{ $index }})">
                                                <i class="fas fa-code"></i> Stack
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                                @if (!empty($log['stack']))
                                    <tr id="stacktrace-{{ $index }}" class="d-none">
                                        <td colspan="4">
                                            <pre class="stack-trace">{{ $log['stack'] }}</pre>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            <!-- "No Data Found" Row (Initially Hidden) -->
                            <tr id="noDataRow" class="text-center">
                                <td colspan="4" class="py-3">🚫 No logs found matching your search.</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                // Attach event listeners to log level filters (switches)
                document.querySelectorAll(".log-filter").forEach(filter => {
                    filter.addEventListener("change", filterLogs);
                });

                // Attach event listener to all search inputs
                document.querySelectorAll(".log-search").forEach(input => {
                    input.addEventListener("keyup", filterLogs);
                });

                // Ensure the "No Data Found" row is initially hidden
                let noDataRow = document.getElementById("noDataRow");
                if (noDataRow) noDataRow.style.display = "none";
            });

            function filterLogs() {
                let selectedLevels = Array.from(document.querySelectorAll(".log-filter:checked")).map(e => e.value);
                let searchQueries = Array.from(document.querySelectorAll(".log-search"))
                    .map(input => input.value.toLowerCase())
                    .filter(query => query.length > 0);

                let logRows = document.querySelectorAll("#logTable tr[data-level]");
                let visibleCount = 0;

                logRows.forEach(row => {
                    let logLevel = row.getAttribute("data-level");
                    let logMessage = row.innerText.toLowerCase();
                    let showRow = selectedLevels.includes(logLevel);

                    if (searchQueries.length > 0) {
                        showRow = searchQueries.some(query => logMessage.includes(query));
                    }

                    row.style.display = showRow ? "" : "none";
                    if (showRow) visibleCount++;
                });

                // Show "No Data Found" message if no visible rows exist
                let noDataRow = document.getElementById("noDataRow");
                if (noDataRow) {
                    noDataRow.style.display = (visibleCount === 0) ? "" : "none";
                }
            }


        </script>
    @endpush
@endsection
