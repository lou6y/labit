<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Produit;
use App\Entity\User;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/commande")
 * @IsGranted("ROLE_USER")
 */
class CommandeController extends AbstractController
{
    /**
     * @Route("/", name="commande_index", methods={"GET"})
     */
    public function index(Request $request,AuthenticationUtils $util): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $yo=$entityManager->getRepository(User::class)
            ->findOneBy(['email' => $util->getLastUsername()]);
        $commande = $this->getDoctrine()->getRepository(Commande::class)
            ->findBy(array('UserId'=> $yo->getId()));
        return $this->render('commande/index.html.twig', [
            'commandes' => $commande
        ]);
    }

    /**
     * @IsGranted("ROLE_MODERATOR")
     * @Route("/back", name="commande_back", methods={"GET"})
     */
    public function back(CommandeRepository $commandeRepository): Response
    {
        return $this->render('commande/listcommandeback.html.twig', [
            'commandes' => $commandeRepository->findAll(),
        ]);
    }


    /**
     * @Route("/new/{idproduit}/{prixtotale}", name="commande_new", methods={"GET","POST"})
     */
    public function new(Request $request, AuthenticationUtils $util,$idproduit,$prixtotale): Response
    {
        $commande = new Commande();
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $produit = $entityManager->getRepository(Produit::class)->find($idproduit);
            $commande->addProduitId($produit);

            $yo=$entityManager->getRepository(User::class)
                ->findOneBy(['email' => $util->getLastUsername()]);
            $commande->setUserId($yo);
            $commande->setDateCmd( new \DateTime('now'));
            $commande->setPrixTotale($prixtotale);
            $commande->setConfirmed(0);
            $entityManager->persist($commande);
            $entityManager->flush();

            return $this->redirectToRoute('commande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('commande/new.html.twig', [
            'commande' => $commande,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_MODERATOR")
     * @Route("/{id}", name="commande_show", methods={"GET"})
     */
    public function show(Commande $commande): Response
    {
        return $this->render('commande/show.html.twig', [
            'commande' => $commande,
        ]);
    }

    /**
     * @Route("/show/{id}", name="commande_showfront", methods={"GET"})
     */
    public function showfront(Commande $commande): Response
    {
        return $this->render('commande/showfront.html.twig', [
            'commande' => $commande,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="commande_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Commande $commande): Response
    {
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('commande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('commande/edit.html.twig', [
            'commande' => $commande,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_MODERATOR")
     * @Route("/confirmer/{idcommande}", name="commande_confirm", methods={"GET"})
     */
    public function confirmer( int $idcommande, \Swift_Mailer $mailer): Response
    {
        $commande = $this->getDoctrine()->getRepository(Commande::class)->find($idcommande);
        $commande->setConfirmed(true);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();
        $mailuser=$commande->getUserId()->getEmail();
        $message = (new \Swift_Message('Confirmation'))
            ->setFrom('Lab-it@lab-it.tn')
            ->setTo($mailuser)
            ->setBody( $this->renderView('commande/facture.html.twig',
                ['commande' => $commande]),
                'text/html'
            );
        $mailer->send($message);
        return $this->redirectToRoute('commande_back');
    }

    /**
     * @Route("/facture/{idcmd}", name="facture", methods={"GET"})
     */
    public function facture(int $idcmd): Response
    {
        $commande = $this->getDoctrine()->getRepository(Commande::class)->find($idcmd);
        return $this->render('commande/facture.html.twig', [
            'commande' => $commande,
        ]);
    }

    /**
     * @Route("/pdf/{idcmd}", name="pdf")
     */
    public function pdf(int $idcmd): Response
    {
        $commande = $this->getDoctrine()->getRepository(Commande::class)->find($idcmd);
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('commande/facture.html.twig', ['commande' => $commande]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("facture.pdf", [
            "Attachment" => true
        ]);

    }

    /**
     * @Route("/{id}", name="commande_delete", methods={"POST"})
     */
    public function delete(Request $request, Commande $commande): Response
    {
        if ($this->isCsrfTokenValid('delete'.$commande->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($commande);
            $entityManager->flush();
        }

        return $this->redirectToRoute('commande_index', [], Response::HTTP_SEE_OTHER);
    }
}
