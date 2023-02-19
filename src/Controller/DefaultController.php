<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{

    #[Route('/', name: 'app_home')]
    public function index()
    {
        
        if ($this->getUser()) {
            // if ( !in_array('ROLE_TEACHER', $this->getUser()->getRoles()) ) {
                return $this->redirectToRoute('quiz_index');
            // }
        }        

        return $this->render('default/index.html.twig');
    }
}
