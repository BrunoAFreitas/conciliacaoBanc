<?php

include_once ('../controller/GlobalConstants.php');
include_once ('../controller/soapSantander.php');
include_once ('../controller/carregaMetodo.php');


class atividadeEconomica extends carregaMetodo {

	public $codigoErro, $descricaoErro, $codigoRetorno, $dadosADP, $atividades;
	private $campos;

	public function __construct($codigoCanal, $usuario, $codigoGrupoAtividade) { $this -> campos = get_defined_vars();
	}

	protected function getDados() {

		//global $global;

		$this -> soapSantander -> setStrImplementacao("listarAtividadeEconomica");
		while (list($key, $val) = each($this -> campos)) {  $this -> soapSantander -> setParametro($key, $val);
		}
		$this -> soapSantander -> consomeWebService(enderecoEndPointDominio);

	}

	protected function trataRetorno() {

		$x = $this -> soapSantander -> toArray("dominioAtividadeEconomicaResponse");

		$this -> codigoErro = $x[0] -> codigoErro;
		$this -> descricaoErro = $x[0] -> descricaoErro;
		$this -> codigoRetorno = $x[0] -> codigoRetorno;
		$this -> atividades = $x[0] -> dominios -> opcoes;

	}

}
?>