<?php
/**
 * Article controller.
 */

namespace App\Controller;

use App\Entity\Comment;
use App\Form\Type\CommentType;
use App\Service\CommentServiceInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class CommentController.
 */
#[\Symfony\Component\Routing\Attribute\Route(
    '/comment'
)]
class CommentController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param CommentServiceInterface $commentService Comment service
     * @param TranslatorInterface     $translator     Translator interface
     */
    public function __construct(private readonly CommentServiceInterface $commentService, private readonly TranslatorInterface $translator)
    {
    }

    /**
     * Index action.
     *
     * @param Request $request HTTP request
     *
     * @return Response Http response
     */
    #[\Symfony\Component\Routing\Attribute\Route(
        '/',
        name: 'comment_index',
        methods: 'GET'
    )]
    public function index(Request $request): Response
    {
        $pagination = $this->commentService->getPaginatedList(
            $request->query->getInt('page', 1)
        );

        return $this->render(
            'comment/index.html.twig',
            ['pagination' => $pagination]
        );
    }

    /**
     * Delete action.
     *
     * @param Request $request HTTP request
     * @param Comment $comment Comment entity
     *
     * @return Response Http response
     */
    #[\Symfony\Component\Routing\Attribute\Route(
        '/{id}/delete',
        name: 'comment_delete',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET|DELETE'
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Comment $comment): Response
    {
        $form = $this->createForm(CommentType::class, $comment, [
            'method' => 'DELETE',
            'action' => $this->generateUrl('comment_delete', ['id' => $comment->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->commentService->delete($comment);

            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            return $this->redirectToRoute('comment_index');
        }

        return $this->render(
            'article/delete.html.twig',
            [
                'form' => $form->createView(),
                'comment' => $comment,
            ]
        );
    }
}
