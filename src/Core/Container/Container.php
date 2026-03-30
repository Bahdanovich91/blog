<?php

declare(strict_types=1);

namespace App\Core\Container;

use Exception;
use ReflectionClass;
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
        // Если уже создан экземпляр, возвращаем его
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        // Рефлексия для класса
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

        // Сохраняем для singleton-подобного поведения
        $this->instances[$id] = $object;

        return $object;
    }

    private function resolveParameter(ReflectionParameter $param)
    {
        $type = $param->getType();

        if ($type === null) {
            throw new Exception("Cannot resolve untyped parameter: {$param->getName()}");
        }

        if ($type->isBuiltin()) {
            if ($param->isDefaultValueAvailable()) {
                return $param->getDefaultValue();
            }
            throw new Exception("Cannot resolve builtin parameter: {$param->getName()}");
        }

        return $this->get($type->getName());
    }
}
