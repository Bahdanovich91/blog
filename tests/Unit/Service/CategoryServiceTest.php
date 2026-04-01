<?php

declare(strict_types=1);

namespace Tests\Unit\Service;

use App\Dto\CategoryPageDto;
use App\Dto\HomePageDto;
use App\Entity\Category;
use App\Entity\Post;
use App\Enum\SortType;
use App\Enum\SortDirection;
use App\Repositories\CategoryRepository;
use App\Repositories\PostRepository;
use App\Service\CategoryService;
use Mockery;
use PHPUnit\Framework\TestCase;

class CategoryServiceTest extends TestCase
{
    use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    private CategoryRepository $categoryRepository;
    private PostRepository $postRepository;
    private CategoryService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->categoryRepository = Mockery::mock(CategoryRepository::class);
        $this->postRepository = Mockery::mock(PostRepository::class);
        $this->service = new CategoryService($this->categoryRepository, $this->postRepository);
    }

    public function testGetHomePageDataReturnsSections(): void
    {
        $category = new Category(['id' => 1, 'name' => 'Tech', 'slug' => 'tech']);
        $posts = [
            new Post(['id' => 1, 'title' => 'Post 1']),
            new Post(['id' => 2, 'title' => 'Post 2']),
            new Post(['id' => 3, 'title' => 'Post 3']),
        ];

        $this->categoryRepository
            ->shouldReceive('findAll')
            ->once()
            ->andReturn([$category]);

        $this->postRepository
            ->shouldReceive('findByCategory')
            ->with(1, 3)
            ->once()
            ->andReturn($posts);

        $dto = $this->service->getHomePageData(3);

        $this->assertInstanceOf(HomePageDto::class, $dto);
        $this->assertCount(1, $dto->sections);
        $this->assertSame($category, $dto->sections[0]['category']);
        $this->assertSame($posts, $dto->sections[0]['posts']);
    }

    public function testGetHomePageDataSkipsCategoriesWithoutPosts(): void
    {
        $category = new Category(['id' => 1, 'name' => 'Empty', 'slug' => 'empty']);

        $this->categoryRepository
            ->shouldReceive('findAll')
            ->once()
            ->andReturn([$category]);

        $this->postRepository
            ->shouldReceive('findByCategory')
            ->with(1, 3)
            ->once()
            ->andReturn([]);

        $dto = $this->service->getHomePageData(3);

        $this->assertCount(0, $dto->sections);
    }

    public function testGetCategoryPageDataReturnsDto(): void
    {
        $category = new Category(['id' => 2, 'name' => 'Design', 'slug' => 'design']);
        $posts = [new Post(['id' => 4, 'title' => 'Design Post'])];
        $paginationResult = [
            'posts' => $posts,
            'sort' => 'date',
            'direction' => 'DESC',
            'currentPage' => 1,
            'totalPages' => 1,
        ];

        $this->categoryRepository
            ->shouldReceive('findOneBy')
            ->with(['slug' => 'design'])
            ->once()
            ->andReturn($category);

        $this->postRepository
            ->shouldReceive('getPaginatedByCategory')
            ->with(2, SortType::DATE, SortDirection::DESC, 1)
            ->once()
            ->andReturn($paginationResult);

        $dto = $this->service->getCategoryPageData('design', SortType::DATE, SortDirection::DESC, 1);

        $this->assertInstanceOf(CategoryPageDto::class, $dto);
        $this->assertSame($category->toArray(), $dto->category);
        $this->assertCount(1, $dto->posts);
        $this->assertEquals('date', $dto->sort);
        $this->assertEquals('DESC', $dto->direction);
    }

    public function testGetCategoryPageDataReturnsNullWhenCategoryNotFound(): void
    {
        $this->categoryRepository
            ->shouldReceive('findOneBy')
            ->with(['slug' => 'missing'])
            ->once()
            ->andReturn(null);

        $dto = $this->service->getCategoryPageData('missing', SortType::DATE, SortDirection::DESC, 1);

        $this->assertNull($dto);
    }
}
