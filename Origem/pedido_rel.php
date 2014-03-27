<?
	include("conexao2.inc.php");
	include("funcoes2.inc.php");
	include("dglogin1.php");
	
	$arquivo = "pedido_rel.php";
	include("auditoria.php");

	$data = date("d/m/Y");
    $hora = date("H:i:s");
	$precoliberado = 'S';
		
      $lojalocada = $ljcod;
      if ($planfinan == "S"){
        $lojalocada = $lojaplanilha;
      }

		$sql_regiao   = "SELECT DISTINCT reg_coef, lj_liberapreco from loja, regioes
						 WHERE lj_regiao = reg_num AND lj_cod = '$ljcod';";
		$query_regiao = mysql_query($sql_regiao,$conexao);
		if(mysql_num_rows($query_regiao) > 0){
		  $linha_regiao = mysql_fetch_object($query_regiao);
		}

      $sql_fechado   = "select ped_situacao from pedcad
                             WHERE ped_num = '$edtNumPed' AND ped_loja = '$lojalocada'";
							 //echo $sql_fechado;
      $query_fechado = mysql_query($sql_fechado,$conexao)or die(mysql_error());
      if(mysql_num_rows($query_fechado) > 0){
     	$linha_fechado = mysql_fetch_object($query_fechado);
      }

		//dados do pedido
		$sql_ped = "SELECT DISTINCT * FROM pedcad, planopag 
									 WHERE ped_num = '$edtNumPed' AND ped_loja = '$lojalocada' AND ped_tipove = pp_cod;";
		//echo $sql_ped;						  
        $query_ped    = mysql_query($sql_ped)or die("Erro na consulta do Pedido 1!");
		$linha_ped    = mysql_fetch_object($query_ped);
		$acrescimo    = $linha_ped->ped_acrescimo;
		$desconto     = $linha_ped->ped_desconto;
		$edtObs       = $linha_ped->ped_obs;
		$edtObsProd   = $linha_ped->ped_obsprod;		
        if ($linha_ped->ped_valorpend != "0"){
		  $edtValorPend = $linha_ped->ped_valorpend;
		  $edtObsPend   = $linha_ped->ped_obspend;
        }
		$tpvenda = $linha_ped->pp_desc;
		if(mysql_num_rows($query_ped) > 0){
			//calculando saidas
			$sql_saida = "SELECT sum(pm_valtot) as valprod FROM pedmov
						WHERE pm_num = '$edtNumPed' AND pm_es = 'S'
                        AND pm_lojaloc = '$lojalocada';";
            //echo $sql_saida;
            $query_saida = mysql_query($sql_saida,$conexao)or die("Erro na Soma das Saidas: ".mysql_error());
			$linha_saida = mysql_fetch_object($query_saida);
			$valprod_saida = $linha_saida->valprod;
			//calculando entradas
			$sql_entra = "SELECT sum(pm_valtot) as valprod FROM pedmov
						WHERE pm_num = '$edtNumPed' AND pm_es = 'E'
                        AND pm_lojaloc = '$lojalocada';";
			//echo $sql_entra;			
			$query_entra = mysql_query($sql_entra,$conexao)or die("Erro na Soma das Entradas: ".mysql_error());
			$linha_entra = mysql_fetch_object($query_entra);
			$valprod_entra = $linha_entra->valprod;
			//calculando totais
			$valprod = $valprod_saida + $valprod_entra;
			$valliq = ($valprod + $acrescimo) - $desconto;
			if ($valliq < 0){ $cred = $valliq*(-1); }
			//dados da loja
			$sql_loja = "SELECT * FROM loja WHERE lj_cod = '$linha_ped->ped_loja';";
			//echo $sql_loja;
			$query_loja = mysql_query($sql_loja)or die("Erro na consulta da Loja!");
			$linha_loja = mysql_fetch_object($query_loja);
			//dados do cliente
			$sql_cli = "SELECT DISTINCT 
			   				   cli_razao, cli_cgccpf, cli_inscrg, cli_end, cli_bairro, cli_cidade, cli_estado, cli_pontoref, lower(cli_email) as cli_email, 
							   cli_profissao, clip_desc, cli_fone, cli_fax, cli_celular1, cli_celular2, cli_operadora1, cli_operadora2, cli_cep 
					      FROM clientes, clientes_prof WHERE cli_profissaocod = clip_cod AND cli_cgccpf = '$linha_ped->ped_cliente';";
			//echo $sql_cli;
			$query_cli = mysql_query($sql_cli)or die("Erro na consulta do Cliente!");
			$linha_cli = mysql_fetch_object($query_cli);
			//dados do vendedor
			$sql_ven = "SELECT ven_cod, ven_nome FROM vendedor WHERE ven_cod = '$linha_ped->ped_vend';";
			//echo $sql_ven;
			$query_ven = mysql_query($sql_ven)or die("Erro na consulta do Vendedor!");
			$linha_ven = mysql_fetch_object($query_ven);
		}

	if ($nf == 's'){
		$sql_nfped = "SELECT ped_nf FROM pedcad WHERE ped_num = '$edtNumPed' and ped_loja = '$lojalocada';";
		$query_nfped = mysql_query($sql_nfped,$conexao);
		if(mysql_num_rows($query_nfped) > 0){
		 $linha_nfped = mysql_fetch_object($query_nfped);
 		 if ($linha_nfped->ped_nf == 'S'){
			$notafiscal = 'N';
	     }else{
			$notafiscal = 'S';			 
		 }
		}

		$sql_nf   = "UPDATE pedcad SET ped_nf = '$notafiscal' WHERE ped_num = '$edtNumPed' and ped_loja = '$lojalocada';";
		$query_nf = mysql_query($sql_nf,$conexao) or die (mysql_error());
    }

	if ($conferido == 'ok'){
        // pedido conferido
		$sql_conf = "UPDATE cxmov SET cxm_conferido = 'S', cxm_dtconferido = '".muda_data_en($data)."', cxm_hrconferido = '$hora', cxm_loginconferido = '$acelogin'
                           WHERE cxm_pedido = '$edtNumPed' and cxm_loja = '$lojalocada';";
		$query_conf = mysql_query($sql_conf,$conexao)or die(mysql_error());
        $msg_fecha .= "<br><font size=\"5\" color=\"#FF0000\">PV Conferido e Confirmado pelo Gerente!</font></br>";		
    }

	if ($flag == 'fechamento'){
		//fecha o pedido e altera a situacao = F
       // $edtValorPend= number_format($edtValorPend,'2',',','.');
        $edtValorPend = valor_mysql($edtValorPend);
		$sql_obs = "UPDATE pedcad SET ped_obs = '$edtObs', ped_obsprod = '$edtObsProd', ped_obspend = '".$edtObsPend."',
                           ped_status = 'PENDÊNCIA', ped_valorpend = '".$edtValorPend."'
                           WHERE ped_num = '$edtNumPed' and ped_loja = '$lojalocada';";
		$query_obs = mysql_query($sql_obs,$conexao)or die(mysql_error());
    }

	if ($flag == 'finalizar'){
      if ($gravaobs == "ok"){

       if ($credito == "ok"){
        if (($credito == "ok") && ($valliq < 0)){
		  $sql_cred = "UPDATE clientes SET cli_credito = cli_credito + $cred
                           WHERE cli_cgccpf = '$cliente';";
		  $query_cred = mysql_query($sql_cred,$conexao)or die(mysql_error());

          $sql_pedst = "UPDATE pedcad SET ped_obs = '$edtObs', ped_obsprod = '$edtObsProd', ped_obspend = '$edtObsPend',
                           ped_situacao = 'H', ped_dtpag = '".muda_data_en($data)."', ped_hora = '$hora'
                           WHERE ped_num = '$edtNumPed' and ped_loja = '$lojalocada';";
		  $query_pedst = mysql_query($sql_pedst,$conexao)or die(mysql_error());
          $msg_fecha .= "<br><font size=\"3\" color=\"#FF0000\">Crédito dado com sucesso para o cliente!</font></br>";
         }else{
          $msg_fecha .= "<br><font size=\"3\" color=\"#FF0000\">Só é permitido dar crédito para um cliente, se o pedido estiver com valor líquido negativo!</font></br>";
         }
       }

        if ($pendencia == "ok"){
         if (($edtValorPend == "0,00") || ($edtValorPend == "") || ($edtValorPend == "0")){
            ?>
			<script>
			 window.alert("Digite o Motivo e o Valor da Pendência")
			</script>
            <?
            $bot_pend = '';
         }else{
	        $sql_fech = "select ped_situacao from pedcad
                              WHERE ped_num = '$edtNumPed' AND ped_situacao = 'A' and ped_loja = '$lojalocada'";
            $query_fech = mysql_query($sql_fech,$conexao)or die(mysql_error());
	        //echo $sql_fech;
	       if(mysql_num_rows($query_fech) > 0){
 	       echo "<table align\"center\"><tr><td></td></tr><tr><td></td></tr><tr><td align=\"center\">";
	       echo "<font size= \"2\" color=\"#FF0000\">Este pedido não está fechado! Feche-o primeiro antes de colocá-lo como pendência!</font>";
 	       echo "</td></tr><tr><td align=\"center\">";
           echo "<input type=\"button\" value=\"Voltar\" onClick=\"javascript: history.back();\">";
 	       echo "</td></tr></table>";
 	       exit;
	      } //if(mysql_num_rows($query_fech) > 0){
          $msg_fecha .= "<br><font size=\"3\" color=\"#FF0000\">Pendência realizada com sucesso!</font></br>";
          $data = muda_data_en($data);
          $pend = " ,ped_situacao = 'D' , ped_dtpendencia = '$data' ";
         }
		//grava a obs do pedido
        $edtValorPend = valor_mysql($edtValorPend);
		$sql_obs = "UPDATE pedcad SET ped_obs = '$edtObs', ped_obsprod = '$edtObsProd', ped_obspend = '".$edtObsPend."',
                           ped_valorpend = '".$edtValorPend."' ".$pend."
                           WHERE ped_num = '$edtNumPed' and ped_loja = '$lojalocada';";
		$query_obs = mysql_query($sql_obs,$conexao)or die(mysql_error());
        }
        //echo $credito.'cred'; echo $valliq.'valliq';
     }
	}elseif($flag == 'detalhe'){
		//dados do pedido
		$sql_ped = "SELECT DISTINCT * FROM pedcad, planopag
									 WHERE ped_num = '$edtNumPed' AND ped_loja = '$lojalocada' AND ped_tipove = pp_cod ;";
		//echo $sql_ped;
		$query_ped = mysql_query($sql_ped)or die("Erro na consulta do Pedido 2!");	
		$linha_ped = mysql_fetch_object($query_ped);
		$acrescimo = $linha_ped->ped_acrescimo;
		$desconto =  $linha_ped->ped_desconto;
		$tpvenda = $linha_ped->pp_desc;
		$edtObs =  $linha_ped->ped_obs;
		$edtObsProd =  $linha_ped->ped_obsprod;		
		$edtObsPend =  $linha_ped->ped_obspend;
		$edtValorPend =  $linha_ped->ped_valorpend;
		if(mysql_num_rows($query_ped) > 0){
           	//calculando saidas
			$sql_saida = "SELECT sum(pm_valtot) as valprod FROM pedmov
						WHERE pm_num = '$edtNumPed' AND pm_es = 'S'
                        AND pm_lojaloc = '$lojalocada';";
			$query_saida = mysql_query($sql_saida,$conexao)or die("Erro na Soma das Saidas: ".mysql_error());
			$linha_saida = mysql_fetch_object($query_saida);
			$valprod_saida = $linha_saida->valprod;
			//calculando entradas
			$sql_entra = "SELECT sum(pm_valtot) as valprod FROM pedmov
						WHERE pm_num = '$edtNumPed' AND pm_es = 'E'
                        AND pm_lojaloc = '$lojalocada';";
			//echo $sql_entra;
			$query_entra = mysql_query($sql_entra,$conexao)or die("Erro na Soma das Entradas: ".mysql_error());
			$linha_entra = mysql_fetch_object($query_entra);
			$valprod_entra = $linha_entra->valprod;
	        //calculando totais		
			$valprod = $valprod_saida + $valprod_entra;
			$valliq = ($valprod + $acrescimo) - $desconto;
			//dados da loja
			$sql_loja = "SELECT * FROM loja WHERE lj_cod = '$linha_ped->ped_loja';";
			//echo $sql_loja;
			$query_loja = mysql_query($sql_loja)or die("Erro na consulta da Loja!");
			$linha_loja = mysql_fetch_object($query_loja);
			//dados do cliente
			$sql_cli = "SELECT DISTINCT 
			   				   cli_razao, cli_cgccpf, cli_inscrg, cli_end, cli_bairro, cli_cidade, cli_estado, cli_pontoref, lower(cli_email) as cli_email, 
							   cli_profissao, clip_desc, cli_fone, cli_fax, cli_celular1, cli_celular2, cli_operadora1, cli_operadora2, cli_cep 
					      FROM clientes, clientes_prof WHERE cli_profissaocod = clip_cod AND cli_cgccpf = '$linha_ped->ped_cliente';";
			$query_cli = mysql_query($sql_cli)or die("Erro na consulta do Cliente!");		
			$linha_cli = mysql_fetch_object($query_cli);
			//dados do vendedor
			$sql_ven = "SELECT ven_cod, ven_nome FROM vendedor WHERE ven_cod = '$linha_ped->ped_vend';";
			$query_ven = mysql_query($sql_ven)or die("Erro na consulta do Vendedor!");		
			$linha_ven = mysql_fetch_object($query_ven);
		}
//      }
	}	
