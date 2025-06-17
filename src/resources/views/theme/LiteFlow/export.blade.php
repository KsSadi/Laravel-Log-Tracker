@extends('log-tracker::theme.LiteFlow.layouts.app')

@section('title', 'Export Log - Log Tracker')

@push('styles')
    <style>

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
            overflow: hidden;
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

        .header-icon {
            font-size: 4rem;
            color: var(--gray-200);
            opacity: 0.5;
        }

        /* Content Cards */
        .content-card {
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: 12px;
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .content-header {
            background-color: var(--gray-50);
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--gray-200);
        }

        .content-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--gray-900);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .content-title i {
            color: var(--primary-color);
        }

        .content-body {
            padding: 1.5rem;
        }

        /* Format Selection */
        .format-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-top: 1rem;
        }

        .format-card {
            position: relative;
            background: white;
            border: 2px solid var(--gray-200);
            border-radius: var(--border-radius);
            padding: 2rem 1.5rem;
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
            overflow: hidden;
        }

        .format-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
            border-color: var(--primary-color);
        }

        .format-card.selected {
            background: #dbeafe;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .format-card .icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            display: block;
            color: var(--gray-500);
            transition: var(--transition);
        }

        .format-card:hover .icon,
        .format-card.selected .icon {
            color: var(--primary-color);
            transform: scale(1.1);
        }

        .format-card h6 {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--gray-700);
        }

        .format-card small {
            color: var(--gray-600);
            font-size: 0.85rem;
        }

        /* Main Grid Layout */
        .main-grid {
            display: grid;
            grid-template-columns: 350px 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        /* Filters Panel */
        .filters-panel {
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: 12px;
            box-shadow: var(--shadow-sm);
            height: fit-content;
            position: sticky;
            top: 2rem;
        }

        .filter-section {
            padding: 1.5rem;
            border-bottom: 1px solid var(--gray-200);
        }

        .filter-section:last-child {
            border-bottom: none;
        }

        .filter-title {
            display: flex;
            align-items: center;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 1rem;
            font-size: 1rem;
            gap: 0.5rem;
        }

        .filter-title i {
            color: var(--primary-color);
        }

        .form-control {
            border: 1px solid var(--gray-300);
            border-radius: var(--border-radius);
            padding: 0.75rem 1rem;
            transition: var(--transition);
            font-size: 0.875rem;
            background: white;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            outline: none;
        }

        .form-label {
            font-size: 0.875rem;
            color: var(--gray-700);
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .date-inputs {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        /* Level Badges */
        .level-badges {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
        }

        .level-badge {
            position: relative;
            cursor: pointer;
        }

        .level-badge input[type="checkbox"] {
            position: absolute;
            opacity: 0;
            cursor: pointer;
        }

        .level-badge .badge {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem;
            border-radius: var(--border-radius);
            font-size: 0.75rem;
            font-weight: 600;
            transition: var(--transition);
            width: 100%;
            opacity: 0.7;
            cursor: pointer;
            text-align: center;
            color: white;
            gap: 0.5rem;
        }

        .level-badge input[type="checkbox"]:checked + .badge {
            opacity: 1;
            transform: scale(1.02);
            box-shadow: var(--shadow);
        }

        /* Files Panel */
        .files-panel {
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: 12px;
            box-shadow: var(--shadow-sm);
        }

        .files-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--gray-200);
            background-color: var(--gray-50);
            flex-wrap: wrap;
            gap: 1rem;
        }

        .select-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .btn-outline-primary {
            border: 1px solid var(--primary-color);
            color: var(--primary-color);
            background: transparent;
            padding: 0.5rem 1rem;
            border-radius: var(--border-radius);
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
        }

        .btn-outline-primary:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-1px);
        }

        .files-list {
            max-height: 400px;
            overflow-y: auto;
            padding: 1rem;
        }

        .file-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            margin-bottom: 0.75rem;
            background: var(--gray-50);
            border: 1px solid var(--gray-200);
            border-radius: var(--border-radius);
            transition: var(--transition);
            cursor: pointer;
        }

        .file-item:hover {
            background: #dbeafe;
            border-color: var(--primary-color);
        }

        .file-item.selected {
            background: #dbeafe;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.1);
        }

        .file-checkbox {
            width: 18px;
            height: 18px;
            margin-right: 1rem;
            accent-color: var(--primary-color);
            flex-shrink: 0;
        }

        .file-info {
            flex: 1;
            min-width: 0;
        }

        .file-name {
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 0.25rem;
            word-break: break-all;
            font-size: 0.875rem;
        }

        .file-size {
            font-size: 0.75rem;
            color: var(--gray-500);
        }

        /* Export Actions */
        .export-actions {
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: var(--shadow-sm);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .export-info {
            display: flex;
            align-items: center;
            color: var(--gray-600);
            flex: 1;
            min-width: 250px;
            gap: 0.5rem;
        }

        .export-info i {
            color: var(--primary-color);
            flex-shrink: 0;
        }

        .export-btn {
            background: var(--primary-color);
            border: none;
            padding: 1rem 2rem;
            border-radius: var(--border-radius);
            color: white;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            min-width: 180px;
            justify-content: center;
            flex-shrink: 0;
        }

        .export-btn:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .export-btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
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

        /* Custom Scrollbar */
        .files-list::-webkit-scrollbar {
            width: 6px;
        }

        .files-list::-webkit-scrollbar-track {
            background: var(--gray-100);
            border-radius: 4px;
        }

        .files-list::-webkit-scrollbar-thumb {
            background: var(--gray-400);
            border-radius: 4px;
        }

        .files-list::-webkit-scrollbar-thumb:hover {
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

        /* Responsive Design */
        @media (max-width: 1024px) {
            .main-grid {
                grid-template-columns: 320px 1fr;
                gap: 1.5rem;
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

            .main-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .format-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
            }

            .date-inputs {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .level-badges {
                grid-template-columns: 1fr;
                gap: 0.5rem;
            }

            .files-header {
                flex-direction: column;
                align-items: stretch;
                text-align: center;
            }

            .export-actions {
                flex-direction: column;
                text-align: center;
            }

            .export-info {
                justify-content: center;
                min-width: auto;
                margin-bottom: 1rem;
            }

            .export-btn {
                width: 100%;
                max-width: 300px;
            }
        }

        @media (max-width: 480px) {
            .page-header {
                padding: 1rem;
            }

            .header-title {
                font-size: 1.3rem;
            }

            .content-body {
                padding: 1rem;
            }

            .filter-section {
                padding: 1rem;
            }

            .format-grid {
                grid-template-columns: 1fr;
                gap: 0.75rem;
            }

            .format-card {
                padding: 1rem;
            }

            .format-card .icon {
                font-size: 2rem;
            }

            .breadcrumb-container {
                padding: 0.5rem 1rem;
            }
        }

        /* Notification styles */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-md);
            padding: 1rem;
            color: white;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transform: translateX(100%);
            opacity: 0;
            transition: var(--transition);
        }

        .notification.success {
            background: var(--success-color);
        }

        .notification.warning {
            background: var(--warning-color);
        }

        .notification.show {
            transform: translateX(0);
            opacity: 1;
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
                    <li class="breadcrumb-item active">Export</li>
                </ol>
            </nav>
        </div>

        <!-- Enhanced Header -->
        <div class="page-header">
            <div class="header-content">
                <div class="header-left">
                    <h1 class="header-title">
                        <i class="fas fa-download"></i>
                        Export Your Logs
                    </h1>
                    <p class="header-subtitle">
                        Generate professional reports in multiple formats with advanced filtering options
                    </p>
                </div>
                <div class="header-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
        </div>

        <form action="{{ route('log-tracker.export') }}" method="POST" id="exportForm">
            @csrf

            <!-- Format Selection -->
            <div class="content-card">
                <div class="content-header">
                    <h5 class="content-title">
                        <i class="fas fa-file-alt"></i>
                        Choose Export Format
                    </h5>
                </div>
                <div class="content-body">
                    <div class="format-grid">
                        <div class="format-card" data-format="csv">
                            <input type="radio" name="format" value="csv" id="csv" style="display: none;">
                            <i class="fas fa-table icon"></i>
                            <h6>CSV</h6>
                            <small>Excel compatible format</small>
                        </div>
                        <div class="format-card" data-format="json">
                            <input type="radio" name="format" value="json" id="json" style="display: none;">
                            <i class="fas fa-code icon"></i>
                            <h6>JSON</h6>
                            <small>API friendly structure</small>
                        </div>
                        <div class="format-card" data-format="excel">
                            <input type="radio" name="format" value="excel" id="excel" style="display: none;">
                            <i class="fas fa-file-excel icon"></i>
                            <h6>Excel</h6>
                            <small>Native Excel format</small>
                        </div>
                        <div class="format-card" data-format="pdf">
                            <input type="radio" name="format" value="pdf" id="pdf" style="display: none;">
                            <i class="fas fa-file-pdf icon"></i>
                            <h6>PDF</h6>
                            <small>Print ready reports</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Grid Layout -->
            <div class="main-grid">
                <!-- Filters Panel -->
                <div class="filters-panel">
                    <!-- Date Range -->
                    <div class="filter-section">
                        <div class="filter-title">
                            <i class="fas fa-calendar"></i>
                            Date Range
                        </div>
                        <div class="date-inputs">
                            <div>
                                <label class="form-label">From</label>
                                <input type="date" name="date_from" class="form-control">
                            </div>
                            <div>
                                <label class="form-label">To</label>
                                <input type="date" name="date_to" class="form-control">
                            </div>
                        </div>
                    </div>

                    <!-- Log Levels -->
                    <div class="filter-section">
                        <div class="filter-title">
                            <i class="fas fa-layer-group"></i>
                            Log Levels
                        </div>
                        <div class="level-badges">
                            @foreach(config('log-tracker.log_levels', []) as $level => $config)
                                @if($level !== 'total')
                                    <label class="level-badge">
                                        <input type="checkbox" name="levels[]" value="{{ $level }}" checked>
                                        <span class="badge" style="background-color: {{ $config['color'] ?? '#6b7280' }};">
                                        <i class="{{ $config['icon'] ?? 'fas fa-circle' }}"></i>
                                        {{ ucfirst($level) }}
                                    </span>
                                    </label>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <!-- Search -->
                    <div class="filter-section">
                        <div class="filter-title">
                            <i class="fas fa-search"></i>
                            Search
                        </div>
                        <input type="text" name="search" class="form-control" placeholder="Search in log messages...">
                    </div>
                </div>

                <!-- Files Panel -->
                <div class="files-panel">
                    <div class="files-header">
                        <h5 class="content-title">
                            <i class="fas fa-list"></i>
                            Select Log Files
                        </h5>
                        <div class="select-buttons">
                            <button type="button" class="btn-outline-primary" onclick="selectAll()">Select All</button>
                            <button type="button" class="btn-outline-primary" onclick="deselectAll()">Deselect All</button>
                        </div>
                    </div>

                    <div class="files-list">
                        @forelse($logFiles ?? [] as $file)
                            <div class="file-item" onclick="toggleFile(this)">
                                <input type="checkbox" name="log_files[]" value="{{ $file }}" class="file-checkbox" checked>
                                <div class="file-info">
                                    <div class="file-name">{{ $file }}</div>
                                    @if(file_exists(storage_path('logs/' . $file)))
                                        <div class="file-size">{{ round(filesize(storage_path('logs/' . $file)) / 1024, 2) }} KB</div>
                                    @else
                                        <div class="file-size">0 KB</div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <h5>No Log Files Found</h5>
                                    <p>There are no log files available for export.</p>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Export Actions -->
            <div class="export-actions">
                <div class="export-info">
                    <i class="fas fa-info-circle"></i>
                    <span>Export will include filtered log entries based on your selections above</span>
                </div>
                <button type="submit" class="export-btn" id="exportBtn">
                    <i class="fas fa-download"></i>
                    <span>Export Logs</span>
                </button>
            </div>
        </form>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Format card selection
            document.querySelectorAll('.format-card').forEach(card => {
                card.addEventListener('click', function() {
                    // Remove selected class from all cards
                    document.querySelectorAll('.format-card').forEach(c => c.classList.remove('selected'));

                    // Add selected class to clicked card
                    this.classList.add('selected');

                    // Set the radio button
                    const format = this.dataset.format;
                    document.getElementById(format).checked = true;
                });
            });

            // File selection
            document.querySelectorAll('.file-item').forEach(item => {
                const checkbox = item.querySelector('.file-checkbox');

                // Set initial state
                if (checkbox && checkbox.checked) {
                    item.classList.add('selected');
                }

                if (checkbox) {
                    checkbox.addEventListener('change', function() {
                        if (this.checked) {
                            item.classList.add('selected');
                        } else {
                            item.classList.remove('selected');
                        }
                    });
                }
            });

            // Form submission with enhanced loading
            const exportForm = document.getElementById('exportForm');
            const exportBtn = document.getElementById('exportBtn');
            const originalBtnContent = exportBtn.innerHTML;

            if (exportForm && exportBtn) {
                exportForm.addEventListener('submit', function(e) {
                    // Validate format selection
                    const format = document.querySelector('input[name="format"]:checked');
                    if (!format) {
                        e.preventDefault();
                        showNotification('Please select an export format', 'warning');
                        return false;
                    }

                    // Validate file selection
                    const files = document.querySelectorAll('input[name="log_files[]"]:checked');
                    if (files.length === 0) {
                        e.preventDefault();
                        showNotification('Please select at least one log file', 'warning');
                        return false;
                    }

                    // Start export process
                    startExportProcess(format.value);
                });
            }

            function startExportProcess(format) {
                const steps = [
                    'Preparing export...',
                    'Processing log files...',
                    `Generating ${format.toUpperCase()} file...`,
                    'Starting download...'
                ];

                let currentStep = 0;
                exportBtn.disabled = true;
                exportBtn.style.background = '#6b7280';

                function updateStep() {
                    if (currentStep < steps.length) {
                        exportBtn.innerHTML = `<i class="fas fa-spinner fa-spin"></i><span>${steps[currentStep]}</span>`;
                        currentStep++;
                        setTimeout(updateStep, 1000);
                    } else {
                        setTimeout(() => {
                            exportBtn.innerHTML = originalBtnContent;
                            exportBtn.disabled = false;
                            exportBtn.style.background = '';
                            showNotification('Export completed successfully!', 'success');
                        }, 1000);
                    }
                }

                updateStep();
            }

            function showNotification(message, type) {
                const notification = document.createElement('div');
                notification.className = `notification ${type}`;
                notification.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}"></i>
                <span>${message}</span>
            `;

                document.body.appendChild(notification);

                // Animate in
                setTimeout(() => {
                    notification.classList.add('show');
                }, 100);

                // Remove after 4 seconds
                setTimeout(() => {
                    notification.classList.remove('show');
                    setTimeout(() => {
                        if (notification.parentNode) {
                            notification.parentNode.removeChild(notification);
                        }
                    }, 300);
                }, 4000);
            }

            // Make showNotification globally available
            window.showNotification = showNotification;
        });

        function selectAll() {
            document.querySelectorAll('.file-checkbox').forEach(cb => {
                cb.checked = true;
                const item = cb.closest('.file-item');
                if (item) {
                    item.classList.add('selected');
                }
            });
        }

        function deselectAll() {
            document.querySelectorAll('.file-checkbox').forEach(cb => {
                cb.checked = false;
                const item = cb.closest('.file-item');
                if (item) {
                    item.classList.remove('selected');
                }
            });
        }

        function toggleFile(item) {
            const checkbox = item.querySelector('.file-checkbox');
            if (checkbox) {
                checkbox.checked = !checkbox.checked;

                if (checkbox.checked) {
                    item.classList.add('selected');
                } else {
                    item.classList.remove('selected');
                }
            }
        }
    </script>
@endpush
