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
 * @example conexao_bd.php
 */

// variaveis para conexão com o servidor e com o banco
$porta = "localhost";
$login = "root";
$senha = "";
$banco = "gercomweb";

//$porta = "91.194.91.10";
//$login = "dgercom";
//$senha = "2012ruah2012";
//$banco = "dgercom";

//conexão com o servidor
$conexao = mysql_connect($porta, $login, $senha);

// caso sua conexão não seja executado ira mostrar uma mensagem
if (!$conexao)
	die("<center><h1>Falha na conexao com o servidor!</h1>" . "<br/>" . mysql_error() . "</center>");

// Caso a conexão seja aprovada, então conecta o Banco de Dados.
mysql_select_db($banco, $conexao);
if (!mysql_select_db)
	die("<center><h1>Falha na conexao com o Banco de Dados!</h1>" . "<br/>" . mysql_error() . "</center>");

?>