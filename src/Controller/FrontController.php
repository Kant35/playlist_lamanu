<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Repository\AlbumRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class FrontController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(AlbumRepository $albumRepository, Request $request): Response
    {
        // dump($request->request->all());
        // $data = $request->request->all();
        // // Ici on va vouloir afficher tous nos albums 
        // $albums = $albumRepository->findAll();
        // $albums = $albumRepository->findAllOrdered($data['artist']);
        // On va créer un système de filtre pour faire des recherches spécifiques


        // Si le formulaire à été soumis
        // Si $_POST est remplit. En Symfony si $request est remplit
        if (!empty($request->request->all())) {
            // Je stocke toutes les valeurs reçu depuis mon formulaire
            $search = $request->request->all();

            // Maintenant je lance ma requête pour filtrer les albums
            $albums = $albumRepository->findAlbumFilter($search);

        } else {
            $albums = $albumRepository->findAll();
        }



        return $this->render('front/index.html.twig', [
            'albums' => $albums,
            'search' => $search ?? []
        ]);
    }

    /**
     * @Route("/inscription", name="app_inscription")
     */
    public function inscription(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $hasher)
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setCreatedAt(new \DateTime());
            // Je récupère le mot de passe entier (visible)
            $pwd = $user->getPassword();
            // J'utilise la méthode hashPassword() de la classe UserPasswordHasherInterface pour hashé mon mdp. 
            // Cette méthode attends 2 paramètres: le user et le mot de passe visible
            $pwdHashed = $hasher->hashPassword($user, $pwd);
            // J'ai maintenant le mdp hashé. 
            // Je peux donc l'envoyer dans mon objet utilisateur.
            $user->setPassword($pwdHashed);

            $userRepository->add($user);
            
            $this->addFlash("succes", "Bravo vous êtes inscrit");

            return $this->redirectToRoute('app_home');
        }


        return $this->renderForm("front/inscription.html.twig", [
            'form' => $form
        ]);
    }
}