?>
<html>
<head>
	<title>| gercomweb | Pedido de Venda |</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<script language="JavaScript">
		function submit_action(caminho){
			//postando para a verificacao;
			document.form.action= caminho; 
			document.form.method= 'post'; 
			document.form.submit();
		}
		
		function valor_java(valor){
			valor = valor.replace(".","");
			valor = valor.replace(".","");
			valor = valor.replace(".","");
			valor = valor.replace(",",".");
			return parseFloat(valor);
		}

		//formata 5000 em 5.000,00
		function formata_valor(numero) {
			//numero = obj.value;
			numero = numero.replace(".","");
			numero = numero.replace(".","");
			numero = numero.replace(".","");
			numero = numero.replace(",",".");
			numero_formatado = (parseFloat(numero) * 1000)/10;
			numero_formatado = parseFloat(numero_formatado);
			numero_formatado = numero_formatado.toString();
			var tam = numero_formatado.length;
			if ( tam <= 1 ){
			numero_formatado = '0,0' + numero_formatado.substr( tam - 2, tam ); }
			if ( tam == 2 ){
			numero_formatado = '0,' + numero_formatado.substr( tam - 2, tam ); }
			if ( (tam > 2) && (tam <= 5) ){
			numero_formatado = numero_formatado.substr( 0, tam - 2 ) + ',' + numero_formatado.substr( tam - 2, tam ); }
			if ( (tam >= 6) && (tam <= 8) ){
			numero_formatado = numero_formatado.substr( 0, tam - 5 ) + '.' + numero_formatado.substr( tam - 5, 3 ) + ',' + numero_formatado.substr( tam - 2, tam ) ; }
			if ( (tam >= 9) && (tam <= 11) ){
			numero_formatado = numero_formatado.substr( 0, tam - 8 ) + '.' + numero_formatado.substr( tam - 8, 3 ) + '.' + numero_formatado.substr( tam - 5, 3 ) + ',' + numero_formatado.substr( tam - 2, tam ) ; }
			if ( (tam >= 12) && (tam <= 14) ){
			numero_formatado = numero_formatado.substr( 0, tam - 11 ) + '.' + numero_formatado.substr( tam - 11, 3 ) + '.' + numero_formatado.substr( tam - 8, 3 ) + '.' + numero_formatado.substr( tam - 5, 3 ) + ',' + numero_formatado.substr( tam - 2, tam ) ; }
			if ( (tam >= 15) && (tam <= 17) ){
			numero_formatado = numero_formatado.substr( 0, tam - 14 ) + '.' + numero_formatado.substr( tam - 14, 3 ) + '.' + numero_formatado.substr( tam - 11, 3 ) + '.' + numero_formatado.substr( tam - 8, 3 ) + '.' + numero_formatado.substr( tam - 5, 3 ) + ',' + numero_formatado.substr( tam - 2, tam ) ;}
			//obj.value = numero_formatado;
			return numero_formatado;
		}

		//converte campos do formato 5000 para 5.000,00;
		function converte(obj){
			if(obj.value != ""){
				var valor = obj.value;
				obj.value = formata_valor(valor);
			}
		}

	</script>
	<style type="text/css">
	<!--
		TD {
			font-family: verdana;
			font-size: 10px;
			font-style: normal;
			font-weight: normal;
			font-variant: normal;
		}
		FONT.tit {
			font-family: verdana;
			font-size: 10px;
			font-style: normal;
			font-weight: bold;
			font-variant: normal;
		}
		
	-->
	</style>
