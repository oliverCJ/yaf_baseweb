<?php
if(!extension_loaded('yaf'))die('Not Install Yaf');
define('APPLICATION_PATH', dirname(dirname(__FILE__)));
error_reporting(E_ALL);
define('DEBUG',true);
define('MB_STRING',(int)function_exists('mb_get_info'));
$application = new \Yaf\Application(APPLICATION_PATH . '/conf/application.ini');
$application->bootstrap()->run();
