<?php

namespace App\Controller;

use Core\Controller;
use Core\View;

class AccueilController extends Controller
{

    public function indexAction()
    {
        $images = scandir("../public/images/");
        $images = array_diff($images, ['.', '..']);

        View::renderTwig('accueil/index.html.twig', array(
            'images' => $images
        ));
    }
}
