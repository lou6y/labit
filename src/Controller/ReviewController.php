<?php

namespace App\Controller;

use App\Entity\Review;
use App\Entity\User;
use App\Form\ReviewType;
use App\Repository\ReviewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/review")
 * @IsGranted("ROLE_USER")
 */
class ReviewController extends AbstractController
{
    /**
     * @Route("/", name="review_index", methods={"GET"})
     */
    public function index(Request $request,AuthenticationUtils $util): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $yo=$entityManager->getRepository(User::class)
            ->findOneBy(['email' => $util->getLastUsername()]);
        $review = $this->getDoctrine()->getRepository(Review::class)
            ->findBy(array('IdUser'=> $yo->getId()));
        return $this->render('review/index.html.twig', [
            'reviews' => $review
        ]);
    }
    /**
     * @IsGranted("ROLE_MODERATOR")
     * @Route("/back", name="review_back", methods={"GET"})
     */
    public function reviewback(ReviewRepository $reviewRepository): Response
    {
        return $this->render('review/listreviewback.html.twig', [
            'reviews' => $reviewRepository->findAll(),
        ]);
    }
    /**
     * @Route("/new", name="review_new", methods={"GET","POST"})
     */
    public function new(Request $request, AuthenticationUtils $util): Response
    {
        $review = new Review();
        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $yo=$entityManager->getRepository(User::class)
                ->findOneBy(['email' => $util->getLastUsername()]);
            $user= $entityManager->getRepository(User::class)->find($yo);
            $review->setIdUser($user);
            $review->setDaterev( new \DateTime('now'));
            $entityManager->persist($review);
            $entityManager->flush();

            return $this->redirectToRoute('review_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('review/new.html.twig', [
            'review' => $review,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="review_show", methods={"GET"})
     */
    public function show(Review $review): Response
    {
        return $this->render('review/show.html.twig', [
            'review' => $review,
        ]);
    }

    /**
     * @Route("/show/{id}", name="review_showfront", methods={"GET"})
     */
    public function showfront(Review $review): Response
    {
        return $this->render('review/showfront.html.twig', [
            'review' => $review,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="review_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Review $review): Response
    {
        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $review->setDaterev( new \DateTime('now'));
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('review_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('review/edit.html.twig', [
            'review' => $review,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="review_delete", methods={"POST"})
     */
    public function delete(Request $request, Review $review): Response
    {
        if ($this->isCsrfTokenValid('delete'.$review->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($review);
            $entityManager->flush();
        }

        return $this->redirectToRoute('review_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @IsGranted("ROLE_MODERATOR")
     * @Route("/deleteback/{id}", name="review_deleteback")
     */
    public function deleteback(Request $request, Review $review): Response
    {
        if ($this->isCsrfTokenValid('delete'.$review->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($review);
            $entityManager->flush();
        }

        return $this->redirectToRoute('review_back', [], Response::HTTP_SEE_OTHER);
    }
}
