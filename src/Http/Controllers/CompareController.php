<?php

namespace Kssadi\LogTracker\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Kssadi\LogTracker\Facades\LogTracker;
use Kssadi\LogTracker\Traits\HasThemeSupport;

class CompareController extends Controller
{
    use HasThemeSupport;

    public function index(Request $request): View
    {
        $logFiles = LogTracker::getLogFiles();
        rsort($logFiles);

        $fileA = $request->string('file_a')->trim()->value() ?: null;
        $fileB = $request->string('file_b')->trim()->value() ?: null;

        $comparison = null;

        if ($fileA && $fileB && $fileA !== $fileB) {
            $comparison = $this->buildComparison($fileA, $fileB);
        }

        return $this->themedView('compare', compact('logFiles', 'fileA', 'fileB', 'comparison'));
    }

    // ─────────────────────────────────────────────
    // Comparison engine
    // ─────────────────────────────────────────────

    /**
     * Build the full comparison payload between two log files.
     *
     * @return array{
     *     fileA: string,
     *     fileB: string,
     *     totalA: int,
     *     totalB: int,
     *     levelsA: array<string, int>,
     *     levelsB: array<string, int>,
     *     allLevels: list<string>,
     *     onlyInA: list<array<string, mixed>>,
     *     onlyInB: list<array<string, mixed>>,
     *     shared:  list<array<string, mixed>>,
     * }
     */
    private function buildComparison(string $fileA, string $fileB): array
    {
        $logConfig = config('log-tracker.log_levels', []);

        $resultA = LogTracker::getAllLogEntries($fileA);
        $resultB = LogTracker::getAllLogEntries($fileB);

        $entriesA = $resultA['entries'] ?? [];
        $entriesB = $resultB['entries'] ?? [];

        // Index by normalised key (level + message fingerprint)
        $keyedA = $this->indexByKey($entriesA);
        $keyedB = $this->indexByKey($entriesB);

        $keysA = array_keys($keyedA);
        $keysB = array_keys($keyedB);

        $onlyInAKeys = array_diff($keysA, $keysB);
        $onlyInBKeys = array_diff($keysB, $keysA);
        $sharedKeys = array_intersect($keysA, $keysB);

        $decorate = function (array $entry) use ($logConfig): array {
            $level = strtolower($entry['level']);

            return array_merge($entry, [
                'color' => $logConfig[$level]['color'] ?? '#6c757d',
                'icon' => $logConfig[$level]['icon'] ?? 'fas fa-circle',
            ]);
        };

        $onlyInA = array_values(array_map($decorate, array_map(fn ($k) => $keyedA[$k], $onlyInAKeys)));
        $onlyInB = array_values(array_map($decorate, array_map(fn ($k) => $keyedB[$k], $onlyInBKeys)));
        $shared = array_values(array_map($decorate, array_map(fn ($k) => $keyedA[$k], $sharedKeys)));

        $levelsA = $this->countByLevel($entriesA);
        $levelsB = $this->countByLevel($entriesB);

        $allLevels = array_values(array_unique(array_merge(array_keys($levelsA), array_keys($levelsB))));
        sort($allLevels);

        return [
            'fileA' => $fileA,
            'fileB' => $fileB,
            'totalA' => count($entriesA),
            'totalB' => count($entriesB),
            'levelsA' => $levelsA,
            'levelsB' => $levelsB,
            'allLevels' => $allLevels,
            'onlyInA' => $onlyInA,
            'onlyInB' => $onlyInB,
            'shared' => $shared,
        ];
    }

    /**
     * Index entries by a normalised level+message fingerprint.
     * When duplicates exist, keeps the first occurrence.
     *
     * @param  array<int, array<string, mixed>>  $entries
     * @return array<string, array<string, mixed>>
     */
    private function indexByKey(array $entries): array
    {
        $indexed = [];

        foreach ($entries as $entry) {
            $key = strtolower($entry['level']).':'.trim($entry['message']);

            if (! isset($indexed[$key])) {
                $indexed[$key] = $entry;
            }
        }

        return $indexed;
    }

    /**
     * Count entries per log level.
     *
     * @param  array<int, array<string, mixed>>  $entries
     * @return array<string, int>
     */
    private function countByLevel(array $entries): array
    {
        $counts = [];

        foreach ($entries as $entry) {
            $level = strtolower($entry['level']);
            $counts[$level] = ($counts[$level] ?? 0) + 1;
        }

        return $counts;
    }
}
