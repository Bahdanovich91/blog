<?php

namespace App\Core;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

class Router
{
    private array $routes = [];

    /**
     * @throws ReflectionException
     */
    public function __construct()
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
        $pattern = preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $path);

        return '#^' . rtrim($pattern, '/') . '$#';
    }

    private function findControllerClasses(): array
    {
        $classes = [];
        $controllerDir = __DIR__ . '/../Controller';
        $namespace = 'App\\Controller';

        $baseDir = rtrim($controllerDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($controllerDir, FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $fullPath = $file->getPathname();
                $relativePath = substr($fullPath, strlen($baseDir));

                $className = $namespace . '\\' . str_replace(
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

    public function dispatch(string $uri, string $method): void
    {
        foreach ($this->routes as $route) {
            if (!in_array($method, $route['methods'])) {
                continue;
            }

            if (preg_match($route['pattern'], $uri, $matches)) {
                $params = $this->extractParams($matches);

                $controller = new $route['controller']();

                call_user_func_array(
                    [$controller, $route['method']],
                    $params
                );

                return;
            }
        }

        http_response_code(404);
        echo "404 Not Found";
    }

    private function extractParams(array $matches): array
    {
        $params = [];

        foreach ($matches as $key => $value) {
            if (!is_int($key)) {
                $params[] = $value;
            }
        }

        return $params;
    }
}
