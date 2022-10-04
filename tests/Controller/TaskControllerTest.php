<?php

namespace App\Tests\Controller;

use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use App\Services\IndexArrayUriService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    public function setUp() : void

    {
      $this->client = static::createClient();
      $this->userRepository = static::getContainer()->get(UserRepository::class);
      $this->taskRepository = static::getContainer()->get(TaskRepository::class);
      $this->user = $this->userRepository->findOneByEmail('john.doe@example.com');
      $this->client->loginUser($this->user);
      $this->urlGenerator = $this->client->getContainer()->get('router.default');
      $this->client->followRedirects();
    }




//////////////////////// validation  forms /////////////////////////////////


    /////////create task ////////////

    //Errors cases in form create task

    public function testUniqueEntityTitle(): void
    {
        $crawler = $this->client->request('GET', '/tasks/create');
        $buttonCrawlerNode = $crawler->selectButton('Ajouter');
        $form = $buttonCrawlerNode->form();
        $crawler = $this->client->submit($form, [
            'task[title]' => 'Task1',
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


    ////Nominal case in form create task////
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

    public function testUniqueEntityTitleEditTask(): void
    {
        $crawler = $this->client->request('GET', '/tasks/7/edit');
        $buttonCrawlerNode = $crawler->selectButton('Modifier');
        $form = $buttonCrawlerNode->form();
        $crawler = $this->client->submit($form, [
            'task[title]' => 'Task1',
            'task[content]'=>'Content'
        ]);
    
        $this->assertStringContainsString("Ce titre est déjà utilisé", $this->client->getResponse()->getContent());
        
    }

    public function testMissingRequiredFieldEditTask(): void
    {
      $crawler = $this->client->request('GET', '/tasks/7/edit');
      $buttonCrawlerNode = $crawler->selectButton('Modifier');
      $form = $buttonCrawlerNode->form();
      $crawler = $this->client->submit($form, [
                'task[title]' => '',
                'task[content]'=>''
      ]);
      $this->assertStringContainsString("Vous devez saisir un titre", $this->client->getResponse()->getContent());
    }

    ////Nominal case in form edit task////

    public function testFormEditTaskNominal(): void
    {
      $crawler = $this->client->request('GET', '/tasks/7/edit');
      $buttonCrawlerNode = $crawler->selectButton('Modifier');
      $form = $buttonCrawlerNode->form();
      $crawler = $this->client->submit($form, [
        'task[title]' => 'Task 7',
        'task[content]'=>'Content7'
      ]);
      $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
      $this->assertSelectorTextContains('div.alert.alert-success',' La tâche a bien été modifiée.'); 
    }


////////////////////////////////   validation buttons    //////////////////////////////


//////  page home /    //////////

    ///// button consult task list completed ////////

    public function testBtnConsultTaskListCompleted()
    {
      $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('homepage'));
      $this->client->clickLink('Consulter la liste des tâches terminées');
      $this->assertEquals('/tasks/completed', $this->client->getRequest()->getRequestUri());
      $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    ///// button consult task list to do ////////

    public function testBtnConsultTaskListToDo()
    {
      $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('homepage'));
      $this->client->clickLink('Consulter la liste des tâches à faire');
      $this->assertEquals('/tasks/to_do', $this->client->getRequest()->getRequestUri());
      $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    ///button manage tasks///
    public function testButtonManageTasks()
    {
      $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('homepage'));
      $this->client->clickLink('Gestion des utilisateurs');
      $this->assertEquals('/admin/users', $this->client->getRequest()->getRequestUri());
      $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }


//////  page /task/to_do   //////////

      ///button create task /////

       public function testButtonCreateTaskPageToDo()
       {
         $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('to-do_list'));
         $this->client->clickLink('Créer une tâche');
         $this->assertEquals('/tasks/create', $this->client->getRequest()->getRequestUri());
         $this->assertResponseStatusCodeSame(Response::HTTP_OK);
       }
 
      ///// button consult task list completed ////////

      public function testButtonConsultTaskListCompleted()
      {
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('to-do_list'));
        $this->client->clickLink('Consulter la liste des tâches effectuées');
        $this->assertEquals('/tasks/completed', $this->client->getRequest()->getRequestUri());
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
      }

      /////button Task completed ////////////

      public function testButtonTaskCompleted()
      {
        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('to-do_list'));

        $idTaskTest = 1;
        $titleTask = $this->taskRepository->find($idTaskTest)->getTitle();
        $arrayUri = $crawler->filter('.btn_check')->extract(['href']);
        $indexUri = IndexArrayUriService::search($idTaskTest, $arrayUri);

        $this->client->clickLink('check_'.$idTaskTest);
        $this->assertEquals('/tasks/'.$idTaskTest.'/toggle', $arrayUri[$indexUri]);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertStringContainsString('La tâche '.$titleTask.' a bien été marquée comme faite.', $this->client->getResponse()); 
      }


      ////button edit task ////

      public function testButtonEditTaskPageTaskToDo()
      {
        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('to-do_list'));
        $idTaskTest = 3;
        $arrayUri = $crawler->filter('.btn_edit')->extract(['href']);
        $indexUri = IndexArrayUriService::search($idTaskTest, $arrayUri);
        $this->client->clickLink('edit_'.$idTaskTest);
        $this->assertEquals('/tasks/'.$idTaskTest.'/edit', $arrayUri[$indexUri]);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
      }


      /////button Task deleted /////////

      public function testButtonTaskDeletedPageToDo()
      {
        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('to-do_list'));
        $idTaskTest = 8;
        $arrayUri = $crawler->filter('.btn_delete')->extract(['href']);
        $indexUri = IndexArrayUriService::search($idTaskTest, $arrayUri);

        $this->client->clickLink('trash_'.$idTaskTest);
        $this->assertEquals('/tasks/'.$idTaskTest.'/delete', $arrayUri[$indexUri]);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertStringContainsString('La tâche a bien été supprimée.', $this->client->getResponse()); 
      }

