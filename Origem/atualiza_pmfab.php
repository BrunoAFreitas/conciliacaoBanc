<?
  $sql2 = "SELECT pro_fabricante FROM produtos WHERE pro_cod = '$edtProd';";
  $query2 = mysql_query($sql2,$conexao)or die("Erro na Consulta!");
  $linha2 = mysql_fetch_object($query2);
  
  if ($linha2->pro_fabricante == ''){
	   $fab = '1';
  }else{
	   $fab = $linha2->pro_fabricante;			
  }
?>