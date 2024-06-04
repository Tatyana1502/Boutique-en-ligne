<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Repository\CommandeRepository;
use App\Entity\Facture;
use App\Repository\FactureRepository;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use App\Entity\LigneCommande;
use App\Form\LigneCommande\LigneCommandeType;
use App\Form\Produit\ProduitType;
use App\Form\LigneCommande\SelectUserLigneCommandeType;
use App\Form\Produit\ProduitLigneCommandeType;
use App\Repository\LigneCommandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LigneCommanderController extends AbstractController
{
    #[Route('/lignecommander', name: 'app_lignecommander')]
    public function index(): Response
    {
        return $this->render('lignecommander/index.html.twig', [
            'controller_name' => 'LigneCommanderController',
        ]);
    }
// .....................Aficher tout les LigneCommandes.....................................
    #[Route('/all_lignecommande', name: 'all_lignecommande')]
    public function all_lignecommande(LigneCommandeRepository $lignecommandeRepository): Response
    {
        $user = $this->getUser();
        if (!$user) 
        {
            throw $this->createAccessDeniedException('Для доступа к корзине нужно войти в систему');
        }
        return $this->render('lignecommande/all_lignecommande.html.twig', [
            'all_lignecommande' => $lignecommandeRepository->findAllLigneCommandeWithJointuresByUser($user)]);
    }

// ======================= produit pour ajoute a LigneCommande===============================================
    #[Route('produit/{idProduit}/edit_produit_lignecommande', name: 'edit_produit_lignecommande', methods: ['GET', 'POST'])]
    public function edit_produit_lignecommande(Request $request, Commande $quantite_achat, 
    Produit $idProduit, EntityManagerInterface $entityManager): Response
    {
        // $this->addFlash('success', 'Connectez-vous pour ajouter au panier');
        // Crée une nouvelle instance de LigneCommande
         $lignecommande = new LigneCommande();

         // Obtient l'utilisateur actuel
         $user = $this->getUser();
         
         $lignecommande->setIdProduit($idProduit);

         // Définit la quantité d'achat sur 1
         $lignecommande->setQuantiteAchat(1);

         // Calcule et définit le total de la ligne de commande (à implémenter)
         $lignecommande->setTotal();

         // Définit l'identifiant de l'utilisateur pour cette ligne de commande
         $lignecommande->setId($user);

         // Persiste la nouvelle ligne de commande dans l'entité manager
         $entityManager->persist($lignecommande);

         // Enregistre les changements dans la base de données
         $entityManager->flush();

          // Récupère toutes les lignes de commande de la commande associée
         $allLigneCommande = $quantite_achat->getToutLigneCommande();  

         // Set a flash message
         $this->addFlash('success', 'Your action was successful!');

         // Redirige vers la page affichant toutes les lignes de commande 
         return $this->redirectToRoute('all_lignecommande', [], Response::HTTP_SEE_OTHER);
    }
// =======================modifier de LigneCommandes===============================================
    #[Route('/LigneCommande/{idLigneCommande}/edit_LigneCommande', name: 'edit_LigneCommande', methods: ['GET', 'POST'])]
    public function edit_LigneCommande(Request $request, LigneCommande $lignecommande, EntityManagerInterface $entityManager): Response
    {
        // Récupération de la nouvelle quantité envoyée via la requête POST
        $newQuantite = $request->request->get('quantite');

        // Mise à jour de la quantité dans l'entité LigneCommande
        $lignecommande->setQuantiteAchat($newQuantite);
        $entityManager->flush();
        return $this->redirectToRoute('all_lignecommande', [], Response::HTTP_SEE_OTHER);
    }

// =======================suprime de Ligne Commande===============================================
    #[Route('/LigneCommande/{idLigneCommande}/suprime_LigneCommande', name: 'suprime_LigneCommande')]
    public function suprime_LigneCommande(LigneCommande $LigneCommande, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($LigneCommande);
        $entityManager->flush();
        return $this->redirectToRoute('all_lignecommande');
    }
// =======================select par emai USER===============================================
    #[Route('/select_par_user_ligneCommande', name: 'select_par_user_ligneCommande', methods: ['GET', 'POST'])]
    public function select_par_user_ligneCommande(Request $request, LigneCommandeRepository $LigneCommandeRepo, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(SelectUserLigneCommandeType::class, null);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {
            $Selectionne = $form->get('id')->getData();
            return $this->render('lignecommande/all_lignecommande.html.twig',[  
                'all_lignecommande' =>$LigneCommandeRepo->findBy(['id'=> $Selectionne]),
            ]);
        }
        return $this->render('lignecommande/select_par_user_ligneCommande.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
