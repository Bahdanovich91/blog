<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\AbstractRepository;
use App\Entity\Category;

class CategoryRepository extends AbstractRepository
{
    protected string $table = 'categories';
    protected string $entity = Category::class;
}
