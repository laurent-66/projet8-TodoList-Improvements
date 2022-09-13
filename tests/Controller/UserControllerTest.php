<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testGetCreateUserPage(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/users/create');

        $this->assertResponseIsSuccessful();
    }

    public function testPostCreateUserPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/users/create');

        $this->assertSelectorTextContains('button', 'Ajouter');
        $this->assertStringContainsString('/users/create', $client->getRequest()->getRequestUri());

        $this->assertEquals(200, $client->getResponse()->getStatusCode());


        // Validate a successful response and some content
        // $this->assertResponseIsSuccessful();

        $crawler = $client->submitForm(
            'Ajouter',
            [
                'user[username]' => 'Laurent',
                'user[password][first]' => 'hello',
                'user[password][second]' => 'hello',
                'user[roleSelection]' => 'ROLE_ADMIN'
            ],
                    
        );
        dd($client->getResponse()->getStatusCode());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $client->followRedirect();  
        $this->assertStringContainsString('/', $client->getRequest()->getRequestUri());  
    }


    //admin access

    // public function testGetListUsersPage(): void
    // {
    //     $client = static::createClient();
    //     $userRepository = static::getContainer()->get(UserRepository::class);

    //     // retrieve the test user
    //     $testUser = $userRepository->findOneByEmail('john.doe@example.com');

    //     // simulate $testUser being logged in
    //     $client->loginUser($testUser);

    //     // test e.g. the profile page
    //     $client->request('GET', '/admin/users');
    //     $this->assertResponseIsSuccessful();
    //     $this->assertSelectorTextContains('h2', 'Liste des utilisateurs');
    // }

}
