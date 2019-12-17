<?php
putenv('HTTPS_PROXY=https://proxy.unicaen.fr:3128');

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
if (!empty($_GET["url"])) {
    $url = $_GET["url"];
}


/**
 * Router
 */
$router = new Router($url);

// Liste des routes disponibles
$router->get('/', 'Accueil::index');

$router->get('/image/add', 'Image::add');
$router->get('/image/:url', 'Image::show');
$router->get('/image/:url/edit', 'Image::edit');
$router->get('/image/:url/delete', 'Image::delete');
$router->post('/ajax/image-pending', 'Image::pending');
$router->post('/ajax/upload', 'Image::upload');
$router->post('/ajax/edit', 'Image::editAjax');


$router->get('/login', 'User::login');
$router->post('/ajax/login', 'User::loginAjax');

$router->get('/logout', 'User::logout');
//$router->get('/createdb', 'User::createdatabase');
try {
    $router->run();
} catch (RouterException $e) {

}
