<?

include_once ('../model/GlobalConstants.php');
include_once ('../model/soapSantander.php');
include_once ('../model/carregaMetodo.php');

class consolidarProposta extends carregaMetodo {

	public $codigoErro, $descricaoErro, $codigoRetorno, $dadosADP;
	private $campos;

	public function __construct( $numeroPropostaAdp ) {
		$this -> campos = get_defined_vars();
	}
	
	protected function getDados() {
		//echo "<pre>";
		//print_r($this -> campos);
		
		

		$this -> soapSantander -> setStrImplementacao("enviaPropostaFinanciamentoVeiculoPasso6Fim");
		while (list($key, $val) = each($this -> campos)) {  $this -> soapSantander -> setParametro($key, $val);
		}
		$this -> soapSantander -> setParametro("numeroIntermediario", numeroIntermediario);
		$this -> soapSantander -> consomeWebService(enderecoEndPointProposta);

	}

	protected function trataRetorno() {

		$x = $this -> soapSantander -> toArray("dadosAdpResponse");

		$this -> codigoErro = $x[0] -> codigoErro;
		$this -> descricaoErro = $x[0] -> descricaoErro;
		$this -> codigoRetorno = $x[0] -> codigoRetorno;
		$this -> dadosADP = $x[0] -> dadosAdp;

	}

}
?>