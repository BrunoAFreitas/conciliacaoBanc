<?
	include ("conexao2.inc.php");
    /* Define o limitador de cache para 'private' */ 
//	session_cache_limiter('private'); 
//	$cache_limiter = session_cache_limiter(); 
	
	/* Define o limite de tempo do cache em 30 minutos */ 
//	session_cache_expire(30); 
//	$cache_expire = session_cache_expire(); 

	//echo "O limitador de cache esta definido agora como $cache_limiter<br />"; 
	//echo "As sessões em cache irão expirar em $cache_expire minutos";	
	
	/* Inicia a sessão */ 
	
      session_start();
	  if(!session_is_registered("codemp") AND !session_is_registered("passemp")
               AND !session_is_registered("ljcod") AND !session_is_registered("passlj") 
			   AND !session_is_registered("acelogin") AND !session_is_registered("acesenha"))
	  {
    	echo "<table width='100%'>";
		echo "<tr>";
			echo "<td width='20%'><img src='imagens/restrito.jpg'></td>";
			echo "<td width='80%'> <font size='4' face='verdana'>Sua sessão no Gercom expirou! Favor clique <a href=\"http://www.jmdec.com.br\" target=\"_blank\">aqui</a> para entrar novamente no sistema</font></td>";
			//$pagina  = "http://www.gercomweb.com.br/";
		    //header("Location:$pagina"; window-target: top);
		echo "</tr>";
		echo "</table>"; 
      	exit;
	  }
?>
