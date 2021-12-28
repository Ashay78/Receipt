<?php

namespace App\Controller;

use App\Entity\Local;
use App\Entity\Tenant;
use App\Form\TenantType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TenantController extends AbstractController
{
    #[Route('/local/{id}/tenant/new', name: 'app_local_tenant_new')]
    public function new(Local $local, Request $request, EntityManagerInterface $entityManager): Response
    {
        $tenant = new Tenant();
        $tenantForm = $this->createForm(TenantType::class, $tenant);

        $tenantForm->handleRequest($request);
        if ($tenantForm->isSubmitted() && $tenantForm->isValid()) {
            $tenant->setLocal($local);
            $entityManager->persist($tenant);
            $entityManager->flush();

            $this->addFlash('alert-success', 'Votre locataire a bien été ajouté.');
            return $this->redirectToRoute('app_local_show', ['id' => $local->getId()]);
        }

        return $this->render('tenant/new.html.twig', [
            'tenantForm' => $tenantForm->createView()
        ]);
    }

    #[Route('/tenant/{id}', name: 'app_tenant_update')]
    public function update(Tenant $tenant, EntityManagerInterface $entityManager, Request $request): Response
    {
        $tenantForm = $this->createForm(TenantType::class, $tenant);

        $tenantForm->handleRequest($request);
        if ($tenantForm->isSubmitted() && $tenantForm->isValid()) {
            $entityManager->persist($tenant);
            $entityManager->flush();

            $this->addFlash('alert-success', 'Votre locataire a bien été ajouté.');
            return $this->redirectToRoute('app_local_show', ['id' => $tenant->getLocal()->getId()]);
        }

        return $this->render('tenant/new.html.twig', [
            'tenantForm' => $tenantForm->createView()
        ]);
    }

    #[Route('/tenant/{id}/update', name: 'app_tenant_change_send_receipt')]
    public function sendReceipt(Tenant $tenant, EntityManagerInterface $entityManager): Response
    {
        $tenant->setSendReceipt( !$tenant->getSendReceipt() );

        $entityManager->persist($tenant);
        $entityManager->flush();

        return $this->redirectToRoute('app_local_show', ['id' => $tenant->getLocal()->getId()]);

    }
}
