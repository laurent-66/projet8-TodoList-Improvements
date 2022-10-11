<?php

namespace App\Tests\Controller;

use App\DataFixtures\AppFixtures;
use App\Repository\UserRepository;
use App\Services\IndexArrayUriService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;

class UserControllerTest extends WebTestCase
{
    private $client;
    private $urlGenerator;
    private $databaseTool;
    private $userRepository;
    private $user;

    public function setUp() : void
    {
      $this->client = static::createClient();
      $this->urlGenerator = $this->client->getContainer()->get('router.default');
      $this->client->followRedirects();
      $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
      $this->databaseTool->loadFixtures([AppFixtures::class]);
      $this->userRepository = static::getContainer()->get(UserRepository::class);
      $this->user = $this->userRepository->findOneByEmail('john.doe@example.com');
      $this->client->loginUser($this->user);
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

    public function testUniqueEntityEmailEditUser(): void
    {
        $crawler = $this->client->request('GET', '/admin/users/3/edit');
        $buttonCrawlerNode = $crawler->selectButton('Modifier');
        $form = $buttonCrawlerNode->form();
        $crawler = $this->client->submit($form, [
                'edit_user[email]'=>'john.doe@example.com',
                'edit_user[roleSelection]' => 'ROLE_ADMIN'
        ]);
        $this->assertStringContainsString("L'adresse email existe déjà.", $this->client->getResponse());
    }

    public function testMissingRequiredFieldEditUser(): void
    {
      $crawler = $this->client->request('GET', '/admin/users/3/edit');
      $buttonCrawlerNode = $crawler->selectButton('Modifier');
      $form = $buttonCrawlerNode->form();
      $crawler = $this->client->submit($form, [
              'edit_user[email]'=>'',
              'edit_user[roleSelection]' => 'ROLE_ADMIN'
      ]); 
      $this->assertStringContainsString("Vous devez saisir une adresse email.", $this->client->getResponse());
    }


    public function testEmailEditUser(): void
    {
      $crawler = $this->client->request('GET', '/admin/users/2/edit');
      $buttonCrawlerNode = $crawler->selectButton('Modifier');
      $form = $buttonCrawlerNode->form();
      $crawler = $this->client->submit($form, [
              'edit_user[email]'=>'john.doeexample.com',
              'edit_user[roleSelection]' => 'ROLE_ADMIN'
      ]);
      $this->assertStringContainsString("Le format de l&#039;adresse n&#039;est pas correcte.", $this->client->getResponse());
    }


    //Nominal case in form create user
    public function testFormEditUserNominal(): void
    {
        $crawler = $this->client->request('GET', '/admin/users/2/edit');
        $buttonCrawlerNode = $crawler->selectButton('Modifier');
        $form = $buttonCrawlerNode->form();
        $crawler = $this->client->submit($form, [
                'edit_user[email]'=>'john.doe@example.com',
                'edit_user[roleSelection]' => 'ROLE_ADMIN'
        ]);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString(' L&#039;utilisateur a bien été modifié', $this->client->getResponse()); 
    }


////////////////////////////////   validation buttons    //////////////////////////////


//////  page home /    //////////

    ///create user ////

    public function testButtonCreateUser()
    {
      $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('homepage'));
      $this->client->clickLink('Créer un utilisateur');
      $this->assertEquals('/admin/users/create', $this->client->getRequest()->getRequestUri());
      $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    ///button manage users///
    public function testButtonManageUsers()
    {
      $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('homepage'));
      $this->client->clickLink('Gestion des utilisateurs');
      $this->assertEquals('/admin/users', $this->client->getRequest()->getRequestUri());
      $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }


//////  page /admin/users //////////

    ///Edit user ////

    public function testButtonEditUser()
    {
      $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('users_list'));
      $idUserTest = 3;
      $arrayUri = $crawler->filter('.btn_edit')->extract(['href']);
      $indexUri = IndexArrayUriService::search($idUserTest, $arrayUri);

      $this->client->clickLink('edit_'.$idUserTest);
      $this->assertEquals('/admin/users/'.$idUserTest.'/edit', $arrayUri[$indexUri]);
      $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

}
