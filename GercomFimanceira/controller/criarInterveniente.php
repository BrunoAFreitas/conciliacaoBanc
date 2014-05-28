<?

include_once ('../model/GlobalConstants.php');
include_once ('../model/soapSantander.php');
include_once ('../model/carregaMetodo.php');

class criarInterveniente extends carregaMetodo {

	public $codigoErro, $descricaoErro, $codigoRetorno;
	private $campos;
	public $simulacaoFinanciamentos, $simulacaoPropostas, $numeroInternoCliente;

	public function __construct($numeroTipoVinculoPart,$numeroPropostaAdp, $codigoDocumento, 
								$codigoEstadoNaturalidade, $codigoEstadoOrgaoEmissor, $codigoNacionalidade, 
								$codigoPaisDocumento, $codigoSedePropria, $codigoSexo, 
								$codigoTipoPessoa, $dataAdmissao, $dataEmissaoDocumento, 
								$dataNascimento,  $descricaoNaturalidade, $descricaoProfissao,   
								$nomeCompleto, $nomeEmpresa, $nomeOrgaoEmissor, $nomeMae, $nomePai, 
								$numeroComprovanteRenda, $numeroCpfCnpj, 
								$numeroDependentes, $numeroEstadoCivil, $numeroProfissao,
								$numeroRenda, $numeroTipoDocumento, $valorOutrasRendas, 
								$valorPatrimonio, $valorRendaMensal,$numeroInstrucao,
								$indicativoDeficienteFisico,$numeroOcupacao) {
		$this -> campos = get_defined_vars();

	}



	protected function getDados() {

		//global $global;
		echo "<pre>";
		print_r($this -> campos);
		$this -> soapSantander -> setStrImplementacao("enviaPropostaFinanciamentoVeiculoPasso2DadosPessoais");
		while (list($key, $val) = each($this -> campos)) {
			  $this -> soapSantander -> setParametro($key, $val);
		}
		$this -> soapSantander -> consomeWebService(enderecoEndPointProposta);

	}

	protected function trataRetorno() {

		$x = $this -> soapSantander -> toArray("dadosPessoaisResponse");

		$this -> codigoErro = $x[0] -> codigoErro;
		$this -> descricaoErro = $x[0] -> descricaoErro;
		$this -> codigoRetorno = $x[0] -> codigoRetorno;
		$this -> numeroInternoCliente = $x[0] -> dadosPessoais -> numeroInternoCliente;

	}

}
?>