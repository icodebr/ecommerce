<?php 
session_start();

require_once("vendor/autoload.php");

use \Slim\Slim;
use \Icode\Page;
use \Icode\PageAdmin;
use \Icode\model\User;


$app = new Slim();

$app->config('debug', true);

//::::::::Rotas:::::::::::::::::

$app->get('/', function() {// Template Index do site

	$page = new Page();

	$page->setTpl("index");

});

$app->get('/admin', function() {// Template Index do site

	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("index");

});

$app->get('/admin/login', function(){


	$page = new PageAdmin([//desabilitando  header  e footer
		"header"=>false,
		"footer"=>false 
	]);

	$page->setTpl("login");
});



$app->post('/admin/login', function(){


	User::login($_POST["login"],$_POST['password']);

	header("location: /admin");
	exit;

});

$app->get('/admin/logout', function(){

	User::logout();

	header("location: /admin/login");
	exit;
});

$app->run();


?>