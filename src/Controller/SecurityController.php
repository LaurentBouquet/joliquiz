<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Psr\Log\LoggerInterface;

use App\Entity\User;
use App\Form\UserType;

class SecurityController extends Controller
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        \Swift_Mailer $mailer)
    {
        // 1) build the form
        $user = new User();
        $form = $this->createForm(UserType::class, $user, array('form_type'=>'register'));

        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // 3) Encode the password (you could also do this via Doctrine listener)
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            // 4) save the User!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', sprintf('User "%s" is registred.', $user->getUsername()));

            /*
            $message = (new \Swift_Message('[JoliQuiz] Please, confirm your email address.'))
                ->setFrom('calagan.dev@gmail.com')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        // templates/emails/registration.html.twig
                        'emails/registration.html.twig',
                        array('username' => $user->getUsername(), 'email' => $user->getEmail())
                    ),
                    'text/html'
                )
                // //If you also want to include a plaintext version of the message
                // ->addPart(
                //     $this->renderView(
                //         'emails/registration.txt.twig',
                //         array('name' => $name)
                //     ),
                //     'text/plain'
                // )
            ;
            $mailer->send($message);

            $this->addFlash('success', sprintf('Please confirm your email address "%s".', $user->getEmail()));
            */

            return $this->redirectToRoute('login');
        }

        return $this->render(
            'security/register.html.twig',
            array('form' => $form->createView())
        );
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
        $form = $this->createForm(UserType::class, $user, array('form_type'=>'login'));

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
}
