<?php

namespace App\Controllers;

use Framework\Controller;
use Framework\View;

class Home extends Controller
{

    public function indexAction()
    {
        View::renderTemplate('Home/index.html.twig');
    }
}
