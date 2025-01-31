<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/home.html.twig', [
            'nom' => 'Rym Badji',
        ]);
    }

    #[Route('/home2', name: 'app_home2')]
    public function index1(): Response
    {
        return $this->render('home/home2.html.twig');
    }
}
