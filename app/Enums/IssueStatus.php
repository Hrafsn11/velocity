<?php

namespace App\Enums;

/**
 * Issue Status Enum
 * 
 * Defines all possible statuses for an issue in the system.
 */
enum IssueStatus: string
{
    case OPEN = 'open';
    case IN_PROGRESS = 'in_progress';
    case RESOLVED = 'resolved';
    case CLOSED = 'closed';
    case REOPENED = 'reopened';

    /**
     * Get the display label for the status
     */
    public function label(): string
    {
        return match($this) {
            self::OPEN => 'Open',
            self::IN_PROGRESS => 'In Progress',
            self::RESOLVED => 'Resolved',
            self::CLOSED => 'Closed',
            self::REOPENED => 'Reopened',
        };
    }

    /**
     * Get the badge CSS class for the status
     */
    public function badge(): string
    {
        return match($this) {
            self::OPEN => 'bg-label-warning',
            self::IN_PROGRESS => 'bg-label-info',
            self::RESOLVED => 'bg-label-success',
            self::CLOSED => 'bg-label-secondary',
            self::REOPENED => 'bg-label-danger',
        };
    }

    /**
     * Get the icon for the status
     */
    public function icon(): string
    {
        return match($this) {
            self::OPEN => 'ti-circle-dot',
            self::IN_PROGRESS => 'ti-progress',
            self::RESOLVED => 'ti-check-circle',
            self::CLOSED => 'ti-circle-x',
            self::REOPENED => 'ti-refresh-alert',
        };
    }

    /**
     * Check if issue is resolved or closed
     */
    public function isFinished(): bool
    {
        return in_array($this, [self::RESOLVED, self::CLOSED]);
    }

    /**
     * Check if issue is active (open, in progress, reopened)
     */
    public function isActive(): bool
    {
        return in_array($this, [self::OPEN, self::IN_PROGRESS, self::REOPENED]);
    }

    /**
     * Get all status values as array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get all statuses for select dropdown
     */
    public static function options(): array
    {
        return array_map(
            fn($status) => ['value' => $status->value, 'label' => $status->label()],
            self::cases()
        );
    }
}
