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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{

    /**
     * @Route("/profile", name="user_profile", methods="GET|POST")
     */
    public function profile(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'Access not allowed');

        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user, array('form_type' => 'profile', 'login_type' => $user->getLoginType()));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (strlen($user->getPlainPassword()) > 0) {
                // Encode the password
                $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
                $user->setPassword($password);
            }

            $em->flush();

            $this->addFlash('success', sprintf('User "%s" is updated.', $user->getUsername()));

            return $this->redirectToRoute('user_show', ['id' => $user->getId()]);
        }

        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/", name="user_index", methods="GET")
     */
    public function index(UserRepository $userRepository, GroupRepository $groupRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_TEACHER', null, 'Access not allowed');

        if ($this->isGranted('ROLE_ADMIN')) {
            $users = $userRepository->findAll($this->isGranted('ROLE_TEACHER'), $this->isGranted('ROLE_ADMIN'));
        } else {
            $groups = $this->getUser()->getGroups();
            $users = $userRepository->findAllByGroups($groups);   
        }

        return $this->render('user/index.html.twig', ['users' => $users]);
    }

    /**
     * @Route("/new", name="user_new", methods="GET|POST")
     */
    public function new(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN', null, 'Access not allowed');

        $user = new User();
        $form = $this->createForm(UserType::class, $user, array('form_type' => 'new'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //$em = $this->getDoctrine()->getManager();
            // Encode the password
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

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

    /**
     * @Route("/{id}/edit", name="user_edit", methods="GET|POST")
     */
    public function edit(Request $request, User $user, EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN', null, 'Access not allowed');

        $form = $this->createForm(UserType::class, $user, array('form_type' => 'update'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (strlen($user->getPlainPassword()) > 0) {
                // Encode the password
                $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
                $user->setPassword($password);
            }

            $em->flush();

            $this->addFlash('success', sprintf('User "%s" is updated.', $user->getUsername()));

            return $this->redirectToRoute('user_edit', ['id' => $user->getId()]);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods="DELETE")
     */
    public function delete(Request $request, EntityManagerInterface $em, User $user): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN', null, 'Access not allowed');

        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            //$em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();

            $this->addFlash('success', sprintf('User "%s" is deleted.', $user->getUsername()));
        }

        return $this->redirectToRoute('user_index');
    }
}
