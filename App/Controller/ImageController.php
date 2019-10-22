<?php

namespace App\Controller;

use App\Utils;
use Core\Controller;
use Core\View;

class ImageController extends Controller{

    public function showAction($url){
        dump(json_decode(shell_exec("..\\Resources\\exiftool\\windows\\exiftool.exe -json -g2 ..\\public\\images\\".$url)));
        View::renderTwig('images/show.html.twig');
    }

    public function addAction()
    {
        Utils::connected();
        View::renderTwig('images/add.html.twig');
    }
}