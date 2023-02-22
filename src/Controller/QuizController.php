<?php

namespace App\Controller;

use DateTime;
use App\Entity\Quiz;
use App\Entity\User;
use App\Form\QuizType;
use App\Entity\Workout;
use App\Entity\Question;
use App\Form\QuestionType;
use App\Entity\AnswerHistory;
use App\Entity\QuestionHistory;
use App\Repository\QuizRepository;
use Symfony\Component\Mime\Address;
use App\Repository\SessionRepository;
use App\Repository\WorkoutRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

/**
 * @Route("/quiz")
 */
class QuizController extends AbstractController
{

    private $tokenGenerator;

    public function __construct(TokenGeneratorInterface $tokenGenerator)
    {
        $this->tokenGenerator = $tokenGenerator;
    }

    /**
     * @Route("/{id}/result", name="quiz_result", methods="GET")
     */
    public function result(Request $request, Quiz $quiz, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'Access not allowed');

        $workout_id = $request->query->get('workout');
        if (isset($workout_id)) {
            $workoutRepository = $em->getRepository(Workout::class);
            $workout = $workoutRepository->find($workout_id);
            if (isset($workout)) {
                $user = $workout->getStudent();

                $workout_token = $request->query->get('token');
                if (isset($workout_token) && ($workout_token != "")) {
                    if ($workout_token == $workout->getToken()) {
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

                        $form = $this->createForm(QuizType::class, $quiz, array('form_type' => 'student_questioning'));

                        $questionHistoryRepository = $em->getRepository(QuestionHistory::class);
                        $questionsHistory = $questionHistoryRepository->findAllByWorkout($workout);

                        return $this->render(
                            'quiz/end.html.twig',
                            [
                                'id' => $workout->getId(),
                                'quiz' => $quiz,
                                'score' => $score,
                                'questionsHistory' => $questionsHistory,
                                'user' => $user,
                                'comment' => $workout->getComment(),
                                'form' => $form->createView(),
                            ]
                        );
                    } else {
                        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Access not allowed');
                    }
                } else {
                    $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Access not allowed');
                }
            } else {
                return $this->redirectToRoute('index');
            }
        } else {
            return $this->redirectToRoute('index');
        }
    }

    /**
     * @Route("/{id}/analyse", name="quiz_analyse", methods="GET")
     */
    public function analyse(Quiz $quiz, Request $request, EntityManagerInterface $em, SessionRepository $sessionRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_TEACHER', null, 'Access not allowed');

        $startedAt = $quiz->getActivedAt();

        $questionHistoryRepository = $em->getRepository(QuestionHistory::class);


        $session = null;
        $session_id = $request->query->get('session');
        if ($session_id > 0) {
            $session = $sessionRepository->find($session_id);
        }
        if (isset($session)) {
            // $workouts = $workoutRepository->findFirstThreeByQuizAndSession($quiz, $session);
            $questionsHistory = $questionHistoryRepository->findAllByQuizAndSession($quiz, $session);
        } else {
            $questionsHistory = $questionHistoryRepository->findAllByQuizAndDate($quiz, $startedAt);
        }

        return $this->render(
            'quiz/monitor/analyse.html.twig',
            [
                'quiz' => $quiz,
                'startedAt' => $startedAt,
                'questionsHistory' => $questionsHistory,
            ]
        );
    }

    /**
     * @Route("/{id}/podium", name="quiz_podium", methods="GET")
     */
    public function podium(Request $request, Quiz $quiz, EntityManagerInterface $em, SessionRepository $sessionRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_TEACHER', null, 'Access not allowed');

        if ($quiz->getActive()) {
            $quiz->setActiveInSession(false, $em); //TODO
            $em->persist($quiz);
            $em->flush();
        }

        $startedAt = $quiz->getActivedAt();

        $workoutRepository = $em->getRepository(Workout::class);

        $session = null;
        $session_id = $request->query->get('session');
        if ($session_id > 0) {
            $session = $sessionRepository->find($session_id);
        }
        if (isset($session)) {
            $workouts = $workoutRepository->findFirstThreeByQuizAndSession($quiz, $session);
        } else {
            $workouts = $workoutRepository->findFirstThreeByQuizAndDate($quiz, $startedAt);
        }

        $firstStudent = new User();
        $firstStudentScore = 0;
        if (sizeof($workouts) > 0) {
            $firstStudent = ($workouts[0])->getStudent();
            $firstStudentScore = ($workouts[0])->getScore();
            $firstStudentDuration = ($workouts[0])->getDuration()->format('%I:%S');            
        }
        $secondStudent = new User();
        $secondStudentScore = 0;
        if (sizeof($workouts) > 1) {
            $secondStudent = ($workouts[1])->getStudent();
            $secondStudentScore = ($workouts[1])->getScore();
            $secondStudentDuration = ($workouts[1])->getDuration()->format('%I:%S');            
        }
        $thirdStudent = new User();
        $thirdStudentScore = 0;
        if (sizeof($workouts) > 2) {
            $thirdStudent = ($workouts[2])->getStudent();
            $thirdStudentScore = ($workouts[2])->getScore();
            $thirdStudentDuration = ($workouts[2])->getDuration()->format('%I:%S');            
        }


        return $this->render(
            'quiz/monitor/podium.html.twig',
            [
                'workouts' => $workouts,
                'quiz' => $quiz,
                'session_id' => $session_id,
                'startedAt' => $startedAt,
                'firstStudent' => $firstStudent,
                'secondStudent' => $secondStudent,
                'thirdStudent' => $thirdStudent,
                'firstStudentScore' => $firstStudentScore,
                'secondStudentScore' => $secondStudentScore,
                'thirdStudentScore' => $thirdStudentScore,
                'firstStudentDuration' => $firstStudentDuration,
                'secondStudentDuration' => $secondStudentDuration,
                'thirdStudentDuration' => $thirdStudentDuration,
            ]
        );
    }

    /**
     * @Route("/{id}/activate", name="quiz_activate", methods="GET")
     */
    public function activate(Request $request, Quiz $quiz, EntityManagerInterface $em): Response
    {
        $activate = ($request->query->get('active') == 1);
        $quiz->setActiveInSession($activate, $em);
        $em->persist($quiz);
        $em->flush();

        // $url = $this->generateUrl('quiz_index');         
        // if (!$activate) {
        //     $url = $url . '#quiz-'.$quiz->getId();
        // }
        // return $this->redirect($url);
        return $this->redirectToRoute('quiz_index');
    }

    /**
     * @Route("/{id}/monitor", name="quiz_monitor", methods="GET")
     */
    public function monitor(Request $request, Quiz $quiz, EntityManagerInterface $em, WorkoutRepository $workoutRepository, SessionRepository $sessionRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_TEACHER', null, 'Access not allowed');

        $startedAt = $quiz->getActivedAt();

        $activate = $request->query->get('active');
        if (isset($activate)) {
            if ($activate == -1) {
                $quiz->setActiveInSession(false, $em);
                $em->persist($quiz);
                $em->flush();        
            }
        }

        $session = null;
        $session_id = $request->query->get('session');
        if ($session_id > 0) {
            $session = $sessionRepository->find($session_id);
        }
        if (isset($session)) {
            $workouts = $session->getWorkouts();
            // $workouts = $workoutRepository->findByQuizAndSession($quiz, $session);
        } else {
            $workouts = $workoutRepository->findByQuizAndDate($quiz, $startedAt);
        }

        $showStudentsName = false;
        $show = $request->query->get('show');
        if (isset($show)) {
            $showStudentsName = ($show == 1);
        }

        return $this->render(
            'quiz/monitor/monitor.html.twig',
            [
                'workouts' => $workouts,
                'quiz' => $quiz,
                'session_id' => $session_id,
                'startedAt' => $startedAt,
                'showStudentsName' => $showStudentsName,
            ]
        );
    }

    /**
     * @Route("/{id}/workout", name="quiz_workout", methods="POST")
     */
    public function workout(Request $request, Workout $workout, EntityManagerInterface $em, UserInterface $user = null, MailerInterface $mailer, TranslatorInterface $translator): Response
    {
        if ($workout->getCompleted()) {
            return $this->redirectToRoute('quiz_result', ['id' => $workout->getQuiz()->getId(), 'workout' => $workout->getId(), 'token' => $workout->getToken()]);
        }

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
        $question_duration = 0;
        $score = $workout->getScore();
        $grade = round($score / 5, 1);
        $quiz = $workout->getQuiz();

        if (!$quiz->getAllowAnonymousWorkout()) {
            $this->denyAccessUnlessGranted('ROLE_USER', null, 'Access not allowed');
        }        

        if (!$user) {
            $user = $workout->getStudent();
        }

        if (!$quiz->getActive()) {
            // $this->denyAccessUnlessGranted('ROLE_TEACHER', null, 'Access not allowed');
            $this->addFlash('danger', $translator->trans('Sorry, the quiz is stopped !'));
            $form = $this->createForm(QuizType::class, $quiz, array('form_type' => 'student_questioning'));
            return $this->render(
                'quiz/end.html.twig',
                [
                    'id' => $workout->getId(),
                    'quiz' => $quiz,
                    'score' => $score,
                    'questionsHistory' => null,
                    'user' => $user,
                    'comment' => $workout->getComment(),
                    'form' => $form->createView(),
                ]
            );            
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

                            $from_email_address = $this->getParameter('FROM_EMAIL_ADDRESS');
                            $admin_email_address = $this->getParameter('ADMIN_EMAIL_ADDRESS');                            
                            $email = (new TemplatedEmail())
                                ->from(new Address($from_email_address, 'JoliQuiz'))                                
                                ->to($admin_email_address)
                                ->subject('🙂 ' . $translator->trans('Quiz "%title%" by %username% = %score%% (%grade%/20) - Before the end!', ['%title%' => $quiz->getTitle(), '%username%' => $user->getName(), '%score%' => $score, '%grade%' => $grade]))
                                // path of the Twig template to render
                                ->htmlTemplate('emails/quiz_result.html.twig')
                                // pass variables (name => value) to the template
                                ->context([
                                    'username' => $user->getName(),
                                    'useremail' => $user->getEmail(),
                                    'quiz' => $quiz,
                                    'score' => $score,
                                    'grade' => $grade,
                                    'workout_id' => $workout->getId(),
                                    'workout_token' => $workout->getToken(),
                                ]);
                            if ($user->isToReceiveMyResultByEmail()) {
                                $email->addTo($user->getEmail());
                            }
                            $mailer->send($email);

                            $this->addFlash('danger', $comment);
                            $form = $this->createForm(QuizType::class, $quiz, array('form_type' => 'student_questioning'));
                            return $this->render(
                                'quiz/end.html.twig',
                                [
                                    'id' => $workout->getId(),
                                    'quiz' => $quiz,
                                    'score' => 0,
                                    'questionsHistory' => $questionsHistory,
                                    'user' => $user,
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
                    $workout->setEndedAt($now);
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
                    'user' => $user,
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
                // Find a random question
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
            $grade = round($score / 5, 1);
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

            // TODO : Mettre l'envoi du mail dans un service
            $admin_email_address = $this->getParameter('ADMIN_EMAIL_ADDRESS');
            $email = (new TemplatedEmail())
                ->from($admin_email_address)
                ->to($admin_email_address)
                ->subject('🙂 ' . $translator->trans('Quiz "%title%" by %username% = %score%% (%grade%/20)', ['%title%' => $quiz->getTitle(), '%username%' => $user->getName(), '%score%' => $score, '%grade%' => $grade]))
                // path of the Twig template to render
                ->htmlTemplate('emails/quiz_result.html.twig')
                // pass variables (name => value) to the template
                ->context([
                    'username' => $user->getName(),
                    'useremail' => $user->getEmail(),
                    'quiz' => $quiz,
                    'score' => $score,
                    'grade' => $grade,
                    'workout_id' => $workout->getId(),
                    'workout_token' => $workout->getToken(),
                ]);
            if ($user->isToReceiveMyResultByEmail()) {
                $email->addTo($user->getEmail());
            }
            $mailer->send($email);

            $form = $this->createForm(QuizType::class, $quiz, array('form_type' => 'student_questioning'));

            return $this->render(
                'quiz/end.html.twig',
                [
                    'id' => $workout->getId(),
                    'quiz' => $quiz,
                    'score' => $score,
                    'questionsHistory' => $questionsHistory,
                    'user' => $user,
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
            $this->denyAccessUnlessGranted('ROLE_TEACHER', null, 'Access not allowed');
        }

        // $workoutRepository = $em->getRepository(Workout::class);
        $workout = new Workout();
        $workout->setStudent($user);
        $workout->setQuiz($quiz);
        $workout->setStartedAt($now);
        $workout->setNumberOfQuestions(0);
        $workout->setToken($this->tokenGenerator->generateToken());
        $em->persist($workout);

        $session = $quiz->getLastSession();
        $session->addWorkout($workout);
        $em->persist($session);

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
    public function index(QuizRepository $quizRepository, CategoryRepository $categoryRepository, Request $request, EntityManagerInterface $em, UserInterface $user = null): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'Access not allowed');

        $now = new DateTime();

        $categoryId = $request->query->get('category');
        $categoryLongName = "";
        $categoryShortName = "";   
        if ($categoryId > 0) {
            $quizzes = $quizRepository->findAllByCategories([$categoryId], $this->isGranted('ROLE_TEACHER'), $this->isGranted('ROLE_ADMIN'));
            $category = $categoryRepository->find($categoryId);
            $categoryLongName = $category->getLongName();
            $categoryShortName =  $category->getShortName();
        } else {
            $quizzes = $quizRepository->findAll($this->isGranted('ROLE_TEACHER'), $this->isGranted('ROLE_ADMIN'));
        }

        if ($this->getUser()) {
            $user->setLastQuizAccess($now);
            $em->persist($user);
            $em->flush();
            if (!$this->isGranted('ROLE_TEACHER')) {
                if (count($quizzes) == 1) {
                    return $this->start($request, $quizzes[0], $em, $user);
                }
            }
        }
        else {
            $user->setLastQuizAccess(null);
            $em->persist($user);
            $em->flush();
        }

        return $this->render('quiz/index.html.twig', ['quizzes' => $quizzes, 'category_id' => $categoryId, 'category_long_name' => $categoryLongName, 'category_short_name' => $categoryShortName]);
    }

    /**
     * @Route("/new", name="quiz_new", methods="GET|POST")
     */
    public function new(Request $request, EntityManagerInterface $em, CategoryRepository $categoryRepository, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('ROLE_TEACHER', null, 'Access not allowed');

        $quiz = $em->getRepository(Quiz::class)->create();        

        // Auto select category
        $categoryId = $request->query->get('category');
        if ($categoryId > 0) {            
            $category = $categoryRepository->find($categoryId);
        }
        if ($categoryId > 0) {            
            $quiz->addCategory($category);
            $quiz->setTitle($category->getLongname());
            $quiz->setNumberOfQuestions(sizeof($category->getQuestions()));
        }

        $form = $this->createForm(QuizType::class, $quiz, ['isAdmin' => $this->isGranted('ROLE_ADMIN')]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $questionRepository = $em->getRepository(Question::class);

            // Check if enough questions for this quiz
            $questionsCount = $questionRepository->countByCategories($quiz->getCategories());
            if ($questionsCount < $quiz->getNumberOfQuestions()) {
                $this->addFlash('danger', 'Not enough questions (' . $questionsCount . ') for this quiz');
            } else {
                $quiz->setCreatedBy($this->getUser());
                $em->persist($quiz);
                $em->flush();

                $this->addFlash('success', sprintf($translator->trans('Quiz "%s" is created.'), $quiz->getTitle()));

                return $this->redirectToRoute('quiz_index');
            }
        }
        return $this->render('quiz/new.html.twig', [
            'quiz' => $quiz,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="quiz_edit", methods="GET|POST")
     */
    public function edit(Request $request, Quiz $quiz, EntityManagerInterface $em, TranslatorInterface $translator): Response
    {
        $now = new \DateTime();

        $this->denyAccessUnlessGranted('ROLE_TEACHER', null, 'Access not allowed');

        $form = $this->createForm(QuizType::class, $quiz, ['isTeacher' => $this->isGranted('ROLE_TEACHER'), 'isAdmin' => $this->isGranted('ROLE_ADMIN')]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $quiz->setUpdatedAt($now);
            $em->flush();
            $this->addFlash('success', sprintf($translator->trans('Quiz "%s" is updated.'), $quiz->getTitle()));
            return $this->redirectToRoute('quiz_edit', ['id' => $quiz->getId()]);
        }

        return $this->render('quiz/edit.html.twig', [
            'quiz' => $quiz,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="quiz_delete", methods="POST")
     */
    public function delete(Request $request, Quiz $quiz, EntityManagerInterface $em, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Access not allowed');

        if ($this->isCsrfTokenValid('delete' . $quiz->getId(), $request->request->get('_token'))) {
            $em->remove($quiz);
            $em->flush();
            $this->addFlash('success', sprintf($translator->trans('Quiz "%s" is deleted.'), $quiz->getTitle()));
        }

        return $this->redirectToRoute('quiz_index');
    }

    /**
     * @Route("/{id}", name="quiz_show", methods="GET")
     */
    public function show(Quiz $quiz): Response
    {
        $this->denyAccessUnlessGranted('ROLE_TEACHER', null, 'Access not allowed');

        return $this->render('quiz/show.html.twig', ['quiz' => $quiz]);
    }

}
