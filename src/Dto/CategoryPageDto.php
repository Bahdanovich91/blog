<?php

declare(strict_types=1);

namespace App\Dto;

final readonly class CategoryPageDto
{
    public function __construct(
        public array $category,
        public array $posts,
        public string $sort,
        public string $direction,
        public int $currentPage,
        public int $totalPages,
    ) {}

    public function toView(string $slug): array
    {
        return [
            'category' => $this->category,
            'posts' => $this->posts,
            'current_sort' => $this->sort,
            'current_direction' => $this->direction,
            'current_page' => $this->currentPage,
            'total_pages' => $this->totalPages,
            'pagination_base_url' => sprintf(
                '/category/%s?sort=%s&direction=%s',
                urlencode($slug),
                $this->sort,
                $this->direction
            ),
            'page_title' => $this->category['name'] . ' — Blogy',
        ];
    }
}
