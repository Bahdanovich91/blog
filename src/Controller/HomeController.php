<?php
declare(strict_types=1);

namespace App\Controller;

use App\Core\Controller\BaseController;
use App\Core\Routing\Route;
use App\Service\CategoryService;
use Psr\Http\Message\ServerRequestInterface;

class HomeController extends BaseController
{
    public function __construct(
        private readonly CategoryService $categoryService
    ) {
        parent::__construct();
    }

    #[Route('/')]
    public function index(ServerRequestInterface $request)
    {
        $sections = $this->categoryService->getCategoriesWithLatestPosts(3);

        $templateSections = array_map(fn($s) => [
            'category' => $s['category']->toArray(),
            'posts' => array_map(fn($p) => $p->toArray(), $s['posts']),
        ], $sections);

        return $this->render('home.tpl', [
            'sections' => $templateSections,
            'page_title' => 'Blogy — Home',
        ]);
    }
}
