<?php

include_once ('../model/GlobalConstants.php');
include_once ('../model/soapSantander.php');
include_once ('../model/carregaMetodo.php');

class imprimeCETPDF extends carregaMetodo {

	protected $numeroPropostaADP;
	public $pdfBase64Content, $codigoErro, $codigoRetorno, $descricaoErro;

	public function __construct($nrProp) {
		$this -> numeroPropostaADP = $nrProp;
	}

	protected function getDados() {

		//global $global;
		$this -> soapSantander -> setStrImplementacao("imprimeCETPDF");
		$this -> soapSantander -> setParametro("numeroPropostaADP", $this -> numeroPropostaADP);
		$this -> soapSantander -> consomeWebService(enderecoEndPointProposta);

	}

	protected function trataRetorno() {

		$x = $this -> soapSantander -> toArray("impressaoClientResponse");
		$this -> pdfBase64Content = $x[0] -> pdfBase64Content;
		$this -> codigoErro = $x[0] -> codigoErro;
		$this -> descricaoErro = $x[0] -> descricaoErro;
		$this -> codigoRetorno = $x[0] -> codigoRetorno;

	}

}
?>