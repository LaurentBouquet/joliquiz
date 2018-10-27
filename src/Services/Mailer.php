<?php

namespace App\Services;

use Twig\Environment;

/**
 * Class Mailer
 */
class Mailer
{
    private $mailer;
    private $environment;

    public function __construct(\Swift_Mailer $mailer, Environment $environment)
    {
        $this->mailer = $mailer;
        $this->environment = $environment;
    }

    public function sendMessage($to, $subject, $body, $attachement = null, $from = 'calagan.dev@gmail.com'): int
    {
        $mail = (new \Swift_Message($subject))
            ->setFrom($from)
            ->setTo($to)
            ->setSubject('[JoliQuiz] ' . $subject)
            ->setBody($body)
            ->setReplyTo($from)
            ->setContentType('text/html');

        // //If you also want to include a plaintext version of the message
        // ->addPart(
        //     $this->renderView(
        //         'emails/registration.txt.twig',
        //         array('name' => $name)
        //     ),
        //     'text/plain'
        // )

        return $this->mailer->send($mail);
    }

    public function createBodyMail($view, array $parameters)
    {
        return $this->environment->render($view, $parameters);
    }
}
