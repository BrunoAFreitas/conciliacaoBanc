<?

include_once ('../controller/GlobalConstants.php');
include_once ('../controller/soapSantander.php');
include_once ('../controller/carregaMetodo.php');

class consultaStatus extends carregaMetodo {

	public $codigoErro, $descricaoErro, $codigoRetorno;
	public $statusPropostas, $descricaoStatus, $numeroPropostas;
	private $campos;

	public function __construct( $numeroPropostas) {
		$this -> campos = get_defined_vars();
	}

	protected function getDados() {
		echo "<pre>";
		print_r($this -> campos);
		$this -> soapSantander -> setStrImplementacao("consultaStatusPropostasFinanciamentoVeiculo");
		while (list($key, $val) = each($this -> campos)) {  $this -> soapSantander -> setParametro($key, $val);
		}
		$this -> soapSantander -> consomeWebService(enderecoEndPointProposta);


	}

	protected function trataRetorno() {

		$x = $this -> soapSantander -> toArray("statusPropostasResponse");
		
		$this -> codigoErro = $x[0] -> codigoErro;
		$this -> descricaoErro = $x[0] -> descricaoErro;
		$this -> codigoRetorno = $x[0] -> codigoRetorno;
		$this -> numeroPropostas = $x[0] -> statusPropostas -> opcoes -> codigoProposta;
		$this -> statusPropostas = $x[0] -> statusPropostas -> opcoes -> codigoStatus;
		$this -> descricaoStatus = $x[0] -> statusPropostas -> opcoes -> descricaoMensagemPropostas;
	}

}