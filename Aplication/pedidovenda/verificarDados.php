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
 * @example verificarDados.php
 *
 * pagina feita para verificar em segundo plano se os dados ja estao 
 * granados no banco de dados
 */

include_once ("connection/conexao_bd.php");

$nomeUsuario = $_POST['nomeUsuario'];
$cliente = $_POST['cliente'];
$nomeCnpj = $_POST['nomeCnpj'];

// verificando se o cpf ja esta cadastrado
if ($nomeUsuario != "") {
	$nomeUsuario = str_replace(array(".", "/", "-"), "", $nomeUsuario);
	$pesquisa = "SELECT cli_cgccpf FROM clientes 
			 WHERE cli_cgccpf = '$nomeUsuario' ";
	$exe_pesquisa = mysql_query($pesquisa);
	if (mysql_num_rows($exe_pesquisa) > 0) {
		echo "1";
	}
}

// verificando se o cnpj ja esta cadastrado
if ($nomeCnpj != "") {
	$nomeCnpj = str_replace(array(".", "/", "-"), "", $nomeCnpj);
	$pesquisaCnpj = "SELECT cli_cgccpf FROM clientes 
			 WHERE cli_cgccpf = '$nomeCnpj' ";
	$exe_pesquisa_cnpj = mysql_query($pesquisaCnpj);
	if (mysql_num_rows($exe_pesquisa_cnpj) > 0) {
		echo "1";
	}
}

// pesquisando pelo nome fantasia
if ($cliente != "") {
	$pesquisaCliente = "SELECT cli_fantasia FROM clientes 
			 		WHERE cli_fantasia = '$cliente' ";
	$exe_pesquisaCliente = mysql_query($pesquisaCliente);
	if (mysql_num_rows($exe_pesquisaCliente) > 0) {
		echo "1";
	} else {
		echo "0";
	}
}
?>