<?php
	include("conexao2.inc.php");
	include("funcoes2.inc.php");
	include("dglogin1.php");
	
	$arquivo = "pedido_senhager.php";
	include("auditoria.php");

	$data  = date("d/m/Y");
    $hora = date("H:i:s");

 $sql = "SELECT * FROM acessos WHERE ace_login = '$acelogin';";
 $query = mysql_query($sql,$conexao)or die("Erro na Consulta 1!");
 if(mysql_num_rows($query) > 0){
   $linha = mysql_fetch_object($query);
   if ($linha->ace_21 == 'S'){

		 $sql_loja   = "SELECT lj_liberapc FROM loja WHERE lj_cod = '$ljcod';";
		 $query_loja = mysql_query($sql_loja,$conexao)or die("Erro na Consulta 2!");
		 if(mysql_num_rows($query_loja) > 0){
		   $linha_loja = mysql_fetch_object($query_loja);
		   if ($linha_loja->lj_liberapc == "S"){

			//verifica os itens
			if ($cons != "s"){
				$sql_mv = "SELECT pm_num, pm_emp, pm_loja, pm_prod, pm_cor, pm_escala, pm_progrupo, pm_qtd, pm_es,
                                  pm_pc, pm_estoqueok, pm_entregue
						   FROM pedmov WHERE pm_num ='$edtNumPed' AND pm_lojaloc = '$ljcod';";
				//echo $sql_mv; exit;		   
				$query_mv = mysql_query($sql_mv,$conexao)or die("Erro na Consulta dos Itens do Pedido");

				if(mysql_num_rows($query_mv) > 0) {
				    $gerar = "NAO"; $condicao = true; $eumatroca = ''; 
					while($linha_mv = mysql_fetch_object($query_mv)){
						
					   if ($linha_mv->pm_es == "E"){
						  $eumatroca = 'S'; 
						  $gerar = 'SIM';
					   }
						
						
						// checar se pedido de fabrica atende o filtro
						$sql_fpve   = "SELECT fpve_escala1 FROM filtropvescalas
										WHERE fpve_prod = '".$linha_mv->pm_prod."'"; //echo $sql_fpve;
						$query_fpve = mysql_query($sql_fpve) or die ("Erro na Inclusao do Pedido de Compra");
						if(mysql_num_rows($query_fpve) > 0){ 
						   while($linha_fpve = mysql_fetch_object($query_fpve)) {
							if ($condicao == true) {
							   if ($linha_fpve->fpve_escala1 == $linha_mv->pm_escala ){
								 $filtropvescalas = 'n';
								 $condicao = true;				 
							   }else{
								 $filtropvescalas = 's';
								 $condicao = true;				 							   
								 $msg = "Existem pedidos de fábricas com as escalas em desacordo!" ;
								 $msg2 = "Produto com as escalas em desacordo!" ;							   
							   }
							}							 
						   }
						}else{
					       $filtropvescalas = 'n';					
						}
					
							//buscando o saldo do produdo
							$sql_busca   = "SELECT sal_estreal FROM saldos
											 WHERE sal_cod      = '".$linha_mv->pm_prod."'
											   AND sal_cor      = '".$linha_mv->pm_cor."'
											   AND sal_escala   = '".$linha_mv->pm_escala."'
											   AND sal_progrupo = '".$linha_mv->pm_progrupo."'
											   AND sal_loja     = '".$linha_mv->pm_loja."';";
							//echo $sql_busca; exit;
							$query_busca = mysql_query($sql_busca)or die("Erro na Consulta do Estoque!");
	
							if(mysql_num_rows($query_busca) <= 0){ //se nao encontrar o saldo ele inclui
								if ($linha_mv->pm_es == 'S'){
									$gerar = "SIM";
								}	
							}else{
							  $linha_busca = mysql_fetch_object($query_busca);						
								if ($linha_mv->pm_es == 'S'){
									if ($linha_busca->sal_estreal < $linha_mv->pm_qtd) {
									  $gerar = "SIM";
									}
								}
							}
						   if ($linha_mv->pm_pc == "S"){
							 $gerar = "SIM";								     
						   }
					}
				}
			}elseif ($cons == "s"){
				$sql_mv = "SELECT pm_num, pm_emp, pm_loja, pm_prod, pm_cor, pm_escala, pm_progrupo, pm_qtd, pm_es, pm_ljtroca,
                                  pm_pc, pm_estoqueok, pm_entregue
						   FROM pedmov WHERE pm_num ='$edtNumPed' AND pm_lojaloc = '$ljcod' AND pm_es = 'S';";
				$query_mv = mysql_query($sql_mv,$conexao)or die("Erro na Consulta dos Itens do Pedido");

				if(mysql_num_rows($query_mv) > 0) {
					while($linha_mv = mysql_fetch_object($query_mv)){
						//buscando o saldo do produdo
						$sql_busca   = "SELECT sal_estreal FROM saldos
									     WHERE sal_cod      = '".$linha_mv->pm_prod."'
									   	   AND sal_cor      = '".$linha_mv->pm_cor."'
									   	   AND sal_escala   = '".$linha_mv->pm_escala."'
									   	   AND sal_progrupo = '".$linha_mv->pm_progrupo."'
									   	   AND sal_loja     = '".$linha_mv->pm_loja."';";
						$query_busca = mysql_query($sql_busca)or die("Erro na Consulta do Estoque!");

                        if(mysql_num_rows($query_busca) <= 0){ //se nao encontrar o saldo ele inclui
							 $sql_ger = "SELECT ace_senha, ace_nivel FROM acessos WHERE ace_login = '$gerlogin' 
							 														AND ace_senha = '$gersenha' AND (ace_nivel = 'MASTER' OR ace_nivel = 'GERENTE') ;";
							 //echo $sql_ger;
							 $query_ger = mysql_query($sql_ger, $conexao) or die ("Erro na Consulta 3!");
							 if (mysql_num_rows($query_ger) > 0){
								 $linha_ger = mysql_fetch_object($query_ger);
								 if ($linha_ger->ace_nivel != 'STANDARD'){
									 $sql_senha = "INSERT INTO pcgerentes (pcg_gerente, pcg_pedido, pcg_data, pcg_hora, pcg_loja, 
																		   pcg_prod, pcg_escala, pcg_progrupo, pcg_cor, pcg_qtd)
																   VALUES ('$gerlogin', '$edtNumPed', '".muda_data_en($data)."', 
																		   '$hora', '$ljcod',
																		   '".$linha_mv->pm_prod."',
																		   '".$linha_mv->pm_escala."',
																		   '".$linha_mv->pm_progrupo."',
																		   '".$linha_mv->pm_cor."',
																		   '".$linha_mv->pm_qtd."');";
									 $query_senha = mysql_query($sql_senha,$conexao)or die("Erro 2!");
									 $gerar = "NAO";
								 }else{
									 $msg   = "Usuário / Senha não autorizado para esta operação!";
								   if ($linha_mv->pm_es == 'S'){
									 $gerar = "SIM"; //echo $gerar.'adsfadf';					 
								   }	 
								 }
							 }else{
								 $msg   = "Usuário / Senha não autorizado para esta operação!";
						      if ($linha_mv->pm_es == 'S'){								 
								 $gerar = "SIM";
							  }	 
							 } 
						}else{
						  $linha_busca = mysql_fetch_object($query_busca);						
							if ($linha_mv->pm_es == 'S'){
								if ($linha_busca->sal_estreal < $linha_mv->pm_qtd) {
								    $saldo = $linha_mv->pm_qtd - $linha_busca->sal_estreal;
									 $sql_ger = "SELECT ace_senha, ace_nivel FROM acessos WHERE ace_login = '$gerlogin' 
																							AND ace_senha = '$gersenha' AND (ace_nivel = 'MASTER' OR ace_nivel = 'GERENTE') ;";
									 //echo $sql_ger;
									 $query_ger = mysql_query($sql_ger, $conexao) or die ("Erro na Consulta 4!");
									 if (mysql_num_rows($query_ger) > 0){
										 $linha_ger = mysql_fetch_object($query_ger);
										 if ($linha_ger->ace_nivel != 'STANDARD'){
											 $sql_senha = "INSERT INTO pcgerentes (pcg_gerente, pcg_pedido, pcg_data, 
											 									   pcg_hora, pcg_loja, pcg_prod, 
																				   pcg_escala, pcg_progrupo, pcg_cor, pcg_qtd)
																		   VALUES ('$gerlogin', '$edtNumPed',
																		   		   '".muda_data_en($data)."', 
																				   '$hora', '$ljcod',
																				   '".$linha_mv->pm_prod."',
																				   '".$linha_mv->pm_escala."',
																				   '".$linha_mv->pm_progrupo."',
																				   '".$linha_mv->pm_cor."',
																				   '".$saldo."');";
											 $query_senha = mysql_query($sql_senha,$conexao)or die("Erro 3!");
											 $gerar = "NAO";
										 }else{
											 $msg   = "Usuário / Senha não autorizado para esta operação!";
								   	       if ($linha_mv->pm_es == 'S'){											 
											 $gerar = "SIM"; //echo $gerar.'adsfadf';					 
										   }	 
										 }
									 }else{
										 $msg   = "Usuário / Senha não autorizado para esta operação!";
								      if ($linha_mv->pm_es == 'S'){										 
										 $gerar = "SIM";
									  }	 
									 } //if (mysql_num_rows($query_ger) > 0){
								} //if ($linha_busca->sal_estreal < $linha_mv->pm_qtd) {
							} //if ($linha_mv->pm_es == 'S'){
						} //if(mysql_num_rows($query_busca) <= 0){
					} //while($linha_mv = mysql_fetch_object($query_mv)){
				} //if(mysql_num_rows($query_mv) > 0) {
					

			// o codigo vai ficar aqui
					
			} //}elseif ($cons == "s"){
		   }else{
		       $gerar = "NAO";		   
		   } //if ($linha_loja->lj_liberapc == "S"){
		 } //if(mysql_num_rows($query_loja) > 0){
		 //echo $gerar.'asdfasdfsadf';
