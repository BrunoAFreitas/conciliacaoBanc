<?php
// teste para envio de dados para o servidor
include_once ("consultarFinanceira.php");

//extende o limite de tempo  de execução da pagina.
set_time_limit(1200);

// id de identificação do cliente.
$idCliente = "05754853378";

//objeto
$consulta = new consultarFinanceira($idCliente);
 	
//variaveis do sistema. 	
$codigoFormaPagamento = "CA";
$numeroIdProduto = "0206";
$valorFinanciamento = "10.000,00";
$codigoTipoMoeda = "PRE";
$dataVencimento1 = date('d/m/Y', strtotime("+30 days"));
$valorPrestacao = "277,80";
$valorBem = "20.000,00";
$numeroQuantidadePrestacoes = "36";
$numeroTabelaFinanciamento = "55785";
$codigoModalidade = "P";
$isencaoTC  = "N";
$isencaoTAB = "N";
$dataEntregaBem = date('d/m/Y');
$nomeVendedor = "Akarlos Vasconcelos";
$numeroVendedor = "13";
$indicadorTac = "N";
$textoControleLoja = "teste do sistema..";
$textoObsLoja = "Teste do sistema...";
$valorEntrada = "10.000,00";


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