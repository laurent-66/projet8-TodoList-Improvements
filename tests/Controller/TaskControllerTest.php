<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
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
      //page task completed

/////////button Task To-do ////////////
      //page task completed

/////////button Task completed ////////////
      //page to do 

/////button Task deleted /////////
      //page to do 
      //page task completed
      //page admin/tasks

////button link to do list page ////
      //page home
      //page task completed

////button link task completed list page ////
      //page home
      //page to do 

///button manage tasks///
      //page home
}
