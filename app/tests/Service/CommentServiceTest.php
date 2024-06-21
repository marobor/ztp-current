<?php
/**
 * Comment service test.
 */

namespace App\Tests\Service;

use App\Entity\Article;
use App\Entity\Comment;
use App\Repository\CommentRepository;
use App\Service\CommentService;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class CommentServiceTest.
 */
class CommentServiceTest extends WebTestCase
{
    /**
     * Paginator.
     */
    private PaginatorInterface $paginator;

    /**
     * Comment service.
     */
    private CommentService $commentService;

    /**
     * Comment repository.
     */
    private CommentRepository $commentRepository;

    /**
     * Set up.
     */
    public function setUp(): void
    {
        $this->commentRepository = $this->createMock(CommentRepository::class);
        $this->paginator = $this->createMock(PaginatorInterface::class);
        $this->commentService = new CommentService($this->commentRepository, $this->paginator);
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
                $this->commentRepository->queryAll(),
                $page,
                $this->commentService::PAGINATOR_ITEMS_PER_PAGE
            )
            ->willReturn($pagination);

        // when
        $result = $this->commentService->getPaginatedList($page);

        // then
        $this->assertSame($pagination, $result);
    }

    /**
     * Test save comment method.
     */
    public function testSave(): void
    {
        // given
        $comment = $this->createMock(Comment::class);

        // when
        $this->commentRepository->expects($this->once())
            ->method('save')
            ->with($comment);

        // then
        $this->commentService->save($comment);
    }

    /**
     * Test delete comment method.
     */
    public function testDelete(): void
    {
        // given
        $comment = $this->createMock(Comment::class);

        // when
        $this->commentRepository->expects($this->once())
            ->method('delete')
            ->with($comment);

        // then
        $this->commentService->delete($comment);
    }

    /**
     * Test delete comment method.
     */
    public function testGetForArticle(): void
    {
        // given
        $comment = $this->createMock(Comment::class);
        $article = $this->createMock(Article::class);
        $expectedComments = [$comment];

        // when
        $this->commentRepository->expects($this->once())
            ->method('queryForArticle')
            ->with($article)
            ->willReturn($expectedComments);

        // then
        $this->assertSame($expectedComments, $this->commentService->getForArticle($article));
    }
}
