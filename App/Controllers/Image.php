<?php

namespace App\Controllers;

use Core\Controller;
use Core\View;

class Image extends Controller{

    public function indexAction()
    {
        View::renderTwig('images/index.html.twig');
    }
}