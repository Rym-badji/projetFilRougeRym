<?php
    namespace App\Classes;

use Mailjet\Client;
use Mailjet\Resources;
use Twig\Environment;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;


    class Mail
    {
        // essaie 1
        private $mj;
        private $twig;
        private $mailer;

        public function __construct(Environment $twig)
        {
            $this->mj = new Client($_ENV["MJ_APIKEY_PUBLIC"], $_ENV["MJ_APIKEY_PRIVATE"], true, ['version' => 'v3.1']);
            $this->twig = $twig;
        }
        // fin essaie 1
        // public function send($to_email, $to_name, $subject, $content)
        public function send($to_email, $to_name, $subject, $template, $vars)
        {
            // Vérifier si le fichier template existe
            // $templatePath = dirname(__DIR__) . '/mail/' . $template;
            // if (!file_exists($templatePath)) {
            //     throw new \Exception("Le fichier template n'existe pas: " . $templatePath);
            // }
            // fin vérification
            // Récupérer le chemin en cours
            // dd(dirname(__DIR__).'/mail/welcome.html');
            // Récupérer le contenu du fichier html
            // $content = file_get_contents(dirname(__DIR__).'/mail/'.$template);
            // $content = file_get_contents($templatePath);
            // if ($vars) {
            //     foreach ($vars as $key => $var) {
            //         $content = str_replace('{' . $key . '}', $var, $content);
            //     }
            // }
            // Générer le contenu HTML avec Twig
            $content = $this->twig->render($template, $vars);
            // MailJet
            // $mj = new Client($_ENV["MJ_APIKEY_PUBLIC"],$_ENV["MJ_APIKEY_PRIVATE"], true, ['version' => 'v3.1']);

            // Define your request body

            $body = [
                'Messages' => [
                    [
                        'From' => [
                            'Email' => "rym.badji1998@gmail.com", // adresse email qui à été validé par mailjet
                            'Name' => "Rym badji"
                        ],
                        'To' => [
                            [
                                'Email' => $to_email,
                                // "managing.projects@yopmail.com",
                                'Name' => $to_name
                                // "You"
                            ]
                        ],
                        'TemplateID' => 6714207,
                        'TemplateLanguage' => true,
                        'Subject' => $subject,
                        'Variables' => [
                            "content" => $content
                        ],
                        // "My first Mailjet Email!",
                        // 'TextPart' => "Greetings from Mailjet!",
                        // 'HTMLPart' => $content
                        // "<h3>Dear passenger 1, welcome to <a href=\"https://www.mailjet.com/\">Mailjet</a>!</h3>
                        // <br />May the delivery force be with you!"
                    ]
                ]
            ];
            // All resources are located in the Resources class

            // $response = 
            // $mj->post(Resources::$Email, ['body' => $body]);

            // Read the response

            // $response->success() && var_dump($response->getData());
            // Envoi de l'email via Mailjet
            $response = $this->mj->post(Resources::$Email, ['body' => $body]);

            // Vérifier si l'envoi a réussi
            if (!$response->success()) {
                throw new \Exception("Erreur d'envoi d'email: " . json_encode($response->getData()));
            }
        }
    }