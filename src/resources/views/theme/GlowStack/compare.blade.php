@extends('log-tracker::theme.GlowStack.layouts.app')

@section('title', 'Compare Logs - Log Tracker')

@push('styles')
<style>
    /* ─── Page Header ─── */
    .page-header {
        background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 100%);
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
        background: rgba(255,255,255,0.07);
        border-radius: 50%;
    }
    .page-header h1 { font-size: 1.8rem; font-weight: 700; margin: 0 0 0.4rem; }
    .page-header p  { margin: 0; opacity: 0.85; font-size: 0.95rem; }

    /* ─── Compare Form Card ─── */
    .cmp-card {
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-soft);
        overflow: hidden;
        margin-bottom: 1.5rem;
    }
    .cmp-card-header {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-bottom: 1px solid #e2e8f0;
        padding: 1rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.6rem;
        font-weight: 700;
        color: #1e293b;
        font-size: 0.95rem;
    }
    .cmp-card-body { padding: 1.5rem; }

    /* ─── File Pickers ─── */
    .cmp-pickers {
        display: grid;
        grid-template-columns: 1fr auto 1fr;
        gap: 1rem;
        align-items: end;
    }
    .cmp-picker label {
        display: block;
        font-size: 0.82rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-bottom: 0.4rem;
    }
    .cmp-select {
        width: 100%;
        padding: 0.6rem 0.9rem;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-size: 0.88rem;
        color: #1e293b;
        background: white;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3E%3Cpath fill='%2364748b' d='M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0.6rem center;
        background-size: 1rem;
        padding-right: 2.2rem;
        transition: border-color 0.15s;
    }
    .cmp-select:focus { outline: none; border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,0.12); }
    .cmp-vs {
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.1rem;
        color: #94a3b8;
        padding-bottom: 0.1rem;
    }
    .cmp-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.6rem 1.4rem;
        background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 100%);
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.88rem;
        cursor: pointer;
        transition: opacity 0.15s;
        margin-top: 0.8rem;
    }
    .cmp-btn:hover { opacity: 0.9; }

    /* ─── Stats Row ─── */
    .cmp-stats {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .cmp-stat {
        background: white;
        border-radius: 10px;
        box-shadow: var(--shadow-soft);
        padding: 1rem;
        text-align: center;
    }
    .cmp-stat-num {
        font-size: 1.8rem;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 0.3rem;
    }
    .cmp-stat-label { font-size: 0.72rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; }

    /* ─── Level breakdown table ─── */
    .cmp-level-table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
    .cmp-level-table th {
        background: #f8fafc;
        padding: 0.6rem 1rem;
        text-align: left;
        font-weight: 700;
        color: #64748b;
        font-size: 0.75rem;
        text-transform: uppercase;
        border-bottom: 2px solid #e2e8f0;
    }
    .cmp-level-table th:last-child, .cmp-level-table td:last-child { text-align: right; }
    .cmp-level-table th:nth-child(2), .cmp-level-table td:nth-child(2) { text-align: right; }
    .cmp-level-table th:nth-child(3), .cmp-level-table td:nth-child(3) { text-align: right; }
    .cmp-level-table td { padding: 0.55rem 1rem; border-bottom: 1px solid #f1f5f9; }
    .cmp-level-table tr:last-child td { border-bottom: none; }
    .cmp-level-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.18rem 0.6rem;
        border-radius: 20px;
        font-size: 0.72rem;
        font-weight: 700;
        color: white;
    }
    .cmp-diff-pos { color: #22c55e; font-weight: 700; }
    .cmp-diff-neg { color: #ef4444; font-weight: 700; }
    .cmp-diff-zero { color: #94a3b8; }

    /* ─── Entry sections ─── */
    .cmp-entries { display: flex; flex-direction: column; gap: 0.5rem; }
    .cmp-entry {
        display: flex;
        gap: 0.75rem;
        align-items: flex-start;
        background: #fafafa;
        border: 1px solid #f0f0f0;
        border-radius: 8px;
        padding: 0.7rem 1rem;
        font-size: 0.84rem;
        transition: background 0.12s;
    }
    .cmp-entry:hover { background: #f5f5f5; }
    .cmp-entry-a  { border-left: 3px solid #ef4444; }
    .cmp-entry-b  { border-left: 3px solid #2563eb; }
    .cmp-entry-shared { border-left: 3px solid #22c55e; }
    .cmp-entry-msg { flex: 1; min-width: 0; font-family: 'JetBrains Mono', monospace; word-break: break-word; color: #1e293b; }
    .cmp-entry-ts { font-size: 0.75rem; color: #94a3b8; white-space: nowrap; padding-top: 0.1rem; }
    .cmp-badge {
        display: inline-flex; align-items: center; gap: 0.25rem;
        padding: 0.15rem 0.5rem; border-radius: 20px;
        font-size: 0.7rem; font-weight: 700; color: white; white-space: nowrap;
    }

    /* ─── Tab navigation ─── */
    .cmp-tabs { display: flex; gap: 0; border-bottom: 2px solid #e2e8f0; margin-bottom: 1rem; }
    .cmp-tab {
        padding: 0.6rem 1.2rem;
        font-size: 0.84rem;
        font-weight: 600;
        cursor: pointer;
        border: none;
        background: none;
        color: #64748b;
        border-bottom: 2px solid transparent;
        margin-bottom: -2px;
        transition: color 0.15s, border-color 0.15s;
    }
    .cmp-tab.active { color: #2563eb; border-bottom-color: #2563eb; }
    .cmp-tab-count {
        background: #e2e8f0;
        border-radius: 20px;
        padding: 0.1rem 0.5rem;
        font-size: 0.72rem;
        margin-left: 0.35rem;
    }
    .cmp-tab.active .cmp-tab-count { background: #dbeafe; color: #1d4ed8; }
    .cmp-tab-panel { display: none; }
    .cmp-tab-panel.active { display: block; }

    /* ─── Empty state ─── */
    .cmp-empty {
        text-align: center;
        padding: 2rem;
        color: #94a3b8;
        font-size: 0.9rem;
    }
    .cmp-empty i { font-size: 2rem; display: block; margin-bottom: 0.5rem; }

    /* ─── Table scroll ─── */
    .cmp-table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }

    /* ─── Tab overflow ─── */
    .cmp-tabs { overflow-x: auto; flex-wrap: nowrap; }
    .cmp-tab  { flex-shrink: 0; }

    /* ─── Responsive: tablet ─── */
    @media (max-width: 768px) {
        .page-header h1    { font-size: 1.3rem; }
        .cmp-pickers       { grid-template-columns: 1fr; }
        .cmp-vs            { display: none; }
        .cmp-stats         { grid-template-columns: repeat(2, 1fr); }
        .cmp-entry-ts      { display: none; }
    }

    /* ─── Responsive: phone ─── */
    @media (max-width: 480px) {
        .cmp-stats         { grid-template-columns: 1fr 1fr; }
        .cmp-card-body     { padding: 1rem; }
        .cmp-entry         { flex-wrap: wrap; gap: 0.4rem; }
        .cmp-entry-msg     { width: 100%; }
        .cmp-tab           { padding: 0.5rem 0.75rem; font-size: 0.78rem; }
    }
</style>
@endpush

@section('content')
<div class="container my-4 page-content">

    {{-- Page Header --}}
    <div class="page-header">
        <h1><i class="fas fa-code-branch me-2"></i>Compare Log Files</h1>
        <p>Side-by-side diff — see what's unique to each file and what's shared</p>
    </div>

    {{-- Picker Form --}}
    <div class="cmp-card">
        <div class="cmp-card-header">
            <i class="fas fa-sliders-h" style="color:#2563eb"></i>
            Select Files to Compare
        </div>
        <div class="cmp-card-body">
            <form method="GET" action="{{ route('log-tracker.compare') }}">
                <div class="cmp-pickers">
                    <div class="cmp-picker">
                        <label><i class="fas fa-file-alt me-1"></i> File A</label>
                        <select name="file_a" class="cmp-select" required>
                            <option value="">— choose file A —</option>
                            @foreach($logFiles as $file)
                                <option value="{{ $file }}" @selected($fileA === $file)>{{ $file }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="cmp-vs">VS</div>
                    <div class="cmp-picker">
                        <label><i class="fas fa-file-alt me-1"></i> File B</label>
                        <select name="file_b" class="cmp-select" required>
                            <option value="">— choose file B —</option>
                            @foreach($logFiles as $file)
                                <option value="{{ $file }}" @selected($fileB === $file)>{{ $file }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @if($fileA === $fileB && $fileA)
                    <p style="color:#ef4444;font-size:0.83rem;margin-top:0.5rem;">
                        <i class="fas fa-exclamation-triangle"></i> Please select two different files.
                    </p>
                @endif
                <button type="submit" class="cmp-btn">
                    <i class="fas fa-code-branch"></i> Compare
                </button>
            </form>
        </div>
    </div>

    @if($comparison)
    {{-- Summary Stats --}}
    <div class="cmp-stats">
        <div class="cmp-stat">
            <div class="cmp-stat-num" style="color:#2563eb">{{ number_format($comparison['totalA']) }}</div>
            <div class="cmp-stat-label">Total in A</div>
        </div>
        <div class="cmp-stat">
            <div class="cmp-stat-num" style="color:#7c3aed">{{ number_format($comparison['totalB']) }}</div>
            <div class="cmp-stat-label">Total in B</div>
        </div>
        <div class="cmp-stat">
            <div class="cmp-stat-num" style="color:#22c55e">{{ count($comparison['shared']) }}</div>
            <div class="cmp-stat-label">Shared</div>
        </div>
        <div class="cmp-stat">
            <div class="cmp-stat-num" style="color:#ef4444">{{ count($comparison['onlyInA']) }}</div>
            <div class="cmp-stat-label">Only in A</div>
        </div>
        <div class="cmp-stat">
            <div class="cmp-stat-num" style="color:#f97316">{{ count($comparison['onlyInB']) }}</div>
            <div class="cmp-stat-label">Only in B</div>
        </div>
    </div>

    {{-- Level Breakdown --}}
    <div class="cmp-card">
        <div class="cmp-card-header">
            <i class="fas fa-chart-bar" style="color:#2563eb"></i>
            Level Breakdown
        </div>
        <div class="cmp-card-body" style="padding:0">
            <div class="cmp-table-wrap">
            <table class="cmp-level-table">
                <thead>
                    <tr>
                        <th>Level</th>
                        <th>{{ basename($comparison['fileA']) }}</th>
                        <th>{{ basename($comparison['fileB']) }}</th>
                        <th>Δ Diff</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($comparison['allLevels'] as $level)
                        @php
                            $logConfig = config('log-tracker.log_levels', []);
                            $cntA = $comparison['levelsA'][$level] ?? 0;
                            $cntB = $comparison['levelsB'][$level] ?? 0;
                            $diff = $cntB - $cntA;
                            $color = $logConfig[$level]['color'] ?? '#6c757d';
                            $icon  = $logConfig[$level]['icon'] ?? 'fas fa-circle';
                        @endphp
                        <tr>
                            <td>
                                <span class="cmp-level-pill" style="background:{{ $color }}">
                                    <i class="{{ $icon }}"></i> {{ strtoupper($level) }}
                                </span>
                            </td>
                            <td>{{ $cntA }}</td>
                            <td>{{ $cntB }}</td>
                            <td>
                                @if($diff > 0)
                                    <span class="cmp-diff-pos">+{{ $diff }}</span>
                                @elseif($diff < 0)
                                    <span class="cmp-diff-neg">{{ $diff }}</span>
                                @else
                                    <span class="cmp-diff-zero">0</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        </div>
    </div>

    {{-- Entry Diff Tabs --}}
    <div class="cmp-card">
        <div class="cmp-card-header">
            <i class="fas fa-list-alt" style="color:#2563eb"></i>
            Entry Differences
        </div>
        <div class="cmp-card-body">
            <div class="cmp-tabs">
                <button class="cmp-tab active" onclick="cmpTab(event,'tab-only-a')">
                    <i class="fas fa-minus-circle" style="color:#ef4444"></i>
                    Only in A
                    <span class="cmp-tab-count">{{ count($comparison['onlyInA']) }}</span>
                </button>
                <button class="cmp-tab" onclick="cmpTab(event,'tab-only-b')">
                    <i class="fas fa-plus-circle" style="color:#2563eb"></i>
                    Only in B
                    <span class="cmp-tab-count">{{ count($comparison['onlyInB']) }}</span>
                </button>
                <button class="cmp-tab" onclick="cmpTab(event,'tab-shared')">
                    <i class="fas fa-equals" style="color:#22c55e"></i>
                    Shared
                    <span class="cmp-tab-count">{{ count($comparison['shared']) }}</span>
                </button>
            </div>

            {{-- Only in A --}}
            <div id="tab-only-a" class="cmp-tab-panel active">
                @if(count($comparison['onlyInA']) > 0)
                    <div class="cmp-entries">
                        @foreach($comparison['onlyInA'] as $entry)
                        <div class="cmp-entry cmp-entry-a">
                            <span class="cmp-badge" style="background:{{ $entry['color'] }}">
                                <i class="{{ $entry['icon'] }}"></i> {{ strtoupper($entry['level']) }}
                            </span>
                            <span class="cmp-entry-msg">{{ $entry['message'] }}</span>
                            <span class="cmp-entry-ts">{{ $entry['timestamp'] }}</span>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="cmp-empty"><i class="fas fa-check-circle" style="color:#22c55e"></i>No entries unique to file A</div>
                @endif
            </div>

            {{-- Only in B --}}
            <div id="tab-only-b" class="cmp-tab-panel">
                @if(count($comparison['onlyInB']) > 0)
                    <div class="cmp-entries">
                        @foreach($comparison['onlyInB'] as $entry)
                        <div class="cmp-entry cmp-entry-b">
                            <span class="cmp-badge" style="background:{{ $entry['color'] }}">
                                <i class="{{ $entry['icon'] }}"></i> {{ strtoupper($entry['level']) }}
                            </span>
                            <span class="cmp-entry-msg">{{ $entry['message'] }}</span>
                            <span class="cmp-entry-ts">{{ $entry['timestamp'] }}</span>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="cmp-empty"><i class="fas fa-check-circle" style="color:#22c55e"></i>No entries unique to file B</div>
                @endif
            </div>

            {{-- Shared --}}
            <div id="tab-shared" class="cmp-tab-panel">
                @if(count($comparison['shared']) > 0)
                    <div class="cmp-entries">
                        @foreach($comparison['shared'] as $entry)
                        <div class="cmp-entry cmp-entry-shared">
                            <span class="cmp-badge" style="background:{{ $entry['color'] }}">
                                <i class="{{ $entry['icon'] }}"></i> {{ strtoupper($entry['level']) }}
                            </span>
                            <span class="cmp-entry-msg">{{ $entry['message'] }}</span>
                            <span class="cmp-entry-ts">{{ $entry['timestamp'] }}</span>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="cmp-empty"><i class="fas fa-info-circle"></i>No shared entries</div>
                @endif
            </div>
        </div>
    </div>

    @elseif($fileA && $fileB && $fileA === $fileB)
    {{-- same file warning shown in form --}}
    @elseif(!$comparison && !$fileA)
    <div class="cmp-card">
        <div class="cmp-card-body cmp-empty">
            <i class="fas fa-code-branch" style="font-size:2.5rem;color:#cbd5e1"></i>
            <p style="margin:0.5rem 0 0">Select two log files above and click <strong>Compare</strong></p>
        </div>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
    function cmpTab(e, id) {
        document.querySelectorAll('.cmp-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.cmp-tab-panel').forEach(p => p.classList.remove('active'));
        e.currentTarget.classList.add('active');
        document.getElementById(id).classList.add('active');
    }
</script>
@endpush
