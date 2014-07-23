<?php
// teste para envio de dados para o servidor
/**
 * Não esta recebendo todos os dados vindos do view, preciso escrever os passos para ver o que esta acontecendo de errado com o arquivo
*/
include_once ("../model/imprimeCETPDF.php");

include_once ("consultarFinanceira.php");

//header('Content-type: application/pdf');
//extende o limite de tempo  de execução da pagina.
set_time_limit(1200);

// id de identificação do cliente.
$idCliente = "33333333333";

//objeto
$consulta = new consultarFinanceira($idCliente);
 	
//variaveis do sistema. 	
$codigoFormaPagamento = 'CA';//$_POST['formapag'];//
$numeroIdProduto = '0206';//$_POST["numprod"];//
$valorFinanciamento = "10000.00";
$codigoTipoMoeda = "PRE";
$dataVencimento1 = date('d/m/Y', strtotime("+30 days"));
$valorPrestacao = "277.00";
$valorBem = "20000.00";
$numeroQuantidadePrestacoes = "36";
$numeroTabelaFinanciamento = "32531";//"34053";//$_POST['tabfinan'];//
$codigoModalidade ="P";
$isencaoTC  ="N";
$isencaoTAB ="N";
$dataEntregaBem = date('d/m/Y');
$nomeVendedor ="Akarlos Vasconcelos";
$numeroVendedor ="13";
$indicadorTac = "N";
$textoControleLoja ="teste do sistema";
$textoObsLoja = "Teste do sistema";
$valorEntrada = "10000.00";


//fazer a chamada do primerio passo
$consulta -> passo1($codigoFormaPagamento , $dataEntregaBem            , $codigoTipoMoeda, 
					$nomeVendedor         , $numeroIdProduto           , $numeroQuantidadePrestacoes, 
					$codigoModalidade     , $numeroTabelaFinanciamento , $numeroVendedor,
					$indicadorTac         , $isencaoTC                 , $isencaoTAB,
					$textoControleLoja    , $textoObsLoja              , $valorBem, 
					$valorEntrada         , $valorFinanciamento        , $valorPrestacao, 
					$dataVencimento1);



$test = $consulta -> getMensage();


$numPro = $consulta -> getNumProposta();
/*
	$cet = new imprimeCETPDF("0038291854");
	$cet->executa();
	echo base64_decode($cet->pdfBase64Content);
	echo $cet->pdfBase64Content;
*/
?>