<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    private $repository;
    private $manager;
    private $encoder;

    public function __construct(UserRepository $repository, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {
        $this->repository = $repository;
        $this->manager = $manager;
        $this->encoder = $encoder;
    }

    /**
     * @Route("/users", name="user_list")
     *
     * @return Response
     */
    public function list(): Response
    {
        return $this->render('user/list.html.twig', [
            'users' => $this->repository->findAll()
        ]);
    }

    /**
     * @Route("/users/create", name="user_create")
     *
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function createUser(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            $this->manager->persist($user);
            $this->manager->flush();
            $this->addFlash('success', "L'utilisateur a bien été ajouté.");

            return $this->redirectToRoute('user_list');
        }
        return $this->render('user/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/users/{id}/edit", name="user_edit")
     *
     * @param User $user
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function editUser(User $user, Request $request)
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            $this->manager->flush();
            $this->addFlash('success', "L'utilisateur a bien été modifié.");
            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/create.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }
}
