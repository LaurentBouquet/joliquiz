<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Entity\Session;
use App\Form\SessionType;
use Psr\Log\LoggerInterface;
use App\Repository\QuizRepository;
use App\Repository\GroupRepository;
use App\Repository\SessionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/session")
 */
class SessionController extends AbstractController
{

    /**
     * @Route("/{id}/send_to_ed", name="session_send_to_ed", methods="GET")
     */
    public function sendToEdStep1(Session $session, Request $request, SessionRepository $sessionRepository, QuizRepository $quizRepository, GroupRepository $groupRepository, UserInterface $user = null, HttpClientInterface $client, LoggerInterface $logger): Response
    {
        $this->denyAccessUnlessGranted('ROLE_TEACHER', null, 'Access not allowed');

        $quiz_id = $request->query->get('quiz');
        $quiz = $quizRepository->find($quiz_id);

        $response = $client->request(
            'POST',
            'https://apip.ecoledirecte.com/v3/niveauxListe.awp?verbe=get',
            [
                'body' => 'data={
                    "token": "' . $user->getToken() . '"
                }',
            ]
        );
        $logger->debug(print_r($response->getContent(), true)); // to debug
        $ecoleDirecteResponse = json_decode($response->getContent());
        $httpEcoleDirecteApiResponse = $ecoleDirecteResponse->code;
        $ecoleDirecteMessage = "";
        //$ecoleDirecteToken = $ecoleDirecteResponse->token;
        $logger->info("HTTP EcoleDirecte API response for get niveauxListe = " . $httpEcoleDirecteApiResponse);

        if ($httpEcoleDirecteApiResponse == 200) {
            $ecoleDirecteData = $ecoleDirecteResponse->data;
            $etablissements = $ecoleDirecteData->etablissements;
        } else {
            $ecoleDirecteMessage = $ecoleDirecteResponse->message;
            $etablissements = [];
        }

