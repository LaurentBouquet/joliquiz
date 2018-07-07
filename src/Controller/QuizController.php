<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Entity\Question;
use App\Entity\Workout;
use App\Entity\AnswerHistory;
use App\Entity\QuestionHistory;
use App\Form\QuizType;
use App\Form\QuestionType;
use App\Repository\QuizRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @Route("/quiz")
 */
class QuizController extends Controller
{

    /**
     * @Route("/{id}/workout", name="quiz_workout", methods="POST")
     */
    public function workout(Request $request, Workout $workout, EntityManagerInterface $em, UserInterface $user = null): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'Access not allowed');

        //////////////
        // TODO mettre ces opérations d'historique dans un service

        // Check that he does not cheat
        // $workoutRepository = $em->getRepository(Workout::Class);
        // $workoutInDatabase = $workoutRepository->findLastNotCompletedByStudent($user);
        // if ($workout != $workoutInDatabase) {
        //     throw $this->createNotFoundException();
        // }

        $questionNumber = $workout->getNumberOfQuestions();
        $quiz = $workout->getQuiz();

        // Re-read (from the database) the previous question
        $questionHistoryRepository = $em->getRepository(QuestionHistory::Class);
        $questionRepository = $em->getRepository(Question::Class);
        $lastQuestionHistory = $questionHistoryRepository->findLastByWorkout($workout);
        if ($lastQuestionHistory) {
            $currentQuestionResult = false;
            $lastQuestion = $questionRepository->findOneById($lastQuestionHistory->getQuestionId());
            $form = $this->createForm(QuestionType::class, $lastQuestion, array('form_type'=>'student_questioning'));
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                foreach ($lastQuestion->getAnswers() as $key => $lastAnswer) {
                    // Save answers history
                    $newAnswerHistory = new AnswerHistory();
                    $newAnswerHistory->setQuestionHistory($lastQuestionHistory);
                    $newAnswerHistory->setAnswerId($lastAnswer->getId());
                    $newAnswerHistory->setAnswerText($lastAnswer->getText());
                    $newAnswerHistory->setAnswerCorrect($lastAnswer->getCorrect());
                    $newAnswerHistory->setCorrectGiven($lastAnswer->getWorkoutCorrectGiven());
                    $currentAnswerResult = $lastAnswer->getWorkoutCorrectGiven() == $lastAnswer->getCorrect();
                    $newAnswerHistory->setAnswerSucces($currentAnswerResult);
                    $em->persist($newAnswerHistory);
                }
            }
            $lastQuestionHistory->setQuestionSuccess($currentQuestionResult);
            $em->persist($lastQuestionHistory);

            if (!$lastQuestionHistory->getEndedAt()) {
                $lastQuestionHistory->setEndedAt(new \DateTime());
                $lastQuestionHistory->setDuration(date_diff($lastQuestionHistory->getEndedAt(), $lastQuestionHistory->getStartedAt()));
                $em->persist($lastQuestionHistory);
                $workout->setEndedAt(new \DateTime());
                $em->persist($workout);
                $em->flush();

                if ($quiz->getShowResultQuestion()) {
                    $form = $this->createForm(QuestionType::class, $lastQuestion, array('form_type'=>'student_marking'));
                    return $this->render('quiz/workout.html.twig',
                        [
                            'id' => $workout->getId(),
                            'quiz' => $quiz,
                            'question' => $lastQuestion,
                            'questionNumber' => $questionNumber,
                            'form' => $form->createView(),
                        ]
                    );
                }
            }

        }


        // Check if enough questions for this quiz
        $questionsCount = $questionRepository->countByCategories($quiz->getCategories());
        if ($questionsCount < $quiz->getNumberOfQuestions()) {
            $this->addFlash('danger', 'Not enough questions for this quiz');
            $form = $this->createForm(QuizType::class, $quiz, array('form_type'=>'student_questioning'));
            return $this->render('quiz/end.html.twig',
                [
                    'id' => $workout->getId(),
                    'quiz' => $quiz,
                    'form' => $form->createView(),
                ]
            );
        }

        // Next question
        if ($questionNumber < $quiz->getNumberOfQuestions()) {
            $questionNumber++;
            $workout->setNumberOfQuestions($questionNumber);

            $questionHasBeenPosted = true;
            while ($questionHasBeenPosted) {
                // Draw a random question
                $nextQuestion = $questionRepository->findOneRandomByCategories($quiz->getCategories());
                // Check if this question has not already been posted
                $questionsHistory = $questionHistoryRepository->findAllByWorkout($workout);
                $questionHasBeenPosted = false;
                foreach ($questionsHistory as $questionHistory) {
                    if ($questionHistory->getQuestionId() == $nextQuestion->getId()) {
                        $questionHasBeenPosted = true;
                    }
                }
            }

            // Save the history of the new question
            $newQuestionHistory = new QuestionHistory();
            $newQuestionHistory->setWorkout($workout);
            $newQuestionHistory->setStartedAt(new \DateTime());
            $newQuestionHistory->setQuestionId($nextQuestion->getId());
            $newQuestionHistory->setQuestionText($nextQuestion->getText());
            $newQuestionHistory->setCompleted(false);
            $em->persist($newQuestionHistory);
            //////////////

            $em->flush();

            $form = $this->createForm(QuestionType::class, $nextQuestion, array('form_type'=>'student_questioning'));

            return $this->render('quiz/workout.html.twig',
                [
                    'id' => $workout->getId(),
                    'quiz' => $quiz,
                    'question' => $nextQuestion,
                    'questionNumber' => $questionNumber,
                    'form' => $form->createView(),
                ]
            );

        } else {
            // Quiz is completed then display end
            $form = $this->createForm(QuizType::class, $quiz, array('form_type'=>'student_questioning'));

            return $this->render('quiz/end.html.twig',
                [
                    'id' => $workout->getId(),
                    'quiz' => $quiz,
                    'form' => $form->createView(),
                ]
            );
        }
    }

    /**
     * @Route("/{id}/start", name="quiz_start", methods="GET")
     */
    public function start(Request $request, Quiz $quiz, EntityManagerInterface $em, UserInterface $user = null): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'Access not allowed');

        $workoutRepository = $em->getRepository(Workout::Class);
        $workout = new Workout();
        $workout->setStudent($user);
        $workout->setQuiz($quiz);
        $workout->setStartedAt(new \DateTime());
        // $workout->setEndedAt(new \DateTime());
        $workout->setNumberOfQuestions(0);
        $em->persist($workout);
        $em->flush();

        return $this->render('quiz/start.html.twig',
            [
                'id' => $workout->getId(),
                'quiz' => $quiz,
            ]
        );
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
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_TEACHER', null, 'Access not allowed');

        $quiz = new Quiz();
        $form = $this->createForm(QuizType::class, $quiz);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $questionRepository = $em->getRepository(Question::Class);

            // Check if enough questions for this quiz
            $questionsCount = $questionRepository->countByCategories($quiz->getCategories());
            if ($questionsCount < $quiz->getNumberOfQuestions()) {
                $this->addFlash('danger', 'Not enough questions ('.$questionsCount.') for this quiz');
            }
            else {
                $em->persist($quiz);
                $em->flush();
                return $this->redirectToRoute('quiz_index');
            }
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
    public function delete(Request $request, Quiz $quiz, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_TEACHER', null, 'Access not allowed');

        if ($this->isCsrfTokenValid('delete'.$quiz->getId(), $request->request->get('_token'))) {
            //$em = $this->getDoctrine()->getManager();
            $em->remove($quiz);
            $em->flush();
        }

        return $this->redirectToRoute('quiz_index');
    }
}
