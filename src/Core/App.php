<?php

declare(strict_types=1);

namespace App\Core;

use Throwable;

readonly class App
{
    public function __construct(
        private Router $router
    ) {
    }

    public function handle(): void
    {
        try {
            $uri = $this->getUri();
            $method = $this->getMethod();

            $this->router->dispatch($uri, $method);
        } catch (Throwable $e) {
            http_response_code($e->getCode());

            echo $e->getMessage();
        }
    }

    private function getUri(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $uri = parse_url($uri, PHP_URL_PATH);

        return rtrim($uri, '/') ?: '/';
    }

    private function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }
}
