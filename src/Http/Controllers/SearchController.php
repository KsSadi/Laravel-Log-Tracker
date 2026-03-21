<?php

namespace Kssadi\LogTracker\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;
use Kssadi\LogTracker\Facades\LogTracker;
use Kssadi\LogTracker\Http\Requests\LogSearchRequest;
use Kssadi\LogTracker\Services\LogSearchService;
use Kssadi\LogTracker\Traits\HasThemeSupport;

class SearchController extends Controller
{
    use HasThemeSupport;

    public function __construct(private readonly LogSearchService $searchService) {}

    public function index(LogSearchRequest $request): View
    {
        $logFiles = LogTracker::getLogFiles();
        rsort($logFiles);

        $logConfig = config('log-tracker.log_levels', []);
        $results = null;

        if ($request->hasSearchFilters()) {
            $perPage = config('log-tracker.log_per_page', 50);

            $results = $this->searchService->search(
                query: $request->string('query')->trim()->value() ?: null,
                level: $request->string('level')->value() ?: null,
                dateFrom: $request->string('date_from')->value() ?: null,
                dateTo: $request->string('date_to')->value() ?: null,
                file: $request->string('file')->value() ?: null,
                page: $request->integer('page', 1),
                perPage: $perPage,
            );

            $results['entries'] = array_map(function (array $entry) use ($logConfig): array {
                $level = strtolower($entry['level']);

                return array_merge($entry, [
                    'level' => $level,
                    'timestamp' => Carbon::parse($entry['timestamp'])->format('j M Y, h:i:s A'),
                    'color' => $logConfig[$level]['color'] ?? '#6c757d',
                    'icon' => $logConfig[$level]['icon'] ?? 'fas fa-circle',
                ]);
            }, $results['entries']);
        }

        return $this->themedView('search', compact('logFiles', 'results', 'logConfig'));
    }
}
