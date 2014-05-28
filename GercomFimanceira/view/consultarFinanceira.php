<?php
/**
 * A classe consultarFinanceira e reponsavel por criar e acoplar os 6 passos do financiamento
 * Execultando um atraz do outro sempre que não cair em exection. 
 */

// para a conexao com o banco de dado do gercom 
include_once("../model/crud.php");
// passo 1
include_once("../controller/criarProposta.php");
// passo 2
include_once("../controller/criarInterveniente.php");
// passo 3
include_once("../controller/associarEnderecoInterveniente.php");
// passo 4
include_once("../controller/associaReferencia.php");
// passo 5
include_once("../controller/associaGarantia.php");
// passo 6
include_once("../controller/consolidarProposta.php");
//classe Adicional
include_once("../controller/form.class.php");

class consultarFinanceira extends crud {

	// para retorno
	private $mensage;
	// para identificação do cliente
	private $idCliente;
	// variavel para todos os passos
	private $numeroProposta;
	// varivel de proposta passo dois
	private $nrInterveniente;
	// Array bd
	private $clienteBD;
	// Classe Form
	private $form;
	
	
	public function __construct($idCliente) {
		$this -> idCliente = $idCliente;
		$this -> clienteBD = $this ->cliente();
		$this -> form = new forms();
	}

	// metodo para setar as msg
	public function setMensage($mesage) {
		$this -> mensage = $mesage;
	}

	// metodo reber as msg
	public function getMensage() {
		return $this -> mensage;
	}
	/**
	 * Consulta campos de cliente para preencimento dos passos para financiamento.
	 */
	public function cliente(){
		$cliente = mysql_fetch_array(self::ListaTable('*','clientes',"cli_cgccpf = '". $this->idCliente ."'"));
		return $cliente;	
	}
	
	public function extrairAnoMes($date){
		$date = explode('-',$date);
		return $date;
	}
	
	public function extrairDDD($fone, $ddd = 's'){
		$fonec = explode(")", $fone);
		$fone = explode("(", $fonec[0]);
		if($ddd == 's'){
			return $fone[1];
		}else{

			return $fonec[1];
		}
		
	}

	public function extrairDigito($conta, $posicao){
		$conta = explode("-", $conta);
		if($posicao == 0){
			return $conta[0];
		}else{
			return $conta[1];
		}
	}
	
	// Criar proposta (passo 1)
	public function passo1( $codigoFormaPagamento , $dataEntregaBem            , $codigoTipoMoeda, 
							$nomeVendedor         , $numeroIdProduto           , $numeroQuantidadePrestacoes, 
							$codigoModalidade     , $numeroTabelaFinanciamento , $numeroVendedor,
							$indicadorTac         , $isencaoTC                 , $isencaoTAB,
							$textoControleLoja    , $textoObsLoja              , $valorBem, 
							$valorEntrada         , $valorFinanciamento        , $valorPrestacao, 
							$dataVencimento1) {
 		
		
		
 		$criarProposta = new criarProposta( $codigoFormaPagamento , $dataEntregaBem            , $codigoTipoMoeda, 
											$nomeVendedor         , $numeroIdProduto           , $numeroQuantidadePrestacoes, 
											$codigoModalidade     , $numeroTabelaFinanciamento , $numeroVendedor,
											$indicadorTac         , $isencaoTC                 , $isencaoTAB,
											$textoControleLoja    , $textoObsLoja              , $valorBem, 
											$valorEntrada         , $valorFinanciamento        , $valorPrestacao, 
											$dataVencimento1);
		$criarProposta -> executa();
		
	
		// variavel que sera usado em todos os passos
		$this -> numeroProposta = $criarProposta->numeroPropostaAdp;
		// mostrando a msg de retorno
		$this->setMensage($this -> numeroProposta);
		// o retorno desse metodo tera que ser usado em todos os outros passos
		// para relacionar as informacaoes inseridas nos demais passos
		
		// iniciando o passo dois e passando o numero da proposta
		$this -> passo2($this -> numeroProposta);
		
	}

