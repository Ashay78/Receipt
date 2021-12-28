<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserUpdateType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


class AccountController extends AbstractController
{
    #[Route('/account', name: 'app_account')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $formUpdateUser = $this->createForm(UserUpdateType::class, $user);
        $formUpdateUser->handleRequest($request);

        if ($formUpdateUser->isSubmitted() && $formUpdateUser->isValid()) {

            $this->addFlash('alert-success', 'Votre profil a été mis à jours');

            $entityManager->persist($user);
            $entityManager->flush();
        }


        return $this->render('account/index.html.twig', [
            'formUpdateUser' => $formUpdateUser->createView()
        ]);
    }
}
