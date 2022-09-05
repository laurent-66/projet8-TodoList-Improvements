<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
public function __construct(
    UserRepository $userRepository,
    EntityManagerInterface $entityManager, 
    UserPasswordHasherInterface $passwordHasher,
    Security $security
    )
{
    $this->userRepository = $userRepository;
    $this->entityManager = $entityManager;
    $this->passwordHasher = $passwordHasher;
    $this->security = $security;
}

    #[Route('/admin/users', name: 'users_list')]
    public function index(): Response
    {
        return $this->render('user/list.html.twig', [
            'users' => $this->userRepository->findAll()]);
    }

    #[Route("/users/create", name:"user_create")]
    public function createAction(Request $request)
    {
        $form = $this->createForm(UserType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();
            $plaintextPassword = $user->getPassword();
            $role = $user->getRoleSelection();
            $hashedPassword = $this->passwordHasher->hashPassword($user,$plaintextPassword);
            $user->setPassword($hashedPassword);
            $user->setRoles([$role]);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->addFlash('success', "L'utilisateur a bien été ajouté.");

            return $this->redirectToRoute('homepage');
        }

        return $this->render('user/create.html.twig', ['form' => $form->createView()]);
    }


    #[Route("/users/{id}/edit", name:"user_edit")]
    public function editAction($id, Request $request)
    {

        $user = $this->userRepository->find($id);
        $currentRole = $user->getRoles();
        $user->setRoleSelection($currentRole[0]);

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $plaintextPassword = $user->getPassword();
            $role = $user->getRoleSelection();
            $hashedPassword = $this->passwordHasher->hashPassword($user,$plaintextPassword);
            $user->setPassword($hashedPassword);
            $user->setRoles([$role]);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->addFlash('success', "L'utilisateur a bien été modifié");

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/edit.html.twig', ['form' => $form->createView(), 'user' => $user]);
    }

}
