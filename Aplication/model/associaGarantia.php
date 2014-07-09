<?

include_once ('../controller/GlobalConstants.php');
include_once ('../controller/soapSantander.php');
include_once ('../controller/carregaMetodo.php');

class associaGarantia extends carregaMetodo {

	public $codigoErro, $descricaoErro, $codigoRetorno;
	private $campos;

	public function __construct( $numeroProposta, $codigoGarantia, $codigoObjFinanciado, $descricaoModelo) {
		$this -> campos = get_defined_vars();
	}

	protected function getDados() {
		echo "<pre>";
		print_r($this -> campos);
		$this -> soapSantander -> setStrImplementacao("enviaPropostaFinanciamentoVeiculoPasso5Garantias");
		while (list($key, $val) = each($this -> campos)) {  $this -> soapSantander -> setParametro($key, $val);
		}
		$this -> soapSantander -> consomeWebService(enderecoEndPointProposta);

	}

	protected function trataRetorno() {

		$x = $this -> soapSantander -> toArray("insereGarantiaResponse");

		$this -> codigoErro = $x[0] -> codigoErro;
		$this -> descricaoErro = $x[0] -> descricaoErro;
		$this -> codigoRetorno = $x[0] -> codigoRetorno;

	}

}
?>