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
	
	
	public function __construct($idCliente) {
		$this -> idCliente = $idCliente;
		$this -> clienteBD = $this ->cliente();
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
	
	public function extrairDDD($fone){
		$fone1 = explode('(',$fone);
		$fone  = explode(')',$fone1[1]);
		return $fone[0];
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
		$codigoEstadoNaturalidade = 'CE';//$cliente['cli_estado'];//2d
		$codigoEstadoOrgaoEmissor = 'CE';//$cliente['cli_estadorgemissor'];//2d
		$codigoNacionalidade      = $cliente['cli_nacionalidade'];//2d
		$codigoPaisDocumento      = 'BR';//$cliente['cli_paisdocumento'];//2d
		$codigoSedePropria        = $cliente['cli_sedepropria'];//1d
		$codigoSexo               = 'M';//$cliente['cli_sexo'];//1d
		$codigoTipoPessoa         = 'F';//$cliente['cli_fisica'];//1d
		$dataAdmissao             = '13/12/1994';//$cliente['cli_dtadmtrab'];//date
		$dataEmissaoDocumento     = '13/12/1994';//$cliente['cli_rgdtemissao'];//date Mudar para padrão nascional
		$dataNascimento           = '13/12/1994';//$cliente['cli_dtnasc'];//date
		$descricaoNaturalidade    = 'Bela Cruz';//$cliente['cli_naturalidade'];//40d
		$descricaoProfissao       = 'Oleiro';//$cliente['cli_profissao'];//40d
		$nomeCompleto             = 'Nome Teste Passo Dois';//$cliente['cli_razao'].' Freitas Araújo';//60d
		$nomeEmpresa              = $cliente['cli_razao'];//60d
		$nomeOrgaoEmissor         = 'SSP';//$cliente['cli_rgemissor'];//5d
		$nomeMae                  = 'Mae Teste Passo Dois';//$cliente['cli_pai'];//60d
		$nomePai    			  = 'Pai Teste Passo Dois';//$cliente['cli_mae'];//60d
		$numeroCnpjEmpresa        = $cliente['cli_cgccpf'];//15d
		$numeroComprovanteRenda   = '01';//$cliente['cli_tpcomprenda'];//xx
		$numeroCpfCnpj            = $cliente['cli_cgccpf'];//11d
		$numeroDependentes        = $cliente['cli_qtdfilhos'];//3d
		$numeroEstadoCivil        = '01';//$cliente['cli_estadocivil'];//2d
		$numeroProfissao          = $cliente['cli_profissaocod'];//5d
		$numeroRenda              = '01';$cliente['cli_numoutrasrendas'];//3d
		$numeroTipoDocumento      = '01';//$cliente['cli_tipodoc'];//xx
		$valorOutrasRendas        = '100,00';//$cliente['cli_outrasrendas'];//15,2m
		$valorPatrimonio          = '100000,00';//$cliente['cli_patrimonio'];//15,2m
		$valorRendaMensal         = '10000,00';//$cliente['cli_rendamensal'];//15,2m
		$numeroInstrucao          = '01';//cod 10
		$indicativoDeficienteFisico = 'N';// S/N
		$numeroOcupacao           = '01';//Cod 21
		
		$interveniente = new criarInterveniente($numeroTipoVinculoPart,$numeroPropostaAdp, $codigoDocumento, $codigoEstadoNaturalidade, $codigoEstadoOrgaoEmissor, 
												$codigoNacionalidade, $codigoPaisDocumento, $codigoSedePropria, $codigoSexo, 
												$codigoTipoPessoa, $dataAdmissao, $dataEmissaoDocumento, $dataNascimento, 
												$descricaoNaturalidade, $descricaoProfissao,  
												$nomeCompleto, $nomeEmpresa, $nomeOrgaoEmissor, 
												$nomeMae, $nomePai, $numeroCnpjEmpresa, $numeroComprovanteRenda, $numeroCpfCnpj, 
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
		$numeroPropostaAdp    =  $numeroProposta1;       // dados obtido passo 1
		$numeroClienteInterno =  $this -> nrInterveniente;    // dados obtido passo 2
		
		$cliente = $this -> clienteBD;
		$reside  = $this -> extrairAnoMes($cliente['cli_residedesde']);
		$dddCel  = $this -> extrairDDD($cliente['cli_cel1']);
		$dddRes  = $this -> extrairDDD($cliente['cli_fone']);
		$dddTra  = $this -> extrairDDD($cliente['cli_fonetrab']); 
		
		$codigoEnderecoCorrespondencia    = "R";
		$codigoPaisComercial 			  = "BR";//2d
		$codigoPaisResidencial 			  = "BR";//2d
		$codigoSiglaUfComercial           = 'AC';//$cliente['cli_estadotrab'];//2d
		$codigoSiglaUfResidencial         = 'AC';//$cliente['cli_estado'];//2d
		//$codigoCel                        = $cliente['cli_celular1'];//10d
		$dataAnoResideDesde               = '1994';// $reside[0];//3d
		$dataMesResideDesde               = '06';//$reside[1];	//3d
		$descricaoComplementoEndComercial = 'CJ. 11';//$cliente['cli_complementotrab'];//20d
		$descricaoComplementoResidencia   = "CJ. 11";//$cliente['cli_complemento'];//20d
		$descricaoEnderecoComercial       = "Rua Pernambuco";//$cliente['cli_endtrab'];//60d
		$descricaoEnderecoResidencia 	  = "Rua Pernambuco";//$cliente['cli_end'];//50d
		$descricaoEnderecoEmail 		  = "teste@teste.com";//$cliente['cli_email'];//50d
		$descricaoTelComercial 			  = '74652221';//$cliente['cli_fonetrab'];//10d
		$descricaotelResidencial 		  = '74652221';//$cliente['cli_fone'];//10d
		$nomeBairroComercial              = "Dom Giocondo";//$cliente['cli_bairrotrab'];//20d
		$nomeBairroResidencial 			  = "Dom Giocondo";//$cliente['cli_bairro'];//20d
		$nomeCidadeComercial              = "Rio Branco";//$cliente['cli_cidadetrab'];//20d
		$nomeCidadeResidencial  		  = "Rio Branco";//$clinete['cli_cidade'];//20d
		$numeroCepComercial               = "69900306";//$cliente['cli_ceptrab'];//8d
		$numeroCepResidencial 			  = '69900306';//$cliente['cli_cep'];//8d
		$numeroDddCel 					  = '11';//$dddCel;//3d
		$numeroDddResidencial 			  = '11';//$dddRes;//3d
		$numeroDddTelComercial 			  = '11';//$dddTra;//3d
		$numeroEnderecoComercial 		  = '158';//$cliente['cli_numerotrab'];//5d
		$numeroResidencial 				  = '1893';//$cliente['cli_numeroend'];//5d
		$numeroTipoResidencia 			  = '1';//$cliente['cli_tiporesid'];//2d
		$numeroTipoTelefResiden 		  = '1';//$cliente['cli_tipofone'];//2d
		
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
		$numeroPropostaAdp = $numeroProposta;   // dados obtido no primeiro passo
		
		$cliente = $this -> clienteBD;
		$dddref1 = $this -> extrairDDD($cliente['cli_telref1']);
		$dddref2 = $this -> extrairDDD($cliente['cli_telref2']);
		$dddbanc = $this -> extrairDDD($cliente['cli_telbanco']);
		$desde   = $this -> extrairAnoMes($cliente['cli_dtaberturaconta']);
		
		$codigoDigitoAgencia       = '0';//$cliente['cli_digitoagencia'];//1d
		$codigoDigitoContaCorrente = '8';//$cliente['cli_digitoconta'];//1d
		$codigoTipoContaBancaria   = $cliente['cli_tipoconta'];//1d
		$descricaoTelefoneBanco    = "723456789";//$cliente['cli_telbanco'];//10d
		$descricaoTelefoneRefer1   = '623456789';//$cliente['cli_telref1'];//10d
		$descricaoTelefoneRefer2   = '723456789';//$cliente['cli_telref1'];//10d
		$nomeRefer1                = 'Referencia Teste Dois';//$cliente['cli_nomeref1'];
		$nomeRefer2                = 'Referencia Teste Dois';//$cliente['cli_nomeoref2'];//50d
		$numeroAgencia             = '043';//$cliente['cli_numeroagenc'];//5d
		$numeroAnoClienteDesde     = $desde[0];//4d
		$numeroBanco               = '033';//$cliente['cli_numerobanco'];//3d
		$numeroContaCorrente       = '10103359';//$cliente['cli_conta'];//8d
		$numeroDddRefer1           = '11';//$dddref1;//3d
		$numeroDddRefer2           = '11';//$dddref2;//3d
		$numeroDddTelefoneBanco    = '11';//$dddbanc;//3d 
		$numeroMesClienteDesde     = $desde[1];//2d
		$numeroClienteInterno      = $this->nrInterveniente;
		$numeroClienteRelacional   = $this->nrInterveniente;
		$descricaoEndRefer1 = "40032001";
		$descricaoEndRefer2 = "623456789";
		
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
		
		$codigoGarantia = '01';				//2d
		$codigoObjFinanciado = 'VN';		//2d
		$descricaoModelo = "TIPO OBJETO FINANCIADO";//20d
		
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
			echo "ok - Eta mainha"; 
		} else { 
			echo $consolidar -> descricaoErro;
		}
	}	
}
?>