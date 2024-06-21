<?php
/**
 * Article controller tests.
 */

namespace App\Tests\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Enum\UserRole;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\ArticleService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ArticleControllerTest.
 */
class ArticleControllerTest extends WebTestCase
{
    /**
     * Article service.
     */
    public ArticleService $articleService;

    /**
     * Test client.
     */
    private KernelBrowser $httpClient;

    /**
     * Post repository.
     */
    private ?EntityManagerInterface $entityManager;

    /**
     * Translator.
     */
    private TranslatorInterface $translator;

    /**
     * Set up.
     */
    public function setUp(): void
    {
        $this->httpClient = static::createClient();
        $container = static::getContainer();
        $this->entityManager = $container->get('doctrine.orm.entity_manager');
        $this->articleService = $container->get(ArticleService::class);
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
        $this->httpClient->request(Request::METHOD_GET, '/');
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

    /**
     * Test index route for Admin.
     */
    public function testIndexRouteForAdmin(): void
    {
        // given
        $expectedStatusCode = 200;
        $user = $this->createUser(['ROLE_ADMIN']);
        $this->httpClient->loginUser($user);

        // when
        $this->httpClient->request(Request::METHOD_GET, '/');
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

    /**
     * Test index route with filters.
     */
    public function testIndexRouteWithFilters(): void
    {
        // given
        $expectedStatusCode = 200;
        $date = new \DateTimeImmutable();

        $category = new Category();
        $category->setName('Category Test');

        $article = new Article();
        $article->setTitle('Article test');
        $article->setContent('Article content');
        $article->setCreatedAt($date);
        $article->setCategory($category);
        $this->entityManager->persist($category);
        $this->entityManager->persist($article);
        $this->entityManager->flush();

        // when
        $this->httpClient->request(Request::METHOD_GET, '/?filters.category_id=1');
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

    /**
     * Test show route.
     */
    public function testShowRoute(): void
    {
        // given
        $date = new \DateTimeImmutable();
        $expectedStatusCode = 200;

        $category = new Category();
        $category->setName('Category Test');

        $article = new Article();
        $article->setTitle('Article test');
        $article->setContent('Article content');
        $article->setCreatedAt($date);
        $article->setCategory($category);

        $this->entityManager->persist($category);
        $this->entityManager->persist($article);
        $this->entityManager->flush();

        // when
        $this->httpClient->request(Request::METHOD_GET, '/'.$article->getId());
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

    /**
     * Test successful create comment action in show route.
     */
    public function testCreateCommentActionSuccess(): void
    {
        // given
        $date = new \DateTimeImmutable();

        $category = new Category();
        $category->setName('Category Test');

        $article = new Article();
        $article->setTitle('Article test');
        $article->setContent('Article content');
        $article->setCreatedAt($date);
        $article->setCategory($category);

        $this->entityManager->persist($category);
        $this->entityManager->persist($article);
        $this->entityManager->flush();

        $crawler = $this->httpClient->request(Request::METHOD_GET, '/'.$article->getId());

        $createButton = $this->translator->trans('action.save');
        $form = $crawler->selectButton($createButton)->form();
        $form['comment[nick]'] = 'Test nick';
        $form['comment[email]'] = 'example@test.com';
        $form['comment[comment_content]'] = 'Comment content test';

        // when
        $this->httpClient->submit($form);
        $response = $this->httpClient->getResponse();

        // then
        $this->assertTrue($response->isRedirect());
        $this->assertEquals('/'.$article->getId(), $response->headers->get('Location'));

        $this->httpClient->followRedirect();

        $successMessage = $this->translator->trans('message.comment_added');
        $this->assertSelectorTextContains('.alert.alert-success[role="alert"]', $successMessage);
    }

    /**
     * Test Create route.
     */
    public function testCreateRoute(): void
    {
        // given
        $date = new \DateTimeImmutable();
        $expectedStatusCode = 302;

        $category = new Category();
        $category->setName('Category Test');

        $article = new Article();
        $article->setTitle('Article test');
        $article->setContent('Article content');
        $article->setCreatedAt($date);
        $article->setCategory($category);

        $this->entityManager->persist($category);
        $this->entityManager->flush();
        $this->entityManager->persist($article);
        $this->entityManager->flush();

        // when
        $this->httpClient->request(Request::METHOD_GET, '/create');
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

    /**
     * Test create route for admin.
     */
    public function testCreateRouteForAdmin(): void
    {
        // given
        $expectedStatusCode = 200;
        $user = $this->createUser(['ROLE_ADMIN']);
        $this->httpClient->loginUser($user);
        $date = new \DateTimeImmutable();

        $category = new Category();
        $category->setName('Category Test');

        $article = new Article();
        $article->setTitle('Article test');
        $article->setContent('Article content');
        $article->setCreatedAt($date);
        $article->setCategory($category);

        $this->entityManager->persist($category);
        $this->entityManager->persist($article);
        $this->entityManager->flush();

        // when
        $this->httpClient->request(Request::METHOD_GET, '/create');
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

    /**
     * Test successful create action.
     */
    public function testCreateActionSuccess(): void
    {
        // given
        $date = new \DateTimeImmutable();

        $category = new Category();
        $category->setName('Category Test');

        $this->entityManager->persist($category);
        $this->entityManager->flush();

        $adminUser = $this->createUser([UserRole::ROLE_ADMIN->value]);
        $this->httpClient->loginUser($adminUser);

        $crawler = $this->httpClient->request(Request::METHOD_GET, '/create');

        $editButton = $this->translator->trans('action.create');
        $form = $crawler->selectButton($editButton)->form();
        $form['article[title]'] = 'Test Article';
        $form['article[content]'] = 'Test Article content';
        $form['article[category]'] = $category->getId();

        // when
        $this->httpClient->submit($form);
        $response = $this->httpClient->getResponse();

        // then
        $this->assertTrue($response->isRedirect());
        $this->assertEquals('/', $response->headers->get('Location'));

        $this->httpClient->followRedirect();

        $successMessage = $this->translator->trans('message.created_successfully');
        $this->assertSelectorTextContains('.alert.alert-success[role="alert"]', $successMessage);
    }

    /**
     * Test edit route.
     */
    public function testEditRoute(): void
    {
        // given
        $date = new \DateTimeImmutable();
        $expectedStatusCode = 302;

        $category = new Category();
        $category->setName('Category Test');

        $article = new Article();
        $article->setTitle('Article test');
        $article->setContent('Article content');
        $article->setCreatedAt($date);
        $article->setCategory($category);

        $this->entityManager->persist($category);
        $this->entityManager->flush();
        $this->entityManager->persist($article);
        $this->entityManager->flush();

        // when
        $this->httpClient->request(Request::METHOD_GET, '/'.$article->getId().'/edit');
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

    /**
     * Test edit route for admin user.
     */
    public function testEditRouteForAdmin(): void
    {
        // given
        $date = new \DateTimeImmutable();
        $user = $this->createUser(['ROLE_ADMIN']);
        $this->httpClient->loginUser($user);
        $expectedStatusCode = 200;

        $category = new Category();
        $category->setName('Category Test');

        $article = new Article();
        $article->setTitle('Article test');
        $article->setContent('Article content');
        $article->setCreatedAt($date);
        $article->setCategory($category);

        $this->entityManager->persist($category);
        $this->entityManager->flush();
        $this->entityManager->persist($article);
        $this->entityManager->flush();

        // when
        $this->httpClient->request(Request::METHOD_GET, '/'.$article->getId().'/edit');
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

    /**
     * Test successful edit action.
     */
    public function testEditActionSuccess(): void
    {
        // given
        $date = new \DateTimeImmutable();

        $category = new Category();
        $category->setName('Category Test');

        $article = new Article();
        $article->setTitle('Article test');
        $article->setContent('Article content');
        $article->setCreatedAt($date);
        $article->setCategory($category);

        $this->entityManager->persist($category);
        $this->entityManager->persist($article);
        $this->entityManager->flush();
        $articleId = $article->getId();

        $adminUser = $this->createUser([UserRole::ROLE_ADMIN->value]);
        $this->httpClient->loginUser($adminUser);

        $crawler = $this->httpClient->request(Request::METHOD_GET, '/'.$articleId.'/edit');

        $editButton = $this->translator->trans('action.edit');
        $form = $crawler->selectButton($editButton)->form();

        // when
        $this->httpClient->submit($form);
        $response = $this->httpClient->getResponse();

        // then
        $this->assertTrue($response->isRedirect());
        $this->assertEquals('/', $response->headers->get('Location'));

        $this->httpClient->followRedirect();

        $successMessage = $this->translator->trans('message.edited_successfully');
        $this->assertSelectorTextContains('.alert.alert-success[role="alert"]', $successMessage);
    }

    /**
     * Test Delete route.
     */
    public function testDeleteRoute(): void
    {
        // given
        $date = new \DateTimeImmutable();
        $expectedStatusCode = 302;

        $category = new Category();
        $category->setName('Category Test');

        $article = new Article();
        $article->setTitle('Article test');
        $article->setContent('Article content');
        $article->setCreatedAt($date);
        $article->setCategory($category);

        $this->entityManager->persist($category);
        $this->entityManager->flush();
        $this->entityManager->persist($article);
        $this->entityManager->flush();

        // when
        $this->httpClient->request(Request::METHOD_GET, '/'.$article->getId().'/delete');
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

    /**
     * Test delete route for admin user.
     */
    public function testDeleteRouteForAdmin(): void
    {
        // given
        $date = new \DateTimeImmutable();
        $user = $this->createUser(['ROLE_ADMIN']);
        $this->httpClient->loginUser($user);
        $expectedStatusCode = 200;

        $category = new Category();
        $category->setName('Category Test');

        $article = new Article();
        $article->setTitle('Article test');
        $article->setContent('Article content');
        $article->setCreatedAt($date);
        $article->setCategory($category);

        $this->entityManager->persist($category);
        $this->entityManager->flush();
        $this->entityManager->persist($article);
        $this->entityManager->flush();

        // when
        $this->httpClient->request(Request::METHOD_GET, '/'.$article->getId().'/delete');
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
        $date = new \DateTimeImmutable();

        $category = new Category();
        $category->setName('Category Test');

        $article = new Article();
        $article->setTitle('Article test');
        $article->setContent('Article content');
        $article->setCreatedAt($date);
        $article->setCategory($category);

        $this->entityManager->persist($category);
        $this->entityManager->persist($article);
        $this->entityManager->flush();
        $articleId = $article->getId();

        $adminUser = $this->createUser([UserRole::ROLE_ADMIN->value]);
        $this->httpClient->loginUser($adminUser);

        $crawler = $this->httpClient->request(Request::METHOD_GET, '/'.$articleId.'/delete');

        $deleteButton = $this->translator->trans('action.delete');
        $form = $crawler->selectButton($deleteButton)->form();

        // when
        $this->httpClient->submit($form);
        $response = $this->httpClient->getResponse();

        // then
        $this->assertTrue($response->isRedirect());
        $this->assertEquals('/', $response->headers->get('Location'));

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
