<?php

namespace App\Enums;

/**
 * Risk Urgency Enum
 * 
 * Defines urgency levels for risks based on risk score.
 */
enum RiskUrgency: string
{
    case LOW = 'Low';
    case MEDIUM = 'Medium';
    case HIGH = 'High';
    case CRITICAL = 'Critical';

    /**
     * Calculate urgency from risk score
     * 
     * @param int $score Risk score (probability × impact)
     * @return self
     */
    public static function fromScore(int $score): self
    {
        return match(true) {
            $score >= 20 => self::CRITICAL,
            $score >= 15 => self::HIGH,
            $score >= 10 => self::MEDIUM,
            default => self::LOW,
        };
    }

    /**
     * Get the badge CSS class for urgency
     */
    public function badge(): string
    {
        return match($this) {
            self::CRITICAL => 'bg-danger',
            self::HIGH => 'bg-warning',
            self::MEDIUM => 'bg-info',
            self::LOW => 'bg-success',
        };
    }

    /**
     * Get the icon for urgency
     */
    public function icon(): string
    {
        return match($this) {
            self::CRITICAL => 'ti-alert-triangle',
            self::HIGH => 'ti-alert-circle',
            self::MEDIUM => 'ti-info-circle',
            self::LOW => 'ti-check-circle',
        };
    }

    /**
     * Get sort order for urgency (higher = more urgent)
     */
    public function sortOrder(): int
    {
        return match($this) {
            self::CRITICAL => 4,
            self::HIGH => 3,
            self::MEDIUM => 2,
            self::LOW => 1,
        };
    }

    /**
     * Get all urgency values as array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
