<?php
/***
 * Pagina feita para melhoria do sistema Gercom
 * do grupo Jacauna, Está pagina está livre para
 * bom uso e melhoria do sistema manter este comentário.
 *
 * @author Akarlos Vasconcelos
 * @version 1.0
 * @copyright Ruah Industria
 * @access private
 * @package pedido_de_venda
 * @example cli_cad.php
 *
 * pagina para pesquisa dos dados do cleinte essa pagina esta separada
 * para melhor manutenção
 */

include_once ("connection/conexao_bd.php");

$idCleinte = $_GET['id_cli'];
$tipo = $_GET['tipo'];

$pesquCleinte = "SELECT * FROM clientes WHERE cli_seq = '$idCleinte'";
$exe_pesqClei = mysql_query($pesquCleinte);

while ($lcleinte = mysql_fetch_array($exe_pesqClei)) {
	$cpf = $lcleinte['cli_cgccpf'];
	$razao = $lcleinte['cli_razao'];
	$inscrg = $lcleinte['cli_inscrg'];
	$rgemissor = $lcleinte['cli_rgemissor'];
	$rgdtemissao = $lcleinte['cli_rgdtemissao'];
	$sexo = $lcleinte['cli_sexo'];
	$dtnasc = $lcleinte['cli_dtnasc'];
	$naturalidade = $lcleinte['cli_naturalidade'];
	$nacionalidade = $lcleinte['cli_nacionalidade'];
	$timefutebol = $lcleinte['cli_timefutebol'];
	$estadocivil = $lcleinte['cli_estadocivil'];
	$conjuge = $lcleinte['cli_conjuge'];
	$pai = $lcleinte['cli_pai'];
	$mae = $lcleinte['cli_mae'];
	$qtdfilhos = $lcleinte['cli_qtdfilhos'];
	
	$tipoend = $lcleinte['cli_tipoend'];
	$endereco = $lcleinte['cli_end'];
	$numeroend = $lcleinte['cli_numeroend'];
	$complemento = $lcleinte['cli_complemento'];
	$tiporesid = $lcleinte['cli_tiporesid'];
	$residedesde = $lcleinte['cli_residedesde'];
	$estado = $lcleinte['cli_estado'];
	$cidadecod = $lcleinte['cli_cidadecod'];
	$bairrocod = $lcleinte['cli_bairrocod'];
	$cep = $lcleinte['cli_cep'];
	$pontoref = $lcleinte['cli_pontoref'];
	$endentrega = $lcleinte['cli_endentrega'];
	$email = $lcleinte['cli_email'];
	$fone = $lcleinte['cli_fone'];
	$celular1 = $lcleinte['cli_celular1'];
	$celular2 = $lcleinte['cli_celular2'];
	$operadora1 = $lcleinte['cli_operadora1'];
	$operadora2 = $lcleinte['cli_operadora2'];
	$usaredesocial = $lcleinte['cli_usaredesocial'];
	$redessociais = $lcleinte['cli_redessociais'];
	
	$profissaocod = $lcleinte['cli_profissaocod'];
	$trabalho = $lcleinte['cli_trabalho'];
	$dtadmtrab = $lcleinte['cli_dtadmtrab'];
	$fonetrab = $lcleinte['cli_fonetrab'];
	$tipoendtrab = $lcleinte['cli_tipoendtrab'];
	$endtrab = $lcleinte['cli_endtrab'];
	$numerotrab = $lcleinte['cli_numerotrab'];
	$complementotrab = $lcleinte['cli_complementotrab'];
	$estadotrab = $lcleinte['cli_estadotrab'];
	$cidadetrab = $lcleinte['cli_cidadetrab'];
	$bairrotrab = $lcleinte['cli_bairrotrab'];
	$ceptrab = $lcleinte['cli_ceptrab'];
	$rendamensal = $lcleinte['cli_rendamensal'];
	$outrasrendas = $lcleinte['cli_outrasrendas'];
	
	$banco = $lcleinte['cli_banco'];
	$agencia = $lcleinte['cli_agencia'];
	$conta = $lcleinte['cli_conta'];
	$tipoconta = $lcleinte['cli_tipoconta'];
	$dtaberturaconta = $lcleinte['cli_dtaberturaconta'];
	$obs = $lcleinte['cli_obs'];
	
}

// colocar um post para editar caso seja preciso

?>