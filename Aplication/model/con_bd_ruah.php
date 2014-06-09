<?php
/**
 * MODIFICADO PARA PHP CODE STANDART PARCIALMENTE. 
 *
 * Este arquivo � parte do projeto NFeJacauna desenvolvido pelos desenvolvedores do grupo afim de adotar melhores tecnicas de   
 * preechimento automatizado dos arquivos XML apartir do romaneio por clinte. O script descrito abaixo n�o pode ser destribuido 
 * ou compartilhado com individuos externos a empresa.
 *
 * Arquivo de conex�o com banco de dados( Localhost, modificalo no host web!!)
 * Aten��o esse arquivo foi testado apenas em servidor local.
 *
 * � aconcelhavel adotar os padr�es de PHP code standart nas altera��es e/ou implementa��es desse documento(apostila de code 
 * standart contido na pasta PHP na pasta riz do projeto). Este documento ainda n�o adota todas as normas e os trexos que n�o
 * seram alvo de modifica��es.
 *
 * Qualquer duvida sobre o processo ou tags da NF-e consultar os manuais na pasta \documentos para estudo.
 *
 *
 * @package          NFeJacauna
 * @name             listaRom.php
 * @author           Bruno Ara�joFreitas <bruno.araujo@jacuna.net>
 *                   Jalen Fabio         <...>
 * @version          1.2
 *
 * Bruno Ara�jo | Ruah industrias | 2012-03-15
 */

//login do banco de dados
$usuario = "dgercom";

//senha do bd
$senha   = "2012ruah2012";

//url, no caso a do mysql
$host    = "91.194.91.10"; //host do mysql

//variavel contendo a porta que deve ser usada
$porta   = "";

//execulta a conex�o
$conexao = mysql_connect("$host:$porta",$usuario,$senha)or die ('no conected');
mysql_select_db("dgercom",$conexao)or die ('no conected');

$endereco = 'localhost';

$hora = date("H:i:s");
$data = date("d/m/Y");

?>