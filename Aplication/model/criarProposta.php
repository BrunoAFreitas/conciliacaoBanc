<?php

include_once ('../controller/GlobalConstants.php');
include_once ('../controller/soapSantander.php');
include_once ('../controller/carregaMetodo.php');

class criarProposta extends carregaMetodo {

	private $campos;
	public $codigoErro, $descricaoErro, $codigoRetorno;
	public $numeroPropostaAdp;

	public function __construct($codigoFormaPagamento , $dataEntregaBem            , $codigoTipoMoeda, 
								$nomeVendedor         , $numeroIdProduto           , $numeroQuantidadePrestacoes, 
								$codigoModalidade     , $numeroTabelaFinanciamento , $numeroVendedor,
								$indicadorTac         , $isencaoTC                 , $isencaoTAB,
								$textoControleLoja    , $textoObsLoja              , $valorBem,  
								$valorEntrada         , $valorFinanciamento        , $valorPrestacao, 
								$dataVencimento1){
			
		$this -> campos = get_defined_vars();	
	}

	protected function getDados() {
	//	echo "<pre>";
	//	print_r($this -> campos);
		$this -> soapSantander -> setStrImplementacao("enviaPropostaFinanciamentoVeiculoPasso1Inicio");
		while (list($key, $val) = each($this -> campos)) {
			 $this -> soapSantander -> setParametro($key, $val);
		}
		
		$this -> soapSantander -> setParametro("numeroIntermediario", numeroIntermediario);
		$this -> soapSantander -> consomeWebService(enderecoEndPointProposta);
		//$this -> trataRetorno(); 
	}

	protected function trataRetorno() {

		$x = $this -> soapSantander -> toArray("inserePropostaResponse");

		$this -> codigoErro = $x[0] -> codigoErro;
		$this -> descricaoErro = $x[0] -> descricaoErro;
		$this -> codigoRetorno = $x[0] -> codigoRetorno;
		$this -> numeroPropostaAdp = $x[0] -> numeroPropostaAdp;

	}

}
?>