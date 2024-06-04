<?php

namespace App\Controller;

use App\Controller\LigneCommandeController;
use App\Entity\LigneCommande;
use App\Repository\LigneCommandeRepository;
use App\Entity\Commande;
use App\Repository\CommandeRepository;
use App\Entity\Facture;
use App\Repository\FactureRepository;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Entity\Produit;
use App\Entity\Panier;
use App\Repository\PanierRepository;
use App\Repository\ProduitRepository;
use App\Form\CommandeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
// use Symfony\Component\Validator\Constraints\DateTime;
use DateTime;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
// use App\Service\CommandeService;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommandeController extends AbstractController
{
    #[Route('/commande', name: 'app_commande')]
    public function index(): Response
    {
        return $this->render('commande/index.html.twig', [
            'controller_name' => 'CommandeController',
        ]);
    }

    public function calculateTotalFromToutLigneCommande(array $toutLigneCommande): float
    {
    $total = 0.0;

    foreach ($toutLigneCommande as $ligneCommandeData) {
        $total += $ligneCommandeData['total'];
    }

    return $total;
    }
// .....................Aficher  le commande status "active" pour le User .....................................
    #[Route('/all_commande', name: 'all_commande')]
    public function all_commande(Commande $commande, CommandeRepository $commandeRepository): Response
    {
        $user = $this->getUser();
        // Récupérer toutes les commandes actives
         $toutLigneCommande = $commandeRepository->findBy(['statut_commande' => 'active', 'user' => $user]);
        // Initialiser la somme totale à zéro
        $totalSum = 0.0;
        // Parcourir toutes les commandes actives
        foreach ($toutLigneCommande as $commande) 
        {
            $premiereCommande = $toutLigneCommande[0];
            $toutLigneCommandeArray = $premiereCommande->getToutLigneCommande();        
            // Si la liste des lignes de commande n'est pas vide  
            if ($toutLigneCommandeArray !== null)
            {
                // Iterate through each item in ToutLigneCommande
                foreach ($toutLigneCommandeArray as $ligneCommande)
                {
                    // Add the total of each line to the total sum
                    $totalSum += $ligneCommande['total'];
                }
            }
        }
        // Rendre le template avec les données
        return $this->render('commande/afficher_tout_ligne_commande.html.twig', [
            'toutLigneCommande' => $toutLigneCommande,
            'total' => $totalSum,
        ]);
    }   

// .....................Aficher  le commande status "active" pour admin tout les Commande.....................................
    #[Route('/all_commande_admin', name: 'all_commande_admin')]
    public function all_commande_admin(Commande $commande, CommandeRepository $commandeRepository): Response
    {
        // Récupérer toutes les commandes actives
         $toutLigneCommande = $commandeRepository->findBy(['statut_commande' => 'active']);
        // Initialiser la somme totale à zéro
         $totalSum = 0.0;
        // dd($toutLigneCommande);
        // Parcourir toutes les commandes actives
         foreach ($toutLigneCommande as $commande)
         {
           $premiereCommande = $toutLigneCommande[0];
           $toutLigneCommandeArray = $premiereCommande->getToutLigneCommande();        
           // Si la liste des lignes de commande n'est pas vide
           if ($toutLigneCommandeArray !== null)
            {
                // Iterate through each item in ToutLigneCommande
                foreach ($toutLigneCommandeArray as $ligneCommande) {
                    // Add the total of each line to the total sum
                    $totalSum += $ligneCommande['total'];
                }
            }
         }
        // Rendre le template avec les données
        return $this->render('commande/all_commande.html.twig', [
            'toutLigneCommande' => $toutLigneCommande,
            'total' => $totalSum,
        ]);
    }  

 // =======================afficher les Linges Commandes===============================================
    #[Route('/afficher_tout_linge_commande', name: 'afficher_tout_linge_commande', methods: ['GET', 'POST'])]
    public function afficher_tout_linge_commande(EntityManagerInterface $entityManager, CommandeRepository $commandeRepository): Response
    {
        $toutLigneCommande = $commandeRepository->findAll();
        return $this->render('commande/afficher_tout_ligne_commande.html.twig', [
            'toutLigneCommande' => $toutLigneCommande,
            ]);
    }   

// =======================entityManager pour add_tout_linge_commande ===============================================
    private EntityManagerInterface $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
 // =======================add to COMMANDE===============================================
    #[Route('/add_tout_linge_commande', name: 'add_tout_linge_commande', methods: ['GET', 'POST'])]
    public function add_tout_linge_commande(Commande $commande, LigneCommande $LigneCommande, 
    CommandeRepository $commandeRepository, EntityManagerInterface $entityManager, Request $request)
    {
        // Get the current user
        $user = $this->getUser();

        // Get all line commands
        $all_lignecommande = $this->entityManager->getRepository(LigneCommande::class)->findAll();

        // Find an active order or create a new one
        $commande = $this->entityManager->getRepository(Commande::class)->findOneBy(['statut_commande' => 'active', 'user' => $user]);

        //Checks if a command already exists. The variable $command appears to be an object representing an existing command.
        if (!$commande)
        {
            //Creates a new instance of the Command class.
            // This means that if no command was found and a new command needs to be created.
            $commande = new Commande();

            //Sets the creation date of the command to the current date and time.
            $commande->setDataCreationCommande(new DateTime());

            //Sets the status of the command to "active".This indicates that the command has just been created and is in progress.
            $commande->setStatutCommande('active');
            $commande->setUser($user);  // Associate the order with the current user
        }
        // Loop through each line command
        foreach ($all_lignecommande as $LigneCommande)
         {
            // Retrieve all line commands for the current order, or initialize an empty array if none exist
            $toutLigneCommande = $commande->getToutLigneCommande() ?? [];

            // Convert the current line command to an array representation
            $toutLigneCommande = $LigneCommande->toArray(); 

            // Add the current line command to the array of all line commands for the order
            $toutLigneCommande[] = $LigneCommande; 

            // Update the list of all line commands for the order
            $commande->setToutLigneCommande($toutLigneCommande); 

            // Check if there's enough quantity of the product in stock

            //Récupère la quantité d'un produit demandée dans une commande
            $quantiteAchat = $LigneCommande->getQuantiteAchat(); 
                    
            //Récupère l'objet produit associé à cette ligne de commande.
            $produit = $LigneCommande->getIdProduit(); 

            // Récupère la quantité disponible en stock pour ce produit.
            $quantityProduit = $produit->getQuantityProduit(); 

            //Vérifie si la quantité demandée dépasse la quantité disponible en stock
            if ($quantiteAchat > $quantityProduit) 
            {
                //Si la quantité demandée dépasse la quantité disponible en stock, récupère le nom du produit
                $nomProduit = $produit->getNomProduit(); 

                //Ajoute un message flash d'erreur indiquant qu'il n'y a pas assez de stock pour le produit.
                $this->addFlash('error', 'Il n y a pas assez de "'.$nomProduit.'" en stock pour honorer la commande.');

                //Redirige vers une route nommée 'all_lignecommande', probablement la liste de toutes les lignes de commande
                return $this->redirectToRoute('all_lignecommande');
            }
            // Update the quantity of the product in stock
            $newQuantity = $produit->getQuantityProduit() - $quantiteAchat;
            $produit->setQuantityProduit($newQuantity);

            // Persist changes to database
            $entityManager->persist($commande);
            $entityManager->persist($LigneCommande);
            $entityManager->persist($produit);
            $entityManager->remove($LigneCommande);
        }
        // Save changes to the database
        $entityManager->flush();
        // If the order is still active, notify admin that it's ready for delivery
        if ($commande->getStatutCommande() === 'active')
         {
            if (in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)) {
                $this->addFlash('success', 'La commande est maintenant en livraison.');
            }; 
        }
         // Redirect to the page showing all orders
         return $this->redirectToRoute('all_commande');
    }

