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
}
