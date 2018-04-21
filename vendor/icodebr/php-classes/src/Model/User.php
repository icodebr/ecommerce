<?php 

namespace Icode\Model;

use  \Icode\DB\Sql;
use  \Icode\Model;

 

class User extends Model {
	const 	SESSION = "User";

	public function login($login, $password)
	{

		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_users WHERE deslogin = :LOGIN", array(

			":LOGIN" =>$login
		));


		if(count($results)===0)
		{
			throw new \Exception("Usuário inexistente ou Senha invalida");
			
		}

		$data = $results[0];

		if(password_verify($password, $data["despassword"]) === true )
		{

			$user = new User();

			$user->setData($data);

			$_SESSION[User::SESSION] = $user->getValues();

			return $user;

		} else {

			throw new \Exception("Usuário inexistente ou Senha invalida");
			
		}
	}

	public static function verifyLogin($inadmin = true)
	{
		if(
			!isset($_SESSION[User::SESSION]) // senao existir a sessão
			|| !$_SESSION[User::SESSION]//sessao estiver nula
			|| !(int)$_SESSION[User::SESSION]["iduser"] >0 // se for menor que zero
			||	(bool)$_SESSION[User::SESSION]["inadmin"] !== $inadmin
		)
	    {
			header("location: /admin/login");
			exit;
		}
	}

	public static function logout()
	{

		$_SESSION[User::SESSION] = NULL;
	}


}

 ?>