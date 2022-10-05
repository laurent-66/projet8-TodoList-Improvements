<?php

namespace App\Tests\Controller;

use App\DataFixtures\AppFixtures;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;

class DefaultControllerTest extends WebTestCase
{
    public function setUp() : void

    {
        $this->client = static::createClient();
        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
        $this->databaseTool->loadFixtures([AppFixtures::class]);
        $this->userRepository = static::getContainer()->get(UserRepository::class);
        $this->user = $this->userRepository->findOneByEmail('john.doe@example.com');
        $this->client->loginUser($this->user);
        $this->urlGenerator = $this->client->getContainer()->get('router.default');
        $this->client->followRedirects();
    }

    public function testToAccessPage()
    {
        $this->client->request('GET', '/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    ///////////// validation buttons /////////////

    ///test of the 'TO DO LIST' header logo

    public function testLogoToDoList() 
    {
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('homepage'));
        $this->client->clickLink('logo_to_list');
        $this->assertEquals('/', $this->client->getRequest()->getRequestUri());
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}