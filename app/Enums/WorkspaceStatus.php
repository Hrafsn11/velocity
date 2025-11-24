<?php

namespace App\Enums;

enum WorkspaceStatus: string
{
    case PLANNING = 'planning';
    case ACTIVE = 'active';
    case REVIEW = 'review';
    case COMPLETED = 'completed';
    case ARCHIVED = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::PLANNING => 'Planning',
            self::ACTIVE => 'Active',
            self::REVIEW => 'Review',
            self::COMPLETED => 'Completed',
            self::ARCHIVED => 'Archived',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::PLANNING => 'bg-label-info',
            self::ACTIVE => 'bg-label-success',
            self::REVIEW => 'bg-label-warning',
            self::COMPLETED => 'bg-label-primary',
            self::ARCHIVED => 'bg-label-secondary',
        };
    }
}