        return $this->render('session/select_period.html.twig', [
            'session' => $session,
            'quiz' => $quiz,
            'response' => $httpEcoleDirecteApiResponse,
            'message' => $ecoleDirecteMessage,
            'etablissements' => $etablissements,
        ]);
    }

    /**
     * @Route("/{id}/send_to_ed2", name="session_send_to_ed2", methods="GET")
     */
    public function sendToEdStep2(Session $session, Request $request, EntityManagerInterface $em, SessionRepository $sessionRepository, QuizRepository $quizRepository, UserInterface $user, HttpClientInterface $client, LoggerInterface $logger): Response
    {
        $this->denyAccessUnlessGranted('ROLE_TEACHER', null, 'Access not allowed');

        $classe_id = $request->query->get('classe');
        $periode_code = $request->query->get('periode');
        $matiere_code = $request->query->get('subject');
        $quiz_id = $request->query->get('quiz');
        $quiz = $quizRepository->find($quiz_id);
        $today = date('Y-m-d');

        $response = $client->request(
            'POST',
            'https://apip.ecoledirecte.com/v3/enseignants/' . $user->getEdId() . '/C/' . $classe_id . '/periodes/' . $periode_code . '/matieres/' . $matiere_code . '%C2%A4/devoirs.awp?verbe=post',
            [
                'body' => 'data={
                    "devoir":{
                        "libelle": "' . urlencode($quiz->getTitle()) . '",
                        "coef": 1,
                        "noteSur": 20,
                        "nonSignificatif": false,
                        "ccf": false,
                        "notationLettre": false,
                        "noteNegative": false,
                        "avecNote": true,
                        "elementsProgramme": [],
                        "dateAffichage": "' . $today . '",
                        "date": "' . $today . '"
                    },
                    "token": "' . $user->getToken() . '"
                }',
            ]
        );
        $logger->debug(print_r($response->getContent(), true)); // to debug
        $ecoleDirecteResponse = json_decode($response->getContent());
        $httpEcoleDirecteApiResponse = $ecoleDirecteResponse->code;
        $ecoleDirecteToken = $ecoleDirecteResponse->token;

        if ($httpEcoleDirecteApiResponse == 200) {
            $ecoleDirecteData = $ecoleDirecteResponse->data;
            $devoir_id = $ecoleDirecteData->id;
            $logger->info("HTTP EcoleDirecte API response to add new evaluation = " . $devoir_id);
        } else {
            $this->addFlash('error', $ecoleDirecteResponse->message);
            $devoir_id = 0;
        }

        if ($this->addMarkToStudentsEd($devoir_id, $ecoleDirecteToken, $today, $session, $quiz, $classe_id, $periode_code, $matiere_code, $user, $client, $logger)) {
            $session->setSendedToED(true);
            $em->persist($session);
            $em->flush();
            $this->addFlash('success', sprintf('Evaluation "%s" is created in EcoleDirecte and students mark were recorded.', $quiz->getTitle()));
        }
        else {
            $this->addFlash('warning', sprintf('Evaluation "%s" is created in EcoleDirecte, but students mark were NOT recorded.', $quiz->getTitle()));
        };

        return $this->render('session/index.html.twig', [
            'sessions' => $sessionRepository->findByQuizId($quiz_id),
            'quiz' => $quiz,
        ]);
    }

    /**
     * Save all students notation to ED
     *
     * @return bool
     */
    private function addMarkToStudentsEd(int $devoir_id, string $ecoleDirecteToken, string $today, Session $session, Quiz $quiz, int $classe_id, string $periode_code, string $matiere_code, UserInterface $user, HttpClientInterface $client, LoggerInterface $logger): bool
    {
        $result = false;
        $devoir_id = 0;
        if ($devoir_id > 0) {

            $eleve_index = 0;
            $data_eleves = '';
            foreach ($session->getWorkouts() as $workout) {
                if ($eleve_index > 0) {
                    $data_eleves = $data_eleves . ',
                ';
                }
                $student = $workout->getStudent();
                $data_eleves = $data_eleves . '{
                "nom": "'.$student->getLastname().'",
                "prenom": "'.$student->getFirstname().'",
                "particule": "",
                "id": '.$student->getEdId().',
                "devoirs": [
                    {
                        "idNote": 0,
                        "idDevoir": ' . $devoir_id . ',
                        "idPeriode": "' . $periode_code . '",
                        "coef": 1,
                        "note": "' . round(($workout->getScore()/5), 1) . '",
                        "noteSur": 20,
                        "lettre": "",
                        "notationLettre": false,
                        "date": "' . $today . '",
                        "devoirLibelle": "' . $quiz->getTitle() . '",
                        "ccf": false,
                        "nonSignificatif": false,
                        "codeMatiere": "' . $matiere_code . '",
                        "codeSSMatiere": "",
                        "elementsProgramme": [],
                        "isGoodNote": true,
                        "id": ' . $devoir_id . ',
                        "idProf": 194,
                        "nomProf": "",
                        "idLong": "",
                        "libelle": "' . $quiz->getTitle() . '",
                        "dateAffichage": "' . $today . '",
                        "noteNegative": false,
                        "statutPeriode": "ouvert",
                        "avecNote": true,
                        "typeDevoir": {
                            "id": 0,
                            "code": "",
                            "libelle": "",
                            "coef": 0
                        },
                        "readOnly": false,
                        "data": "devoirs.' . $devoir_id . '.note"
                    }
                ]
            }';
                $eleve_index++;
            }

            $data = '{
            "devoirs": [
                {
                    "id": ' . $devoir_id . ',
                    "idProf": ' . $user->getEdId() . ',
                    "nomProf": "",
                    "idLong": "",
                    "libelle": "' . $quiz->getTitle() . '",
                    "coef": 1,
                    "date": "' . $today . '",
                    "dateAffichage": "' . $today . '",
                    "nonSignificatif": false,
                    "ccf": false,
                    "noteSur": 20,
                    "notationLettre": false,
                    "noteNegative": false,
                    "statutPeriode": "ouvert",
                    "idPeriode": "' . $periode_code . '",
                    "codeMatiere": "' . $matiere_code . '",
                    "codeSSMatiere": "",
                    "avecNote": true,
                    "elementsProgramme": [],
                    "typeDevoir": {
                        "id": 0,
                        "code": "",
                        "libelle": "",
                        "coef": 0
                    },
                    "readOnly": false,
                    "data": "devoirs.' . $devoir_id . '.note",
                    "eleves": [' .
                        $data_eleves
                        . ']
                        }
                    ],
                    "token": "' . $ecoleDirecteToken . '"
                }';

            // https://apip.ecoledirecte.com/v3/enseignants/194/C/73/periodes/' . $periode_code . '/matieres/' . $matiere_code . '%C2%A4/notes.awp?verbe=post&
            $response = $client->request(
                'POST',
                'https://apip.ecoledirecte.com/v3/enseignants/' . $user->getEdId() . '/C/' . $classe_id . '/periodes/' . $periode_code . '/matieres/' . $matiere_code . '%C2%A4/notes.awp?verbe=post',
                [
                    'body' => 'data=' . $data,
                ]
            );
            $logger->debug(print_r($response->getContent(), true)); // to debug
            $ecoleDirecteResponse = json_decode($response->getContent());

            if ($ecoleDirecteResponse) {
                $httpEcoleDirecteApiResponse = $ecoleDirecteResponse->code;
                $ecoleDirecteMessage = "";
                $ecoleDirecteToken = $ecoleDirecteResponse->token;
                $logger->info("HTTP EcoleDirecte API response for get niveauxListe = " . $httpEcoleDirecteApiResponse);

                if ($httpEcoleDirecteApiResponse == 200) {
                    $ecoleDirecteData = $ecoleDirecteResponse->data;
                    $result = true;
                } else {
                    $ecoleDirecteMessage = $ecoleDirecteResponse->message;
                }
            }
        }

        return $result;
    }


    /**
     * @Route("/clean", name="session_clean", methods={"GET"})
     */
    public function clean(SessionRepository $sessionRepository, QuizRepository $quizRepository, Request $request): Response
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
    public function quiz(SessionRepository $sessionRepository, QuizRepository $quizRepository, Request $request): Response
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
     * @Route("/{id}/edit", name="session_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Session $session, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(SessionType::class, $session);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('session_index');
        }

        return $this->render('session/edit.html.twig', [
            'session' => $session,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="session_delete", methods="POST")
     */
    public function delete(Request $request, Session $session, EntityManagerInterface $em, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Access not allowed');

        $quizId = $session->getQuiz()->getId();
        if ($this->isCsrfTokenValid('delete' . $session->getId(), $request->request->get('_token'))) {
            $em->remove($session);
            $em->flush();

            $this->addFlash('success', sprintf($translator->trans('Session started at %s is deleted.'), $session->getStartedAt()->format("d/m/Y h:m")));
        }

        return $this->redirectToRoute('session_quiz', ['id' => $quizId]);
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
    
}
