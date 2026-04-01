<?php

declare(strict_types=1);

namespace App\Dto;

final readonly class HomePageDto
{
    public function __construct(
        public array $sections,
    ) {}

    public function toView(): array
    {
        return [
            'sections' => array_map(
                static fn(array $s) => [
                    'category' => $s['category']->toArray(),
                    'posts' => array_map(
                        static fn($p) => $p->toArray(),
                        $s['posts']
                    ),
                ],
                $this->sections
            ),
            'page_title' => 'Blogy',
        ];
    }
}
