<?php

declare(strict_types=1);

namespace App\Enum;

enum SortType: string
{
    case DATE = 'date';
    case VIEWS = 'views';

    public function getColumn(): string
    {
        return match($this) {
            self::DATE => 'p.created_at',
            self::VIEWS => 'p.view_count',
        };
    }

    public static function fromString(string $value): self
    {
        return match($value) {
            'date' => self::DATE,
            'views' => self::VIEWS,
            default => self::DATE,
        };
    }
}
