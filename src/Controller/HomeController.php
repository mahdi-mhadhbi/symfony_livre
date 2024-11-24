<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\LivreRepository;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(LivreRepository $livreRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'livres' => $livreRepository->findAll(),
        ]);
    }
}
