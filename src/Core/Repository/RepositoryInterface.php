<?php

declare(strict_types=1);

namespace App\Core\Repository;

use api\core\Domain\Entity\CollectionInterface;
use api\core\Domain\Entity\EntityInterface;
use api\core\Infrastructure\Persistence\Model\CriteriaInterface;

interface RepositoryInterface
{
    public function findOne(int $id);
}
