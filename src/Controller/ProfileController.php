<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ProfileController extends AbstractController
{
    #[Route('/my-profile', name: 'app_my_profile')]
    public function index( Security $security): Response
    {
        $user = $security->getUser();

        return $this->render('profile/index.html.twig', [
            'user' => $user,
        ]);
    }
}