</head>

<body topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0">
<form name="form" action="pedido_rel.php?flag=detalhe&edtNumPed=<?=$edtNumPed?>&edtPedido=<?=$edtPedido?>&lstLoja=<?=$lstLoja?>">
<table width="100%" border="0" cellspacing="2" cellpadding="0" bordercolor="#CCCCCC">
	<?
		if(isset($msg_fecha)){
			echo "<tr>";
			echo "<td align='center' width='100%'><font class='AVISO'>".$msg_fecha."</font></td>";
			echo "</tr>";
		}
	?>
    <tr>
        <td bordercolor="#004000" valign="top">
			<table width="100%" valign="top" border="0" cellspacing="0" cellpadding="2" bordercolor="#008000">
				<tr>
            		<td valign="top" bordercolor="#FFFFFF" width="20%" height="100%" align="center"><img src="../imagens/<?=$linha_loja->lj_logopv?>" width="100" height="100" border="0"></td>
					<td valign="top" bordercolor="#FFFFFF" width="40%" height="100%">
						<table valign="top" bordercolor="#004000" border='1' height="100%" width="100%">
							<tr bordercolor="#FFFFFF">
								<td width="14%"><u>Loja:</u></td>
								<td width="86%"><?=$linha_loja->lj_fantasia?></td>
							</tr>
							<tr bordercolor="#FFFFFF">
								<td><u>End.:</u></td>
								<td><?=$linha_loja->lj_end?></td>
							</tr>
							<tr bordercolor="#FFFFFF">
								<td><u>Bairro:</u></td>
								<td>
									<table width="100%" border="0" cellspacing="0" cellpadding="0">
										<tr>
											<td width="40%"><?=$linha_loja->lj_bairro?></td>
											<td width="20%">Cidade:</td>
											<td width="40%"><?=$linha_loja->lj_cidade?></td>
										</tr>
									</table>
								</td>
							</tr>
							<tr bordercolor="#FFFFFF">
								<td><u>Fone:</u></td>
								<td>
									<table width="100%" border="0" cellspacing="0" cellpadding="0">
										<tr>
											<td width="40%"><?=$linha_loja->lj_fone?></td>
											<td width="20%">Fax:</td>
											<td width="40%"><?=$linha_loja->lj_fax?></td>
										</tr>
									</table>
								</td>
							</tr>
							<tr bordercolor="#FFFFFF">
								<td width="14%"><u>CGC:</u></td>
								<td width="86%"><?=$linha_loja->lj_cgc?></td>
							</tr>
						</table>
					</td>
					<td valign="top" bordercolor="#FFFFFF" width="40%" height="100%">
						<table valign="top" bordercolor="#004000" border='1' height="100%" width="100%">
							<tr bordercolor="#FFFFFF">
								<td><u>Cliente:</u></td>
								<td colspan='3' width="83%"><?=$linha_cli->cli_razao?></td>
							</tr>
							<tr bordercolor="#FFFFFF">
							  <td><u>CGC/CPF:</u></td>
							  <td><?=$linha_cli->cli_cgccpf?></td>
							  <td><u>RG/Insc.:</u></td>
							  <td><?=$linha_cli->cli_inscrg?></td>
							</tr>
							<tr bordercolor="#FFFFFF">
								<td><u>End.:</u></td>
								<td colspan='3'><?=$linha_cli->cli_end?> - <?=$linha_cli->cli_bairro?> - <?=$linha_cli->cli_cidade?>/<?=$linha_cli->cli_estado?> | CEP <?=$linha_cli->cli_cep?></td>
							</tr>
							<tr bordercolor="#FFFFFF">
								<td><u>Ponto Ref.:</u></td>
								<td colspan='3'><?=$linha_cli->cli_pontoref?></td>
							</tr>
							<tr bordercolor="#FFFFFF">
								<td><u>Email:</u></td>
								<td><?=$linha_cli->cli_email?></td>
								<td><u>Profissão:</u></td>
								<td><?=$linha_cli->clip_desc?></td>
							</tr>
							<tr bordercolor="#FFFFFF">
								<td><u>Fone:</u></td>
								<td><?=$linha_cli->cli_fone?></td>
								<td><u>Celular:</u></td>
								<td>[<?=$linha_cli->cli_operadora1?>] <?=$linha_cli->cli_celular1?> / [<?=$linha_cli->cli_operadora2?>] <?=$linha_cli->cli_celular2?> / <?=$linha_cli->cli_fax?></td>
							</tr>
						</table>						
					</td>
				</tr>
		<? // dados dos financiadores 
		$sql_financiador   = "SELECT cxd_financiador from cxdoc
							   WHERE cxd_pedido = '$edtNumPed' AND cxd_loja = '$lojalocada'
							   GROUP BY cxd_financiador
							   ORDER BY cxd_financiador;";
		$query_financiador = mysql_query($sql_financiador,$conexao);
		if(mysql_num_rows($query_financiador) > 0){
		
		?>
				<tr>
           		  <td valign="top" bordercolor="#FFFFFF" width="20%" height="100%" align="center"></td>
                  <td valign="top" bordercolor="#FFFFFF" width="40%" height="100%"></td>
                  <td valign="top" bordercolor="#FFFFFF" width="40%" height="100%">
				  <table valign="top" bordercolor="#004000" border='1' height="100%" width="100%">
							<tr bordercolor="#FFFFFF">
							  <td colspan="4"><u>Financiador(es):</u></td>
							</tr>
                            <tr>
							<?  while($linha_financiador = mysql_fetch_object($query_financiador)){ ?>
						  <tr bordercolor="#FFFFFF">
								<td colspan="4"><?=$linha_financiador->cxd_financiador?></td>
						  </tr>
                            <?  } // fim do while($linha_financiador = mysql_fetch_object($query_financiador)){ ?> 
                            
						</table>						
					</td>
				</tr>
		<? 
        } // fim do if(mysql_num_rows($query_financiador) > 0){
		 // fim dos dados dos financiadores 
		?>
			</table>
		</td>
    </tr>
	<tr>
		<td width="100%" height="100%">
			<table height="100%" width="100%" align='left' border="1" bordercolor="#FFFFFF">
				<tr>
					<td align='left'><font size='2'>Nº Pedido:</td>
					<td colspan="3" bordercolor="#008000" align='center' bgcolor="#FFFFE8"><font style="font-size:18px"><b><u><?=$edtNumPed?></u></b></font></td>
					<td align='right'><font size='2'>Data:</font></td>
					<td bordercolor="#008000" align='center' bgcolor="#FFFFE8"><font size='2'><b><u><?=muda_data_pt($linha_ped->ped_emissao)?></u></b></font></td>
				</tr>
			</table>
		</td>
	</tr>
    <tr>
        <td>
			<table width="100%" border="1" bordercolor="#FFFFFF">
				<tr bordercolor="#008000">
					<td align="left" bgcolor="#FFFFE8"><font class="tit">Cod</font></td>
					<td align="left" bgcolor="#FFFFE8"><font class="tit">Produto</font></td>
					<td align="left" bgcolor="#FFFFE8"><font class="tit">Escala 1</font></td>
					<td align="left" bgcolor="#FFFFE8"><font class="tit">Grupo</font></td>
					<td align="left" bgcolor="#FFFFE8"><font class="tit">Escala 2</font></td>
					<td align="right" bgcolor="#FFFFE8"><font class="tit">Qtde</font></td>
					<td align="right" bgcolor="#FFFFE8"><font class="tit">Val. Unit</font></td>
					<td align="right" bgcolor="#FFFFE8"><font class="tit">Total</font></td>
					<td align="center" bgcolor="#FFFFE8"><font class="tit">Tipo</font></td>
					<td align="center" bgcolor="#FFFFE8"><font class="tit">NCM</font></td>
					<td align="center" bgcolor="#FFFFE8"><font class="tit">Ped Fáb</font></td>
					<td align="left" bgcolor="#FFFFE8"><font class="tit">Loja</font></td>
					<td align="left" bgcolor="#FFFFE8"><font class="tit">Obs</font></td>                    
				</tr>
				<?
				//itens do pedido
				$sql_itens = "SELECT DISTINCT pm_prod, pro_desc, pro_descabv, pro_ncm, pm_valuni, pm_valtot, pm_cor, pm_precolib, pro_subgrupo,
											  pm_progrupo, pm_escala, pm_loginprecolib,
											  pm_dataprecolib, pm_horaprecolib, pm_precolibdir,
											  pm_qtd, pm_loja, pm_es, pm_num, pm_id
								FROM pedmov, produtos
							  WHERE pm_prod = pro_cod AND pm_num = '$edtNumPed' AND pm_lojaloc = '$lojalocada' ;";
				$query_itens  = mysql_query($sql_itens ,$conexao)or die("Erro na consulta dos Itens do Pedido!");
				//echo $sql_itens;
				if(mysql_num_rows($query_itens) > 0){
				    $precosugerido = 0;
					while($linha_itens = mysql_fetch_object($query_itens)){
						//verifica se existe pedido de compra para este pedido de venda
						$sql_pc = "SELECT pc_cod, pc_pedvend, pc_seq FROM pedcomp WHERE pc_pedvend='$edtNumPed'
									  AND pc_prod = '$linha_itens->pm_prod' AND pc_progrupo = '$linha_itens->pm_progrupo' 
									  AND pc_cor = '$linha_itens->pm_cor' 
									  AND pc_escala = '$linha_itens->pm_escala' AND pc_loja = '$linha_itens->pm_loja'; ";
						$query_pc = mysql_query($sql_pc, $conexao);
						//echo $sql_pc; exit;
						if(mysql_num_rows($query_pc) > 0){
							$linha_pc = mysql_fetch_object($query_pc);
							$pedcomp = 'SIM'; 
						}else{
							$pedcomp = "-";
						}				
						?>
						
					  	<?
						  //echo $linha_regiao->lj_liberapreco.'$linha_regiao->lj_liberapreco';
				    	 $precosugerido = 0; $precotravado = "";
						 if ($linha_regiao->lj_liberapreco == 'S'){
						 
						  if ($linha_itens->pro_subgrupo == "14"){
							$mostragrupo = " AND pre_grupo = '".$linha_itens->prog_cod."' ";
						  }else{
							$mostragrupo = "";						  
						  }
							
						  if ($linha_itens->pro_subgrupo == "09"){
						    if ($linha_itens->pm_escala == "9"){  
							  $tipov = "BIZ";
							}elseif ($linha_itens->pm_escala == "10"){
							  $tipov = "LAP";
							}else{
							  $tipov = "MOL";   
							}
  						  }else{
							$tipov = "";	
						  }
						  
							$sql_precof   = "SELECT DISTINCT pre_precocusto, pro_subgrupo, pre_tipovidro 
												        FROM precos, produtos 
											           WHERE pre_prod = pro_cod ".$mostragrupo." AND
													   	     pre_prod = '".$linha_itens->pm_prod."' AND
															 pre_tipovidro = '".$tipov."';"; 
							//echo $sql_precof;
							$query_precof = mysql_query($sql_precof,$conexao);
							if (mysql_num_rows($query_precof) > 0){
								$linha_precof = mysql_fetch_object($query_precof);

								$precosugerido = ($linha_regiao->reg_coef * $linha_precof->pre_precocusto) -
								(($linha_regiao->reg_coef * $linha_precof->pre_precocusto) * ($linha_ped->ppl_descmax * 0.01));
								
							  if ($linha_itens->pm_precolib != 'S'){	
								if ($precosugerido <= $linha_itens->pm_valuni){
									$sql_uppm   = "UPDATE pedmov SET pm_precolib = 'S', pm_loginprecolib = '$acelogin',
																	 pm_dataprecolib = '".muda_data_en($data)."',
																	 pm_horaprecolib = '$hora',
																	 pm_comissao = '".$linha_ped->ppl_comissao."'
												    WHERE pm_num = '$edtNumPed' AND pm_lojaloc = '$lojalocada' AND
													      pm_loja     = '".$linha_itens->pm_loja."' AND
													      pm_prod     = '".$linha_itens->pm_prod."' AND
													      pm_escala   = '".$linha_itens->pm_escala."' AND
													      pm_progrupo = '".$linha_itens->pm_progrupo."' AND														  														  pm_cor      = '".$linha_itens->pm_cor."' ;";
									//echo $sql_uppm;					  
									$query_uppm = mysql_query($sql_uppm,$conexao);
									$precotravado = 'N';
									if ($linha_itens->pm_precolibdir == "S"){
									  $autorizadopeladiretoria = 'S';
									}  
								}else{
									$sql_uppm   = "UPDATE pedmov SET pm_precolib = 'N', pm_loginprecolib = '$acelogin',
																	 pm_dataprecolib = '".muda_data_en($data)."',
																	 pm_horaprecolib = '$hora',
																	 pm_comissao = '".$linha_ped->ppl_comissao."'															 												    WHERE pm_num   = '$edtNumPed' AND pm_lojaloc = '$lojalocada' AND
													      pm_loja     = '".$linha_itens->pm_loja."' AND
													      pm_prod     = '".$linha_itens->pm_prod."' AND
													      pm_escala   = '".$linha_itens->pm_escala."' AND
													      pm_progrupo = '".$linha_itens->pm_progrupo."' AND														  														  pm_cor      = '".$linha_itens->pm_cor."' ;";
									//echo $sql_uppm;					  
									$query_uppm = mysql_query($sql_uppm,$conexao);
									$precotravado = 'S';
									if ($linha_itens->pm_precolibdir != "S"){
									  $autorizadopeladiretoria = 'N';
									}  
								}
							  }	
									if ($linha_itens->pm_precolibdir == "S"){
									  $autorizadopeladiretoria = 'S';
									}else{
									  $autorizadopeladiretoria = 'N';									
									}  
							}
							    //echo $linha_ped->ped_situacao.'$linha_ped->ped_situacao';
							   	 if ($linha_ped->ped_situacao == 'A'){								
									$sql_uppm   = "UPDATE pedmov SET pm_comissao = '".$linha_ped->ppl_comissao."'															 												    WHERE pm_num   = '$edtNumPed' AND pm_lojaloc = '$lojalocada' AND
													      pm_loja     = '".$linha_itens->pm_loja."' AND
													      pm_prod     = '".$linha_itens->pm_prod."' AND
													      pm_escala   = '".$linha_itens->pm_escala."' AND
													      pm_progrupo = '".$linha_itens->pm_progrupo."' AND														  														  pm_cor      = '".$linha_itens->pm_cor."' ;";
									//echo $sql_uppm;					  
									$query_uppm = mysql_query($sql_uppm,$conexao);
								 }	
						 } 	
					  	?>
						
<?
//codigo novo aki
				//escolhendo a escala
				$sql_escala_ = "SELECT esc_descabv FROM escala where esc_cod = '".$linha_itens->pm_escala."';";
                $query_escala_ = mysql_query($sql_escala_,$conexao);
				if (mysql_num_rows($query_escala_) > 0){
				  $linha_escala_ = mysql_fetch_object($query_escala_);
				}
				//escolhendo a grupo
				$sql_prog_ = "SELECT prog_descabv FROM progrupo where prog_cod = '".$linha_itens->pm_progrupo."';";
                $query_prog_ = mysql_query($sql_prog_,$conexao);
				if (mysql_num_rows($query_prog_) > 0){
				  $linha_prog_ = mysql_fetch_object($query_prog_);
				}
				//escolhendo a cor
				$sql_cor_ = "SELECT cor_descabv FROM cores where cor_cod = '".$linha_itens->pm_cor."';";
                $query_cor_ = mysql_query($sql_cor_,$conexao);
				if (mysql_num_rows($query_cor_) > 0){
				  $linha_cor_ = mysql_fetch_object($query_cor_);
				}
				//escolhendo a loja
				$sql_loja_ = "SELECT lj_sigla FROM loja where lj_cod = '".$linha_itens->pm_loja."';";
                $query_loja_ = mysql_query($sql_loja_,$conexao);
				if (mysql_num_rows($query_loja_) > 0){
				  $linha_loja_ = mysql_fetch_object($query_loja_);
				}

//fim do codigo novo aki									   
									   

?>                        
						<tr>
							<td bordercolor="#FFFFFF" align="left"><?=$linha_itens->pm_prod?></td>
							<td bordercolor="#FFFFFF" align="left"><?=$linha_itens->pro_descabv?></td>
							<td bordercolor="#FFFFFF" align="left"><?=$linha_escala_->esc_descabv?></td>
							<td bordercolor="#FFFFFF" align="left"><?=$linha_prog_->prog_descabv?></td>
							<td bordercolor="#FFFFFF" align="left"><?=$linha_cor_->cor_descabv?></td>
							<td bordercolor="#FFFFFF" align="right"><?=number_format($linha_itens->pm_qtd,'2',',','.')?></td>
							<td bordercolor="#FFFFFF" align="right"><?=number_format($linha_itens->pm_valuni,'2',',','.')?></td>
							<td bordercolor="#FFFFFF" align="right"><?=number_format($linha_itens->pm_valtot,'2',',','.')?></td>
							<td bordercolor="#FFFFFF" align="center"><?=$linha_itens->pm_es?></td>
							<td bordercolor="#FFFFFF" align="center"><?=$linha_itens->pro_ncm?></td>                            
							<td bordercolor="#FFFFFF" align="center"><?=$pedcomp?></td>
							<td bordercolor="#FFFFFF" align="left"><?=$linha_loja_->lj_sigla?></td>
                            <? if ($pedcomp == '-'){ ?>
							<td bordercolor="#FFFFFF" align="center">-</td>                            
                            <? }else{ ?>                            
							<?  $sql_sit   = "SELECT ped_situacao FROM pedcad WHERE ped_num = '$edtNumPed';";
                                $query_sit = mysql_query($sql_sit,$conexao)or die("Erro na Consulta!");
                                if(mysql_num_rows($query_sit) > 0){
                                 $linha_sit = mysql_fetch_object($query_sit);
								 if ($linha_sit->ped_situacao != "P") {
                            ?>
                            
          					<td bordercolor="#FFFFFF" align='center'>
                            <a href="pedido_rel_obs.php?seq=<?=$linha_pc->pc_seq?>&edtNumPed=<?=$edtNumPed?>"><img border="0" src="../imagens/icon_07c.gif"></a></td> 
							<?
								 }else{
							?>
							<td bordercolor="#FFFFFF" align="center">-</td>                                                        
                            <?
								 }									 
								}
							?>							                            
                            <? } ?>                                                        
						</tr>

				<?
				//checando se o pf tem obs comercial
  			    $obspf = '';					
				$sql_obspf_ = "SELECT pc_situacaoobs 
							     FROM pedcomp where pc_pedvend = '".$linha_itens->pm_num."' AND 
								   	  pc_prod = '".$linha_itens->pm_prod."' AND pc_escala = '".$linha_itens->pm_escala."' AND
									  pc_cor = '".$linha_itens->pm_cor."' AND pc_progrupo = '".$linha_itens->pm_progrupo."' AND
									  pc_loja = '".$linha_itens->pm_loja."' ;";
                $query_obspf = mysql_query($sql_obspf_,$conexao);
				if (mysql_num_rows($query_obspf) > 0){
				  $linha_obspf = mysql_fetch_object($query_obspf);
				  $obspf = $linha_obspf->pc_situacaoobs;
				}else{
				  $obspf = '';					
				}
                
				if ($obspf != '') {
				?>
						<tr>
							<td bordercolor="#FFFFFF" align="center">-</td>
							<td bordercolor="#FFFFFF" align="left" colspan="4">Detalhes: <?=$obspf?></td>
							<td bordercolor="#FFFFFF" align="center" colspan="8">-</td>                                                        
						</tr>
				<?
				} //fim do if ($obspf != '') {
				?>
	
						
			<? if ($linha_itens->pm_es == 'S'){ ?>				
				<? if ($linha_regiao->lj_liberapreco == 'S'){ ?>
					<? if ($precotravado == 'S') { ?>
						<tr>
						 <td bgcolor="#FF0000" colspan="6" bordercolor="#FFFFFF" align="right">
						 <font color="#FFFFFF">PREÇO MÍNIMO PARA ESTE PRODUTO:</font></td>
						 <td bgcolor="#FF0000" bordercolor="#FFFFFF" align="right">
						 <font color="#FFFFFF"><?=number_format($precosugerido,'2',',','.')?></font></td>
						 <td bgcolor="#FF0000" colspan="4" bordercolor="#FFFFFF" align="right">
						 <font color="#FFFFFF">ALTERAR O VALOR OU AUTORIZAÇÃO DA DIRETORIA.</font></td>
						</tr>
					<? } ?>						
					<? if ($autorizadopeladiretoria == 'S') { ?>
						<tr>
						 <td bgcolor="#FF0000" colspan="11" bordercolor="#FFFFFF" align="right">
						 <font color="#FFFFFF">PREÇO AUTORIZADO PELO SR(A) <?=$linha_itens->pm_loginprecolib?> NO DIA
						 <?=muda_data_pt($linha_itens->pm_dataprecolib)?> ÁS <?=$linha_itens->pm_horaprecolib?></font></td>
						</tr>
					<? } ?>						
				<? } ?>
			<? } ?>										
						<? 
					}
				}
				?>
			</table>
		</td>
    </tr>
    <tr>
        <td>

			<table width="100%" border="1" bordercolor="#FFFFFF">
