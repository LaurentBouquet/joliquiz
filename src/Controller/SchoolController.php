<?php

namespace App\Controller;

use App\Entity\School;
use App\Form\School1Type;
use App\Repository\SchoolRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/school")
 */
class SchoolController extends AbstractController
{
    /**
     * @Route("/", name="school_index", methods={"GET"})
     */
    public function index(SchoolRepository $schoolRepository): Response
    {
        return $this->render('school/index.html.twig', [
            'schools' => $schoolRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="school_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $school = new School();
        $form = $this->createForm(School1Type::class, $school);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($school);
            $entityManager->flush();

            return $this->redirectToRoute('school_index');
        }

        return $this->render('school/new.html.twig', [
            'school' => $school,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="school_show", methods={"GET"})
     */
    public function show(School $school): Response
    {
        return $this->render('school/show.html.twig', [
            'school' => $school,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="school_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, School $school): Response
    {
        $form = $this->createForm(School1Type::class, $school);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('school_index');
        }

        return $this->render('school/edit.html.twig', [
            'school' => $school,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="school_delete", methods={"POST"})
     */
    public function delete(Request $request, School $school): Response
    {
        if ($this->isCsrfTokenValid('delete'.$school->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($school);
            $entityManager->flush();
        }

        return $this->redirectToRoute('school_index');
    }
}
