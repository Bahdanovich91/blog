<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Controller\BaseController;
use App\Core\Routing\Route;
use App\Service\PostService;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class PostController extends BaseController
{
    public function __construct(
        private readonly PostService $postService
    ) {
        parent::__construct();
    }

    #[Route('/post/{slug}')]
    public function show(ServerRequestInterface $request, string $slug): ResponseInterface
    {
        $dto = $this->postService->getPostPageData($slug);
        if ($dto === null) {
            return new Response(404, [], 'Post not found');
        }

        return new Response(
            200,
            [],
            $this->render('post.tpl', $dto->toView())
        );
    }
}
