<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends WebTestCase
{

    /**
     * Login form show username, password and submit button
     */
    public function testShowLogin()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        // asserts that login path exists and don't return an error
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // asserts that the response content contains csrf token
        $this->assertContains('<input type="hidden" id="user__token" name="user[_token]" value="', $client->getResponse()->getContent());

        // asserts that the response content contains input type="text" id="username
        $this->assertContains('<input type="text" id="username" name="_username" required="required"', $client->getResponse()->getContent());

        // asserts that the response content contains input type="text" id="password
        $this->assertContains('<input type="password" id="password" name="_password" required="required"', $client->getResponse()->getContent());
    }

    public function testSecuredSuperadmin()
    {
        $client = $this->logIn('admin', 'ROLE_ADMIN');
        $crawler = $client->request('GET', '/user/');

        $this->assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());

        $client = $this->logIn('superadmin', 'ROLE_SUPER_ADMIN');
        $crawler = $client->request('GET', '/user/');

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertSame('User index', $crawler->filter('h1')->text());
    }

    /**
     * Create the Authentication Token¶
     *
     * @return Client A Client instance
     */
    private function logIn($username='admin', $role='ROLE_ADMIN')
    {
		$client = static::createClient();
        $session = $client->getContainer()->get('session');

        $firewallName = 'main';
        $firewallContext = 'main';

        $token = new UsernamePasswordToken($username, null, $firewallName, array($role));
        $session->set('_security_'.$firewallContext, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);

        return $client;
    }

}
