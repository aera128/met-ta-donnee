<?php

namespace App\Controllers;

use App\Config;
use Core\Controller;
use Core\View;

class Accueil extends Controller
{

    public function indexAction()
    {
        dump(glob("../".Config::IMG_DIR."/*"));
        View::renderTwig('accueil/index.html.twig');
    }
}
