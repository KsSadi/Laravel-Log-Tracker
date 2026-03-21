@extends('log-tracker::theme.LiteFlow.layouts.app')

@section('title', 'Search Logs - Log Tracker')

@section('content')
    <div class="container my-4 page-content">

        {{-- Page Header --}}
        <div class="page-header">
            <h1>
                <i class="fas fa-search" style="color: var(--primary-color);"></i>
                Search Logs
            </h1>
            <p>Full-text search across all log files with level, date, and file filters</p>
        </div>

        {{-- Search Form --}}
        <div class="lf-card mb-4">
            <div class="lf-card-header">
                <i class="fas fa-sliders-h" style="color: var(--primary-color);"></i>
                <span class="lf-card-title">Search &amp; Filter</span>
            </div>
            <div class="lf-card-body">
                <form method="GET" action="{{ route('log-tracker.search') }}" id="searchForm">

                    {{-- Keyword --}}
                    <div class="lf-search-wrapper">
                        <i class="fas fa-search lf-search-icon"></i>
                        <input
                            type="text"
                            name="query"
                            id="queryInput"
                            class="lf-search-input"
                            placeholder="Search by keyword, exception class, message…"
                            value="{{ request('query') }}"
                            autocomplete="off"
                        >
                        <button type="button" class="lf-clear-btn" id="clearQuery" title="Clear">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    {{-- Filters Row --}}
                    <div class="lf-filters-row">
                        <div class="lf-filter-group">
                            <label for="levelSelect">Log Level</label>
                            <select name="level" id="levelSelect" class="lf-select">
                                <option value="">All Levels</option>
                                @foreach(array_keys($logConfig) as $lvl)
                                    @if($lvl !== 'total')
                                        <option value="{{ $lvl }}" {{ request('level') === $lvl ? 'selected' : '' }}>
                                            {{ ucfirst($lvl) }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="lf-filter-group">
                            <label for="dateFrom">Date From</label>
                            <input type="date" name="date_from" id="dateFrom" class="lf-input"
                                   value="{{ request('date_from') }}">
                        </div>

                        <div class="lf-filter-group">
                            <label for="dateTo">Date To</label>
                            <input type="date" name="date_to" id="dateTo" class="lf-input"
                                   value="{{ request('date_to') }}">
                        </div>

                        <div class="lf-filter-group">
                            <label for="fileSelect">Log File</label>
                            <select name="file" id="fileSelect" class="lf-select">
                                <option value="">All Files</option>
                                @foreach($logFiles as $logFile)
                                    <option value="{{ $logFile }}" {{ request('file') === $logFile ? 'selected' : '' }}>
                                        {{ $logFile }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="lf-actions">
                        <button type="submit" class="lf-btn-primary">
                            <i class="fas fa-search"></i> Search
                        </button>
                        <a href="{{ route('log-tracker.search') }}" class="lf-btn-secondary">
                            <i class="fas fa-undo-alt"></i> Reset
                        </a>
                    </div>

                </form>
            </div>
        </div>

        {{-- Validation Errors --}}
        @if($errors->any())
            <div class="alert alert-danger mb-3" style="border-radius: 8px; font-size: 0.9rem;">
                <i class="fas fa-exclamation-circle me-2"></i>{{ $errors->first() }}
            </div>
        @endif

        {{-- Results --}}
        @if($results !== null)
            <div class="lf-card">

                {{-- Results Header --}}
                <div class="lf-results-header">
                    <div class="lf-results-title">
                        <i class="fas fa-list-ul" style="color: var(--primary-color);"></i>
                        Search Results
                    </div>
                    <span class="lf-results-meta">
                        @if($results['total'] > 0)
                            Showing {{ number_format($results['from']) }}–{{ number_format($results['to']) }}
                            of <strong>{{ number_format($results['total']) }}</strong>
                            {{ Str::plural('entry', $results['total']) }}
                        @else
                            No entries found
                        @endif
                    </span>
                </div>

                {{-- Active Filters --}}
                @php
                    $activeFilters = array_filter([
                        'query'     => request('query'),
                        'level'     => request('level'),
                        'date_from' => request('date_from'),
                        'date_to'   => request('date_to'),
                        'file'      => request('file'),
                    ]);
                @endphp
                @if(count($activeFilters))
                    <div class="lf-chips-bar">
                        <span class="lf-chips-label">Active filters:</span>
                        @foreach($activeFilters as $key => $value)
                            <span class="lf-chip">
                                {{ ucfirst(str_replace('_', ' ', $key)) }}: {{ $value }}
                            </span>
                        @endforeach
                    </div>
                @endif

                @if($results['total'] > 0)
                    <div class="table-responsive">
                        <table class="lf-table">
                            <thead>
                                <tr>
                                    <th class="col-file">File</th>
                                    <th class="col-time">Timestamp</th>
                                    <th class="col-level">Level</th>
                                    <th class="col-msg">Message</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($results['entries'] as $entry)
                                    <tr>
                                        <td>
                                            <a href="{{ route('log-tracker.show', $entry['file']) }}"
                                               class="lf-file-link" title="{{ $entry['file'] }}">
                                                {{ $entry['file'] }}
                                            </a>
                                        </td>
                                        <td class="lf-timestamp">{{ $entry['timestamp'] }}</td>
                                        <td>
                                            <span class="lf-badge" style="background-color: {{ $entry['color'] }};">
                                                <i class="{{ $entry['icon'] }}"></i>
                                                {{ ucfirst($entry['level']) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="lf-message">
                                                @php
                                                    $message = e($entry['message']);
                                                    $q = request('query');
                                                    if ($q && strlen(trim($q)) > 0) {
                                                        $message = preg_replace(
                                                            '/('.preg_quote(e(trim($q)), '/').')/iu',
                                                            '<mark class="lf-highlight">$1</mark>',
                                                            $message
                                                        );
                                                    }
                                                @endphp
                                                {!! $message !!}
                                            </div>
                                            @if(!empty($entry['stack']))
                                                <button class="lf-stack-btn" onclick="toggleStack(this)" type="button">
                                                    <i class="fas fa-code"></i> Show stack trace
                                                </button>
                                                <div class="lf-stack">{{ $entry['stack'] }}</div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if($results['last_page'] > 1)
                        <div class="lf-pagination">
                            <span class="lf-page-info">
                                Page {{ $results['current_page'] }} of {{ $results['last_page'] }}
                            </span>
                            <div class="lf-page-links">
                                @if($results['current_page'] > 1)
                                    <a class="lf-page-btn"
                                       href="{{ request()->fullUrlWithQuery(['page' => $results['current_page'] - 1]) }}">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                @else
                                    <span class="lf-page-btn disabled"><i class="fas fa-chevron-left"></i></span>
                                @endif

                                @php
                                    $start = max(1, $results['current_page'] - 2);
                                    $end   = min($results['last_page'], $results['current_page'] + 2);
                                @endphp
                                @if($start > 1)
                                    <a class="lf-page-btn"
                                       href="{{ request()->fullUrlWithQuery(['page' => 1]) }}">1</a>
                                    @if($start > 2) <span class="lf-page-btn disabled">…</span> @endif
                                @endif
                                @for($p = $start; $p <= $end; $p++)
                                    <a class="lf-page-btn {{ $p === $results['current_page'] ? 'active' : '' }}"
                                       href="{{ request()->fullUrlWithQuery(['page' => $p]) }}">{{ $p }}</a>
                                @endfor
                                @if($end < $results['last_page'])
                                    @if($end < $results['last_page'] - 1)
                                        <span class="lf-page-btn disabled">…</span>
                                    @endif
                                    <a class="lf-page-btn"
                                       href="{{ request()->fullUrlWithQuery(['page' => $results['last_page']]) }}">
                                        {{ $results['last_page'] }}
                                    </a>
                                @endif

                                @if($results['current_page'] < $results['last_page'])
                                    <a class="lf-page-btn"
                                       href="{{ request()->fullUrlWithQuery(['page' => $results['current_page'] + 1]) }}">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                @else
                                    <span class="lf-page-btn disabled"><i class="fas fa-chevron-right"></i></span>
                                @endif
                            </div>
                        </div>
                    @endif

                @else
                    <div class="lf-empty-state">
                        <i class="fas fa-search-minus"></i>
                        <h4>No results found</h4>
                        <p>Try broadening your search or adjusting the filters.</p>
                    </div>
                @endif

            </div>

        @else
            {{-- Landing state --}}
            <div class="lf-card">
                <div class="lf-empty-state">
                    <i class="fas fa-search"></i>
                    <h4>Enter a keyword or select filters above</h4>
                    <p>Search across all log files at once — filter by level, date range, or specific file.</p>
                </div>
            </div>
        @endif

    </div>
@endsection

@push('styles')
<style>
    /* ─── Card ─── */
    .lf-card {
        background: white;
        border: 1px solid var(--gray-200);
        border-radius: 12px;
        box-shadow: var(--shadow);
        overflow: hidden;
    }
    .lf-card-header {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--gray-200);
        background: var(--gray-50);
    }
    .lf-card-title { font-weight: 600; font-size: 0.95rem; color: var(--gray-800); }
    .lf-card-body  { padding: 1.5rem; }

    /* ─── Search Input ─── */
    .lf-search-wrapper {
        position: relative;
        margin-bottom: 1.25rem;
    }
    .lf-search-icon {
        position: absolute;
        left: 0.95rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--gray-400);
        pointer-events: none;
    }
    .lf-search-input {
        width: 100%;
        padding: 0.75rem 2.5rem 0.75rem 2.5rem;
        border: 1px solid var(--gray-300);
        border-radius: var(--border-radius);
        font-size: 0.95rem;
        outline: none;
        transition: var(--transition);
        background: white;
        color: var(--gray-900);
    }
    .lf-search-input:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
    }
    .lf-clear-btn {
        position: absolute;
        right: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: var(--gray-400);
        cursor: pointer;
        padding: 0.2rem;
        border-radius: 50%;
        display: none;
        transition: var(--transition);
    }
    .lf-clear-btn:hover { color: var(--danger-color); }

    /* ─── Filters Row ─── */
    .lf-filters-row {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 1rem;
        margin-bottom: 1.25rem;
    }
    .lf-filter-group label {
        display: block;
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--gray-600);
        margin-bottom: 0.35rem;
    }
    .lf-select, .lf-input {
        width: 100%;
        padding: 0.55rem 0.85rem;
        border: 1px solid var(--gray-300);
        border-radius: var(--border-radius);
        font-size: 0.875rem;
        background: white;
        color: var(--gray-900);
        outline: none;
        transition: var(--transition);
    }
    .lf-select:focus, .lf-input:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
    }

    /* ─── Actions ─── */
    .lf-actions { display: flex; gap: 0.75rem; flex-wrap: wrap; }
    .lf-btn-primary {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.6rem 1.5rem;
        background-color: var(--primary-color);
        color: white;
        border: none;
        border-radius: var(--border-radius);
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition);
        text-decoration: none;
    }
    .lf-btn-primary:hover { opacity: 0.88; }
    .lf-btn-secondary {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.6rem 1.25rem;
        background: white;
        color: var(--gray-600);
        border: 1px solid var(--gray-300);
        border-radius: var(--border-radius);
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: var(--transition);
        text-decoration: none;
    }
    .lf-btn-secondary:hover { border-color: var(--gray-400); color: var(--gray-800); }

    /* ─── Results Header ─── */
    .lf-results-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.5rem;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--gray-200);
        background: var(--gray-50);
    }
    .lf-results-title {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 600;
        font-size: 0.95rem;
        color: var(--gray-800);
    }
    .lf-results-meta { font-size: 0.85rem; color: var(--gray-500); }

    /* ─── Chips ─── */
    .lf-chips-bar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.5rem;
        padding: 0.6rem 1.5rem;
        border-bottom: 1px solid var(--gray-100);
        background: #eff6ff;
    }
    .lf-chips-label { font-size: 0.78rem; font-weight: 600; color: var(--gray-500); }
    .lf-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        background: #dbeafe;
        color: var(--primary-color);
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    /* ─── Table ─── */
    .lf-table { width: 100%; border-collapse: collapse; }
    .lf-table th {
        background: var(--gray-50);
        padding: 0.75rem 1.25rem;
        text-align: left;
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--gray-500);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 1px solid var(--gray-200);
        white-space: nowrap;
    }
    .lf-table td {
        padding: 0.85rem 1.25rem;
        border-bottom: 1px solid var(--gray-100);
        vertical-align: top;
        font-size: 0.875rem;
        color: var(--gray-700);
    }
    .lf-table tr:last-child td { border-bottom: none; }
    .lf-table tr:hover td { background-color: var(--gray-50); }
    .lf-table .col-file  { width: 16%; }
    .lf-table .col-time  { width: 18%; }
    .lf-table .col-level { width: 10%; }
    .lf-table .col-msg   { width: 56%; }

    .lf-file-link {
        font-family: 'SF Mono', Monaco, Consolas, monospace;
        font-size: 0.78rem;
        color: var(--primary-color);
        text-decoration: none;
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        max-width: 140px;
    }
    .lf-file-link:hover { text-decoration: underline; }
    .lf-timestamp { font-size: 0.8rem; white-space: nowrap; color: var(--gray-600); }
    .lf-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.2rem 0.55rem;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 700;
        color: white;
        white-space: nowrap;
    }
    .lf-message { line-height: 1.5; color: var(--gray-800); word-break: break-word; }
    .lf-highlight { background: rgba(255, 220, 0, 0.4); border-radius: 2px; font-weight: 600; }
    .lf-stack-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        margin-top: 0.3rem;
        font-size: 0.75rem;
        color: var(--primary-color);
        background: none;
        border: none;
        padding: 0;
        cursor: pointer;
        font-weight: 500;
    }
    .lf-stack-btn:hover { text-decoration: underline; }
    .lf-stack {
        display: none;
        margin-top: 0.5rem;
        font-family: 'SF Mono', Monaco, Consolas, monospace;
        font-size: 0.72rem;
        color: var(--gray-600);
        background: var(--gray-50);
        border-left: 3px solid var(--gray-300);
        padding: 0.5rem 0.75rem;
        border-radius: 0 4px 4px 0;
        white-space: pre-wrap;
        word-break: break-all;
        max-height: 200px;
        overflow-y: auto;
    }

    /* ─── Pagination ─── */
    .lf-pagination {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
        padding: 1rem 1.5rem;
        border-top: 1px solid var(--gray-200);
        background: var(--gray-50);
    }
    .lf-page-info { font-size: 0.85rem; color: var(--gray-500); }
    .lf-page-links { display: flex; gap: 0.25rem; }
    .lf-page-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 34px;
        height: 34px;
        padding: 0 0.4rem;
        border: 1px solid var(--gray-300);
        background: white;
        color: var(--gray-600);
        border-radius: var(--border-radius);
        font-size: 0.85rem;
        font-weight: 500;
        text-decoration: none;
        transition: var(--transition);
    }
    .lf-page-btn:hover { border-color: var(--primary-color); color: var(--primary-color); }
    .lf-page-btn.active {
        background: var(--primary-color);
        border-color: var(--primary-color);
        color: white;
    }
    .lf-page-btn.disabled { opacity: 0.4; pointer-events: none; }

    /* ─── Empty State ─── */
    .lf-empty-state {
        padding: 3.5rem 2rem;
        text-align: center;
        color: var(--gray-400);
    }
    .lf-empty-state i   { font-size: 3rem; margin-bottom: 1rem; display: block; }
    .lf-empty-state h4  { font-size: 1rem; font-weight: 600; color: var(--gray-700); margin-bottom: 0.4rem; }
    .lf-empty-state p   { font-size: 0.875rem; color: var(--gray-500); margin: 0; }

    /* ─── Responsive ─── */
    @media (max-width: 768px) {
        .lf-filters-row { grid-template-columns: 1fr 1fr; }
        .lf-table .col-file, .lf-table td:nth-child(1) { display: none; }
    }
    @media (max-width: 576px) {
        .lf-filters-row { grid-template-columns: 1fr; }
    }
