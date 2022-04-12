<?php

namespace App\Controller;

use App\Repository\AlbumRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(AlbumRepository $albumRepository): Response
    {
        // Ici on va vouloir afficher tous nos albums 
        $albums = $albumRepository->findAll();
        // On va créer un système de filtre pour faire des recherches spécifiques
        return $this->render('front/index.html.twig', [
            'albums' => $albums,
        ]);
    }
}