	// Associa interveniente (passo 2) dados do cliente
	public function passo2($criarProposta) {
		
		$cliente = $this -> clienteBD;
		$numeroPropostaAdp = $criarProposta;// este codigo vem do primeiro passo

		
		$numeroTipoVinculoPart    = "1";
		$codigoDocumento          = $cliente['cli_inscrg'];//20d
		$codigoEstadoNaturalidade = $cliente['cli_estado'];//2d
		$codigoEstadoOrgaoEmissor = $cliente['cli_estadorgemissor'];//2d
		$codigoNacionalidade      = $cliente['cli_nacionalidade'];//2d
		$codigoPaisDocumento      = $cliente['cli_paisdocumento'];//2d
		//sedePropria
		$codigoSexo               = $cliente['cli_sexo'];//1d
		$codigoTipoPessoa         = ($cliente['cli_fisica'] == 'S' ? 'F' : 'J');
		$dataAdmissao             = $this -> form -> muda_data_pt($cliente['cli_dtadmtrab']);//date
		$dataEmissaoDocumento     = $this -> form -> muda_data_pt($cliente['cli_rgdtemissao']);//date Mudar para padrão nascional
		$dataNascimento           = $this -> form -> muda_data_pt($cliente['cli_dtnasc']);//date
		$descricaoNaturalidade    = $cliente['cli_naturalidade'];//40d
		$descricaoProfissao       = $cliente['cli_profissao'];//40d
		$nomeCompleto             = $cliente['cli_razao'];//60d
		//nomeEmpresa
		$nomeOrgaoEmissor         = $cliente['cli_rgemissor'];//5d
		$nomeMae                  = $cliente['cli_mae'];//60d
		$nomePai    			  = $cliente['cli_pai'];//60d
		//$numeroCnpjEmpresa        = $cliente['cli_cgccpf'];//15d
		$numeroComprovanteRenda   = $cliente['cli_tpcomprenda'];//xx
		$numeroCpfCnpj            = $cliente['cli_cgccpf'];//11d
		$numeroDependentes        = $cliente['cli_qtdfilhos'];//3d
		$numeroEstadoCivil        = $cliente['cli_estadocivil'];//2d
		$numeroProfissao          = $cliente['cli_profissaocod'];//5d
		$numeroRenda              = $cliente['cli_numoutrasrendas'];//3d
		$numeroTipoDocumento      = $cliente['cli_tipodoc'];//xx
		$valorOutrasRendas        = $cliente['cli_outrasrendas'];//15,2m
		$valorPatrimonio          = $cliente['cli_patrimonio'];//15,2m
		$valorRendaMensal         = $cliente['cli_rendamensal'];//15,2m
		$numeroInstrucao          = '01';//cod 10
		$indicativoDeficienteFisico = 'N';// S/N
		$numeroOcupacao           = '01';//Cod 21
		
		
		$nomeEmpresa = $cliente['cli_razao'];//60d
		$codigoSedePropria = $cliente['cli_sedepropria'];//1d

			$interveniente = new criarInterveniente($numeroTipoVinculoPart,$numeroPropostaAdp, $codigoDocumento, $codigoEstadoNaturalidade, $codigoEstadoOrgaoEmissor, 
													$codigoNacionalidade, $codigoPaisDocumento, $codigoSedePropria, $codigoSexo, 
													$codigoTipoPessoa, $dataAdmissao, $dataEmissaoDocumento, $dataNascimento, 
													$descricaoNaturalidade, $descricaoProfissao,  
													$nomeCompleto, $nomeEmpresa, $nomeOrgaoEmissor, 
													$nomeMae, $nomePai,  $numeroComprovanteRenda, $numeroCpfCnpj, 
													$numeroDependentes, $numeroEstadoCivil, $numeroProfissao, 
													$numeroRenda, $numeroTipoDocumento, $valorOutrasRendas, $valorPatrimonio, 
													$valorRendaMensal,$numeroInstrucao,$indicativoDeficienteFisico,$numeroOcupacao);
		

		$interveniente -> executa();
		// criando o numero do passo dois
		$this -> nrInterveniente = $interveniente -> numeroInternoCliente;
		// mostrando a msg de retorno
		$this -> setMensage($this -> nrInterveniente);
		// retorno codigo de cliente avalista que foi incluido
		
		// chmando passo quatro		
		$this -> passo3($this -> numeroProposta);
		$this -> passo4($this -> numeroProposta);
		$this -> passo5($this -> numeroProposta);
		$this -> passo6($this -> numeroProposta);
	}
	