<!--             
			<tr>					
				<td bordercolor="#FFFFFF" width="49%" valign="bottom">
					OBSERVAÇÕES PEDIDOS DE FÁBRICA:
					<textarea name="edtObsProd" cols="60" rows="2"><?=$edtObsProd?></textarea>
				</td>
				<td bordercolor="#FFFFFF" width="51%" valign="bottom">
				</td>
		    </tr>
-->            
			<tr>					
          <? if (($linha_ped->ped_situacao == 'A')  || ($linha_ped->ped_situacao == 'F') || ($linha_ped->ped_situacao == 'P') || 
		         ($linha_ped->ped_situacao == 'D') || ($linha_ped->ped_situacao == 'F')){ 
		  ?>		
				<td bordercolor="#FFFFFF" width="49%" valign="bottom">
					OBSERVAÇÕES GERAIS:
					<textarea name="edtObs" cols="60" rows="5"><?=$edtObs?></textarea>
				</td>
          <? } ?>								
				<td bordercolor="#FFFFFF" width="51%" valign="bottom">
					<table width="100%" border="1" bordercolor="#008000">
					<tr> 
						<td bordercolor="#FFFFFF" width="70%">Total dos Produtos:</td>
						<td width="30%" bordercolor="#FFFFFF" align="right">
						<font face='Arial' size='2'><b><?=number_format($valprod,'2',',','.')?></b></font>
						</td>
					</tr>
					<tr> 
						<td bordercolor="#FFFFFF" width="70%">Desconto:</td>
						<td width="30%" bordercolor="#FFFFFF" align="right">
							<?=number_format($desconto,'2',',','.')?>
						</td>
					</tr>
					<tr> 
						<td bordercolor="#FFFFFF" width="70%">Acrescimo:</td>
						<td width="30%" bordercolor="#FFFFFF" align="right">
							<?=number_format($acrescimo,'2',',','.')?>
						</td>
					</tr>
					<tr> 
						<td bordercolor="#FFFFFF" width="70%">Líquido:</td>
						<td bgcolor="#FFFFE8" width="30%" align="right">
						<font face='Arial' size='3'><b><u><?=number_format($valliq,'2',',','.')?></u></b></font>
						</td>
					</tr>
					</table>
				</td>
 			    </tr>
              <?
                 if ($planfinan != "S"){
                   $relatorio = " AND ped_loja = '$lojalocada'";
                 }
            	 $sql_ped = "SELECT ped_situacao FROM pedcad WHERE ped_num = '$edtNumPed' ".$relatorio.";";
                 //echo $sql_ped;
                 $query_ped = mysql_query($sql_ped)or die("Erro na consulta do Pedido 3!");
	             if (mysql_num_rows($query_ped) > 0){
                  $linha_ped = mysql_fetch_object($query_ped);
                   if ($linha_ped->ped_situacao == 'F'){
              ?>
                <tr>
 			    <td>
                <table>
                <tr>
          <? if (($linha_ped->ped_situacao == 'F') || ($linha_ped->ped_situacao == 'P') || 
		         ($linha_ped->ped_situacao == 'D') || ($linha_ped->ped_situacao == 'F')){ 
		  ?>		
			   	 <td width="49%" valign="top">
			   	  Motivo de Pendência:
			   	 </td>
			   	 <td>
			   	  Valor de Pendência:
			   	 </td>
          <? } ?>						 
			   	</tr>
			   	<tr>
          <? if (($linha_ped->ped_situacao == 'F') || ($linha_ped->ped_situacao == 'P') || 
		         ($linha_ped->ped_situacao == 'D') || ($linha_ped->ped_situacao == 'F')){ 
		  ?>		
                 <td>
			   	   	<textarea name="edtObsPend" cols="45" rows="1"><?=$edtObsPend?></textarea>
			   	 </td>
			   	 <td>
			   	   	<input name="edtValorPend" type="text" id="edtValorPend" value="<?=number_format($edtValorPend,'2',',','.')?>" size="15" maxlength="20">
			   	 </td>
          <? } ?>						 
			   	</tr>
			   	</table>

			   	</td>
			   	</tr>
			  <? } ?>
   <? if ($linha_ped->ped_situacao == 'P'){  ?>

			<tr>
				<td colspan="2"><br><br>RECEBI DO(A) SR(A). <?=$linha_cli->cli_razao?>, O VALOR DESTE PEDIDO DE VENDA DE ACORDO COM AS CONDIÇOES DE PAGAMENTO ABAIXO ESTIPULADAS:<br><br>
            </tr>
            <tr>
			<table width="100%">
			<td>
             FORMA DE PAGAMENTO:
			</td>
			</tr>
			<tr>
            <?
             if ($planfinan != "S"){
              $relatorio2 = " AND cxm_loja = '$lojalocada' ";
             }
        		$sql_cxmov = "SELECT cxm_din,cxm_tc,cxm_de, cxm_chd, cxm_chpl, cxm_chp, cxm_car, cxm_ccd 
							  FROM cxmov WHERE cxm_pedido = '$edtNumPed' ".$relatorio2.";";
                //echo $sql_cxmov;
                $query_cxmov = mysql_query($sql_cxmov)or die("Erro na consulta do Pedido 4!");
				if (mysql_num_rows($query_cxmov) > 0){
					$linha_cxmov = mysql_fetch_object($query_cxmov);
					$dinheiro  = $linha_cxmov->cxm_din;
					$deposito  = $linha_cxmov->cxm_de;
					$transferencia  = $linha_cxmov->cxm_tc;										
					$chequedia = $linha_cxmov->cxm_chd;
					$chequepreloja = $linha_cxmov->cxm_chpl;
					$chequepre = $linha_cxmov->cxm_chp;
					$cartao = $linha_cxmov->cxm_car;
					$cartacr = $linha_cxmov->cxm_ccd;
				}					
            ?>
            <td>
            <table width="100%" border="1" cellspacing="0" cellpadding="2">
			<tr>
            <td>DINHEIRO: <b><?=number_format($dinheiro,'2',',','.')?></td>
            <td>DEPÓSITO: <b><?=number_format($deposito,'2',',','.')?></td>
            <td>TRANSF: <b><?=number_format($transferencia,'2',',','.')?></td>                        
            <td>CH DIA: <b><?=number_format($chequedia,'2',',','.')?></td>
            <td>CH PRÉ LOJA: <b><?=number_format($chequepreloja,'2',',','.')?></td>
            <td>FIN: <b><?=number_format($chequepre,'2',',','.')?></td>
            <td>CARTÃO: <b><?=number_format($cartao,'2',',','.')?></td>
            <td>CARTA CRÉD: <b><?=number_format($cartacr,'2',',','.')?></td>
            </tr>
			</table>
            </td>
			</tr>
            <tr>
            <?
        		$sql_fin = "SELECT DISTINCT fin_desc FROM cxmov, financeira WHERE cxm_finan = fin_cod AND cxm_pedido = '$edtNumPed';";
        		$query_fin = mysql_query($sql_fin)or die("Erro na consulta do Pedido 5!");
        		$linha_fin = mysql_fetch_object($query_fin);
        		$sql_finp = "SELECT DISTINCT fp_financeira FROM finplano, cxmov  WHERE cxm_finplano = fp_cod AND cxm_pedido = '$edtNumPed';";
        		$query_finp = mysql_query($sql_finp)or die("Erro na consulta do Pedido 6!");
        		$linha_finp = mysql_fetch_object($query_finp);
            ?>
            
            <?
        		$sql_cxdoc   = "SELECT DISTINCT cxd_doc, cxd_venc, cxd_valor, cxd_financiador,cxd_conta,cxd_banco,cxd_tipodoc, fp_financeira, cxd_financ
                                      FROM cxdoc, finplano
                                      WHERE cxd_plano = fp_cod AND cxd_pedido = '$edtNumPed'
                                      AND cxd_loja = '$lojalocada'
                                      order by cxd_tipodoc, fp_financeira, cxd_venc;";
        		$query_cxdoc = mysql_query($sql_cxdoc)or die("Erro na consulta do cxdoc!");
        		//echo $sql_cxdoc;
            ?>
             <? if(mysql_num_rows($query_cxdoc) > 0){
                //$linha_cxdoc = mysql_fetch_object($query_cxdoc);
             ?>
			<td>
             RELAÇÃO DE CARTÕES / FINANCEIRAS:
			</td>
            </tr>
            <tr>
            <td>
            <table width="100%" border="1" cellspacing="0" cellpadding="2">
			<tr>
            <td align="left">TIPO DOC</td>
            <td align="left">FINAN/CARTÃO</td>
            <td align="left">PLANO</td>
            <td align="center">Nº DOC</td>
            <td align="left">FINANCIADOR</td>
            <td align="center">VENCIMENTO</td>
            <td align="right">VALOR R$</td>
            <td align="center">CONTA</td>
            <td align="center">BANCO</td>
            </tr>
			<? while($linha_cxdoc = mysql_fetch_object($query_cxdoc)){
        		$recvalor      = $linha_cxdoc->cxd_valor;
				
        		$sql_cxdoc2   = "SELECT fin_desc
                                      FROM financeira
                                      WHERE fin_cod = '".$linha_cxdoc->cxd_financ."';";
        		$query_cxdoc2 = mysql_query($sql_cxdoc2)or die("Erro na consulta do cxdoc!");
        		//echo $sql_cxdoc;
				if(mysql_num_rows($query_cxdoc2) > 0){
				  $linha_cxdoc2 = mysql_fetch_object($query_cxdoc2);
				}
				
            ?>
			<tr>
            <? if ($linha_cxdoc->cxd_tipodoc == "CA"){ $tipodedoc = "CARTÃO";} ?>
            <? if ($linha_cxdoc->cxd_tipodoc == "CP"){ $tipodedoc = "FIN CHEQUE";} ?>
            <? if ($linha_cxdoc->cxd_tipodoc == "CT"){ $tipodedoc = "FIN CARNET";} ?>
            <? if ($linha_cxdoc->cxd_tipodoc == "DC"){ $tipodedoc = "FIN DÉB C";} ?>
            <td width="10%" align="left"><b><?=$tipodedoc?></td>
            <td width="10%" align="left"><b><?=$linha_cxdoc->fp_financeira ?></td>
            <td width="10%" align="left"><b><?=$linha_cxdoc2->fin_desc ?></td>
            <td width="10%" align="center"><b><?=$linha_cxdoc->cxd_doc ?></td>
            <td width="30%" align="left"><b><?=$linha_cxdoc->cxd_financiador ?></td>
            <td width="10%" align="center"><b><?=muda_data_pt($linha_cxdoc->cxd_venc)?></td>
            <td width="10%"align="right"><b><?=number_format($recvalor,'2',',','.')?></td>
            <td width="5%" align="center"><b><?=$linha_cxdoc->cxd_conta ?></td>
            <td width="5%" align="center"><b><?=$linha_cxdoc->cxd_banco ?></td>
            </tr>
            <? } ?>
			</table>
            </td>
            </table>
            </tr>
   <?      } ?>
   <?    }   ?>
   <?  }     ?>
			</table>
		</td>
	</tr>

