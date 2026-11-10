<?php

namespace App\Enums;

use Illuminate\Database\Eloquent\Model;
use Intrfce\LaravelFrontendEnums\Attributes\PublishEnum;
use Spatie\Activitylog\Support\ActivityLogger;

/**
 * Every activity log entry's `event`. Descriptions are stored in English and translated when
 * displayed (see ActivityResource), so the log stays locale-independent.
 */
#[PublishEnum]
enum ActivityEvent: string
{
    case created = 'created';
    case updated = 'updated';
    case deleted = 'deleted';
    case smtp_updated = 'smtp_updated';

    /** English description; placeholders are filled from the activity's properties at display time. */
    public function description(): string
    {
        return match ($this) {
            // __('Created')
            self::created => 'Created',
            // __('Updated')
            self::updated => 'Updated',
            // __('Deleted')
            self::deleted => 'Deleted',
            // __('SMTP settings updated')
            self::smtp_updated => 'SMTP settings updated',
        };
    }

    public function logger(?Model $subject = null): ActivityLogger
    {
        $logger = activity()->event($this->value);

        return $subject ? $logger->performedOn($subject) : $logger;
    }

    /** @param array<string, mixed> $properties */
    public function log(?Model $subject = null, array $properties = []): void
    {
        $this->logger($subject)
            ->withProperties($properties)
            ->log($this->description());
    }
}
