<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Entity\Produit;
use App\Repository\ProduitRepository;
use App\Form\User\UserType;
use App\Form\User\UserRoleType;
use App\Form\User\UserFavoritProduitType;
use App\Form\ConnectionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use DateTime;

class UserController extends AbstractController
{
    #[Route('/app_user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
#-----------------------AjoutUser addUser.html.twig------------#
    #[Route('/adduser', name: 'user_add')]
    public function addUser(UserPasswordHasherInterface $passwordHasher,EntityManagerInterface $entityManager, Request $request):Response
    {
        // Create an instance of the User entity
        $user = new User();
        $user->setDateCreationUser(new DateTime());

        // Create form using ConnectionType form class and bind it with user entity
        $form = $this->createForm(ConnectionType::class, $user);
        $form->handleRequest($request);

        // Check if form is submitted and valid
        if ($form->isSubmitted() && $form->isValid())
        {
            // Get user data from the form
            $user = $form->getData();
            // Get preferred language from the request
            $locale = $request->getPreferredLanguage(['pt', 'fr_Latn_CH', 'en_US'] );
            // Get plain text password from user object
            $textPassword = $user->getPassword();
            // Hash the plain text password using password hasher
            $hashedPassword = $passwordHasher->hashPassword($user, $textPassword);
            // Set hashed password to the user object
            $user->setPassword($hashedPassword);
            // Set user roles
            $user->setRoles(['ROLE_USER']);
            // Persist user object to the database
            $entityManager->persist($user);
            $entityManager->flush();
        }
        // Render addUser.html.twig template with form twig
        return $this->render('user/addUser.html.twig', [
        'form' => $form->createView()
         ]);
    }

// .....................Aficher tout les USERS.....................................
    #[Route('/all_user', name: 'all_user')]
    public function all_user(UserRepository $UserRepository): Response
    {
        return $this->render('user/all_user.html.twig', [
            'user' => $UserRepository->findAll()]);
    }
// // .....................Aficher  le USER .....................................
    #[Route('/un_user', name: 'un_user')]
    public function un_user(UserRepository $UserRepository, UserRepository $favoriteProduct): Response
    {
        $user = $this->getUser();  
        $favoriteProduct = $this->getUser()->getFavoriteProduct();
        return $this->render('user/un_user.html.twig', [
            'user' => $UserRepository->find($user),
            'favoriteProduct' => $favoriteProduct,
        ]);            
    }
// =======================select user===============================================
    #[Route('/select_user', name: 'select_user', methods: ['GET', 'POST'])]
    public function select_user(Request $request, UserRepository $UserRepo, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(UserRoleType::class, null);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {
            $Selectionne = $form->get('roles')->getData();
            // Adjust query to handle multiple roles
            $queryBuilder = $UserRepo->createQueryBuilder('u');
            foreach ($Selectionne as $index => $role)
            {
                $queryBuilder->orWhere('u.roles LIKE :role' . $index)
                ->setParameter('role' . $index, '%"' . $role . '"%');
            }
            $user = $queryBuilder->getQuery()->getResult();
            return $this->render('user/all_user.html.twig',[  
               'user' =>$user,
            ]);
        }
            return $this->render('user/select_user.html.twig', [
                'form' => $form->createView(),
             ]);
    }

 // =======================suprime de user===============================================

    #[Route('/admin/user/{id}/delete_user', name: 'delete_user')]
    public function delete_user(User $user, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($user);
        $entityManager->flush();
        return $this->redirectToRoute('all_user');

    }

// =======================modifier de user===============================================
    #[Route('/edit_user', name: 'edit_user', methods: ['GET', 'POST'])]
    public function edit_user_un(Request $request, User $user, EntityManagerInterface $entityManager,
    UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->getUser(); 
        $form = $this->createForm(ConnectionType::class, $user);
         $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {
            $textPassword = $user->getPassword();
            $hashedPassword = $passwordHasher->hashPassword($user,
            $textPassword);
            $user->setPassword($hashedPassword);
            $entityManager->flush();
        }
        return $this->render('user/un_user_edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

// =======================afficher Favorites Produits===============================================
    #[Route('/afficher_to_favorites', name: 'afficher_to_favorites', methods: ['GET', 'POST'])]
    public function afficher_to_favorites(EntityManagerInterface $entityManager, UserRepository $favoriteProduct): Response
    {  
        $favoriteProduct = $this->getUser()->getFavoriteProduct();
        return $this->render('produit/afficher_aime_produit.html.twig', [
            'favoriteProduct' => $favoriteProduct,
        ]);           
    }

// =======================add to Favorites Produits===============================================
    #[Route('/produit/{idProduit}/add_to_favorites', name: 'add_to_favorites', methods: ['GET', 'POST'])]
    public function add_to_favorites(User $favoriteProduct, Produit $produit, UserRepository $userRepo, EntityManagerInterface $entityManager, Request $request)
    {
        $user = $this->getUser();
        $favoriteProduct = $produit->toArray(); 
        $favoriteProduct[] = $produit;
        $user->setFavoriteProduct($favoriteProduct);
        $entityManager->flush();
       // Rediriger vers la route précédente enregistrée dans la session
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }
            
// =======================suprime de  Favorite Produit===============================================

    #[Route('/produit/{idProduit}/delete_favoriteProduct', name: 'delete_favoriteProduct',  methods: ['GET', 'POST'])]
    public function delete_favoriteProduct(User $user, int $idProduit,  UserRepository $userRepo,Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $favoriteProduct = $user->getFavoriteProduct();
        $foundKey = false;
        foreach ($favoriteProduct as $key => $product)
        {
            if ($product["idProduit"] === $idProduit)
            {
                $foundKey = $key;
            }
            if ($foundKey !== false) 
            {
                if (array_key_exists($foundKey, $favoriteProduct))
                {
                    unset($favoriteProduct[$foundKey]);
                     $user->getFavoriteProduct($favoriteProduct);
                }  
                $user->setFavoriteProductSuprimme($favoriteProduct);
                $entityManager->persist($user);
                $entityManager->flush();
                // dd($favoriteProduct, $user,$key ,$foundKey);
            }
        } 
        return $this->redirectToRoute('afficher_to_favorites', [], Response::HTTP_SEE_OTHER);
    }
//  //  / =======================select les languages===============================================    
    public function index_fr(TranslatorInterface $translator): Response
    {
        $translated = $translator->trans('welcome');
        return new Response('<html><body>' . $translated . '</body></html>');
    }
    public function index_en(TranslatorInterface $translator): Response
    {
        $translated = $translator->trans('bounjour');
        return new Response('<html><body>' . $translated . '</body></html>');
    }
}
