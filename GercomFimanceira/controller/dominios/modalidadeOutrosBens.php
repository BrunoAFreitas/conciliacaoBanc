<?

include_once ('../model/GlobalConstants.php');
include_once ('../model/soapSantander.php');
include_once ('../model/carregaMetodo.php');

class modalidadeOutrosBens extends carregaMetodo {

	public $codigoErro, $descricaoErro, $codigoRetorno, $dadosADP, $modalidadesOutrosBens;
	private $campos;

	public function __construct($codigoCanal, $usuario, $numeroTipoFinanciamento) { $this -> campos = get_defined_vars();
	}

	protected function getDados() {

		$this -> soapSantander -> setStrImplementacao("listarModalidadesOutrosBens");
		while (list($key, $val) = each($this -> campos)) {  $this -> soapSantander -> setParametro($key, $val);
		}
		$this -> soapSantander -> consomeWebService(enderecoEndPointDominio);

	}

	protected function trataRetorno() {

		$x = $this -> soapSantander -> toArray("dominioModalidadesOutrosBensResponse");

		$this -> codigoErro = $x[0] -> codigoErro;
		$this -> descricaoErro = $x[0] -> descricaoErro;
		$this -> codigoRetorno = $x[0] -> codigoRetorno;
		$this -> modalidadesOutrosBens = $x[0] -> dominios -> opcoes;

	}

}
?>