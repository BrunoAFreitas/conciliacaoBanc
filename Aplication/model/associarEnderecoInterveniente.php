<?

include_once ('../controller/GlobalConstants.php');
include_once ('../controller/soapSantander.php');
include_once ('../controller/carregaMetodo.php');

class associarEnderecoInterveniente extends carregaMetodo {

	public $codigoErro, $descricaoErro, $codigoRetorno;
	private $campos;

	public function __construct($numeroPropostaAdp,  $numeroClienteInterno, $codigoEnderecoCorrespondencia, $codigoPaisComercial, $codigoPaisResidencial, $codigoSiglaUfComercial, $codigoSiglaUfResidencial, $dataAnoResideDesde, $dataMesResideDesde, $descricaoComplementoEndComercial, $descricaoComplementoResidencia, $descricaoEnderecoComercial, $descricaoEnderecoResidencia, $descricaoEnderecoEmail, $descricaoTelComercial, $descricaotelResidencial, $nomeBairroComercial, $nomeBairroResidencial, $nomeCidadeComercial, $nomeCidadeResidencial, $numeroCepComercial, $numeroCepResidencial, $numeroDddCel, $numeroDddResidencial, $numeroDddTelComercial, $numeroEnderecoComercial, $numeroResidencial, $numeroTipoResidencia, $numeroTipoTelefResiden) {
		$this -> campos = get_defined_vars();
	}

	protected function getDados() {
		//echo "<pre>";
		//print_r($this -> campos);
		$this -> soapSantander -> setStrImplementacao("enviaPropostaFinanciamentoVeiculoPasso3Endereco");
		while (list($key, $val) = each($this -> campos)) {  $this -> soapSantander -> setParametro($key, $val);
		}
		$this -> soapSantander -> consomeWebService(enderecoEndPointProposta);

	}

	protected function trataRetorno() {

		$x = $this -> soapSantander -> toArray("insereEnderecoResponse");

		$this -> codigoErro = $x[0] -> codigoErro;
		$this -> descricaoErro = $x[0] -> descricaoErro;
		$this -> codigoRetorno = $x[0] -> codigoRetorno;

	}

}
?>