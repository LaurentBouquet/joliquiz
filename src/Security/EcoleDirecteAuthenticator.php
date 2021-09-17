<?php

namespace App\Security;

use App\Entity\User;
use App\Entity\Group;
use App\Entity\School;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class EcoleDirecteAuthenticator extends AbstractGuardAuthenticator
{
    public const LOGIN_ROUTE = 'login';

    private $entityManager;
    private $urlGenerator;
    private $csrfTokenManager;
    private $passwordEncoder;
    private $client;
    private $logger;
    private $parameterBag;

    public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator, CsrfTokenManagerInterface $csrfTokenManager, UserPasswordEncoderInterface $passwordEncoder, HttpClientInterface $client, LoggerInterface $logger, ParameterBagInterface $parameterBag)
    {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->client = $client;
        $this->logger = $logger;
        $this->parameterBag = $parameterBag;
    }


    public function supports(Request $request)
    {
        return self::LOGIN_ROUTE === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {
        // $user_token = $request->request->get('user')['_token'];
        $credentials = [
            'username' => $request->request->get('_username'),
            'password' => $request->request->get('_password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];

        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['username']
        );

        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $username = $credentials['username'];
        $password = random_bytes(15);

        $em = $this->entityManager;
        $user = $em->getRepository(User::class)->findOneBy(['username' => $credentials['username']]);
        if (!$user) {
            $user = new User();
            $user->setUsername($username);
            $user->setPassword($this->passwordEncoder->encodePassword($user, $password));
            // $em->persist($user);
            // $em->flush();
        }
        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        $username = $credentials['username'];
        $password = $credentials['password'];

        $response = $this->client->request(
            'POST',
            'https://api.ecoledirecte.com/v3/login.awp',
            [
                'body' => 'data={
                    "identifiant": "' . $username . '",
                    "motdepasse": "' . urlencode($password) . '"
                }',
            ]
        );

        $codeOgec = "";
        //$this->logger->debug(print_r($response->getContent(), true)); // to debug
        $ecoleDirecteResponse = json_decode($response->getContent());

        $httpEcoleDirecteApiResponse = $ecoleDirecteResponse->code;
        $this->logger->info("HTTP EcoleDirecte API response for '" . $username . "' login = " . $httpEcoleDirecteApiResponse);

        $ecoleDirecteMessage = $ecoleDirecteResponse->message;
        //$ecoleDirecteToken = $ecoleDirecteResponse->token;
        $ecoleDirecteData = $ecoleDirecteResponse->data;
        if (sizeof($ecoleDirecteData->accounts) > 0) {
            $ecoleDirecteAccount = $ecoleDirecteData->accounts[0];
            $this->logger->info('codeOgec = "' . $ecoleDirecteAccount->codeOgec . '"');
            $userEmail = $ecoleDirecteAccount->email;
            $typeCompte = $ecoleDirecteAccount->typeCompte;
            $codeOgec = $ecoleDirecteAccount->codeOgec;
            $nomEtablissement = $ecoleDirecteAccount->nomEtablissement;
            $telPortable = $ecoleDirecteAccount->profile->telPortable;
            $comment = "";
            $classesCount = sizeof($ecoleDirecteAccount->profile->classes);
            for ($i = 0; $i < $classesCount; $i++) {
                $comment .= $ecoleDirecteAccount->profile->classes[$i]->libelle . ' (' . $ecoleDirecteAccount->profile->classes[$i]->code . ')' . PHP_EOL;
            }
            $comment = trim($comment);
        }

        // Did ED authenticate this user ?
        if (empty($codeOgec)) {
            // No, ED did not authenticate this user -> Is login/password valid in database ?
            $dbAuthenticationResult = $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
            if ($dbAuthenticationResult) {
                $em = $this->entityManager;
                $user->setLoginType('DB');
                $em->persist($user);
                $em->flush();
            }
            return $dbAuthenticationResult;
        } else {
            // Yes, ED did authenticate this user -> Is this OGEC authorized ?
            $ONLY_OGEC = $this->parameterBag->get('ONLY_OGEC');
            if (isset($ONLY_OGEC)) {
                $ogec_list = explode(",", $ONLY_OGEC);
                if (!in_array($codeOgec, $ogec_list)) {
                    $this->logger->info("Unauthorized access to this OGEC for '" . $username . "'");
                    throw new CustomUserMessageAuthenticationException("Unauthorized access to this OGEC");
                }
            }
        }

        switch ($httpEcoleDirecteApiResponse) {
            case 200:
                // authentication success 
                $em = $this->entityManager;
                // Save school
                $school = $em->getRepository(School::class)->findOneBy(['code' => $codeOgec]);
                if (!$school) {
                    $school = new School();
                    $school->setCode($codeOgec);
                    $school->setName($nomEtablissement);
                    $em->persist($school);
                }
                // Save group
                $currentYear = date("Y");
                $julyFifteenth = date_create($currentYear . "-07-15");
                if ($julyFifteenth->getTimestamp() < mktime()) {
                    $schoolYearName = $currentYear . '-' . strval((intval($currentYear)) + 1);
                } else {
                    $schoolYearName = strval((intval($currentYear)) - 1) . '-' . $currentYear;
                }

                $classesCount = sizeof($ecoleDirecteAccount->profile->classes);
                for ($i = 0; $i < $classesCount; $i++) {
                    $group = $em->getRepository(Group::class)->findOneBy(['code' => $codeOgec.'-'.$ecoleDirecteAccount->profile->classes[$i]->code.'-'.$schoolYearName]);
                    if (!$group) {
                        $group = new Group();
                        $group->setSchool($school);
                        $group->setCode($codeOgec.'-'.$ecoleDirecteAccount->profile->classes[$i]->code.'-'.$schoolYearName);
                        $group->setName($ecoleDirecteAccount->profile->classes[$i]->libelle.' ('.$schoolYearName.')');
                        $group->setShortname($ecoleDirecteAccount->profile->classes[$i]->code);
                    }
                    $em->persist($group);
                    $user->addGroup($group);
                }
                // Save user
                $user->setLoginType('ED');
                //$user->setToken($ecoleDirecteToken);
                $user->setEmail($userEmail);
                $user->setToReceiveMyResultByEmail(false);
                $user->setAccountType($typeCompte);
                $user->setOrganizationCode($codeOgec);
                $user->setOrganizationLabel($nomEtablissement);
                $user->setPhone($telPortable);
                $user->setComment($comment); 
                if ($typeCompte == "P") {
                    $user->addRole('ROLE_TEACHER');
                }    
                $em->persist($user);
                $em->flush();
                return true;
                break;
            case 505:
                // not valid username              
                throw new CustomUserMessageAuthenticationException($ecoleDirecteMessage);
                break;
            default:
                // fail authentication with a custom error
                throw new CustomUserMessageAuthenticationException($ecoleDirecteMessage);
                break;
        }
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        //Handles the failure of an authentication
        $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
        $data = array(
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())
        );
        return new RedirectResponse($this->urlGenerator->generate('login'));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        //redirect the user to where they wanted to go or redirect them to home by default
        $targetPath = $request->getSession()->get('_security.main.target_path');
        if (!$targetPath) {
            $targetPath = $this->urlGenerator->generate('index');
        }
        return new RedirectResponse($targetPath);
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse($this->urlGenerator->generate('login'));
    }

    public function supportsRememberMe()
    {
        return false;
    }
}
