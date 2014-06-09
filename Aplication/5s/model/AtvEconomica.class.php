<?php

/**
 * classe responsavel por listar o grupo de atividades economicas,
 * este grupo contem um metodo proprio, com codigos de dominios diferentes
 * segue o mesmo padrão dos dominios gerais.
 */
include_once ("../model/dominios/atividadeEconomica.php");

class ConsultaAtvEconomica {

	public $codigoCanal;
	public $usuario;

	public function __construct($codigoCanal, $usuario) {
		$this -> codigoCanal = $codigoCanal;
		$this -> usuario = $usuario;
	}

	public function atvEconomica($codigoDominio) {

		$dominio = new atividadeEconomica($this -> codigoCanal, $this -> usuario, $codigoDominio);

		$dominio -> executa();

		echo "<option value='--' selected>--</option> ";

		for ($i = 0; $i < count($dominio -> atividades); $i++) {
			$opcao = $dominio -> atividades[$i];
			$xml = simplexml_import_dom($opcao);
			echo "<option value='" . strval($xml -> codigo) . "'>" . strval($xml -> descricao) . "  </option>";
		}
		
	}

}

?>