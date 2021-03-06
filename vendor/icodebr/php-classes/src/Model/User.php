<?php 

namespace Icode\Model;

use  \Icode\DB\Sql;
use  \Icode\Model;
use  \Icode\Mailer;


class User extends Model {
	const SESSION = "User";
	const SECRET = "123";

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

	public static function listAll()
	{

		$sql = new Sql();

		return $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) ORDER BY b.desperson");

	}

	public static function getPasswordHash($password)
	{
		return password_hash($password, PASSWORD_DEFAULT, [
			'cost'=>12
		]);
	}

	public function Save()
	{
		$sql = new sql();

		$results = $sql->select("CALL sp_users_save(:desperson, :deslogin, :despassword,  :desemail, :nrphone, :inadmin)", array(
			":desperson"=>$this->getdesperson(),
			":deslogin"=>$this->getdeslogin(),
			":despassword"=>User::getPasswordHash($this->getdespassword()),
			":desemail"=>$this->getdesemail(),
			":nrphone"=>$this->getnrphone(),
			":inadmin"=>$this->getinadmin()	
			));

		$this->setData($results[0]);
	}

	public function get($iduser)
	{
		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) WHERE a.iduser = :iduser", 
			array(":iduser"=>$iduser
		));

		$this->setData($results[0]);
	}

	public function update()
	{
				$sql = new sql();

		$results = $sql->select("CALL sp_usersupdate_save(:iduser,:desperson, :deslogin, :despassword,:desemail, :nrphone, :inadmin)", array(
			":iduser"=>$this->getiduser(),
			":desperson"=>$this->getdesperson(),
			":deslogin"=>$this->getdeslogin(),
			":despassword"=>$this->getdespassword(),
			":desemail"=>$this->getdesemail(),
			":nrphone"=>$this->getnrphone(),
			":inadmin"=>$this->getinadmin()	
		));

		$this->setData($results[0]);
	}

	public function delete()
	{
		$sql = new Sql();
		$sql->query("CALL sp_users_delete(:iduser)", array(

			":iduser"=>$this->getiduser()
		));

	}

	public static function getForgot($email)
	{
		$sql = new Sql();
		$results = $sql->select("
			SELECT * 
			FROM tb_persons a
			INNER JOIN tb_users b USING(idperson)
			WHERE a.desemail = :email;
		", array(
			":email"=>$email
		));

		if (count($results) ===0){

			throw new \Exception("Não foi possivel recuperar a senha.");			
		}
		else
		{
			$data = $results[0];
			$res_recovery =  $sql->select("CALL sp_userspasswordsrecoveries_create(:iduser, :desip)", array(
				":iduser"=>$data["iduser"],
				":desip"=>$_SERVER["REMOTE_ADDR"]
			));

			if(count($res_recovery) === 0)
			{
				
				throw new \Exception("Não foi possivel recuperar a senha.");

			}
			else				
			{
				$dataRecovery = $res_recovery[0];

				$code = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, User::SECRET, $dataRecovery["idrecovery"], MCRYPT_MODE_ECB)); // criptografia

				$link = "http://www.icodecommerce.com.br/admin/forgot/reset?code=$code";

				$mailer = new Mailer($data["desemail"], $data["desperson"], "Redefinir Senha da Icode Store","forgot", array(
							"name"=>$data["desperson"],
							"link"=>$link
				));

				$mailer->send();
				return $data;
			}
		}

	}
	public static function  validForgotDecrypt($code)
	{

		$idrecovery = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, User::SECRET, base64_decode($code), MCRYPT_MODE_ECB); //decriptando o id;

		$sql = new Sql();
		$results = $sql->select("SELECT *
		 	FROM tb_userspasswordsrecoveries a 
		 	INNER JOIN  tb_users b USING(iduser)
		 	INNER JOIN tb_persons c USING(idperson)
		 	WHERE
		 		a.idrecovery = :idrecovery
		 		AND
		 		a.dtrecovery IS NULL
		 		AND
		 		DATE_ADD(a.dtregister, INTERVAL 1 HOUR) >= NOW();
			", array(
				":idrecovery"=>$idrecovery
		));

		if(count($results) === 0)
		{
			throw new \Exception("Não foi possivel recuperar a senha.");
		}
		else
		{
			return $results[0];
		}

	}

	public static function setForgotUser($idrecovery)
	{
		$sql = new Sql();

		$sql->query("UPDATE tb_userspasswordsrecoveries set dtrecovery = NOW() WHERE idrecovery = :idrecovery", array(
			"idrecovery"=>$idrecovery
		));
	}


	public function setPassword($password)
	{

		$sql = new Sql();

		$sql->query("UPDATE tb_users set despassword = :password WHERE iduser = :iduser ", array(
			":password"=>$password,
			":iduser"=>$this->getiduser()
		));


	}
}

?>