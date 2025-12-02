<?php

namespace App\Enums;

enum TaskLabel: string
{
    case UX = 'UX';
    case IMAGES = 'Images';
    case INFO = 'Info';
    case CODE_REVIEW = 'Code Review';
    case APP = 'App';
    case CHARTS_MAPS = 'Charts & Maps';
    case FEATURE = 'Feature';
    case BUG = 'Bug';

    public function badgeClass(): string
    {
        return match($this) {
            self::UX, self::FEATURE => 'bg-label-success',
            self::IMAGES => 'bg-label-warning',
            self::INFO => 'bg-label-info',
            self::CODE_REVIEW, self::BUG => 'bg-label-danger',
            self::APP => 'bg-label-secondary',
            self::CHARTS_MAPS => 'bg-label-primary',
        };
    }

    public static function options(): array
    {
        return array_map(
            fn($case) => ['value' => $case->value, 'label' => $case->value],
            self::cases()
        );
    }
}
