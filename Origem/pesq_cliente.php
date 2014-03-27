<?
	include("conexao2.inc.php");
	include("funcoes2.inc.php");
	//include("dglogin1.php");
	
	$arquivo = "pesq_cliente.php";
	//include("auditoria.php");
?>

<html>
<head>
	<link rel="stylesheet" href="est_big.css" type="text/css">
	<title>:: Gercom.NET - Pesquisa do Clientes ::</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script src="funcoes.js"></script>	
<script>
<!--
function enviar(prod, descr, ref, esc, cor, prog, loja){
			if(prod != ""){
				var formcod  =  "<?=$formcod_ck?>";
				var formdesc =  "<?=$formdesc_ck?>";
				var formref  =  "<?=$formref_ck?>";
				var formesc  =  "<?=$formesc_ck?>";
				var formcor  =  "<?=$formcor_ck?>";
				var formprog =  "<?=$formprog_ck?>";
				var formloja =  "<?=$formloja_ck?>";
				eval("window.opener.document."+formcod+".value  = "+"'"+prod +"'"+";");
				eval("window.opener.document."+formdesc+".value = "+"'"+descr+"'"+";");
				eval("window.opener.document."+formref+".value  = "+"'"+ref+"'"+";");
				eval("window.opener.document."+formesc+".value  = "+"'"+esc+"'"+";");
				eval("window.opener.document."+formcor+".value  = "+"'"+cor+"'"+";");
				eval("window.opener.document."+formprog+".value  = "+"'"+prog+"'"+";");
				eval("window.opener.document."+formloja+".value  = "+"'"+loja+"'"+";");
				eval("window.opener.document.formpedido.edtqtd.focus();");
				window.close();
			}
		}

		function abrir(caminho){
		 window.opener.location = caminho;
         window.close();
		 window.opener.focus();
		}

		function submit_action(caminho){
		 //postando para a verificacao;
		 document.formPesq.action= caminho; 
		 document.formPesq.method= 'post'; 
		 document.formPesq.submit();			
		}
		
		function foco(obj){
			if(obj){
				obj.focus();
			}
		}

function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
//-->
</script>
</head>

<style>
	.t1 {font : bold 12px verdana; color : white;}
	.t2 {font : normal 11px tahoma;}
</style>

<body bgcolor="#F4FEEB" onLoad="foco(document.formPesq.edtBusca);MM_preloadImages('../aracaju_p.jpg')">
<? if ($popup == "ok") {
     //include("menu_java.php");
   }
?>
<form action="pesq_cliente.php?tela=<?=$tela?>&campo=<?=campo?>&cons=s&lstCli=<?=$lstCli?>&menuoff=<?=$menuoff?>&orc_num=<?=$orc_num?>" method="post" name="formPesq">

<table cellpadding="2" cellspacing="2" align="center" width="100%" border="1" bordercolor="#CCCCCC" background=
"fundomain.png">

<tr>
  <td width="100%" bgcolor="#000000" align="center"><font color="#FFFFFF">Pesquisa de Clientes</font></td>
</tr>