<? if ($ljcod == "XXXXXXXX") { ?>
    <tr>
        <td>
			<table width="100%" border="1" cellspacing="0" cellpadding="2" bordercolor="#000000">
			    <tr>
				 <td width="33%" align="center"><br><b><u>OBSERVAÇÕES EXTRAS:<br><br></u></b></td>
			    </tr>
                
<?
		$sql_poe   = "SELECT poe_desc FROM pedcadobsextra;";
		$query_poe = mysql_query($sql_poe,$conexao);
		if(mysql_num_rows($query_poe) > 0){
			while($linha_poe = mysql_fetch_object($query_poe)){
?>
			  <tr><td align="center"><font style="font-size:10px"><?=$linha_poe->poe_desc?></font></td></tr>
<?				
			} // fim do while($linha_poe = mysql_fetch_object($query_poe)){
		} // fim do if(mysql_num_rows($query_poe) > 0){

?>                
<? /*
                <tr><td align="center"><font style="font-size:12px">NÃO DAMOS GARANTIA PARA TECIDO NEM REVESTIMENTO EM GERAL, NEM POR MAU USO.</font></td></tr>
                <tr><td align="center"><font style="font-size:12px">EM CASO DE CANCELAMENTO SERÁ COBRADO UMA TAXA DE 20% DE MULTA POR QUEBRA DE CONTRATO.</font></td></tr>
                <tr><td align="center"><font style="font-size:12px">O FRETE E MONTAGEM SÃO POR CONTA DO CLIENTE.</font></td></tr>
                <tr><td align="center"><font style="font-size:12px">TODA RECLAMAÇÃO E SOLICITAÇÃO DE ASSISTÊNCIA, O CLIENTE TEM QUE SE DIRIGIR A LOJA COM A NOTA DE COMPRA E OFICIALIZAR A RECLAMAÇÃO OU DESISTÊNCIA ESPECIFICANDO OS MOTIVOS.</font></td></tr>
                <tr><td align="center"><font style="font-size:12px">PEÇAS DO MOSTRUÁRIO E PEÇAS COM AVARIAS, O CLIENTE SÓ RECEBERÁ A MERCADORIA SE ESTIVER DE ACORDO COM O QUE FOI VISTO, CASO NÃO ESTEJA, DEVOLVER PELO MESMO CARRO QUE FOI ENTREGAR. SE POR VENTURA, VIER RECLAMAR DEPOIS QUE CHEGOU RASGADO, FURADO, ARRANHADO, NÃO DAREMOS GARANTIA.</font>
</td></tr>
                <tr><td align="center"><font style="font-size:12px">PRAZO DE 90 DIAS PARA RECLAMAÇÃO DE DEFEITO DE FÁBRICA.</font></td></tr>
                <tr><td align="center"><font style="font-size:12px">NÃO ACEITAMOS TROCA NEM DEVOLUÇÃO PARA MERCADORIA PONTA DE ESTOQUE.</font></td></tr>
                <tr><td align="center"><font style="font-size:12px">NÃO FAZEMOS TROCA DEPOIS DE ENTREGUE A MERCADORIA.</font></td></tr>
                <tr><td align="center"><font style="font-size:12px">MERCADORIA PARA PEDIDO DE FÁBRICA PRAZO MÉDIO DE +/- ______ DIAS ÚTEIS.</font></td></tr>
                <tr><td align="center"><font style="font-size:12px">SERÁ COBRADO UMA TAXA DE R$ 30,00 PARA IR APURAR AS RECLAMAÇOES DE ASSISTÊNCIA, CASO SEJA PROBLEMA DE FABRICAÇÃO E ESTIVER NA GARANTIA, A TAXA SERÁ DISPENSADA/RESTITUÍDA.</font></td></tr>                                
                <tr><td align="center"><font style="font-size:12px">PRAZO LIMITE PARA MERCADORIA VENDIDA NO DEPÓSITO É DE 60 DIAS. APÓS ESSE PERÍODO, SERÁ COBRADO UMA MULTA DIÁRIA DE R$ 10,00.</font></td></tr>                                
                <tr><td align="center"><font style="font-size:12px">NÃO DESMONTAMOS NEM RETIRAMOS MÓVEIS DA RESIDÊNCIA DO CLIENTE PARA MONTAR EM SEU LUGAR O MÓVEL VENDIDO PELA LOJA.</font></td></tr>                                
                <tr><td align="center"><font style="font-size:12px">TODA MERCADORIA SÓ DEVERÁ RECEBIDA SE TIVER EM PERFEITO ESTADO, NÃO NOS RESPONSABILIZAREMOS POR DANOS POSTERIORES.</font></td></tr>                                
                <tr><td align="center"><font style="font-size:12px">MANUTENÇÃO E LIMPEZA DOS MÓVEIS ADQUIRIDOS NA LOJA É DE TOTAL RESPONSABILIDADE DO CLIENTE.</font></td></tr>                                
                <tr><td align="center"><font style="font-size:12px">SÓ GUARDAMOS A MERCADORIA NO NOSSO DEPÓSITO POR ATÉ 30 DIAS A PARTIR DA DATA DA VENDA.</font></td></tr>                                
                <tr><td align="center"><font style="font-size:12px">FAZER MANUTENÇÃO DOS PRODUTOS ADQUIRIDOS CONFORME INSTRUÇÃO ANEXADA PARA NÃO PERDEREM A GARANTIA. COURINOS OU QUAISQUER OUTROS PRODUTOS SINTÉTICOS NÃO RESISTE A UTILIZAÇÃO DE PRODUTOS QUÍMICOS E NEM INCIDÊNCIA INTENSA DO SOL POIS ESTÁ SUJEITO A RESSECAMENTO E QUEBRADURAS.</font></td></tr>
                <tr><td align="center"><font style="font-size:12px">SÓ RECEBER A MERCADORIA SE TIVER EM ACORDO, CASO CONTRÁRIO DEVOLVER IMEDIATAMENTE. NÃO TROCAMOS NENHUMA MERCADORIA DEPOIS DE ENTREGUE. TODA SOLICITAÇÃO DEVERÁ SER FEITA PARA O EMAIL DO SAC ABAIXO OU CORRESPONDÊNCIA PARA AV. SANTOS DUMONT, 1937, ALDEOTA, CEP 60150-160, FORTALEZA/CE. SE POSSÍVEL COM CÓPIA DO PEDIDO DE VENDA E FOTOS DO PRODUTO.</font></td></tr>
                <tr><td align="center"><font style="font-size:12px">GARANTIA DA IMPERMEABILIZAÇÃO DE TECIDOS É DE SEIS MESES E DA HIDRATAÇÃO DE COUROS E COURINOS É DE UM MÊS.</font></td></tr>
*/ ?>                
                
                <tr><td align="center"><font style="font-size:12px"><b><br><br>======== SAC: aguiara@jacauna.net ============</b></font></td></tr>
			</table>
		</td>
    </tr>
<? } ?>
    <tr>
        <td>
		  <table width="100%" border="0" cellspacing="0" cellpadding="2">
			<tr>
				<td width="33%" align="center">&nbsp;</td>
                <td width="33%" align="center">&nbsp;</td>
                <td width="34%" align="center">&nbsp;</td>                
			</tr>
			<tr>
				<td width="33%" align="center">&nbsp;</td>
				<td width="33%" align="center">&nbsp;</td>
                <td width="34%" align="center">&nbsp;</td>                                
			</tr>
			<tr>
				<td width="33%" align="center">&nbsp;</td>
				<td width="33%" align="center">&nbsp;</td>
                <td width="34%" align="center">&nbsp;</td>                                
			</tr>				
