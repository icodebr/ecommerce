<?php 

use \Icode\PageAdmin;
use \Icode\model\User;
use \Icode\model\Category;

//===================================ROTAS ADMIN==========================================

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




 ?>