<?php 
session_start();

require_once("vendor/autoload.php");

use \Slim\Slim;
use \Icode\Page;
use \Icode\PageAdmin;
use \Icode\model\User;
use \Icode\model\Category;


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


$app->get('/admin/users', function() {// Template Index do site

	User::verifyLogin();

	$users =  User::listALL();//lista todos usuarios..

	$page = new PageAdmin();

	$page->setTpl("users",array(
		"users"=>$users
	));

});

$app->get('/admin/users/create', function() {// create

	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("users-create");

});

$app->get("/admin/users/:iduser/delete", function($iduser) {

	User::verifyLogin();

	$user = new User();

	$user->get((int)$iduser);

	$user->delete();
	header("location: /admin/users");
	exit;


});

$app->get('/admin/users/:iduser', function($iduser) {//update

	User::verifyLogin();

		$user = new User();

	$user->get((int)$iduser);

	$page = new PageAdmin();

	$page->setTpl("users-update", array(
		"user"=>$user->getValues()

	));

});

$app->post('/admin/users/create', function() {

	User::verifyLogin();

	$user = new User();

	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;

	$user->setData($_POST);// manda o array com os dados do formulario;

	$user->save();

	header("location: /admin/users");
	exit;
});

$app->post('/admin/users/:iduser', function($iduser) {

	User::verifyLogin();

	$user = new User();
	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;
    $user->get((int)$iduser);// busca no bd os registros
    $user->setData($_POST);// busca os dados do formulario
    $user->update(); // executa a alteração

    header("location: /admin/users");
	exit;
});



$app->get('/admin/forgot', function(){

	$page = new PageAdmin([//desabilitando  header  e footer
		"header"=>false,
		"footer"=>false 
	]);

	$page->setTpl("forgot");
});

$app->post("/admin/forgot",function(){
	
  	$user = User::getForgot($_POST["email"]);

  	header("location: /admin/forgot/sent");
  	exit;

});


$app->get("/admin/forgot/sent", function(){

	$page = new PageAdmin([//desabilitando  header  e footer
		"header"=>false,
		"footer"=>false 
	]);

	$page->setTpl("forgot-sent");

});

$app->get("/admin/forgot/reset", function(){

	$user = User::validForgotDecrypt($_GET['code']); // recupera os dados do usuario


	$page = new PageAdmin([//desabilitando  header  e footer
		"header"=>false,
		"footer"=>false 
	]);

	$page->setTpl("forgot-reset", array(
		"name"=>$user["desperson"],
		"code"=>$_GET["code"]
	));
});

$app->post("/admin/forgot/reset", function(){

	$forgot = User::validForgotDecrypt($_POST["code"]); // recupera os dados do usuario

	User::setForgotUser($forgot["idrecovery"]);

	
	$user = new User();

	$user->get((int)$forgot["iduser"]);
	

	$password = password_hash($_POST["password"], PASSWORD_DEFAULT, [
		"cost"=>12
	]);


	$user->setPassword($password);

	$page = new PageAdmin([//desabilitando  header  e footer
		"header"=>false,
		"footer"=>false 
	]);

	$page->setTpl("forgot-reset-success");

});


$app->get("/admin/categories", function(){

	User::verifyLogin();

	$categories = Category::listALL();

	$page = new PageAdmin();

	$page->setTpl("categories", [
		"categories"=>$categories
	]);

});



$app->get("/admin/categories/create", function(){

	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("categories-create");

});

$app->post("/admin/categories/create", function(){

	User::verifyLogin();

	$category = new Category();

	$category->setData($_POST);// pega o array com dados da categoria e seta os mesmos.

    $category->save();

    header("location: /admin/categories");
    exit;
});

$app->get("/admin/categories/:idcategory/delete", function($idcategory){

	User::verifyLogin();

	$category = new Category();

	$category->get((int)$idcategory);

	$category->delete();

	header("location: /admin/categories");
    exit;
});


$app->get("/admin/categories/:idcategory", function($idcategory){

	User::verifyLogin();

	$category = new Category();

	$category->get((int)$idcategory);

	$page = new PageAdmin();

	$page->setTpl("/admin/categories-update", [

		"category"=>$category->getValues()
	]);

});

	$app->post("/admin/categories/:idcategory", function($idcategory){

	User::verifyLogin();

	$category = new Category();

	$category->get((int)$idcategory);

	$category->setData($_POST);// pega o array com dados da categoria e seta os mesmos.

    $category->save();

    header("location: /admin/categories");
    exit;
});






$app->run();


?>