</style>
@endpush

@push('scripts')
<script>
    const queryInput = document.getElementById('queryInput');
    const clearBtn   = document.getElementById('clearQuery');

    function updateClearBtn() {
        clearBtn.style.display = queryInput.value.length > 0 ? 'flex' : 'none';
    }
    queryInput.addEventListener('input', updateClearBtn);
    clearBtn.addEventListener('click', () => {
        queryInput.value = '';
        updateClearBtn();
        queryInput.focus();
    });
    updateClearBtn();

    const dateFrom = document.getElementById('dateFrom');
    const dateTo   = document.getElementById('dateTo');
    dateFrom.addEventListener('change', () => {
        if (dateTo.value && dateTo.value < dateFrom.value) {
            dateTo.value = dateFrom.value;
        }
        dateTo.min = dateFrom.value;
    });
    if (dateFrom.value) { dateTo.min = dateFrom.value; }

    function toggleStack(btn) {
        const stack = btn.nextElementSibling;
        const isHidden = stack.style.display !== 'block';
        stack.style.display = isHidden ? 'block' : 'none';
        btn.innerHTML = isHidden
            ? '<i class="fas fa-code"></i> Hide stack trace'
            : '<i class="fas fa-code"></i> Show stack trace';
    }
</script>
@endpush
