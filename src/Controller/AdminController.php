<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Form\EditUserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;



class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @IsGranted("ROLE_MODERATOR")
     * @Route("/users", name="users")
     */
    public function usersList(UserRepository $user)
    {
        return $this->render('admin/users.html.twig', [
            'user' => $user->findAll(),
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("edituser/{id}", name="edituser")
     */
    public function editUser(User $user, Request $request)
    {
        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('message', 'Utilisateur modifié avec succès');
            return $this->redirectToRoute('users');
        }

        return $this->render('admin/edituser.html.twig', [
            'userForm' => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("deleteuser/{id}", name="deleteuser")
     */
    public function delete($id, UserRepository $repository)
    {
        $user = $repository->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();
        return $this->redirectToRoute('users');
    }
}