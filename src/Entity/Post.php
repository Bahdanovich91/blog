<?php

declare(strict_types=1);

namespace App\Entity;

use App\Core\Entity\EntityInterface;

class Post implements EntityInterface
{
    protected int $id;

    protected string $title;

    protected string $slug;
    protected ?string $image = null;

    protected ?string $description = null;

    protected string $content;

    protected int $viewCount = 0;

    protected string $createdAt;

    protected array $categories = [];

    public function __construct(array $data = [])
    {
        $this->id = (int)($data['id'] ?? 0);
        $this->title = $data['title'] ?? '';
        $this->slug = $data['slug'] ?? '';
        $this->image = $data['image'] ?? null;
        $this->description = $data['description'] ?? null;
        $this->content = $data['content'] ?? '';
        $this->viewCount = (int)($data['view_count'] ?? 0);
        $this->createdAt = $data['created_at'] ?? date('Y-m-d H:i:s');
    }

    public function setCategories(array $categories): void
    {
        $this->categories = $categories;
    }

    public function getCategories(): array
    {
        return $this->categories;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getViewCount(): int
    {
        return $this->viewCount;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getCreatedAtFormatted(): string
    {
        return date('F d Y', strtotime($this->createdAt));
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'image' => $this->image,
            'description' => $this->description,
            'content' => $this->content,
            'view_count' => $this->viewCount,
            'created_at' => $this->createdAt,
            'created_at_formatted' => $this->getCreatedAtFormatted(),
            'categories' => array_map(fn($c) => $c->toArray(), $this->categories),
        ];
    }
}
