<?php

namespace App\Controller;

use App\Entity\PrivacyPolicy;
use App\Form\PrivacyPolicyType;
use App\Repository\PrivacyPolicyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/privacy/policy')]
final class PrivacyPolicyController extends AbstractController
{
    #[Route(name: 'app_privacy_policy_index', methods: ['GET'])]
    public function index(PrivacyPolicyRepository $privacyPolicyRepository): Response
    {
        return $this->render('privacy_policy/index.html.twig', [
            'privacy_policies' => $privacyPolicyRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_privacy_policy_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $privacyPolicy = new PrivacyPolicy();
        $form = $this->createForm(PrivacyPolicyType::class, $privacyPolicy);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($privacyPolicy);
            $entityManager->flush();

            return $this->redirectToRoute('app_privacy_policy_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('privacy_policy/new.html.twig', [
            'privacy_policy' => $privacyPolicy,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_privacy_policy_show', methods: ['GET'])]
    public function show(PrivacyPolicy $privacyPolicy): Response
    {
        return $this->render('privacy_policy/show.html.twig', [
            'privacy_policy' => $privacyPolicy,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_privacy_policy_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PrivacyPolicy $privacyPolicy, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PrivacyPolicyType::class, $privacyPolicy);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_privacy_policy_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('privacy_policy/edit.html.twig', [
            'privacy_policy' => $privacyPolicy,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_privacy_policy_delete', methods: ['POST'])]
    public function delete(Request $request, PrivacyPolicy $privacyPolicy, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$privacyPolicy->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($privacyPolicy);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_privacy_policy_index', [], Response::HTTP_SEE_OTHER);
    }
}
