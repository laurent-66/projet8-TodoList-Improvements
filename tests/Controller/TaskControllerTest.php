<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
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

    /////////create task ////////////

    //Errors cases in form create task

    public function testUniqueEntityTitle(): void
    {
        $crawler = $this->client->request('GET', '/tasks/create');
        $buttonCrawlerNode = $crawler->selectButton('Ajouter');
        $form = $buttonCrawlerNode->form();
        $crawler = $this->client->submit($form, [
            'task[title]' => 'Task',
            'task[content]'=>'Content'
        ]);
    
        $this->assertStringContainsString("Ce titre est déjà utilisé", $this->client->getResponse()->getContent());
    }

    public function testWithMissingRequiredField(): void
    {
      $crawler = $this->client->request('GET', '/tasks/create');
      $buttonCrawlerNode = $crawler->selectButton('Ajouter');
      $form = $buttonCrawlerNode->form();
      $crawler = $this->client->submit($form, [
                'task[title]' => '',
                'task[content]'=>''
      ]);
      $this->assertStringContainsString("Vous devez saisir un titre", $this->client->getResponse()->getContent());
    }


    //Nominal case in form create task
    public function testFormCreateTaskNominal(): void
    {
      $crawler = $this->client->request('GET', '/tasks/create');
      $buttonCrawlerNode = $crawler->selectButton('Ajouter');
      $form = $buttonCrawlerNode->form();
      $crawler = $this->client->submit($form, [
        'task[title]' => 'Task 2',
        'task[content]'=>'Content'
      ]);
      $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
      $this->assertSelectorTextContains('div.alert.alert-success',' La tâche a bien été ajoutée.'); 
    }

    /////////Edit task ////////////

    //Errors cases in form edit task

    // public function testUniqueEntityTitleEditTask(): void
    // {
    //     $crawler = $this->client->request('GET', '/tasks/{id}/edit');
    //     $buttonCrawlerNode = $crawler->selectButton('Modifier');
    //     $form = $buttonCrawlerNode->form();
    //     $crawler = $this->client->submit($form, [
    //         'task[title]' => 'Task',
    //         'task[content]'=>'Content'
    //     ]);
    
    //     $this->assertStringContainsString("Ce titre est déjà utilisé", $this->client->getResponse()->getContent());
        
    // }

    // public function testMissingRequiredFieldEditTask(): void
    // {
    //   $crawler = $this->client->request('GET', '/tasks/{id}/edit');
    //   $buttonCrawlerNode = $crawler->selectButton('Modifier');
    //   $form = $buttonCrawlerNode->form();
    //   $crawler = $this->client->submit($form, [
    //             'task[title]' => '',
    //             'task[content]'=>''
    //   ]);
    //   $this->assertStringContainsString("Vous devez saisir un titre", $this->client->getResponse()->getContent());
    // }


///validation button/////

 ///button create task /////
      //page to do 
      public function testButtonCreateTaskPageToDo()
      {
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('to-do_list'));
        $this->client->clickLink('Créer une tâche');
        $this->assertEquals('/tasks/create', $this->client->getRequest()->getRequestUri());
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
      }

      //page task completed

      public function testButtonCreateTaskPageTaskCompleted()
      {
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('task_completed'));
        $this->client->clickLink('Créer une tâche');
        $this->assertEquals('/tasks/create', $this->client->getRequest()->getRequestUri());
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
      }


/////////button Task To-do ////////////
      //page task completed

      public function testButtonTaskToDo()
      {
            $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('task_completed'));
            $this->client->clickLink('xmark-solid');
            $uri = $this->client->getRequest()->getRequestUri();
            $id = substr(substr($uri, 7),0,-7);
            $this->assertEquals('/tasks/'.$id.'/toggle', $this->client->getRequest()->getRequestUri());
            $this->assertResponseStatusCodeSame(Response::HTTP_OK);
      }

/////////button Task completed ////////////
      //page to do 

      public function testButtonTaskCompleted()
      {
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('to-do_list'));
        $this->client->clickLink('check-solid');
        $uri = $this->client->getRequest()->getRequestUri();
        $id = substr(substr($uri, 7),0,-7);
        $this->assertEquals('/tasks/'.$id.'/toggle', $this->client->getRequest()->getRequestUri());
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
      }


/////button Task deleted /////////
      //page to do 
      public function testButtonTaskDeletedPageToDo()
      {
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('to-do_list'));
        $this->client->clickLink('check-solid');
        $uri = $this->client->getRequest()->getRequestUri();
        $id = substr(substr($uri, 7),0,-7);
        $this->assertEquals('/tasks/'.$id.'/toggle', $this->client->getRequest()->getRequestUri());
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
      }

      //page task completed

      public function testButtonTaskDeletedPageTaskCompleted()
      {
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('to-do_list'));
        $this->client->clickLink('check-solid');
        $uri = $this->client->getRequest()->getRequestUri();
        $id = substr(substr($uri, 7),0,-7);
        $this->assertEquals('/tasks/'.$id.'/toggle', $this->client->getRequest()->getRequestUri());
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
      }

      //page admin/tasks

      public function testButtonTaskDeletedPageAdminTasks()
      {
            $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('tasksUsersAnonymous'));
            $this->client->clickLink('supprimer');
            $uri = $this->client->getRequest()->getRequestUri();
            dd($uri);
            $id = substr(substr($uri, 12),0,-7);
            $this->assertEquals('admin/tasks/'.$id.'/delete', $this->client->getRequest()->getRequestUri());
            $this->assertResponseStatusCodeSame(Response::HTTP_OK);
      }


////button link to do list page ////
      //page home
      //page task completed

////button link task completed list page ////
      //page home
      //page to do 

///button manage tasks///
      //page home
}
