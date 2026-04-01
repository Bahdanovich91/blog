<?php

declare(strict_types=1);

namespace App\Service;

use App\Repositories\CategoryRepository;
use App\Repositories\PostRepository;

readonly class CategoryService
{
    public function __construct(
        private CategoryRepository $categoryRepository,
        private PostRepository     $postRepository,
    ) {
    }

    public function getCategoriesWithLatestPosts(int $limit = 3): array
    {
        $categories = $this->categoryRepository->findAll();
        $sections = [];

        foreach ($categories as $category) {
            $posts = $this->postRepository->findByCategory(
                categoryId: $category->getId(),
                limit: $limit
            );

            if (empty($posts)) {
                continue;
            }

            $sections[] = [
                'category' => $category,
                'posts' => $posts,
            ];
        }

        return $sections;
    }

    public function getCategoryPageData(string $slug, string $sort, string $direction, int $page): ?array
    {
        $category = $this->categoryRepository->findOneBy(['slug' => $slug]);
        if ($category === null) {
            return null;
        }

        $result = $this->postRepository->getPaginatedByCategory(
            $category->getId(),
            $sort,
            $direction,
            $page
        );

        return [
            'category' => $category->toArray(),
            'posts' => array_map(fn($p) => $p->toArray(), $result['posts']),
            'current_sort' => $result['sort'],
            'current_direction' => $result['direction'],
            'current_page' => $result['currentPage'],
            'total_pages' => $result['totalPages'],
            'pagination_base_url' => '/category/' . urlencode($category->getSlug())
                . '?sort=' . $result['sort']
                . '&direction=' . $result['direction'],
            'page_title' => $category->getName() . ' — Blogy',
        ];
    }
}
