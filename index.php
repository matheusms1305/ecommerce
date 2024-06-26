<?php 
session_start();

require_once("vendor/autoload.php");

use \Slim\Slim;

$app = new Slim();

$app->config('debug', true);

require_once("functions.php");

require_once("admin_site.php");

require_once("admin.php");

require_once("admin_user.php");

require_once("admin_categories.php");

require_once("admin_products.php");

$app->run();

?>