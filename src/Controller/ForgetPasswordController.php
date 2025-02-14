<?php

// namespace App\Controller;

// use App\Classes\Mail;
// use App\Form\ForgetPasswordType;
// use App\Form\ResetPasswordType;
// use App\Repository\UserRepository;
// use DateTime;
// use Doctrine\ORM\EntityManagerInterface;
// use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// use Symfony\Component\HttpFoundation\Request;
// use Symfony\Component\HttpFoundation\Response;
// use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
// use Symfony\Component\Routing\Attribute\Route;
// use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
// use Symfony\Component\Mailer\MailerInterface;

// final class ForgetPasswordController extends AbstractController
// {
//     private $em;
//     public function __construct(EntityManagerInterface $em)
//     {
//         $this->em = $em;
//     }

//     #[Route('/password/forget', name: 'app_forget_password')]
//     public function forget(Request $request, UserRepository $userRepository): Response
//     {
//         // formulaire
//         $form = $this->createForm(ForgetPasswordType::class);
//         $form->handleRequest($request);

//         // Traitement du formulaire
//         if($form->isSubmitted() && $form->isValid()){
//             // Si email existe
//             $email = $form->get('email')->getData();
//             $user = $userRepository->findOneByEmail($email);
//             // dd($user);
//             $this->addFlash('success', 'Si votre adresse email existe, vous recevrez un email de réinitialisation du mot de passe');
//             if($user){
//                 // Créer un token de vérification
//                 $randomBytes = random_bytes(15);
//                 $token = bin2hex($randomBytes);
//                 $user->setToken($token);
//                 // expiredAt
//                 $date = new DateTime();
//                 $date->modify('+30 minutes');
//                 $user->setTokenExpiredAt($date);

//                 // dd($user);

//                 $this->em->flush();

//                 // générer l'url qui sera envoyé dans l'email
//                 $url = $this->generateUrl(
//                     'app_reset_password', 
//                     ['token' => $token],
//                     UrlGeneratorInterface::ABSOLUTE_URL 
//                 );
//                 // Envoyer un email de réinitialisation
//                 $email = new Mail();
//                 $name = $user->getFirstName().' '.$user->getLastName();
//                 $subject = "Réinitialisation de votre mot de passe";
//                 $content = $this->renderView(
//                     'password/forgetPassword.html.twig',
//                     [
//                         'link' => $url,
//                         'team' => 'Projects solutions'
//                     ]
//                 );    
//                 $vars = [
//                     'link' => $url,
//                     'team' => 'Projects solutions'
//                 ];
//                 $email->send($user->getEmail(), $name, $subject, $content, $vars);

//             }
//             // Reset du password

//             // Sinon : email inexistant
//             return $this->redirectToRoute('app_forget_password', [], Response::HTTP_SEE_OTHER);
//         }
//         return $this->render('password/forgetPassword.html.twig', [
//             'forgetPassword' => $form->createView(),
//         ]);
//     }

//     #[Route('/password/reset/{token}', name: 'app_reset_password')]
//     public function reset($token, Request $request, UserRepository $userRepository, UserPasswordHasherInterface $uphi): Response
//     {
//         // Vérification de sécurité
//         if(!$token){
//             return $this->redirectToRoute('app_forget_password');
//         }
//         $user = $userRepository->findOneByToken($token);
//         // Vérifier la date d'expiration
//         $now = new DateTime();
//         if(!$user || $now > $user->getTokenExpiredAt()){
//             return $this->redirectToRoute('app_forget_password');
//         }
//         // die('On peut renouveler le mot de passe');

//         // Formulaire
//         $form = $this->createForm(ResetPasswordType::class, $user);
//         $form->handleRequest($request);
//         // Traitement du formulaire
//         if($form->isSubmitted() && $form->isValid()){ 
//             $user = $form->getData();
//             $mdp = $user->getPassword();
//             $mdp = $uphi->hashPassword($user, $mdp);
//             // remettre le mdp dans l'objet user
//             $user->setPassword($mdp);
//             // dd($form->getData());
//             $user->setToken(null);
//             $user->setTokenExpiredAt(null);
//             $this->em->flush();