?>
<html>
<head>
	<link rel="stylesheet" href="est_big.css" type="text/css">
	<title>:: Gercom.NET - Autorização para Pedido de Compra ::</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<script src="funcoes.js"></script>
<script language="javascript">
		function submit_action(caminho){
			//postando para a verificacao;
			document.formCons.action= caminho; 
			document.formCons.method= 'post'; 
			document.formCons.submit();			
		}
</script>

</head>

<body topmargin="0" background="../imagens/fundomain.jpg" bottommargin="0" leftmargin="0" rightmargin="0">
<? if ($menu != off) {
     include("menu_java.php");
   }
?>
<form name="formCons" action="pedido_senhager.php?gerar=NAO&edtNumPed=<?=$edtNumPed?>&menu=off" method="post">
<? if ($gerar == "SIM"){ ?>
<table width="100%" border="0" cellspacing="2" cellpadding="2" bordercolorlight="#000000"> 
    <tr>
        <td align="center" width="100%" bgcolor="#004000"><strong><font size="3" color="#FFFFFF">Autorização para Pedido de Compra e Troca</font></strong></td>
    </tr>
<?php
	if(isset($msg)){
		echo "<tr>";
			  echo "<td bgcolor='#FF0000' align='center' width='100%'><font size='5' color='#FFFFFF'>".$msg."</font></td>";
		echo "</tr>";
	}
?>
    <tr>
      <td>		
		<table bordercolorlight='#000099' width='100%' border='1' bgcolor='#008000' align='left' cellpadding='2' cellspacing='1' width='100%'> 
				<?
				//verifica os itens
				$sql_cli = "SELECT DISTINCT cli_razao
						   FROM  pedcad, clientes 
						   WHERE ped_cliente=cli_cgccpf AND ped_num='$edtNumPed' AND ped_loja='$ljcod'";
				$query_cli = mysql_query($sql_cli,$conexao)or die("Erro na Consulta dos Itens do Pedido");

				if(mysql_num_rows($query_cli) > 0) {
				  $linha_cli = mysql_fetch_object($query_cli);
				}
				?>

			<tr><br>
				<td width='20%' bgcolor='#008000' align='left'><font size='2' color="#FFFFFF">Pedido de Venda:</font></td>
				<td width='80%' bgcolor='#008000' align='left'><font size='2' color="#FFFFFF">Cliente:</font></td>
			</tr>
			<tr>
				<td width='20%' bgcolor='#CEFFCE' align='left'><font style="font-size:18px; color:#030"><?=$edtNumPed?></font></td>
				<td width='80%' bgcolor='#CEFFCE' align='left'><font style="font-size:18px; color:#030"><?=$linha_cli->cli_razao?></font></td>
			</tr>
		</table>
       </td>
	</tr> 

				<?
				//verifica os itens
				$sql_mv2 = "SELECT DISTINCT pm_num, pm_emp, pm_loja, pm_prod, pm_cor, pm_escala, pm_progrupo, pm_qtd, pm_es,
                                           pm_pc, pm_estoqueok, pm_entregue, pro_descabv, pro_foralinha, esc_descabv, prog_descabv, cor_descabv, pro_promocional
						   FROM  pedmov, produtos, escala, progrupo, cores, loja
						   WHERE pm_prod=pro_cod AND pm_escala=esc_cod AND pm_progrupo=prog_cod AND pm_cor=cor_cod AND
						         pm_num ='$edtNumPed' AND pm_lojaloc = '$ljcod' and pm_es = 'S';";
				//echo $sql_mv2 ;exit;
				$query_mv2 = mysql_query($sql_mv2,$conexao) or die ("Erro na Consulta dos Itens do Pedido");

				if(mysql_num_rows($query_mv2) > 0) {
				?>

	<tr>
		<td width="100%"><br><br>
			<table bordercolorlight="#000099" width="100%" border="1" bgcolor="#FFFFFF" cellspacing="1" cellpadding="2">
				<tr> 
                  <td colspan="6" bgcolor="#800000"><strong><font size='2' color="#FFFFFF"><u>Produtos do Pedido para Compra</u></font></strong>
				  </td>
				</tr>
						<tr>
							<td bgcolor="#804040"><font color="#FFFFFF">Cód.</font></td>
							<td bgcolor="#804040"><font color="#FFFFFF">Descrição</font></td>		
							<td bgcolor="#804040"><font color="#FFFFFF">Escala 1</font></td>
							<td bgcolor="#804040"><font color="#FFFFFF">Grupo</font></td>
							<td bgcolor="#804040"><font color="#FFFFFF">Escala 2</font></td>
							<td align="center" bgcolor="#804040"><font color="#FFFFFF">Qtde</font></td>							
						</tr>
				<?				
					$eumatroca = ''; 				
					while($linha_mv2 = mysql_fetch_object($query_mv2)){
					         $gerar2 = "";
                             static $flagcolor = false;
                             if ($flagcolor = !$flagcolor){
                               $color = "#FFFFFF";
                             }else{
                               $color = "#FFFFFF";
                             }
							 if ($linha_mv2->pro_foralinha == "S"){
                               $color = "#FF7D7D";							
							 }
							 

						//buscando o saldo do produdo
						$sql_busca2   = "SELECT sal_estreal FROM saldos
									    WHERE sal_cod      = '".$linha_mv2->pm_prod."'
									   	  AND sal_cor      = '".$linha_mv2->pm_cor."'
									   	  AND sal_escala   = '".$linha_mv2->pm_escala."'
									   	  AND sal_progrupo = '".$linha_mv2->pm_progrupo."'
									   	  AND sal_loja     = '".$linha_mv2->pm_loja."';";
						$query_busca2 = mysql_query($sql_busca2)or die("Erro na Consulta do Estoque!");

                        if(mysql_num_rows($query_busca2) <= 0){ //se nao encontrar o saldo ele inclui
						  $gerar2 = "SIM";
						}else{
						  $linha_busca2 = mysql_fetch_object($query_busca2);						
							if ($linha_mv2->pm_es == 'S'){
								if ($linha_busca2->sal_estreal < $linha_mv2->pm_qtd) {
								  $gerar2 = "SIM";
								}
							}
						}
						   if ($linha_mv2->pm_pc == "S"){
							 $gerar2 = "SIM";								     
						   }
						
						  if ($gerar2 == "SIM"){
							$promocional = '';
							if ($linha_mv2->pro_promocional == "S"){
							 $promocional = '<font color="#FF0000"><b> ( PROMOCIONAL )</b></font>';
						     $sql_laca   = "SELECT esc_desc, esc_cod FROM escala WHERE esc_cod = '".$linha_mv2->pm_escala."';"; //echo $sql_laca;
							 $query_laca = mysql_query($sql_laca)or die("Erro na Laca!");
	  						 if(mysql_num_rows($query_laca) > 0){ //se nao for laca nao deixa passar
							  $linha_laca = mysql_fetch_object($query_laca);
							  $tipodeescala = substr($linha_laca->esc_desc,0,8); //echo $tipodeescala.'tipo de escala';
							  if ( ( $tipodeescala == "COR LACA" ) ) { // TESTAR ESSA NOVA OPÇÃO
							  	$promocionallaca = '';
							  }else{
							  	$promocionallaca = 'S';	
							  }

							 }
							}
							//echo $promocionallaca.'AAAAAAAAAAAAAAA';

							if ($linha_mv2->pro_foralinha == "S"){
							 $foralinha = '<font color="#FF0000"><b> ( FORA DE LINHA )</b></font>';
						     $tempelomenosum = 'S';
							 
							}else{
							 $foralinha = '';
						     $tempelomenosum = '';							 
							}
						  
				?>
							<tr>
								<td bgcolor="<?=$color?>">[ <?=$linha_mv2->pm_prod?> ]</td>
								<td bgcolor="<?=$color?>"><?=$linha_mv2->pro_descabv?><?=$foralinha?><?=$promocional?></td>		
								<td bgcolor="<?=$color?>"><?=$linha_mv2->esc_descabv?></td>
								<td bgcolor="<?=$color?>"><?=$linha_mv2->prog_descabv?></td>
								<td bgcolor="<?=$color?>"><?=$linha_mv2->cor_descabv?></td>
								<td align="center" bgcolor="<?=$color?>"><?=$linha_mv2->pm_qtd?></td>							
                       
							</tr>
					<?
					      }
						}
					}
					?>
			</table>
		</td>
	</tr>

	 <? if ($tempelomenosum == "S"){ ?>
     <tr>
      <td><br><br>
		<table border='1' bordercolor="#FF0000" width="100%" bgcolor='#FFFF99' cellpadding="0" cellspacing="0">     
           <tr bordercolor="#FFFF99">
            <td bgcolor='#FF0000' align="left"><font style="font-size:18px; color:#FFF"><center>Neste PV existe algum PRODUTO EM DESACORDO conforme destaque acima. Não é possível gerar Pedido de Fábrica de um Produto Fora de Linha. Volte ao PV e corrija-o!</center></font></td>
           </tr>
		</table> 
      </td>    
     </tr>
	 <? }else if ($promocionallaca == "S"){ ?>
     <tr>
      <td><br><br>
		<table border='1' bordercolor="#FF0000" width="100%" bgcolor='#FFFF99' cellpadding="0" cellspacing="0">     
           <tr bordercolor="#FFFF99">
            <td bgcolor='#FF0000' align="left"><font style="font-size:18px; color:#FFF"><center>Neste PV existe algum PRODUTO EM DESACORDO conforme destaque acima. Não é possível gerar Pedido de Fábrica de um Produto Promocional sem ser na Laca. Volte ao PV e corrija-o!</center></font></td>
           </tr>
		</table>     
      </td>    
     </tr>
	 <? }else{ ?>     
	 <? if ($eumatroca == "S"){ ?>
     <tr>
      <td><br><br>
		<table bordercolorlight="#000099" border='1' width="100%" bgcolor='#FFFFFF' cellpadding="2" cellspacing="1">     
				<tr> 
                  <td colspan="7" bgcolor="#0099FF"><strong><font size='2' color="#FFFFFF"><u>Produtos Trocados no Pedido</u></font></strong>
				  </td>
				</tr>
				<?
				//verifica os itens
				$sql_mv3 = "SELECT DISTINCT pm_num, pm_emp, pm_loja, pm_prod, pm_cor, pm_escala, pm_progrupo, pm_qtd, pm_es, pm_id,
                                           pm_pc, pm_estoqueok, pm_entregue, pro_descabv, pro_foralinha, esc_descabv, prog_descabv, cor_descabv, pro_promocional
						   FROM  pedmov, produtos, escala, progrupo, cores, loja
						   WHERE pm_prod=pro_cod AND pm_escala=esc_cod AND pm_progrupo=prog_cod AND pm_cor=cor_cod AND
						         pm_num ='$edtNumPed' AND pm_lojaloc = '$ljcod' AND pm_es = 'E'
						ORDER BY pm_id ;";
				//echo $sql_mv2 ;exit;
				$query_mv3 = mysql_query($sql_mv3,$conexao) or die ("Erro na Consulta dos Itens do Pedido Troca");

				if(mysql_num_rows($query_mv3) > 0) {
				?>
						<tr>
							<td bgcolor="#00CCFF"><font color="#000000">Cód.</font></td>
							<td bgcolor="#00CCFF"><font color="#000000">Descrição</font></td>		
							<td bgcolor="#00CCFF"><font color="#000000">Escala 1</font></td>
							<td bgcolor="#00CCFF"><font color="#000000">Grupo</font></td>
							<td bgcolor="#00CCFF"><font color="#000000">Escala 2</font></td>
							<td align="center" bgcolor="#00CCFF"><font color="#000000">Qtde</font></td>							
							<td align="center" bgcolor="#00CCFF"><font color="#000000">Produto Retorna para</font></td>							                            
						</tr>
				<?				
					while($linha_mv3 = mysql_fetch_object($query_mv3)){
				?>
							<tr>
								<td bgcolor="<?=$color?>">[ <?=$linha_mv3->pm_prod?> ]</td>
								<td bgcolor="<?=$color?>"><?=$linha_mv3->pro_descabv?></td>		
								<td bgcolor="<?=$color?>"><?=$linha_mv3->esc_descabv?></td>
								<td bgcolor="<?=$color?>"><?=$linha_mv3->prog_descabv?></td>
								<td bgcolor="<?=$color?>"><?=$linha_mv3->cor_descabv?></td>
								<td align="center" bgcolor="<?=$color?>"><?=$linha_mv3->pm_qtd?></td>
								<td align="center" bgcolor="<?=$color?>">
                                 <select name="<?=$linha_mv3->pm_id?>" style="width:180; background-color:#FFFFFF;">
                                  <?
                                        $sql_uf   = "SELECT lj_estado FROM loja where lj_cod = '$ljcod';";
                                        $query_uf = mysql_query($sql_uf,$conexao);
                                        if (mysql_num_rows($query_uf) > 0){
                                            $linha_uf = mysql_fetch_object($query_uf);
                                        }
                                        $sql_loja = "SELECT * FROM loja where lj_estado = '".$linha_uf->lj_estado."' order by lj_fantasia;";
                                        $query_loja = mysql_query($sql_loja,$conexao);
            
                                        if (mysql_num_rows($query_loja) > 0) {
                                            while($linha_loja = mysql_fetch_object($query_loja)){
                                              if(!isset($lstLojaRetorno)){
                                                if($linha_loja->lj_cod == $ljcod){
                                                    echo "<option value='".$linha_loja->lj_cod."' selected>".$linha_loja->lj_fantasia."</option>";
                                                }else{
                                                    echo "<option value='".$linha_loja->lj_cod."'>".$linha_loja->lj_fantasia."</option>";
                                                }
                                              }else{
                                                if($linha_loja->lj_cod == $lstLojaRetorno){
                                                    echo "<option value='".$linha_loja->lj_cod."' selected>".$linha_loja->lj_fantasia."</option>";
                                                }else{
                                                    echo "<option value='".$linha_loja->lj_cod."'>".$linha_loja->lj_fantasia."</option>";
                                                }
                                              }
                                            }
                                        }
                                        ?>
                                </select>
                                </td>							                                         							
							</tr>
				<?				
					}
				}
				?>

		</table>     
      </td>    
     </tr>
	 <? } ?>

	 <tr>
      <td><br><br>
		<table border='1' bordercolorlight="#FF0000" width="100%" bgcolor='#FFFF99' cellpadding="2" cellspacing="1">     
				 <tr bordercolor="#FFFF99">
				  <td bgcolor='#FFFF99' align="left"><font size="2"><u>Digite o login e senha do gerente:</u></font></td>
				 </tr>
				 <tr bordercolor="#FFFF99">
				  <td align="center">
					  <font size="2">LOGIN:</font>
				      <input type="text" name="gerlogin" size="15" maxlength="35" style="background-color: #FFFFFF; font-weight:bold;">
					  <font size="2">SENHA:</font>
				      <input type="password" name="gersenha" size="15" maxlength="35" style="background-color: #FFFFFF; font-weight:bold;">
				  </td>
				</tr>
		</table>
      </td>    
     </tr>
     <tr>
      <td><br><br>
		<table width="100%" cellpadding="2" cellspacing="1">     
				<tr>
				  <td align="center"> 

					<? if ($filtropvescalas != "s") { ?>                  
					  <input style="text-align:center; color:#FFFFFF; font-size:14px; border:solid 1; font-weight:bold; border-color:#000; width:150; height:40; background-color:#FF0000;" type="button" onClick="submit_action('pedido_senhager.php?cons=s&edtNumPed=<?=$edtNumPed?>&menu=off&gerar=NAO');" value="Confirmar">
					<? } ?>                                      
					  <input style="text-align:center; color:#FFFFFF; font-size:14px; border:solid 1; font-weight:bold; border-color:#000; width:150; height:40; background-color:#FF0000;" type="button" onClick="javascript:history.back();" value="Voltar">
                  </td>
				</tr>
       
       </table>
      </td>
     </tr>
	 <? } // fim do if ($linha_mv2->pro_foralinha == "S"){ ?>
<? }elseif ($gerar == "NAO"){ ?>
	<script language="javascript">
		submit_action('pedido_fecha.php?edtNumPed=<?=$edtNumPed?>&edtPedido=<?=$edtPedido?>&lstLoja=<?=$lstLoja?>&menu=off');
	</script>
<? } ?>
</form>
</body>
</html>

<?
  }else{
	include("naoautorizado.php");
  }
 }
?>
<?
  include("rodape.php");
?>