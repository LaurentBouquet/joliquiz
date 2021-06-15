<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Services\Mailer;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mime\RawMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class SecurityController extends AbstractController
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, MailerInterface $mailer, TokenGeneratorInterface $tokenGenerator, EntityManagerInterface $em)
    {
        // 1) build the form
        $user = new User();
        $form = $this->createForm(UserType::class, $user, array('form_type' => 'register'));

        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // 3) Encode the password (you could also do this via Doctrine listener)
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $user->setToken($tokenGenerator->generateToken());

            // 4) save the User!
            //$em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            // $this->addFlash('success', sprintf('User "%s" is registred.', $user->getUsername()));

            // TODO : Mettre l'envoi du mail dans un service
            $admin_email_address = $this->getParameter('ADMIN_EMAIL_ADDRESS');
            $email = (new TemplatedEmail())
                ->from($admin_email_address)
                ->to($user->getEmail())
                ->subject('Please, confirm your email address.')
                // path of the Twig template to render
                ->htmlTemplate('emails/registration.html.twig')
                // pass variables (name => value) to the template
                ->context([
                    'username' => $user->getUsername(),
                    'useremail' => $user->getEmail(),
                    'token' => $user->getToken(),
                ]);
            $mailer->send($email);

            $this->addFlash('success', sprintf('We have sent you an email, please click on the link in it to confirm your email address "%s".', $user->getEmail()));

            return $this->redirectToRoute('login');
        }

        return $this->render(
            'security/register.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * @Route("/sendmail/{id}", name="user_sendmail")
     */
    public function sendmail(Request $request, User $user, TokenGeneratorInterface $tokenGenerator, EntityManagerInterface $em, MailerInterface $mailer)
    {
        $token = $tokenGenerator->generateToken();
        $user->setToken($token);
        $em->flush();

        // TODO : Mettre l'envoi du mail dans un service
        $admin_email_address = $this->getParameter('ADMIN_EMAIL_ADDRESS');
        $email = (new TemplatedEmail())
            ->from($admin_email_address)
            ->to($user->getEmail())
            ->subject('Please, confirm your email address.')
            // path of the Twig template to render
            ->htmlTemplate('emails/registration.html.twig')
            // pass variables (name => value) to the template
            ->context([
                'username' => $user->getUsername(),
                'useremail' => $user->getEmail(),
                'token' => $token,
            ]);
        $mailer->send($email);

        $this->addFlash('success', sprintf('A confirmation mail was sended to %s.', $user->getEmail()));

        return $this->render('user/show.html.twig', ['user' => $user]);
    }

    /**
     * @Route("/confirm", name="user_confirm")
     */
    public function confirm(Request $request, EntityManagerInterface $em)
    {
        $email = $request->query->get('email');
        $token = $request->query->get('token');

        $user = $em->getRepository(User::class)->findOneByEmail($email);

        if ($user->getToken() == $token) {
            $user->setIsActive(true);
            $user->setToken('');
            $em->flush();
            $this->addFlash('success', sprintf('Thank you! You can now log into JoliQuiz with your username : %s', $user->getUsername()));
        } else {
            $this->addFlash('error', sprintf('Your email address (%s) has not been confirmed.', $user->getEmail()));
        }
        return $this->redirectToRoute("login");
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        if ($error) {
            $this->addFlash('danger', $error->getMessageKey());
        }

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        // 1) build the form
        $user = new User();
        $user->setUsername($lastUsername);
        $form = $this->createForm(UserType::class, $user, array('form_type' => 'login'));

        return $this->render('security/login.html.twig', array(
            'form' => $form->createView(),
            'last_username' => $lastUsername,
        ));
    }

    /**
     * The road to disconnect.
     * But this one should never be executed because symfony will intercept it before.
     * @Route("/logout", name="logout")
     */
    public function logout(): void
    {
        throw new \Exception('This should never be reached!');
    }

    /**
     * @Route("/newpassword", name="newpassword")
     */
    public function requestNewPassword(Request $request, MailerInterface $mailer, TokenGeneratorInterface $tokenGenerator, EntityManagerInterface $em)
    {
        // Creation of a form "on the fly", so that the user can inform his email
        $form = $this->createFormBuilder()
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Email(),
                    new NotBlank(),
                ],
            ])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $em->getRepository(User::class)->findOneByEmail($form->getData()['email']);

            if (!$user) {
                $this->addFlash('warning', 'This email does not exist.');
            } else {
                $user->setToken($tokenGenerator->generateToken());
                $user->setPasswordRequestedAt(new \Datetime());
                $em->flush();

                $admin_email_address = $this->getParameter('ADMIN_EMAIL_ADDRESS');
                $email = (new TemplatedEmail())
                    ->from($admin_email_address)
                    ->to($user->getEmail())
                    ->subject('Renew your password')
                    // path of the Twig template to render
                    ->htmlTemplate('emails/passwordresetting.html.twig')
                    // pass variables (name => value) to the template
                    ->context([
                        'user' => $user,
                    ]);
                $mailer->send($email);

                $this->addFlash('success', 'An email will be sent to you so you can renew your password. The link you receive will be valid 24h.');

                return $this->redirectToRoute("login");
            }
        }

        return $this->render('security/passwordrequest.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /*
    @Route("/{id}/{token}", name="resetting")

    public function resetPassword(User $user, $token, Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $em)
    {
    // Forbid access to the page if:
    // the token associated with the member is null
    // the token registered in base and the token present in the url are not equal
    // the token is more than 10 minutes old
    if ($user->getToken() === null || $token !== $user->getToken() || !$this->isRequestInTime($user->getPasswordRequestedAt()))
    {
    throw new AccessDeniedHttpException();
    }

    $form = $this->createForm(PasswordResettingType::class, $user);
    $form->handleRequest($request);

    if($form->isSubmitted() && $form->isValid())
    {
    $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
    $user->setPassword($password);

    // Reset the token to null so that it is no longer reusable
    $user->setToken(null);
    $user->setPasswordRequestedAt(null);

    //$em = $this->getDoctrine()->getManager();
    $em->persist($user);
    $em->flush();

    $request->getSession()->getFlashBag()->add('success', "Votre mot de passe a été renouvelé.");

    return $this->redirectToRoute('login');

    }

    return $this->render('security/passwordreset.html.twig', [
    'form' => $form->createView()
    ]);

    }
     */
    // if greater than 10 min, return false, otherwise return true
    private function isRequestInTime(\Datetime $passwordRequestedAt = null)
    {
        if ($passwordRequestedAt === null) {
            return false;
        }

        $now = new \DateTime();
        $interval = $now->getTimestamp() - $passwordRequestedAt->getTimestamp();

        $daySeconds = 60 * 10;
        $response = $interval > $daySeconds ? false : $reponse = true;
        return $response;
    }
}
