<?php

declare(strict_types=1);

namespace App\Core\Repository;

use App\Core\Database\Database;
use App\Core\Entity\EntityInterface;
use App\Core\Mapping\ArrayToEntityMapper;

abstract class AbstractRepository implements RepositoryInterface
{
    protected string $table;
    protected string $entity;
    protected Database $database;
    protected ArrayToEntityMapper $mapper;

    public function __construct(Database $database, ArrayToEntityMapper $mapper)
    {
        $this->database = $database;
        $this->mapper = $mapper;
    }

    public function findOne(int $id)
    {
        return $this->findOneBy(['id' => $id]);
    }

    public function findAll(): array
    {
        return $this->findBy();
    }

    public function findOneBy(array $criteria): ?EntityInterface
    {
        $result = $this->findBy($criteria, limit: 1);

        return $result[0] ?? null;
    }

    public function findBy(
        array $criteria = [],
        string $orderBy = '',
        ?int $limit = null,
        ?int $offset = null
    ): array {
        $conditions = '';
        $params = [];

        if (!empty($criteria)) {
            $fields = array_keys($criteria);
            $conditions = 'WHERE ' . implode(' AND ', array_map(fn($f) => "$f = ?", $fields));
            $params = array_values($criteria);
        }

        $sql = "SELECT * FROM {$this->table} {$conditions}";

        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }

        if ($limit !== null) {
            $sql .= " LIMIT {$limit}";
        }

        if ($offset !== null) {
            $sql .= " OFFSET {$offset}";
        }

        $rows = $this->database->getAll($sql, $params);

        return $this->mapper->mapCollection($rows, $this->entity);
    }

    protected function execute(string $sql, array $params = []): void
    {
        $this->database->insert($sql, $params);
    }
}
