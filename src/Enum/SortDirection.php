<?php

declare(strict_types=1);

namespace App\Enum;

enum SortDirection: string
{
    case ASC = 'ASC';
    case DESC = 'DESC';

    public static function fromString(string $value): self
    {
        return match(strtoupper($value)) {
            'ASC' => self::ASC,
            default => self::DESC,
        };
    }
}
