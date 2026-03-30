<?php

declare(strict_types=1);

namespace App\Core;

class ArrayToEntityMapper
{
    public function map(array $data, string $entityClass)
    {
        return new $entityClass($data);
    }

    public function mapCollection(array $data, string $entityClass): array
    {
        $collection = [];
        foreach ($data as $row) {
            $collection[] = $this->map($row, $entityClass);
        }
        return $collection;
    }
}
