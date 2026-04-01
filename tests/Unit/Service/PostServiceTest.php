<?php

declare(strict_types=1);

namespace Tests\Unit\Service;

use App\Dto\PostPageDto;
use App\Entity\Category;
use App\Entity\Post;
use App\Enum\SortType;
use App\Repositories\CategoryRepository;
use App\Repositories\PostRepository;
use App\Service\PostService;
use Mockery;
use PHPUnit\Framework\TestCase;

class PostServiceTest extends TestCase
{
    use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    private PostRepository $postRepository;
    private CategoryRepository $categoryRepository;
    private PostService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->postRepository = Mockery::mock(PostRepository::class);
        $this->categoryRepository = Mockery::mock(CategoryRepository::class);
        $this->service = new PostService($this->postRepository, $this->categoryRepository);
    }

    public function testGetPostPageDataReturnsDto(): void
    {
        $post = new Post([
            'id' => 1,
            'slug' => 'test-post',
            'title' => 'Test Post',
            'content' => "First paragraph\n\nSecond paragraph",
            'view_count' => 10,
        ]);
        $categories = [new Category(['id' => 1, 'name' => 'Tech'])];
        $similarPosts = [new Post(['id' => 2, 'title' => 'Similar'])];

        $this->postRepository
            ->shouldReceive('findOneBy')
            ->with(['slug' => 'test-post'])
            ->once()
            ->andReturn($post);

        $this->categoryRepository
            ->shouldReceive('findByPost')
            ->with(1)
            ->once()
            ->andReturn($categories);

        $this->postRepository
            ->shouldReceive('incrementViews')
            ->with(1)
            ->once();

        $this->postRepository
            ->shouldReceive('findSimilar')
            ->with(1, [1])
            ->once()
            ->andReturn($similarPosts);

        $dto = $this->service->getPostPageData('test-post');

        $this->assertInstanceOf(PostPageDto::class, $dto);
        $this->assertSame($post->toArray(), $dto->post);
        $this->assertStringContainsString('<p>First paragraph</p>', $dto->contentHtml);
        $this->assertCount(1, $dto->similarPosts);
    }

    public function testGetPostPageDataReturnsNullWhenPostNotFound(): void
    {
        $this->postRepository
            ->shouldReceive('findOneBy')
            ->with(['slug' => 'missing'])
            ->once()
            ->andReturn(null);

        $dto = $this->service->getPostPageData('missing');

        $this->assertNull($dto);
    }

    public function testGetPaginatedPostsByCategory(): void
    {
        $posts = [new Post(['id' => 1]), new Post(['id' => 2])];

        $this->postRepository
            ->shouldReceive('countByCategory')
            ->with(1)
            ->once()
            ->andReturn(12);

        $this->postRepository
            ->shouldReceive('findByCategory')
            ->with(1, 9, 0, 'p.created_at DESC')
            ->once()
            ->andReturn($posts);

        $result = $this->service->getPaginatedPostsByCategory(1, SortType::DATE, 1, 9);

        $this->assertSame($posts, $result['posts']);
        $this->assertEquals(12, $result['total']);
        $this->assertEquals(2, $result['totalPages']);
        $this->assertEquals(1, $result['currentPage']);
        $this->assertEquals('date', $result['sort']);
    }

    public function testRecordView(): void
    {
        $this->postRepository
            ->shouldReceive('incrementViews')
            ->with(5)
            ->once();

        $this->service->recordView(5);
    }
}
