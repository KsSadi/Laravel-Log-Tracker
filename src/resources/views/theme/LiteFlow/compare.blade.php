@extends('log-tracker::theme.LiteFlow.layouts.app')

@section('title', 'Compare Logs - Log Tracker')

@section('content')
<div class="container my-4 page-content">

    {{-- Page Header --}}
    <div class="page-header mb-4">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-code-branch" style="font-size:1.4rem;color:#2563eb"></i>
            <div>
                <h4 class="mb-0 fw-bold">Compare Log Files</h4>
                <p class="mb-0 text-muted small">Side-by-side diff — unique and shared entries between two files</p>
            </div>
        </div>
    </div>

    {{-- Picker Form --}}
    <div class="lf-card mb-4">
        <div class="lf-card-header">
            <i class="fas fa-sliders-h me-1"></i> Select Files to Compare
        </div>
        <div class="lf-card-body">
            <style>
                .lf-cmp-pickers { display: grid; grid-template-columns: 1fr auto 1fr; gap: 1rem; align-items: end; }
                .lf-cmp-vs { display: flex; align-items: center; justify-content: center; font-weight: 800; color: #94a3b8; padding-bottom: 0.1rem; }
                .lf-cmp-label { font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 0.35rem; }
                .lf-cmp-select { width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 0.87rem; color: #1e293b; background: white; }
                .lf-cmp-select:focus { outline: none; border-color: #2563eb; }
                .lf-cmp-btn { display: inline-flex; align-items: center; gap: 0.45rem; padding: 0.55rem 1.2rem; background: #2563eb; color: white; border: none; border-radius: 6px; font-weight: 700; font-size: 0.85rem; cursor: pointer; margin-top: 0.75rem; }
                .lf-cmp-btn:hover { background: #1d4ed8; }

                /* Stats */
                .lf-cmp-stats { display: grid; grid-template-columns: repeat(5, 1fr); gap: 0.75rem; margin-bottom: 1.25rem; }
                .lf-cmp-stat { border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.85rem; text-align: center; background: white; }
                .lf-cmp-stat-num { font-size: 1.6rem; font-weight: 800; line-height: 1; margin-bottom: 0.25rem; }
                .lf-cmp-stat-lbl { font-size: 0.7rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; }

                /* Level table */
                .lf-cmp-table { width: 100%; border-collapse: collapse; font-size: 0.84rem; }
                .lf-cmp-table th { background: #f8fafc; padding: 0.55rem 1rem; text-align: left; font-weight: 700; font-size: 0.74rem; text-transform: uppercase; color: #64748b; border-bottom: 2px solid #e2e8f0; }
                .lf-cmp-table th:nth-child(2), .lf-cmp-table td:nth-child(2) { text-align: right; }
                .lf-cmp-table th:nth-child(3), .lf-cmp-table td:nth-child(3) { text-align: right; }
                .lf-cmp-table th:last-child, .lf-cmp-table td:last-child { text-align: right; }
                .lf-cmp-table td { padding: 0.5rem 1rem; border-bottom: 1px solid #f1f5f9; }
                .lf-cmp-table tr:last-child td { border-bottom: none; }

                /* Entries */
                .lf-cmp-entries { display: flex; flex-direction: column; gap: 0.4rem; }
                .lf-cmp-entry { display: flex; gap: 0.65rem; align-items: flex-start; border: 1px solid #f1f5f9; border-radius: 6px; padding: 0.6rem 0.9rem; background: #fafafa; font-size: 0.83rem; }
                .lf-cmp-entry-a      { border-left: 3px solid #ef4444; }
                .lf-cmp-entry-b      { border-left: 3px solid #2563eb; }
                .lf-cmp-entry-shared { border-left: 3px solid #22c55e; }
                .lf-cmp-msg { flex: 1; min-width: 0; word-break: break-word; color: #1e293b; font-family: monospace; }
                .lf-cmp-ts  { font-size: 0.74rem; color: #94a3b8; white-space: nowrap; padding-top: 0.1rem; }
                .lf-cmp-badge { display: inline-flex; align-items: center; gap: 0.2rem; padding: 0.13rem 0.48rem; border-radius: 20px; font-size: 0.69rem; font-weight: 700; color: white; white-space: nowrap; }
                .lf-cmp-pill { display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.15rem 0.55rem; border-radius: 20px; font-size: 0.69rem; font-weight: 700; color: white; }

                /* Tabs */
                .lf-cmp-tabs { display: flex; gap: 0; border-bottom: 2px solid #e2e8f0; margin-bottom: 1rem; }
                .lf-cmp-tab { padding: 0.55rem 1.1rem; font-size: 0.83rem; font-weight: 600; cursor: pointer; border: none; background: none; color: #64748b; border-bottom: 2px solid transparent; margin-bottom: -2px; }
                .lf-cmp-tab.active { color: #2563eb; border-bottom-color: #2563eb; }
                .lf-cmp-tab-cnt { background: #e2e8f0; border-radius: 20px; padding: 0.08rem 0.45rem; font-size: 0.7rem; margin-left: 0.3rem; }
                .lf-cmp-tab.active .lf-cmp-tab-cnt { background: #dbeafe; color: #1d4ed8; }
                .lf-cmp-panel { display: none; }
                .lf-cmp-panel.active { display: block; }

                /* Empty */
                .lf-cmp-empty { text-align: center; padding: 1.75rem; color: #94a3b8; font-size: 0.88rem; }
                .lf-cmp-empty i { font-size: 1.8rem; display: block; margin-bottom: 0.4rem; }
                .lf-diff-pos { color: #22c55e; font-weight: 700; }
                .lf-diff-neg { color: #ef4444; font-weight: 700; }
                .lf-diff-zero { color: #94a3b8; }

                /* Table scroll */
                .lf-cmp-table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }

                /* Tab overflow */
                .lf-cmp-tabs { overflow-x: auto; flex-wrap: nowrap; }
                .lf-cmp-tab  { flex-shrink: 0; }

                @media (max-width: 768px) {
                    .lf-cmp-pickers { grid-template-columns: 1fr; }
                    .lf-cmp-vs      { display: none; }
                    .lf-cmp-stats   { grid-template-columns: repeat(2, 1fr); }
                    .lf-cmp-ts      { display: none; }
                }

                @media (max-width: 480px) {
                    .lf-cmp-stats   { grid-template-columns: 1fr 1fr; }
                    .lf-cmp-entry   { flex-wrap: wrap; gap: 0.35rem; }
                    .lf-cmp-msg     { width: 100%; }
                    .lf-cmp-tab     { padding: 0.45rem 0.7rem; font-size: 0.77rem; }
                }
            </style>

            <form method="GET" action="{{ route('log-tracker.compare') }}">
                <div class="lf-cmp-pickers">
                    <div>
                        <div class="lf-cmp-label"><i class="fas fa-file-alt me-1"></i>File A</div>
                        <select name="file_a" class="lf-cmp-select" required>
                            <option value="">— choose file A —</option>
                            @foreach($logFiles as $file)
                                <option value="{{ $file }}" @selected($fileA === $file)>{{ $file }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="lf-cmp-vs">VS</div>
                    <div>
                        <div class="lf-cmp-label"><i class="fas fa-file-alt me-1"></i>File B</div>
                        <select name="file_b" class="lf-cmp-select" required>
                            <option value="">— choose file B —</option>
                            @foreach($logFiles as $file)
                                <option value="{{ $file }}" @selected($fileB === $file)>{{ $file }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                @if($fileA === $fileB && $fileA)
                    <p class="text-danger small mt-2 mb-0">
                        <i class="fas fa-exclamation-triangle"></i> Please select two different files.
                    </p>
                @endif

                <button type="submit" class="lf-cmp-btn">
                    <i class="fas fa-code-branch"></i> Compare
                </button>
            </form>
        </div>
    </div>

    @if($comparison)

    {{-- Summary Stats --}}
    <div class="lf-cmp-stats">
        <div class="lf-cmp-stat">
            <div class="lf-cmp-stat-num" style="color:#2563eb">{{ number_format($comparison['totalA']) }}</div>
            <div class="lf-cmp-stat-lbl">Total in A</div>
        </div>
        <div class="lf-cmp-stat">
            <div class="lf-cmp-stat-num" style="color:#7c3aed">{{ number_format($comparison['totalB']) }}</div>
            <div class="lf-cmp-stat-lbl">Total in B</div>
        </div>
        <div class="lf-cmp-stat">
            <div class="lf-cmp-stat-num" style="color:#22c55e">{{ count($comparison['shared']) }}</div>
            <div class="lf-cmp-stat-lbl">Shared</div>
        </div>
        <div class="lf-cmp-stat">
            <div class="lf-cmp-stat-num" style="color:#ef4444">{{ count($comparison['onlyInA']) }}</div>
            <div class="lf-cmp-stat-lbl">Only in A</div>
        </div>
        <div class="lf-cmp-stat">
            <div class="lf-cmp-stat-num" style="color:#f97316">{{ count($comparison['onlyInB']) }}</div>
            <div class="lf-cmp-stat-lbl">Only in B</div>
        </div>
    </div>

    {{-- Level Breakdown --}}
    <div class="lf-card mb-4">
        <div class="lf-card-header">
            <i class="fas fa-chart-bar me-1"></i> Level Breakdown
        </div>
        <div class="lf-card-body p-0">
            <div class="lf-cmp-table-wrap">
            <table class="lf-cmp-table">
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
                            $cntA  = $comparison['levelsA'][$level] ?? 0;
                            $cntB  = $comparison['levelsB'][$level] ?? 0;
                            $diff  = $cntB - $cntA;
                            $color = $logConfig[$level]['color'] ?? '#6c757d';
                            $icon  = $logConfig[$level]['icon']  ?? 'fas fa-circle';
                        @endphp
                        <tr>
                            <td>
                                <span class="lf-cmp-pill" style="background:{{ $color }}">
                                    <i class="{{ $icon }}"></i> {{ strtoupper($level) }}
                                </span>
                            </td>
                            <td>{{ $cntA }}</td>
                            <td>{{ $cntB }}</td>
                            <td>
                                @if($diff > 0)
                                    <span class="lf-diff-pos">+{{ $diff }}</span>
                                @elseif($diff < 0)
                                    <span class="lf-diff-neg">{{ $diff }}</span>
                                @else
                                    <span class="lf-diff-zero">0</span>
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
    <div class="lf-card">
        <div class="lf-card-header">
            <i class="fas fa-list-alt me-1"></i> Entry Differences
        </div>
        <div class="lf-card-body">
            <div class="lf-cmp-tabs">
                <button class="lf-cmp-tab active" onclick="lfTab(event,'lf-only-a')">
                    <i class="fas fa-minus-circle" style="color:#ef4444"></i>
                    Only in A<span class="lf-cmp-tab-cnt">{{ count($comparison['onlyInA']) }}</span>
                </button>
                <button class="lf-cmp-tab" onclick="lfTab(event,'lf-only-b')">
                    <i class="fas fa-plus-circle" style="color:#2563eb"></i>
                    Only in B<span class="lf-cmp-tab-cnt">{{ count($comparison['onlyInB']) }}</span>
                </button>
                <button class="lf-cmp-tab" onclick="lfTab(event,'lf-shared')">
                    <i class="fas fa-equals" style="color:#22c55e"></i>
                    Shared<span class="lf-cmp-tab-cnt">{{ count($comparison['shared']) }}</span>
                </button>
            </div>

            {{-- Only in A --}}
            <div id="lf-only-a" class="lf-cmp-panel active">
                @if(count($comparison['onlyInA']) > 0)
                    <div class="lf-cmp-entries">
                        @foreach($comparison['onlyInA'] as $entry)
                        <div class="lf-cmp-entry lf-cmp-entry-a">
                            <span class="lf-cmp-badge" style="background:{{ $entry['color'] }}">
                                <i class="{{ $entry['icon'] }}"></i> {{ strtoupper($entry['level']) }}
                            </span>
                            <span class="lf-cmp-msg">{{ $entry['message'] }}</span>
                            <span class="lf-cmp-ts">{{ $entry['timestamp'] }}</span>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="lf-cmp-empty">
                        <i class="fas fa-check-circle" style="color:#22c55e"></i>
                        No entries unique to file A
                    </div>
                @endif
            </div>

            {{-- Only in B --}}
            <div id="lf-only-b" class="lf-cmp-panel">
                @if(count($comparison['onlyInB']) > 0)
                    <div class="lf-cmp-entries">
                        @foreach($comparison['onlyInB'] as $entry)
                        <div class="lf-cmp-entry lf-cmp-entry-b">
                            <span class="lf-cmp-badge" style="background:{{ $entry['color'] }}">
                                <i class="{{ $entry['icon'] }}"></i> {{ strtoupper($entry['level']) }}
                            </span>
                            <span class="lf-cmp-msg">{{ $entry['message'] }}</span>
                            <span class="lf-cmp-ts">{{ $entry['timestamp'] }}</span>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="lf-cmp-empty">
                        <i class="fas fa-check-circle" style="color:#22c55e"></i>
                        No entries unique to file B
                    </div>
                @endif
            </div>

            {{-- Shared --}}
            <div id="lf-shared" class="lf-cmp-panel">
                @if(count($comparison['shared']) > 0)
                    <div class="lf-cmp-entries">
                        @foreach($comparison['shared'] as $entry)
                        <div class="lf-cmp-entry lf-cmp-entry-shared">
                            <span class="lf-cmp-badge" style="background:{{ $entry['color'] }}">
                                <i class="{{ $entry['icon'] }}"></i> {{ strtoupper($entry['level']) }}
                            </span>
                            <span class="lf-cmp-msg">{{ $entry['message'] }}</span>
                            <span class="lf-cmp-ts">{{ $entry['timestamp'] }}</span>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="lf-cmp-empty">
                        <i class="fas fa-info-circle"></i> No shared entries
                    </div>
                @endif
            </div>
        </div>
    </div>

    @elseif(!$fileA)
    <div class="lf-card">
        <div class="lf-card-body lf-cmp-empty">
            <i class="fas fa-code-branch" style="color:#cbd5e1"></i>
            Select two log files above and click <strong>Compare</strong>
        </div>
    </div>
    @endif

</div>

<script>
    function lfTab(e, id) {
        document.querySelectorAll('.lf-cmp-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.lf-cmp-panel').forEach(p => p.classList.remove('active'));
        e.currentTarget.classList.add('active');
        document.getElementById(id).classList.add('active');
    }
</script>
@endsection
