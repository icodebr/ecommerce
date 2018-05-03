<?php 
use \Icode\Page;
use \Icode\Model\category;

//::::::::site home:::::::::::::::::

$app->get('/', function() {// Template Index do site

	$page = new Page();

	$page->setTpl("index");

});


//=================Categorias================================

$app->get("/categories/:idcategory", function($idcategory){

	$category = new Category();
	$category->get((int)$idcategory);

	$page = new Page();// front End

	$page->setTpl("category", [
		"category"=>$category->getValues(),
		"products"=>[]
	]);

});



?>