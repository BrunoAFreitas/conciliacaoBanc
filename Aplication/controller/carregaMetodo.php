<?php
/**
 * CARREGA METODO
 * 
 * CLASSE PACOTE CONTROLLER
*/
class carregaMetodo {

	protected $userName, $token, $cnpj, $cogidoGrupoCanal, $nrIntermediario;
	protected $soapSantander;

	public function __construct() {

	}

	protected function getDados() {
	}

	protected function trataRetorno() {
	}

	public function executa($userName = null, $token = null, $cnpj = null, $cogidoGrupoCanal = null, $nrIntermediario = null) {

		$this -> userName = $userName ? $userName : usuario;
		$this -> token = $token ? $token : Key;
		$this -> cnpj = $cnpj ? $cnpj : cnpj;
		$this -> cogidoGrupoCanal = $cogidoGrupoCanal ? $cogidoGrupoCanal : codigoGrupoCanal;
		$this -> nrIntermediario = $nrIntermediario ? $nrIntermediario : numeroIntermediario;

		$this -> soapSantander = new clsSantanderSoap($this -> userName, $this -> token, $this -> cnpj, $this -> cogidoGrupoCanal, $this -> nrIntermediario);

		$this -> getDados();
		$this -> trataRetorno();

	}

}
?>