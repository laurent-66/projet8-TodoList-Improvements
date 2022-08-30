<?php

namespace App\Controller;

use App\Services\AnonymousAttachedTask;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(AnonymousAttachedTask $anonymousAttachedTask): Response
    {
        // Associed the tasks without user to user anonymous
        $anonymousAttachedTask->execute();

        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }
}
