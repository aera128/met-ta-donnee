<?php


namespace App\Controller;


use Core\Controller;
use Core\Response;
use Core\View;

class UserController extends Controller
{
    private $users = array(
        array(
            "username" => "niveau",
            "password" => "devoir2019"
        ),
    );

    public function loginAction()
    {
        $userData = array(
            "username" => $this->request->getPostParam("username", null),
            "password" => $this->request->getPostParam("password", null)
        );
        if(in_array(
            array(
                "username" => $userData["username"],
                "password" => $userData["password"]),
            $this->users)){

            $_SESSION["connected"] = true;
            header("Location: /devoir-idc2019/");

        }
        View::renderTwig('user/login.html.twig');
    }

    public function loginConfirmAction()
    {
        var_dump("bonjour");
    }

    public function logout()
    {

    }
}