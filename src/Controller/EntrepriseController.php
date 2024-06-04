<?php

namespace App\Controller;

use App\Entity\Entreprise;
use App\Repository\EntrepriseRepository;
use App\Form\EntrepriseType;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class EntrepriseController extends AbstractController
{
    #[Route('/app_entreprise', name: 'app_entreprise')]
    public function index(): Response
    {
        return $this->render('entreprise/index.html.twig', [
            'controller_name' => 'EntrepriseController',
        ]);
    }

// .....................Aficher Entreprise.....................................
    #[Route('/entreprise', name: 'entreprise')]
    public function entreprise(EntrepriseRepository $entrepriseRepository): Response
    {   
        return $this->render('entreprise/entreprise.html.twig', [
            'entreprise' => $entrepriseRepository->findAll()]);
    }
    
// =======================modifier d'Entreprise===============================================
    #[Route('/admin/entreprise/{idEntreprise}/edit_entreprise', name: 'edit_entreprise', methods: ['GET', 'POST'])]
    public function edit_entreprise(Request $request, Entreprise $entreprise, EntityManagerInterface $entityManager): Response
    {
        // Create a form for the company entity using the EntrepriseType form class
        $form = $this->createForm(EntrepriseType::class, $entreprise);

        // Handle the request and submit the form data
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
         {
            // Save the changes to the database
            $entityManager->flush();
            // Redirect to the 'app_home' route with a HTTP 303 See Other response
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        // Render the template 'edit_entreprise.html.twig' with the company entity and form
        return $this->render('entreprise/edit_entreprise.html.twig', [
            'entreprise' => $entreprise,
            'form' => $form,
         ]);
    }
}
