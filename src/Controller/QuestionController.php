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
use Symfony\Contracts\Translation\TranslatorInterface;
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
    public function index(int $page, QuestionRepository $questionRepository, CategoryRepository $categoryRepository, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_TEACHER', null, 'Access not allowed');

        $categoryId = $request->query->get('category');
        $categoryLongName = "";        
        $categoryShortName = "";        
        if ($categoryId > 0 ) {
            $page = -1;
            $questions = $questionRepository->findAllByCategories([$categoryId], $page, $this->isGranted('ROLE_TEACHER'), $this->isGranted('ROLE_ADMIN'));
            $category = $categoryRepository->find($categoryId);
            $categoryLongName = $category->getLongName();
            $categoryShortName =  $category->getShortName();
        }
        else {
            $onlyOrphan = $request->query->get('only-orphan');
            if ( ($this->isGranted('ROLE_ADMIN')) && (intval($onlyOrphan) > 0) ) {

                $RAW_QUERY = 'SELECT tbl_question.* FROM tbl_question WHERE id NOT IN (SELECT question_id as id FROM tbl_question_category);';
                $statement = $em->getConnection()->prepare($RAW_QUERY);
                $questions = $statement->executeQuery()->fetchAllAssociative();
                
                // dump($questions);
                return $this->render('question/index_onlyorphan.html.twig', ['page' => $page, 'questions' => $questions, 'category_id' => $categoryId, 'category_long_name' => $categoryLongName, 'category_short_name' => $categoryShortName]);
            } else {
                $questions = $questionRepository->findAll($page, $this->isGranted('ROLE_TEACHER'), $this->isGranted('ROLE_ADMIN'));
                return $this->render('question/index.html.twig', ['page' => $page, 'questions' => $questions, 'category_id' => $categoryId, 'category_long_name' => $categoryLongName, 'category_short_name' => $categoryShortName]);
            }                                 
        } 
    }

    /**
     * @Route("/new", name="question_new", methods="GET|POST")
     */
    public function new(Request $request, EntityManagerInterface $em, CategoryRepository $categoryRepository, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('ROLE_TEACHER', null, 'Access not allowed');

        $question = $em->getRepository(Question::class)->create();
        
        // Auto select category
        $categoryId = $request->query->get('category');
        if ($categoryId > 0) {            
            $category = $categoryRepository->find($categoryId);
        }        
        if ($categoryId > 0) {            
            $question->addCategory($category);
        }
        
        if ($this->isGranted('ROLE_ADMIN')) {
            $form = $this->createForm(QuestionType::class, $question, array('form_type'=>'admin'));
        } else {
            $form = $this->createForm(QuestionType::class, $question, array('form_type'=>'teacher'));
        }    
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $question->setCreatedBy($this->getUser());
            $em->persist($question);

            foreach ($question->getAnswers() as $answer) {
                $em->persist($answer);
            }

            $em->flush();

            //$flashMessage = sprintf($translator->trans('Question #%s is created.'), $question->getId());
            $flashMessage = sprintf($translator->trans('The question has been recorded.'));            
            if ($categoryId > 0) {     
                $categoryCount = sizeof($category->getQuestions());
                if ($categoryCount > 1) {
                    $flashMessage .= ' ' . sprintf($translator->trans('Now there are  %s questions in the category "%s".'), $categoryCount, $category->getShortname());                
                }                
            }   
            $this->addFlash('success', $flashMessage);         

            //return $this->redirectToRoute('question_index');
            $newQuestion = new Question();
            foreach ($question->getCategories() as $newCategory) {
                $newQuestion->addCategory($newCategory);
            }
            if ($this->isGranted('ROLE_ADMIN')) {
                $form = $this->createForm(QuestionType::class, $newQuestion, array('form_type'=>'admin'));
            } else {
                $form = $this->createForm(QuestionType::class, $newQuestion, array('form_type'=>'teacher'));
            }                       
        }

        return $this->render('question/new.html.twig', [
            'question' => $question,
            'category_id' => $categoryId, 
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="question_edit", methods="GET|POST")
     */
    public function edit(Request $request, Question $question, EntityManagerInterface $em, TranslatorInterface $translator): Response
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

            $this->addFlash('success', sprintf($translator->trans('Question #%s is updated.'), $question->getId()));

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
     * @Route("/{id}", name="question_delete", methods="POST")
     */
    public function delete(Request $request, Question $question, EntityManagerInterface $em, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Access not allowed');

        $categoryId = $request->query->get('category');
        
        if ($this->isCsrfTokenValid('delete'.$question->getId(), $request->request->get('_token'))) {
            $em->remove($question);
            $em->flush();

            $this->addFlash('success', sprintf($translator->trans('Question #%s is deleted.'), $question->getId()));
            $question = null;
        }

        if ($categoryId > 0) {            
            return $this->redirectToRoute('question_index', ['category' => $categoryId]);
        } else {
            return $this->redirectToRoute('question_index');
        }        
    }

    /**
     * @Route("/{id}", name="question_show", methods="GET")
     */
    public function show(Question $question): Response
    {
        $this->denyAccessUnlessGranted('ROLE_TEACHER', null, 'Access not allowed');

        return $this->render('question/show.html.twig', ['question' => $question]);
    }
        
}
