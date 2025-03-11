@extends('log-tracker::layouts.app')

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <h2 class="mb-4"><i class="fas fa-list me-2"></i>Log Files</h2>

    <!-- Filter Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="filter-box p-3">
                <div class="row g-2 align-items-center">
                    <!-- Search Input -->
                    <div class="col-md-7">
                        <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="fas fa-search"></i>
                        </span>
                            <input type="text" id="searchLogFiles" class="form-control" placeholder="Search log files..." onkeyup="filterLogs()">
                        </div>
                    </div>

                    <!-- Date Range Filter -->
                    <div class="col-md-3">
                        <select class="form-select" id="dateFilter" onchange="handleDateSelection()">
                            <option value="all">All Time</option>
                            <option value="today">Today</option>
                            <option value="last7">Last 7 Days</option>
                            <option value="last30">Last 30 Days</option>
                            <option value="custom">Custom Range</option>
                        </select>
                    </div>

                    <!-- Apply Button -->
                    <div class="col-md-2">
                        <button class="btn btn-success w-100" onclick="filterLogs()">
                            <i class="fas fa-filter me-1"></i> Apply
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Date Picker Modal -->
    <div class="modal fade" id="datePickerModal" tabindex="-1" aria-labelledby="datePickerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Select Date Range</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="startDate" class="form-label">Start Date:</label>
                    <input type="date" id="startDate" class="form-control" onchange="filterLogs()">

                    <label for="endDate" class="form-label mt-3">End Date:</label>
                    <input type="date" id="endDate" class="form-control" onchange="filterLogs()">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="filterLogs()">Apply</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Log Files Table -->
    <div class="card shadow-sm border-0 rounded-lg">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 px-4">
            <h5 class="mb-0 fw-bold text-dark">Log Files</h5>
            <span class="badge bg-success rounded-pill px-3 py-2" id="totalFileCount">{{ $totalFiles }} files</span>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="logTable">
                    <thead>
                    <tr class="bg-light">
                        <th class="py-3 px-4 border-0">Date</th>
                        <th class="py-3 px-4 border-0 text-center">Total</th>
                        <th class="py-3 px-4 border-0 text-center">Error</th>
                        <th class="py-3 px-4 border-0 text-center">Warning</th>
                        <th class="py-3 px-4 border-0 text-center">Info</th>
                        <th class="py-3 px-4 border-0">Size</th>
                        <th class="py-3 px-4 border-0 text-center">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($logFiles as $file)
                        <tr data-log-date="{{ $file }}" class="border-bottom">
                            <td class="py-3 px-4 align-middle fw-semibold">{{ $formattedFileNames[$file] ?? $file }}</td>
                            <td class="py-3 px-4 align-middle text-center">
                                <span class="badge bg-secondary rounded-pill px-3">{{ $counts[$file]['total'] ?? 0 }}</span>
                            </td>
                            <td class="py-3 px-4 align-middle text-center">
                                <span class="badge rounded-pill px-3"
                                      style="background-color: {{ config('log-tracker.log_levels.error.color', '#dc3545') }};">
                                    {{ $counts[$file]['error'] ?? 0 }}
                                </span>
                            </td>

                            <td class="py-3 px-4 align-middle text-center">
                                <span class="badge rounded-pill px-3"
                                      style="background-color: {{ config('log-tracker.log_levels.warning.color', '#ffc107') }};">
                                    {{ $counts[$file]['warning'] ?? 0 }}
                                </span>
                            </td>

                            <td class="py-3 px-4 align-middle text-center">
                                <span class="badge rounded-pill px-3"
                                      style="background-color: {{ config('log-tracker.log_levels.info.color', '#0d6efd') }};">
                                    {{ $counts[$file]['info'] ?? 0 }}
                                </span>
                            </td>
                            <td class="py-3 px-4 align-middle">{{ $fileSizes[$file] ?? 'Unknown' }}</td>
                            <td class="py-3 px-4 align-middle text-center log-actions">
                                <div class="btn-group">
                                    <a href="{{ route('log-tracker.show', ['logName' => $file]) }}" class="btn btn-sm btn-outline-primary me-1" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('log-tracker.download', ['logName' => $file]) }}" class="btn btn-sm btn-outline-success me-1" title="Download">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <form action="{{ route('log-tracker.delete', ['logName' => $file]) }}" method="POST" style="display:inline;" class="delete-log-form">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger delete-log-btn" title="Delete" data-log-name="{{ $file }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @push('scripts')
        <!-- JavaScript for Filtering -->
        <script>
            function handleDateSelection() {
                let dateFilter = document.getElementById("dateFilter").value;

                if (dateFilter === "custom") {
                    let datePickerModal = new bootstrap.Modal(document.getElementById('datePickerModal'));
                    datePickerModal.show();
                } else {
                    filterLogs();
                }
            }

            function filterLogs() {
                let searchInput = document.getElementById("searchLogFiles").value.toLowerCase();
                let dateFilter = document.getElementById("dateFilter").value;
                let startDate = document.getElementById("startDate").value;
                let endDate = document.getElementById("endDate").value;
                let rows = document.querySelectorAll("#logTable tbody tr");

                let today = new Date().toISOString().split('T')[0];
                let last7Days = new Date();
                last7Days.setDate(last7Days.getDate() - 7);

                let last30Days = new Date();
                last30Days.setDate(last30Days.getDate() - 30);

                let visibleCount = 0;

                rows.forEach(row => {
                    let logDate = row.getAttribute("data-log-date");
                    let formattedDate = formatLogDate(logDate);

                    let showRow = true;

                    if (dateFilter === "today" && formattedDate !== today) {
                        showRow = false;
                    } else if (dateFilter === "last7" && new Date(formattedDate) < last7Days) {
                        showRow = false;
                    } else if (dateFilter === "last30" && new Date(formattedDate) < last30Days) {
                        showRow = false;
                    } else if (dateFilter === "custom" && (formattedDate < startDate || formattedDate > endDate)) {
                        showRow = false;
                    }

                    if (!row.innerText.toLowerCase().includes(searchInput)) {
                        showRow = false;
                    }

                    row.style.display = showRow ? "" : "none";

                    if (showRow) {
                        visibleCount++;
                    }
                });

                document.getElementById("totalFileCount").innerText = visibleCount + " files";
            }

            function formatLogDate(logFileName) {
                let match = logFileName.match(/laravel-(\d{4})-(\d{2})-(\d{2})\.log/);
                if (match) {
                    return `${match[1]}-${match[2]}-${match[3]}`;
                }
                return "";
            }


            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.delete-log-btn').forEach(button => {
                    button.addEventListener('click', function () {
                        let logName = this.getAttribute('data-log-name');

                        if (confirm(`Are you sure you want to delete the log file: ${logName}?`)) {
                            this.closest('form').submit(); // Submit the form if confirmed
                        }
                    });
                });
            });
        </script>

    @endpush


@endsection
