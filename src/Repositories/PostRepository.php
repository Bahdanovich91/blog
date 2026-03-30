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

    public function findByCategory(int $categoryId, int $limit, int $offset = 0, string $orderBy = 'p.created_at DESC'): array
    {
        $sql = "
        SELECT DISTINCT p.* 
        FROM posts AS  p
        INNER JOIN post_category AS pc ON p.id = pc.post_id
        WHERE pc.category_id = ?
        ORDER BY {$orderBy}
        LIMIT {$limit} OFFSET {$offset}
    ";

        $rows = $this->database->getAll($sql, [$categoryId]);

        return $this->mapper->mapCollection($rows, $this->entity);
    }
}
