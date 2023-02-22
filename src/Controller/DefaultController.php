<?php

namespace App\Controller;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{

    #[Route('/', name: 'app_home')]
    public function index()
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('quiz_index');
        }        

        return $this->render('default/index.html.twig');
    }
}
