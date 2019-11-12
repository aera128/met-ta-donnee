<?php

namespace App\Controller;

use App\Config;
use Core\AuthManager;
use Core\Controller;
use Core\View;
use PDO;

class AccueilController extends Controller
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
