<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Entity\Question;
use App\Entity\Workout;
use App\Entity\QuestionHistory;
use App\Form\QuizType;
use App\Form\QuestionType;
use App\Repository\QuizRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/quiz")
 */
class QuizController extends Controller
{

    /**
     * @Route("/{id}/workout", name="quiz_workout", methods="GET")
     */
    public function workout(Request $request, Quiz $quiz, UserInterface $user = null): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'Access not allowed');

        $questionNumber = 0;

        //////////////
        // TODO mettre ces opérations d'historique dans un service
        $em = $this->getDoctrine()->getManager();

        $questionRepository = $em->getRepository(Question::Class);
        $question = $questionRepository->findOneByRandomCategories($quiz->getCategories());

        $workoutRepository = $em->getRepository(Workout::Class);
        $workout = $workoutRepository->findLastNotCompletedByStudent($user);
        if (!$workout) {
            $workout = new Workout();
            $workout->setStudent($user);
            $workout->setQuiz($quiz);
            $workout->setStartedAt(new \DateTime());
            $questionNumber = 0;
        }
        else {
            $questionNumber = $workout->getNumberOfQuestions();
        }
        $questionNumber++;
        $workout->setEndedAt(new \DateTime());
        $workout->setNumberOfQuestions($questionNumber);
        $em->persist($workout);

        $questionHistoryRepository = $em->getRepository(QuestionHistory::Class);
        $questionHistoryRepository = new QuestionHistory();
        $questionHistoryRepository->setWorkout($workout);
        $questionHistoryRepository->setDateTime(new \DateTimeImmutable());
        $questionHistoryRepository->setQuestionText($question->getText());
        $questionHistoryRepository->setCompleted(false);
        $em->persist($questionHistoryRepository);

        $form = $this->createForm(QuestionType::class, $question, array('form_type'=>'student'));
        $form->handleRequest($request);

        // if ($form->isSubmitted() && $form->isValid()) {
        // }

        $em->flush();
        //////////////

        return $this->render('quiz/workout.html.twig',
            [
                'quiz' => $quiz,
                'question' => $question,
                'questionNumber' => $questionNumber,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}/start", name="quiz_start", methods="GET")
     */
    public function start(Request $request, Quiz $quiz): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'Access not allowed');

        return $this->render('quiz/start.html.twig', ['quiz' => $quiz]);
    }

    /**
     * @Route("/", name="quiz_index", methods="GET")
     */
    public function index(QuizRepository $quizRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'Access not allowed');

        return $this->render('quiz/index.html.twig', ['quizzes' => $quizRepository->findAll()]);
    }

    /**
     * @Route("/new", name="quiz_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_TEACHER', null, 'Access not allowed');

        $quiz = new Quiz();
        $form = $this->createForm(QuizType::class, $quiz);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($quiz);
            $em->flush();

            return $this->redirectToRoute('quiz_index');
        }

        return $this->render('quiz/new.html.twig', [
            'quiz' => $quiz,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="quiz_show", methods="GET")
     */
    public function show(Quiz $quiz): Response
    {
        $this->denyAccessUnlessGranted('ROLE_TEACHER', null, 'Access not allowed');

        return $this->render('quiz/show.html.twig', ['quiz' => $quiz]);
    }

    /**
     * @Route("/{id}/edit", name="quiz_edit", methods="GET|POST")
     */
    public function edit(Request $request, Quiz $quiz): Response
    {
        $this->denyAccessUnlessGranted('ROLE_TEACHER', null, 'Access not allowed');

        $form = $this->createForm(QuizType::class, $quiz);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $quiz->setUpdatedAt(new \DateTime());

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('quiz_edit', ['id' => $quiz->getId()]);
        }

        return $this->render('quiz/edit.html.twig', [
            'quiz' => $quiz,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="quiz_delete", methods="DELETE")
     */
    public function delete(Request $request, Quiz $quiz): Response
    {
        $this->denyAccessUnlessGranted('ROLE_TEACHER', null, 'Access not allowed');

        if ($this->isCsrfTokenValid('delete'.$quiz->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($quiz);
            $em->flush();
        }

        return $this->redirectToRoute('quiz_index');
    }
}
