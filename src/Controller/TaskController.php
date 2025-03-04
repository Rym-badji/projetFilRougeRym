<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProjetRepository;
use Symfony\Bundle\SecurityBundle\Security;

#[Route('/task')]
final class TaskController extends AbstractController
{

    #[Route('/', name: 'app_task_index', methods: ['GET'])]
    public function index(TaskRepository $taskRepository, Security $security): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $security->getUser();
        
        if (!$user) {
            throw $this->createAccessDeniedException("Vous devez être connecté pour voir vos tâches.");
        }

        // Récupérer uniquement les tâches de l'utilisateur
        $tasks = $taskRepository->findBy(['user' => $user]);

        return $this->render('task/indexTask.html.twig', [
            'tasks' => $tasks,
        ]);
    }

    #[Route('/new/{id}', name: 'app_task_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ProjetRepository $projetRepository, Security $security, int $id): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $security->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException("Vous devez être connecté pour créer une tâche.");
        }

        // Récupérer le projet via son ID
        $projet = $projetRepository->find($id);
        if (!$projet) {
            throw $this->createNotFoundException("Le projet n'existe pas.");
        }

        // Créer une nouvelle tâche et associer le projet + l'utilisateur
        $task = new Task();
        $task->setProjet($projet);
        $task->setUser($user);

        // Créer le formulaire pour la tâche
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($task);
            $entityManager->flush();

            // Rediriger vers la page du projet
            return $this->redirectToRoute('app_projet_show', ['id' => $projet->getId()]);
        }

        return $this->render('task/newTask.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_task_show', methods: ['GET'])]
    public function show(Task $task): Response
    {

        // Si la tâche n'est pas trouvée, afficher une erreur
        if (!$task) {
            throw $this->createNotFoundException("La tâche n'existe pas.");
        }

        // Vérifiez si l'utilisateur connecté a accès à la tâche
        $user = $this->getUser();
        $projet = $task->getProjet();

        if (!$projet->getUser()->contains($user)) {
            throw $this->createAccessDeniedException("Vous n'avez pas accès à cette tâche.");
        }
        return $this->render('task/showTask.html.twig', [
            'task' => $task,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_task_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Task $task, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('task/editTask.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_task_delete', methods: ['POST'])]
    public function delete(Request $request, Task $task, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$task->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($task);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
    }

    // route pour status

    #[Route('/task/mark-as-done/{id}', name: 'app_task_mark_as_done', methods: ['POST'])]
    public function markAsDone(Request $request, Task $task, EntityManagerInterface $entityManager): Response
    {
        // Vérifiez le token CSRF
        if (!$this->isCsrfTokenValid('mark_as_done' . $task->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Action non autorisée.');
        }

        // Marquez la tâche comme "Terminé"
        if ($task->getStatus() !== 'Terminé') {
            $task->markAsTerminated();
            
            // Enregistre la date actuelle si elle n'est pas déjà définie
            if ($task->getRealEndDate() === null) {
                $task->setRealEndDate(new \DateTime());
            }

            // Sauvegarder dans la base de données
            $entityManager->persist($task);
            $entityManager->flush();

            // Ajouter un message flash
            $this->addFlash('success', 'La tâche a été marqué comme terminé.');
        }
        return $this->redirectToRoute('app_task_show', ['id' => $task->getId()]);
    }
}
