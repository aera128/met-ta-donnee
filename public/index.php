<?php
session_start();

/**
 * Autoloader Composer
 */

use Core\Router\Router;

require dirname(__DIR__) . '/vendor/autoload.php';

/**
 * Handler des erreurs
 */
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');

/**
 * Router
 */
$router = new Router($_GET['url']);

// Liste des routes disponibles
$router->get('/', 'Accueil::index');
$router->get('/images', 'Image::index');

$router->get('/login', 'User::login');
$router->post('/login', 'User::login');

try {
    $router->run();
} catch (\Core\Router\RouterException $e) {

}

