<?

include_once ('../controller/GlobalConstants.php');
include_once ('../controller/soapSantander.php');
include_once ('../controller/carregaMetodo.php');

class associaReferencia extends carregaMetodo {

	public $codigoErro, $descricaoErro, $codigoRetorno;
	private $campos;

	public function __construct($numeroPropostaAdp,  $codigoDigitoAgencia, $codigoDigitoContaCorrente, $codigoTipoContaBancaria, $descricaoTelefoneBanco, $descricaoTelefoneRefer1, $descricaoTelefoneRefer2, $nomeRefer1, $nomeRefer2, $numeroAgencia, $numeroAnoClienteDesde, $numeroBanco, $numeroContaCorrente, $numeroDddRefer1, $numeroDddRefer2,$numeroDddTelefoneBanco, $numeroMesClienteDesde,$numeroClienteInterno,$numeroClienteRelacional, $descricaoEndRefer1, $descricaoEndRefer2) {
		$this -> campos = get_defined_vars();

	}

	protected function getDados() {
		echo "<pre>";
		print_r($this -> campos);
		$this -> soapSantander -> setStrImplementacao("enviaPropostaFinanciamentoVeiculoPasso4Referencias");
		while (list($key, $val) = each($this -> campos)) {  $this -> soapSantander -> setParametro($key, $val);
		}
		$this -> soapSantander -> consomeWebService(enderecoEndPointProposta);

	}

	protected function trataRetorno() {

		$x = $this -> soapSantander -> toArray("insereReferenciaResponse");

		$this -> codigoErro = $x[0] -> codigoErro;
		$this -> descricaoErro = $x[0] -> descricaoErro;
		$this -> codigoRetorno = $x[0] -> codigoRetorno;

	}

}
?>