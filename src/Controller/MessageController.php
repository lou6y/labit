<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Message;
use App\Entity\User;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


/**
 * @Route("/message")
 * @IsGranted("ROLE_USER")
 */
class MessageController extends AbstractController
{
    /**
     * @Route("/", name="message_index", methods={"GET"})
     */
    public function index(Request $request,AuthenticationUtils $util): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $yo=$entityManager->getRepository(User::class)
            ->findOneBy(['email' => $util->getLastUsername()]);


        $msg = $this->getDoctrine()->getRepository(Message::class)
            ->findBy(array('UserId'=> $yo->getId()));
        $answer = $this->getDoctrine()->getRepository(Answer::class)
            ->findOneBy(array('UserId'=>$yo));
        return $this->render('message/index.html.twig', [
            'messages' => $msg, 'answers' => $answer
        ]);
    }
    /**
     * @IsGranted("ROLE_MODERATOR")
     * @Route("/back", name="message_back", methods={"GET"})
     */
    public function messageback(MessageRepository $messageRepository): Response
    {
        return $this->render('message/listmessageback.html.twig', [
            'messages' => $messageRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="message_new", methods={"GET","POST"})
     */
    public function new(Request $request, AuthenticationUtils $util): Response
    {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $yo=$entityManager->getRepository(User::class)
                ->findOneBy(['email' => $util->getLastUsername()]);
            $user= $entityManager->getRepository(User::class)->find($yo);
            $message->setUserId($user);
            $message->setDateMsg( new \DateTime('now'));
            $message->setStatut(0);
            $entityManager->persist($message);
            $entityManager->flush();

            return $this->redirectToRoute('message_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('message/new.html.twig', [
            'message' => $message,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="message_show", methods={"GET"})
     */
    public function show(Message $message): Response
    {
        return $this->render('message/show.html.twig', [
            'message' => $message,
        ]);
    }

    /**
     * @Route("/show/{id}", name="message_showfront", methods={"GET"})
     */
    public function showfront(Message $message): Response
    {
        return $this->render('message/showfront.html.twig', [
            'message' => $message,
        ]);
    }
    /**
     * @Route("/{id}/edit", name="message_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Message $message): Response
    {
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message->setDateMsg( new \DateTime('now'));
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('message_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('message/edit.html.twig', [
            'message' => $message,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="message_delete", methods={"POST"})
     */
    public function delete(Request $request, Message $message): Response
    {
        if ($this->isCsrfTokenValid('delete'.$message->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($message);
            $entityManager->flush();
        }

        return $this->redirectToRoute('message_index', [], Response::HTTP_SEE_OTHER);
    }
    /**
     * @IsGranted("ROLE_MODERATOR")
     * @Route("/deleteback/{id}", name="message_deleteback")
     */
    public function deleteback(Request $request, Message $message): Response
    {
        if ($this->isCsrfTokenValid('delete'.$message->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($message);
            $entityManager->flush();
        }

        return $this->redirectToRoute('message_back', [], Response::HTTP_SEE_OTHER);
    }
}
