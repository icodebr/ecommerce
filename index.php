<?php 

require_once("vendor/autoload.php");

use \Slim\Slim;
use \Icode\Page;
use \Icode\PageAdmin;

$app = new Slim();

$app->config('debug', true);

//::::::::Rotas:::::::::::::::::

$app->get('/', function() {// Template Index do site

	$page = new Page();

	$page->setTpl("index");

});


$app->get('/admin', function() {// Template Admin

	$page = new PageAdmin();

	$page->setTpl("index");

});


$app->run();

?>