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
        $this->getRoutes();
    }

    /**
     * @throws ReflectionException
     */
    private function getRoutes(): void
    {
        foreach ($this->findControllerClasses() as $controllerClass) {
            $reflection = new ReflectionClass($controllerClass);
            foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                $attributes = $method->getAttributes(Route::class);
                foreach ($attributes as $attribute) {
                    /** @var Route $route */
                    $route = $attribute->newInstance();
                    $key = $this->getRouteKey($route->methods, $route->path);
                    $this->routes[$key] = [
                        'controller' => $controllerClass,
                        'method' => $method->getName(),
                        'path' => $route->path,
                        'methods' => $route->methods,
                    ];
                }
            }
        }
    }

    private function findControllerClasses(): array
    {
        $classes = [];
        $controllerDir = __DIR__ . '/../Controller';
        $namespace = 'App\\Controller';

        var_dump($controllerDir);

        $baseDir = rtrim($controllerDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($controllerDir, FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $fullPath = $file->getPathname();
                $relativePath = substr($fullPath, strlen($baseDir));
                $className = $namespace . '\\' . str_replace(DIRECTORY_SEPARATOR, '\\', substr($relativePath, 0, -4));
                if (class_exists($className, true)) {
                    $classes[] = $className;
                }
            }
        }

        var_dump($classes);

        return $classes;
    }

    private function getRouteKey(array $methods, string $path): string
    {
        sort($methods);
        return implode('|', $methods) . '|' . $path;
    }

    public function dispatch(string $uri, string $method)
    {
        $uri = parse_url($uri, PHP_URL_PATH);
        $uri = rtrim($uri, '/');

        if (empty($uri)) {
            $uri = '/';
        }

        foreach ($this->routes as $key => $route) {
            if (in_array($method, $route['methods']) && $route['path'] === $uri) {
                $controllerClass = $route['controller'];
                $action = $route['method'];
                $controller = new $controllerClass();

                return $controller->$action();
            }
        }

        http_response_code(404);
        echo "404 Not Found";
    }
}
