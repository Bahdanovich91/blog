<?php

namespace App\Core\Routing;

use App\Core\Container\Container;
use FilesystemIterator;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionNamedType;

class Router
{
    private array $routes = [];
    private string $controllerDir = __DIR__ . '/../../Controller';
    private string $controllerNamespace = 'App\\Controller';

    /**
     * @throws ReflectionException
     */
    public function __construct(private Container $container)
    {
        $this->collectRoutes();
    }

    /**
     * @throws ReflectionException
     */
    private function collectRoutes(): void
    {
        foreach ($this->findControllerClasses() as $controllerClass) {
            $reflection = new ReflectionClass($controllerClass);

            foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                $attributes = $method->getAttributes(Route::class);

                foreach ($attributes as $attribute) {
                    /** @var Route $route */
                    $route = $attribute->newInstance();

                    $this->routes[] = [
                        'controller' => $controllerClass,
                        'method' => $method->getName(),
                        'path' => $route->path,
                        'methods' => $route->methods,
                        'pattern' => $this->convertPathToRegex($route->path),
                    ];
                }
            }
        }
    }

    private function convertPathToRegex(string $path): string
    {
        $pattern = preg_replace('#\{(\w+)}#', '(?P<$1>[^/]+)', $path);
        $pattern = rtrim($pattern, '/');
        if ($pattern === '') {
            $pattern = '/';
        }

        return '#^' . $pattern . '$#';
    }

    private function findControllerClasses(): array
    {
        $classes = [];

        $baseDir = rtrim($this->controllerDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->controllerDir, FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $fullPath = $file->getPathname();
                $relativePath = substr($fullPath, strlen($baseDir));

                $className = $this->controllerNamespace . '\\' . str_replace(
                        DIRECTORY_SEPARATOR,
                        '\\',
                        substr($relativePath, 0, -4)
                    );

                if (class_exists($className, true)) {
                    $classes[] = $className;
                }
            }
        }

        return $classes;
    }

    /**
     * @throws ReflectionException
     */
    public function dispatch(string $uri, string $method, ServerRequestInterface $request): ResponseInterface
    {
        foreach ($this->routes as $route) {
            if (!in_array($method, $route['methods'])) {
                continue;
            }

            if (preg_match($route['pattern'], $uri, $matches)) {
                $params = $this->extractParams($matches);
                $controller = $this->container->get($route['controller']);
                $reflection = new \ReflectionMethod($controller, $route['method']);

                $args = [];
                foreach ($reflection->getParameters() as $param) {
                    $type = $param->getType();

                    $args[] = match (true) {
                        $type instanceof \ReflectionNamedType && $type->getName() === ServerRequestInterface::class => $request,
                        default => $params[$param->getName()] ?? null,
                    };
                }

                /** @var ResponseInterface $response */
                $response = call_user_func_array([$controller, $route['method']], $args);

                return $response;
            }
        }

        return new Response(404, [], '404 Not Found');
    }

    private function extractParams(array $matches): array
    {
        return array_filter($matches, function ($key) {
            return !is_int($key);
        }, ARRAY_FILTER_USE_KEY);
    }
}