<tr> 
	<td height="48" align="center"> 
		<table cellpadding="4" cellspacing="4" width="100%">
		<tr> 
			<td width="70%">Consultar por: 
            	<select name="lstTipo" style="background-color: #FF0000; color:#FFFFFF;">
                <? 
					if ($lstTipo == 'estado'){
						echo "<option value='nome'>Nome</option>";
						echo "<option value='cpfcnpj'>CPF/CNPJ</option>";
						echo "<option value='end'>Endereço</option>";
						echo "<option value='bairro'>Bairro</option>";
						echo "<option value='cidade'>Cidade</option>";
						echo "<option value='estado'selected>Estado</option>";
					}elseif($lstTipo == 'cpfcnpj'){
						echo "<option value='nome'>Nome</option>";
						echo "<option value='cpfcnpj' selected>CPF/CNPJ</option>";
						echo "<option value='end'>Endereço</option>";
						echo "<option value='bairro'>Bairro</option>";
						echo "<option value='cidade'>Cidade</option>";
						echo "<option value='estado'>Estado</option>";						
					}elseif($lstTipo == 'end'){
						echo "<option value='nome'>Nome</option>";
						echo "<option value='cpfcnpj'>CPF/CNPJ</option>";
						echo "<option value='end' selected>Endereço</option>";
						echo "<option value='bairro'>Bairro</option>";
						echo "<option value='cidade'>Cidade</option>";
						echo "<option value='estado'>Estado</option>";					
					}elseif($lstTipo == 'bairro'){
						echo "<option value='nome'>Nome</option>";
						echo "<option value='cpfcnpj'>CPF/CNPJ</option>";
						echo "<option value='end'>Endereço</option>";
						echo "<option value='bairro'selected>Bairro</option>";
						echo "<option value='cidade'>Cidade</option>";
						echo "<option value='estado'>Estado</option>";						
					}elseif($lstTipo == 'cidade'){
						echo "<option value='nome'>Nome</option>";
						echo "<option value='cpfcnpj'>CPF/CNPJ</option>";
						echo "<option value='end'>Endereço</option>";
						echo "<option value='bairro'>Bairro</option>";
						echo "<option value='cidade' selected>Cidade</option>";
						echo "<option value='estado'>Estado</option>";					
					}else{
						echo "<option value='nome' selected>Nome</option>";
						echo "<option value='cpfcnpj'>CPF/CNPJ</option>";
						echo "<option value='end'>Endereço</option>";
						echo "<option value='bairro'>Bairro</option>";
						echo "<option value='cidade'>Cidade</option>";
						echo "<option value='estado'>Estado</option>";							
					}
				?>
				</select>
				: 
				
                <input style="background-color:#0099FF; text-transform:uppercase; font-family:Tahoma; width:250;" 
                type="text" name="edtBusca" value="<?=$edtBusca?>">
			</td>
			<td width="15%">
            <input type="submit" style="border:solid 1; width:150; background-color:#FFFFC0;" value="Consultar">
            </td>
			<td width="15%">
            
            <input type="button" style="border:solid 1; font-size:14px; width:180; background-color:#004000; color:
            #FFFFFF;" value="Cadastrar Novo Cliente" onClick="javascript:popup(' cli_cad.php?menuoff=ok ',850,550,' 
            center ',' center ',POP_tot);">
            </td>
		</tr>
        </table>
	</td>
