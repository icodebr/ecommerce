<?php 

namespace Icode\Model;

use  \Icode\DB\Sql;
use  \Icode\Model;
use  \Icode\Mailer;


class Category extends Model {
	

	
	public static function listAll()
	{

		$sql = new Sql();

		return $sql->select("SELECT * FROM tb_categories ORDER BY descategory");

	}

	public function save()
	{
		$sql = new sql();

		$results = $sql->select("CALL sp_categories_save(:idcategory,:descategory)", array(
			":idcategory"=>$this->getidcategory(),
			":descategory"=>$this->getdescategory()			
			));

		$this->setData($results[0]);
	}

	public function get($idcategory)
	{

		$sql = new Sql();

		$results = $sql->select("SELECT  * FROM  tb_categories WHERE idcategory = :idcategory", [
			":idcategory"=>$idcategory
		]);

		$this->setData($results[0]);
		Category::updateFile();
			
	}

	public function delete()
	{

		$sql = new Sql();

		$sql->query("DELETE FROM  tb_categories WHERE idcategory = :idcategory", [
			":idcategory"=>$this->getidcategory()
		]);	
		Category::updateFile();	
	}




	public static function updateFile()
	{
		$categories = Category::listALL();

		$html = [];

		foreach ($categories as $key => $row) {

			array_push($html, '<li><a href="/categories/'.$row['idcategory'].'">'.$row['descategory'].'</a></li>');			
		}

		file_put_contents($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR. "views" .DIRECTORY_SEPARATOR . "categories-menu.html", implode('', $html));
	}

	
}

?>