<?php
include ("conexao2.inc.php");

if ($acelogin != ""){

 $data = date("d/m/Y");
 $data = muda_data_en($data);
 $hora = date("H:i:s");
 $ip = $_SERVER["REMOTE_ADDR"];	 

  $sql_aud = "SELECT ace_login FROM acessos WHERE ace_login = '$acelogin';";
  //echo $sql_aud;
  $query_aud = mysql_query($sql_aud, $conexao) or die ("Erro na Auditoria Interna");
  if(mysql_num_rows($query_aud) > 0){
    $linha_aud = mysql_fetch_object($query_aud);
    $sql_grv_aud   = "INSERT INTO logacessos (log_login, log_data, log_loja, log_hora, log_arquivo, log_ip)
                      VALUES ('".$linha_aud->ace_login."', '$data', '$ljcod', '$hora', '$arquivo', '$ip');";
	//echo $sql_grv_aud;								  
    $query_grv_aud = mysql_query($sql_grv_aud,$conexao)or die("Erro!");
  }
 }else{
   echo "Problemas na sua conexão, reinicie o computador.";
 }
?>
