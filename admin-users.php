<?php 
use \Icode\PageAdmin;
use \Icode\model\User;

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


 ?>