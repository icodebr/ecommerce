<?php 

use \Icode\PageAdmin;
use \Icode\model\User;
use \Icode\model\Category;

// ======================ROTAS CATEGORIAS========================

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





?>