<?php

namespace App\Tests\Controller;

use App\Services\Mailer;
use Symfony\Component\BrowserKit\Cookie;
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

        $mailer = self::$container->get(Mailer::class);

        $email = 'calagan.dev@gmail.com'; 
        $bodyMail = $mailer->createBodyMail('emails/registration.html.twig', [
            'username' => 'test', 
            'email' => $email,
        ]);
        $result = $mailer->sendMessage($email, 'Test sending mail', $bodyMail);
        
        $this->assertGreaterThan(0, $result);

    }


}
