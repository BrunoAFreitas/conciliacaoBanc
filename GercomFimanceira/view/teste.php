<?php
// teste para envio de dados para o servidor
include_once ("consultarFinanceira.php");

//extende o limite de tempo  de execução da pagina.
set_time_limit(1200);

// id de identificação do cliente.
$idCliente = "44830171000113";

//objeto
$consulta = new consultarFinanceira($idCliente);
 	
//variaveis do sistema. 	
$codigoFormaPagamento = "CA";
$numeroIdProduto = "0206";
$valorFinanciamento = "60.000,00";
$codigoTipoMoeda = "PRE";
$dataVencimento1 = date('d/m/Y', strtotime("+30 days"));
$valorPrestacao = "434,08";
$valorBem = "80.000,00";
$numeroQuantidadePrestacoes = "48";
$numeroTabelaFinanciamento = "55785";
$codigoModalidade = "P";
$isencaoTC  = "S";
$isencaoTAB = "N";
$dataEntregaBem = date('d/m/Y');
$nomeVendedor = "Vendedor Teste";
$numeroVendedor = "13";
$indicadorTac = "N";
$textoControleLoja = "teste do sistema..";
$textoObsLoja = "Teste do sistema...";
$valorEntrada = "20.000,00";


//fazer a chamada do primerio passo
$consulta -> passo1($codigoFormaPagamento , $dataEntregaBem            , $codigoTipoMoeda, 
					$nomeVendedor         , $numeroIdProduto           , $numeroQuantidadePrestacoes, 
					$codigoModalidade     , $numeroTabelaFinanciamento , $numeroVendedor,
					$indicadorTac         , $isencaoTC                 , $isencaoTAB,
					$textoControleLoja    , $textoObsLoja              , $valorBem, 
					$valorEntrada         , $valorFinanciamento        , $valorPrestacao, 
					$dataVencimento1);



$test = $consulta -> getMensage();
echo $test;

?>