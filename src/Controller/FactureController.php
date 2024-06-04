<?php

namespace App\Controller;

use App\Entity\Facture;
use App\Repository\FactureRepository;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Entity\Produit;
use App\Repository\ProduitRepository;
use App\Entity\Commande;
use App\Repository\CommandeRepository;
use App\Entity\Entreprise;
use App\Repository\EntrepriseRepository;
use App\Form\FactureType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use DateTime;
use Symfony\Component\Security\Core\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Dompdf\Dompdf;

class FactureController extends AbstractController
{
    #[Route('/facture', name: 'app_facture')]
    public function index(): Response
    {
        return $this->render('facture/index.html.twig', [
            'controller_name' => 'FactureController',
        ]);
    }

// .....................Aficher tout les facture.....................................
    #[Route('/all_facture', name: 'all_facture')]
    public function all_facture(FactureRepository $FactureRepository): Response
    {
        $user = $this->getUser(); // Récupérer l'utilisateur actuel
        if (!$user) 
        {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }
        $facture = $FactureRepository->findBy(['id' => $user]);
        return $this->render('facture/all_facture.html.twig', [
            'facture' => $FactureRepository->findAll()
        ]);
    }
// .....................Aficher tout les facture.....................................
    #[Route('/all_facture_par_user', name: 'all_facture_par_user')]
    public function all_facture_par_user(FactureRepository $FactureRepository): Response
    {
        $user = $this->getUser(); // Récupérer l'utilisateur actuel
        if (!$user)
        {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }
        $facture = $FactureRepository->findBy(['id' => $user]);
        return $this->render('facture/all_facture.html.twig', [
            'facture' => $facture, ]);
    }
// .....................Aficher 1 facture.....................................
    #[Route('/facture/{idFacture}/affiche_facture', name: 'affiche_facture', methods: ['GET'])]
    public function affiche_facture(int $idFacture, FactureRepository $factureRepository): Response
    {
        $facture = $factureRepository->find($idFacture);
        $totalSum = $factureRepository->find($idFacture);
        if (!$facture)
        {
            throw $this->createNotFoundException('Facture non trouvée');
        }
        return $this->render('facture/add_facture.html.twig', [
            'facture' => $facture,
            'totalSum' => $totalSum,   
        ]);
    }

// .....................ajoute le facture retourne.....................................
    #[Route('/add_facture_retourne', name: 'add_facture_retourne')]
    public function add_facture_retourne(EntityManagerInterface $entityManager, Request $request): Response
    {
        $facture = new Facture();
        $form = $this->createForm(FactureType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {
            $factureData = $form->getData();
            $entityManager->persist($factureData);
            $entityManager->flush();
            return $this->redirectToRoute('all_facture', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('facture/add_facture.html.twig', [
            'facture' => $facture,
            'form' => $form->createView(),

        ]);
    }
// =======================entityManager pour add_tout_linge_commande ===============================================
    private EntityManagerInterface $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
// .....................ajoute le facture .....................................
    #[Route('/add_facture', name: 'add_facture', methods: ['GET', 'POST'])]
    public function add_facture(EntityManagerInterface $entityManager, CommandeRepository $commandeRepository, Commande  $toutLigneCommande, Entreprise $Entreprise, Request $request, MailerInterface $mailer): Response
    {
        $commande = $commandeRepository->findOneBy(['statut_commande' => 'active']);
        $existingFacture = $entityManager->getRepository(Facture::class)->findOneBy(['idCommande' => $commande]);
        if ($existingFacture !== null) 
        {
            return $this->redirectToRoute('all_facture', ['id' => $existingFacture->getIdFacture()]);
        }
        $totalSum = 0.0;
        $toutLigneCommandeArray = $commande->getToutLigneCommande();
        foreach ($toutLigneCommandeArray as $ligneCommande) 
         {
            $totalSum += $ligneCommande['total'];
         }
        $commande->setStatutCommande('livrason');
        $facture = new Facture();
        $facture->setDate(new DateTime());
        $user = $this->getUser();
        $Entreprise = $this->entityManager->getRepository(Entreprise::class)->findOneBy(['nom_entreprise' => 'T&G']);
        $facture->setIdCommande($commande);
        $facture->setIdEntreprise($Entreprise);
        $facture->setUser($user);
        $facture->setSomme($totalSum);
        $entityManager->persist($facture);
        $entityManager->flush();
        // Генерация PDF
        $pdfContent = $this->renderView('facture/add_facture.html.twig', [
            'facture' => $facture,
            'totalSum' => $totalSum,
        ]);
        $dompdf = new Dompdf();
        $dompdf->loadHtml($pdfContent);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $pdf = $dompdf->output();
        // Отправка PDF по электронной почте
        $email = (new Email())
            ->from('tatyana15022011@gmail.com')
            ->to($user->getEmail())
            ->subject('Ваша фактура')
            ->text('Пожалуйста, найдите прикрепленный файл в формате PDF')
            ->attach($pdf, 'facture.pdf', 'application/pdf');
        try 
        {
            $mailer->send($email);
            $this->addFlash('success', 'Email sent successfully');
        } 
        catch (\Throwable $e)
        {
            $this->addFlash('error', 'Failed to send email: ' . $e->getMessage());
        }
        return $this->render('facture/add_facture.html.twig', [
            'facture' => $facture,  
            'totalSum' => $totalSum,   
        ]);
    }
        
// =======================suprime de produit===============================================
    #[Route('/admin/facture/{idFacture}/delete_facture', name: 'delete_facture')]
    public function delete_facture(Facture $facture, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($facture);
        $entityManager->flush();
        return $this->redirectToRoute('all_facture');

    }
}
