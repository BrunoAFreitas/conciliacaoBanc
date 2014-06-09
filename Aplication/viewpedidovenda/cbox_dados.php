<?php
/**
 * Pagina feita para melhoria do sistema Gercom
 * do grupo Jacauna, Está pagina está livre para
 * bom uso e melhoria do sistema manter este comentário.
 *
 * @author Akarlos Vasconcelos
 * @version 1.0
 * @copyright Ruah Industria
 * @access private
 * @package pedido_de_venda
 * @example cbox_dados.php
 * 
 * Pagina para criar os boxes de pesquisa de Cidade e Bairros
 * onde recebe os dados passados por post
 */
 
// pegando a conexao com o banco de dados
include_once ("connection/conexao_bd.php");

$uf = $_POST['edtEstado'];
$cidade = $_POST['edtCidade'];

// para a pesquisa de cidade
if ($uf != "") {
	$sql = mysql_query("SELECT fmun_desc,fmun_cod FROM fmunicipios where fmun_uf = '$uf' order by fmun_desc");
	//verifica se o resultado da pesquisa foi positivo ou não
	if (mysql_num_rows($sql) == 0) {
		echo '<option value="-1" selected>Nada Encontrado</option>';
	} else {
		//se resultado positivo entao mostra as cidades no combo
		echo '<option value="0" selected>ESCOLHA UMA CIDADE</option>';
		while ($result_sql = mysql_fetch_object($sql)) {
			$cod_mun = $result_sql -> fmun_cod;
			$municipio = $result_sql -> fmun_desc;
			echo "<option value=$cod_mun>$municipio</option>";
		}
	}
}

// para a pesquisa de bairros
if ($cidade != "") {
	$sql1 = mysql_query("SELECT bair_cod, bair_desc,bair_cidade FROM bairros 
					WHERE bair_cidade = '$cidade' ORDER BY bair_desc ASC");
	//verifica se o resultado da pesquisa foi positivo ou não
	if (mysql_num_rows($sql1) == 0) {
		echo '<option value="-1" selected>Nada Encontrado</option>';
	} else {
		//se resultado positivo entao mostra as cidades no combo
		while ($result_sql = mysql_fetch_object($sql1)) {
			$cod_bairro = $result_sql->bair_cod;
			$desc_bairro = $result_sql->bair_desc;
			echo "<option value=$cod_bairro>$desc_bairro</option>";
			
		}
	}
}
?>