<?

include_once ('../model/GlobalConstants.php');
include_once ('../model/soapSantander.php');
include_once ('../model/carregaMetodo.php');

class simulacaoOutrosBens extends carregaMetodo {

	private $campos, $codigoErro, $descricaoErro, $codigoRetorno;
	public $simulacaoFinanciamentos, $simulacaoPropostas;

	public function __construct($numeroIntermediario, $codigoIndicadorEntradaIgualParcela, $codigoIndicadorProcedencia, $codigoIndicadorSeguro, $codigoIndicadorTacAVista, $codigoIndicadorTaxi, $codigoIndicadorVeiculoAdaptado, $codigoIndicadorZeroKm, $codigoModalidade, $codigoModeloVeiculo, $codigoObjeto, $codigoPacote, $codigoTarifaCadastroRenovacao, $codigoTipoCombustivel, $codigoTipoPagamento, $codigoTipoPessoa, $controleLojista, $dataPrimeiroVencimento, $numeroAnoFabricacao, $numeroAnoModeloVeiculo, $numeroBanco, $numeroCpfCnpj, $numeroMarca, $numeroParcelas, $numeroProduto, $numeroTabelaFinanciamento, $valorAproxParcela, $valorEntrada, $valorTotal) {
		$this -> campos = get_defined_vars();
	}

	protected function getDados() {

		$this -> soapSantander -> setStrImplementacao("simulaFinanciamentoOutrosBens");
		while (list($key, $val) = each($this -> campos)) {
			$this -> soapSantander -> setParametro($key, $val);
		}
		$this -> soapSantander -> consomeWebService(enderecoEndPointProposta);

	}

	protected function trataRetorno() {

		$x = $this -> soapSantander -> toArray("simulacaoPropostaResponse");

		$this -> simulacaoFinanciamentos = $x[0] -> simulacaoOutrosBens -> simulacaoFinanciamento;
		$this -> simulacaoPropostas = $x[0] -> simulacaoOutrosBens -> simulacaoProposta;
		//array

		$this -> codigoErro = $x[0] -> codigoErro;
		$this -> descricaoErro = $x[0] -> descricaoErro;
		$this -> codigoRetorno = $x[0] -> codigoRetorno;

	}

}
?>