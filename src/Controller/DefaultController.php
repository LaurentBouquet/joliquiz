<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/")
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        if ($this->getUser()) {
            // if (count($this->getUser()->getRoles()) < 2) {
                return $this->redirectToRoute('quiz_index');
            // }
        }

        return $this->render('default/index.html.twig');
    }
}
