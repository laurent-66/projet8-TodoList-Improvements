<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testToAccessPage()
    {
        // This calls KernelTestCase::bootKernel(), and creates a
        // "client" that is acting as the browser
        $client = static::createClient();

        // Request a specific page
        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());


        // Validate a successful response and some content
        $this->assertResponseIsSuccessful();

        // $this->assertSelectorTextContains('a','Créer un utilisateur');
        // $this->assertSelectorTextContains('a','Créer une nouvelle tâche');
        // $this->assertSelectorTextContains('a','Consulter la liste des tâches à faire');
        // $this->assertSelectorTextContains('a','Consulter la liste des tâches terminées');

        // $client->clickLink('Créer un utilisateur');
        // $this->assertStringContainsString('/users/create', $client->getRequest()->getRequestUri());

        // $client->clickLink('Créer une nouvelle tâche');
        // $this->assertStringContainsString('/tasks/create', $client->getRequest()->getRequestUri());

        $client->clickLink('Consulter la liste des tâches à faire');
        $this->assertStringContainsString('/tasks/to_do', $client->getRequest()->getRequestUri());

        // $client->clickLink('Consulter la liste des tâches terminées');
        // $this->assertStringContainsString('/tasks/completed', $client->getRequest()->getRequestUri());

    }

}