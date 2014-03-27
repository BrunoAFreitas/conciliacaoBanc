<?php

/**
 * Classe que cria o uma lista com os dominios em uma tag html
 * para ser usada no cadastro de clientes, afim de enviar para
 * o banco de dados os memsmo valores usados no financiamento
 *
 * foi necessario retirar o get e o set dessa class, apenas para
 * deixar o codigo mais limpo
 */
//include_once ("dominio.php");
include_once ("../controller/dominios/dominio.php");

class ConsultaDom {

	public $codigoCanal;
	public $usuario;

	public function __construct($codigoCanal, $usuario) {
			$this -> codigoCanal = $codigoCanal;
			$this -> usuario = $usuario;
	}

	public function dominio($codigoDominio) {

		$dominio = new dominio($this -> codigoCanal, $this -> usuario, $codigoDominio);
		$dominio -> executa();

		echo   "<option value='--' selected>--</option> ";
		
		for ($i = 0; $i < count($dominio -> dominios); $i++) {
			$opcao = $dominio -> dominios[$i];
			$xml = simplexml_import_dom($opcao);
			echo  "<option value='" . strval($xml -> codigo) . "'>" . strval($xml -> descricao) . "  </option>";
		}	
	}
}

?>


