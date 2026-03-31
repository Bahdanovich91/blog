<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database\Database;
use App\Core\Mapping\ArrayToEntityMapper;
use App\Core\Repository\AbstractRepository;
use App\Entity\Category;

class CategoryRepository extends AbstractRepository
{
    protected string $table = 'categories';
    protected string $entity = Category::class;

    public function __construct(ArrayToEntityMapper $mapper)
    {
        parent::__construct(Database::getInstance(), $mapper);
    }

    public function findAll(): array
    {
        return $this->findBy(orderBy: 'name ASC');
    }

    public function findByPost(int $postId): array
    {
        $sql = "
            SELECT c.* FROM categories AS c
            JOIN post_category AS pc ON c.id = pc.category_id
            WHERE pc.post_id = ?
            ORDER BY c.name
        ";

        $rows = $this->database->getAll($sql, [$postId]);

        return $this->mapper->mapCollection($rows, $this->entity);
    }
}
