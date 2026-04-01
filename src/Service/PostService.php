<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\PostPageDto;
use App\Entity\Post;
use App\Enum\SortType;
use App\Repositories\CategoryRepository;
use App\Repositories\PostRepository;

readonly class PostService
{
    public function __construct(
        private PostRepository $postRepository,
        private CategoryRepository $categoryRepository,
    ) {
    }

    /**
     * @param int $categoryId
     * @param SortType $sort
     * @param int $page
     * @param int $perPage
     *
     * @return array
     */
    public function getPaginatedPostsByCategory(
        int $categoryId,
        SortType $sort,
        int $page,
        int $perPage = 9,
    ): array {
        $orderBy = sprintf('%s DESC', $sort->getColumn());

        $page = max(1, $page);
        $total = $this->postRepository->countByCategory($categoryId);
        $totalPages = (int) ceil($total / $perPage);
        $page = min($page, max(1, $totalPages));
        $offset = ($page - 1) * $perPage;

        $posts = $this->postRepository->findByCategory($categoryId, $perPage, $offset, $orderBy);

        return [
            'posts' => $posts,
            'total' => $total,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'sort' => $sort->value,
        ];
    }

    public function recordView(int $postId): void
    {
        $this->postRepository->incrementViews($postId);
    }

    public function getPostPageData(string $slug): ?PostPageDto
    {
        $post = $this->getPostWithCategories($slug);

        if ($post === null) {
            return null;
        }

        $this->recordView($post->getId());

        $similarPosts = $this->getSimilarPosts($post);

        return new PostPageDto(
            post: $post->toArray(),
            contentHtml: $this->formatContent($post->getContent()),
            similarPosts: array_map(
                static fn($p) => $p->toArray(),
                $similarPosts
            ),
            title: $post->getTitle() . ' — Blogy',
        );
    }

    private function getPostWithCategories(string $slug): ?Post
    {
        /** @var ?Post $post */
        $post = $this->postRepository->findOneBy(['slug' => $slug]);
        if ($post === null) {
            return null;
        }

        $categories = $this->categoryRepository->findByPost($post->getId());
        $post->setCategories($categories);

        return $post;
    }

    private function getSimilarPosts(Post $post): array
    {
        $categoryIds = array_map(fn($category) => $category->getId(), $post->getCategories());

        return $this->postRepository->findSimilar(
            $post->getId(),
            $categoryIds
        );
    }

    private function formatContent(string $content): string
    {
        return implode('', array_map(
            fn(string $p) => '<p>' . htmlspecialchars(trim($p), ENT_QUOTES, 'UTF-8') . '</p>',
            array_filter(explode("\n\n", $content))
        ));
    }
}
