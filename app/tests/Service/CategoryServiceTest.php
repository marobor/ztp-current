<?php
/**
 * Category service test.
 */

namespace App\Tests\Service;

use App\Entity\Category;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use App\Service\CategoryService;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class CategoryServiceTest.
 */
class CategoryServiceTest extends WebTestCase
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
     * Category repository.
     */
    private CategoryRepository $categoryRepository;

    /**
     * Article repository.
     */
    private ArticleRepository $articleRepository;

    /**
     * Set up.
     */
    public function setUp(): void
    {
        $this->categoryRepository = $this->createMock(CategoryRepository::class);
        $this->paginator = $this->createMock(PaginatorInterface::class);
        $this->articleRepository = $this->createMock(ArticleRepository::class);
        $this->categoryService = new CategoryService($this->categoryRepository, $this->paginator, $this->articleRepository);
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
                $this->categoryRepository->queryAll(),
                $page,
                $this->categoryService::PAGINATOR_ITEMS_PER_PAGE
            )
            ->willReturn($pagination);

        // when
        $result = $this->categoryService->getPaginatedList($page);

        // then
        $this->assertSame($pagination, $result);
    }

    /**
     * Test save category method.
     */
    public function testSave(): void
    {
        // given
        $category = $this->createMock(Category::class);

        // when
        $this->categoryRepository->expects($this->once())
            ->method('save')
            ->with($category);

        // then
        $this->categoryService->save($category);
    }

    /**
     * Test delete category method.
     */
    public function testDelete(): void
    {
        // given
        $category = $this->createMock(Category::class);

        // when
        $this->categoryRepository->expects($this->once())
            ->method('delete')
            ->with($category);

        // then
        $this->categoryService->delete($category);
    }

    /**
     * Test canBeDeleted method when category can be deleted.
     */
    public function testCanBeDeletedWhenCategoryCanBeDeleted(): void
    {
        // given
        $category = $this->createMock(Category::class);

        // when
        $this->articleRepository->expects($this->once())
            ->method('countByCategory')
            ->with($category)
            ->willReturn(0);

        // then
        $this->assertTrue($this->categoryService->canBeDeleted($category));
    }

    /**
     * Test canBeDeleted method when category cannot be deleted.
     */
    public function testCanBeDeletedWhenCategoryCannotBeDeleted(): void
    {
        // given
        $category = $this->createMock(Category::class);

        // when
        $this->articleRepository->expects($this->once())
            ->method('countByCategory')
            ->with($category)
            ->willReturn(5);

        // then
        $this->assertFalse($this->categoryService->canBeDeleted($category));
    }

    /**
     * Test canBeDeleted method when NoResultException is thrown.
     */
    public function testCanBeDeletedNoResultException(): void
    {
        // given
        $category = new Category();

        // Mock articleRepository to throw NoResultException
        $this->articleRepository->method('countByCategory')
            ->will($this->throwException(new NoResultException()));

        // when
        $result = $this->categoryService->canBeDeleted($category);

        // then
        $this->assertFalse($result);
    }

    /**
     * Test canBeDeleted method when NonUniqueResultException is thrown.
     */
    public function testCanBeDeletedNonUniqueResultException(): void
    {
        // given
        $category = new Category();

        // Mock articleRepository to throw NoResultException
        $this->articleRepository->method('countByCategory')
            ->will($this->throwException(new NonUniqueResultException()));

        // when
        $result = $this->categoryService->canBeDeleted($category);

        // then
        $this->assertFalse($result);
    }

    /**
     * Test findOneById method.
     */
    public function testFindOneById(): void
    {
        // given
        $expectedCategory = new Category();
        $expectedCategory->setName('Category test');
        $expectedCategoryId = 1;

        // when
        $this->categoryRepository->expects($this->once())
            ->method('findOneById')
            ->with($expectedCategoryId)
            ->willReturn($expectedCategory);

        $resultCategory = $this->categoryService->findOneById($expectedCategoryId);

        // then
        $this->assertSame($expectedCategory, $resultCategory);
    }
}
