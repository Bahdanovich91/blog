<?php

namespace App\Controller;

use App\Core\Route;

class TestController
{
    #[Route('/test')]
    public function test(): void
    {
        var_dump(1111);
    }

    #[Route('/test/{id}')]
    public function testShow(string $id): void
    {
        var_dump($id);
    }
}