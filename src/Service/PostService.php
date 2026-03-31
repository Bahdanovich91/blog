<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Post;
use App\Repositories\CategoryRepository;
use App\Repositories\PostRepository;

class PostService
{
    private const SORT_MAP = [
        'date' => 'p.created_at DESC',
        'views' => 'p.view_count DESC',
    ];

    private const DEFAULT_SORT = 'date';

    public function __construct(
        private readonly PostRepository $postRepository,
        private readonly CategoryRepository $categoryRepository,
    ) {}

    /**
     * @return array{posts: Post[], total: int, totalPages: int, currentPage: int, sort: string}
     */
    public function getPaginatedPostsByCategory(
        int $categoryId,
        string $sort,
        int $page,
        int $perPage = 9,
    ): array {
        $sort = array_key_exists($sort, self::SORT_MAP) ? $sort : self::DEFAULT_SORT;
        $orderBy = self::SORT_MAP[$sort];

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
            'sort' => $sort,
        ];
    }

    public function recordView(int $postId): void
    {
        $this->postRepository->incrementViews($postId);
    }

    /** @return Post[] */
    public function getSimilarPosts(int $postId, array $categoryIds, int $limit = 3): array
    {
        return $this->postRepository->findSimilar($postId, $categoryIds, $limit);
    }

    public function getPostPageData(string $slug): ?array
    {
        $post = $this->getPostWithCategories($slug);

        if ($post === null) {
            return null;
        }

        $this->recordView($post->getId());

        $similarPosts = $this->getSimilarPostsFor($post);

        return [
            'post' => $post->toArray(),
            'post_content_html' => $this->formatContent($post->getContent()),
            'similar_posts' => array_map(fn($p) => $p->toArray(), $similarPosts),
            'page_title' => $post->getTitle() . ' — Blogy',
        ];
    }

    private function getPostWithCategories(string $slug): ?Post
    {
        $post = $this->postRepository->findOneBy(['slug' => $slug]);

        if ($post === null) {
            return null;
        }

        $categories = $this->categoryRepository->findByPost($post->getId());
        $post->setCategories($categories);

        return $post;
    }

    private function getSimilarPostsFor(Post $post): array
    {
        $categoryIds = array_map(fn($c) => $c->getId(), $post->getCategories());

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
