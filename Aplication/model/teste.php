<?php
// teste para envio de dados para o servidor
include_once ("../model/imprimeCETPDF.php");

include_once ("consultarFinanceira.php");

//header('Content-type: application/pdf');
//extende o limite de tempo  de execução da pagina.
set_time_limit(1200);

// id de identificação do cliente.
$idCliente = $_POST['cpf'];//"05754853378";

//objeto
$consulta = new consultarFinanceira($idCliente);
 	
//variaveis do sistema. 	
$codigoFormaPagamento = 'CA';//$_POST['formapag'];//
$numeroIdProduto = '0206';//$_POST["numprod"];//
$valorFinanciamento = $_POST['valfinan'];//"10.000,00"
$codigoTipoMoeda = $_POST['tpmoeda'];//"PRE";
$dataVencimento1 = date('d/m/Y', strtotime("+30 days"));
$valorPrestacao = $_POST['valprest'];//"277,80";
$valorBem = $_POST['valbem'];//"20.000,00";
$numeroQuantidadePrestacoes = $_POST['qtprest'];//"36";
$numeroTabelaFinanciamento = "34053";//$_POST['tabfinan'];//
$codigoModalidade = $_POST['modalidade'];//"P";
$isencaoTC  = $_POST['tc'];//"N";
$isencaoTAB = $_POST['tab'];//"N";
$dataEntregaBem = date('d/m/Y');
$nomeVendedor = $_POST['vendedor'];//"Akarlos Vasconcelos";
$numeroVendedor = $_POST['numvendedor'];//"13";
$indicadorTac = $_POST['tac'];//"N";
$textoControleLoja = $_POST['textoloja'];//"teste do sistema..";
$textoObsLoja = $_POST['textobs'];//"Teste do sistema...";
$valorEntrada = $_POST['valentrada'];//"10.000,00";


//fazer a chamada do primerio passo
$consulta -> passo1($codigoFormaPagamento , $dataEntregaBem            , $codigoTipoMoeda, 
					$nomeVendedor         , $numeroIdProduto           , $numeroQuantidadePrestacoes, 
					$codigoModalidade     , $numeroTabelaFinanciamento , $numeroVendedor,
					$indicadorTac         , $isencaoTC                 , $isencaoTAB,
					$textoControleLoja    , $textoObsLoja              , $valorBem, 
					$valorEntrada         , $valorFinanciamento        , $valorPrestacao, 
					$dataVencimento1);



$test = $consulta -> getMensage();
//echo $test;

$numPro = $consulta -> getNumProposta();
/*
	$cet = new imprimeCETPDF("0038291854");
	$cet->executa();
	echo base64_decode($cet->pdfBase64Content);
	echo $cet->pdfBase64Content;
*/
?>