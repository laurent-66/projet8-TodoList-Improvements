<?php

namespace App\Tests\Controller;

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


        $crawler = $client->submitForm(
            'Nom d\'utilisateur', ['user[username]' => 'Laurent'],
            'Adresse email', ['user[email]' => 'laurent.lesage51@gmail.com'],        
            'Mot de passe', ['user[password][first]' => 'hello'], 
            'Tapez le mot de passe à nouveau', ['user[password][second]' => 'hello'], 
            'Role selection', ['user[roleSelection]' => ['ROLE_ADMIN']],              
                // 'user[_token]' => ''                       
        );

        $client->followRedirect();  
        $this->assertStringContainsString('/', $client->getRequest()->getRequestUri());  
    }

}
