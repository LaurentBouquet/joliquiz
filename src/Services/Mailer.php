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

    public function sendMessage($to, $subject, $body, $attachement = null, $from = null): int
    {
        if (!isset($from)) {
            $from = getenv('ADMIN_EMAIL_ADDRESS');
        }
        
        $mail = (new \Swift_Message($subject))
            ->setFrom($from)
            ->setTo($to)
            ->setSubject('[JoliQuiz] ' . $subject)
            ->setBody($body)
            ->setReplyTo($from)
            ->setContentType('text/html');
            
        return $this->mailer->send($mail);
    }

    public function createBodyMail($view, array $parameters)
    {
        return $this->environment->render($view, $parameters);
    }
}
