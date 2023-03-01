<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Message;
use App\Entity\User;
use App\Form\AnswerType;
use App\Repository\AnswerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/answer")
 * @IsGranted("ROLE_USER")
 */
class AnswerController extends AbstractController
{
    /**
     * @Route("/", name="answer_index", methods={"GET"})
     */
    public function index(AnswerRepository $answerRepository): Response
    {
        return $this->render('answer/index.html.twig', [
            'answers' => $answerRepository->findAll(),
        ]);
    }

    /**
     * @IsGranted("ROLE_MODERATOR")
     * @Route("/new/{idmsg}", name="answer_new", methods={"GET","POST"})
     */
    public function new(Request $request, AuthenticationUtils $util,$idmsg): Response
    {
        $answer = new Answer();
        $form = $this->createForm(AnswerType::class, $answer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $msg = $entityManager->getRepository(Message::class)->find($idmsg);
            $yo=$entityManager->getRepository(User::class)
                ->findOneBy(['email' => $util->getLastUsername()]);

            $user= $entityManager->getRepository(User::class)->find($yo);
            $answer->setMessageId($msg);
            $answer->setUserId($user);
            $msg->setStatut(1);
            $answer->setDateAns(new \DateTime('now'));
            $entityManager->persist($answer);
            $entityManager->persist($msg);
            $entityManager->flush();

            return $this->redirectToRoute('message_back', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('answer/new.html.twig', [
            'answer' => $answer,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_MODERATOR")
     * @Route("/{idmsg}", name="answer_show", methods={"GET"})
     */
    public function show(int $idmsg): Response
    {
        $answer = $this->getDoctrine()->getRepository(Answer::class)->findOneBy(['MessageId' => $idmsg]);
        return $this->render('answer/show.html.twig', [
            'answer' => $answer,
        ]);
    }
    /**
     * @Route("/show/{idmsg}", name="answer_showfront", methods={"GET"})
     */
    public function showfront(int $idmsg): Response
    {
        $answer = $this->getDoctrine()->getRepository(Answer::class)->findOneBy(['MessageId' => $idmsg]);
        return $this->render('answer/showfront.html.twig', [
            'answer' => $answer,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="answer_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Answer $answer): Response
    {
        $form = $this->createForm(AnswerType::class, $answer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->render('answer/show.html.twig', [
                'answer' => $answer,
            ]);
        }

        return $this->render('answer/edit.html.twig', [
            'answer' => $answer,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_MODERATOR")
     * @Route("/{id}", name="answer_delete", methods={"POST"})
     */
    public function delete(Request $request, Answer $answer): Response
    {
        if ($this->isCsrfTokenValid('delete'.$answer->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($answer);
            $entityManager->flush();
        }

        return $this->redirectToRoute('message_back', [], Response::HTTP_SEE_OTHER);
    }
}
