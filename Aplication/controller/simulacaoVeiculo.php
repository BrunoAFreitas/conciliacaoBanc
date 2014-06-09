<?

include_once ('../model/GlobalConstants.php');
include_once ('../model/soapSantander.php');
include_once ('../model/carregaMetodo.php');

class simulacaoVeiculo extends carregaMetodo {

	//private $codigoCanal, $codigoGrupoCanal, $codigoReferenciaOperacao, $dataOrigem, $horaOrigem, $numeroAreaNegocio, $numeroEmpresa, $numeroIntermediario, $codigoEstado, $codigoFrota, $codigoIndicadorEntradaIgualParcela, $codigoIndicadorProcedencia, $codigoIndicadorSeguro, $codigoIndicadorTaxi, $codigoIndicadorVeiculoAdaptado, $codigoIndicadorZeroKm, $codigoModalidade, $codigoObjeto, $codigoSegmento, $codigoTipoCombustivel, $codigoTipoPagamento, $codigoTipoPessoa, $controleLojista, $dataPrimeiroVencimento, $numeroAnoFabricacao, $numeroAnoModeloVeiculo, $numeroCpfCnpj, $numeroMarca, $codigoModeloVeiculo, $numeroParcelas, $numeroProduto, $numeroTabelaFinanciamento, $valorAproxParcela, $valorEntrada, $valorTotal, $isencaoTC, $isencaoTAB, $codigoIndicadorTacAVista, $codigoTarifaCadastroRenovacao ;

	private $campos, $codigoErro, $descricaoErro, $codigoRetorno;
	public $simulacaoFinanciamentos, $simulacaoPropostasComSeguro, $simulacaoPropostaSemSeguro;
	public function __construct($codigoCanal, $codigoGrupoCanal, $codigoReferenciaOperacao, $dataOrigem, $horaOrigem, $numeroAreaNegocio, $numeroEmpresa, $numeroIntermediario, $codigoEstado, $codigoFrota, $codigoIndicadorEntradaIgualParcela, $codigoIndicadorProcedencia, $codigoIndicadorSeguro, $codigoIndicadorTaxi, $codigoIndicadorVeiculoAdaptado, $codigoIndicadorZeroKm, $codigoModalidade, $codigoObjeto, $codigoSegmento, $codigoTipoCombustivel, $codigoTipoPagamento, $codigoTipoPessoa, $controleLojista, $dataPrimeiroVencimento, $numeroAnoFabricacao, $numeroAnoModeloVeiculo, $numeroCpfCnpj, $numeroMarca, $codigoModeloVeiculo, $numeroParcelas, $numeroProduto, $numeroTabelaFinanciamento, $valorAproxParcela, $valorEntrada, $valorTotal, $isencaoTC, $isencaoTAB, $codigoIndicadorTacAVista, $codigoTarifaCadastroRenovacao) {
		$this -> campos = get_defined_vars();
	}

	protected function getDados() {

		//global $global;

		$this -> soapSantander -> setStrImplementacao("simulaFinanciamentoVeiculo");
		while (list($key, $val) = each($this -> campos)) {
			$this -> soapSantander -> setParametro($key, $val);
		}
		$this -> soapSantander -> consomeWebService(enderecoEndPointProposta);

	}

	protected function trataRetorno() {

		$x = $this -> soapSantander -> toArray("simulacaoPropostaResponse");
		$this -> simulacaoFinanciamentos = $x[0] -> simulacao -> simulacaoFinanciamento;
		//array
		$this -> simulacaoPropostasComSeguro = $x[0] -> simulacao -> simulacaoProposta;
		//array
		$this -> simulacaoPropostaSemSeguro = $x[0] -> simulacao -> simulacaoPropostaSemSeguro;
		//array

		$this -> codigoErro = $x[0] -> codigoErro;
		$this -> descricaoErro = $x[0] -> descricaoErro;
		$this -> codigoRetorno = $x[0] -> codigoRetorno;

	}

}
?>