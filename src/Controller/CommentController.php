<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_USER")
 * @Route("/comment")
 */
class CommentController extends AbstractController
{
    private $commentRepository;

    public function __construct(CommentRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    /**
     * @Route("/{id}", name="app_comment_add")
     */
    public function commentAdd(Album $album, Request $request): Response
    {
        $comment = new Comment();
        
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setPublishedAt(new \DateTime());
            $comment->setAlbum($album);
            $comment->setAuthor($this->getUser());
            $this->commentRepository->add($comment);

            $this->addFlash('success', "Votre avis à été envoyé");

            return $this->redirectToRoute('app_home');

        }

        return $this->renderForm('comment/add.html.twig', [
            'form' => $form
        ]);
    }

    /**
     * @Route("/update/{id}", name="app_comment_update")
     */
    public function commentUpdate(Comment $comment, Request $request)
    {
        // Je fais appel à mon voter avec un attribut particulier (COMMENT_EDIT) et sur un objet Comment particulier ($comment)
        // la méthode denyAccessUnlessGranted() vient de notre AbstractController et va appeler notre Voter.
        $this->denyAccessUnlessGranted("COMMENT_EDIT", $comment);

        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->commentRepository->add($comment);

            $this->addFlash('success', 'Avis Modifié');
            return $this->redirectToRoute('app_album', ['id' => $comment->getAlbum()->getId() ]);

        }

        return $this->renderForm("comment/update.html.twig", [
            'form' => $form
        ]);
    }

    /**
     * @Route("/delete/{id}", name="app_comment_delete", methods={"POST"})
     */
    public function commentDelete(Comment $comment, Request $request)
    {
        $this->denyAccessUnlessGranted("COMMENT_DELETE", $comment);

        if ( $this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token')) ) {
            $this->commentRepository->remove($comment);
        }

        return $this->redirectToRoute('app_home');
    }

}
