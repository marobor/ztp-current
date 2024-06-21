<?php
/**
 * Comment controller test.
 */

namespace App\Tests\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Enum\UserRole;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\CommentService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class CommentControllerTest.
 */
class CommentControllerTest extends WebTestCase
{
    /**
     * @var object
     */
    public $commentService;
    /**
     * Test route.
     */
    public const TEST_ROUTE = '/comment';

    /**
     * Test client.
     */
    private KernelBrowser $httpClient;

    /**
     * Translator.
     */
    private TranslatorInterface $translator;

    /**
     * Post repository.
     */
    private ?EntityManagerInterface $entityManager;

    /**
     * Set up.
     */
    public function setUp(): void
    {
        $this->httpClient = static::createClient();
        $container = static::getContainer();
        $this->entityManager = $container->get('doctrine.orm.entity_manager');
        $this->commentService = $container->get(CommentService::class);
        $this->translator = $container->get(TranslatorInterface::class);
    }

    /**
     * Test index route.
     */
    public function testIndexRoute(): void
    {
        // given
        $expectedStatusCode = 200;

        // when
        $this->httpClient->request(Request::METHOD_GET, self::TEST_ROUTE);
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

    /**
     * Test Delete route.
     */
    public function testDeleteRoute(): void
    {
        // given
        $expectedStatusCode = 302;
        $date = new \DateTimeImmutable();

        $category = new Category();
        $category->setName('Category Test');

        $article = new Article();
        $article->setTitle('Article test');
        $article->setContent('Article content');
        $article->setCreatedAt($date);
        $article->setCategory($category);

        $comment = new Comment();
        $comment->setNick('Comment test - nick');
        $comment->setEmail('test@example.com');
        $comment->setCommentContent('Comment test - content');
        $comment->setArticle($article);

        $this->entityManager->persist($category);
        $this->entityManager->persist($article);
        $this->entityManager->persist($comment);
        $this->entityManager->flush();

        // when
        $this->httpClient->request(Request::METHOD_GET, self::TEST_ROUTE.'/'.$comment->getId().'/delete');
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

    /**
     * Test Delete route.
     */
    public function testDeleteRouteForAdmin(): void
    {
        // given
        $expectedStatusCode = 200;
        $date = new \DateTimeImmutable();

        $user = $this->createUser(['ROLE_ADMIN']);
        $this->httpClient->loginUser($user);

        $category = new Category();
        $category->setName('Category Test');

        $article = new Article();
        $article->setTitle('Article test');
        $article->setContent('Article content');
        $article->setCreatedAt($date);
        $article->setCategory($category);

        $comment = new Comment();
        $comment->setNick('Comment test - nick');
        $comment->setEmail('test@example.com');
        $comment->setCommentContent('Comment test - content');
        $comment->setArticle($article);

        $this->entityManager->persist($category);
        $this->entityManager->persist($article);
        $this->entityManager->persist($comment);
        $this->entityManager->flush();

        // when
        $this->httpClient->request(Request::METHOD_GET, self::TEST_ROUTE.'/'.$comment->getId().'/delete');
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

    /**
     * Test successful delete action.
     */
    public function testDeleteActionSuccess(): void
    {
        // given
        $comment = new Comment();
        $comment->setNick('Nick');
        $comment->setCommentContent('test');
        $comment->setEmail('exampl@test.com');

        $this->entityManager->persist($comment);
        $this->entityManager->flush();
        $commentId = $comment->getId();

        $adminUser = $this->createUser([UserRole::ROLE_ADMIN->value]);
        $this->httpClient->loginUser($adminUser);

        $crawler = $this->httpClient->request(Request::METHOD_GET, self::TEST_ROUTE.'/'.$commentId.'/delete');

        $deleteButton = $this->translator->trans('action.delete');
        $form = $crawler->selectButton($deleteButton)->form();

        // when
        $this->httpClient->submit($form);
        $response = $this->httpClient->getResponse();

        // then
        $this->assertTrue($response->isRedirect());
        $this->assertEquals(self::TEST_ROUTE, $response->headers->get('Location'));

        $this->httpClient->followRedirect();

        $successMessage = $this->translator->trans('message.deleted_successfully');
        $this->assertSelectorTextContains('.alert.alert-success[role="alert"]', $successMessage);
    }

    /**
     * Create user.
     *
     * @param array $roles User roles
     *
     * @return User User entity
     */
    private function createUser(array $roles): User
    {
        $passwordHasher = static::getContainer()->get('security.password_hasher');
        $user = new User();
        $user->setEmail('user@example.com');
        $user->setRoles($roles);
        $user->setPassword(
            $passwordHasher->hashPassword(
                $user,
                'user12345'
            )
        );
        $userRepository = static::getContainer()->get(UserRepository::class);
        $userRepository->save($user);

        return $user;
    }
}
