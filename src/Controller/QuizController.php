<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Entity\User;
use App\Form\QuizType;
use App\Entity\Workout;
use App\Entity\Question;
use App\Services\Mailer;
use App\Form\QuestionType;
use App\Entity\AnswerHistory;
use App\Entity\QuestionHistory;
use App\Repository\QuizRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/quiz")
 */
class QuizController extends AbstractController
{

    /**
     * @Route("/{id}/workout", name="quiz_workout", methods="POST")
     */
    public function workout(Request $request, Workout $workout, EntityManagerInterface $em, UserInterface $user = null, Mailer $mailer, TranslatorInterface $translator): Response
    {
        $now = new \DateTime();
        //////////////
        // TODO mettre ces opérations d'historique dans un service

        // Check that he does not cheat
        // $workoutRepository = $em->getRepository(Workout::Class);
        // $workoutInDatabase = $workoutRepository->findLastNotCompletedByStudent($user);
        // if ($workout != $workoutInDatabase) {
        //     throw $this->createNotFoundException();
        // }

        $questionNumber = $workout->getNumberOfQuestions();
        $questionResult = 0;
        $score = $workout->getScore();
        $quiz = $workout->getQuiz();

        if (!$quiz->getAllowAnonymousWorkout()) {
            $this->denyAccessUnlessGranted('ROLE_USER', null, 'Access not allowed');
        }
        
        if (!$user) {
            $user = $workout->getStudent();
        }

        if (!$quiz->getActive()) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Access not allowed');
        }
        
