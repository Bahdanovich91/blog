<?php

declare(strict_types=1);

namespace App\Core\Http;

use App\Core\Routing\Router;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use Nyholm\Psr7Server\ServerRequestCreator;
use Psr\Http\Message\ServerRequestInterface;
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
            $response = $this->router->dispatch(
                $this->getUri(),
                $this->getMethod(),
                $this->getRequest()
            );
        } catch (Throwable $e) {
            $response = new Response(
                $e->getCode() ?: 500,
                [],
                $e->getMessage()
            );
        }

        $emitter = new SapiEmitter();
        $emitter->emit($response);
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

    private function getRequest(): ServerRequestInterface
    {
        $factory = new Psr17Factory();
        $creator = new ServerRequestCreator($factory, $factory, $factory, $factory);

        return $creator->fromGlobals();
    }
}