	// associa endereco ao interveniente (passo 3) endereço
	public function passo3($numeroProposta1) {
		
		//$codigoCel = $cliente['cli_celular1'];//10d

		$numeroPropostaAdp    =  $numeroProposta1;       // dados obtido passo 1
		$numeroClienteInterno =  $this -> nrInterveniente;    // dados obtido passo 2
		
		$cliente = $this -> clienteBD;
		$reside  = $this -> extrairAnoMes($cliente['cli_residedesde']);
		$dddCel  = $this -> extrairDDD($cliente['cli_celular1']);
		$dddRes  = $this -> extrairDDD($cliente['cli_fone']);
		$dddTra  = $this -> extrairDDD($cliente['cli_fonetrab']); 
		
		$codigoEnderecoCorrespondencia    = "R";
		$codigoPaisComercial 			  = "BR";//2d
		$codigoPaisResidencial 			  = "BR";//2d
		$codigoSiglaUfComercial           = $cliente['cli_estadotrab'];//2d
		$codigoSiglaUfResidencial         = $cliente['cli_estado'];//2d
		$dataAnoResideDesde               = $reside[0];//3d
		$dataMesResideDesde               = $reside[1];	//3d
		$descricaoComplementoEndComercial = $cliente['cli_complementotrab'];//20d
		$descricaoComplementoResidencia   = $cliente['cli_complemento'];//20d
		$descricaoEnderecoComercial       = $cliente['cli_endtrab'];//60d
		$descricaoEnderecoResidencia 	  = $cliente['cli_end'];//50d
		$descricaoEnderecoEmail 		  = $cliente['cli_email'];//50d
		$descricaoTelComercial 			  = $this -> extrairDDD($cliente['cli_fonetrab'],'n');//10d
		$descricaotelResidencial 		  = $this -> extrairDDD($cliente['cli_fone'],'n');//10d
		$nomeBairroComercial              = $cliente['cli_bairrotrab'];//20d
		$nomeBairroResidencial 			  = $cliente['cli_bairro'];//20d
		$nomeCidadeComercial              = $cliente['cli_cidadetrab'];//20d
		$nomeCidadeResidencial  		  = $cliente['cli_cidade'];//20d
		$numeroCepComercial               = $cliente['cli_ceptrab'];//8d
		$numeroCepResidencial 			  = $cliente['cli_cep'];//8d
		$numeroDddCel 					  = $dddCel;//3d
		$numeroDddResidencial 			  = $dddRes;//3d
		$numeroDddTelComercial 			  = $dddTra;//3d
		$numeroEnderecoComercial 		  = $cliente['cli_numerotrab'];//5d
		$numeroResidencial 				  = $cliente['cli_numeroend'];//5d
		$numeroTipoResidencia 			  = $cliente['cli_tiporesid'];//2d
		$numeroTipoTelefResiden 		  = $cliente['cli_tipofone'];//2d
		
		$endereco = new associarEnderecoInterveniente( $numeroPropostaAdp,  $numeroClienteInterno, $codigoEnderecoCorrespondencia, $codigoPaisComercial, $codigoPaisResidencial,
													   $codigoSiglaUfComercial, $codigoSiglaUfResidencial,  $dataAnoResideDesde,
													   $dataMesResideDesde, $descricaoComplementoEndComercial, $descricaoComplementoResidencia,
													   $descricaoEnderecoComercial, $descricaoEnderecoResidencia, $descricaoEnderecoEmail,
													   $descricaoTelComercial, $descricaotelResidencial, $nomeBairroComercial, $nomeBairroResidencial,
													   $nomeCidadeComercial, $nomeCidadeResidencial, $numeroCepComercial, $numeroCepResidencial,
													   $numeroDddCel, $numeroDddResidencial, $numeroDddTelComercial, $numeroEnderecoComercial,
													   $numeroResidencial, $numeroTipoResidencia, $numeroTipoTelefResiden );
		$endereco->executa();
		
		if($endereco->codigoRetorno == "00") {
			// ok pode ir para o proximo passo
			echo "ok";
		} else {
			// algo deu errado verificar dados
			echo "<br/>";
			echo  $endereco->descricaoErro;
		}
	}

