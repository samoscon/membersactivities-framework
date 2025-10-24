<?php 
/*
 * Make sure to disable the display of errors in production code! You can enable in the test mode
 */
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

include './vendor/samoscon/membersactivities-framework/autoload.php';

controllers\Controller::run();
