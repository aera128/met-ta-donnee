<?php

namespace App\Controller;

use App\Models\User;
use Core\AuthManager;
use Core\Controller;
use Core\Request;
use Core\Response;
use Core\View;
use Error;
use Monolog\Logger;
use PHPExiftool\Driver\Metadata\Metadata;
use PHPExiftool\Driver\Metadata\MetadataBag;
use PHPExiftool\Driver\Tag\IPTC\ObjectName;
use PHPExiftool\Driver\Value\Mono;
use PHPExiftool\Writer;

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
        $meta["G0"] = json_decode(shell_exec("exiftool -json -g0 images/" . $url), true)[0];
        $meta["G1"] = json_decode(shell_exec("exiftool -json -g1 images/" . $url), true)[0];
        $meta["G2"] = json_decode(shell_exec("exiftool -json -g2 images/" . $url), true)[0];
        $meta["G3"] = json_decode(shell_exec("exiftool -json -g2 images/" . $url), true)[0];
        $meta["G4"] = json_decode(shell_exec("exiftool -json -g2 images/" . $url), true)[0];

        $meta = array_merge($meta["G0"], $meta["G1"], $meta["G2"], $meta["G3"], $meta["G4"]);

        unset(
            $meta["SourceFile"],
            $meta["ExifTool"],
        );

        $lat = null;
        $long = null;
        if (isset($meta["Location"]) && isset($meta["Location"]["GPSLatitude"]) && isset($meta["Location"]["GPSLongitude"])) {
            if ($meta["Location"]["GPSLatitude"] !== null && $meta["Location"]["GPSLongitude"]) {
                $lat = explode(" ", $meta["Location"]["GPSLatitude"]);
                $lat = $lat[3];
                $lat = rtrim($lat, "\"");
                $lat = (float)$lat;

                $long = explode(" ", $meta["Location"]["GPSLongitude"]);
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
            exit();
        }
        View::renderTwig('images/add.html.twig');
    }

    public function editAction($url)
    {
        if ($this->auth->user() === null) {
            header('Location: /devoir-idc2019/login');
            exit();
        }

        $meta["G0"] = json_decode(shell_exec("exiftool -json -g0 images/" . $url), true)[0];
        $meta["G1"] = json_decode(shell_exec("exiftool -json -g0 images/" . $url), true)[0];
        $meta["G2"] = json_decode(shell_exec("exiftool -json -g0 images/" . $url), true)[0];
        $meta["G3"] = json_decode(shell_exec("exiftool -json -g0 images/" . $url), true)[0];
        $meta["G4"] = json_decode(shell_exec("exiftool -json -g0 images/" . $url), true)[0];

        $meta = array_merge($meta["G0"], $meta["G1"], $meta["G2"], $meta["G3"], $meta["G4"]);

        $path = $meta['SourceFile'];

        $form_html = '<div class="row"><div class="col-md-10 mx-auto"><div id="formTabs">';

        if ($meta !== null) {
            $list_tabs = '<ul>';
            $list_tabs_views = '';
            $active = false;
            foreach ($meta as $key => $value) {
                if ($key !== "SourceFile" && $key !== "ExifTool" && $key !== "File") {
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
                    $list_tabs_views .= $this->developPartExif($value, $key, $key);
                    $list_tabs_views .= '</div>';
                }
            }
            $list_tabs .= '</ul>';
            $form_html .= $list_tabs . $list_tabs_views;

        } else {
            $form_html .= '<div class="alert alert-warning rounded-0">Pas de métadonnées détectées</div>';
        }
        $form_html .= '</div></div></div>';
        $form_html .= '<input type="submit" class="btn btn-outline-dark rounded-0 my-3 float-right" value="Valider et Téléverser">';
        View::renderTwig('images/edit.html.twig', array(
            "form" => $form_html,
            "url" => $url,
            "path" => $path
        ));
    }

    public function editAjaxAction()
    {
        $data = $this->request->getAllPostParams();
        $source = $data['SourceFile'];
        $last_key = '';
        $meta = array();
        $meta['SourceFile'] = $source;
        foreach ($data as $key => $value) {
            $keys = explode("::", $key);
            if (count($keys) > 1) {
                $meta[$keys[0]][$keys[1]] = $value;
            } else {
                $meta[$keys[0]] = $value;
            }
        }
        $meta = '[' . json_encode($meta) . ']';
        $fileName = uniqid('', true) . '.json';
        $fp = fopen($fileName, 'w');
        fwrite($fp, $meta);
        fclose($fp);

        shell_exec('exiftool -overwrite_original -json=' . $fileName . ' ' . $source);

        unlink($fileName);
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
                if ($key === "XMP" || $key === "EXIF" || $key === "IPTC") {
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
                    $list_tabs_views .= $this->developPartExif($value, $key, $key);
                    $list_tabs_views .= '</div>';
                }
            }
            $list_tabs .= '</ul>';
            $form_html .= $list_tabs . $list_tabs_views;

        } else {
            $form_html .= '<div class="alert alert-warning rounded-0">Pas de métadonnées détectées</div>';
        }
        $form_html .= '</div></div></div>';
        $form_html .= '<input type="submit" class="btn btn-outline-dark rounded-0 my-3 float-right" value="Valider et Téléverser">';
        echo $form_html;
    }

    /**
     * @return string
     */
    private function developPartExif($value, $key, $tab, $parent = null, $index = 0)
    {
        $html = '';
        if (is_array($value)) {
            foreach ($value as $key_bis => $value_bis) {
                $html .= $this->developPartExif($value_bis, $key_bis, $tab, $key, $index + 1);
            }
        } else {
            if ($index > 1) {
                $html .= '<div class="form-group"><label>' . $tab . ':' . $parent . '::' . $key . '</label>';
                $html .= '<input type = "text" name = "' . $tab . ':' . $parent . '::' . $key . '" class="form-control form-control rounded-0" value = "' . $value . '" ></div>';

            } else {
                $html .= '<div class="form-group"><label>' . $tab . ':' . $key . '</label>';
                $html .= '<input type = "text" name = "' . $tab . ':' . $key . '" class="form-control form-control rounded-0" value = "' . $value . '" ></div>';
            }
        }
        return $html;
    }

    public function uploadAction()
    {
        if ($this->request->isAjaxRequest()) {
            $data = array();
            $data['SourceFile'] = $_FILES['file']['tmp_name'];

            $last_key = '';
            foreach ($this->request->getAllPostParams() as $key => $value) {
                $keys = explode("::", $key);
                if (count($keys) > 1) {
                    $data[$keys[0]][$keys[1]] = $value;
                } else {
                    $data[$keys[0]] = $value;
                }
            }

            $data = '[' . json_encode($data) . ']';
            $fileName = uniqid('', true) . '.json';
            $fp = fopen($fileName, 'w');
            fwrite($fp, $data);
            fclose($fp);

            shell_exec('exiftool -overwrite_original -json=' . $fileName . ' ' . $_FILES['file']['tmp_name']);

            unlink($fileName);
            move_uploaded_file($_FILES['file']['tmp_name'], 'images/' . $_FILES['file']['name']);
        }
    }

    public function deleteAction($url)
    {
        unlink('images/' . $url);
        header('Location: /devoir-idc2019/');
        exit();
    }
}