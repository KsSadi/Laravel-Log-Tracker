<?php

namespace Kssadi\LogTracker\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Kssadi\LogTracker\Services\LogParserService;

class LogSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, \Illuminate\Contracts\Validation\Rule|string>>
     */
    public function rules(): array
    {
        $validLevels = implode(',', LogParserService::logLevelNames());

        return [
            'query' => ['nullable', 'string', 'max:200'],
            'level' => ['nullable', 'string', "in:{$validLevels}"],
            'date_from' => ['nullable', 'date_format:Y-m-d'],
            'date_to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:date_from'],
            'file' => ['nullable', 'string', 'max:100'],
        ];
    }

    /**
     * Check whether any search filter has been supplied.
     */
    public function hasSearchFilters(): bool
    {
        return $this->hasAny(['query', 'level', 'date_from', 'date_to', 'file'])
            && collect($this->only(['query', 'level', 'date_from', 'date_to', 'file']))
                ->filter(fn ($v): bool => $v !== null && $v !== '')
                ->isNotEmpty();
    }
}
