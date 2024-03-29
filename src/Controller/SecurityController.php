<?php

namespace App\Controller;

use App\Entity\User;
<<<<<<< HEAD
use App\Form\UserType;
use App\Services\Mailer;
use Psr\Log\LoggerInterface;
=======
use App\Form\ChangePasswordFormType;
use App\Form\RegistrationFormType;
use App\Form\ResetPasswordRequestFormType;
use App\Repository\ConfigurationRepository;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
>>>>>>> develop-mysql-symf6
use Doctrine\ORM\EntityManagerInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

class SecurityController extends AbstractController
{
    use ResetPasswordControllerTrait;

    private EmailVerifier $emailVerifier;
    private ResetPasswordHelperInterface $resetPasswordHelper;

    public function __construct(EmailVerifier $emailVerifier, ResetPasswordHelperInterface $resetPasswordHelper)
    {
        $this->emailVerifier = $emailVerifier;
        $this->resetPasswordHelper = $resetPasswordHelper;
    }

    #[Route('/register', name: 'app_register')]
    function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, TranslatorInterface $translator, ConfigurationRepository $configurationRepository): Response
    {
        if ($this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('quiz_index');
        }

        // $this->addFlash('warning', "MAIN_ALLOW_USER_ACCOUNT_CREATION = " . intval($configurationRepository->getValue('MAIN_ALLOW_USER_ACCOUNT_CREATION')));
        if (intval($configurationRepository->getValue('MAIN_ALLOW_USER_ACCOUNT_CREATION')) < 1) {
            return $this->redirectToRoute('app_login');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            if (empty($user->getUsername())) {
                $prefix = substr($user->getEmail(), 0, strrpos($user->getEmail(), '@'));
                $user->setUsername($prefix);
            }

            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $from_email_address = $this->getParameter('FROM_EMAIL_ADDRESS');
            // $admin_email_address = $this->getParameter('ADMIN_EMAIL_ADDRESS');
            $this->emailVerifier->sendEmailConfirmation(
                'app_verify_email',
                $user,
                (new TemplatedEmail())
                    ->from(new Address($from_email_address, 'JoliQuiz'))
                    ->to($user->getEmail())
                    ->subject('🙂 ' . $translator->trans('I confirm my email address'))
                    // path of the Twig template to render
                    ->htmlTemplate('emails/confirmation_email.html.twig')
                    // pass variables (name => value) to the template
                    ->context([
                        'username' => $user->getName(),
                        'useremail' => $user->getEmail(),
                    ])
            );

            return $this->redirectToRoute('app_invit_verify_email', ['id' => $user->getId()]);
        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * Send a mail to user to reset their password.
     */
    #[Route('/{id}/resetpassword/send-email', name: 'app_send_resetpassword_email', methods: 'GET')]
    function resetpassword_email(User $user, MailerInterface $mailer, TranslatorInterface $translator, EntityManagerInterface $entityManager, Request $request): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);       

        $this->processSendingPasswordResetEmail(
            $user->getEmail(),
            $mailer,
            $translator,
            $entityManager
        );

        $this->addFlash('success', sprintf($translator->trans('An email has been sent to %s (%s).'), $user->getName(), $user->getEmail()));

        $route = $request->get('route');
        if (isset($route) && !empty($route)) {
            $group = $request->get('group');
            return $this->redirectToRoute($route, ['group' => $group]);
        } else {
            return $this->redirectToRoute('app_home');
        }
    }

    #[Route('/{id}/verify/send-email', name: 'app_send_verif_email', methods: 'GET')]
    function sendVerificationEmail(User $user, TranslatorInterface $translator, Request $request): Response
    {
        if ($user->isVerified()) {
            $route = $request->get('route');
            if (isset($route) && !empty($route)) {
                $group = $request->get('group');
                return $this->redirectToRoute($route, ['group' => $group]);
            } else {
                return $this->redirectToRoute('quiz_index');
            }
        }

        // generate a signed url and email it to the user
        $from_email_address = $this->getParameter('FROM_EMAIL_ADDRESS');
        // $admin_email_address = $this->getParameter('ADMIN_EMAIL_ADDRESS');
        $this->emailVerifier->sendEmailConfirmation(
            'app_verify_email',
            $user,
            (new TemplatedEmail())
                ->from(new Address($from_email_address, 'JoliQuiz'))
                ->to($user->getEmail())
                ->subject('🙂 ' . $translator->trans('I confirm my email address'))
                // path of the Twig template to render
                ->htmlTemplate('emails/confirmation_email.html.twig')
                // pass variables (name => value) to the template
                ->context([
                    'username' => $user->getName(),
                    'useremail' => $user->getEmail(),
                ])
        );

        $this->addFlash('success', sprintf($translator->trans('An email has been sent to %s (%s).'), $user->getName(), $user->getEmail()));

        $route = $request->get('route');
        if (isset($route) && !empty($route)) {
            $group = $request->get('group');
            return $this->redirectToRoute($route, ['group' => $group]);
        } else {
            return $this->redirectToRoute('app_invit_verify_email', ['id' => $user->getId(), 'resent' => true]);
        }
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    function verifyUserEmail(Request $request, TranslatorInterface $translator, UserRepository $userRepository): Response
    {
        $id = $request->get('id');

        if (null === $id) {
            return $this->redirectToRoute('app_register');
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            return $this->redirectToRoute('app_register');
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        $this->addFlash('success', $translator->trans('Your email address has been verified.'));

        return $this->redirectToRoute('quiz_index');
    }

    #[Route('/login', name: 'app_login')]
    function login(AuthenticationUtils $authenticationUtils, TranslatorInterface $translator, ConfigurationRepository $configurationRepository): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        if ($error) {
            $this->addFlash('danger', $translator->trans($error->getMessageKey()));
        }

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'MAIN_ALLOW_USER_ACCOUNT_CREATION' => intval($configurationRepository->getValue('MAIN_ALLOW_USER_ACCOUNT_CREATION')),
        ]);
    }

    #[Route('/logout2', name: 'app_logout2', methods: ['GET'])]
    function logout2(EntityManagerInterface $em)
    {
        $user = $this->getUser();
        if ($user) {
            $user->setLastQuizAccess(null);
            $em->persist($user);
            $em->flush();
        }
        return $this->redirectToRoute('app_logout');
    }

    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    function logout()
    {
        throw new \Exception('This should never be reached!');
    }

    /**
     * Display & process form to request a password reset.
     */
    #[Route('forgot_password_request', name: 'app_forgot_password_request')]
    function request(Request $request, MailerInterface $mailer, TranslatorInterface $translator, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->processSendingPasswordResetEmail(
                $form->get('email')->getData(),
                $mailer,
                $translator,
                $entityManager
            );
        }

        return $this->render('security/reset_password/request.html.twig', [
            'requestForm' => $form->createView(),
        ]);
    }

    /**
     * Confirmation page after a user has requested a password reset.
     */
    #[Route('/check-email', name: 'app_check_email')]
    function checkEmail(): Response
    {
        // Generate a fake token if the user does not exist or someone hit this page directly.
        // This prevents exposing whether or not a user was found with the given email address or not
        if (null === ($resetToken = $this->getTokenObjectFromSession())) {
            $resetToken = $this->resetPasswordHelper->generateFakeResetToken();
        }

        return $this->render('security/reset_password/check_email.html.twig', [
            'resetToken' => $resetToken,
        ]);
    }

    /**
     * Information page after a user registers.
     */
    #[Route('/{id}/invit-email', name: 'app_invit_verify_email', methods: 'GET')]
    function inviteEmail(Request $request, User $user, TranslatorInterface $translator): Response
    {

        $message = $translator->trans('Your user account has been successfully created and we thank you :-)');

        $resent = $request->get('resent');
        if ($resent) {
            $message = $translator->trans('Your request has been taken into account.');
        }

        if (null === ($resetToken = $this->getTokenObjectFromSession())) {
            $resetToken = $this->resetPasswordHelper->generateFakeResetToken();
        }

        return $this->render('security/verify_email/invit_email.html.twig', [
            'user_email' => $user->getEmail(),
            'resetToken' => $resetToken,
            'message' => $message,
        ]);
    }

    /**
     * Validates and process the reset URL that the user clicked in their email.
     */
    #[Route('/reset/{token}', name: 'app_reset_password')]
    function reset(Request $request, UserPasswordHasherInterface $passwordHasher, TranslatorInterface $translator, EntityManagerInterface $entityManager, string $token = null): Response
    {
        if ($token) {
            // We store the token in session and remove it from the URL, to avoid the URL being
            // loaded in a browser and potentially leaking the token to 3rd party JavaScript.
            $this->storeTokenInSession($token);

            return $this->redirectToRoute('app_reset_password');
        }

        $token = $this->getTokenFromSession();
        if (null === $token) {
            throw $this->createNotFoundException('No reset password token found in the URL or in the session.');
        }

        try {
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            $this->addFlash('reset_password_error', sprintf(
                '%s - %s',
                $translator->trans(ResetPasswordExceptionInterface::MESSAGE_PROBLEM_VALIDATE, [], 'ResetPasswordBundle'),
                $translator->trans($e->getReason(), [], 'ResetPasswordBundle')
            ));

            return $this->redirectToRoute('app_forgot_password_request');
        }

        // The token is valid; allow the user to change their password.
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // A password reset token should be used only once, remove it.
            $this->resetPasswordHelper->removeResetRequest($token);

            // Encode(hash) the plain password, and set it.
            $encodedPassword = $passwordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            );

            $user->setPassword($encodedPassword);
            $entityManager->flush();

            // The session is cleaned up after the password has been changed.
            $this->cleanSessionAfterReset();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/reset_password/reset.html.twig', [
            'resetForm' => $form->createView(),
        ]);
    }

    function processSendingPasswordResetEmail(string $emailFormData, MailerInterface $mailer, TranslatorInterface $translator, EntityManagerInterface $entityManager): RedirectResponse
    {
        $user = $entityManager->getRepository(User::class)->findOneBy([
            'email' => $emailFormData,
        ]);

        // Do not reveal whether a user account was found or not.
        if (!$user) {
            return $this->redirectToRoute('app_check_email');
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            // If you want to tell the user why a reset email was not sent, uncomment
            // the lines below and change the redirect to 'app_forgot_password_request'.
            // Caution: This may reveal if a user is registered or not.
            //
            $this->addFlash('reset_password_error', sprintf(
                '%s - %s',
                $translator->trans(ResetPasswordExceptionInterface::MESSAGE_PROBLEM_HANDLE, [], 'ResetPasswordBundle'),
                $translator->trans($e->getReason(), [], 'ResetPasswordBundle')
            ));

            return $this->redirectToRoute('app_forgot_password_request');
        }

        $from_email_address = $this->getParameter('FROM_EMAIL_ADDRESS');
        // $admin_email_address = $this->getParameter('ADMIN_EMAIL_ADDRESS');
        $email = (new TemplatedEmail())
            ->from(new Address($from_email_address, 'JoliQuiz'))
            // ->bcc($admin_email_address)
            ->to($user->getEmail())
            ->subject('🙂 ' . $translator->trans('Your password reset request'))
            ->htmlTemplate('emails/passwordresetting.html.twig')
            ->context([
                'resetToken' => $resetToken,
            ]);

        try {
            $mailer->send($email);
        } catch (\Throwable $th) {
            $this->addFlash('error', $th->getMessage());
        }

        // Store the token object in session for retrieval in check-email route.
        $this->setTokenObjectInSession($resetToken);

        return $this->redirectToRoute('app_check_email');
    }
}
