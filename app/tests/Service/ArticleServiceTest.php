<?php
/**
 * Article service test.
 */

namespace App\Tests\Service;

use App\Entity\Article;
use App\Entity\Category;
use App\Repository\ArticleRepository;
use App\Service\ArticleService;
use App\Service\ArticleServiceInterface;
use App\Service\CategoryService;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * ArticleServiceTest.
 */
class ArticleServiceTest extends WebTestCase
{
    /**
     * Paginator.
     */
    private PaginatorInterface $paginator;

    /**
     * Category service.
     */
    private CategoryService $categoryService;

    /**
     * Article repository.
     */
    private ArticleRepository $articleRepository;

    /**
     * Article service.
     */
    private ?ArticleServiceInterface $articleService;

    /**
     * Set up.
     */
    public function setUp(): void
    {
        $this->articleRepository = $this->createMock(ArticleRepository::class);
        $this->categoryService = $this->createMock(CategoryService::class);
        $this->paginator = $this->createMock(PaginatorInterface::class);
        $this->articleService = new ArticleService($this->categoryService, $this->articleRepository, $this->paginator);
    }

    /**
     * Test getPaginatedList method.
     */
    public function testGetPaginatedList(): void
    {
        // given
        $page = 1;
        $pagination = $this->createMock(PaginationInterface::class);

        $this->paginator->expects($this->once())
            ->method('paginate')
            ->with(
                $this->articleRepository->queryAll(),
                $page,
                $this->articleService::PAGINATOR_ITEMS_PER_PAGE
            )
            ->willReturn($pagination);

        // when
        $result = $this->articleService->getPaginatedList($page);

        // then
        $this->assertSame($pagination, $result);
    }

    /**
     * Test save category method.
     */
    public function testSave(): void
    {
        // given
        $article = $this->createMock(Article::class);

        // when
        $this->articleRepository->expects($this->once())
            ->method('save')
            ->with($article);

        // then
        $this->articleService->save($article);
    }

    /**
     * Test delete category method.
     */
    public function testDelete(): void
    {
        // given
        $article = $this->createMock(Article::class);

        // when
        $this->articleRepository->expects($this->once())
            ->method('delete')
            ->with($article);

        // then
        $this->articleService->delete($article);
    }

    /**
     * Test prepareFilters method.
     */
    public function testPrepareFiltersWithValidCategoryId(): void
    {
        // Given
        $categoryId = 1;
        $filters = ['category_id' => $categoryId];
        $category = new Category();
        $category->setName('Test Category');

        $this->categoryService->expects($this->once())
            ->method('findOneById')
            ->with($categoryId)
            ->willReturn($category);

        // When
        $resultFilters = $this->articleService->prepareFilters($filters);

        // Then
        $this->assertArrayHasKey('category', $resultFilters);
        $this->assertSame($category, $resultFilters['category']);
    }
}
