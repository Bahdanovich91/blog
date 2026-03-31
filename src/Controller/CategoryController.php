<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Controller\BaseController;
use App\Core\Routing\Route;
use App\Service\CategoryService;
use App\Service\PostService;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CategoryController extends BaseController
{
    public function __construct(
        private readonly CategoryService $categoryService,
    ) {
        parent::__construct();
    }

    #[Route('/category/{slug}')]
    public function show(ServerRequestInterface $request, string $slug): ResponseInterface
    {
        $queryParams = $request->getQueryParams();

        $sort = $queryParams['sort'] ?? 'date';
        $page = (int) ($queryParams['page'] ?? 1);

        $result = $this->categoryService->getCategoryPageData($slug, $sort, $page);

        $html = $this->render('category.tpl', $result);

        return new Response(200, [], $html);
    }
}