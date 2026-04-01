<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Controller\BaseController;
use App\Core\Routing\Route;
use App\Enum\SortDirection;
use App\Enum\SortType;
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

        $sort = SortType::fromString($queryParams['sort'] ?? 'date');
        $direction = SortDirection::fromString($queryParams['direction'] ?? 'DESC');
//        $sort = $queryParams['sort'] ?? 'date';
//        $direction = $queryParams['direction'] ?? 'DESC';
        $page = (int) ($queryParams['page'] ?? 1);

        $dto = $this->categoryService->getCategoryPageData($slug, $sort, $direction, $page);
        if ($dto === null) {
            return new Response(404, [], 'Category not found');
        }

        return new Response(
            200,
            [],
            $this->render('category.tpl', $dto->toView($slug))
        );
    }
}
