<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Repository\ProduitRepository;
use App\Form\Produit\ProduitType;
use App\Form\Produit\AimeProduitType;
use App\Form\Produit\ProduitLigneCommandeType;
use App\Entity\LigneCommande;
use App\Repository\LigneCommandeRepository;
use App\Form\LigneCommande\LigneCommandeType;
use App\Form\Produit\CategorieProduitType;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\ChoiceList\ChoiceList;

class ProduitController extends AbstractController
{
    #[Route('/produit', name: 'app_produit')]
    public function index(): Response
    {
        return $this->render('produit/all_produit.html.twig', [
            'controller_name' => 'ProduitController',
        ]);
    }
// .....................Aficher tout les produit.....................................
    #[Route('/all_produit', name: 'all_produit')]
    public function all_produit(ProduitRepository $ProduitRepository): Response
    {
        
        return $this->render('produit/all_produit.html.twig', [
            'produit' => $ProduitRepository->findAll()
        ]);
    }

// .....................ajoute le produit.....................................
    #[Route('/add_produit', name: 'add_produit')]
    public function add_produit(EntityManagerInterface $entityManager, Request $request): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {
            $imageFile = $form->get('image_produit')->getData();
        
            // Проверка наличия файла
            if ($imageFile) 
            {
                // Генерация уникального имени файла
                $fileName = md5(uniqid()) . '.' . $imageFile->guessExtension();
    
                // Перемещение файла в директорию для хранения
                $imageFile->move(
                    $this->getParameter('photos_directory'),
                    $fileName
                );
                // Сохранение имени файла в сущности
                
                $produit->setNomProduit($form->get('nom_produit')->getData());
                $produit->setDescriptionProduit($form->get('description_produit')->getData());
                $produit->setPrixProduit($form->get('prix_produit')->getData());
                $produit->setCategorieProduit($form->get('categorie_produit')->getData());
                $produit->setCodeBarreProduit($form->get('code_barre_produit')->getData());
                $produit->setImageProduit($fileName);
                $produit->setQuantityProduit($form->get('quantity_produit')->getData());
            }
            $entityManager->persist($produit);
            $entityManager->flush();
            return $this->redirectToRoute('all_produit', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('produit/add_produit.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
        ]);
    }
// =======================modifier de produit===============================================
    #[Route('produit/{idProduit}/edit', name: 'edit_produit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {
            // Vérifie si un nouveau fichier d'image a été téléchargé
            $imageFile = $form->get('image_produit')->getData();
            if ($imageFile) 
            {
                // Генерация уникального имени файла
                 $fileName = md5(uniqid()) . '.' . $imageFile->guessExtension();
                 // Перемещение файла в директорию для хранения
                $imageFile->move(
                    $this->getParameter('photos_directory'),
                    $fileName
                );
                 $produit->setImageProduit($fileName);
             }
            $entityManager->flush();
            return $this->redirectToRoute('all_produit', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('produit/edit_produit.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

// =======================suprime de produit===============================================
     #[Route('/admin/produit/{idProduit}/delete_produit', name: 'delete_produit')]
    public function delete_produit(Produit $produit, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($produit);
        $entityManager->flush();
        return $this->redirectToRoute('all_produit');
    }
 // =======================select par Categorie===============================================
    #[Route('/select_produit', name: 'select_produit', methods: ['GET', 'POST'])]
    public function select_produit(Request $request, ProduitRepository $CategorieProduitRepo, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(CategorieProduitType::class, null);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {
            $Selectionne = $form->get('categorie_produit')->getData();
            return $this->render('produit/all_produit.html.twig',[  
                'produit' =>$CategorieProduitRepo->findBy(['categorie_produit'=> $Selectionne]),
            ]);
        }
        return $this->render('produit/select_produit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
  
// =======================select par Categorie "FRUITS"===============================================             
    #[Route('/select_produit_fruits', name: 'select_produit_fruits')]
     public function select_produit_fruits(ProduitRepository $ProduitRepository): Response
    {
        return $this->render('produit/all_produit.html.twig', [
            'produit' => $ProduitRepository->findBy(['categorie_produit' => 'des fruits'])
        ]);
    }   
// =======================select par Categorie "legumes"===============================================             
    #[Route('/select_produit_legume', name: 'select_produit_legume')]
    public function select_produit_legume(ProduitRepository $ProduitRepository): Response
    {           
        return $this->render('produit/all_produit.html.twig', [
            'produit' => $ProduitRepository->findBy(['categorie_produit' => 'legumes'])
        ]);
    }    

// =======================select par Categorie "baies"===============================================             
    #[Route('/select_produit_baies', name: 'select_produit_baies')]
    public function select_produit_baies(ProduitRepository $ProduitRepository): Response
    {                  
        return $this->render('produit/all_produit.html.twig', [
            'produit' => $ProduitRepository->findBy(['categorie_produit' => 'baies'])
        ]);
    }  
// =======================select par Categorie "noisettes"===============================================             
    #[Route('/select_produit_noisettes', name: 'select_produit_noisettes')]
    public function select_produit_noisettes(ProduitRepository $ProduitRepository): Response
    {
        return $this->render('produit/all_produit.html.twig', [
             'produit' => $ProduitRepository->findBy(['categorie_produit' => 'noisettes'])
        ]);
    }   
 // =======================select par Categorie "noisettes"===============================================             
    #[Route('/select_produit_rasin', name: 'select_produit_rasin')]
    public function select_produit_rasin(ProduitRepository $ProduitRepository): Response
    {             
        return $this->render('produit/all_produit.html.twig', [
            'produit' => $ProduitRepository->findBy(['categorie_produit' => 'Raisin'])
        ]);
 }  
      
// =======================select par Categorie "noisettes"===============================================             
    #[Route('/select_produit_apple', name: 'select_produit_apple')]
    public function select_produit_apple(ProduitRepository $ProduitRepository): Response
    {            
        return $this->render('produit/all_produit.html.twig', [
            'produit' => $ProduitRepository->findBy(['categorie_produit' => 'Apple'])
        ]);
    }  
// =======================select par Categorie "noisettes"===============================================             
    #[Route('/select_produit_citron', name: 'select_produit_citron')]
    public function select_produit_citron(ProduitRepository $ProduitRepository): Response
    {          
        return $this->render('produit/all_produit.html.twig', [
            'produit' => $ProduitRepository->findBy(['categorie_produit' => 'Agrumes'])
        ]);
    }  
// =======================cherche tout Produit===============================================     
    #[Route('/search', name: 'produit_search')]
    public function search(Request $request, ProduitRepository $produitRepository): Response
    {
        $query = $request->query->get('q'); // Получаем поисковый запрос из URL
        $produit = $produitRepository->searchByName($query);
        return $this->render('produit/all_produit.html.twig', [
            'produit' => $produit,
        ]);
    }
}