<? if ($ljcod == "XXXXXXXX") { ?>            
			<tr>
				<td width="33%" align="center">________________________________________</td>
				<td width="33%" align="center">________________________________________</td>                
				<td width="34%" align="center">________________________________________</td>
			</tr>
<? } ?>            
<?			$sql_ger = "SELECT ger_nome FROM gerentes WHERE ger_loja = '$lojalocada' LIMIT 0,1;";
			$query_ger = mysql_query($sql_ger)or die("Erro na consulta do Gerente!");
			$linha_ger = mysql_fetch_object($query_ger);
?>
			<tr>
				<td width="33%" align="center">Vendedor: <?=$linha_ven->ven_nome ?></td>
<!-- 			<td width="33%" align="center">Cliente:  <?=$linha_cli->cli_razao?></td> -->
	 			<td width="33%" align="center"></td>
				<td width="34%" align="center">Gerente: <?=$linha_ger->ger_nome?></td>                
			</tr>
            
<? if ($ljcod == "XXXXXXXX") { ?>                        
		<? // dados dos financiadores 
		$sql_financiador2   = "SELECT cxd_financiador from cxdoc
							   WHERE cxd_pedido = '$edtNumPed' AND cxd_loja = '$lojalocada'
							   GROUP BY cxd_financiador
							   ORDER BY cxd_financiador;";
		$query_financiador2 = mysql_query($sql_financiador2,$conexao);
		if(mysql_num_rows($query_financiador2) > 0){
	     while($linha_financiador2 = mysql_fetch_object($query_financiador2)){
	      if ($linha_cli->cli_razao != $linha_financiador2->cxd_financiador ){
		?>
			<tr>
				<td width="33%" align="center"></td>
				<td width="33%" align="center"><br><br>________________________________________</td>                
				<td width="34%" align="center"></td>
			</tr>
			<tr>
				<td width="33%" align="center"></td>
				<td width="33%" align="center">Financiador:  <?=$linha_financiador2->cxd_financiador?></td>
				<td width="34%" align="center"></td>                
			</tr>
      <?  } //fim do if ($linha_cli->cli_razao != $linha_financiador2->cxd_financiador ){
	     } // fim do while($linha_financiador = mysql_fetch_object($query_financiador)){
        } // fim do if(mysql_num_rows($query_financiador) > 0){
		 // fim dos dados dos financiadores 
	  ?>
<? } ?>                        
            
            
            
			</table>
		</td>
    </tr>



	<tr>
	
	<? if($button != "no"){ ?>
		<tr>
		<table align="center">
			<td width="100%" align="center"> <br>
  	  <? if($bot_pend != "ok"){ ?>
        <? //if ($naoimprimebotoes != "ok") {?>
              <? // if($REQUEST_METHOD == "GET") { if ($fecham != "OK") { ?>

			<?
				  $sql_pl   = "select pm_precolib FROM pedmov
								WHERE pm_precolib = 'N' AND pm_num = '$edtNumPed' 
								  AND pm_lojaloc = '$lojalocada' AND pm_es = 'S'";
				  //echo $sql_pl;					 
				  $query_pl = mysql_query($sql_pl,$conexao)or die(mysql_error());
				  if(mysql_num_rows($query_pl) > 0){
					$linha_pl = mysql_fetch_object($query_pl);
					$precoliberado = 'N';
				  }else{
					$precoliberado = 'S';				  
				  }	
			
			?>
            <? if ($precoliberado == 'S'){ ?>
              <? if ($linha_fechado->ped_situacao == 'A'){ ?>
				<input type="button" value="Fechamento" style="color:#000000; border:solid 1; width:150; background-color:#FFFF00;" onClick="submit_action('pedido_senhager.php?edtNumPed=<?=$edtNumPed?>&edtPedido=<?=$edtPedido?>&menu=off&edtObs=<?=$edtObs?>&edtObsProd=<?=$edtObsProd?>&edtObsPend=<?=$edtObsPend?>&edtValorPend=<?=$edtValorPend?>&gravaobs=ok&credito=ok');">
              <? } ?>

			<? } ?>

              <? if ($linha_fechado->ped_situacao == 'F'){ ?>
				<input type="button" value="Pagamento" style="color:#000000; border:solid 1; width:150; background-color:#FFFF00;" onClick="submit_action('pedido_pag.php?edtNumPed=<?=$edtNumPed?>&menuoff=ok&edtObs=<?=$edtObs?>&edtObsProd=<?=$edtObsProd?>&edtObsPend=<?=$edtObsPend?>&edtValorPend=<?=$edtValorPend?>&gravaobs=ok');">
              <? // if ($ljcod != '33'){ //reabilitando esses dois botoes no dia 05-09-2012 email alexandre ?>
              <? if ($ljcod == '33' || $ljcod == '24' || $ljcod == '45'){ //INABILITADO bota pendencia no dia 05-09-2012 fone jonathan ADD PIT COC DIA 27-05-2013 ?>              
             <?  }else{ ?>                
				<input type="button" value="Pendência" style="color:#000000; border:solid 1; width:150; background-color:#FFFF80;" onClick="submit_action('pedido_rel.php?flag=finalizar&edtNumPed=<?=$edtNumPed?>&lstLoja=<?=$lstLoja?>&grava=sim&pendencia=ok&gravaobs=ok&bot_pend=ok&credito=ok&edtObs=<?=$edtObs?>&edtObsProd=<?=$edtObsProd?>');">
              <?  } ?>                              
				<input type="button" value="Crédito para o Cliente" style="color:#000000; border:solid 1; width:150; background-color:#FFFF80;" onClick="submit_action('pedido_rel.php?flag=finalizar&edtNumPed=<?=$edtNumPed?>&lstLoja=<?=$lstLoja?>&cliente=<?=$linha_cli->cli_cgccpf?>&grava=sim&credito=ok&gravaobs=ok&edtObs=<?=$edtObs?>&edtObsProd=<?=$edtObsProd?>');">


              <? } ?>
              <? if ($linha_fechado->ped_situacao == 'D'){ ?>
				<input type="button" value="Pagamento" style="color:#000000; border:solid 1; width:150; background-color:#FFFF80;" onClick="submit_action('pedido_pag.php?edtNumPed=<?=$edtNumPed?>&menuoff=ok&edtObs=<?=$edtObs?>&edtObsProd=<?=$edtObsProd?>&edtObsPend=<?=$edtObsPend?>&edtValorPend=<?=$edtValorPend?>&gravaobs=ok');">
              <? } ?>
	    <?// } ?>
	   <? } ?>

				<input type="button" value="Imprimir PDF" style="color:#FFFFFF; border-color:#000; border-style:solid 1;  width:150; background-color:#AA0000;" onClick="submit_action('pedido_rel_novo.php?edtNumPed=<?=$edtNumPed?>')">

				<input type="button" value="Imprimir" style="color:#000000; border:solid 1; width:150; background-color:#FFFF80;" onClick="submit_action('pedido_rel.php?flag=finalizar&edtNumPed=<?=$edtNumPed?>&lstLoja=<?=$lstLoja?>&button=no&grava=sim&edtObs=<?=$edtObs?>&edtObsProd=<?=$edtObsProd?>&edtObsPend=<?=$edtObsPend?>&edtValorPend=<?=$edtValorPend?>&gravaobs=ok'); window.print(); window.close();">


<?  $sql_nv   = "SELECT ace_nivel FROM acessos WHERE ace_login = '$acelogin';";
    $query_nv = mysql_query($sql_nv,$conexao)or die("Erro na Consulta!");
    if(mysql_num_rows($query_nv) > 0){
     $linha_nv = mysql_fetch_object($query_nv);
     if ($linha_nv->ace_nivel != 'STANDARD'){
?>
              <? if ($linha_fechado->ped_situacao == 'P'){ ?>
				<input type="button" value="PV CONFERIDO" style="color:#000000; border:solid 1; width:150; background-color:#FFFF80;" onClick="submit_action('pedido_rel.php?flag=finalizar&edtNumPed=<?=$edtNumPed?>&lstLoja=<?=$lstLoja?>&grava=sim&conferido=ok');">
              <? } ?>

<?   }
    }
?>
				<input type="button" value="Garantia Estendida" style="color:#000000; border:solid 1; width:150; background-color:#FFFF80;">
			</td>
		</table>
		</tr>
	<? } ?>
</table>

</form>
</body>
</html>
<?
  include("rodape.php");
?>