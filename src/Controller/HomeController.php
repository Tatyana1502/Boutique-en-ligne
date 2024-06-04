<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
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