        // Re-read (from the database) the previous question
        $questionHistoryRepository = $em->getRepository(QuestionHistory::class);
        $questionRepository = $em->getRepository(Question::class);
        //$lastQuestionHistory = $questionHistoryRepository->findLastByWorkout($workout);
        $questionsHistory = $questionHistoryRepository->findAllByWorkout($workout);
        if ($questionsHistory) {
            $lastQuestionHistory = $questionsHistory[0];
            $currentQuestionResult = +1;

            if (!$lastQuestionHistory->getEndedAt()) {
                $lastQuestion = $questionRepository->findOneById($lastQuestionHistory->getQuestionId());

                /////////////////////////////////////////
                //Check timer
                $question_max_duration = $lastQuestion->getMaxDuration();
                if (!isset($question_max_duration)) {
                    $question_max_duration = $quiz->getDefaultQuestionMaxDuration();
                    if (isset($question_max_duration)) {
                        $question_duration_minutes = ($now->diff($lastQuestionHistory->getStartedAt()))->format('%i');
                        $question_duration = ($now->diff($lastQuestionHistory->getStartedAt()))->format('%s') + ($question_duration_minutes * 60);
                        if ($question_duration > ($question_max_duration * 1.5)) { // 50% margin
                            $comment = $translator->trans('Response time (%question_duration% s) exceeded time limit (%question_max_duration% s) by question', array(
                                '%question_duration%' => $question_duration,
                                '%question_max_duration%' => $question_max_duration,
                            ));
                            //$comment = 'Response time (' . $question_duration . ' s) exceeded time limit (' . $question_max_duration . ' s) by question';
                            $workout->setComment($comment);
                            $workout->setCompleted(true);
                            $em->persist($workout);
                            $em->flush();

                            $email = getenv('ADMIN_EMAIL_ADDRESS');
                            $bodyMail = $mailer->createBodyMail('emails/quiz_result.html.twig', [
                                'username' => $user->getUsername(),
                                'email' => $user->getEmail(),
                                'quiz' => $quiz,
                                'score' => $score,
                            ]);
                            $result = $mailer->sendMessage($email, '🎓 A quiz has just been completed by '.$user->getUsername().' (before the end) !', $bodyMail);

                            $this->addFlash('danger', $comment);
                            $form = $this->createForm(QuizType::class, $quiz, array('form_type' => 'student_questioning'));
                            return $this->render(
                                'quiz/end.html.twig',
                                [
                                    'id' => $workout->getId(),
                                    'quiz' => $quiz,
                                    'score' => 0,
                                    'questionsHistory' => $questionsHistory,
                                    'comment' => $workout->getComment(),
                                    'form' => $form->createView(),
                                ],
                            );
                        }
                    }
                }
                /////////////////////////////////////////

                /////////////////////////////////////////
                //Check answers
                $form = $this->createForm(QuestionType::class, $lastQuestion, array('form_type' => 'student_questioning'));
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
                        if (!$currentAnswerResult) {
                            $currentQuestionResult = -1;
                        }
                        $newAnswerHistory->setAnswerSucces($currentAnswerResult);
                        $em->persist($newAnswerHistory);
                    }

                    $lastQuestionHistory->setQuestionSuccess($currentQuestionResult == +1);
                    $questionResult = $currentQuestionResult;
                    $em->persist($lastQuestionHistory);

                    $lastQuestionHistory->setEndedAt($now);
                    $lastQuestionHistory->setDuration(date_diff($lastQuestionHistory->getEndedAt(), $lastQuestionHistory->getStartedAt()));
                    $em->persist($lastQuestionHistory);
                    $workout->setEndedAt($now);
                    ////////////////////////////
                    // Calc score
                    $workoutSuccess = 0;
                    foreach ($questionsHistory as $questionHistory) {
                        if ($questionHistory->getQuestionSuccess()) {
                            $workoutSuccess++;
                        }
                    }
                    $score = round(($workoutSuccess / $quiz->getNumberOfQuestions()) * 100);
                    $workout->setScore($score);
                    ////////////////////////////
                    $em->persist($workout);
                    $em->flush();

                    if ($quiz->getShowResultQuestion()) {
                        // Show question result
                        $form = $this->createForm(QuestionType::class, $lastQuestion, array('form_type' => 'student_marking'));
                        return $this->render(
                            'quiz/workout.html.twig',
                            [
                                'id' => $workout->getId(),
                                'quiz' => $quiz,
                                'question' => $lastQuestion,
                                'questionNumber' => $questionNumber,
                                'questionResult' => $questionResult,
                                'progress' => ($questionNumber / $quiz->getNumberOfQuestions()) * 100,
                                'question_max_duration' => $question_max_duration,
                                'question_duration' => $question_duration,
                                'form' => $form->createView(),
                            ]
                        );
                    }
                }
                /////////////////////////////////////////

            }
        }

        // Check if enough questions for this quiz
        $questionsCount = $questionRepository->countByCategories($quiz->getCategories());
        if ($questionsCount < $quiz->getNumberOfQuestions()) {
            $this->addFlash('danger', 'Not enough questions for this quiz');
            $form = $this->createForm(QuizType::class, $quiz, array('form_type' => 'student_questioning'));
            return $this->render(
                'quiz/end.html.twig',
                [
                    'id' => $workout->getId(),
                    'quiz' => $quiz,
                    'score' => $score,
                    'questionsHistory' => $questionsHistory,
                    'comment' => $workout->getComment(),
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
            $newQuestionHistory->setStartedAt($now);
            $newQuestionHistory->setQuestionId($nextQuestion->getId());
            $newQuestionHistory->setQuestionText($nextQuestion->getText());
            $newQuestionHistory->setCompleted(false);
            $em->persist($newQuestionHistory);
            //////////////

            $em->flush();

            //Set timer
            $question_max_duration = $nextQuestion->getMaxDuration();
            if (!isset($question_max_duration)) {
                $question_max_duration = $quiz->getDefaultQuestionMaxDuration();
            }
            $question_duration = 0;

            $form = $this->createForm(QuestionType::class, $nextQuestion, array('form_type' => 'student_questioning'));
            return $this->render(
                'quiz/workout.html.twig',
                [
                    'id' => $workout->getId(),
                    'quiz' => $quiz,
                    'question' => $nextQuestion,
                    'questionNumber' => $questionNumber,
                    'questionResult' => 0,
                    'progress' => (($questionNumber - 1) / $quiz->getNumberOfQuestions()) * 100,
                    'question_max_duration' => $question_max_duration,
                    'question_duration' => $question_duration,
                    'form' => $form->createView(),
                ]
            );
        } else {
            // Quiz is completed then display end
            $score = $workout->getScore();
            $comment = '';
            $commentLines = explode("\n", $quiz->getResultQuizComment());
            foreach ($commentLines as $commentLine) {
                list($commentInterval, $commentText) = explode(":", $commentLine);
                list($min, $max) = explode("-", $commentInterval);
                if (($score >= $min) && ($score <= $max)) {
                    $comment = $comment . $commentText . ' ';
                }
            }
            $workout->setComment($comment);
            $workout->setCompleted(true);
            $em->persist($workout);
            $em->flush();

            $email = getenv('ADMIN_EMAIL_ADDRESS');
            $bodyMail = $mailer->createBodyMail('emails/quiz_result.html.twig', [
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'quiz' => $quiz,
                'score' => $score,
            ]);
            $result = $mailer->sendMessage($email, '🎓 A quiz has just been completed by '.$user->getUsername().'!', $bodyMail);

            $form = $this->createForm(QuizType::class, $quiz, array('form_type' => 'student_questioning'));

            return $this->render(
                'quiz/end.html.twig',
                [
                    'id' => $workout->getId(),
                    'quiz' => $quiz,
                    'score' => $score,
                    'questionsHistory' => $questionsHistory,
                    'comment' => $workout->getComment(),
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
        $now = new \DateTime();
        if (!$quiz->getAllowAnonymousWorkout()) {
            $this->denyAccessUnlessGranted('ROLE_USER', null, 'Access not allowed');
        } else {
            if (!$user) {
                $userRepository = $em->getRepository(User::class);
                $user = $userRepository->findOneBy(['username' => 'anonymous']);
                if (!$user) {
                    $user = new User();
                    $user->setUsername('anonymous');
                    $user->setPassword(bin2hex(random_bytes(10)));
                    $user->setEmail('anonymous@domain.tld');
                    $em->persist($user);
                }
            }
        }

        if (!$quiz->getActive()) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Access not allowed');
        }
        
        $workoutRepository = $em->getRepository(Workout::class);
        $workout = new Workout();
        $workout->setStudent($user);
        $workout->setQuiz($quiz);
        $workout->setStartedAt($now);
        $workout->setNumberOfQuestions(0);
        $em->persist($workout);
        $em->flush();

        return $this->render(
            'quiz/start.html.twig',
            [
                'id' => $workout->getId(),
                'quiz' => $quiz,
            ]
        );
    }

    /**
     * @Route("/", name="quiz_index", methods="GET")
     */
    public function index(QuizRepository $quizRepository, CategoryRepository $categoryRepository, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'Access not allowed');

        $categoryId = $request->query->get('category');

        $categoryLongName = "";
        
        if ($categoryId > 0 ) {
            $quizzes = $quizRepository->findAllByCategories($this->isGranted('ROLE_ADMIN'), [$categoryId]);
            $category = $categoryRepository->find($categoryId);
            $categoryLongName = $category->getLongName();
        }
        else {
            $quizzes = $quizRepository->findAll($this->isGranted('ROLE_ADMIN'));
        }

        return $this->render('quiz/index.html.twig', ['quizzes' => $quizzes, 'category_id' => $categoryId, 'category_long_name' => $categoryLongName]);
    }

    /**
     * @Route("/new", name="quiz_new", methods="GET|POST")
     */
    function new (Request $request, EntityManagerInterface $em): Response {
        $this->denyAccessUnlessGranted('ROLE_TEACHER', null, 'Access not allowed');

        $quiz = $em->getRepository(Quiz::class)->create();

        $form = $this->createForm(QuizType::class, $quiz);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $questionRepository = $em->getRepository(Question::class);

            // Check if enough questions for this quiz
            $questionsCount = $questionRepository->countByCategories($quiz->getCategories());
            if ($questionsCount < $quiz->getNumberOfQuestions()) {
                $this->addFlash('danger', 'Not enough questions (' . $questionsCount . ') for this quiz');
            } else {
                $em->persist($quiz);
                $em->flush();

                $this->addFlash('success', sprintf('Quiz "%s" is created.', $quiz->getTitle()));

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
    public function edit(Request $request, Quiz $quiz, EntityManagerInterface $em): Response
    {
        $now = new \DateTime();

        $this->denyAccessUnlessGranted('ROLE_TEACHER', null, 'Access not allowed');

        $form = $this->createForm(QuizType::class, $quiz);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $quiz->setUpdatedAt($now);

            //$this->getDoctrine()->getManager()->flush();
            $em->flush();

            $this->addFlash('success', sprintf('Quiz "%s" is updated.', $quiz->getTitle()));

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

        if ($this->isCsrfTokenValid('delete' . $quiz->getId(), $request->request->get('_token'))) {
            //$em = $this->getDoctrine()->getManager();
            $em->remove($quiz);
            $em->flush();

            $this->addFlash('success', sprintf('Quiz "%s" is deleted.', $quiz->getTitle()));
        }

        return $this->redirectToRoute('quiz_index');
    }
}
