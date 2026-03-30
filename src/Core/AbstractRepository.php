<?php
declare(strict_types=1);

namespace App\Core;

abstract class AbstractRepository
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
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $row = $this->database->getRow($sql, [$id]);

        return $row ? $this->mapper->map($row, $this->entity) : null;
    }

    public function findAll(): array
    {
        $sql = "SELECT * FROM {$this->table}";
        $rows = $this->database->getAll($sql);

        return $this->mapper->mapCollection($rows, $this->entity);
    }
}
