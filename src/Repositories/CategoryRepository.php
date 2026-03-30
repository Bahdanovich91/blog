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

}
