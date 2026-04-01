<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\CategoryPageDto;
use App\Dto\HomePageDto;
use App\Entity\Category;
use App\Enum\SortDirection;
use App\Enum\SortType;
use App\Repositories\CategoryRepository;
use App\Repositories\PostRepository;

readonly class CategoryService
{
    public function __construct(
        private CategoryRepository $categoryRepository,
        private PostRepository $postRepository,
    ) {
    }

    public function getHomePageData(int $limit = 3): HomePageDto
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

        return new HomePageDto($sections);
    }

    public function getCategoryPageData(
        string $slug,
        SortType $sort,
        SortDirection $direction,
        int $page
    ): ?CategoryPageDto {
        /** @var ?Category $category */
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

        return new CategoryPageDto(
            category: $category->toArray(),
            posts: array_map(fn($post) => $post->toArray(), $result['posts']),
            sort: $result['sort'],
            direction: $result['direction'],
            currentPage: $result['currentPage'],
            totalPages: $result['totalPages'],
        );
    }
}
