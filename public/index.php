<?php



use Framework\Router;

/**
 * Composer
 */
require dirname(__DIR__) . '/vendor/autoload.php';


/**
 * Error and Exception handling
 */
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');


/**
 * Routing
 */
$router = new Router();

// Liste des routes disponibles
$router->add('/', ['controller' => 'Home', 'action' => 'index']);

$router->dispatch($_SERVER['REQUEST_URI']);

