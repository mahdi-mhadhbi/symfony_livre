<?php
namespace App\Controller;

use App\Entity\Categories;
use App\Entity\Editeur;
use App\Form\CategoriesType;
use App\Repository\AuteurRepository;
use App\Repository\CategoriesRepository;
use App\Repository\EditeurRepository;  // Import the EditeurRepository
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\DocBlock\Tags\Author;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/categories')]
final class CategoriesController extends AbstractController
{
    #[Route(name: 'app_categories_index', methods: ['GET'])]
    public function index(CategoriesRepository $categoriesRepository,AuteurRepository $auteurRepository ,EditeurRepository $editeurRepository): Response
    {
        // Fetch all editors using the injected repository
        $editors = $editeurRepository->findAll();

        // Fetch all categories
        $categories = $categoriesRepository->findAll();

        // Fetch all authors
        $Auteur = $auteurRepository->findAll();

        return $this->render('categories/index.html.twig', [
            'categories' => $categories,
            'editors' => $editors, // Pass editors to the view
            'authors'=> $Auteur,
        ]);
    }

    #[Route('/new', name: 'app_categories_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, EditeurRepository $editeurRepository): Response
    {
        $category = new Categories();
        $form = $this->createForm(CategoriesType::class, $category);

        // Get all editors and pass them to the form
        $editors = $editeurRepository->findAll();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('app_categories_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('categories/new.html.twig', [
            'category' => $category,
            'form' => $form,
            'editors' => $editors, // Passing editors to the view
        ]);
    }

    #[Route('/{id}', name: 'app_categories_show', methods: ['GET'])]
    public function show(Categories $category, CategoriesRepository $categoriesRepository, EditeurRepository $editeurRepository): Response
    {
        // Fetch all editors to display in the view
        $editors = $editeurRepository->findAll();
        return $this->render('categories/show.html.twig', [
            'category' => $category,
            'editors' => $editors, // Passing editors to the view
        ]);
    }

    #[Route('/{id}/edit', name: 'app_categories_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Categories $category, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategoriesType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_categories_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('categories/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_categories_delete', methods: ['POST'])]
    public function delete(Request $request, Categories $category, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->get('_token'))) {
            $entityManager->remove($category);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_categories_index', [], Response::HTTP_SEE_OTHER);
    }
}
