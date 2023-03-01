<?php


namespace App\Controller;


use App\Repository\ReviewRepository;
use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
Use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class RedirectController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    public function home()
    {
        return $this->render('frontend/homefront.html.twig');
    }

    /**
     * @IsGranted("ROLE_MODERATOR")
     * @Route("/homeback", name="homeback")
     */
    public function homeback()
    {
        return $this->render('backend/homeback.html.twig');
    }

    /**
     * @Route("/about", name="about")
     */
    public function about(ReviewRepository $reviewRepository, ServiceRepository  $serviceRepository)
    {
        return $this->render('frontend/about.html.twig', [
            'reviews' => $reviewRepository->findAll(),
            'services' => $serviceRepository->findall(),
        ]);
    }
}
