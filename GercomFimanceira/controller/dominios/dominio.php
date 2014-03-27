<?

include_once ('../model/GlobalConstants.php');
include_once ('../model/soapSantander.php');
include_once ('../model/carregaMetodo.php');

class dominio extends carregaMetodo {

	public $codigoErro, $descricaoErro, $codigoRetorno, $dadosADP, $dominios;
	private $campos;

	public function __construct($codigoCanal, $usuario, $codigoDominio) {
		 $this -> campos = get_defined_vars();
	}

	protected function getDados() {

		//global $global;
		$this -> soapSantander -> setStrImplementacao("listarDominiosGerais");
		while (list($key, $val) = each($this -> campos)) {  $this -> soapSantander -> setParametro($key, $val);
		}
		$this -> soapSantander -> consomeWebService(enderecoEndPointDominio);

	}

	protected function trataRetorno() {

		$x = $this -> soapSantander -> toArray("dominiosGeraisResponse");

		$this -> codigoErro = $x[0] -> codigoErro;
		$this -> descricaoErro = $x[0] -> descricaoErro;
		$this -> codigoRetorno = $x[0] -> codigoRetorno;
		$this -> dominios = $x[0] -> dominios -> opcoes;

	}

}
?>