<?php
/**
 * Article controller tests.
 */

namespace App\Tests\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Service\ArticleService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ArticleControllerTest.
 */
class ArticleControllerTest extends WebTestCase
{
    /**
     * @var object
     */
    public $categoryService;
    /**
     * Test client.
     */
    private KernelBrowser $httpClient;

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
        $this->categoryService = $container->get(ArticleService::class);
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
        $this->entityManager->flush();
        $this->entityManager->persist($article);
        $this->entityManager->flush();

        // when
        $this->httpClient->request(Request::METHOD_GET, '/'.$article->getId());
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
}
