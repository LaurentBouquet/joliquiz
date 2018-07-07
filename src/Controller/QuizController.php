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

        // // vérifier qu'il n'ait pas de triche
        // $workoutRepository = $em->getRepository(Workout::Class);
        // $workoutInDatabase = $workoutRepository->findLastNotCompletedByStudent($user);
        // if ($workout != $workoutInDatabase) {
        //     throw $this->createNotFoundException();
        // }

        // Mettre à jour date-heure de fin et n° de la dernière question
        $questionNumber = $workout->getNumberOfQuestions() + 1;
        $workout->setEndedAt(new \DateTime());
        $workout->setNumberOfQuestions($questionNumber);
        $em->persist($workout);


        // Relire (dans la BD) la question posée
        $questionHistoryRepository = $em->getRepository(QuestionHistory::Class);
        $questionRepository = $em->getRepository(Question::Class);
        $lastQuestionHistory = $questionHistoryRepository->findLastByWorkout($workout);
        if ($lastQuestionHistory) {
            $lastQuestion = $questionRepository->findOneById($lastQuestionHistory->getQuestionId());
            $form = $this->createForm(QuestionType::class, $lastQuestion, array('form_type'=>'student'));
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                dump($lastQuestion->getText());
                foreach ($lastQuestion->getAnswers() as $key => $lastAnswer) {
                    dump($lastAnswer->getText());
                    dump($lastAnswer->getWorkoutCorrectGiven());
                    // Mémoriser l'historique des réponses
                    $newAnswerHistory = new AnswerHistory();
                    $newAnswerHistory->setQuestionHistory($lastQuestionHistory);
                    $newAnswerHistory->setDateTime(new \DateTimeImmutable());
                    $newAnswerHistory->setAnswerId($lastAnswer->getId());
                    $newAnswerHistory->setAnswerText($lastAnswer->getText());
                    $newAnswerHistory->setAnswerCorrect($lastAnswer->getCorrect());
                    $newAnswerHistory->setCorrectGiven($lastAnswer->getWorkoutCorrectGiven());
                    $newAnswerHistory->setAnswerSucces($lastAnswer->getWorkoutCorrectGiven() == $lastAnswer->getCorrect());
                    $em->persist($newAnswerHistory);

                }
            }
        }













        // Tirer une question au hazard
        $quiz = $workout->getQuiz();
        $question = $questionRepository->findOneByRandomCategories($quiz->getCategories());
        // Mémoriser l'historique de la nouvelle question posée
        $newQuestionHistory = new QuestionHistory();
        $newQuestionHistory->setWorkout($workout);
        $newQuestionHistory->setDateTime(new \DateTimeImmutable());
        $newQuestionHistory->setQuestionId($question->getId());
        $newQuestionHistory->setQuestionText($question->getText());
        $newQuestionHistory->setCompleted(false);
        $em->persist($newQuestionHistory);
        $em->flush();
        // dump($newQuestionHistory);
        dump($question->getText());
        foreach ($question->getAnswers() as $key => $answer) {
            dump($answer->getText());
            dump($answer->getWorkoutCorrectGiven());
        }
        //////////////


        $form = $this->createForm(QuestionType::class, $question, array('form_type'=>'student'));

        return $this->render('quiz/workout.html.twig',
            [
                'id' => $workout->getId(),
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
    public function start(Request $request, Quiz $quiz, EntityManagerInterface $em, UserInterface $user = null): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'Access not allowed');

        $workoutRepository = $em->getRepository(Workout::Class);
        $workout = new Workout();
        $workout->setStudent($user);
        $workout->setQuiz($quiz);
        $workout->setStartedAt(new \DateTime());
        $workout->setEndedAt(new \DateTime());
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
            //$em = $this->getDoctrine()->getManager();
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