	// associa referencia (passo 4) pessoal bancaria
	public function passo4($numeroProposta) {
		$numeroPropostaAdp = $numeroProposta;   // dados obtido no primeiro passo2
		
		$cliente = $this -> clienteBD;
		$dddref1 = $this -> extrairDDD($cliente['cli_telref1']);
		$dddref2 = $this -> extrairDDD($cliente['cli_telref2']);
		$dddbanc = $this -> extrairDDD($cliente['cli_telbanco']);
		$desde   = $this -> extrairAnoMes($cliente['cli_dtaberturaconta']);
		
		$codigoDigitoAgencia       = $cliente['cli_digitoagencia'];//1d
		$codigoDigitoContaCorrente = $cliente['cli_digitoconta'];//1d
		$codigoTipoContaBancaria   = $cliente['cli_tipoconta'];//1d
		$descricaoTelefoneBanco    = $this->extrairDDD($cliente['cli_telbanco'],'n');//10d
		$descricaoTelefoneRefer1   = $this->extrairDDD($cliente['cli_telref1'],'n');//10d
		$descricaoTelefoneRefer2   = $this->extrairDDD($cliente['cli_telref1'],'n');//10d
		$nomeRefer1                = $cliente['cli_nomeref1'];
		$nomeRefer2                = $cliente['cli_nomeref2'];//50d
		$numeroAgencia             = $cliente['cli_numeroagenc'];//5d
		$numeroAnoClienteDesde     = $desde[0];//4d
		$numeroBanco               = $cliente['cli_numerobanco'];//3d
		$numeroContaCorrente       = $cliente['cli_conta'];//8d
		$numeroDddRefer1           = $dddref1;//3d
		$numeroDddRefer2           = $dddref2;//3d
		$numeroDddTelefoneBanco    = $dddbanc;//3d 
		$numeroMesClienteDesde     = $desde[1];//2d
		$numeroClienteInterno      = $this->nrInterveniente;
		$numeroClienteRelacional   = $this->nrInterveniente;
		$descricaoEndRefer1	 	   = "40032001";
		$descricaoEndRefer2 	   = "623456789";
		
		$referencia = new associaReferencia( $numeroPropostaAdp, $codigoDigitoAgencia,$codigoDigitoContaCorrente, $codigoTipoContaBancaria, 
											 $descricaoTelefoneBanco, $descricaoTelefoneRefer1, $descricaoTelefoneRefer2,
											 $nomeRefer1, $nomeRefer2,$numeroAgencia, 
											 $numeroAnoClienteDesde, $numeroBanco, $numeroContaCorrente,
											 $numeroDddRefer1,$numeroDddRefer2, $numeroDddTelefoneBanco, 
											 $numeroMesClienteDesde,$numeroClienteInterno,$numeroClienteRelacional, $descricaoEndRefer1, $descricaoEndRefer2 );
		$referencia->executa(); 
		
		if($referencia->codigoRetorno == "00") {
			echo "ok";
		} else {
			echo "<br/>";
			echo $referencia->descricaoErro;
		}
		// neste passo tb nao se tem retorno do xml
	}

	// associa garantia(passo 5) insercao de garantia neste passo pode criar varios metodos como parametro do xml
	// sao dados para descrever o produto
	public function passo5($numeroProposta) {
		$numeroProposta = $numeroProposta;   //dados obtidos no primeiro passo 
		
		$codigoGarantia = '01';//2d
		$codigoObjFinanciado = 'MO';//2d
		$descricaoModelo = "CD-130";//20d
		
		$garantia = new associaGarantia( $numeroProposta, $codigoGarantia, $codigoObjFinanciado, $descricaoModelo);
		$garantia->executa();
		
		
		if($garantia->codigoRetorno == "00") {
			echo "ok";
		} else {
			echo "<br/>";
			echo $garantia->descricaoErro;
		}
		
		
		// neste passo tb nao se tem retorno do xml
	}

	// consolidacao de dados e incluçao da compra (passo 6)
	public function passo6($numeroPropostaAdp) {
		$numeroPropostaAdp = $numeroPropostaAdp;    // dados do primeiro passo		
		//$numeroIntermediario = "420648";			//6d
		
		$consolidar = new consolidarProposta( $numeroPropostaAdp );
		$consolidar -> executa();
		
		echo  $consolidar -> dadosADP -> descricaoCor;
		print_r( $consolidar -> dadosADP );
	 
		if( $consolidar -> codigoRetorno == "00" ) {
			echo "ok"; 
		} else { 
			echo $consolidar -> descricaoErro;
		}
	}	
}
?>