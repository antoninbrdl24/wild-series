<?php

namespace App\Controller;

use App\Entity\Episode;
use App\Entity\Comment;
use App\Form\EpisodeType;
use App\Form\CommentType;
use Symfony\Component\Mime\Email;
use App\Repository\EpisodeRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\service\ProgramDuration;
use Symfony\Component\Mailer\MailerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/episode')]
class EpisodeController extends AbstractController
{
    #[Route('/', name: 'app_episode_index', methods: ['GET'])]
    public function index(EpisodeRepository $episodeRepository): Response
    {
        return $this->render('episode/index.html.twig', [
            'episodes' => $episodeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_episode_new', methods: ['GET', 'POST'], )]
    #[IsGranted('ROLE_CONTRIBUTOR')]
    public function new(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer,  SluggerInterface $slugger,  ProgramDuration $programDuration): Response
    {

        $episode = new Episode();

        $form = $this->createForm(EpisodeType::class, $episode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slug = $slugger->slug($episode->getNumber());
            $episode->setSlug($slug);
            $entityManager->persist($episode);
            $entityManager->flush();

             
            $email = (new Email())
            ->from($this->getParameter('mailer_from'))
            ->to('antonin.brondel24@gmail.com')
            ->subject('Un nouvel épisode vient d\'être publiée !')
            ->html($this->renderView('Episode/newEpisodeEmail.html.twig', ['episode' => $episode]));

            $mailer->send($email);

            $this->addFlash('success', 'The new episode has been created');

            return $this->redirectToRoute('app_episode_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('episode/new.html.twig', [
            'episode' => $episode,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_episode_show', methods: ['GET','POST'])]
    public function show(Episode $episode, EntityManagerInterface $entityManager, CommentRepository $commentRepository, Request $request): Response
    {
        $comments = $commentRepository->findBy(['Episode' => $episode], ['createdAt' => 'ASC']);
        $comment = new Comment();
        $comment->setEpisode($episode); 
        $comment->setAuthor($this->getUser()); 

        $comment->setCreatedAt();
    
        $form = $this->createForm(CommentType::class, $comment);
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Persistez le commentaire dans la base de données
            $entityManager->persist($comment);
            $entityManager->flush();
    
            // Redirigez l'utilisateur après avoir soumis le commentaire
            return $this->redirectToRoute('app_episode_show', ['id' => $episode->getId()]);
        }
    
        return $this->render('episode/show.html.twig', [
            'episode' => $episode,
            'comments' => $comments,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_episode_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Episode $episode, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(EpisodeType::class, $episode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slug = $slugger->slug($episode->getNumber());
            $episode->setSlug($slug);
            $entityManager->flush();

            $this->addFlash('success', 'The episode has been edited successfully');

            return $this->redirectToRoute('app_episode_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('episode/edit.html.twig', [
            'episode' => $episode,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_episode_delete', methods: ['POST'])]
    public function delete(Request $request, Episode $episode, EntityManagerInterface $entityManager): Response
    {
        $submittedToken = $request->request->get('_token');
        var_dump($submittedToken);
        if ($this->isCsrfTokenValid('delete'.$episode->getId(), $request->request->get('_token'))) {
            $entityManager->remove($episode);
            $entityManager->flush();

            $this->addFlash('danger', 'The episode has been deleted successfully');
        }

        return $this->redirectToRoute('app_episode_index', [], Response::HTTP_SEE_OTHER);
    }
}
