<?php
declare(strict_types=1);

namespace App\Controller;

use App\Core\Controller\BaseController;
use App\Core\Routing\Route;
use App\Service\CategoryService;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;

class HomeController extends BaseController
{
    public function __construct(
        private readonly CategoryService $categoryService
    ) {
        parent::__construct();
    }

    #[Route('/')]
    public function index(ServerRequestInterface $request): Response
    {
        $dto = $this->categoryService->getHomePageData();

        return new Response(
            200,
            [],
            $this->render('home.tpl', $dto->toView())
        );
    }
}
