<?php
/**
 * Article Service.
 */

namespace App\Service;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Doctrine\ORM\NonUniqueResultException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class ArticleService.
 */
class ArticleService implements ArticleServiceInterface
{
    /**
     * Items per page.
     *
     * Use constants to define configuration options that rarely change instead
     * of specifying them in configuration files.
     * See https://symfony.com/doc/current/best_practices.html#configuration
     *
     * @constant int
     */
    public const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Constructor.
     *
     * @param CategoryServiceInterface $categoryService   Category service
     * @param ArticleRepository        $articleRepository Article repository
     * @param PaginatorInterface       $paginator         Paginator
     */
    public function __construct(private readonly CategoryServiceInterface $categoryService, private readonly ArticleRepository $articleRepository, private readonly PaginatorInterface $paginator)
    {
    }

    /**
     * Get paginated list.
     *
     * @param int   $page    Page number
     * @param array $filters Filters
     *
     * @return PaginationInterface<string, mixed> Paginated list
     *
     * @throws NonUniqueResultException
     */
    public function getPaginatedList(int $page, array $filters = []): PaginationInterface
    {
        $filters = $this->prepareFilters($filters);

        return $this->paginator->paginate(
            $this->articleRepository->queryAll($filters),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Save entity.
     *
     * @param Article $article Article entity
     */
    public function save(Article $article): void
    {
        if (null === $article->getId()) {
            $article->setCreatedAt(new \DateTimeImmutable());
        }

        $this->articleRepository->save($article);
    }

    //    /**
    //     * Update entity.
    //     *
    //     * @param Article $article Article entity
    //     */
    //    public function update(Article $article): void
    //    {
    //        $this->save($article);
    //    }

    /**
     * Delete entity.
     *
     * @param Article $article Article entity
     */
    public function delete(Article $article): void
    {
        $this->articleRepository->delete($article);
    }

    /**
     * Prepare filters.
     *
     * @param array $filters Filters
     *
     * @return array Filters
     *
     * @throws NonUniqueResultException
     */
    public function prepareFilters(array $filters): array
    {
        $resultFilters = [];
        if (!empty($filters['category_id'])) {
            $category = $this->categoryService->findOneById($filters['category_id']);
            if (null !== $category) {
                $resultFilters['category'] = $category;
            }
        }

        return $resultFilters;
    }
}
