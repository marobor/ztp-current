<?php
/**
 * Category controller tests.
 */

namespace App\Tests\Controller;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CategoryControllerTest.
 */
class CategoryControllerTest extends WebTestCase
{
    /**
     * @var object
     */
    public $categoryService;
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
     * Set up.
     */
    protected function SetUp(): void
    {
        $this->httpClient = static::createClient();
        $container = static::getContainer();
        $this->entityManager = $container->get('doctrine.orm.entity_manager');
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
}
