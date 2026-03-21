@extends('log-tracker::theme.GlowStack.layouts.app')

@section('title', 'Search Logs - Log Tracker')

@push('styles')
<style>
    /* ─── Page Header ─── */
    .page-header {
        background: var(--primary-gradient);
        border-radius: var(--border-radius);
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: var(--shadow-soft);
        color: white;
        position: relative;
        overflow: hidden;
    }
    .page-header::before {
        content: '';
        position: absolute;
        top: -40%;
        right: -8%;
        width: 180px;
        height: 180px;
        background: rgba(255,255,255,0.08);
        border-radius: 50%;
        animation: float 8s ease-in-out infinite;
    }
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50%       { transform: translateY(-18px); }
    }
    .header-title {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 0.35rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .header-subtitle { opacity: 0.85; font-size: 0.95rem; margin: 0; }

    /* ─── Search Card ─── */
    .search-card {
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-soft);
        overflow: hidden;
        margin-bottom: 2rem;
    }
    .search-card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 1.25rem 1.75rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        color: white;
        font-weight: 600;
        font-size: 1rem;
    }
    .search-card-body { padding: 1.75rem; }

    /* ─── Search Input ─── */
    .search-input-wrapper {
        position: relative;
        margin-bottom: 1.5rem;
    }
    .search-input-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #667eea;
        font-size: 1.1rem;
        pointer-events: none;
    }
    .search-input {
        width: 100%;
        padding: 0.85rem 3rem 0.85rem 2.75rem;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 1rem;
        transition: var(--transition);
        background: #f8fafc;
        outline: none;
    }
    .search-input:focus {
        border-color: #667eea;
        background: white;
        box-shadow: 0 0 0 3px rgba(102,126,234,0.12);
    }
    .search-clear-btn {
        position: absolute;
        right: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #94a3b8;
        cursor: pointer;
        padding: 0.25rem;
        border-radius: 50%;
        display: none;
        transition: var(--transition);
    }
    .search-clear-btn:hover { color: #ef4444; background: #fee2e2; }

    /* ─── Filters Row ─── */
    .filters-row {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .filter-group label {
        display: block;
        font-size: 0.8rem;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-bottom: 0.4rem;
    }
    .filter-select, .filter-input {
        width: 100%;
        padding: 0.6rem 0.9rem;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        font-size: 0.9rem;
        background: #f8fafc;
        color: #374151;
        transition: var(--transition);
        outline: none;
        appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2364748b' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0.75rem center;
        padding-right: 2rem;
    }
    .filter-input {
        background-image: none;
        padding-right: 0.9rem;
    }
    .filter-select:focus, .filter-input:focus {
        border-color: #667eea;
        background-color: white;
        box-shadow: 0 0 0 3px rgba(102,126,234,0.12);
    }

    /* ─── Action Buttons ─── */
    .search-actions {
        display: flex;
        gap: 0.75rem;
        align-items: center;
        flex-wrap: wrap;
    }
    .btn-search {
        background: var(--primary-gradient);
        color: white;
        border: none;
        padding: 0.7rem 2rem;
        border-radius: 8px;
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: var(--transition);
        box-shadow: 0 4px 12px rgba(102,126,234,0.3);
    }
    .btn-search:hover { opacity: 0.9; transform: translateY(-1px); box-shadow: 0 6px 16px rgba(102,126,234,0.4); }
    .btn-reset {
        background: white;
        color: #64748b;
        border: 2px solid #e2e8f0;
        padding: 0.7rem 1.5rem;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 500;
        text-decoration: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: var(--transition);
    }
    .btn-reset:hover { border-color: #94a3b8; color: #374151; }

    /* ─── Results Card ─── */
    .results-card {
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-soft);
        overflow: hidden;
    }
    .results-header {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        padding: 1.25rem 1.75rem;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }
    .results-title {
        font-size: 1rem;
        font-weight: 600;
        color: #2d3748;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin: 0;
    }
    .results-meta { font-size: 0.85rem; color: #64748b; }

    /* ─── Active Filters Chips ─── */
    .active-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        padding: 0.75rem 1.75rem;
        border-bottom: 1px solid #f1f5f9;
        background: #fafbff;
    }
    .filter-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        background: rgba(102,126,234,0.1);
        color: #4f46e5;
        padding: 0.25rem 0.65rem;
        border-radius: 20px;
        font-size: 0.78rem;
        font-weight: 600;
    }
    .filter-chip i { font-size: 0.7rem; }

    /* ─── Results Table ─── */
    .results-table { width: 100%; border-collapse: collapse; }
    .results-table th {
        background: #f8fafc;
        padding: 0.85rem 1.25rem;
        text-align: left;
        font-size: 0.78rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 2px solid #e2e8f0;
        white-space: nowrap;
    }
    .results-table td {
        padding: 0.9rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: top;
    }
    .results-table tr:last-child td { border-bottom: none; }
    .results-table tr:hover td { background: rgba(102,126,234,0.03); }
    .results-table th.col-file      { width: 16%; }
    .results-table th.col-time      { width: 18%; }
    .results-table th.col-level     { width: 10%; }
    .results-table th.col-message   { width: 56%; }

    .entry-file {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.78rem;
        color: #64748b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 140px;
        display: block;
    }
    .entry-file-link { color: #667eea; text-decoration: none; }
    .entry-file-link:hover { text-decoration: underline; }

    .entry-timestamp {
        font-size: 0.82rem;
        color: #374151;
        white-space: nowrap;
    }
    .entry-level-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.25rem 0.65rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        color: white;
        white-space: nowrap;
    }
    .entry-message {
        font-size: 0.875rem;
        color: #1e293b;
        line-height: 1.5;
        word-break: break-word;
    }
    .entry-message.has-stack { cursor: pointer; }
    .entry-stack {
        display: none;
        margin-top: 0.5rem;
        font-family: 'JetBrains Mono', 'Fira Code', monospace;
        font-size: 0.75rem;
        color: #64748b;
        background: #f8fafc;
        border-left: 3px solid #e2e8f0;
        padding: 0.6rem 0.75rem;
        border-radius: 0 6px 6px 0;
        white-space: pre-wrap;
        word-break: break-all;
        max-height: 200px;
        overflow-y: auto;
    }
    .toggle-stack-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        margin-top: 0.35rem;
        font-size: 0.75rem;
        color: #667eea;
        background: none;
        border: none;
        padding: 0;
        cursor: pointer;
        font-weight: 500;
    }
    .toggle-stack-btn:hover { color: #4f46e5; text-decoration: underline; }
    .highlight {
        background: rgba(255, 230, 0, 0.35);
        border-radius: 2px;
        padding: 0 1px;
        font-weight: 600;
    }

    /* ─── Pagination ─── */
    .pagination-wrapper {
        padding: 1.25rem 1.75rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
        border-top: 1px solid #e2e8f0;
        background: #f8fafc;
    }
    .pagination-info { font-size: 0.85rem; color: #64748b; }
    .pagination-links { display: flex; gap: 0.35rem; align-items: center; }
    .page-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 36px;
        padding: 0 0.5rem;
        border: 2px solid #e2e8f0;
        background: white;
        color: #374151;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 500;
        text-decoration: none;
        transition: var(--transition);
    }
    .page-btn:hover { border-color: #667eea; color: #667eea; }
    .page-btn.active {
        background: var(--primary-gradient);
        border-color: transparent;
        color: white;
        box-shadow: 0 4px 10px rgba(102,126,234,0.3);
    }
    .page-btn.disabled { opacity: 0.4; pointer-events: none; }

    /* ─── Empty / Landing States ─── */
    .state-box {
        padding: 4rem 2rem;
        text-align: center;
    }
    .state-icon {
        font-size: 3.5rem;
        margin-bottom: 1rem;
        opacity: 0.35;
    }
    .state-title { font-size: 1.15rem; font-weight: 600; color: #2d3748; margin-bottom: 0.5rem; }
    .state-subtitle { font-size: 0.9rem; color: #64748b; }

    /* ─── Responsive ─── */
    @media (max-width: 768px) {
        .filters-row { grid-template-columns: 1fr 1fr; }
        .results-table th.col-file, .results-table td:nth-child(1) { display: none; }
    }
    @media (max-width: 576px) {
        .filters-row { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')

    {{-- Page Header --}}
    <div class="page-header">
        <h1 class="header-title">
            <i class="fas fa-search"></i>
            Search Logs
        </h1>
        <p class="header-subtitle">Full-text search across all log files with level, date, and file filters</p>
    </div>

    {{-- Search Form --}}
    <div class="search-card">
        <div class="search-card-header">
            <i class="fas fa-sliders-h"></i>
            Search &amp; Filter
        </div>
        <div class="search-card-body">
            <form method="GET" action="{{ route('log-tracker.search') }}" id="searchForm">

                {{-- Keyword --}}
                <div class="search-input-wrapper">
                    <i class="fas fa-search search-input-icon"></i>
                    <input
                        type="text"
                        name="query"
                        id="queryInput"
                        class="search-input"
                        placeholder="Search by keyword, exception class, message…"
                        value="{{ request('query') }}"
                        autocomplete="off"
                    >
                    <button type="button" class="search-clear-btn" id="clearQuery" title="Clear">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                {{-- Filters Row --}}
                <div class="filters-row">
                    {{-- Level --}}
                    <div class="filter-group">
                        <label for="levelSelect"><i class="fas fa-layer-group"></i> Log Level</label>
                        <select name="level" id="levelSelect" class="filter-select">
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

                    {{-- Date From --}}
                    <div class="filter-group">
                        <label for="dateFrom"><i class="fas fa-calendar-alt"></i> Date From</label>
                        <input type="date" name="date_from" id="dateFrom" class="filter-input"
                               value="{{ request('date_from') }}">
                    </div>

                    {{-- Date To --}}
                    <div class="filter-group">
                        <label for="dateTo"><i class="fas fa-calendar-check"></i> Date To</label>
                        <input type="date" name="date_to" id="dateTo" class="filter-input"
                               value="{{ request('date_to') }}">
                    </div>

                    {{-- File --}}
                    <div class="filter-group">
                        <label for="fileSelect"><i class="fas fa-file-alt"></i> Log File</label>
                        <select name="file" id="fileSelect" class="filter-select">
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
                <div class="search-actions">
                    <button type="submit" class="btn-search">
                        <i class="fas fa-search"></i>
                        Search
                    </button>
                    <a href="{{ route('log-tracker.search') }}" class="btn-reset">
                        <i class="fas fa-undo-alt"></i>
                        Reset
                    </a>
                </div>

            </form>
        </div>
    </div>

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="alert alert-danger mb-3" style="border-radius: 10px;">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ $errors->first() }}
        </div>
    @endif

    {{-- Results --}}
    @if($results !== null)
        <div class="results-card">
            {{-- Results Header --}}
            <div class="results-header">
                <h2 class="results-title">
                    <i class="fas fa-list-ul" style="color:#667eea;"></i>
                    Search Results
                </h2>
                <span class="results-meta">
                    @if($results['total'] > 0)
                        Showing {{ number_format($results['from']) }}–{{ number_format($results['to']) }}
                        of <strong>{{ number_format($results['total']) }}</strong> matching
                        {{ Str::plural('entry', $results['total']) }}
                    @else
                        No entries found
                    @endif
                </span>
            </div>

            {{-- Active Filters Chips --}}
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
                <div class="active-filters">
                    <span style="font-size:0.78rem;font-weight:600;color:#64748b;align-self:center;">Filters:</span>
                    @foreach($activeFilters as $key => $value)
                        <span class="filter-chip">
                            <i class="fas fa-tag"></i>
                            {{ ucfirst(str_replace('_', ' ', $key)) }}: {{ $value }}
                        </span>
                    @endforeach
                </div>
            @endif

            @if($results['total'] > 0)
                <div style="overflow-x: auto;">
                    <table class="results-table">
                        <thead>
                            <tr>
                                <th class="col-file">File</th>
                                <th class="col-time">Timestamp</th>
                                <th class="col-level">Level</th>
                                <th class="col-message">Message</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($results['entries'] as $i => $entry)
                                <tr>
                                    <td>
                                        <a href="{{ route('log-tracker.show', $entry['file']) }}"
                                           class="entry-file-link"
                                           title="{{ $entry['file'] }}">
                                            <span class="entry-file">{{ $entry['file'] }}</span>
                                        </a>
                                    </td>
                                    <td>
                                        <span class="entry-timestamp">{{ $entry['timestamp'] }}</span>
                                    </td>
                                    <td>
                                        <span class="entry-level-badge" style="background:{{ $entry['color'] }};">
                                            <i class="{{ $entry['icon'] }}"></i>
                                            {{ ucfirst($entry['level']) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="entry-message {{ !empty($entry['stack']) ? 'has-stack' : '' }}">
                                            @php
                                                $message = e($entry['message']);
                                                $q = request('query');
                                                if ($q && strlen(trim($q)) > 0) {
                                                    $message = preg_replace(
                                                        '/('.preg_quote(e(trim($q)), '/').')/iu',
                                                        '<mark class="highlight">$1</mark>',
                                                        $message
                                                    );
                                                }
                                            @endphp
                                            {!! $message !!}
                                        </div>
                                        @if(!empty($entry['stack']))
                                            <button class="toggle-stack-btn" onclick="toggleStack(this)" type="button">
                                                <i class="fas fa-code"></i> Show stack trace
                                            </button>
                                            <div class="entry-stack">{{ $entry['stack'] }}</div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($results['last_page'] > 1)
                    <div class="pagination-wrapper">
                        <span class="pagination-info">
                            Page {{ $results['current_page'] }} of {{ $results['last_page'] }}
                        </span>
                        <div class="pagination-links">
                            {{-- Previous --}}
                            @if($results['current_page'] > 1)
                                <a class="page-btn"
                                   href="{{ request()->fullUrlWithQuery(['page' => $results['current_page'] - 1]) }}">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            @else
                                <span class="page-btn disabled"><i class="fas fa-chevron-left"></i></span>
                            @endif

                            {{-- Page Numbers --}}
                            @php
                                $start = max(1, $results['current_page'] - 2);
                                $end   = min($results['last_page'], $results['current_page'] + 2);
                            @endphp
                            @if($start > 1)
                                <a class="page-btn" href="{{ request()->fullUrlWithQuery(['page' => 1]) }}">1</a>
                                @if($start > 2) <span class="page-btn disabled">…</span> @endif
                            @endif
                            @for($p = $start; $p <= $end; $p++)
                                <a class="page-btn {{ $p === $results['current_page'] ? 'active' : '' }}"
                                   href="{{ request()->fullUrlWithQuery(['page' => $p]) }}">
                                    {{ $p }}
                                </a>
                            @endfor
                            @if($end < $results['last_page'])
                                @if($end < $results['last_page'] - 1)
                                    <span class="page-btn disabled">…</span>
                                @endif
                                <a class="page-btn"
                                   href="{{ request()->fullUrlWithQuery(['page' => $results['last_page']]) }}">
                                    {{ $results['last_page'] }}
                                </a>
                            @endif

                            {{-- Next --}}
                            @if($results['current_page'] < $results['last_page'])
                                <a class="page-btn"
                                   href="{{ request()->fullUrlWithQuery(['page' => $results['current_page'] + 1]) }}">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            @else
                                <span class="page-btn disabled"><i class="fas fa-chevron-right"></i></span>
                            @endif
                        </div>
                    </div>
                @endif

            @else
                <div class="state-box">
                    <div class="state-icon"><i class="fas fa-search-minus"></i></div>
                    <div class="state-title">No results found</div>
                    <div class="state-subtitle">Try broadening your search or adjusting the filters.</div>
                </div>
            @endif
        </div>

    @else
        {{-- Landing state — no search performed yet --}}
        <div class="results-card">
            <div class="state-box">
                <div class="state-icon"><i class="fas fa-search"></i></div>
                <div class="state-title">Enter a keyword or select filters above</div>
                <div class="state-subtitle">
                    Search across all log files at once — filter by level, date range, or specific file.
                </div>
            </div>
        </div>
    @endif

@endsection

@push('scripts')
<script>
    // Show/hide clear button for search query input
    const queryInput   = document.getElementById('queryInput');
    const clearBtn     = document.getElementById('clearQuery');

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

    // Enforce date_from <= date_to
    const dateFrom = document.getElementById('dateFrom');
    const dateTo   = document.getElementById('dateTo');
    dateFrom.addEventListener('change', () => {
        if (dateTo.value && dateTo.value < dateFrom.value) {
            dateTo.value = dateFrom.value;
        }
        dateTo.min = dateFrom.value;
    });
    if (dateFrom.value) {
        dateTo.min = dateFrom.value;
    }

    // Toggle stack trace visibility
    function toggleStack(btn) {
        const stackEl = btn.nextElementSibling;
        const isHidden = stackEl.style.display !== 'block';
        stackEl.style.display = isHidden ? 'block' : 'none';
        btn.innerHTML = isHidden
            ? '<i class="fas fa-code"></i> Hide stack trace'
            : '<i class="fas fa-code"></i> Show stack trace';
    }
</script>
@endpush
