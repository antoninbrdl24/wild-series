<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Program;
use App\Entity\Season;
use App\Entity\Episode;
use App\Entity\Category;
use App\service\ProgramDuration;
use App\Form\ProgramType;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Repository\CategoryRepository;
use App\Repository\ProgramRepository;
use App\Repository\SeasonRepository;
use App\Repository\EpisodeRepository;

#[Route('/program', name: 'program_')]
class ProgramController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ProgramRepository $programRepository): Response
    {
        $programs = $programRepository->findAll();
    
        return $this->render(
            'program/index.html.twig',
            ['programs' => $programs]
            );
        }
    
    #[Route('/new', name: 'new')]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger,  ProgramDuration $programDuration): Response
    {
        $program = new Program();

        // Create the form, linked with $category
        $form = $this->createForm(ProgramType::class, $program);
        
        $form->handleRequest($request);
        // Was the form submitted ?
        if ($form->isSubmitted() && $form->isValid()) {
            $slug = $slugger->slug($program->getTitle());
            $program->setSlug($slug); 
            $entityManager->persist($program);
            $entityManager->flush();            
    
            // Redirect to categories list
            return $this->redirectToRoute('program_index');
        }

        return $this->render('program/new.html.twig', [
            'form' => $form,
            'programDuration' => $programDuration->calculate($program)
        ]);
    }
    #[Route('/show/{slug}', name: 'show')]
    public function show( 
        #[MapEntity(mapping:['slug'=>'slug'])] Program $program, ProgramDuration $programDuration): Response
    {
        if (!$program) {
            throw $this->createNotFoundException(
                'No program found in program\'s table.'
            );
        }
        return $this->render('program/show.html.twig', [
            'program' => $program,
            'programDuration' => $programDuration->calculate($program),
        ]);
    }
    #[Route('/{programSlug}/season/{seasonSlug}', name: 'season_show')]
    public function showSeason( $programSlug, ProgramRepository $programRepository, $seasonSlug, SeasonRepository $seasonRepository) : Response
    {   
        $program = $programRepository->findOneBy(['slug' => $programSlug]);
        $season=$seasonRepository->findOneBy(['slug'=>$seasonSlug]);
        
        if (!$program) {
            throw $this->createNotFoundException('Program not found');
        }

        if (!$season) {
            throw $this->createNotFoundException('Season not found for the given program');
        }
        
        return $this->render('program/season_show.html.twig', [
            'program' => $program,
            'season' => $season,
        ]);
    }

    #[Route('/{programSlug}/season/{seasonSlug}/episode/{episodeSlug}', name: 'episode_show')]
    public function showEpisode(
         $programSlug, ProgramRepository $programRepository, $seasonSlug, SeasonRepository $seasonRepository, $episodeSlug, EpisodeRepository $episodeRepository):Response
        {
            $program = $programRepository->findOneBy(['slug' => $programSlug]);
            $season=$seasonRepository->findOneBy(['slug'=>$seasonSlug]);
            $episode=$episodeRepository->findOneBy(['slug'=>$episodeSlug]);
        if (!$program) {
            throw $this->createNotFoundException('Program not found');
        }

        if (!$season) {
            throw $this->createNotFoundException('Season not found for the given program');
        }
        
        if (!$episode) {
            throw $this->createNotFoundException('Episode not found for the given season');
        }
        return $this->render('program/episode_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episode' => $episode,
        ]);
    }
    #[Route('/edit/{slug}', name: 'edit')]
    public function edit(Request $request, Program $program, SluggerInterface $slugger, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProgramType::class, $program);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $slug = $slugger->slug($program->getTitle());
            $program->setSlug($slug); 

            $entityManager->flush();

            $this->addFlash('success', 'The program has been edited successfully');

            return $this->redirectToRoute('program_index');
        }

        return $this->render('program/edit.html.twig', [
            'form' => $form->createView(),
            'program' => $program,
        ]);
    }

    #[Route('/delete/{id<^[0-9]+$>}', name: 'delete')]
    public function delete(Program $program, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($program);
        $entityManager->flush();
        $this->addFlash('danger', 'The program has been deleted successfully');

        return $this->redirectToRoute('program_index');
    }

}