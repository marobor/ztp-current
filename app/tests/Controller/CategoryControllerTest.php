<?php
/**
 * Category controller tests.
 */

namespace App\Tests\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Enum\UserRole;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\CategoryService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class CategoryControllerTest.
 */
class CategoryControllerTest extends WebTestCase
{
    /**
     * Category service.
     */
    public CategoryService $categoryService;

    /**
     * Test route.
     */
    public const TEST_ROUTE = '/category';

    /**
     * @var KernelBrowser Http Client
     */
    private KernelBrowser $httpClient;

    /**
     * Entity Manager;.
     */
    private ?EntityManagerInterface $entityManager;

    /**
     * Translator.
     */
    private TranslatorInterface $translator;

    /**
     * Set up.
     */
    protected function SetUp(): void
    {
        $this->httpClient = static::createClient();
        $container = static::getContainer();
        $this->entityManager = $container->get('doctrine.orm.entity_manager');
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
     * Test show route.
     */
    public function testShowRoute(): void
    {
        // given
        $expectedStatusCode = 200;
        $category = new Category();
        $category->setName('Category test');
        $this->entityManager->persist($category);
        $this->entityManager->flush();

        // when
        $this->httpClient->request(Request::METHOD_GET, self::TEST_ROUTE.'/'.$category->getId());
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

    /**
     * Test Create route.
     */
    public function testCreateRoute(): void
    {
        // given
        $expectedStatusCode = 302;
        $this->entityManager->flush();

        // when
        $this->httpClient->request(Request::METHOD_GET, self::TEST_ROUTE.'/create');
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
        $adminUser = $this->createUser([UserRole::ROLE_ADMIN->value]);
        $this->httpClient->loginUser($adminUser);

        $crawler = $this->httpClient->request(Request::METHOD_GET, self::TEST_ROUTE.'/create');

        $createButton = $this->translator->trans('action.create');
        $form = $crawler->selectButton($createButton)->form();
        $form['category[name]'] = 'Test Category';

        // when
        $this->httpClient->submit($form);
        $response = $this->httpClient->getResponse();

        // then
        $this->assertTrue($response->isRedirect());
        $this->assertEquals(self::TEST_ROUTE, $response->headers->get('Location'));

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
        $expectedStatusCode = 302;
        $category = new Category();
        $category->setName('Category test');
        $this->entityManager->persist($category);
        $this->entityManager->flush();

        // when
        $this->httpClient->request(Request::METHOD_GET, self::TEST_ROUTE.'/'.$category->getId().'/edit');
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
        $category = new Category();
        $category->setName('Category test');

        $this->entityManager->persist($category);
        $this->entityManager->flush();
        $categoryId = $category->getId();

        $adminUser = $this->createUser([UserRole::ROLE_ADMIN->value]);
        $this->httpClient->loginUser($adminUser);

        $crawler = $this->httpClient->request(Request::METHOD_GET, self::TEST_ROUTE.'/'.$categoryId.'/edit');

        $editButton = $this->translator->trans('action.edit');
        $form = $crawler->selectButton($editButton)->form();

        // when
        $this->httpClient->submit($form);
        $response = $this->httpClient->getResponse();

        // then
        $this->assertTrue($response->isRedirect());
        $this->assertEquals(self::TEST_ROUTE, $response->headers->get('Location'));

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
        $expectedStatusCode = 302;
        $category = new Category();
        $category->setName('Category test');
        $this->entityManager->persist($category);
        $this->entityManager->flush();

        // when
        $this->httpClient->request(Request::METHOD_GET, self::TEST_ROUTE.'/'.$category->getId().'/delete');
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
        $category = new Category();
        $category->setName('Category test');

        $this->entityManager->persist($category);
        $this->entityManager->flush();
        $categoryId = $category->getId();

        $adminUser = $this->createUser([UserRole::ROLE_ADMIN->value]);
        $this->httpClient->loginUser($adminUser);

        $crawler = $this->httpClient->request(Request::METHOD_GET, self::TEST_ROUTE.'/'.$categoryId.'/delete');

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
     * Test failed delete action when category contains articles.
     */
    public function testDeleteActionWhenCategoryContainsArticles(): void
    {
        // given
        $date = new \DateTimeImmutable();
        $category = new Category();
        $category->setName('Category test');

        $article = new Article();
        $article->setTitle('Article test');
        $article->setContent('Article test content');
        $article->setCreatedAt($date);
        $article->setCategory($category);

        $this->entityManager->persist($category);
        $this->entityManager->persist($article);
        $this->entityManager->flush();
        $categoryId = $category->getId();

        $adminUser = $this->createUser([UserRole::ROLE_ADMIN->value]);
        $this->httpClient->loginUser($adminUser);

        // when
        $this->httpClient->request(Request::METHOD_GET, self::TEST_ROUTE.'/'.$categoryId.'/delete');
        $resultHttpStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals(302, $resultHttpStatusCode);
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
