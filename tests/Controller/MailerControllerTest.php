<?php

namespace App\Tests\Controller;

use App\Services\Mailer;
use Symfony\Component\Mime\RawMessage;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class MailerControllerTest extends WebTestCase
{

    /**
     * Test send mail 
     */
    public function testSendMail()
    {

        self::bootKernel();

        // returns the real and unchanged service container
        $container = self::$kernel->getContainer();

        // gets the special container that allows fetching private services
        $container = self::$container;

        // TODO : Mettre l'envoi du mail dans un service
        $mailer = self::$container->get(MailerInterface::class);
        $tokenGenerator = self::$container->get(TokenGeneratorInterface::class);

        $admin_email_address = $this->getParameter('ADMIN_EMAIL_ADDRESS');
        $email = (new TemplatedEmail())
            ->from($admin_email_address)
            ->to($admin_email_address)
            ->subject('Test sending mail')
            // path of the Twig template to render
            ->htmlTemplate('emails/registration.html.twig')
            // pass variables (name => value) to the template
            ->context([
                'username' => 'test',
                'useremail' => $admin_email_address,
                'token' => $tokenGenerator->generateToken(),            ]);
        $result = $mailer->send($email);

        $this->assertGreaterThan(0, $result);
    }
}
