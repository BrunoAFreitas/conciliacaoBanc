<?


	// Modalidades Outros Bens
		/*************************************************/
		include_once("../controller/dominios/modalidadeOutrosBens.php");
		$codigoCanal = "0008";
		$usuario = "rosani";
		$numeroTipoFinanciamento = "11";

		$modalidadeOutrosBens = new modalidadeOutrosBens( $codigoCanal, $usuario,	$nomeTipoRetorno );

		$modalidadeOutrosBens->executa();

		if($modalidadeOutrosBens->codigoRetorno != "00") echo $modalidadeOutrosBens->descricaoErro;

				echo count($modalidadeOutrosBens->modalidadesOutrosBens);

				for($i=0; $i <count($modalidadeOutrosBens->modalidadesOutrosBens); $i++) {
					$opcao = $modalidadeOutrosBens->modalidadesOutrosBens[$i]; 

					//echo $opcao->codigo;
					echo "<pre>";
					print_r($opcao);
					echo "</pre>";
				}

		//exit();
 



	//  FormasPagamento
		/**************************************************
		include_once("../controller/dominios/formaPagamento.php");
		$codigoCanal = "0008";
		$usuario = "rosani";
		$nomeTipoRetorno = "2";

		$formaPagamento = new formaPagamento( $codigoCanal, $usuario,	$nomeTipoRetorno );

		$formaPagamento->executa();

		if($formaPagamento->codigoRetorno != "00") 

		echo "<pre>";
		echo $formaPagamento->descricaoErro;
		print_r(count($formaPagamento->formaPagamentos));
		echo "</pre>";

				for($i=0; $i <count($formaPagamento->formaPagamentos); $i++) {
					$opcao = $formaPagamento->formaPagamentos[$i]; 
					echo "<pre>";
					print_r($opcao);
					echo "</pre>";
				}
		//exit();
 	

	 // Dominios Gerais
	/**************************************************
	include_once("../controller/dominios/dominio.php");
	$codigoCanal = "0008";
	$usuario = "rosani";
	$codigoDominio = "33";

	$dominio = new dominio( $codigoCanal, $usuario,	$codigoDominio );

	$dominio->executa();

	if($dominio->codigoRetorno != "00") {
		echo "<pre>";
		print_r($dominio->descricaoErro);
		print_r(count($dominio->dominios));
		echo "</pre>";
	}
	
	for($i=0; $i <count($dominio->dominios); $i++) {
		$opcao = $dominio->dominios[$i]; 		
		echo "<pre>";
		print_r($opcao);
		echo "</pre>";
	}
	
 	


    // Lista bancos  
	/**************************************************
	include_once("../controller/dominios/banco.php");
	$codigoCanal = "0008";
	$usuario = "rosani";
	$codigoFormaPagamento = "DC";

	$banco = new banco( $codigoCanal, $usuario,	$codigoFormaPagamento );
	$banco->executa();

	if($banco->codigoRetorno != "00") 

	echo "<pre>";
	print_r($banco->descricaoErro);
	print_r(count($banco->bancos));
	echo "</pre>";

			for($i=0; $i <count($banco->bancos); $i++) {
				$opcao = $banco->bancos[$i]; 
				
				echo "<pre>";
				print_r($opcao);
				echo "</pre>";
				
			}
	//exit();
	
   
   // atividade economica
	/**************************************************
	include_once("../controller/dominios/atividadeEconomica.php");
	$codigoCanal = "0008";
	$usuario = "rosani";
	$codigoTipoVeiculo = "codigoGrupoAtividade";

	$atividadeEconomica = new atividadeEconomica( $codigoCanal,$usuario,$codigoTipoVeiculo );
	$atividadeEconomica->executa();

	if($atividadeEconomica->codigoRetorno != "00")
	echo "<pre>";
	print_r($atividadeEconomica->descricaoErro);
	print_r(count($atividadeEconomica->atividades));
	echo "</pre>";

			for($i=0; $i <count($atividadeEconomica->atividades); $i++) {
				$opcao = $atividadeEconomica->atividades[$i];
				echo "<pre>";
				print_r($opcao);
				echo "</pre>";
			}
	

	//exit();
	
   
	 
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	 
	  
	  
	  
	  
	  
	 
	// anoCombustivel
	/**************************************************/
	/***
	include_once("./metodos/dominios/anoCombustivel.php");
	$codigoCanal = "0011";
	$usuario = "fibrasil";
	$codigoTipoVeiculo = "N";
	$numeroMarca = "0004";
	$codigoEmpresa = "002";
	$numeroModelo = "0318";

	$combustiveis = new anoCombustivel( 
		$codigoCanal,
		$usuario,
		$codigoTipoVeiculo,
		$numeroMarca,
		$codigoEmpresa,
		$numeroModelo
	);
	$combustiveis->executa();

	if($combustiveis->codigoRetorno != "00") echo $combustiveis->descricaoErro;

	echo count($combustiveis->combustiveis);

			for($i=0; $i <count($combustiveis->combustiveis); $i++) {
				$opcao = $combustiveis->combustiveis[$i]; 
				//echo $opcao->combustiveis->codigo . " - ". $opcao->combustiveis->descricao ;
				print_r($opcao);
			}

	exit();
 	*/
 
 	/***************
	include_once("../model/soapSantander.php");
	
	$username = "fibrasil";
	$key = "93874382DB99B855";
	$cnpj = "14443697000180";     
	$codigoGrupoCanal = "0011.003";
	$numeroIntermediario = "420648";

	$enderecoEndPoint = "https://afc.santanderfinanciamentos.com.br:443/afc-services/FinanciamentosOnlineEndpointService";
	$soap =  new clsSantanderSoap($username, $key, $cnpj, $codigoGrupoCanal, $numeroIntermediario);
    $soap->setStrImplementacao("imprimeCETPDF");
    $soap->setParametro("numeroPropostaADP","0038291854");

	$soap->consomeWebService($enderecoEndPoint);

	$x = $soap->toArray("impressaoClientResponse");
   	$retorno = $x[0];

	echo $retorno->pdfBase64Content;
 	
    /**************************************************
	include_once("../controller/imprimeCETPDF.php");
	$cet = new imprimeCETPDF("0038291854");
	$cet->executa();
	header('Content-type: application/pdf');
	echo base64_decode($cet->pdfBase64Content);
	echo $cet->pdfBase64Content;
    /**************************************************/


   /***
		SIMULAÇÃO COMPLETA
	*/

	// Simulação outros bens
	/**************************************************
	$numeroIntermediario = "148425";
	$codigoIndicadorEntradaIgualParcela = "N";
	$codigoIndicadorProcedencia = "";
	$codigoIndicadorSeguro = "";
	$codigoIndicadorTacAVista = "";
	$codigoIndicadorTaxi = "";
	$codigoIndicadorVeiculoAdaptado = "";
	$codigoIndicadorZeroKm = "";
	$codigoModalidade = "P";
	$codigoModeloVeiculo = "";
	$codigoObjeto = "VN";
	$codigoPacote = "";
	$codigoTarifaCadastroRenovacao = "";
	$codigoTipoCombustivel = "";
	$codigoTipoPagamento = "CA";
	$codigoTipoPessoa = "F";
	$controleLojista = "";
	$dataPrimeiroVencimento = date('d/m/Y', strtotime("+30 days"));
	$numeroAnoFabricacao = "";
	$numeroAnoModeloVeiculo = "";
	$numeroBanco = "";
	$numeroCpfCnpj = "30030030030";
	$numeroMarca = "";
	$numeroParcelas = "";
	$numeroProduto = "0015";
	$numeroTabelaFinanciamento = "48884";
	$valorAproxParcela = "";
	$valorEntrada = "";
	$valorTotal = "5.000,00";

	include_once("./metodos/proposta/simulacaoOutrosBens.php");

	$simulacaoOutros = new simulacaoOutrosBens( $numeroIntermediario, $codigoIndicadorEntradaIgualParcela, $codigoIndicadorProcedencia,	$codigoIndicadorSeguro,	$codigoIndicadorTacAVista,	$codigoIndicadorTaxi,	$codigoIndicadorVeiculoAdaptado,	$codigoIndicadorZeroKm,	$codigoModalidade,	$codigoModeloVeiculo,	$codigoObjeto,	$codigoPacote,	$codigoTarifaCadastroRenovacao,	$codigoTipoCombustivel,	$codigoTipoPagamento,	$codigoTipoPessoa,	$controleLojista,	$dataPrimeiroVencimento,	$numeroAnoFabricacao,	$numeroAnoModeloVeiculo,	$numeroBanco,	$numeroCpfCnpj,	$numeroMarca,	$numeroParcelas,	$numeroProduto,	$numeroTabelaFinanciamento,	$valorAproxParcela,	$valorEntrada,	$valorTotal );
	$simulacaoOutros->executa();

	// nome de variaveis de retorno
	echo "<pre>";
	print_r($simulacaoOutros->simulacaoFinanciamentos);
	print_r($simulacaoOutros->simulacaoPropostas[0]);
	echo "</pre>";


	// Veículo
	/**************************************************
	$codigoCanal = "0011";
	$codigoGrupoCanal = "003";
	$codigoReferenciaOperacao = "123456789";
	$dataOrigem = "24";
	$horaOrigem = "111000";
	$numeroAreaNegocio = "10";
	$numeroEmpresa = "002";
	$numeroIntermediario = "421071";
	$codigoEstado = "SP";
	$codigoFrota = "00";
	$codigoIndicadorEntradaIgualParcela = "N";
	$codigoIndicadorProcedencia = "N";
	$codigoIndicadorSeguro = "N";
	$codigoIndicadorTaxi = "N";
	$codigoIndicadorVeiculoAdaptado = "S";
	$codigoIndicadorZeroKm = "S";
	$codigoModalidade = "P";
	$codigoObjeto = "AU";
	$codigoSegmento = "100";
	$codigoTipoCombustivel = "G";
	$codigoTipoPagamento = "CA";
	$codigoTipoPessoa = "F";
	$controleLojista = "123456789";
	$dataPrimeiroVencimento = date('d/m/Y', strtotime("+30 days"));
	$numeroAnoFabricacao = "2012";
	$numeroAnoModeloVeiculo = "2013";
	$numeroCpfCnpj = "30527359858";
	$numeroMarca = "00004";
	$codigoModeloVeiculo = "0318";
	$numeroParcelas = "24";
	$numeroProduto = "0015";
	$numeroTabelaFinanciamento = "59012";
	$valorAproxParcela = "";
	$valorEntrada = "10.000,00";
	$valorTotal = "20.000,00";
	$isencaoTC = "N";
	$isencaoTAB = "N";
	$codigoIndicadorTacAVista = "N";
	$codigoTarifaCadastroRenovacao = "";

	include_once("../controller/simulacaoVeiculo.php");
	$simulacao = new simulacaoVeiculo(
	$codigoCanal, $codigoGrupoCanal, $codigoReferenciaOperacao, $dataOrigem,  $horaOrigem, $numeroAreaNegocio, 	$numeroEmpresa, $numeroIntermediario, $codigoEstado, $codigoFrota, $codigoIndicadorEntradaIgualParcela, $codigoIndicadorProcedencia, $codigoIndicadorSeguro, $codigoIndicadorTaxi, $codigoIndicadorVeiculoAdaptado, $codigoIndicadorZeroKm, 		$codigoModalidade, 	$codigoObjeto, 		$codigoSegmento, $codigoTipoCombustivel, $codigoTipoPagamento, 	$codigoTipoPessoa, 	$controleLojista, 		$dataPrimeiroVencimento, 	$numeroAnoFabricacao, 	$numeroAnoModeloVeiculo, 	$numeroCpfCnpj, $numeroMarca, 	$codigoModeloVeiculo, 	$numeroParcelas, $numeroProduto, 		$numeroTabelaFinanciamento, $valorAproxParcela, $valorEntrada, 	$valorTotal, 	$isencaoTC, 	$isencaoTAB, 	$codigoIndicadorTacAVista, 		$codigoTarifaCadastroRenovacao	);
	$simulacao->executa();

	echo "<pre>";
	print_r($simulacao->simulacaoFinanciamentos);
	print_r($simulacao->simulacaoPropostasComSeguro[0]);
	print_r($simulacao->simulacaoPropostaSemSeguro[1]);	 
	echo "</pre>";
	/**************************************************/

	/***
		Etapas que todos os tipode de financiomentos teram que passar
	*/
	
	// Criar proposta (passo 1)
	/**************************************************/
	include_once("../controller/criarProposta.php");

	$codigoComissao = "00";
	$codigoFormaPagamento = "CA";
	$codigoModalidade = "P";
	$codigoTipoMoeda = "PRE";
	$dataVencimento1 = date('d/m/Y', strtotime("+30 days"));
	$indicadorTac = "N";
	$numeroIdProduto = "15";
	$numeroQuantidadeDiasCarencia = "30";
	$numeroQuantidadePrestacoes = "48";
	$numeroTabelaFinanciamento = "59525";
	$valorBem = "80.000,00";
	$valorEntrada = "20.000,00";
	$valorFinanciamento = "60.000,00";
	$valorPrestacao = "434,08";
	$isencaoTAB = "N";
	$isencaoTC  = "N";

	$criarProposta = new criarProposta( $codigoComissao, $codigoFormaPagamento,$codigoModalidade,$codigoTipoMoeda,$dataVencimento1,$indicadorTac,$numeroIdProduto,$numeroQuantidadeDiasCarencia,$numeroQuantidadePrestacoes,$numeroTabelaFinanciamento,$valorBem,$valorEntrada,$valorFinanciamento,$valorPrestacao,$isencaoTAB,$isencaoTC );
	$criarProposta->executa();

	echo "<pre>".$criarProposta->numeroPropostaAdp."</pre>";
		 

	// Associa interveniente (passo 2)
	/**************************************************/
	include_once("../controller/criarInterveniente.php");
 
	$nrProposta  = $criarProposta->numeroPropostaAdp;
	$codigoDocumento = "425126549";
	$codigoEstadoNaturalidade = "PR";
	$codigoEstadoOrgaoEmissor = "SP";
	$codigoNacionalidade = "BR";
	$codigoPaisDocumento = "SP";
	$codigoSexo = "M";
	$codigoTipoPessoa = "F";
	$dataAdmissao = "02/02/2010";
	$dataEmissaoDocumento = "05/03/1995";
	$dataNascimento = "05/12/1982";
	$descricaoNaturalidade = "Maringá";
	$descricaoProfissao = "MOTORISTA";
	$indicativoDeficienteFisico = "N";
	$indicativoFuncSantander = "N";
	$nomeCompleto = "TESTE Sistema";
	$nomeEmpresa = "NOME EMPRESA PROPONENTE";
	$nomeMae = "MAE DO PROPONENTE";
	$nomeOrgaoEmissor = "SSP";
	$numeroComprovanteRenda = "1";
	$numeroCpfCnpj = "30030030030";
	$numeroEstadoCivil = "1";
	$numeroInstrucao = "4";
	$numeroOcupacao = "3";
	$numeroPessoaPoliticaExpo = "0";
	$numeroRenda = "1";
	$numeroTipoDocumento = "1";
	$numeroTipoVinculoPart = "1";
	$valorPatrimonio = "5.000.000,00";
	$valorRendaMensal = "5.400,00";

	$interveniente = new criarInterveniente( $nrProposta, $codigoDocumento,$codigoEstadoNaturalidade,$codigoEstadoOrgaoEmissor,$codigoNacionalidade,$codigoPaisDocumento,$codigoSexo,$codigoTipoPessoa,$dataAdmissao,$dataEmissaoDocumento,$dataNascimento,$descricaoNaturalidade,$descricaoProfissao,$indicativoDeficienteFisico,$indicativoFuncSantander,$nomeCompleto,$nomeEmpresa,$nomeMae,$nomeOrgaoEmissor,$numeroComprovanteRenda,$numeroCpfCnpj,$numeroEstadoCivil,$numeroInstrucao,$numeroOcupacao,$numeroPessoaPoliticaExpo,$numeroRenda,$numeroTipoDocumento,$numeroTipoVinculoPart,$valorPatrimonio, $valorRendaMensal );
	$interveniente->executa();
 	$nrInterveniente = $interveniente->numeroInternoCliente;

	echo "<pre>";
	echo $interveniente->numeroInternoCliente;
 	echo "</pre>";


	// associa endereco ao interveniente (passo 3)
	/**************************************************/
	include_once("../controller/associarEnderecoInterveniente.php");

	$nrProposta = $criarProposta->numeroPropostaAdp;			
	$codigoEnderecoCorrespondencia = "R";
	$codigoPaisComercial = "BR";
	$codigoPaisResidencial = "BR";
	$codigoSiglaUfComercial = "AC";
	$codigoSiglaUfResidencial = "AC";
	$dataAnoResideDesde = "6";
	$dataMesResideDesde = "2";
	$descricaoComplementoResidencia = "CJ. 11";
	$descricaoEnderecoComercial = "Rua Pernambuco";
	$descricaoEnderecoEmail = "teste@teste.com";
	$descricaoEnderecoResidencia = "Rua Pernambuco";
	$descricaoTelComercial = "74652221";
	$descricaotelResidencial = "74652221";
	$nomeBairroComercial = "Dom Giocondo";
	$nomeBairroResidencial = "Dom Giocondo";
	$nomeCidadeComercial = "Rio Branco";
	$nomeCidadeResidencial = "Rio Branco";
	$numeroCepComercial = "69900306";
	$numeroCepResidencial = "69900306";
	$numeroClienteInterno = $nrInterveniente;
	$numeroDddResidencial = "11";
	$numeroDddTelComercial = "11";
	$numeroEnderecoComercial = "158";
	$numeroResidencial = "1893";
	$numeroTipoResidencia = "1";
	$numeroTipoTelefResiden  = "1";

	$endereco= new associarEnderecoInterveniente( $nrProposta,	$codigoEnderecoCorrespondencia, $codigoPaisComercial, $codigoPaisResidencial, $codigoSiglaUfComercial, $codigoSiglaUfResidencial, $dataAnoResideDesde, $dataMesResideDesde, $descricaoComplementoResidencia, $descricaoEnderecoComercial, $descricaoEnderecoEmail, $descricaoEnderecoResidencia, $descricaoTelComercial, $descricaotelResidencial, $nomeBairroComercial, $nomeBairroResidencial, $nomeCidadeComercial,  $nomeCidadeResidencial, $numeroCepComercial, $numeroCepResidencial, $numeroClienteInterno, $numeroDddResidencial, $numeroDddTelComercial, $numeroEnderecoComercial, $numeroResidencial,  $numeroTipoResidencia, $numeroTipoTelefResiden	 );
	$endereco->executa();

	if($endereco->codigoRetorno == "00") {
		echo "<pre>";
		echo "ok";
		echo "</pre>";
 	} else {
 		echo "<pre>";
		echo  $endereco->descricaoErro;                                              
		echo "</pre>";
 	}


	// associa referencia (passo 4)
	/**************************************************/
	include_once("../controller/proposta/associaReferencia.php");

	$nrProposta = $criarProposta->numeroPropostaAdp;			
	$codigoDigitoAgencia = "0";
	$codigoDigitoContaCorrente = "8";
	$codigoTipoContaBancaria = "C";
	$descricaoEndRefer1 = "40032001";
	$descricaoEndRefer2 = "623456789";
	$descricaoTelefoneBanco = "723456789";
	$descricaoTelefoneRefer1 = "623456789";
	$descricaoTelefoneRefer2 = "723456789";
	$nomeRefer1 = "REFERENCIA UM PROPONENTE";
	$nomeRefer2 = "REFERENCIA DOIS PROPONENTE";
	$numeroAgencia = "043";
	$numeroAnoClienteDesde = "2001";
	$numeroBanco = "033";
	$numeroClienteInterno = $nrInterveniente;
	$numeroClienteRelacional = $nrInterveniente;
	$numeroContaCorrente = "101033599";
	$numeroDddRefer1 = "11";
	$numeroDddRefer2 = "11";
	$numeroDddTelefoneBanco = "11";
	$numeroMesClienteDesde = "01";

	$referencia = new associaReferencia( $nrProposta, $codigoDigitoAgencia,  $codigoDigitoContaCorrente, $codigoTipoContaBancaria, $descricaoEndRefer1, $descricaoEndRefer2, $descricaoTelefoneBanco, $descricaoTelefoneRefer1, $descricaoTelefoneRefer2, $nomeRefer1, $nomeRefer2, $numeroAgencia, $numeroAnoClienteDesde, $numeroBanco, $numeroClienteInterno, $numeroClienteRelacional,  $numeroContaCorrente, $numeroDddRefer1, $numeroDddRefer2, $numeroDddTelefoneBanco, $numeroMesClienteDesde );
	$referencia->executa();

	if($referencia->codigoRetorno == "00") {
		echo "<pre>";
		echo "ok"; 
		echo "</pre>";
	} else {
		echo "<pre>";
		echo $referencia->descricaoErro;
		echo "</pre>";
 	}

		   
	// associa garantia(passo 5)
	/**************************************************/
	include_once("../controller/associaGarantia.php");

	$nrProposta =  (String) $criarProposta->numeroPropostaAdp;
	$codigoEstadoLicenciamento123= "SP123";
	$codigoTipoCombustivel= "G";
	$indicativoAdaptado= "N";
	$indicativoProcVeiculo= "I";
	$indicativoTaxi= "N";
	$indicativoZeroKm= "S";
	$numeroAnoFabricacao= "2013";
	$numeroAnoModelo= "2013";
	$numeroTabMarca= "00004";
	$numeroTabModelo= "0357";
	$codigoEstadoPlaca= "SP";
	$codigoRenavam= "9999009999";
	$descricaoChassi= "QWERTY12345";
	$descricaoCor= "PRETO";
	$descricaoMarca= "DESCRICAO MARCA";
	$codigoGarantia= "1"; // ????????????
	$codigoObjFinanciado= "AN";
	$descricaoModelo= "DESCRICAO MODELO";
	$descricaoPlaca= "AAA1234";
	$indicativoBemFinanciado= ""; //???
	$numeroGarantia= ""; //???
	$porcentagemDiferVeiculo= ""; //?
	$porcentagemEntrVeiculo= ""; // ???
	$valorVeiculoMercado= ""; //???
	$valorVenda = ""; //???

	$garantia = new associaGarantia( $nrProposta, $codigoEstadoLicenciamento123, $codigoTipoCombustivel, $indicativoAdaptado, $indicativoProcVeiculo, $indicativoTaxi, $indicativoZeroKm, $numeroAnoFabricacao, $numeroAnoModelo, $numeroTabMarca, $numeroTabModelo, $codigoEstadoPlaca, $codigoRenavam, $descricaoChassi, $descricaoCor, $descricaoMarca, $codigoGarantia, $codigoObjFinanciado, $descricaoModelo, $descricaoPlaca, $indicativoBemFinanciado, $numeroGarantia, $porcentagemDiferVeiculo,  $porcentagemEntrVeiculo, $valorVeiculoMercado,$valorVenda );
	$garantia->executa();

	if($garantia->codigoRetorno == "00") {
		echo "<pre>";
		echo "ok";
		echo "</pre>";
	} else {
		echo "<pre>";
		echo $garantia->descricaoErro;
		echo "</pre>";
	}
	// associa garantia (passo 6)
	/**************************************************/
	include_once("../controller/consolidarProposta.php");

	$nrProposta =  (String) $criarProposta->numeroPropostaAdp;

	$consolidar = new consolidarProposta( $nrProposta, "114228");
	$consolidar->executa();
	
	echo "<pre>";
	echo  $consolidar->dadosADP->descricaoCor;
	print_r($consolidar->dadosADP);
	echo "</pre>";
	
	if($consolidar->codigoRetorno == "00") {
		echo "<pre>";
		echo "ok"; 
		echo "</pre>";
	} else { 
		echo "<pre>";
		echo $consolidar->descricaoErro;
		echo "</pre>";
	}
	/**************************************************/
 
?>