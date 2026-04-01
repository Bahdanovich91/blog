<?php

declare(strict_types=1);

namespace App\Core\Repository;

use App\Core\Entity\EntityInterface;

interface RepositoryInterface
{
    public function findOneBy(array $criteria): ?EntityInterface;
    public function findBy(array $criteria = [], string $orderBy = '', ?int $limit = null, ?int $offset = null ): array;
}
