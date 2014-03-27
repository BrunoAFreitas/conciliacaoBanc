<?php
/***
 * Pagina feita para melhoria do sistema Gercom
 * do grupo Jacauna, Está pagina está livre para
 * bom uso e melhoria do sistema.
 *
 * @author Akarlos Vasconcelos
 * @version 1.0
 * @copyright Ruah Industria
 * @access private
 * @package pedido_de_venda
 * @example autocomplete.php
 * 
 * pagina feita para enviar os dados de uma pesquisa para o autocomplete
 */

// incluindo conexao.php
include_once ("connection/conexao_bd.php");

$queryString = $_GET['q'];

$nome_cliente = "SELECT cli_razao FROM clientes 
				 WHERE cli_razao LIKE '%$queryString%'
				 ORDER BY cli_razao LIMIT 0,20 ";
$exe_nome_cliente = mysql_query($nome_cliente);

if (mysql_num_rows($exe_nome_cliente) > 0) {
	while ($nome = mysql_fetch_object($exe_nome_cliente)) {
		echo $nome -> cli_razao . "\n";
	}
} else {
	echo "Não cadastrado!";
}


?>