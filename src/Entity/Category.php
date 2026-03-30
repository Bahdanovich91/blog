<?php

declare(strict_types=1);

namespace App\Entity;

class Category
{
    protected int $id;
    protected string $name;
    protected string $slug;
    protected ?string $description = null;

    public function __construct(array $data = [])
    {
        $this->id = (int)($data['id'] ?? 0);
        $this->name = $data['name'] ?? '';
        $this->slug = $data['slug'] ?? '';
        $this->description = $data['description'] ?? null;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}
