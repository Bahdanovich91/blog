<?php

declare(strict_types=1);

namespace App\Dto;

final readonly class PostPageDto
{
    public function __construct(
        public array $post,
        public string $contentHtml,
        public array $similarPosts,
        public string $title,
    ) {}

    public function toView(): array
    {
        return [
            'post' => $this->post,
            'post_content_html' => $this->contentHtml,
            'similar_posts' => $this->similarPosts,
            'page_title' => $this->title,
        ];
    }
}
