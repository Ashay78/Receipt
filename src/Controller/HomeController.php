<?php

namespace App\Controller;

use App\Entity\Tenant;
use App\Repository\LocalRepository;
use App\Repository\TenantRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    #[Security("is_granted('IS_AUTHENTICATED_FULLY')")]
    public function index(LocalRepository $localRepository, TenantRepository $tenantRepository): Response
    {
        $locals = $localRepository->findBy(
            ['owner' => $this->getUser()->getId()],
        );

        $tenants = [];
        $price = 0;

        foreach ($locals as $local) {
            foreach ($local->getTenants() as $tenant) {
                $tenants[] = $tenant;

            }
        }

        /** @var Tenant $tenant */
        foreach ($tenants as $tenant) {
            $price = $price + $tenant->getRental();
        }

        return $this->render('home/index.html.twig', [
            'locals' => $locals,
            'tenants' => $tenants,
            'price' => $price
        ]);
    }
}