////////  page /tasks/completed  /////////

      ///button create task /////

      public function testButtonCreateTaskPageTaskCompleted()
      {
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('task_completed'));
        $this->client->clickLink('Créer une tâche');
        $this->assertEquals('/tasks/create', $this->client->getRequest()->getRequestUri());
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
      }

      ///// button consult task list to do ////////

      public function testButtonConsultTaskListToDo()
      {
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('task_completed'));
        $this->client->clickLink('Consulter la liste des tâches à faire');
        $this->assertEquals('/tasks/to_do', $this->client->getRequest()->getRequestUri());
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
      }

      ///button Task To-do ////////////

      public function testButtonTaskToDoPageTaskCompleted()
      {
        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('task_completed'));
        $idTaskTest = 1;
        $titleTask = $this->taskRepository->find($idTaskTest)->getTitle();
        $arrayUri = $crawler->filter('.btn_todo')->extract(['href']);
        $indexUri = IndexArrayUriService::search($idTaskTest, $arrayUri);

        $this->client->clickLink('todo_'.$idTaskTest);
        $this->assertEquals('/tasks/'.$idTaskTest.'/toggle', $arrayUri[$indexUri]);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertStringContainsString('La tâche '.$titleTask.' a bien été marquée comme non terminée.', $this->client->getResponse()); 
      }      

      ///button edit Task ////////////

      public function testButtonEditTaskPageTaskCompleted()
      {
        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('task_completed'));
        $idTaskTest = 2;
        $arrayUri = $crawler->filter('.btn_edit')->extract(['href']);
        $indexUri = IndexArrayUriService::search($idTaskTest, $arrayUri);

        $this->client->clickLink('edit_'.$idTaskTest);
        $this->assertEquals('/tasks/'.$idTaskTest.'/edit', $arrayUri[$indexUri]);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
      }

      ///button Task delete ////////////

      public function testButtonTaskDeletedPageTaskCompleted()
      {
        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('task_completed'));
        $idTaskTest = 2;
        $arrayUri = $crawler->filter('.btn_delete')->extract(['href']);
        $indexUri = IndexArrayUriService::search($idTaskTest, $arrayUri);

        $this->client->clickLink('trash_'.$idTaskTest);
        $this->assertEquals('/tasks/'.$idTaskTest.'/delete', $arrayUri[$indexUri]);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertStringContainsString('La tâche a bien été supprimée.', $this->client->getResponse()); 
      }


////////page admin/tasks//////

      public function testButtonTaskDeletedPageAdminTasks()
      {
            $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('tasksUsersAnonymous'));
            $idTaskTest = 4;
            $arrayUri = $crawler->filter('.btn_delete')->extract(['href']);
            $indexUri = IndexArrayUriService::search($idTaskTest, $arrayUri);
    
            $this->client->clickLink('trash_'.$idTaskTest);
            $this->assertEquals('/admin/tasks/'.$idTaskTest.'/delete', $arrayUri[$indexUri]);
            $this->assertResponseStatusCodeSame(Response::HTTP_OK);
            $this->assertStringContainsString('La tâche a bien été supprimée.', $this->client->getResponse()); 
      }

}
