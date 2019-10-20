<?php

namespace App\Controller;

use App\Utils;
use Core\Controller;
use Core\View;

class ImageController extends Controller{

    public function indexAction()
    {
        Utils::connected();
        View::renderTwig('images/index.html.twig');
    }
}