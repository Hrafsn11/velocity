<?php

namespace App\Enums;

/**
 * Risk Status Enum
 * 
 * Defines all possible statuses for a risk in the system.
 */
enum RiskStatus: string
{
    case IDENTIFIED = 'identified';
    case ANALYZING = 'analyzing';
    case ACTIVE = 'active';
    case MITIGATING = 'mitigating';
    case MONITORING = 'monitoring';
    case MITIGATED = 'mitigated';
    case MATERIALIZED = 'materialized';

    /**
     * Get the display label for the status
     */
    public function label(): string
    {
        return match($this) {
            self::IDENTIFIED => 'Identified',
            self::ANALYZING => 'Analyzing',
            self::ACTIVE => 'Active',
            self::MITIGATING => 'Mitigating',
            self::MONITORING => 'Monitoring',
            self::MITIGATED => 'Mitigated',
            self::MATERIALIZED => 'Materialized',
        };
    }

    /**
     * Get the badge CSS class for the status
     */
    public function badge(): string
    {
        return match($this) {
            self::IDENTIFIED => 'bg-label-secondary',
            self::ANALYZING => 'bg-label-info',
            self::ACTIVE => 'bg-label-danger',
            self::MITIGATING => 'bg-label-warning',
            self::MONITORING => 'bg-label-primary',
            self::MITIGATED => 'bg-label-success',
            self::MATERIALIZED => 'bg-danger',
        };
    }

    /**
     * Get the icon for the status
     */
    public function icon(): string
    {
        return match($this) {
            self::IDENTIFIED => 'ti-eye',
            self::ANALYZING => 'ti-search',
            self::ACTIVE => 'ti-alert-triangle',
            self::MITIGATING => 'ti-shield',
            self::MONITORING => 'ti-eye-check',
            self::MITIGATED => 'ti-shield-check',
            self::MATERIALIZED => 'ti-alert-octagon',
        };
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
