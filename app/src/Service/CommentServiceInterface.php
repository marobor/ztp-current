<?php
/**
 * Comment service interface.
 */

namespace App\Service;

use App\Entity\Article;
use App\Entity\Comment;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * CommentServiceInterface.
 */
interface CommentServiceInterface
{
    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface;

    /**
     * Save entity.
     *
     * @param Comment $comment Comment entity
     */
    public function save(Comment $comment): void;

    /**
     * Delete entity.
     *
     * @param Comment $comment Comment entity
     */
    public function delete(Comment $comment): void;

    /**
     * Get comment for article.
     *
     * @param Article $article Article entity
     */
    public function getForArticle(Article $article): array;
}
