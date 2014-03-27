<?php
include("conexao2.inc.php");

  mysql_query(" SET NAMES 'utf8' ");
  mysql_query(' SET character_set_connection=utf8 ');
  mysql_query(' SET character_set_client=utf8 ');
  mysql_query(' SET character_set_results=utf8 ');

  $cidade = $_POST['edtCidade']; 
  $sql=mysql_query("SELECT `bair_cod`,`bair_desc` FROM `bairros` WHERE `bair_cidade`='$cidade' ORDER BY `bairros`.`bair_desc` ASC");
  //verifica se o resultado da pesquisa foi positivo ou não	  
  if(mysql_num_rows($sql) == 0){
    echo '<option value="-1" selected>Nada Encontrado</option>';
  }else{
    //se resultado positivo entao mostra as cidades no combo
    while($result_sql = mysql_fetch_object($sql)){
		$cod_bairro=$result_sql->bair_cod;
		$desc_bairro= $result_sql->bair_desc ;
		echo "<option value=$cod_bairro>$desc_bairro</option>";
    }
  }
?>