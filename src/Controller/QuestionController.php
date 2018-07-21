<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Question;
use App\Form\QuestionType;
use App\Repository\QuestionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @Route("/question")
 */
class QuestionController extends Controller
{
    /**
     * @Route("/", defaults={"page": "1"}, name="question_index", methods="GET")
     * @Route("/page/{page}", requirements={"page": "[1-9]\d*"}, methods={"GET"}, name="question_index_paginated")
     */
    public function index(int $page, QuestionRepository $questionRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_TEACHER', null, 'Access not allowed');

        return $this->render('question/index.html.twig', ['questions' => $questionRepository->findAll($page)]);
    }

    /**
     * @Route("/new", name="question_new", methods="GET|POST")
     */
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_TEACHER', null, 'Access not allowed');

        $question = $em->getRepository(Question::class)->create();

        $form = $this->createForm(QuestionType::class, $question, array('form_type'=>'teacher'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($question);

            foreach ($question->getAnswers() as $answer) {
                $em->persist($answer);
            }

            $em->flush();

            $this->addFlash('success', sprintf('Question #%s is created.', $question->getId()));

            return $this->redirectToRoute('question_index');
        }

        return $this->render('question/new.html.twig', [
            'question' => $question,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="question_show", methods="GET")
     */
    public function show(Question $question): Response
    {
        $this->denyAccessUnlessGranted('ROLE_TEACHER', null, 'Access not allowed');

        return $this->render('question/show.html.twig', ['question' => $question]);
    }

    /**
     * @Route("/{id}/edit", name="question_edit", methods="GET|POST")
     */
    public function edit(Request $request, Question $question, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_TEACHER', null, 'Access not allowed');

        $form = $this->createForm(QuestionType::class, $question, array('form_type'=>'teacher'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $question->setUpdatedAt(new \DateTime());

            foreach ($question->getAnswers() as $answer) {
                $em->persist($answer);
            }

            $em->flush();

            $this->addFlash('success', sprintf('Question #%s is updated.', $question->getId()));

            return $this->redirectToRoute('question_edit', ['id' => $question->getId()]);
        }

        return $this->render('question/edit.html.twig', [
            'question' => $question,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="question_delete", methods="DELETE")
     */
    public function delete(Request $request, Question $question, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_TEACHER', null, 'Access not allowed');

        if ($this->isCsrfTokenValid('delete'.$question->getId(), $request->request->get('_token'))) {
            $em->remove($question);
            $em->flush();

            $this->addFlash('success', sprintf('Question #%s is deleted.', $question->getId()));
        }

        return $this->redirectToRoute('question_index');
    }
}
