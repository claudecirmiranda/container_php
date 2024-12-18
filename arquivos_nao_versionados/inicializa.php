<?php
//Configurando o path
//Identifica o sistema operacional do servidor, considerando que pode ser Windows ou Linux
$operatingSystem = ( array_key_exists("WINDIR", $_SERVER) ) ? 'WINDOWS' : 'LINUX';

//Define a barra que ser� utilizada ao montar o path
$bar = $operatingSystem == 'WINDOWS' ? '\\' : '/';

//Define o separador de path que ser� utilizado (';' para windows e ':' para linux) bem como o caminho do projeto at� a pasta public
$raiz = $operatingSystem == 'WINDOWS' ? ';' . str_replace('/', '\\', $_SERVER['DOCUMENT_ROOT']) : ':' . $_SERVER['DOCUMENT_ROOT'];

//Como a index est� dentro de public, precisamos sempre voltar um n�vel no path atual para extrair a raiz do projeto
$raiz .= $bar . '..' . $bar;

$path = $raiz . 'application' . $bar . 'constants';
$path .= $raiz . 'application' . $bar . 'models';
$path .= $raiz . 'application' . $bar . 'models' . $bar . 'DAO';
$path .= $raiz . 'application' . $bar . 'models' . $bar . 'VOs';
$path .= $raiz . 'application' . $bar . 'models' . $bar . 'BO';
$path .= $raiz . 'application' . $bar . 'models' . $bar . 'Entidades';
$path .= $raiz . 'library';
$path .= $raiz . 'library' . $bar . 'Validate';
$path .= $raiz . 'library' . $bar . 'Frete';
$path .= $raiz . 'library' . $bar . 'boleto';


// --
// Define path to application directory
defined('APPLICATION_PATH')
        || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
        || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'desenvolvimento'));


// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
            get_include_path(),
            realpath(APPLICATION_PATH . '/../library'),
            realpath('/srv/www/librarys/ZendFramework-1.11.0/library'), //precisa incluir a library do zend
            $path,
        )));


/** Zend_Application */
require_once 'Zend/Application.php';


if (!function_exists('ctype_alnum')) {
  function ctype_alnum($text) {
    return true;//!preg_match('/^\w*$/', $text);
  }
}

// Create application, bootstrap, and run
$application = new Zend_Application(
                APPLICATION_ENV,
                APPLICATION_PATH . $bar . 'configs' . $bar . 'application.ini'
);

    $application->bootstrap()
        ->run();