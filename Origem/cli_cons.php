<?php
	include("conexao2.inc.php");
	include("funcoes2.inc.php");
	//include("dglogin1.php");

	$arquivo = "cli_cons.php";
	//include("auditoria.php");
	
	
	
 $sql = "SELECT * FROM acessos WHERE ace_login = '$acelogin';";
 $query = mysql_query($sql)or die("Erro na Consulta!");
 if(mysql_num_rows($query) > 0){
   $linha = mysql_fetch_object($query);
   if ($linha->ace_14 == 'S'){

?>
<html>
	<head>
		<link rel="stylesheet" href="est_big.css" type="text/css">
		<title>:: gercom.NET - Consulta de Clientes por Nome ou CGC ::</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<script language="JavaScript">
			function submit_action(caminho) {
				//postando para a verificacao;
				document.formclicons.action = caminho;
				document.formclicons.method = 'post';
				document.formclicons.submit();
			}
		</script>
	</head>

	<body background="../imagens/fundomain.jpg" topmargin="5" leftmargin="5" rightmargin="5" bottommargin="5">
		<?
		//include("menu_java.php");
	?>
		<table width="100%" cellpadding="2" align="center" cellspacing="2" border="1" bordercolor="#CCCCCC">
			<tr>
				<td width="100%" align="center" bgcolor="#004000"><font size='3' color="#FFFFFF">Consulta de Clientes Por Nome/CGC</font></td>
			</tr>
			<tr>
			<td>
			<form action="cli_cons.php" method="post" name="formclicons">
			<table cellpadding="4" cellspacing="4" width="100%">
			<tr>
			<td width="200">
			Consultar por:
			<select name="lstTipo">
			<?
			if ($lstTipo == 'nomefan') {
				echo "<option value='nomefan' selected>Nome Fantasia</option>";
				echo "<option value='nomeraz'>Nome Raz�o</option>";
				echo "<option value='cgc'>CGC</option>";
			} elseif ($lstTipo == 'cgc') {
				echo "<option value='nomefan' selected>Nome Fantasia</option>";
				echo "<option value='nomeraz' selected>Nome Raz�o</option>";
				echo "<option value='cgc' selected>CGC</option>";
			} else {
				echo "<option value='nomefan'>Nome Fantasia</option>";
				echo "<option value='nomeraz' selected>Nome Raz�o</option>";
				echo "<option value='cgc'>CGC</option>";
			}
			?>
			</select>
			</td>
			<td width="400" align="left">
			<input type="text" name="edtNomeCgc" value="<?=$edtNomeCgc ?>" style="width:350">
			</td>
			<td align="right">
			<input type="button" value="APENAS CLIENTES COM CR�DITO" onClick="submit_action('cli_cons.php?credito=s')" style="border:solid 1; width:300; background-color:#FFFFC0">
			</td>
			<td align="right">
			<input type="submit" value="Consultar" style="border:solid 1; width:200; height:30; background-color:#030; color:#FFF" >
			</td>
			</tr>
			</table>
			</form>
			</td>
			</tr>
			<?
			if ($REQUEST_METHOD == "POST") {
				echo "<tr>";
				echo "<td width='100%'>";
				echo "<table cellpadding='2' cellspacing='0' align='center' width='100%' border='1'>";
				echo "<tr>";
				echo "<td align='left' bgcolor='#004000'><b><font size='2' color='#FFFFFF'>Loja que cadastrou</font></b></td>";
				echo "<td align='left' bgcolor='#004000'><b><font size='2' color='#FFFFFF'>CGC/CPF</font></b></td>";
				echo "<td align='left' bgcolor='#004000'><b><font size='2' color='#FFFFFF'>Nome</font></b></td>";
				if ($credito == "s") {
					echo "<td align='right' bgcolor='#004000'><b><font size='2' color='#FFFFFF'>Cr�dito</font></b></td>";
				}
				echo "<td align='center' bgcolor='#004000'><b><font size='2' color='#FFFFFF'>Detalhes</font></b></td>";
				echo "<td align='center' bgcolor='#004000'><b><font size='2' color='#FFFFFF'>Alterar</font></b></td>";
				echo "<td align='center' bgcolor='#004000'><b><font size='2' color='#FFFFFF'>Excluir</font></b></td>";
				echo "</tr>";
	
					if ($credito == "s") {
					$sql_cons = "SELECT DISTINCT * FROM clientes, loja WHERE cli_loja = lj_cod AND cli_loja = '$ljcod' AND cli_credito > 0;";
					} else {
					if ($lstTipo == "cgc") {
						$sql_cons = "SELECT DISTINCT * FROM clientes, loja WHERE cli_loja = lj_cod AND cli_cgccpf = '$edtNomeCgc';";
					} elseif ($lstTipo == "nomefan") {
						$sql_cons = "SELECT DISTINCT * FROM clientes, loja WHERE cli_loja = lj_cod AND cli_fantasia LIKE '%" . $edtNomeCgc . "%';";
					} else {
						$sql_cons = "SELECT DISTINCT * FROM clientes, loja WHERE cli_loja = lj_cod AND cli_razao LIKE '%" . $edtNomeCgc . "%';";
					}
					}
					
				$query_cons = mysql_query($sql_cons, $conexao) or die("Erro na Consulta!!!");
				while ($linha_cons = mysql_fetch_object($query_cons)) {
					echo "<tr>";
					echo "<td bgcolor='#FFFFFF' align='left'>" . $linha_cons -> lj_fantasia . "</td>";
					echo "<td bgcolor='#FFFFFF' align='left'>" . $linha_cons -> cli_cgccpf . "</td>";
					if ($lstTipo == "nomefan") {
						echo "<td bgcolor='#FFFFFF' align='left'>" . $linha_cons -> cli_fantasia . "</td>";
					} else {
						echo "<td bgcolor='#FFFFFF' align='left'>" . $linha_cons -> cli_razao . "</td>";
					}
					if ($credito == "s") {
						echo "<td bgcolor='#FFFFFF' align='right'>" . number_format($linha_cons -> cli_credito, '2', ',', '.') . "</td>";
					}

					echo "<td bgcolor='#FFFFFF' align='center'><a href='cli_det.php?cgc=" . $linha_cons -> cli_cgccpf . "&flag=detalhe'> <img src='../imagens/detalhe.bmp' border='no'></a></td>";
					echo "<td bgcolor='#FFFFFF' align='center'><a href='cli_alt.php?cgc=" . $linha_cons -> cli_cgccpf . "&flag=alterar'> <img src='../imagens/editar.gif' border='no'></a></td>";
					
					if ($linha -> ace_nivel == 'STANDARD') {
						echo "<td bgcolor='#FFFFFF' align='center'><a href='#'> <img src='../imagens/apagar.gif' border='no'></a></td>";
					} else {
						echo "<td bgcolor='#FFFFFF' align='center'><a href='cli_excluir.php?cgc=" . $linha_cons -> cli_cgccpf . "&flag=excluir'> <img src='../imagens/apagar.gif' border='no'></a></td>";
					}
					echo "</tr>";
				}
			}
			?>
			</table>
			</td>
			</tr>
		</table>

	</body>
</html>
<?
}else{
//include("naoautorizado.php");
}
}
?>
<?
include ("rodape.php");
?>