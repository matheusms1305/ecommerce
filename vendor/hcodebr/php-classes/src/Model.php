<?php 

namespace Hcode;

class Model {
	//todas nossas classes dao vao extender a classe model que vai criar dinamicamente os metodos get e set 
	//todos os dados dos campos do objeto
	private $values = [];

	//metodo magico: ele recebe o nome do metodo chamado (get ou set) e os parametros passados
	public function __call($name, $args)
	{

		$method = substr($name, 0, 3); //traz os tres primeiros campos
		$fieldName = substr($name, 3, strlen($name));// pega o restante a partir do 3 até o final

		switch ($method)
		{

			case "get": //retorna a informação
				return (isset($this->values[$fieldName])) ? $this->values[$fieldName] : NULL;
			break;

			case "set": // vamos setar a informação
				$this->values[$fieldName] = $args[0];
			break;

		}

	}
	//cria as variaveis automatiamente pro DAO
	public function setData($data = array())
	{

		foreach ($data as $key => $value) {
			
			$this->{"set".$key}($value);

		}

	}

	public function getValues()
	{

		return $this->values;

	}

}

 ?>