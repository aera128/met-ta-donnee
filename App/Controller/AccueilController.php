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

        foreach ($images as $image){
            $path = '../public/images/'.$image;
            $path_thumb500 = '../public/thumbnails500/'.$image;
            $path_thumb300 = '../public/thumbnails300/'.$image;
            if (!file_exists($path_thumb500) && !file_exists($path_thumb300)){
                $img_new = imagecreatefromstring(file_get_contents($path));
                $size = getimagesize($path);
                $img_mini = imagecreatetruecolor (500,$size[1]/($size[0]/500));
                imagecopyresampled ($img_mini,$img_new,0,0,0,0,500,$size[1]/($size[0]/500),$size[0],$size[1]);
                imagejpeg($img_mini, $path_thumb500,90);

                $img_mini = imagecreatetruecolor (300,$size[1]/($size[0]/300));
                imagecopyresampled ($img_mini,$img_new,0,0,0,0,300,$size[1]/($size[0]/300),$size[0],$size[1]);
                imagejpeg($img_mini, $path_thumb300,90);
            }
        }

        View::renderTwig('accueil/index.html.twig', array(
            'images' => $images
        ));
    }

    public function aboutAction(){
        View::renderTwig('accueil/about.html.twig');
    }
}
