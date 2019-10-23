<?php

namespace App\Controller;

use App\Utils;
use Core\Controller;
use Core\View;

class ImageController extends Controller{

    public function showAction($url){
        $meta = json_decode(shell_exec("..\\Resources\\exiftool\\windows\\exiftool.exe -json -g2 ..\\public\\images\\" . $url), true);
        View::renderTwig('images/show.html.twig', array(
            "url" => $url,
            "meta" => $meta[0],
        ));
        dump($meta[0]);
    }

    public function addAction()
    {
        Utils::connected();
        View::renderTwig('images/add.html.twig');
    }
}