<?php

declare(strict_types=1);

namespace App\Core\Container;

use Exception;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionParameter;

class Container
{
    private array $instances = [];

    public function set(string $id, $value): void
    {
        $this->instances[$id] = $value;
    }

    public function get(string $id): object
    {
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        if (!class_exists($id)) {
            throw new Exception("Class $id not found");
        }

        $reflection = new ReflectionClass($id);
        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            $object = new $id();
        } else {
            $params = $constructor->getParameters();
            $args = [];

            foreach ($params as $param) {
                $args[] = $this->resolveParameter($param);
            }

            $object = $reflection->newInstanceArgs($args);
        }

        $this->instances[$id] = $object;

        return $object;
    }

    private function resolveParameter(\ReflectionParameter $param)
    {
        $type = $param->getType();

        return match(true) {
            $type === null => throw new Exception("Cannot resolve untyped parameter: {$param->getName()}"),
            !$type instanceof ReflectionNamedType => throw new Exception("Cannot resolve union/complex type: {$param->getName()}"),
            $type->isBuiltin() && $param->isDefaultValueAvailable() => $param->getDefaultValue(),
            $type->isBuiltin() => throw new Exception("Cannot resolve builtin parameter: {$param->getName()}"),
            default => $this->get($type->getName()),
        };
    }
}
