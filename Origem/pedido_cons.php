<?php
/**
ALTERAÇÕES SÓ NAS IMAGENS
E NA CRIAÇÃO DE UMA VARIAVEL
**/

	include("conexao2.inc.php");
	include("funcoes2.inc.php");
	//include("dglogin1.php");

	$arquivo = "pedido_cons.php";
	//include("auditoria.php");
	
	//so para tests
	$acelogin = "jalen";
	
    $sql_pc = "SELECT * FROM acessos WHERE ace_login = '$acelogin';";
    $query_pc = mysql_query($sql_pc,$conexao)or die("Erro na Consulta!");
    if(mysql_num_rows($query_pc) > 0){
      $linha_pc = mysql_fetch_object($query_pc);
    }

?>
<html>
<head>
	<link rel="stylesheet" href="est_big.css" type="text/css">
	<title>:: Gercom.NET - Consulta de Pedidos de Venda por Situação e Período ::</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<script src="funcoes.js"></script>
</head>
<!-- -->
<body background="fundomain.png" topmargin="0" leftmargin="0" rightmargin="0" bottommargin="0">
<?
//  if ($menuoff != "ok") {
    //include("menu_java.php");
//  } 
?>
<? if($button == "no"){ ?>
<table width="100%" border="1" cellspacing="2" cellpadding="2" bordercolor="#CCCCCC">
<? } else { ?>
<table width="100%" border="1" cellspacing="2" cellpadding="2" bordercolor="#CCCCCC">
<? } ?>
    <tr>
        <td align="center" width="100%" bgcolor="#004000"><font color="#FFFFFF">Consulta de Pedidos de Venda por Situação e Período</font></td>
    </tr>
    <tr>
        <td width="100%">
			<form name="formPedCons" action="pedido_cons.php?campo=formPedCons.lstSituacao&menuoff=<?=$menuoff?>" method="post">
			<table width="100%" border="0" cellspacing="0" cellpadding="2">
				<? if($button == "no"){ ?>
				<tr>
					<? if($lstSituacao == "A"){?>
						<td style="width:100"><font size="3">Situação: ABERTO</font></td>
					<? }elseif($lstSituacao == "E"){?>
						<td style="width:100" width="60%"><font size="3">Situação: ENTREGUE</font></td>
					<? }elseif(($lstSituacao == "C") && ($linha_pc->ace_nivel != 'STANDARD')){ ?>
						<td style="width:100"><font size="3">Situação: CANCELADO</font></td>
					<? }elseif($lstSituacao == "D"){ ?>
						<td style="width:100"><font size="3">Situação: PENDÊNCIA</font></td>
					<? }elseif($lstSituacao == "H"){ ?>
						<td style="width:100"><font size="3">Situação: CARTA CRÉDITO</font></td>
					<? }elseif($lstSituacao == "F"){ ?>
						<td style="width:100"><font size="3">Situação: FECHADO</font></td>
					<? }elseif(($lstSituacao == "P") && ($linha_pc->ace_nivel != 'STANDARD')){ ?>
						<td style="width:100"><font size="3">Situação: PAGO</font></td>
					<? }elseif(($lstSituacao == "T") && ($linha_pc->ace_nivel != 'STANDARD')){ ?>
						<td style="width:100"><font size="3">Situação: TODOS</font></td>
					<? } ?>
					<td width="1%">&nbsp;</td>
				</tr>
				<? }else{ ?>	
				<tr>
					<td>Situação:
						<select style="width:140" name="lstSituacao">
							<? if($lstSituacao == "C"){?>
									<option value="A">ABERTO</option>
									<option value="C" selected>CANCELADO</option>
									<option value="D">PENDÊNCIA</option>
									<option value="H">CARTA CRÉDITO</option>
									<option value="F">FECHADO</option>
                   				<? if ($linha_pc->ace_nivel != 'STANDARD'){ ?>
									<option value="P">PAGO</option>
									<option value="T">TODOS</option>                                    
                   				<? } ?>
							<? }elseif($lstSituacao == "D"){?>
									<option value="A">ABERTO</option>
									<option value="C">CANCELADO</option>
									<option value="D" selected>PENDÊNCIA</option>
									<option value="H">CARTA CRÉDITO</option>
									<option value="F">FECHADO</option>
                   				<? if ($linha_pc->ace_nivel != 'STANDARD'){ ?>
									<option value="P">PAGO</option>
									<option value="T">TODOS</option>                                                                        
                   				<? } ?>
							<? }elseif($lstSituacao == "H"){?>
									<option value="A">ABERTO</option>
									<option value="C">CANCELADO</option>
									<option value="D">PENDÊNCIA</option>
									<option value="H" selected>CARTA CRÉDITO</option>
									<option value="F">FECHADO</option>
                   				<? if ($linha_pc->ace_nivel != 'STANDARD'){ ?>
									<option value="P">PAGO</option>
									<option value="T">TODOS</option>                                                                        
                   				<? } ?>
							<? }elseif($lstSituacao == "F"){?>
									<option value="A">ABERTO</option>
									<option value="C">CANCELADO</option>
									<option value="D">PENDÊNCIA</option>
									<option value="H">CARTA CRÉDITO</option>
									<option value="F" selected>FECHADO</option>
                   				<? if ($linha_pc->ace_nivel != 'STANDARD'){ ?>
									<option value="P">PAGO</option>
									<option value="T">TODOS</option>                                                                        
                   				<? } ?>
							<? }elseif($lstSituacao == "P"){ ?>
                   				<? if ($linha_pc->ace_nivel != 'STANDARD'){ ?>                            
									<option value="A">ABERTO</option>
									<option value="C">CANCELADO</option>
									<option value="D">PENDÊNCIA</option>
									<option value="H">CARTA CRÉDITO</option>
									<option value="F">FECHADO</option>
									<option value="P" selected>PAGO</option>
									<option value="T">TODOS</option>                                                                        
                   				<? } ?>
							<? }elseif($lstSituacao == "T"){ ?>
                   				<? if ($linha_pc->ace_nivel != 'STANDARD'){ ?>                            
									<option value="A">ABERTO</option>
									<option value="C">CANCELADO</option>
									<option value="D">PENDÊNCIA</option>
									<option value="H">CARTA CRÉDITO</option>
									<option value="F">FECHADO</option>
									<option value="P">PAGO</option>
									<option value="T" selected>TODOS</option>                                                                        
                   				<? } ?>
							<? }else{?>
									<option value="A" selected>ABERTO</option>
									<option value="C">CANCELADO</option>
									<option value="D">PENDÊNCIA</option>
									<option value="H">CARTA CRÉDITO</option>
									<option value="F">FECHADO</option>
                   				<? if ($linha_pc->ace_nivel != 'STANDARD'){ ?>
									<option value="P">PAGO</option>
									<option value="T">TODOS</option>                                                                        
                   				<? } ?>
							<? } ?>
						</select>
						<? //data final como o dia de hoje
							if(!isset($edtInicio)){
								$edtInicio = date("d/m/Y");
							    $edtFim = date("d/m/Y");
							    
							}
						?>
						Emissão:
				      <input type="text" name="edtInicio" style="width:110" value="<?=$edtInicio?>">
						à <input type="text" name="edtFim" style="width:110" value="<?=$edtFim?>">
                        
                        </td>
                  
                   <td rowspan="2" align="center" valign="middle">
					<input type="submit" value="Consultar" style="border:solid 1; width:200; height:60; background-color:#030; color:#FFF" >
                   </td>
                </tr>
                <tr>
                        <td>
                         <table bordercolorlight="#000000" bgcolor="#EAEAEA" border="1" cellpadding="2" cellspacing="2">
                           <tr>
                            <td><font style="font-size:14px"><u>OPCIONAIS:</u></font>&nbsp;
                                PV: <input style="background-color:#FF9; width:130" name="edtBusca" type="text" 
                                value="<?=$edtBusca?>">&nbsp;  
        
                                Vendedor:
                                <select style="background-color:#FF9; width:130" name="lstVendedor">
                                    <?
										
										//
										//so para teste
										$ljcod = "01";
										//
										//
										
                                        $sql_ven = "SELECT * FROM vendedor WHERE ven_loja = '$ljcod' ;";
                                        $query_ven = mysql_query($sql_ven,$conexao);
                                        if (mysql_num_rows($query_ven) > 0){
                                            echo "<option value='TODOS' selected>TODOS</option>";									
                                            while($linha_ven = mysql_fetch_object($query_ven)){
                                                if($linha_ven->ven_cod == $lstVendedor){
                                                    echo "<option value='".$linha_ven->ven_cod."' selected>".$linha_ven->ven_nome."</option>";
                                                }else{
                                                    echo "<option value='".$linha_ven->ven_cod."'>".$linha_ven->ven_nome."</option>";
                                                }
                                            }
                                        }
                                    ?>	
                                </select>&nbsp;
                                CPF/CNPJ: <input style="background-color:#FF9; width:130" name="edtCPF" type="text" value="<?=$edtCPF?>">                                
                            </td>
                            <td>
						   <?  if ($chkTroca == "sim") { ?>                            
                            <input type="checkbox" name="chkTroca" value="sim" checked>Marcar Trocas
						   <? }else{ ?>                            
                            <input type="checkbox" name="chkTroca" value="sim">Marcar Trocas
						   <? }  ?>                                                        
                            </td>
                           </tr>
                         </table>                           
					    </td>					
                </tr>
				<? } ?>
			</table>
			</form>
		</td>
    </tr>
	<?php
		if($REQUEST_METHOD == "POST"){
			echo "<tr>";
				echo "<td>"; ?>
					<table bgcolor="#FFFFFF" width="100%" cellspacing="0" cellpadding="2" border="1">
						<?
						if($edtInicio != "" && $edtFim != ""){
							$edtInicio = muda_data_en($edtInicio);
							$edtFim = muda_data_en($edtFim);

							if ($edtCPF == ""){
								$cpf = '';
							}else{
								$cpf = " AND ped_cliente LIKE '%".$edtCPF."%' ";
							}

							if ($edtBusca == ""){
								$pv = '';
							}else{
								$pv = " AND ped_num LIKE '%".$edtBusca."%' ";
							}

							if ($lstVendedor == "TODOS"){
								$vendedor = '';
							}else{
								$vendedor = " AND ped_vend = '".$lstVendedor."' ";
							}

							if ($lstSituacao == "T"){
								$situacao = '';
							}else{
								$situacao = " ped_situacao = '$lstSituacao' AND  ";
							}


							$sql = "SELECT DISTINCT ped_num, ped_cliente,ped_obs, ped_motivotroca, cli_cgccpf, cli_razao, 
													ped_situacao, ped_status, ped_loja, ped_obscanc, ped_nf, ped_desconto,
													ped_valliq, ped_emissao, ped_logincancel, ped_dtcancel, ped_motivocanc
											   FROM pedcad, clientes
											  WHERE ".$situacao."
											  	    ped_emissao BETWEEN '$edtInicio' AND '$edtFim' AND 
													cli_cgccpf = ped_cliente AND ped_loja = '$ljcod' ".$cpf." ".$pv." ".$vendedor." 
									ORDER BY ped_num;";
							$query = mysql_query($sql,$conexao); 
							if(mysql_num_rows($query) > 0){
								$total = 0;
							?>
                                  <tr>
                                      <td align="left" bgcolor="#003300"><font color="#FFFFFF">Pedido</font></td>
                                      <td align="left" bgcolor="#003300"><font color="#FFFFFF">Cliente</font></td>							
                                      <!-- <td align="center" bgcolor="#CCCCCC">Valor</td> -->
                                      <td align="center" bgcolor="#003300"><font color="#FFFFFF">Emissão</font></td>
                                      <? if($button != "no"){ ?>
                                          <td align="center" bgcolor="#003300"><font color="#FFFFFF">Alterar</font></td>
                                          <td align="center" bgcolor="#003300"><font color="#FFFFFF">NF</font></td> 
                                          <td align="center" bgcolor="#003300"><font color="#FFFFFF">Retenção</font></td>                             
                                          <td align="center" bgcolor="#003300"><font color="#FFFFFF">Status</font></td>
										  <td align="right" bgcolor="#003300"><font color="#FFFFFF">Valor</font></td>            
										<? if ($lstSituacao == "T"){ ?>
                                          <td align="center" bgcolor="#003300"><font color="#FFFFFF">Situação</font></td>                                          
										<? } ?>                                          
                                          <td align="center" bgcolor="#003300"><font color="#FFFFFF">Impressão</font></td>
                                      <? } ?>
                                  </tr>
							
							<?	$totalgeral = 0; 
								while($linha = mysql_fetch_object($query)){ 
								 static $flagcolor = false;
								 if ($flagcolor = !$flagcolor){
								   $color = "#FFFFC0";
								 }else{
								   $color = "#FFFFFF";
								 }
								 if ($linha->ped_nf == "S"){
								   $color = "#FFA4A4";									 
							     }
								
								 //checando se tem troca
								 $pvtroca = '';
								 if ($chkTroca == "sim") {
								  $sqltroca = "SELECT pm_num FROM pedmov WHERE pm_num = '".$linha->ped_num."' AND pm_es = 'E';";
								  $querytroca = mysql_query($sqltroca,$conexao); 
								  if(mysql_num_rows($querytroca) > 0){
									$pvtroca = "<font style='font-color:#0000FF; font-weight:bold'>| TROCA</font>";
								  }
								 } 													

								?>
									<tr>
										<td bgcolor='<?=$color?>' align="left"><font class="NORMAL"><?=$linha->ped_num?> <?=$pvtroca?> </font></td>
										<td bgcolor='<?=$color?>' align="left"><font class="NORMAL"><?=$linha->cli_razao?> </font></td>										
										<td bgcolor='<?=$color?>' align="center"><font class="NORMAL"><?=muda_data_pt($linha->ped_emissao)?></font></td>
										<? if($button != "no"){ ?>
										<? if($lstSituacao == "A"){ ?>
											<td bgcolor='<?=$color?>' align="center"><font class="NORMAL"><a href="pedido_alt.php?flag=alterar&edtNumPed=<?=$linha->ped_num?>&ljcod=<?=$linha->ped_loja?>&lstCli2=<?=$lstCli?>&lstCli=<?=$lstCli?>&lstVend=<?=$lstVend?>&lstTV=<?=$lstTV?>"><img src="../imagens/editar.gif" border="no" alt="Alterar"></a></font></td>
										<? }else{ ?>
											<td bgcolor='<?=$color?>' align="center"><img src="../imagens/editar.gif" border="no" alt="Não Disponível"></td>								
										<? } ?>
                                        
									<? if($linha->ped_situacao == "P"){ ?>							                                        
										<td bgcolor='<?=$color?>' align="center"><font class="NORMAL"><a href="#" onClick="popup('pedido_rel.php?flag=detalhe&planfinan=S&edtNumPed=<?=$linha->ped_num?>&naoimprimebotoes=ok&voltarok=ok&lojaplanilha=<?=$linha->ped_loja?>&nf=s',920,650,'center','center',POP_tot)"><img src="../imagens/printcomfiscal.bmp" border="no"></a></font></td>
									<? }else{ ?>							
										<td bgcolor='<?=$color?>' align="center"><img src="../imagens/printcomfiscal.bmp" border="no"></td>
									<? } ?>		

									<? if($linha->ped_situacao == "P"){ 	
										  $sql_cxmov = "SELECT cxm_coef FROM cxmov WHERE cxm_pedido = '".$linha->ped_num."' AND 
										  					   cxm_loja = '".$linha->ped_loja."';";// echo $sql_cxmov;
										  $query_cxmov = mysql_query($sql_cxmov,$conexao);
										  if(mysql_num_rows($query_cxmov) > 0){
											  $linha_cxmov = mysql_fetch_object($query_cxmov);
											  $coef = $linha_cxmov->cxm_coef;
										  }
									  ?>
                                    						
                                        <td align="center" bgcolor='<?=$color?>'><font class="NORMAL"><?=$coef?></font></td>                             
                                    <? }else{ ?>							                            
                                        <td align="center" bgcolor='<?=$color?>'><font class="NORMAL">-</font></td>
                                    <? } ?>							                                                                
                                    					                                    
                                        <? if ($linha->ped_status == ''){ ?>
										   <td bgcolor='<?=$color?>' align="center"><font class="NORMAL">----</font></td>
										<? }else{ ?>
										   <td bgcolor='<?=$color?>' align="center"><font class="NORMAL"><?=$linha->ped_status?></font></td>
										<? } ?>
                                        
									  <?
                                      $sqlcxmov = "SELECT cxm_valor FROM cxmov WHERE cxm_pedido = '".$linha->ped_num."';";
                                      $querycxmov = mysql_query($sqlcxmov,$conexao);
                                      if(mysql_num_rows($querycxmov) > 0){
										  $linhacxmov = mysql_fetch_object($querycxmov);
										  $valorliquidopv = $linhacxmov->cxm_valor;
									  }else{
										  $valorliquidopv = 0;										  
									  }
                                      ?>                                        
                                        <? $valortotal = $valorliquidopv - $linha->ped_desconto; ?>
										<td bgcolor='<?=$color?>' align="right"><font class="NORMAL"><?=number_format($valortotal,'2',',','.')?></font></td>                                        <? $totalgeral = $totalgeral + $valortotal; ?>
                                        
								  <? if ($lstSituacao == "T"){ ?>
									<? if ($linha->ped_situacao == "A"){ $situacaopv = 'ABERTO'; } ?>    <? if ($linha->ped_situacao == "H"){ $situacaopv = 'CARTA CRÉDITO'; } ?>
                                    <? if ($linha->ped_situacao == "F"){ $situacaopv = 'FECHADO'; } ?>   <? if ($linha->ped_situacao == "P"){ $situacaopv = 'PAGO'; } ?>
                                    <? if ($linha->ped_situacao == "D"){ $situacaopv = 'PENDÊNCIA'; } ?> <? if ($linha->ped_situacao == "C"){ $situacaopv = 'CANCELADO'; } ?>										   										<td bgcolor='<?=$color?>' align="center"><font class="NORMAL"><?=$situacaopv?></font></td>                                        
                                  <? } ?>
                                        
										<td bgcolor='<?=$color?>' align="center"><font class="NORMAL"><a href="#" onClick="popup('pedido_rel.php?flag=detalhe&planfinan=S&edtNumPed=<?=$linha->ped_num?>&naoimprimebotoes=ok&voltarok=ok&lojaplanilha=<?=$linha->ped_loja?>',920,650,'center','center',POP_tot)"><img src="../imagens/print.bmp" border="no"></a>
<a href="#" onClick="popup('pedido_rel_novo.php?flag=detalhe&planfinan=S&edtNumPed=<?=$linha->ped_num?>&naoimprimebotoes=ok&voltarok=ok&lojaplanilha=<?=$linha->ped_loja?>',920,650,'center','center',POP_tot)"><img src="../imagens/pdf.jpg" width="20" height="23" border="no"></a>                                        </font></td>
										<? $total = $total + $linha->ped_valliq ;
										}?>
										<!--&loja=<?=$lstLoja?>&prod=<?=$linha->pm_prod?>&cor=<?=$linha->pm_cor?>&escala=<?=$linha->pm_escala?>-->										
									</tr>
						  	              
									<? if ($lstSituacao == "C"){ 
                                        $sqlcanc = "SELECT tc_desc FROM tipocanc WHERE tc_cod = '".$linha->ped_motivocanc."';";
                                                //echo $sqlcanc;
                                        $querycanc = mysql_query($sqlcanc,$conexao);
                                        if(mysql_num_rows($querycanc) > 0){
                                            $linha_canc = mysql_fetch_object($querycanc);
                                        }
                                    ?>    
                                        <tr>                    
                                          <td bgcolor='<?=$color?>' align="left" colspan="4"><font class="NORMAL"><b>Motivo: </b><?=$linha_canc->tc_desc?></font></td>
                                          <td bgcolor='<?=$color?>' align="left"><font class="NORMAL"><b>Data: </b><?=muda_data_pt($linha->ped_dtcancel)?></font></td>
                                          <td bgcolor='<?=$color?>' align="left"><font class="NORMAL"><b>Cancelado por: </b><?=$linha->ped_logincancel?></font></td>
                                        </tr>                    
                                        <tr>                    
                                          <td bgcolor='<?=$color?>' align="left" colspan="6"><font class="NORMAL"><b>OBS: </b><?=$linha->ped_obscanc?></font></td>                            </tr>                                                                                                          
								  <?  
                                     } 
          
                                     if ($linha->ped_motivotroca != ""){ 	?>    
                                      <tr>                    
                                        <td bgcolor='<?=$color?>' align="left" colspan="9"><font class="NORMAL"><b>Motivo Troca: </b><?=$linha->ped_motivotroca?></font></td>                            </tr>                                                                                                          
                                  <?  
                                     } 
                                    
                                   }  ?>
                                      <tr>                    
                                        <td align="right" colspan="7"><b>Total: </b></td>
                                        <td align="right"><font style="font-size:14px;"><?=number_format($totalgeral,'2',',','.')?></font></td>
                                        <td align="center">.</td>                              
                                      </tr>                    
                                   
<?
			                 } //fim do while($linha = mysql_fetch_object($query)){ 
			               } //fim do if(mysql_num_rows($query) > 0){ 	            
						?>
					 </table>
		           </td>
	            </tr>

<?
     } //fim do if($REQUEST_METHOD == "POST"){  ?>	
</table>
</body>
</html>
<?
  include("rodape.php");
?>