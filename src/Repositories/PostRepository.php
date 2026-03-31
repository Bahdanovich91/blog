<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database\Database;
use App\Core\Mapping\ArrayToEntityMapper;
use App\Core\Repository\AbstractRepository;
use App\Entity\Post;

class PostRepository extends AbstractRepository
{
    protected string $table = 'posts';
    protected string $entity = Post::class;

    public function __construct(ArrayToEntityMapper $mapper)
    {
        parent::__construct(Database::getInstance(), $mapper);
    }

    public function findByCategory(
        int $categoryId,
        int $limit,
        int $offset = 0,
        string $orderBy = 'p.created_at DESC'
    ): array {
        $sql = "
            SELECT DISTINCT p.* 
            FROM posts p
            INNER JOIN post_category pc ON p.id = pc.post_id
            WHERE pc.category_id = ?
            ORDER BY {$orderBy}
            LIMIT {$limit} OFFSET {$offset}
        ";

        $rows = $this->database->getAll($sql, [$categoryId]);

        return $this->mapper->mapCollection($rows, $this->entity);
    }

    public function getPaginatedByCategory(
        int $categoryId,
        string $sort,
        int $page,
        int $perPage = 9
    ): array {
        $sortMap = [
            'date' => 'p.created_at DESC',
            'views' => 'p.view_count DESC',
        ];

        $sortKey = array_key_exists($sort, $sortMap) ? $sort : 'date';
        $orderBy = $sortMap[$sortKey];

        $total = $this->countByCategory($categoryId);
        $totalPages = (int) ceil($total / $perPage);

        $page = max(1, min($page, $totalPages ?: 1));
        $offset = ($page - 1) * $perPage;

        return [
            'posts' => $this->findByCategory($categoryId, $perPage, $offset, $orderBy),
            'total' => $total,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'sort' => $sortKey,
        ];
    }

    public function countByCategory(int $categoryId): int
    {
        return (int) $this->database->getOne(
            "SELECT COUNT(*) FROM post_category WHERE category_id = ?",
            [$categoryId]
        );
    }

    public function incrementViews(int $postId): void
    {
        $this->execute(
            "UPDATE {$this->table} SET view_count = view_count + 1 WHERE id = ?",
            [$postId]
        );
    }

    public function findSimilar(int $postId, array $categoryIds, int $limit = 3): array
    {
        if (empty($categoryIds)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($categoryIds), '?'));

        $sql = "
            SELECT DISTINCT p.* 
            FROM posts AS p
            JOIN post_category AS pc ON p.id = pc.post_id
            WHERE pc.category_id IN ({$placeholders})
              AND p.id != ?
            ORDER BY p.created_at DESC
            LIMIT {$limit}
        ";

        $params = [...$categoryIds, $postId];
        $rows = $this->database->getAll($sql, $params);

        return $this->mapper->mapCollection($rows, $this->entity);
    }
}

