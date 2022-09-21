<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function setUp() : void

    {
      $this->client = static::createClient();
      $this->userRepository = static::getContainer()->get(UserRepository::class);
      $this->user = $this->userRepository->findOneByEmail('john.doe@example.com');
      $this->client->loginUser($this->user);
      $this->urlGenerator = $this->client->getContainer()->get('router.default');
      $this->client->followRedirects();
    }

    //Prévoir les cas d'erreurs une méthode par cas d'usage dans le formulaire

    public function testUniqueEntityEmail(): void
    {
        $crawler = $this->client->request('GET', '/admin/users/create');
        $buttonCrawlerNode = $crawler->selectButton('Ajouter');
        $form = $buttonCrawlerNode->form();
        $crawler = $this->client->submit($form, [
                'user[username]' => 'John Doe',
                'user[email]'=>'john.doe@example.com',
                'user[password][first]' => '',
                'user[password][second]' => '',
                'user[roleSelection]' => 'ROLE_ADMIN'
        ]);

        $this->assertStringContainsString("Cet email est déjà utilisé", $this->client->getResponse()->getContent());
    }


    public function testWithMissingRequiredField(): void
    {
      $crawler = $this->client->request('GET', '/admin/users/create');
      $buttonCrawlerNode = $crawler->selectButton('Ajouter');
      $form = $buttonCrawlerNode->form();
      $this->client->submit($form, [
                'user[username]' => '',
                'user[email]'=>'',
                'user[password][first]' => '',
                'user[password][second]' => '',
                'user[roleSelection]' => 'ROLE_ADMIN'
      ]);
      $this->assertStringContainsString("Vous devez saisir un nom d&#039;utilisateur.", $this->client->getResponse());
      $this->assertStringContainsString("Vous devez saisir une adresse email.", $this->client->getResponse());
      $this->assertStringContainsString("Vous devez saisir un mot de passe.", $this->client->getResponse());
    }

    public function testEmail(): void
    {
      $crawler = $this->client->request('GET', '/admin/users/create');
      $buttonCrawlerNode = $crawler->selectButton('Ajouter');
      $form = $buttonCrawlerNode->form();
      $this->client->submit($form, [
                'user[username]' => 'Laurent',
                'user[email]'=>'laurent.lesage51gmail.com',
                'user[password][first]' => '',
                'user[password][second]' => '',
                'user[roleSelection]' => 'ROLE_ADMIN'
      ]);
      $this->assertStringContainsString("Le format de l&#039;adresse n&#039;est pas correcte.", $this->client->getResponse());
    }

    //validate the same passwords
    // public function testComparaisonPasswords(): void
    // {

      
    // }


    public function testFormCreateUserNominal(): void
    {
        //navigation on URI
        $crawler = $this->client->request('GET', '/admin/users/create');

        //verify presence button with text 'Ajouter' in page
        $this->assertSelectorTextContains('button', 'Ajouter');

        //select the button
        $buttonCrawlerNode = $crawler->selectButton('Ajouter');

        //retrieve the Form object for the form belonging to this button
        $form = $buttonCrawlerNode->form();

        //submit the Form object
        $this->client->submit($form, [
                'user[username]' => 'Laurent',
                'user[email]'=>'laurent.lesage51@gmail.com',
                'user[password][first]' => 'hello',
                'user[password][second]' => 'hello',
                'user[roleSelection]' => 'ROLE_ADMIN'
        ]);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('div.alert.alert-success','L\'utilisateur a bien été ajouté.');

    }

}
