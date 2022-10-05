<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function setUp() : void
    {
        $this->client = static::createClient();
        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
        $this->databaseTool->loadFixtures([AppFixtures::class]);
        $this->userRepository = static::getContainer()->get(UserRepository::class);
        $this->user = $this->userRepository->findOneByEmail('john.doe@example.com');
        $this->urlGenerator = $this->client->getContainer()->get('router.default');
        $this->client->followRedirects();
    }


    public function testGetLoginPage()
    {
        // Request a specific page
        $this->client->request('GET', '/login');

        // Validate a successful response and some content
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }


    public function testVisitingWhileLoggedIn()
    {
        // retrieve the test user
        $testUser = $this->userRepository->findOneByEmail('john.doe@example.com');

        // simulate $testUser being logged in
        $this->client->loginUser($testUser);

        //test the home page
        $this->client->request('GET', '/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    ///////////// validation buttons /////////////

    ///validation button 'se connecter'

    public function testbuttonLogin() 
    {
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('homepage'));
        $this->client->clickLink('Se connecter');
        $this->assertEquals('/login', $this->client->getRequest()->getRequestUri());
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    } 

    ///validation button 'se déconnecter'

    public function testbuttonLogout() 
    {
        $this->client->loginUser($this->user);
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('homepage'));
        $this->client->clickLink('Se déconnecter');
        $this->assertEquals('/', $this->client->getRequest()->getRequestUri());
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    } 

}