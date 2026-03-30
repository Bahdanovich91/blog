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
        $sections = $this->categoryService->getCategoriesWithLatestPosts(3);

        $templateSections = array_map(fn($s) => [
            'category' => $s['category']->toArray(),
            'posts' => array_map(fn($p) => $p->toArray(), $s['posts']),
        ], $sections);

        $html = $this->render('home.tpl', [
            'sections' => $templateSections,
            'page_title' => 'Blogy',
        ]);

        return new Response(200, [], $html);
    }
}
