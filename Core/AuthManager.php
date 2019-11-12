<?php


namespace Core;


use App\Models\User;
use PDO;

class AuthManager
{
    private $pdo;

    /**
     * AutenticationManager constructor.
     */
    public function __construct()
    {
        $this->pdo = new PDO('sqlite:../data.sqlite', null, null, array(
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ));
    }

    public function user()
    {
        if (session_status() === PHP_SESSION_NONE){
            session_start();
        }
        if (empty($_SESSION['auth'])){
            return null;
        }
        $query = $this->pdo->prepare('SELECT * FROM users WHERE id = :id');
        $query->execute(['id' => $_SESSION['auth']]);
        $user = $query->fetchObject(User::class);
        return $user;
    }

    public function login($username, $password)
    {
        if ($username === null || $password === null) {
            return null;
        }

        $query = $this->pdo->prepare('SELECT * FROM users WHERE username = :username');
        $query->execute(['username' => $username]);
        $user = $query->fetchObject(User::class);
        if ($user === false) {
            return null;
        }
        if (password_verify($password, $user->password)) {
            if (session_status() === PHP_SESSION_NONE){
                session_start();
            }
            $_SESSION['auth'] = $user->id;
            return $user;
        }
        return null;
    }

}