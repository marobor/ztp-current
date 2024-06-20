<?php
/**
 * Comment repository.
 */

namespace App\Repository;

use App\Entity\Article;
use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comment>
 *
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
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
     * @param ManagerRegistry $registry Manager registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    /**
     * Query all records.
     *
     * @return QueryBuilder Query builder
     */
    public function queryAll(): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder()
            ->orderBy('comment.id', 'ASC');
    }

    /**
     * Query all records.
     *
     * @param Article $article Article entity
     *
     * @return array Comments
     */
    public function queryForArticle(Article $article): array
    {
        return $this->getOrCreateQueryBuilder()
            ->where('comment.article = :article')
            ->setParameter('article', $article)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Save entity.
     *
     * @param Comment $comment Comment entity
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function save(Comment $comment): void
    {
        $this->_em->persist($comment);
        $this->_em->flush();
    }

    /**
     * @param Comment $comment Comment entity
     */
    public function delete(Comment $comment): void
    {
        $this->_em->remove($comment);
        $this->_em->flush();
    }

    /**
     * Get or create new query builder.
     *
     * @param QueryBuilder|null $queryBuilder Query builder
     *
     * @return QueryBuilder Query builder
     */
    private function getOrCreateQueryBuilder(?QueryBuilder $queryBuilder = null): QueryBuilder
    {
        return $queryBuilder ?? $this->createQueryBuilder('comment');
    }
}
