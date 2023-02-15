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
     * TODO : méthode A EFFACER XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
     * @Route("/{id}/test", name="session_test", methods="GET")
     */
    /*

	<a class="btn mx-1" href="{{ path('session_test', {'id': session.id, 'quiz': quiz.id}) }}">{{ 'à effacer'|trans }}</a>

    public function test(Session $session, Request $request, SessionRepository $sessionRepository, QuizRepository $quizRepository, GroupRepository $groupRepository, UserInterface $user = null, HttpClientInterface $client, LoggerInterface $logger): Response
    {
        $this->denyAccessUnlessGranted('ROLE_TEACHER', null, 'Access not allowed');

        $quiz_id = $request->query->get('quiz');
        $quiz = $quizRepository->find($quiz_id);
        $today = date('Y-m-d');

        $classe_id = 73;
        $periode_code = "' . $periode_code . '";
        $matiere_code = "' . $matiere_code . '";

        $devoir_id = ' . $devoir_id . ';
        $ecoleDirecteToken = '546c6772616c4658556a566f516e4130576a4a4c4b3346544d45566f625778505230785154584172543264525455646965564e315a6b354a656b785861486c6a56576871576d4a77576d6c364e6d68790d0a5a44685a553068724d334252556e6c77626e64364d6d4643626e6b4e436a6855556d6858513068616248647055304e7563584972565456444f46643661556c5553303176557a567865465a47574759300d0a526b316c4d565a30575578765647526b4d45356d516c4931566b64705a455a584e5756514e57527863576f30546c5a5363466778576c567744517032547a466b616d565164486c36646e5249516b6b320d0a646b783559554e4c543170435656526b636d6f79554552445244464c5355524361306b7a4d5668494d6b7032636e525864466c4a644646464c303573574455326546524a5a6e4979546b4e7159556c420d0a6458497a6377304b57456c365a334e7755697469555539315757314257474977516c564d553046705a32463161555a616345683462554e4355576454633245795a6c686965464a444f554a6c565867780d0a6131686c5254453362324a4b63305532543030356345317756555653564859355246634e436b395a4d56704453335a7557485a476330644a525531534e6a4e5a5932704b563278704d316b305a6a6c360d0a5931413356304e734d6c704c633035545447743559555a7965484e4b61464e58525746494d466f34537a684a633034315a564a784e47783553585a7357565a304451706e5a584e6c63586c75624770760d0a54486731615670575969394a5a7a46504f545a4555433975596c41796548465154586b34617a524f527a524b64473143644739316569743364486c5461324a6d6253394b4f46425954576c34574768740d0a56544e6f537a5268643035715277304b61316c54576b4a6b56325178563039325a586c4e526a6777595464314d314a74616e424d524556365a477478516d683653464a4f56334a314e6a4677554531780d0a626c52356155394d6457395454444e7962455172655652535257747a52306b314c304e7265587055566c4d4e436b70365a557331536a49764f56704d5547524c54586c33555649356279387a616d63790d0a4d47467963446c784b30646852326831566a6b765447597a5933684b546c42614f486c6e4d324a324d476844626c5535596d4e48545842594d54427657457071526b6c735348597a44517045535559320d0a53576b334c7a427a5a433955566e466d567a6878576b6379596b64705a314e7564305676526d353357484572645739444e7a643564444231536c464d534768615a4746616157645755444e6d656d30780d0a53305a69516d7832546d3972566a4d7a534539475677304b526d684461335236536b6c42643034796544633459323133553068745432564b6254423261564e6c56564e42576a4e4d646c4e344f5456420d0a596c525251544a6a547a6332535842555133646b5444526d5555566f6546597753554e4f5356467262576c576133567256446b4e436e5651525868304d304e4551793834567a6432633145314d47737a0d0a516d4e745a48704b6446557a647a46564f456c6c4f484679566a4179526a4134515468494e6a4a75565670325530637861546b304d56563062433932537a4d77526d686c5a55316e61566c47595539710d0a44517034615856446132687154475a59654339735745394e6254686c54484a32516a64525744646b52585646656b64796358426d6431704953554a365332737a5657784459306449536b5930643234790d0a5a6a5a566432557a62324e5562327334555456425448526a56486b316151304b4f553543633342766157563461325a4451553131537a42585757677952566c4d596b4a7155466861646a557a556d68700d0a616c705461575253564670494f57706a596b4e6c56574e725657685a647a46365257733362455a6d4e54497a643356795369746164335535526b634e436a525a4d45593556316c4353454979636e5a510d0a636c4657556e4a796454424d626c684f5157644262564a6a655735504e58557a536a6c4d4e7a426a54454d334f565932643052564c305270567a4130564655354e6e424565564e4a547a5233646b70570d0a5657394f576b744f445170696147396f595642614d464a55574868344d455a57596e4a47516e4a546133413451556447616d6c79556c4931595756505633517951586430526b354361457851645574530d0a4f48527552573478644539315a7a6430616e466851586c7564314e774b326c69536a526f5967304b645856774d6e70555332677859574e726130315264554e725330355a55314e56596e565164574a730d0a51576c5762586c785954464b4e58566d513146795755355a5548464e5a6a4977597a46744f45314f556a42354f464a5363464a745254687354305a51546a6333616c634e436a4a7164473171525842720d0a636c64565958526e63545a42646a42335630524f56304e6d4f554e5161465933655846444b3342765a466c424f48563663554a546346645a545456785230644456305a7a545646355757786b545759330d0a59546c6c5555397a4d5739455747704e44517036545646786544685759304d30616c4645516e6c4c616b6f34524738334e445648556b706e52316876576e5644566b646e4b30354359304e70553239460d0a56554a704e45567351544a534d584a49546d30325a3356314b3046455a6b3972646c55794e564e6e566e5a6b4e67304b5a30773057544e515a5864466332525362555632544770325456644d5254526f0d0a61554643576b68505254524a546b6b3063325255616b746d4d556c505656673059337030613231564e485630516b56596546685051334653527a6b72535774715356566f4f5578705430634e436c68330d0a566a46564e46704964456c6b64544530526b644c526c5a425a455a7465566c4b4f486c4f4d6b744c5330673252466c444e46645a566c7068614868735a54464b64484236595577784d54564c4f4464750d0a4d574a79566c5a72596a5648656d4e365a47343551564a69445170515a79387a546b5a324d305a556347646a4b3259354e4464705a474e49636c64716145526e5132463063473546576c566163314e740d0a656b35514d5868794f5756305a58673063574d315248707462336472553368355133637852326f79536a5677516b466151304a366541304b4e303579595578704f4451315548467853466330557a42360d0a5954677663586c6163554e3656485a524c32464e64544671546a4e4e513142345246684253454a4d61544a6b56466f7662486c35576e6469626b45';

        dump('https://apip.ecoledirecte.com/v3/enseignants/' . $user->getEdId() . '/C/' . $classe_id . '/periodes/' . $periode_code . '/matieres/' . $matiere_code . '%C2%A4/notes.awp?verbe=post');

        // https://apip.ecoledirecte.com/v3/enseignants/194/C/73/periodes/' . $periode_code . '/matieres/' . $matiere_code . '%C2%A4/notes.awp?verbe=post&
        $response = $client->request(
            'POST',
            'https://apip.ecoledirecte.com/v3/enseignants/' . $user->getEdId() . '/C/' . $classe_id . '/periodes/' . $periode_code . '/matieres/' . $matiere_code . '%C2%A4/notes.awp?verbe=post',
            [
                'body' => 'data={
                    "devoirs": [
                        {
                            "id": ' . $devoir_id . ',
                            "idProf": 194,
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
                            "eleves": [
                                {
                                    "nom": "AIT ALI OULAHCEN",
                                    "prenom": "Rachid",
                                    "particule": "",
                                    "id": 2253,
                                    "devoirs": [
                                        {
                                            "idNote": 0,
                                            "idDevoir": ' . $devoir_id . ',
                                            "idPeriode": "' . $periode_code . '",
                                            "coef": 1,
                                            "note": "12",
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
                                },
                                {
                                    "nom": "ALEXIS",
                                    "prenom": "Mathis",
                                    "particule": "",
                                    "id": 2248,
                                    "devoirs": [
                                        {
                                            "idNote": 0,
                                            "idDevoir": ' . $devoir_id . ',
                                            "idPeriode": "' . $periode_code . '",
                                            "coef": 1,
                                            "note": "",
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
                                },
                                {
                                    "nom": "BAL",
                                    "prenom": "Erwan",
                                    "particule": "",
                                    "id": 1164,
                                    "devoirs": [
                                        {
                                            "idNote": 0,
                                            "idDevoir": ' . $devoir_id . ',
                                            "idPeriode": "' . $periode_code . '",
                                            "coef": 1,
                                            "note": "",
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
                                },
                                {
                                    "nom": "CHARPENTIER",
                                    "prenom": "Steven",
                                    "particule": "",
                                    "id": 2242,
                                    "devoirs": [
                                        {
                                            "idNote": 0,
                                            "idDevoir": ' . $devoir_id . ',
                                            "idPeriode": "' . $periode_code . '",
                                            "coef": 1,
                                            "note": "11",
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
                                },
                                {
                                    "nom": "GENISSEL",
                                    "prenom": "Benjamin",
                                    "particule": "",
                                    "id": 2257,
                                    "devoirs": [
                                        {
                                            "idNote": 0,
                                            "idDevoir": ' . $devoir_id . ',
                                            "idPeriode": "' . $periode_code . '",
                                            "coef": 1,
                                            "note": "",
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
                                },
                                {
                                    "nom": "GEORGEAULT",
                                    "prenom": "Louis",
                                    "particule": "",
                                    "id": 2232,
                                    "devoirs": [
                                        {
                                            "idNote": 0,
                                            "idDevoir": ' . $devoir_id . ',
                                            "idPeriode": "' . $periode_code . '",
                                            "coef": 1,
                                            "note": "7",
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
                                },
                                {
                                    "nom": "HAUER",
                                    "prenom": "Harry",
                                    "particule": "",
                                    "id": 2204,
                                    "devoirs": [
                                        {
                                            "idNote": 0,
                                            "idDevoir": ' . $devoir_id . ',
                                            "idPeriode": "' . $periode_code . '",
                                            "coef": 1,
                                            "note": "",
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
                                },
                                {
                                    "nom": "HOUGUET",
                                    "prenom": "Théo",
                                    "particule": "",
                                    "id": 2235,
                                    "devoirs": [
                                        {
                                            "idNote": 0,
                                            "idDevoir": ' . $devoir_id . ',
                                            "idPeriode": "' . $periode_code . '",
                                            "coef": 1,
                                            "note": "",
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
                                },
                                {
                                    "nom": "KURANDY",
                                    "prenom": "Eva",
                                    "particule": "",
                                    "id": 2254,
                                    "devoirs": [
                                        {
                                            "idNote": 0,
                                            "idDevoir": ' . $devoir_id . ',
                                            "idPeriode": "' . $periode_code . '",
                                            "coef": 1,
                                            "note": "11",
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
                                },
                                {
                                    "nom": "LE MOIGNE",
                                    "prenom": "Djorka",
                                    "particule": "",
                                    "id": 2231,
                                    "devoirs": [
                                        {
                                            "idNote": 0,
                                            "idDevoir": ' . $devoir_id . ',
                                            "idPeriode": "' . $periode_code . '",
                                            "coef": 1,
                                            "note": "",
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
                                },
                                {
                                    "nom": "LEROUX",
                                    "prenom": "Noam",
                                    "particule": "",
                                    "id": 2200,
                                    "devoirs": [
                                        {
                                            "idNote": 0,
                                            "idDevoir": ' . $devoir_id . ',
                                            "idPeriode": "' . $periode_code . '",
                                            "coef": 1,
                                            "note": "",
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
                                },
                                {
                                    "nom": "LEVESQUE",
                                    "prenom": "Willy",
                                    "particule": "",
                                    "id": 2245,
                                    "devoirs": [
                                        {
                                            "idNote": 0,
                                            "idDevoir": ' . $devoir_id . ',
                                            "idPeriode": "' . $periode_code . '",
                                            "coef": 1,
                                            "note": "",
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
                                },
                                {
                                    "nom": "LHERMITE",
                                    "prenom": "Thomas",
                                    "particule": "",
                                    "id": 2244,
                                    "devoirs": [
                                        {
                                            "idNote": 0,
                                            "idDevoir": ' . $devoir_id . ',
                                            "idPeriode": "' . $periode_code . '",
                                            "coef": 1,
                                            "note": "",
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
                                },
                                {
                                    "nom": "NOGUEIRE",
                                    "prenom": "Clément",
                                    "particule": "",
                                    "id": 2203,
                                    "devoirs": [
                                        {
                                            "idNote": 0,
                                            "idDevoir": ' . $devoir_id . ',
                                            "idPeriode": "' . $periode_code . '",
                                            "coef": 1,
                                            "note": "",
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
                                },
                                {
                                    "nom": "PINSON",
                                    "prenom": "Théo",
                                    "particule": "",
                                    "id": 2199,
                                    "devoirs": [
                                        {
                                            "idNote": 0,
                                            "idDevoir": ' . $devoir_id . ',
                                            "idPeriode": "' . $periode_code . '",
                                            "coef": 1,
                                            "note": "",
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
                                },
                                {
                                    "nom": "PRUNIER-GROSSIN",
                                    "prenom": "Théo",
                                    "particule": "",
                                    "id": 291,
                                    "devoirs": [
                                        {
                                            "idNote": 0,
                                            "idDevoir": ' . $devoir_id . ',
                                            "idPeriode": "' . $periode_code . '",
                                            "coef": 1,
                                            "note": "",
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
                                },
                                {
                                    "nom": "ROGER",
                                    "prenom": "Pierre",
                                    "particule": "",
                                    "id": 2243,
                                    "devoirs": [
                                        {
                                            "idNote": 0,
                                            "idDevoir": ' . $devoir_id . ',
                                            "idPeriode": "' . $periode_code . '",
                                            "coef": 1,
                                            "note": "",
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
                                },
                                {
                                    "nom": "VIRAULT",
                                    "prenom": "Rayan",
                                    "particule": "",
                                    "id": 2237,
                                    "devoirs": [
                                        {
                                            "idNote": 0,
                                            "idDevoir": ' . $devoir_id . ',
                                            "idPeriode": "' . $periode_code . '",
                                            "coef": 1,
                                            "note": "",
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
                                },
                                {
                                    "nom": "WALTER",
                                    "prenom": "Guillaume",
                                    "particule": "",
                                    "id": 2234,
                                    "devoirs": [
                                        {
                                            "idNote": 0,
                                            "idDevoir": ' . $devoir_id . ',
                                            "idPeriode": "' . $periode_code . '",
                                            "coef": 1,
                                            "note": "",
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
                                }
                            ]
                        }
                    ],
                    "token": "' . $ecoleDirecteToken . '"
                }',
            ]
        );
        $logger->debug(print_r($response->getContent(), true)); // to debug
        $ecoleDirecteResponse = json_decode($response->getContent());
        dump($ecoleDirecteResponse);

        return $this->render('session/index.html.twig', [
            'sessions' => $sessionRepository->findByQuizId($quiz_id),
            'quiz' => $quiz,
        ]);
    }
    */

}
