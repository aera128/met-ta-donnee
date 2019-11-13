<?php

namespace App\Controller;

use App\Models\User;
use App\Utils;
use Core\AuthManager;
use Core\Controller;
use Core\Request;
use Core\Response;
use Core\View;

class ImageController extends Controller{

    public $auth;

    /**
     * ImageController constructor.
     * @param Request $request
     * @param Response $response
     */
    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
        $this->auth = new AuthManager();
    }

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
        if ($this->auth->user() === null){
            header('Location: /devoir-idc2019/login');
        }
        View::renderTwig('images/add.html.twig');
    }

    public function pendingAction(){
        dump($_FILES);
        $image = $_FILES["image"];
        $meta = json_decode(shell_exec("..\\Resources\\exiftool\\windows\\exiftool.exe -json -g1 " . $image['tmp_name']), true);
        dump($meta);
    }
}