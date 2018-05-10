<?php 

use \Icode\PageAdmin;
use \Icode\model\User;
use \Icode\model\Category;
use \Icode\model\Product;

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


$app->get("/admin/categories/:idcategory/products", function($idcategory){ // categorias x produtos...
	User::verifyLogin();
	$category = new Category();
	$category->get((int)$idcategory);
	$page = new PageAdmin();
	$page->setTpl("categories-products", [
		"category"=>$category->getValues(),
		"productsRelated"=>$category->getProducts(),
		"productsNotRelated"=>$category->getProducts(false)
	]);
});

$app->get("/admin/categories/:idcategory/products/:idproduct/add", function($idcategory, $idproduct){
	User::verifyLogin();
	$category = new Category();
	$category->get((int)$idcategory);
	$product = new Product();
	$product->get((int)$idproduct);
	$category->addProduct($product);
	header("Location: /admin/categories/".$idcategory."/products");
	exit;
});

$app->get("/admin/categories/:idcategory/products/:idproduct/remove", function($idcategory, $idproduct){
	User::verifyLogin();
	$category = new Category();
	$category->get((int)$idcategory);
	$product = new Product();
	$product->get((int)$idproduct);
	$category->removeProduct($product);
	header("Location: /admin/categories/".$idcategory."/products");
	exit;
});



?>