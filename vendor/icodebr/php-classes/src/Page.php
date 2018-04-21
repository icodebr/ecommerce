<?php
namespace Icode;

use Rain\Tpl;

class Page {

	private $tpl;
	private $options =[];
	private $defaults = [
		"header"=>true,
		"footer"=>true,
		"data"=>[]
	];


	public function __construct($opts = array(), $tpl_dir = "/views/"){

		$this->options = array_merge($this->defaults, $opts);

		$config = array(
		    "tpl_dir"       => $_SERVER['DOCUMENT_ROOT'].$tpl_dir,
		    "cache_dir"     => $_SERVER['DOCUMENT_ROOT']."/views-cache/",
		    "debug"         => false
		);

		Tpl::configure( $config );

		$this->tpl = new Tpl; // estancia o  Tpl(template)			

		$this->setData($this->options['data']);

		if($this->options["header"] === true) $this->tpl->draw("header"); // se for true inclui na pagina o (header)

	}

	// metodo para pegar os dados template	
	private function setData($data = array())
	{
		foreach ($data as $key => $value) {
			$this->tpl->assign($key, $value);
		}


	}

	// incluir template na tela
	public function setTpl($nome,$data = array(),$returnHtml = false)
	{

		$this->setData($data);
		return $this->tpl->draw($nome, $returnHtml);
	}


	public function __destruct(){

		if($this->options["footer"] === true) $this->tpl->draw("footer");// ser options for true ,inclui o footer.
	}
}



?>