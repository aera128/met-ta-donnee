<?php

namespace App\Controller;

use App\Utils;
use Core\Controller;
use Core\View;

class ImageController extends Controller{

    public function showAction($url){
//        Linux
//        $meta = json_decode(shell_exec("exiftool -json -g2 images/" . $url), true);

//        Windows
        $meta = json_decode(shell_exec("..\\Resources\\exiftool\\windows\\exiftool.exe -json -g2 ..\\public\\images\\" . $url), true);

        
        $meta = $meta[0];

        $lat = null;
        $long = null;
        if (isset($meta["Location"])){
            if ($meta["Location"]["GPSLatitude"] !== null && $meta["Location"]["GPSLongitude"]){
                $lat = explode(" ", $meta["Location"]["GPSLatitude"]);
                $lat= $lat[3];
                $lat = rtrim($lat, "\"");
                $lat = (float)$lat;

                $long = explode(" ", $meta["Location"]["GPSLongitude"]);
                $long= $long[3];
                $long = rtrim($long, "\"");
                $long = (float)$long;
            }
        }

        if ($long !== null && $lat !== null){
            View::renderTwig('images/show.html.twig', array(
                "url" => $url,
                "meta" => $meta,
                "lat" => $lat,
                "long" => $long
            ));
        }
        else{
            View::renderTwig('images/show.html.twig', array(
                "url" => $url,
                "meta" => $meta,
            ));
        }

//        dump($meta);
    }

    public function addAction()
    {
        Utils::connected();
        View::renderTwig('images/add.html.twig');
    }
}