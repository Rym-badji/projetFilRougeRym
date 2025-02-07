<?php

namespace App\Controller;

use App\Entity\Projet;
use App\Form\ProjetType;
use App\Repository\ProjetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserRepository;
use App\Form\SearchType;
use App\Classes\Search;



#[Route('/projet')]
final class ProjetController extends AbstractController
{

    #[Route(name: 'app_projet_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();
        // vérifier si l'utilisateur est connecté
        if (!$user) {
            throw $this->createAccessDeniedException("Vous devez être connecté pour accéder à vos projets.");
        }

        // Filtrer les projets auxquels l'utilisateur participe
        $projets = $entityManager->getRepository(Projet::class)
            ->createQueryBuilder('p')
            ->join('p.user', 'u')
            ->where('u.id = :userId')
            ->setParameter('userId', $user->getId())
            ->getQuery()
            ->getResult();

        // Passer les projets filtrés à la vue
        return $this->render('projet/indexProjet.html.twig', [
            'projets' => $projets,
        ]);
    }

    #[Route('/new', name: 'app_projet_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $projet = new Projet();
        $form = $this->createForm(ProjetType::class, $projet);
        $form->handleRequest($request);

        // seul l'utilisateur connecté peut créer un projet
        // seul l'utilisateur afilié au projet peut voir le projet
        if ($form->isSubmitted() && $form->isValid()) {
            // Ajout de l'utilisateur connecté au projet
            $projet->addUser($this->getUser());
            // persist : figer les données
            $entityManager->persist($projet);
            // exécution effective de la requête
            $entityManager->flush();

            return $this->redirectToRoute('app_projet_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('projet/newProjet.html.twig', [
            'projet' => $projet,
            'form' => $form,
        ]);
    }

    // ajout route show pour afficher les projets de l'utilisateur connecté
    #[Route('/{id}', name: 'app_projet_show', methods: ['GET'])]
    public function show(Projet $projet): Response
    {
        $user = $this->getUser();
        // Vérifiez si l'utilisateur est connecté
        if (!$user) {
            throw $this->createAccessDeniedException("Vous devez être connecté pour accéder à ce projet.");
        }
        // Vérifiez si l'utilisateur connecté participe au projet
        if (!$projet->getUser()->contains($user)) {
            throw $this->createAccessDeniedException("Vous n'avez pas accès à ce projet.");
        }
        // Vérifiez si le projet a des utilisateurs associés
        if (!$projet->getUser()) {
            throw $this->createNotFoundException("Aucun utilisateur n'est associé à ce projet.");
        }
    
        return $this->render('projet/showProjet.html.twig', [
            'projet' => $projet,
            'tasks' => $projet->getTasks(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_projet_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Projet $projet, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProjetType::class, $projet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_projet_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('projet/editProjet.html.twig', [
            'projet' => $projet,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_projet_delete', methods: ['POST'])]
    public function delete(Request $request, Projet $projet, EntityManagerInterface $entityManager): Response
    {
        // Vérifiez le token CSRF
        $submittedToken = $request->request->get('_token');
    
        if ($this->isCsrfTokenValid('delete'.$projet->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($projet);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_projet_index', [], Response::HTTP_SEE_OTHER);
    }

    // Route pour status

    #[Route('/projet/mark-as-done/{id}', name: 'app_projet_mark_as_done', methods: ['POST'])]
    public function markAsDone(Request $request, Projet $projet, EntityManagerInterface $entityManager): Response
    {
        // Vérifiez le token CSRF
        if (!$this->isCsrfTokenValid('mark_as_done' . $projet->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Action non autorisée.');
        }

        // Marquez le projet comme "Terminé"
        if ($projet->getStatus() !== 'Terminé') {
            $projet->markAsTerminated();

            // Enregistre la date actuelle si elle n'est pas déjà définie
            if ($projet->getRealEndDate() === null) {
                $projet->setRealEndDate(new \DateTime());
            }

            // Sauvegarder dans la base de données
            $entityManager->persist($projet);
            $entityManager->flush();

            // Ajouter un message flash
            $this->addFlash('success', 'Le projet a été marqué comme terminé.');
        }
        return $this->redirectToRoute('app_projet_show', ['id' => $projet->getId()]);
    }

    //  route pour affecetr un utilisateur à un projet
    #[Route('/{id}/affecter', name: 'app_projet_affecter', methods: ['GET'])]
    public function affecter(int $id, ProjetRepository $projetRepository, UserRepository $userRepository): Response
    {
        $projet = $projetRepository->find($id);

        if (!$projet) {
            throw $this->createNotFoundException("Projet non trouvé.");
        }

        // Récupérer les utilisateurs déjà affectés
        $usersAffectes = $projet->getUser();

        // Récupérer les utilisateurs non encore affectés
        $usersNonAffectes = $userRepository->createQueryBuilder('u')
            ->where('u NOT IN (:usersAffectes)')
            ->setParameter('usersAffectes', $usersAffectes)
            ->getQuery()
            ->getResult();

        return $this->render('projet/affecterProjet.html.twig', [
            'projet' => $projet,
            'usersAffectes' => $usersAffectes,
            'usersNonAffectes' => $usersNonAffectes
        ]);
    }

    #[Route('/{id}/affecter-user/{userId}', name: 'app_projet_affecter_user', methods: ['POST'])]
    public function affecterUser(int $id, int $userId, ProjetRepository $projetRepository, UserRepository $userRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $projet = $projetRepository->find($id);
        $user = $userRepository->find($userId);

        if (!$projet || !$user) {
            return new JsonResponse(['message' => 'Projet ou utilisateur non trouvé.'], Response::HTTP_NOT_FOUND);
        }

        if (!$projet->getUser()->contains($user)) {
            $projet->addUser($user);
            $entityManager->persist($projet);
            $entityManager->flush();

            return new JsonResponse(['message' => 'Utilisateur affecté avec succès !']);
        }

        return new JsonResponse(['message' => 'Utilisateur déjà affecté.'], Response::HTTP_BAD_REQUEST);
    }

    // fin route pour affecter un utilisateur à un projet
    // début ajout d'une route pour retirer un utilisateur d'un projet

    #[Route('/{id}/remove-user/{userId}', name: 'app_projet_remove_user', methods: ['POST'])]
    public function removeUserFromProject(int $id, int $userId, EntityManagerInterface $entityManager, ProjetRepository $projetRepository, UserRepository $userRepository): Response
    {
        $projet = $projetRepository->find($id);
        $user = $userRepository->find($userId);

        if (!$projet || !$user) {
            throw $this->createNotFoundException("Projet ou utilisateur introuvable.");
        }

        // Vérifier si l'utilisateur fait bien partie du projet
        if ($projet->getUser()->contains($user)) {
            $projet->removeUser($user);
            $entityManager->flush();

            // Message de confirmation
            $this->addFlash('success', "{$user->getFirstName()} {$user->getLastName()} a bien été retiré du projet.");
        } else {
            $this->addFlash('warning', "Cet utilisateur ne fait pas partie de ce projet.");
        }

        return $this->redirectToRoute('app_projet_affecter', ['id' => $projet->getId()]);
    }


}
