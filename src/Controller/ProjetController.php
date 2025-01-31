<?php

namespace App\Controller;

use App\Entity\Projet;
use App\Form\ProjetType;
use App\Repository\ProjetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserRepository;
use App\Form\SearchType;
use App\Classes\Search;


#[Route('/projet')]
final class ProjetController extends AbstractController
{
    // #[Route(name: 'app_projet_index', methods: ['GET'])]
    // public function index(ProjetRepository $projetRepository): Response
    // {
    //     return $this->render('projet/indexProjet.html.twig', [
    //         'projets' => $projetRepository->findAll(),
    //     ]);
    // }

    // route pour ne montrer que les projets de l'utilisateur connecté

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
        $projet->markAsTerminated();

        // Sauvegarder dans la base de données
        $entityManager->persist($projet);
        $entityManager->flush();

        // Ajouter un message flash
        $this->addFlash('success', 'Le projet a été marqué comme terminé.');

        return $this->redirectToRoute('app_projet_show', ['id' => $projet->getId()]);
    }

    // Route pour affecter des utilisateurs à un projet

    #[Route('/affecter/{id}', name: 'app_projet_affecter', methods: ['GET'])]
    public function affecter(Request $request, UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        $search = new Search();
        $form = $this->createForm(SearchType::class, $search);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $users = $userRepository->findByService($search);
        }

        return $this->render('projet/affecterProjet.html.twig', [
            'users' => $users,
            'f' => $form->createView()
        ]);
    }


}
