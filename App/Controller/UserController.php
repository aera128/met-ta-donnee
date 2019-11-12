<?php


namespace App\Controller;


use Core\AuthManager;
use Core\Controller;
use Core\Request;
use Core\Response;
use Core\View;
use PDO;

class UserController extends Controller
{
    private $auth;
    /**
     * UserController constructor.
     * @param Request $request
     * @param Response $response
     */
    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
        $this->auth = new AuthManager();
    }

    public function loginAction()
    {
        if ($this->auth->user() !== null){
            header('Location: /devoir-idc2019');
        }
        View::renderTwig('user/login.html.twig');
    }

    public function loginAjaxAction(){
        $formData = array(
            "username" => $this->request->getPostParam("username", null),
            "password" => $this->request->getPostParam("password", null)
        );

        $user = $this->auth->login($formData['username'], $formData['password']);
        if ($user !== null){
            echo 'success';
            exit();
        }
        echo 'error';
    }

    public function logoutAction()
    {
        unset($_SESSION['auth']);
        header("Location: /devoir-idc2019/");
    }

//    public function createdatabaseAction(){
//        $pdo = new PDO("sqlite:../data.sqlite", null, null, [
//            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
//        ]);
//        $pdo->query("DELETE FROM users");
//        $pdo->query("DELETE from sqlite_sequence where name='users';");
//        $query = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
//        $query->execute([
//            username,
//            password_hash(password, PASSWORD_BCRYPT),
//            role,
//        ]);
//        echo "done";
//    }
}