// =======================suprime de  tout_linge_commande===============================================

    // Define the route for deleting a favorite product, allowing GET and POST methods
    #[Route('/produit/{idProduit}/delete_tout_linge_commande', name: 'delete_tout_linge_commande', methods: ['GET', 'POST'])]
    public function delete_tout_linge_commande(User $user, int $idProduit, UserRepository $userRepo, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Get the currently authenticated user
        $user = $this->getUser();

        // Get the list of the user's favorite products
        $favoriteProduct = $user->getFavoriteProduct();

        // Initialize a variable to track if the product is found
        $foundKey = false;

        // Iterate through the list of favorite products
        foreach ($favoriteProduct as $key => $product) 
        {
            // Check if the current product's ID matches the ID of the product to be deleted
            if ($product["idProduit"] === $idProduit) 
            {
                // Store the key where the product was found
                $foundKey = $key;
                // Commented out: Exit the loop once the product is found
            }

            // If the product was found
            if ($foundKey !== false) 
            {
                // Check if the product exists in the favorite products list
                if (array_key_exists($foundKey, $favoriteProduct)) 
                {
                    // Remove the product from the favorite products list
                    unset($favoriteProduct[$foundKey]);

                    // Update the user's favorite products with the modified list
                    $user->getFavoriteProduct($favoriteProduct);
                }

                // Set the user's favorite products to the updated list
                $user->setFavoriteProductSuprimme($favoriteProduct);

                // Persist the changes to the database
                $entityManager->persist($user);

                // Flush the changes to the database
                $entityManager->flush();

                // Uncommented: Debugging output
                // dd($favoriteProduct, $user, $key, $foundKey);
            }
        }

        // Redirect to the route that displays the user's favorite products
        return $this->redirectToRoute('afficher_to_favorites', [], Response::HTTP_SEE_OTHER);
    }

// // // .....................suprimer old commandeL.....................................
    #[Route('/delete-old-commands', name: 'delete_old_commands')]
    public function deleteOldCommands(): Response
    {
        // Абсолютный путь к файлу bin/console
         $consolePath = $this->getParameter('kernel.project_dir') . '/bin/console';

        // Выполнение команды удаления старых заказов
         $process = new Process(['php', $consolePath, 'app:delete-old-commands']);
        $process->run();

        // Проверяем наличие ошибок при выполнении команды
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        // Получаем вывод команды
        $output = $process->getOutput();

        // Возвращаем ответ
        return new Response('Old commands deleted successfully. Output: ' . $output);
    }
}
