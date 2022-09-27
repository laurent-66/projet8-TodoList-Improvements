<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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


    //////////////////create user///////////////////////

    //Errors cases in form create user

    public function testUniqueEntityEmailCreateUser(): void
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

    public function testComparaisonPasswords(): void
    {
      $crawler = $this->client->request('GET', '/admin/users/create');
      $buttonCrawlerNode = $crawler->selectButton('Ajouter');
      $form = $buttonCrawlerNode->form();
      $this->client->submit($form, [
                'user[username]' => 'Laurent',
                'user[email]'=>'laurent.lesage51gmail.com',
                'user[password][first]' => 'hello',
                'user[password][second]' => 'hello1',
                'user[roleSelection]' => 'ROLE_ADMIN'
      ]);
      $this->assertStringContainsString("Les deux mots de passe doivent correspondre.", $this->client->getResponse());
      
    }

    //Nominal case in form create user
    public function testFormCreateUserNominal(): void
    {
        $crawler = $this->client->request('GET', '/admin/users/create');

        $response = $this->client->getRequest()->getRequestUri();
        dump($response);
        exit;


        $buttonCrawlerNode = $crawler->selectButton('Ajouter');
        $form = $buttonCrawlerNode->form();
        $crawler = $this->client->submit($form, [
                'user[username]' => 'Laurent',
                'user[email]'=>'laurent.lesage51@gmail.com',
                'user[password][first]' => 'hello',
                'user[password][second]' => 'hello',
                'user[roleSelection]' => 'ROLE_ADMIN'
        ]);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->assertSelectorTextContains('div.alert.alert-success','L\'utilisateur a bien été ajouté.');

    }

    //////////////// Edit user /////////////////////

    //Errors cases in form edit user

    // public function testUniqueEntityEmailEditUser(): void
    // {
    //     $crawler = $this->client->request('GET', '/admin/users/{id}/edit');
        // $response = $this->client->getRequest()->getRequestUri();
        // $buttonCrawlerNode = $crawler->selectButton('Modifier');
        // $form = $buttonCrawlerNode->form();

        //code v1
        // $crawler = $this->client->submit($form, [
        //         'edit_user[email]'=>'john.doe@example.com',
        //         'edit_user[roleSelection]' => 'ROLE_ADMIN'
        // ]);

        //code v2
        // $form['edit_user[email]'] = 'john.doe@example.com';
        // $form['edit_user[roleSelection]']->select('ROLE_USER');
        // $this->client->submit($form);

        // $this->assertStringContainsString("Cet email est déjà utilisé", $this->client->getResponse()->getContent());
    // }

    // public function testMissingRequiredFieldEditUser(): void
    // {
      // $crawler = $this->client->request('GET', '/admin/users/{id}/edit');
      //$uri = $crawler->getUri();
      // $buttonCrawlerNode = $crawler->selectButton('Modifier');
      // $form = $buttonCrawlerNode->form();
      // $this->client->submit($form, [
      //           'edit_user[email]'=>'',
      //           'edit_user[roleSelection]' => 'ROLE_USER'
      // ]);
      // $this->assertStringContainsString("Vous devez saisir une adresse email.", $this->client->getResponse()->getContent());
    // }




///validation button/////

///create user ////
    //page home
    public function testButtonCreateUser()
    {
      $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('homepage'));
      $this->client->clickLink('Créer un utilisateur');
      $this->assertEquals('/admin/users/create', $this->client->getRequest()->getRequestUri());
      $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

///mange users///
    //page home
    public function testButtonManageUser()
    {
      $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('homepage'));
      $this->client->clickLink('Gestion des utilisateurs');
      $this->assertEquals('/admin/users', $this->client->getRequest()->getRequestUri());
      $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

///Edit user///
    //page admin/users

    public function testButtonEditUser()
    {
      $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('users_list'));
      $this->client->clickLink('Edit');
      $uri = $this->client->getRequest()->getRequestUri();
      $id = substr(substr($uri, 13),0,-5);
      $this->assertEquals('/admin/users/'.$id.'/edit', $this->client->getRequest()->getRequestUri());
      $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

}