</tr>
<tr>
	<td align="center" width="100%"> 
		<table width="100%" border="0" cellpadding="2">
            <?
				if($REQUEST_METHOD == "POST"){
					if($cons == "s"){
					//selecionando o tipo de consulta
						if ($lstTipo == "nome") {
							$sql_cons = "SELECT * FROM clientes
											WHERE cli_razao LIKE '%$edtBusca%'  order by cli_razao;";
						}elseif($lstTipo == "cpfcnpj") {
							$sql_cons = "SELECT * FROM clientes
                                            WHERE cli_cgccpf LIKE '%$edtBusca%' order by cli_razao;";
						}elseif($lstTipo == "end"){
							$sql_cons = "SELECT * FROM clientes
										 WHERE cli_end LIKE '%$edtBusca%' order by cli_razao;";
						}elseif($lstTipo == "bairro"){
							$sql_cons = "SELECT * FROM clientes
										 WHERE cli_bairro LIKE '%$edtBusca%' order by cli_razao;";
						}elseif($lstTipo == "cidade"){
							$sql_cons = "SELECT * FROM clientes
										 WHERE cli_cidade LIKE '%$edtBusca%' order by cli_razao;";
						}elseif($lstTipo == "estado"){
							$sql_cons = "SELECT * FROM clientes
										 WHERE cli_estado LIKE '%$edtBusca%' order by cli_razao;";
						}   
						$query_cons = mysql_query($sql_cons, $conexao)or die("Erro na Consulta!!!");
			?>
						  <tr>
						   <td>
						    <table width="100%" border="1" bordercolor="#004000" bgcolor="#004000" cellpadding="0" 
                            cellspacing="1">
							  <tr>
							    <td bgcolor="#004000" width="80%"><font color="#FFFFFF" style="font-size:14px">Nome 
                                do Cliente:</font></td>
							    <td bgcolor="#004000" width="20%"><font color="#FFFFFF" style="font-size:14px">
                                CPF/CPNJ:</font></td>																
							    <td align="center" bgcolor="#004000" width="20%"><font color="#FFFFFF" style="
                                font-size:14px">Escolha</font></td>																								
							  </tr>
			<?
							while($linha_cons = mysql_fetch_object($query_cons)){
							   static $flagcolor = false;
							   if ($flagcolor = !$flagcolor){
								 $color  = "#CCFFCC";
								 $color2 = "#336600";								 
								 $color3 = "#FFFFFF";								 								 
							   }else{
								 $color  = "#FFFFCC";
								 $color2 = "#FFCC33";								 
								 $color3 = "#000000";								 								 
							   }
							
			?>
							  <tr>
							    <td bordercolor="#FFFFFF" bgcolor="<?=$color?>"><font style="font-size:10px" color=
                                "#880000">
												<?=$linha_cons->cli_razao?></font></td>
							    <td bordercolor="#FFFFFF" bgcolor="<?=$color?>"><font style="font-size:10px; 
                                font-style:italic;" color="#880000">
												<?=$linha_cons->cli_cgccpf?></font></td>																
                                <td width="12%" align="center" bordercolor="#FFFFFF" bgcolor="<?=$color?>">
			<?	if ($tela == "pedidocad") { ?>
									<input style="width:90; border:solid 1; font-family:Tahoma; font-size:12px; 
                                    background-color:<?=$color2?>; color:<?=$color3?>; height:16;" type="button" 
                                    value="ESCOLHER" onClick=
                                    "javascript:abrir('pedido_cad.php?campo=formpedido.edtqtd&lstCli=<?=$linha_cons
									->cli_cgccpf?>&edtSit=<?=$edtSit?>menuoff=<?=$menuoff?>&orc_num=<?=$orc_num?>
                                    ');">
			<?	}elseif ($tela == "pedidoalt") { ?>
									<input style="width:90; border:solid 1; font-family:Tahoma; font-size:12px; 
                                    background-color:<?=$color2?>; color:<?=$color3?>; height:16;" type="button" 
                                    value="ESCOLHER" onClick=
                                    "javascript:abrir('pedido_alt.php?campo=formpedido.edtqtd&lstCli=<?=$linha_cons
									->cli_cgccpf?>&edtSit=<?=$edtSit?>menuoff=<?=$menuoff?>&orc_num=<?=$orc_num?>
                                    ');">
			<?	} ?>												
								</td>
							  </tr>

<? if (($lstTipo == "end") || ($lstTipo == "bairro") || ($lstTipo == "cidade") || ($lstTipo == "estado")) { ?>
							  <tr>
							    <td colspan="3" bordercolor="#FFFFFF" bgcolor="<?=$color?>">
								<font style="font-size:9px; font-style:italic;" color="#004000">
								 ===== Endereço: <?=$linha_cons->cli_end?> - <?=$linha_cons->cli_bairro?> - 
								 <?=$linha_cons->cli_cidade?>/<?=$linha_cons->cli_estado?></font></td>
							  </tr>
			<? } ?>			  
			<?				
							}
			?>
						    </table>
			               </td>
						  </tr>
			<?											
					}
				}
			?>
		</table>		
	</td>
</tr> 
<tr> 
	<td width="100%" align="center"> 
		<input type="button" value="Fechar" onClick="javascript:window.close();" style="border:solid 1"> 
	</td>
</tr>
</table>
</form>
</body>
</html>
<?
  include("rodape.php");
?>