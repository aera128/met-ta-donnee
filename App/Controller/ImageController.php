<?php

namespace App\Controller;

use App\Models\User;
use Core\AuthManager;
use Core\Controller;
use Core\Request;
use Core\Response;
use Core\View;

class ImageController extends Controller
{

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

    public function showAction($url)
    {
        $meta = json_decode(shell_exec("exiftool -json -g2 images/" . $url), true);

        $meta = $meta[0];

        $lat = null;
        $long = null;
        if (isset($meta["Composite"])) {
            if ($meta["Composite"]["GPSLatitude"] !== null && $meta["Composite"]["GPSLongitude"]) {
                $lat = explode(" ", $meta["Composite"]["GPSLatitude"]);
                $lat = $lat[3];
                $lat = rtrim($lat, "\"");
                $lat = (float)$lat;

                $long = explode(" ", $meta["Composite"]["GPSLongitude"]);
                $long = $long[3];
                $long = rtrim($long, "\"");
                $long = (float)$long;
            }
        }

        if ($long !== null && $lat !== null) {
            View::renderTwig('images/show.html.twig', array(
                "url" => $url,
                "meta" => $meta,
                "lat" => $lat,
                "long" => $long
            ));
        } else {
            View::renderTwig('images/show.html.twig', array(
                "url" => $url,
                "meta" => $meta,
            ));
        }

//        dump($meta);
    }

    public function addAction()
    {
        if ($this->auth->user() === null) {
            header('Location: /devoir-idc2019/login');
        }
        View::renderTwig('images/add.html.twig');
    }

    public function pendingAction()
    {
        $image = $_FILES["image"];
        $meta = json_decode(shell_exec("exiftool -json -g0 " . $image['tmp_name']), true);
        $meta = $meta[0];
        $form_html = '<div class="row"><div class="col-md-10 mx-auto"><div id="formTabs">';

        if ($meta !== null) {
            $list_tabs = '<ul>';
            $list_tabs_views = '';

            $active = false;
            foreach ($meta as $key => $value) {
                if (!$active) {
                    $list_tabs .= '<li class="active">
                                        <a class=""
                                        href="#' . $key . 'Div">' . $key . '</a>
                                    </li>';
                    $list_tabs_views .=
                        '<div class="active" id="' . $key . 'Div">';
                    $active = true;
                } else {
                    $list_tabs .= '<li class="">
                                        <a class=""
                                        href="#' . $key . 'Div">' . $key . '</a>
                                    </li>';
                    $list_tabs_views .= '<div class="" id="' . $key . 'Div">';
                }
                $list_tabs_views .= $this->developPartExif($value);
                $list_tabs_views .= '</div>';
            }
            $list_tabs .= '</ul>';
            $form_html .= $list_tabs . $list_tabs_views;

        } else {
            $form_html .= '<div class="alert alert-warning rounded-0">Pas de métadonnées détéctées</div>';
        }
        $form_html .= '</div></div></div>';
        $form_html .= '<input type="submit" class="btn btn-outline-dark rounded-0 my-3 float-right" value="Valider et Téléverser">';
        echo $form_html;
    }

    /**
     * @return string
     */
    private function developPartExif($value, $key = null)
    {
        $html = '';
        if (is_array($value)) {
            foreach ($value as $key_bis => $value_bis) {
                if ($key !== null) {
                    $html .= '<label>' . $key . '</label>';
                }
                $html .= $this->developPartExif($value_bis, $key_bis);
            }
        } else {
            $html .= '<div class="form-group"><label>'.$key.'</label>';
            $html .= '<input type = "text" name = "' . $key . '" class="form-control form-control rounded-0" value = "' . $value . '" ></div>';
        }
        return $html;
    }

    public function uploadAction()
    {
        if ($this->request->isAjaxRequest()) {

        }
    }
}