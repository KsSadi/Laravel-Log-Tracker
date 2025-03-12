<?php

namespace Kssadi\LogTracker\Models;

use Illuminate\Support\Carbon;

class LogEntry
{
    /**
     * Log entry attributes.
     *
     * @var array
     */
    protected $attributes;

    /**
     * Create a new log entry instance.
     *
     * @param array $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    /**
     * Get an attribute from the log entry.
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        if ($key === 'formatted_timestamp' && isset($this->attributes['timestamp'])) {
            return $this->formatTimestamp($this->attributes['timestamp']);
        }

        return isset($this->attributes[$key]) ? $this->attributes[$key] : null;
    }

    /**
     * Format the timestamp for display.
     *
     * @param string $timestamp
     * @return string
     */
    protected function formatTimestamp($timestamp)
    {
        try {
            $carbon = new Carbon($timestamp);
            return $carbon->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return $timestamp;
        }
    }

    /**
     * Get a snippet of the log message.
     *
     * @param int $length
     * @return string
     */
    public function getMessageSnippet($length = 100)
    {
        $message = isset($this->attributes['message']) ? $this->attributes['message'] : '';

        if (strlen($message) <= $length) {
            return $message;
        }

        return substr($message, 0, $length) . '...';
    }

    /**
     * Determine if the log entry has a stack trace.
     *
     * @return bool
     */
    public function hasStackTrace()
    {
        return !empty($this->attributes['stack_trace']);
    }

    /**
     * Get all attributes.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->attributes;
    }
}