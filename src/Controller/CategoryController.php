<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CategoryRepository;
use App\Repository\ProgramRepository;
use App\Form\CategoryType;
use App\Entity\Category;

#[Route('/category', name: 'category_')]
class CategoryController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();
    
        return $this->render(
            'category/index.html.twig',
            ['categories' => $categories]
            );
    }

    #[Route('/new', name: 'new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $category = new Category();

        // Create the form, linked with $category
        $form = $this->createForm(CategoryType::class, $category);
        
        $form->handleRequest($request);
        // Was the form submitted ?
        if ($form->isSubmitted()) {
            $entityManager->persist($category);
            $entityManager->flush();            
    
            // Redirect to categories list
            return $this->redirectToRoute('category_index');
        }

        return $this->render('category/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{categoryName}', name: 'show')]
    public function show(string $categoryName, CategoryRepository $categoryRepository, ProgramRepository $programRepository):Response
    {
        $category = $categoryRepository->findOneBy(['name' => $categoryName]);
        
        if (!$category) {
            throw $this->createNotFoundException(
                'No category with id : '.$categoryName.'  found in category\'s table.'
            );
        }
        $programs = $programRepository->findBy(
            ['category' => $category],
            ['id' => 'DESC'],
            3
        );

        return $this->render('category/show.html.twig', [
            'category' => $category,
            'programs' => $programs,
        ]);
    }
}