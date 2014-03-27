<?php

//login do banco de dados
$usuario = "djmdec";

//senha do bd
$senha   = "1qaz2wsx";

//url, no caso a do mysql
$host    = "78.47.123.138"; //host do mysql

//variavel contendo a porta que deve ser usada
$porta   = "";

//execulta a conexão
$conexao = mysql_connect("$host:$porta",$usuario,$senha)or die ('no conected');
mysql_select_db("gercomweb",$conexao)or die ('no conected');

$endereco = 'localhost';

$hora = date("H:i:s");
$data = date("d/m/Y");

?>