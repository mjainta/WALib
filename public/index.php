<?php
/**
 * A sample index file which shows what needs to be done to use the WAWALib.
 *
 * @package public
 * @copyright 2013, Martin Jainta
 */

define('APPLICATION_PATH', __DIR__.'/..');

use WALib\Loader\Autoloader as Autoloader;
use WALib\Application\Application as Application;

/*
 * Debug configuration.
 */
error_reporting(E_ALL);
ini_set('display_errors', '1');

/*
 * Get config data from the config.php.
 */
$config = require_once APPLICATION_PATH.'/config.php';

require_once APPLICATION_PATH.'/src/Loader/Autoloader.php';
require_once APPLICATION_PATH.'/vendor/autoload.php';
Autoloader::getInstance()->register();
Autoloader::getInstance()->addExternalWALib('PHPMailer', APPLICATION_PATH.'/library/PHPMailer/class.phpmailer.php');

$frontController = new Application($config);
$frontController->run();

function dumpVar()
{
    echo '<pre>';

    foreach(func_get_args() as $arg)
    {
        var_dump($arg);
    }

    echo '</pre>';
}