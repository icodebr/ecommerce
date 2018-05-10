<?php 
use \Icode\Page;
use \Icode\Model\category;
use \Icode\Model\Product;

//::::::::site home:::::::::::::::::

$app->get('/', function() {// Template Index do site

	$products = Product::listAll();

	$page = new Page();

	$page->setTpl("index",[
		'products'=>Product::cheklist($products)

	]);

});


//=================Categorias================================

$app->get("/categories/:idcategory", function($idcategory){

	$category = new Category();
	$category->get((int)$idcategory);

	$page = new Page();// front End

	$page->setTpl("category", [
		"category"=>$category->getValues(),
		"products"=>Product::cheklist($category->getProducts())
	]);

});


?>