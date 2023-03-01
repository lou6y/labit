<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/produit")
 */
class ProduitController extends AbstractController
{
    /**
     * @Route("/", name="produit_index", methods={"GET"})
     */
    public function index(ProduitRepository $produitRepository): Response
    {
        return $this->render('produit/index.html.twig', [
            'produits' => $produitRepository->findAll(),
        ]);
    }

    /**
     * @Route("/category/{CategoryId}", name="produit_category", methods={"GET"})
     */
    public function ProduitCategory(Request $request, int $CategoryId): Response
    {
        $repository = $this->getDoctrine()->getRepository(Category::class)->find($CategoryId);
        return $this->render('produit/index.html.twig', [
            'produits' => $repository
        ]);
    }

    /**
     * @IsGranted("ROLE_MODERATOR")
     * @Route("/back", name="produit_back", methods={"GET"})
     */
    public function produitback(ProduitRepository $produitRepository): Response
    {
        return $this->render('produit/listproduitback.html.twig', [
            'produits' => $produitRepository->findAll(),
        ]);
    }

    /**
     * @IsGranted("ROLE_MODERATOR")
     * @Route("/new", name="produit_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $produit->setDateAjout( new \DateTime('now'));
            $entityManager->persist($produit);
            $entityManager->flush();

            return $this->redirectToRoute('produit_back', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="produit_show", methods={"GET"})
     */
    public function show(Produit $produit): Response
    {
        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }

    /**
     * @Route("/show/{id}", name="produit_showfront", methods={"GET"})
     */
    public function showfront(Produit $produit): Response
    {
        return $this->render('produit/showfront.html.twig', [
            'produit' => $produit,
        ]);
    }

    /**
     * @IsGranted("ROLE_MODERATOR")
     * @Route("/{id}/edit", name="produit_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Produit $produit): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('produit_back', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_MODERATOR")
     * @Route("/{id}", name="produit_delete", methods={"POST"})
     */
    public function delete(Request $request, Produit $produit): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($produit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('produit_back', [], Response::HTTP_SEE_OTHER);
    }
}
