<?php

namespace App\Controller;

use App\Entity\Question;
use App\Form\QuestionType;
use App\Repository\CategoryRepository;
use App\Repository\QuestionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/question")
 */
class QuestionController extends AbstractController
{
    /**
     * @Route("/", defaults={"page": "1"}, name="question_index", methods="GET")
     * @Route("/page/{page}", requirements={"page": "[1-9]\d*"}, methods={"GET"}, name="question_index_paginated")
     */
    public function index(int $page, QuestionRepository $questionRepository, CategoryRepository $categoryRepository, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_TEACHER', null, 'Access not allowed');

        $categoryId = $request->query->get('category');

        $categoryLongName = "";
        
        if ($categoryId > 0 ) {
            $page = -1;
            $questions = $questionRepository->findAllByCategories([$categoryId], $page, $this->isGranted('ROLE_TEACHER'), $this->isGranted('ROLE_ADMIN'));
            $category = $categoryRepository->find($categoryId);
            $categoryLongName = $category->getLongName();
        }
        else {
            $questions = $questionRepository->findAll($page, $this->isGranted('ROLE_TEACHER'), $this->isGranted('ROLE_ADMIN'));
        }

        return $this->render('question/index.html.twig', ['page' => $page, 'questions' => $questions, 'category_id' => $categoryId, 'category_long_name' => $categoryLongName]);

    }

    /**
     * @Route("/new", name="question_new", methods="GET|POST")
     */
    public function new(Request $request, EntityManagerInterface $em, CategoryRepository $categoryRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_TEACHER', null, 'Access not allowed');

        $categoryId = $request->query->get('category');
        if ($categoryId > 0) {            
            $category = $categoryRepository->find($categoryId);
        }

        $question = $em->getRepository(Question::class)->create();
        if ($categoryId > 0) {            
            $question->addCategory($category);
        }

        $form = $this->createForm(QuestionType::class, $question, array('form_type'=>'teacher'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $question->setCreatedBy($this->getUser());
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
            'category_id' => $categoryId, 
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

        $formType = 'teacher';
        if ($this->isGranted('ROLE_ADMIN')) {
            $formType = 'admin';
        }
        $form = $this->createForm(QuestionType::class, $question, array('form_type'=>$formType));
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
        $questionFirstCategory = $question->getFirstCategory();
        if (isset($questionFirstCategory)) {
            $questionFirstCategoryId = $questionFirstCategory->getId();
        } else {
            $questionFirstCategoryId = 0;
        }
        

        return $this->render('question/edit.html.twig', [
            'question' => $question,
            'category_id' => $questionFirstCategoryId, 
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
