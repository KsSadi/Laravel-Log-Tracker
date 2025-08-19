@extends('log-tracker::theme.GlowStack.layouts.app')

@section('title', 'Export Log - Log Tracker')

@push('styles')
    <style>
        .export-header {
            background: var(--primary-gradient);
            border-radius: var(--border-radius);
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-soft);
            color: white;
            position: relative;
            overflow: hidden;
        }

        .export-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 100px;
            height: 100px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        .format-selection {
            background: white;
            border-radius: var(--border-radius);
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-soft);
            border: 0;
        }

        .format-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        .format-card {
            position: relative;
            background: white;
            border: 2px solid #e9ecef;
            border-radius: var(--border-radius);
            padding: 2rem 1.5rem;
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
            overflow: hidden;
        }

        .format-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.8), transparent);
            transition: left 0.6s;
        }

        .format-card:hover::before {
            left: 100%;
        }

        .format-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
            border-color: #667eea;
        }

        .format-card.selected {
            background: var(--primary-gradient);
            color: white;
            border-color: #667eea;
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
        }

        .format-card .icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            display: block;
            opacity: 0.8;
            transition: var(--transition);
        }

        .format-card:hover .icon,
        .format-card.selected .icon {
            opacity: 1;
            transform: scale(1.1);
        }

        .format-card h6 {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .format-card small {
            opacity: 0.8;
            font-size: 0.85rem;
        }

        .main-content {
            display: grid;
            grid-template-columns: 350px 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .filters-panel {
            background: white;
            border-radius: var(--border-radius);
            padding: 2rem;
            height: fit-content;
            box-shadow: var(--shadow-soft);
            position: sticky;
            top: 2rem;
        }

        .filter-section {
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid #f0f0f0;
        }

        .filter-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .filter-title {
            display: flex;
            align-items: center;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }

        .filter-title i {
            margin-right: 0.5rem;
            color: #667eea;
        }

        .date-inputs {
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .date-input-group {
            display: flex;
            flex-direction: column;
        }

        .date-input-group label {
            font-size: 0.9rem;
            color: #4a5568;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            transition: var(--transition);
            font-size: 0.9rem;
            width: 100%;
            box-sizing: border-box;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            outline: none;
        }

        /* 🔧 FIXED: Level badges responsive layout */
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
            padding: 0.6rem;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 600;
            transition: var(--transition);
            width: 100%;
            opacity: 0.6;
            cursor: pointer;
            text-align: center;
            white-space: nowrap;
        }

        .level-badge input[type="checkbox"]:checked + .badge {
            opacity: 1;
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        .files-panel {
            background: white;
            border-radius: var(--border-radius);
            padding: 2rem;
            box-shadow: var(--shadow-soft);
        }

        .files-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f0f0f0;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .files-list {
            max-height: 400px;
            overflow-y: auto;
            padding-right: 1rem;
        }

        .file-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            margin-bottom: 0.75rem;
            background: #f8f9fa;
            border-radius: 10px;
            transition: var(--transition);
            border: 2px solid transparent;
            cursor: pointer;
        }

        .file-item:hover {
            background: #e3f2fd;
            border-color: #2196f3;
        }

        .file-item.selected {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            border-color: #2196f3;
        }

        .file-checkbox {
            width: 20px;
            height: 20px;
            margin-right: 1rem;
            accent-color: #667eea;
            flex-shrink: 0;
        }

        .file-info {
            flex: 1;
            min-width: 0;
        }

        .file-name {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 0.25rem;
            word-break: break-all;
        }

        .file-size {
            font-size: 0.8rem;
            color: #718096;
        }

        .export-actions {
            background: white;
            border-radius: var(--border-radius);
            padding: 2rem;
            box-shadow: var(--shadow-soft);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .export-info {
            display: flex;
            align-items: center;
            color: #4a5568;
            flex: 1;
            min-width: 250px;
        }

        .export-info i {
            margin-right: 0.5rem;
            color: #667eea;
            flex-shrink: 0;
        }

        .export-btn {
            background: var(--primary-gradient);
            border: none;
            padding: 1rem 2rem;
            border-radius: 50px;
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
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }

        .export-btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .select-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .btn-outline-custom {
            border: 2px solid #667eea;
            color: #667eea;
            background: transparent;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-size: 0.85rem;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            white-space: nowrap;
        }

        .btn-outline-custom:hover {
            background: #667eea;
            color: white;
            transform: translateY(-1px);
        }

        .breadcrumb-modern {
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(10px);
            border-radius: 50px;
            padding: 0.75rem 1.5rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(255,255,255,0.2);
        }

        .breadcrumb-modern .breadcrumb {
            margin-bottom: 0;
            background: none;
            padding: 0;
        }

        .breadcrumb-modern .breadcrumb-item a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }

        .breadcrumb-modern .breadcrumb-item.active {
            color: #2d3748;
            font-weight: 600;
        }

        /* 🔧 ENHANCED RESPONSIVE DESIGN */

        /* Large tablets and small desktops */
        @media (max-width: 1200px) {
            .main-content {
                grid-template-columns: 320px 1fr;
                gap: 1.5rem;
            }

            .filters-panel {
                padding: 1.5rem;
            }
        }

        /* Tablets */
        @media (max-width: 1024px) {
            .main-content {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .filters-panel {
                position: static;
                order: 2;
            }

            .files-panel {
                order: 1;
            }

            .format-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        /* Large phones and small tablets */
        @media (max-width: 768px) {
            .export-header {
                padding: 1.5rem;
                text-align: center;
            }

            .export-header .row {
                text-align: center;
            }

            .format-selection {
                padding: 1.5rem;
            }

            .format-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
            }

            .format-card {
                padding: 1.5rem 1rem;
            }

            .format-card .icon {
                font-size: 2.5rem;
            }

            /* 🔧 FIXED: Date inputs stack on mobile */
            .date-inputs {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            /* 🔧 FIXED: Level badges stack on mobile */
            .level-badges {
                grid-template-columns: 1fr;
                gap: 0.5rem;
            }

            .level-badge .badge {
                font-size: 0.75rem;
                padding: 0.5rem;
            }

            .filters-panel {
                padding: 1.5rem;
            }

            .files-panel {
                padding: 1.5rem;
            }

            .files-header {
                flex-direction: column;
                align-items: stretch;
                text-align: center;
            }

            .select-buttons {
                justify-content: center;
            }

            .export-actions {
                flex-direction: column;
                text-align: center;
                padding: 1.5rem;
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

        /* Small phones */
        @media (max-width: 480px) {
            .export-header {
                padding: 1rem;
            }

            .export-header h1 {
                font-size: 1.5rem;
            }

            .format-selection,
            .filters-panel,
            .files-panel,
            .export-actions {
                padding: 1rem;
            }

            .format-grid {
                grid-template-columns: 1fr;
                gap: 0.75rem;
            }

            .format-card {
                padding: 1rem;
            }

            /* 🔧 FIXED: Better spacing for small screens */
            .filter-section {
                margin-bottom: 1.5rem;
                padding-bottom: 1rem;
            }

            .filter-title {
                font-size: 1rem;
            }

            .form-control {
                padding: 0.6rem 0.8rem;
                font-size: 0.85rem;
            }

            .level-badge .badge {
                padding: 0.4rem;
                font-size: 0.7rem;
            }

            .file-item {
                padding: 0.8rem;
                margin-bottom: 0.5rem;
            }

            .file-name {
                font-size: 0.9rem;
            }

            .file-size {
                font-size: 0.75rem;
            }

            .breadcrumb-modern {
                padding: 0.5rem 1rem;
                margin-bottom: 1rem;
            }

            .btn-outline-custom {
                padding: 0.4rem 0.8rem;
                font-size: 0.8rem;
            }
        }

        /* Extra small screens */
        @media (max-width: 360px) {
            .export-header h1 {
                font-size: 1.3rem;
            }

            .format-card .icon {
                font-size: 2rem;
            }

            .format-card h6 {
                font-size: 1rem;
            }

            .level-badge .badge {
                padding: 0.3rem;
                font-size: 0.65rem;
            }
        }

        /* Custom Scrollbar */
        .files-list::-webkit-scrollbar {
            width: 6px;
        }

        .files-list::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .files-list::-webkit-scrollbar-thumb {
            background: #667eea;
            border-radius: 10px;
        }

        .files-list::-webkit-scrollbar-thumb:hover {
            background: #5a67d8;
        }

        /* 🔧 ADDITIONAL FIXES */

        /* Ensure inputs don't overflow on any screen */
        * {
            box-sizing: border-box;
        }

        /* Better text wrapping */
        .export-info span {
            word-break: break-word;
            line-height: 1.4;
        }

        /* Ensure buttons are always readable */
        .btn-outline-custom {
            min-height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Improve touch targets on mobile */
        @media (max-width: 768px) {
            .format-card,
            .file-item,
            .level-badge,
            .btn-outline-custom,
            .export-btn {
                min-height: 44px;
            }

            .file-checkbox {
                width: 24px;
                height: 24px;
            }
        }
    </style>
@endpush


@section('content')
    <!-- Modern Breadcrumb -->
    <div class="breadcrumb-modern">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('log-tracker.dashboard')}}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{route('log-tracker.index')}}">Log Files</a></li>
                <li class="breadcrumb-item active">Export</li>
            </ol>
        </nav>
    </div>

    <!-- Enhanced Header -->
    <div class="export-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="mb-2"><i class="fas fa-download me-3"></i>Export Your Logs</h1>
                <p class="mb-0 opacity-90">Generate professional reports in multiple formats with advanced filtering options</p>
            </div>
            <div class="col-md-4 text-end">
                <div style="font-size: 4rem; opacity: 0.2;">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('log-tracker.export') }}" method="POST" id="exportForm">
        @csrf

        <!-- Format Selection -->
        <div class="format-selection">
            <div class="filter-title">
                <i class="fas fa-file-alt"></i>
                Choose Export Format
            </div>
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

        <!-- Main Content Grid -->
        <div class="main-content">
            <!-- Filters Panel -->
            <div class="filters-panel">
                <div class="filter-title">
                    <i class="fas fa-filter"></i>
                    Filters
                </div>

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
                        @php
                            $logLevels = config('log-tracker.log_levels', [
                                'error' => ['color' => '#dc2626', 'icon' => 'fas fa-times-circle'],
                                'warning' => ['color' => '#d97706', 'icon' => 'fas fa-exclamation-triangle'],
                                'info' => ['color' => '#0284c7', 'icon' => 'fas fa-info-circle'],
                                'debug' => ['color' => '#059669', 'icon' => 'fas fa-bug']
                            ]);
                        @endphp
                        
                        @foreach($logLevels as $level => $config)
                            @if($level !== 'total')
                                <label class="level-badge">
                                    <input type="checkbox" name="levels[]" value="{{ $level }}" checked>
                                    <span class="badge" style="background-color: {{ $config['color'] }};">
                                    <i class="{{ $config['icon'] }} me-1"></i>{{ ucfirst($level) }}
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
                    <div class="filter-title">
                        <i class="fas fa-list"></i>
                        Select Log Files
                    </div>
                    <div class="select-buttons">
                        <button type="button" class="btn-outline-custom" onclick="selectAll()">Select All</button>
                        <button type="button" class="btn-outline-custom" onclick="deselectAll()">Deselect All</button>
                    </div>
                </div>

                <div class="files-list">
                    @foreach($logFiles as $file)
                        <div class="file-item" onclick="toggleFile(this)">
                            <input type="checkbox" name="log_files[]" value="{{ $file }}" class="file-checkbox" checked>
                            <div class="file-info">
                                <div class="file-name">{{ $file }}</div>
                                <div class="file-size">{{ round(filesize(storage_path('logs/' . $file)) / 1024, 2) }} KB</div>
                            </div>
                        </div>
                    @endforeach
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
                if (checkbox.checked) {
                    item.classList.add('selected');
                }

                checkbox.addEventListener('change', function() {
                    if (this.checked) {
                        item.classList.add('selected');
                    } else {
                        item.classList.remove('selected');
                    }
                });
            });

            // Form submission with enhanced loading
            const exportForm = document.getElementById('exportForm');
            const exportBtn = document.getElementById('exportBtn');
            const originalBtnContent = exportBtn.innerHTML;

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

            function startExportProcess(format) {
                const steps = [
                    'Preparing export...',
                    'Processing log files...',
                    `Generating ${format.toUpperCase()} file...`,
                    'Starting download...'
                ];

                let currentStep = 0;
                exportBtn.disabled = true;
                exportBtn.style.background = 'linear-gradient(135deg, #94a3b8 0%, #64748b 100%)';

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
                notification.className = `alert alert-${type} position-fixed`;
                notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px; border-radius: 10px; box-shadow: 0 10px 25px rgba(0,0,0,0.2);';
                notification.innerHTML = `
                        <div class="d-flex align-items-center">
                            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
                            <span>${message}</span>
                        </div>
                    `;

                document.body.appendChild(notification);

                // Animate in
                setTimeout(() => {
                    notification.style.transform = 'translateX(0)';
                    notification.style.opacity = '1';
                }, 100);

                // Remove after 4 seconds
                setTimeout(() => {
                    notification.style.transform = 'translateX(100%)';
                    notification.style.opacity = '0';
                    setTimeout(() => {
                        if (notification.parentNode) {
                            notification.parentNode.removeChild(notification);
                        }
                    }, 300);
                }, 4000);
            }
        });

        function selectAll() {
            document.querySelectorAll('.file-checkbox').forEach(cb => {
                cb.checked = true;
                cb.closest('.file-item').classList.add('selected');
            });
        }

        function deselectAll() {
            document.querySelectorAll('.file-checkbox').forEach(cb => {
                cb.checked = false;
                cb.closest('.file-item').classList.remove('selected');
            });
        }

        function toggleFile(item) {
            const checkbox = item.querySelector('.file-checkbox');
            checkbox.checked = !checkbox.checked;

            if (checkbox.checked) {
                item.classList.add('selected');
            } else {
                item.classList.remove('selected');
            }
        }
    </script>
@endpush
