<?php

use Core\Router;

/**
 * Autoloader Composer
 */
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
$router = new Router();

// Liste des routes disponibles
$router->add('/', ['controller' => 'accueil', 'action' => 'index']);
$router->add('/images/', ['controller' => 'image', 'action' => 'index']);

$router->dispatch($_SERVER['REQUEST_URI']);

