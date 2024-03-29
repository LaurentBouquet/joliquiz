<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Repository\GroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ResetPasswordRequestRepository;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{

    /**
     * @Route("/password", name="user_password", methods="GET|POST")
     */
    public function password(Request $request, EntityManagerInterface $em, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'Access not allowed');

        $user = $this->getUser();        
        // Convert user type from UserInterface to PasswordAuthenticatedUserInterface
        $user = $userRepository->find($user->getId());        
        $user = $userRepository->find($user->getId());        
        if ($user) {
            $user->setLastQuizAccess(null);
            $em->persist($user);
            $em->flush();
        }  
                
        $form = $this->createForm(UserType::class, $user, array('form_type' => 'password', 'login_type' => $user->getLoginType()));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (strlen($user->getPlainPassword()) > 0) {
                // Encode the password
                $password = $passwordHasher->hashPassword($user, $user->getPlainPassword());
                $user->setPassword($password);
            }

            $em->flush();

            $this->addFlash('success', sprintf($translator->trans('Password for user "%s" has been updated.'), $user->getUsername()));

            if ($this->isGranted('ROLE_ADMIN')) {
                return $this->redirectToRoute('user_show', ['id' => $user->getId()]);
            } else {
                return $this->redirectToRoute('quiz_index');
            }            
            
        }

        return $this->render('user/profilepassword.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/profile", name="user_profile", methods="GET|POST")
     */
    public function profile(Request $request, EntityManagerInterface $em, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'Access not allowed');

        $user = $this->getUser();
        // Convert user type from UserInterface to PasswordAuthenticatedUserInterface
        $user = $userRepository->find($user->getId());        
        if ($user) {
            $user->setLastQuizAccess(null);
            $em->persist($user);
            $em->flush();
        }   

        $form = $this->createForm(UserType::class, $user, array('form_type' => 'profile', 'login_type' => $user->getLoginType()));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (strlen($user->getPlainPassword()) > 0) {
                // Encode the password
                $password = $passwordHasher->hashPassword($user, $user->getPlainPassword());
                $user->setPassword($password);
            }

            $em->flush();

            $this->addFlash('success', sprintf($translator->trans('User "%s" is updated.'), $user->getUsername()));

            if ($this->isGranted('ROLE_ADMIN')) {
                return $this->redirectToRoute('user_show', ['id' => $user->getId()]);
            } else {
                return $this->redirectToRoute('quiz_index');
            }            
            
        }

        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/", name="user_index", methods="GET")
     */
    public function index(UserRepository $userRepository, GroupRepository $groupRepository, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_TEACHER', null, 'Access not allowed');

        $groupId = $request->query->get('group');        
        $groupName = "";        
        $groupShortName = "";
        if ($groupId > 0 ) {
            if ($this->isGranted('ROLE_ADMIN')) {
                $users = $userRepository->findAllByGroups([$groupId],  $this->isGranted('ROLE_TEACHER'), $this->isGranted('ROLE_ADMIN'));
                $group = $groupRepository->find($groupId);
                $groupName = $group->getName();    
                $groupShortName = $group->getShortName(); 
            } else {
                $groups = $this->getUser()->getGroups();
                $users = $userRepository->findAllByGroups($groups);   
            }
        }
        else {        
            if ($this->isGranted('ROLE_ADMIN')) {
                $users = $userRepository->findAll($this->isGranted('ROLE_TEACHER'), $this->isGranted('ROLE_ADMIN'));
            } else {
                $groups = $this->getUser()->getGroups();
                $users = $userRepository->findAllByGroups($groups);   
            }
        }

        return $this->render('user/index.html.twig', ['users' => $users, 'group_id' => $groupId, 'group_name' => $groupName, 'group_shortname' => $groupShortName]);
    }

    /**
     * @Route("/new", name="user_new", methods="GET|POST")
     */
    public function new(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN', null, 'Access not allowed');

        $user = new User();
        $form = $this->createForm(UserType::class, $user, array('form_type' => 'new'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Encode the password
            $password = $passwordHasher->hashPassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            if (empty($user->getUsername())) {
                $prefix = substr($user->getEmail(), 0, strrpos($user->getEmail(), '@'));
                $user->setUsername($prefix);
            }

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', sprintf('User "%s" is registred.', $user->getUsername()));

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods="GET|POST")
     */
    public function edit(Request $request, User $user, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN', null, 'Access not allowed');

        $form = $this->createForm(UserType::class, $user, array('form_type' => 'update'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (strlen($user->getPlainPassword()) > 0) {
                // Encode the password
                $password = $passwordHasher->hashPassword($user, $user->getPlainPassword());
                $user->setPassword($password);
            }    

            if (empty($user->getUsername())) {
                $prefix = substr($user->getEmail(), 0, strrpos($user->getEmail(), '@'));
                $user->setUsername($prefix);
            }
            
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', sprintf($translator->trans('User "%s" is updated.'), $user->getUsername()));

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods="POST")
     */
    public function delete(Request $request, EntityManagerInterface $em, User $user, ResetPasswordRequestRepository $resetPasswordRequestRepository, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN', null, 'Access not allowed');

        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {

            $resetPasswordRequests = $resetPasswordRequestRepository->findByUserId($user->getId());

            if (isset($resetPasswordRequests)) {
                foreach ($resetPasswordRequests as $resetPasswordRequest) {
                    $resetPasswordRequestRepository->remove($resetPasswordRequest);
                }
            }

            $em->remove($user);
            $em->flush();
            $this->addFlash('success', sprintf($translator->trans('User "%s" is deleted.'), $user->getUsername()));
        }

        return $this->redirectToRoute('user_index');
    }

    /**
     * @Route("/{id}", name="user_show", methods="GET")
     */
    public function show(User $user): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'Access not allowed');

        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            return $this->render('user/show.html.twig', ['user' => $user]);
        } else {
            return $this->render('user/show.html.twig', ['user' => $this->getUser()]);
        }
    }
        
}
