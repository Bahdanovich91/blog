<?php

namespace App\Controller;

use App\Core\BaseController;
use App\Core\Route;

class TestController extends BaseController
{
    #[Route('/test')]
    public function test(): void
    {
        $this->render('test.tpl', [
            'value' => 1111
        ]);
    }

    #[Route('/test/{id}')]
    public function testShow(string $id): void
    {
        var_dump($id);
    }
}