//             $this->addFlash(
//                 'success', 
//                 'Si votre adresse email existe, vous recevrez un email de réinitialisation du mot de passe'
//             );
//             return $this->redirectToRoute('app_login');
//         }
//         return $this->render('password/resetPassword.html.twig', [
//             'resetPassword' => $form,
//         ]);
//     }
// }








































































namespace App\Controller;

use App\Classes\Mail;
use App\Form\ForgetPasswordType;
use App\Form\ResetPasswordType;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/profil')]
class ForgetPasswordController extends AbstractController
{
    private $em;
    private $mailer;
    public function __construct(EntityManagerInterface $em, Mail $mailer)
    {
        $this->em = $em;
        $this->mailer = $mailer;
    }
    
    #[Route('/password/forget', name: 'app_forget_password')]
    public function forget(Request $request, UserRepository $userRepository): Response
    {
        // Formulaire
        $form = $this->createForm(ForgetPasswordType::class);
        $form->handleRequest($request);
        // Traitement du formulaire
        if($form->isSubmitted() && $form->isValid()){      
            // Si email existe
            $email = $form->get('email')->getData();
            $user = $userRepository->findOneByEmail($email);
            // dd($user);
            $this->addFlash('success', 'Si votre adresse email existe, vous recevrez un email de réinitialisation du mot de passe');
            if($user){
                // Créer un token de vérification
                $randomBytes = random_bytes(15);
                $token = bin2hex($randomBytes);
                $user->setToken($token);
                // expiredAt
                $date = new DateTime();
                $date->modify('+30 minutes');
                $user->setTokenExpiredAt($date);
                $this->em->flush();
                // générer l'url qui sera envoyé dans l'email
                $url = $this->generateUrl(
                    'app_reset_password', 
                    ['token' => $token],
                    UrlGeneratorInterface::ABSOLUTE_URL 
                );
                // Envoyer un email de réinitialisation
                // $email = new Mail();
                $this->mailer->send(
                    $user->getEmail(),
                    $user->getFirstName().' '.$user->getLastName(),
                    $subject = "Réinitialisation de votre mot de passe",
                    $content = "password/forgetPassword.html.twig",
                    $vars = [
                        'link' => $url,
                        'team' => 'Projects solutions',
                    ],
                );
                // $email->send($user->getEmail(), $name, $subject, $content, $vars);
                dump('Email envoyé à ' . $user->getEmail());
                die();

            }
            return $this->redirectToRoute('app_forget_password', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('password/forgetPassword.html.twig', [
            'forgetPassword' => $form->createView(),
        ]);
    }

    #[Route('/password/reset/{token}', name: 'app_reset_password')]
    public function reset($token, Request $request, UserRepository $userRepository, UserPasswordHasherInterface $uphi): Response
    {
        // Vérification de sécurité
        if(!$token){
            return $this->redirectToRoute('app_forget_password');
        }
        $user = $userRepository->findOneByToken($token);
        // Vérifier la date d'expiration
        $now = new DateTime();
        if(!$user || $now > $user->getTokenExpiredAt()){
            return $this->redirectToRoute('app_forget_password');
        }
        // die('On peut renouveler le mot de passe');
        
        // Formulaire
        $form = $this->createForm(ResetPasswordType::class, $user);
        $form->handleRequest($request);
        // Traitement du formulaire
        if($form->isSubmitted() && $form->isValid()){ 
            $user = $form->getData();
            $mdp = $user->getPassword();
            $mdp = $uphi->hashPassword($user, $mdp);
            // remettre le mdp dans l'objet user
            $user->setPassword($mdp);
            // dd($form->getData());
            $user->setToken(null);
            $user->setTokenExpiredAt(null);
            $this->em->flush();
            
            $this->addFlash(
                'success', 
                'Si votre adresse email existe, vous recevrez un email de réinitialisation du mot de passe'
            );
            return $this->redirectToRoute('app_login');
        }     
        return $this->render('password/resetPassword.html.twig', [
            'resetPassword' => $form->createView(),
        ]);
    }
}