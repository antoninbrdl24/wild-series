<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProgramController extends AbstractController
{
    #[Route('/program/', name: 'program_index')]
    public function index(): Response
    {
        return $this->render('program/index.html.twig', [
           'website' => 'Wild Series',
        ]);
    }
    #[Route('/program/{id}/', name: 'program_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(int $id): Response
    {
        // Load program data from the database based on $id
        // Replace this with your actual logic to fetch program data
        $programData = [
            'id' => $id,
            // Add other program data fields here
        ];

        return $this->render('program/show.html.twig', [
            'id' => $id,
        ]);
    }
}