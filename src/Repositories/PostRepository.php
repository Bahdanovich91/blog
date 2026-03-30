<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\AbstractRepository;
use App\Entity\Post;

class PostRepository extends AbstractRepository
{
    protected string $table = 'posts';
    protected string $entity = Post::class;
}
