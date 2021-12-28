<?php

namespace App\Controller;

use App\Entity\Local;
use App\Form\LocalType;
use App\Repository\LocalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Security("is_granted('IS_AUTHENTICATED_FULLY')")]
class LocalController extends AbstractController
{
    #[Route('/local', name: 'app_local')]
    public function index(LocalRepository $localRepository): Response
    {
        $locals = $localRepository->findAll();
        return $this->render('local/index.html.twig', [
            'locals' => $locals,
        ]);
    }

    #[Route('/local/{id}/show', name: 'app_local_show')]
    public function local(Local $local, Request $request): Response
    {

        for ($cpt=6; $cpt > 0; $cpt--) {
            $arrayMonth[] = [date("Y-m",strtotime("-".$cpt." month")), false];
        }
        $arrayMonth[] = [date("Y-m"), true];
        $arrayMonth[] = [date("Y-m",strtotime("+1 month")), false];

        return $this->render('local/show.html.twig', [
            'months' => $arrayMonth,
            'local' => $local,
        ]);
    }

    #[Route('/local/{id}/update', name: 'app_local_update')]
    public function update(Local $local, Request $request, EntityManagerInterface $entityManager): Response
    {
        $localForm = $this->createForm(LocalType::class, $local);

        $localForm->handleRequest($request);
        if ($localForm->isSubmitted() && $localForm->isValid()) {
            $local->setOwner($this->getUser());
            $entityManager->persist($local);
            $entityManager->flush();

            $this->addFlash('alert-success', 'Votre local a bien été modifié.');
            return $this->redirectToRoute('app_local');
        }


        return $this->render('local/new.html.twig', [
            'localForm' => $localForm->createView()
        ]);
    }

    #[Route('/local/new', name: 'app_local_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $local = new Local();
        $localForm = $this->createForm(LocalType::class, $local);

        $localForm->handleRequest($request);
        if ($localForm->isSubmitted() && $localForm->isValid()) {
            $local->setOwner($this->getUser());
            $entityManager->persist($local);
            $entityManager->flush();

            $this->addFlash('alert-success', 'Votre local a bien été ajouté.');
            return $this->redirectToRoute('app_local');
        }


        return $this->render('local/new.html.twig', [
            'localForm' => $localForm->createView()
        ]);
    }

    #[Route('/local/{id}/delete', name: 'app_local_delete')]
    public function delete(Local $local, EntityManagerInterface $entityManager): Response
    {
        if(is_null($local)) {
            return $this->redirectToRoute('app_local');
        }


        $entityManager->remove($local);
        $entityManager->flush();
        $this->addFlash('alert-success', 'Votre local a bien été supprimé.');


        return $this->redirectToRoute('app_local');
    }

    #[Route('/local/{id}/send_email/{date}', name: 'app_local_send_email')]
    public function sendReceipt(Local $local, string $date): Response
    {
//        dd($local, $date);
        // TODO send receipt
        return $this->redirectToRoute('app_local_show', ['id' => $local->getId()]);
    }
}
