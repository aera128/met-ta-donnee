<?php

namespace App\Controllers;

use App\Config;
use Core\Controller;
use Core\View;

class Accueil extends Controller
{

    public function indexAction()
    {
        $images = scandir("../public/images/");
        $images = array_diff($images, ['.','..']);

        View::renderTwig('accueil/index.html.twig', array(
            'images' => $images
        ));
    }
}
