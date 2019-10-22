<?php
session_start();

/**
 * Autoloader Composer
 */

use Core\Router\Router;
use Core\Router\RouterException;

require dirname(__DIR__) . '/vendor/autoload.php';

/**
 * Handler des erreurs
 */
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');

$url = "/";
if(!empty($_GET["url"])){
    $url = $_GET["url"];
}



/**
 * Router
 */
$router = new Router($url);

// Liste des routes disponibles
$router->get('/', 'Accueil::index');
$router->get('/image/:url', 'Image::show');

$router->get('/login', 'User::login');
$router->post('/login', 'User::login');

$router->get('/logout', 'User::logout');

try {
    $router->run();
} catch (RouterException $e) {

}

