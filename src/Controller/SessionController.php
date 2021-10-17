<?php

namespace App\Controller;

use App\Entity\Session;
use App\Form\SessionType;
use App\Repository\QuizRepository;
use App\Repository\SessionRepository;
use App\Repository\WorkoutRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/session")
 */
class SessionController extends AbstractController
{
    /**
     * @Route("/clean", name="session_clean", methods={"GET"})
     */
    public function clean(SessionRepository $sessionRepository, QuizRepository $quizRepository, WorkoutRepository $workoutRepository, Request $request): Response
    {
        $quiz_id = $request->query->get('id');
        $quiz = $quizRepository->find($quiz_id);

        $sessionRepository->cleanByQuizId($quiz_id);

        return $this->render('session/index.html.twig', [
            'sessions' => $sessionRepository->findByQuizId($quiz_id),
            'quiz' => $quiz,           
        ]);
    }
    
    /**
     * @Route("/quiz", name="session_quiz", methods={"GET"})
     */
    public function quiz(SessionRepository $sessionRepository, QuizRepository $quizRepository, WorkoutRepository $workoutRepository, Request $request): Response
    {
        $quiz_id = $request->query->get('id');
        $quiz = $quizRepository->find($quiz_id);

        return $this->render('session/index.html.twig', [
            'sessions' => $sessionRepository->findByQuizId($quiz_id),
            'quiz' => $quiz,           
        ]);
    }
    
    /**
     * @Route("/", name="session_index", methods={"GET"})
     */
    public function index(SessionRepository $sessionRepository): Response
    {
        return $this->render('session/index.html.twig', [
            'sessions' => $sessionRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{id}", name="session_show", methods={"GET"})
     */
    public function show(Session $session): Response
    {
        return $this->render('session/show.html.twig', [
            'session' => $session,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="session_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Session $session): Response
    {
        $form = $this->createForm(SessionType::class, $session);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('session_index');
        }

        return $this->render('session/edit.html.twig', [
            'session' => $session,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="session_delete", methods="DELETE")
     */
    public function delete(Request $request, Session $session, EntityManagerInterface $em, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('ROLE_TEACHER', null, 'Access not allowed');

        $quizId = $session->getQuiz()->getId();
        if ($this->isCsrfTokenValid('delete'.$session->getId(), $request->request->get('_token'))) {
            $em->remove($session);
            $em->flush();

            $this->addFlash('success', sprintf($translator->trans('Session started at %s is deleted.'), $session->getStartedAt()->format("d/m/Y h:m")));
        }

        return $this->redirectToRoute('session_quiz', ['id'=> $quizId]);
    }

}
