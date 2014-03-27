<?php
include("conexao2.inc.php");
  $uf = $_POST['edtEstado']; 
  $sql=mysql_query("SELECT fmun_desc,fmun_cod FROM fmunicipios where fmun_uf = '$uf' order by fmun_desc;");
  //verifica se o resultado da pesquisa foi positivo ou não	  
  if(mysql_num_rows($sql) == 0){
    echo '<option value="-1" selected>Nada Encontrado</option>';
  }else{
    //se resultado positivo entao mostra as cidades no combo
	echo '<option value="0" selected>ESCOLHA UMA CIDADE</option>';
    while($result_sql = mysql_fetch_object($sql)){
		$cod_mun=$result_sql->fmun_cod;
		$municipio=$result_sql->fmun_desc;
		echo "<option value=$cod_mun>$municipio</option>";
    }
  }
?>