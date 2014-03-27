<?
	include("conexao2.inc.php");
	include("funcoes2.inc.php");
	//include("dglogin1.php");
  	
    $arquivo = "pedido_cad.php";
    //include("auditoria.php");

	$data = date("d/m/Y");
    $hora = date("H:i:s");
	$trava_alt = "readonly";
	
	//so para teste
	$ljcod = "01";
	$acelogin = "jalen"; 
	$orc_num = "";
	$trava_cab = "disabled";
	$flag = "finalizar";
	//$edtNumPed = "";
	//so para teste
	
   // travamento total do caixa
   // COMEÇA FAZENDO UM TRAVAMENTO DO CAIXA
   // TEM COMO ENTRADA O CODIGO DA LOJA
   $caixatravado = "N";	   
   $sql_trc = "SELECT trc_loja, trc_motivo FROM travacaixa WHERE trc_loja = '$ljcod' AND trc_excluido = 'N';";
   $query_trc = mysql_query($sql_trc,$conexao)or die("Erro na Consulta!");
   if(mysql_num_rows($query_trc) > 0){
     $linha_trc = mysql_fetch_object($query_trc);	   
	 $caixatravado = "S";
   }else{
	 $caixatravado = "N";
   }
	// CASO O CAIXA DA LOJA ESTEJA TRAVADO VAI ABIR ESTA TELA
	// CASO NAO ELA VAI ABIR OUTRA TELA ONDE PODE-SE CONTINUAR COM
	// A VENDA
  if ($caixatravado == "S") { ?>
		<html>
		<head>
			<link rel="stylesheet" href="est_big.css" type="text/css">
			<title>.:/gercomweb</title>
        </head>
        <body>
          <table align="center">
            <tr valign="middle">
             <td align="center" bgcolor="#FFFFFF"><font style="font-size:36px; color:#FF0000">Caixa da Loja 
             Travado!!!</font></td>
            </tr>
            <tr>
             <td align="center" bgcolor="#FFFFFF"><font style="font-size:36px; color:#FF0000">
             Motivo: <?=$linha_trc -> trc_motivo ?></font></td>            
          </table>
        </body>
        </html>    
		<? } else {  //fim do if ($caixatravado == "S"){
			/**
			* COMEÇA A NOVA TELA
			*/
			
			// PEGA A REGIAO DA LOJA
			//////////////////////////////////////////////
			$sql_regiao   = "SELECT DISTINCT reg_coef, reg_descav1, reg_descav2, reg_descav3 from loja, regioes
			WHERE lj_regiao = reg_num AND lj_cod = '$ljcod';";
			$query_regiao = mysql_query($sql_regiao,$conexao);
			if(mysql_num_rows($query_regiao) > 0){
			$linha_regiao = mysql_fetch_object($query_regiao);
			}//////////////////////////////////////////////
			
			
			//////////////////////////////////////////////
			if($flag == "excluir_item"){
				$sql_ex = "DELETE FROM pedmov
				WHERE pm_num = '$edtNumPed' AND
				pm_prod = '$prod' AND pm_cor = '$cor' AND pm_escala = '$escala'
				AND pm_progrupo = '$progrupo' AND pm_loja = '$loja';";
				$query_ex = mysql_query($sql_ex,$conexao) or die ("Erro na Exclusão do Item '$prod'!");
				$edtProd   = "";  $edtRef     = "";  $edtNomeprod = "";
				$lstCor    = "";  $lstEscala  = "";  $lstProg     = "";
				$edtqtd    = "";  $edtValunit = "";  $edtTotal    = "";
				$trava_cab = "disabled";
				$trava_ped = "readonly";
			}//////////////////////////////////////////////
			
			
			//////////////////////////////////////////////
			if($REQUEST_METHOD == "GET"){
				if($acao == "reload"){
				$sql_busca = "SELECT ped_vend, ped_tipove, ped_cliente, ped_situacao, ped_status
				FROM pedcad WHERE ped_num = '$edtNumPed' AND ped_loja = '$ljcod';";
				$query_busca = mysql_query($sql_busca,$conexao)or die("Erro na Consulta do Reload!");
				if(mysql_num_rows($query_busca) > 0){
				$linha_busca = mysql_fetch_object($query_busca);
				$lstVend = $linha_busca->ped_vend;
				$lstTV = $linha_busca->ped_tipove;
				$lstCli = $linha_busca->ped_cliente;
				$edtSit = $linha_busca->ped_situacao;
				$edtStatus = $linha_busca->ped_status;
				}
			}//////////////////////////////////////////////
			
			
			// ELE SO VAI FECHAR NO FINAL DESSA PARTE DE PHP
			////////////////////  1  //////////////////////////
			} elseif ($REQUEST_METHOD == "POST") {
				//////////////////////////////////////////////					
				if($incluir_desconto == "s"){
				$descontov = valor_mysql($edtDesconto);
				$sql_desconto = "UPDATE pedcad SET ped_desconto = '$descontov'
				WHERE ped_num = '$edtNumPed' AND ped_loja = '$ljcod';";
				$query_desconto = mysql_query($sql_desconto,$conexao)or die("Erro na Alteração do Pedido Desconto!");
				$trava_cab  = "disabled";
				$trava_cab2 = "disabled";
				$trava_ped  = "readonly";
				$pro_det    = "";
			}//////////////////////////////////////////////
				
				// FLAG = FINALIZAR
				if($flag == "finalizar") {
				$data = muda_data_en($data);
				//selecionando os produtos do pedido
				$sql_pm = "SELECT * FROM pedmov WHERE pm_num = '$edtNumPed' AND pm_lojaloc = '$ljcod';";
				$query_pm = mysql_query($sql_pm, $conexao)or die("Erro na Busca dos Produtos do Pedido!");

				if(mysql_num_rows($query_pm) > 0){
				while($result_pm = mysql_fetch_object($query_pm)){
				if ($alteraritem != "OK"){
				if($result_pm->pm_es == "E"){
				if ($result_pm->pm_loja == $ljcod){
				//buscando o saldo do produdo
				$sql_busca = "SELECT * FROM saldos
				WHERE sal_cod = '".$result_pm->pm_prod."'
				AND sal_cor = '".$result_pm->pm_cor."'
				AND sal_escala = '".$result_pm->pm_escala."'
				AND sal_progrupo = '".$result_pm->pm_progrupo."'
				AND sal_loja = '$ljcod' AND sal_pv = '';";
				$query_busca = mysql_query($sql_busca)or die("Erro na Consulta do Estoque!");
				$linha_busca = mysql_fetch_object($query_busca);

				// tirando do estoque real
				$est_anterior = $linha_busca->sal_estreal;
				$est_atual = $est_anterior - $result_pm->pm_qtd + $edtqtdantes;
				$data = date("d/m/Y");
				$data = muda_data_en($data);
				$msg_prod = "Pedido Finalizado Com sucesso! corrente";
				
				} else {
				//buscando o saldo do produdo
				$sql_busca = "SELECT * FROM saldos
				WHERE sal_cod = '".$result_pm->pm_prod ."'
				AND sal_cor = '".$result_pm->pm_cor."'
				AND sal_escala = '".$result_pm->pm_escala."'
				AND sal_progrupo = '".$result_pm->pm_prog."'
				AND sal_loja = '".$result_pm->pm_loja."' AND sal_pv = '';";
				$query_busca = mysql_query($sql_busca)or die("Erro na Consulta do Estoque!");
				$linha_busca = mysql_fetch_object($query_busca);
				// tirando do estoque real
				$saldoant = $linha_busca->sal_estreal;
				$saldo = $saldoant - $result_pm->pm_qtd;
				$data = date("d/m/Y");
				$data = muda_data_en($data);
				$msg_prod = "Pedido Finalizado Com sucesso!";
				}
				}elseif($result_pm->pm_es == "E"){
				if ($result_pm->pm_loja != "JAC"){
				//buscando o saldo do produdo
				$sql_busca = "SELECT * FROM saldos
				WHERE sal_cod = '".$result_pm->pm_prod."'
				AND sal_cor = '".$result_pm->pm_cor."'
				AND sal_escala = '".$result_pm->pm_escala."'
				AND sal_progrupo = '".$result_pm->pm_progrupo."'
				AND sal_loja = '$ljcod';";
				$query_busca = mysql_query($sql_busca)or die("Erro na Consulta do Estoque!");
				$linha_busca = mysql_fetch_object($query_busca);
				// tirando do estoque real
				$est_anterior = $linha_busca->sal_estreal;
				$est_atual = $est_anterior + $result_pm->pm_qtd;
				$data = date("d/m/Y");
				$data = muda_data_en($data);
				$msg_prod = "Pedido Finalizado Com sucesso! corrente";
				//buscando o saldo do produdo
				$sql_busca = "SELECT * FROM saldos
				WHERE sal_cod = '".$result_pm->pm_prod."'
				AND sal_cor = '".$result_pm->pm_cor."'
				AND sal_escala = '".$result_pm->pm_escala."'
				AND sal_loja = '".$result_pm->pm_loja."';";
				$query_busca = mysql_query($sql_busca)or die("Erro na Consulta do Estoque!");
				$linha_busca = mysql_fetch_object($query_busca);
				// tirando do estoque real
				$saldoant = $linha_busca->sal_estreal;
				$saldo = $saldoant + $result_pm->pm_qtd;
				$data = date("d/m/Y");
				$data = muda_data_en($data);
				$msg_prod = "Pedido Finalizado Com sucesso!";
				}
				}//fechando o else de situacao
				//calculando saidas
				$sql_saida = "SELECT sum(pm_valtot) as valprod FROM pedmov
				WHERE pm_num = '$edtNumPed' AND pm_es = 'S'
				AND pm_lojaloc = '$ljcod';";
				$query_saida = mysql_query($sql_saida,$conexao)or die("Erro na Soma das Saidas: ".mysql_error());
				$linha_saida = mysql_fetch_object($query_saida);
				$valprod_saida = $linha_saida->valprod;
				//calculando entradas
				$sql_entra = "SELECT sum(pm_valtot) as valprod FROM pedmov
				WHERE pm_num = '$edtNumPed' AND pm_es = 'E'
				AND pm_lojaloc = '$ljcod';";
				$query_entra = mysql_query($sql_entra,$conexao)or die("Erro na Soma das Entradas: ".mysql_error());
				$linha_entra = mysql_fetch_object($query_entra);
				$valprod_entra = $linha_entra->valprod;
				//calculando totais
				$valprod = $valprod_saida + $valprod_entra;
				$valliq = ($valprod + $acrescimo) - $desconto;
				$sql_alt = "UPDATE pedcad SET ped_valprod = $valprod, ped_valliq = $valliq, ped_hora = '$hora',
				ped_login = '$acelogin'
				WHERE ped_num = '$edtNumPed' AND ped_emp = '$codemp' AND ped_loja = '$ljcod'";
				$query_alt = mysql_query($sql_alt)or die("Erro na Atualização do Pedido!");
	
				//retirando o * da descr do prod na movimentacao do pedido
				$descr = substr($result_pm->pm_desc, 0 ,strlen($result_pm->pm_desc)-1);
				$sql_atu_mp = "UPDATE pedmov SET pm_desc='$descr', pm_hora = '$hora', pm_login = '$acelogin'
				WHERE pm_num = '$edtNumPed' AND pm_prod = '$result_pm->pm_prod'
				AND pm_cor = '$result_pm->pm_cor' AND pm_escala = '$result_pm->pm_escala'
				AND pm_progrupo = '$result_pm->pm_progrupo' AND pm_lojaloc = '$ljcod';";
				$query_atu_mp = mysql_query($sql_atu_mp, $conexao)or die("Erro na Retirada do * do Cod do Item
				do Pedido!");
				}  //fim do result = s ou e
			  } //fim do while
			} //fim do if alteraritem == ok
				echo "<script>window.location = '../main.php?menuoff=".$menuoff."';</script>";
				exit;
			}
			// FECHANDO A PARTE DO 
			// FLAG = FINALIZAR
			
			
				//////////////////////////////////////////////
				// INCLUIR = S
				if($incluir == "s") {
				$edtEmissao = muda_data_en($edtEmissao);
				$data = muda_data_en($data);
				//buscando o numero do pedido
				$sql_num   = "SELECT ped_num FROM pedcad;";
				$query_num = mysql_query($sql_num,$conexao)or die("Erro na Busca do Codigo!");
				$rows = mysql_num_rows($query_num);//num de registros

				//codigo novo começa aqui
				$sql_lojaloc   = "SELECT lj_liberatalao FROM loja WHERE lj_cod = '$ljcod';";
				$query_lojaloc = mysql_query($sql_lojaloc,$conexao)or die("Erro na Busca da Loja!");
				$linha_lojaloc = mysql_fetch_object($query_lojaloc);

				if ($linha_lojaloc->lj_liberatalao == 'S') {
				$sql_pedant   = "SELECT ped_num FROM pedcad WHERE ped_vend = '$lstVend'
				AND (ped_situacao = 'A' or ped_situacao = 'F');";
				$query_pedant = mysql_query($sql_pedant,$conexao)or die("Erro na Busca do Codigo!");
				if(mysql_num_rows($query_pedant) > 0){
				while($linha_pedant = mysql_fetch_object($query_pedant)){
				$pedidos = $pedidos.'['.$linha_pedant->ped_num.']';
				}
				$msg_ped = "<table bgcolor='#004000' bordercolor='#004000' border='1' align='center'>
				<tr><td align='center'><b><font size='3' color='#FFFF00'>
				Este Vendedor possui pedidos de venda [ ".$pedidos." ] em aberto ou fechado.
				Finalize-os primeiro!</font></b>
				</td></tr></table>";
				}else{
				//consultando seqpv no loja
				$sql_pvautomatico0 = "SELECT lj_pvautomatico FROM loja where lj_cod = '$ljcod';";
				$query_pvautomatico0 = mysql_query($sql_pvautomatico0,$conexao);
				$linha_pvautomatico0 = mysql_fetch_object($query_pvautomatico0);
				if ($linha_pvautomatico0->lj_pvautomatico == "S"){

				if ($lstCli == ''){
				$msg_ped = "<table bgcolor='#004000' bordercolor='#004000' border='1' align='center'>
				<tr><td align='center'><b><font size='3' color='#FFFF00'>
				Não pode gravar o cabeçalho do pedido sem escolher um cliente! Volte e refaça os
				procedimentos corretos!</font></b>
				</td></tr></table>";
				}else{ //if ($lstCli == ''){
				//inclui o item no banco pedcad 1
				$sql_incl = "INSERT INTO pedcad (ped_num,ped_emp,ped_loja,ped_cliente,ped_tipove,
				ped_emissao,ped_situacao,ped_dtsituacao,ped_vend,
				ped_incluido,ped_alterado,ped_dtincluido, ped_hora, ped_login,
				ped_orcamento)
				VALUES ('$edtNumPed', '$codemp', '$ljcod', '$lstCli', '0',
				'$edtEmissao','A','$data','$lstVend',
				'S', 'N','$data', '$hora','$acelogin', '$orc_num')";
				$query_incl = mysql_query($sql_incl)or die("<table bgcolor='#004000' bordercolor='#004000'
				border='1' align='center'><tr><td align='center'><b>
				<font size='3' color='#FFFF00'>Erro na Conclusão do Pedido ou Pedido já Cadastrado!</font></b>
				</td></tr></table>");
				//atualizando seqpv no loja

				//gravando itens do orcamento no pv
				$sql_orcitens   = "SELECT DISTINCT orc_num, orc_prod, orc_escala1, orc_grupo, orc_escala2,
				orc_qtd, orc_valor, pro_descabv, pro_comissao
				FROM orcamento, produtos WHERE orc_prod = pro_cod AND orc_num = '$orc_num'";
				$query_orcitens = mysql_query($sql_orcitens,$conexao)or die("Erro na Busca do PV do Talao!");
				if(mysql_num_rows($query_orcitens) > 0){
				while ($linha_orcitens = mysql_fetch_object($query_orcitens)) {
				$totaldoitem = $linha_orcitens->orc_qtd * $linha_orcitens->orc_valor;
				$sql_incl   = "INSERT INTO pedmov
				(pm_num, pm_prod, pm_emp, pm_loja, pm_lojaloc, pm_cor, pm_escala,
				pm_progrupo, pm_es, pm_desc,pm_comple,pm_qtd,pm_valuni, pm_valtot,
				pm_incluido,pm_alterado,pm_dtincluido, pm_entregue,pm_comissao,
				pm_hora,pm_login)
				VALUES
				('$edtNumPed', '".$linha_orcitens->orc_prod."','$codemp', '$lstLoja',
				'$ljcod', '".$linha_orcitens->orc_escala2."',
				'".$linha_orcitens->orc_escala1."','".$linha_orcitens->orc_grupo."','S', '"
				.$linha_orcitens->pro_descabv."',
				'$pro_comple','".$linha_orcitens->orc_qtd."', '".$linha_orcitens->orc_valor
				."','".$totaldoitem."', 'S', 'N','$data','N',
				'".$linha_orcitens->pro_comissao."', '$hora', '$acelogin')";
				//echo $sql_incl;
				$query_incl = mysql_query($sql_incl)or die("Erro na Conclusao do Item do Pedido ou Item do
				Pedido já Cadastrado!");
				}
				}
				//fim do gravando itens do orcamento no pv

				$sql_pvautomatico1 = "SELECT lj_pvautomatico FROM loja where lj_cod = '$ljcod';";
				$query_pvautomatico1 = mysql_query($sql_pvautomatico1,$conexao);
				$linha_pvautomatico1 = mysql_fetch_object($query_pvautomatico1);
				if ($linha_pvautomatico1->lj_pvautomatico == "S"){
				$sql_incloja = "update loja set lj_seqpv = lj_seqpv + 1 WHERE lj_cod = '$ljcod'";
				$query_incloja = mysql_query($sql_incloja) or die ("Erro!");
				}
				} // fim do if ($lstCli == ''){
				}else{

				$sql_tp   = "SELECT tp_numero FROM taloespv
				WHERE tp_numero = '$edtNumPed' AND tp_loja = '$ljcod'
				AND tp_vendedor = '$lstVend' AND tp_ativo = 'S';";
				$query_tp = mysql_query($sql_tp,$conexao)or die("Erro na Busca do PV do Talao!");
				if(mysql_num_rows($query_tp) > 0){
				$sql_tpmin   = "SELECT MIN(tp_numero) as tp_numero FROM taloespv
				WHERE tp_loja = '$ljcod' AND tp_vendedor = '$lstVend' AND tp_ativo = 'S';";
				$query_tpmin = mysql_query($sql_tpmin,$conexao)or die("Erro na Busca do PV do Talao 2!");
				if(mysql_num_rows($query_tpmin) > 0){
				$linha_tpmin = mysql_fetch_object($query_tpmin);
				if ($linha_tpmin->tp_numero == $edtNumPed){

				if ($lstCli == ''){
				$msg_ped = "<table bgcolor='#004000' bordercolor='#004000' border='1' align='center'>
				<tr><td align='center'><b><font size='3' color='#FFFF00'>
				Não pode gravar o cabeçalho do pedido sem escolher um cliente! Volte e refaça
				os procedimentos corretos!</font></b>
				</td></tr></table>";
	
				}else{ //if ($lstCli == ''){
				//inclui o item no banco pedcad 2
				$sql_incl = "INSERT INTO pedcad (ped_num,ped_emp,ped_loja,ped_cliente,ped_tipove,
				ped_emissao,ped_situacao,ped_dtsituacao,ped_vend,
				ped_incluido,ped_alterado,ped_dtincluido, ped_hora, ped_login, ped_orcamento)
				VALUES ('$edtNumPed', '$codemp', '$ljcod', '$lstCli', '0',
				'$edtEmissao','A','$data','$lstVend',
				'S', 'N','$data', '$hora','$acelogin','$orc_num')";
				$query_incl = mysql_query($sql_incl)or die("<table bgcolor='#004000' bordercolor='#004000' border='1' align='center'><tr><td align='center'><b><font size='3' color='#FFFF00'>Erro na Conclusão do Pedido ou Pedido já Cadastrado!</font></b></td></tr></table>");

				//gravando itens do orcamento no pv
				$sql_orcitens   = "SELECT DISTINCT orc_num, orc_prod, orc_escala1, orc_grupo, orc_escala2, orc_qtd, orc_valor, pro_descabv, pro_comissao
				FROM orcamento, produtos WHERE orc_prod = pro_cod AND orc_num = '$orc_num';";
				$query_orcitens = mysql_query($sql_orcitens,$conexao)or die("Erro na Busca do PV do Talao!");
				if(mysql_num_rows($query_orcitens) > 0){
				while ($linha_orcitens = mysql_fetch_object($query_orcitens)) {
				$totaldoitem = $linha_orcitens->orc_qtd * $linha_orcitens->orc_valor;
				$sql_incl   = "INSERT INTO pedmov
				(pm_num, pm_prod, pm_emp, pm_loja, pm_lojaloc, pm_cor, pm_escala,
				pm_progrupo, pm_es, pm_desc,pm_comple,pm_qtd,pm_valuni, pm_valtot,
				pm_incluido,pm_alterado,pm_dtincluido, pm_entregue,pm_comissao, pm_hora,pm_login)
				VALUES
				('$edtNumPed', '".$linha_orcitens->orc_prod."','$codemp', '$lstLoja', '$ljcod', '".$linha_orcitens->orc_escala2."',
				'".$linha_orcitens->orc_escala1."','".$linha_orcitens->orc_grupo."','S', '".$linha_orcitens->pro_descabv."',
				'$pro_comple','".$linha_orcitens->orc_qtd."', '".$linha_orcitens->orc_valor."','$totaldoitem', 'S', 'N','$data','N',
				'".$linha_orcitens->pro_comissao."', '$hora', '$acelogin')";
				//echo $sql_incl;
				$query_incl = mysql_query($sql_incl)or die("Erro na Conclusao do Item do Pedido ou Item do Pedido já Cadastrado!");
				}
				}
				//fim do gravando itens do orcamento no pv

				//atualizando seqpv no loja
				$sql_pvautomatico1 = "SELECT lj_pvautomatico FROM loja where lj_cod = '$ljcod';";
				$query_pvautomatico1 = mysql_query($sql_pvautomatico1,$conexao);
				$linha_pvautomatico1 = mysql_fetch_object($query_pvautomatico1);
				if ($linha_pvautomatico1->lj_pvautomatico == "S"){
				$sql_incloja = "update loja set lj_seqpv = lj_seqpv + 1 WHERE lj_cod = '$ljcod'";
				$query_incloja = mysql_query($sql_incloja) or die ("Erro!");
				}

				//atualizando taloespv
				$sql_updtp   = "UPDATE taloespv set tp_ativo = 'N'
				WHERE tp_numero = '$edtNumPed' AND tp_loja = '$ljcod'
				AND tp_vendedor = '$lstVend' AND tp_ativo = 'S';";
				$query_updtp = mysql_query($sql_updtp,$conexao)or die("Erro na Busca do PV do Talao!");

				} //if ($lstCli == ''){
				}else{
				$msg_ped = "<table bgcolor='#004000' bordercolor='#004000' border='1' align='center'>
				<tr><td align='center'><b><font size='3' color='#FFFF00'>
				Este deve ser o próximo Pedido de Venda a ser lançado por este vendedor: [ ".$linha_tpmin->tp_numero." ].<br>
				Siga a seqüência do talão, pois o Pedido de Venda [ ".$edtNumPed." ] não é o próximo!</font></b>
				</td></tr></table>";
				}
				}
				}else{
				$msg_ped = "<table bgcolor='#004000' bordercolor='#004000' border='1' align='center'>
				<tr><td align='center'><b><font size='3' color='#FFFF00'>
				Pedido de Venda [ ".$edtNumPed." ] não foi cadastrado pelo gerente para este vendedor!
				</font></b>
				</td></tr></table>";
				}
				}//fim do pvautomatico = s
				}
			
				// PRIMEIRO IF GRANDE
				} else { //final do else lj_liberatalao = s
				if ($lstCli == ''){
				$msg_ped = "<table bgcolor='#004000' bordercolor='#004000' border='1' align='center'>
				<tr><td align='center'><b><font size='3' color='#FFFF00'>
				Não pode gravar o cabeçalho do pedido sem escolher um cliente! Volte e refaça
				os procedimentos corretos!</font></b>
				</td></tr></table>";
				}else{ //if ($lstCli == ''){
				//inclui o item no banco pedcad 3
				$sql_incl = "INSERT INTO pedcad (ped_num,ped_emp,ped_loja,ped_cliente,ped_tipove,
				ped_emissao,ped_situacao,ped_dtsituacao,ped_vend,
				ped_incluido,ped_alterado,ped_dtincluido, ped_hora,
				ped_login, ped_orcamento)
				VALUES ('$edtNumPed', '$codemp', '$ljcod', '$lstCli', '0',
				'$edtEmissao','A','$data','$lstVend',
				'S', 'N','$data', '$hora','$acelogin','$orc_num')";
				$query_incl = mysql_query($sql_incl)or die("<table bgcolor='#004000' bordercolor='#004000'
				border='1' align='center'><tr><td align='center'><b><font size='3' color='#FFFF00'>Erro na
				Conclusão do Pedido ou
				Pedido já Cadastrado!</font></b></td></tr></table>");

				//gravando itens do orcamento no pv
				$sql_orcitens   = "SELECT DISTINCT orc_num, orc_prod, orc_escala1, orc_grupo,
				orc_escala2, orc_qtd, orc_valor, pro_descabv, pro_comissao
				FROM orcamento, produtos WHERE orc_prod = pro_cod
				AND orc_num = '$orc_num'";
				$query_orcitens = mysql_query($sql_orcitens,$conexao)or die("Erro na Busca do PV do
				Talao!");
				if(mysql_num_rows($query_orcitens) > 0){
				while ($linha_orcitens = mysql_fetch_object($query_orcitens)) {
				$totaldoitem = $linha_orcitens->orc_qtd * $linha_orcitens->orc_valor;
				$sql_incl   = "INSERT INTO pedmov
				(pm_num, pm_prod, pm_emp, pm_loja, pm_lojaloc, pm_cor, pm_escala,
				pm_progrupo, pm_es, pm_desc,pm_comple,pm_qtd,pm_valuni, pm_valtot,
				pm_incluido,pm_alterado,pm_dtincluido, pm_entregue,pm_comissao,
				pm_hora,pm_login)
				VALUES
				('$edtNumPed', '".$linha_orcitens->orc_prod."','$codemp', '$ljcod',
				'$ljcod', '".$linha_orcitens->orc_escala2."',
				'".$linha_orcitens->orc_escala1."','".$linha_orcitens->orc_grupo.
				"','S', '".$linha_orcitens->pro_descabv."',
				'$pro_comple','".$linha_orcitens->orc_qtd."', '".$linha_orcitens->
				orc_valor."','$totaldoitem', 'S', 'N','$data','N',
				'".$linha_orcitens->pro_comissao."', '$hora', '$acelogin')";
				//echo $sql_incl;
				$query_incl = mysql_query($sql_incl)or die("Erro na Conclusao do Item do Pedido ou
				Item do Pedido já Cadastrado!");
				}
				}
				//fim do gravando itens do orcamento no pv
				//atualizando seqpv no loja
				$sql_pvautomatico1 = "SELECT lj_pvautomatico FROM loja where lj_cod = '$ljcod';";
				$query_pvautomatico1 = mysql_query($sql_pvautomatico1,$conexao);
				$linha_pvautomatico1 = mysql_fetch_object($query_pvautomatico1);
				if ($linha_pvautomatico1->lj_pvautomatico == "S"){
				$sql_incloja = "update loja set lj_seqpv = lj_seqpv + 1 WHERE lj_cod = '$ljcod'";
				$query_incloja = mysql_query($sql_incloja) or die ("Erro!");
				}
				} //if ($lstCli == ''){
				} //final do lj_liberatalao = s
				//codigo novo termina aqui
				$trava = "disabled";
				$trava_list = "disabled";
				$incluir = 'n';
				$grav = 's';
				$trava_alt = "";
				$trava_cab = "disabled";
				$trava_ped = "readonly";
				} //incluir=s
				// FIM DO
				// INCLUIR = S
				//////////////////////////////////////////////
			
			
			
			
				/////////////////   2   /////////////////////////////
				//////INCLUIR_PROD = S
				//////ULTIMO IF DA PARTE DE PHP
				if($incluir_prod == "s"){
				// INICIO DO CODIGO PARA VERIFICAR O DESCONTO APLICADO NO PRODUTO
				//avarias escala 102, prog 22 cor 128    promocao prog 254 cor 51 exclusivo cor 1751    cor 1 fora de linha
				if (($lstProg == "22") || ($lstProg == "254") || ($lstEscala == "102") || ($lstCor == "128") ||
				($lstCor == "51") || ($lstCor == "1751")  || ($lstCor == "1") ){

				$descontomax = 0;
				}else{
				include("calculo_desconto.php");
				}
				
				//////////////////////////////////////////////
				/////SEGUNDO IF GRANDE
				if ($descontomax <= $edtValunit){ //pode lançar

				if ($pedcompra != "ok"){
				if ($lstLoja != "JAC"){
				if ($prod_estoque == "s"){
				$edtqtd = valor_mysql($edtqtd);
				$sql_estoqlj = "SELECT sal_cod,sal_loja,sal_cor,sal_escala,sal_estreal from saldos
				WHERE sal_cod = '$edtProd' AND sal_cor = '$lstCor' AND sal_progrupo = '$lstProg'
				AND sal_loja = '$ljcod' AND sal_escala = '$lstEscala'
				AND sal_estreal >= $edtqtd";
				$query_estoqlj = mysql_query($sql_estoqlj,$conexao);
				//CHECA ESTOQUE
				$sql_checaestoque   = "SELECT lj_checaestoque from loja where lj_cod = '$ljcod'";
				$query_checaestoque = mysql_query($sql_checaestoque,$conexao)or die("Erro!");
				$linha_checaestoque = mysql_fetch_object($query_checaestoque);

				if ($linha_checaestoque->lj_checaestoque == "S"){
					
					
				//////////////////////////////////////////////
				//////////OUTRO IF GRANDE
				if(mysql_num_rows($query_estoqlj) > 0){
				//ESTA COMENTADO POIS O METODO NÃO ESTA SENSO INCLUIDO NA PAGINA
				//$data = muda_data_en($data);
				// checando se produto já foi lançado.
				$sql_checa = "SELECT pm_num,pm_prod,pm_emp,pm_loja from pedmov
				WHERE pm_num = '$edtNumPed' AND pm_prod = '$edtProd'
				AND pm_cor = '$lstCor' AND pm_escala = '$lstEscala' AND pm_progrupo = '$lstProg'
				AND pm_emp = '$codemp' AND pm_loja = '$lstLoja'
				AND pm_es = 'S' AND pm_lojaloc = '$ljcod';";
				$query_checa = mysql_query($sql_checa,$conexao)or die("Erro na Veririfcação do Item");
				
				if(mysql_num_rows($query_checa) > 0){
				$trava = "disabled";
				$trava_list = "disabled";
				$incluir_prod = 'n';
				$edtProd 	  = "";
				$edtRef  	  = "";
				$edtNomeprod  = "";
				$edtValunit   = "";
				$edtTotal     = "";
				$edtqtd     = "";
				$trava_alt = "";
				$trava_cab = "disabled";
				$trava_ped = "readonly";
				$incluir_prod = "n";
				$msg_prod = "Erro: Item já Cadastrado!";
				}
				
				///////////INCLUIR_PROD = S
				if($incluir_prod == "s"){
				// pegando o desc e comple
				$sql_prod = "SELECT pro_descabv,pro_comple, pro_comissao from produtos where pro_cod = '$edtProd'";
				$query_prod = mysql_query($sql_prod,$conexao);
				if(mysql_num_rows($query_prod) > 0){
				$linha_prod    	 = mysql_fetch_object($query_prod);
				$pro_descabv  	  	 = $linha_prod->pro_descabv;
				$pro_comple	  	 = $linha_prod->pro_comple;
				$pro_comissao  	 = $linha_prod->pro_comissao;
				}
				//inclui o item no banco pedmov
				$edtValunit = valor_mysql($edtValunit);
				$edtTotal   = $edtqtd * $edtValunit;
				//$edtTotal   = valor_mysql($edtTotal);

				$sql_saldo   = "SELECT * from saldos
				where sal_cod = '$edtProd' AND sal_loja = '$lstLoja'
				AND sal_cor = '$lstCor' AND sal_escala = '$lstEscala'
				AND sal_progrupo = '$lstProg' AND sal_estreal >= '$edtqtd'";
				$query_saldo = mysql_query($sql_saldo)or die("Erro na Conclusao do Saldo!");

				if ($lstLoja != $ljcod) {
				if(mysql_num_rows($query_saldo) > 0){
				$linha_saldo = mysql_fetch_object($query_saldo);
				if ($incluirpc == "s"){
				$parapc  = " pm_pc, ";
				$valorpc = " 'S', "; //".$parapc."   ".$valorpc."
				}
				
				if ($linha_saldo->sal_estreal >= $edtqtd){
				$estoqueok = 'S';
				}else{
				$estoqueok = 'N';
				}

				$sql_prom   = "select * from promocoes where prom_prod = '$edtProd'
				AND prom_loja = '$lstLoja' AND prom_dtinicial <= '$data'
				AND prom_dtfinal >= '$data' ";
				$query_prom = mysql_query($sql_prom)or die("Erro");
				if(mysql_num_rows($query_prom) > 0){
				$linha_prom = mysql_fetch_object($query_prom);
				$pro_comissao = $linha_prom->prom_comissao;
				$pro_promocao = 'S';
				}

				if ($lstEscala != "ESC1"){
				if ($lstProg != "GRU1"){
				if ($lstCor != "ESC2"){
				if ($edtNomeprod != "Produto não encontrado"){
				if (($edtqtd == "") || ($edtqtd == "0") || ($edtProd == "")){
				$msg_prod = "Não foi escolhido quantidade para este produto e ele não será inserido!";
				}else{
				include("atualiza_pmfab.php");
				$sql_incl   = "INSERT INTO pedmov
				(pm_num, pm_prod, pm_emp, pm_loja, pm_lojaloc, pm_cor, pm_escala,
				pm_progrupo, pm_es, pm_desc,pm_comple,pm_qtd,pm_valuni, pm_valtot,
				".$parapc." pm_incluido,pm_alterado,pm_dtincluido,pm_dtalterado,
				pm_entregue,pm_comissao,pm_estoqueok,pm_promocao, pm_hora,pm_login, pm_fab)
				VALUES
				('$edtNumPed', '$edtProd','$codemp', '$lstLoja', '$ljcod', '$lstCor',
				'$lstEscala','$lstProg','S', '$pro_descabv','$pro_comple','$edtqtd',
				'$edtValunit','$edtTotal', ".$valorpc." 'S', 'N','$data','$data','N',
				'$pro_comissao','".$estoqueok."','$pro_promocao', '$hora',
				'$acelogin', '$fab')";
				$query_incl = mysql_query($sql_incl)or die("Erro na Conclusao do Item do Pedido ou Item do Pedido já Cadastrado!");
				}
				}else{
				$msg_prod = "Foi escolhido um produto que não existe e ele não será inserido!";
				}
				}else{
				$msg_prod = "Não foi escolhido a Escala 2 desse produto e ele não será inserido!";
				}
				}else{
				$msg_prod = "Não foi escolhido o Grupo desse produto e ele não será inserido!";
				}
				}else{
				$msg_prod = "Não foi escolhido a Escala 1 desse produto e ele não será inserido!";
				}

				}else{
				$msg_prod = "Produto escolhido não tem saldo para esta loja e não será inserido!";
				}
				}elseif ($lstLoja == $ljcod){
				if(mysql_num_rows($query_saldo) > 0){
				$linha_saldo = mysql_fetch_object($query_saldo);
				if ($incluirpc == "s"){
				$parapc  = " pm_pc, ";
				$valorpc = " 'S', ";
				}
				if ($linha_saldo->sal_estreal >= $edtqtd){
				$estoqueok = 'S';
				}else{
				$estoqueok = 'N';
				}

				$sql_prom   = "select * from promocoes where prom_prod = '$edtProd'
				AND prom_loja = '$lstLoja' AND prom_dtinicial <= '$data'
				AND prom_dtfinal >= '$data' ";
				$query_prom = mysql_query($sql_prom)or die("Erro");
				if(mysql_num_rows($query_prom) > 0){
				$linha_prom = mysql_fetch_object($query_prom);
				$pro_comissao = $linha_prom->prom_comissao;
				$pro_promocao = 'S';
				}

				if ($lstEscala != "ESC1"){
				if ($lstProg != "GRU1"){
				if ($lstCor != "ESC2"){
				if ($edtNomeprod != "Produto não encontrado"){
				if (($edtqtd == "") || ($edtqtd == "0") || ($edtProd == "")){
				$msg_prod = "Não foi escolhido quantidade para este produto e ele não será inserido!";
				}else{
				include("atualiza_pmfab.php");
				$sql_incl   = "INSERT INTO pedmov (pm_num,pm_prod,pm_emp,pm_loja,pm_lojaloc,pm_cor,pm_escala, pm_progrupo, pm_es,
				pm_desc,pm_comple,pm_qtd,pm_valuni,pm_valtot, ".$parapc."
				pm_incluido,pm_alterado,pm_dtincluido,pm_dtalterado,
				pm_entregue,pm_comissao,pm_estoqueok,pm_promocao, pm_hora,pm_login, pm_fab)
				VALUES ('$edtNumPed', '$edtProd','$codemp', '$lstLoja', '$ljcod','$lstCor','$lstEscala','$lstProg','S',
				'$pro_descabv*','$pro_comple','$edtqtd','$edtValunit','$edtTotal', ".$valorpc."
				'S', 'N','$data','$data','N','$pro_comissao','".$estoqueok."','$pro_promocao', '$hora','$acelogin', '$fab')";
				$query_incl = mysql_query($sql_incl)or die("Erro na Conclusao do Item do Pedido ou Item do Pedido já Cadastrado!");
				}
				}else{
				$msg_prod = "Foi escolhido um produto que não existe e ele não será inserido!";
				}
				}else{
				$msg_prod = "Não foi escolhido a Escala 2 desse produto e ele não será inserido!";
				}
				}else{
				$msg_prod = "Não foi escolhido o Grupo desse produto e ele não será inserido!";
				}
				}else{
				$msg_prod = "Não foi escolhido a Escala 1 desse produto e ele não será inserido!";
				}
				}
				}
				$trava = "disabled";
				$trava_list = "disabled";
				$incluir_prod = 'n';
				$edtProd 	  = "";
				$edtRef  	  = "";
				$edtNomeprod  = "";
				$edtValunit   = "";
				$edtTotal     = "";
				$edtqtd     = "";
				$trava_alt = "";
				$trava_cab = "disabled";
				$trava_ped = "readonly";
				}	//fim do incluirprod=s
				//////////////////////////////////////////////
			
				//////////////////////////////////////////////
				///////FIM DO OUTRO IF GRANDE
				}else{
				$pro_det = "ok";
				$sql_loja = "select lj_fantasia from loja where lj_cod = '$lstLoja'";
				$query_loja = mysql_query($sql_loja,$conexao);
				$linha_loja = mysql_fetch_object($query_loja);
				$msg_estoq  = "Produto: ".$edtProd." não possui estoque na Loja: ".$lstLoja." - ".$linha_loja->lj_fantasia."!";
				$msg_estoq2 = "<a href=\"#\" onClick=\"submit_action('pedido_cad.php?incluir_prod=s&campo=formpedido.edtProd&produtos=ok&trava=disabled&trava_list=disabled&edtNumPed=$edtNumPed&edtSit=$edtSit&lstLoja=$lstLoja&menuoff=".$menuoff."')\">Clique Aqui 1</a> para Voltar.";
				$trava_alt = "";
				$trava_cab = "disabled";
				$trava_ped = "readonly";

				if ($lstLoja != $ljcod) {
				$data = muda_data_en($data);
				// checando se produto já foi lançado.
				$sql_checa = "SELECT pm_num,pm_prod,pm_emp,pm_loja from pedmov
				WHERE pm_num = '$edtNumPed' AND pm_prod = '$edtProd'
				AND pm_cor = '$lstCor' AND pm_escala = '$lstEscala' AND pm_progrupo = '$lstProg'
				AND pm_emp = '$codemp' AND pm_loja = '$lstLoja'
				AND pm_es = 'S' AND pm_lojaloc = '$ljcod';";
				$query_checa = mysql_query($sql_checa,$conexao)or die("Erro na Veririfcação do Item");
				if(mysql_num_rows($query_checa) > 0){
				$trava = "disabled";
				$trava_list = "disabled";
				$incluir_prod = 'n';
				$edtProd 	  = "";
				$edtRef  	  = "";
				$edtNomeprod  = "";
				$edtValunit   = "";
				$edtTotal     = "";
				$edtqtd     = "";
				$trava_alt = "";
				$trava_cab = "disabled";
				$trava_ped = "readonly";
				$incluir_prod = "n";
				$msg_prod = "Erro: Item já Cadastrado!";
				}

				if($incluir_prod == "s"){
				// pegando o desc e comple
				$sql_prod = "SELECT pro_descabv,pro_comple, pro_comissao from produtos where pro_cod = '$edtProd'";
				$query_prod = mysql_query($sql_prod,$conexao);
				if(mysql_num_rows($query_prod) > 0){
				$linha_prod    	 = mysql_fetch_object($query_prod);
				$pro_descabv  	  	 = $linha_prod->pro_descabv;
				$pro_comple	  	 = $linha_prod->pro_comple;
				$pro_comissao  	 = $linha_prod->pro_comissao;
				}
				//inclui o item no banco pedmov
				$edtValunit = valor_mysql($edtValunit);
				$edtTotal   = $edtqtd * $edtValunit;
				//$edtTotal   = valor_mysql($edtTotal);

				$sql_saldo   = "SELECT * from saldos
				where sal_cod = '$edtProd' AND sal_loja = '$lstLoja'
				AND sal_cor = '$lstCor' AND sal_escala = '$lstEscala'
				AND sal_progrupo = '$lstProg' AND sal_estreal >= '$edtqtd'";
				$query_saldo = mysql_query($sql_saldo)or die("Erro na Conclusao do Saldo!");

				if ($lstLoja != $ljcod) {
				if(mysql_num_rows($query_saldo) > 0){
				$linha_saldo = mysql_fetch_object($query_saldo);
				if ($incluirpc == "s"){
				$parapc  = " pm_pc, ";
				$valorpc = " 'S', "; //".$parapc."   ".$valorpc."
				}
				if ($linha_saldo->sal_estreal >= $edtqtd){
				$estoqueok = 'S';
				}else{
				$estoqueok = 'N';
				}

				$sql_prom   = "select * from promocoes where prom_prod = '$edtProd'
				AND prom_loja = '$lstLoja' AND prom_dtinicial <= '$data'
				AND prom_dtfinal >= '$data' ";
				$query_prom = mysql_query($sql_prom)or die("Erro");
				if(mysql_num_rows($query_prom) > 0){
				$linha_prom = mysql_fetch_object($query_prom);
				$pro_comissao = $linha_prom->prom_comissao;
				$pro_promocao = 'S';
				}

				if ($lstEscala != "ESC1"){
				if ($lstProg != "GRU1"){
				if ($lstCor != "ESC2"){
				if ($edtNomeprod != "Produto não encontrado"){
				if (($edtqtd == "") || ($edtqtd == "0") || ($edtProd == "")){
				$msg_prod = "Não foi escolhido quantidade para este produto e ele não será inserido!";
				}else{
				include("atualiza_pmfab.php");
				$sql_incl   = "INSERT INTO pedmov (pm_num,pm_prod,pm_emp,pm_loja,pm_lojaloc,pm_cor,pm_escala, pm_progrupo, pm_es,
				pm_desc,pm_comple,pm_qtd,pm_valuni,pm_valtot, ".$parapc."
				pm_incluido,pm_alterado,pm_dtincluido,pm_dtalterado, pm_entregue,pm_comissao,pm_estoqueok,pm_promocao, pm_hora,pm_login, pm_fab)
				VALUES ('$edtNumPed', '$edtProd','$codemp', '$lstLoja','$ljcod', '$lstCor','$lstEscala','$lstProg','S',
				'$pro_descabv*','$pro_comple','$edtqtd','$edtValunit','$edtTotal', ".$valorpc."
				'S', 'N','$data','$data','N','$pro_comissao','".$estoqueok."','$pro_promocao','$hora','$acelogin', '$fab')";
				$query_incl = mysql_query($sql_incl)or die("Erro na Conclusao do Item do Pedido ou Item do Pedido já Cadastrado!");
				}
				}else{
				$msg_prod = "Foi escolhido um produto que não existe e ele não será inserido!";
				}
				}else{
				$msg_prod = "Não foi escolhido a Escala 2 desse produto e ele não será inserido!";
				}
				}else{
				$msg_prod = "Não foi escolhido o Grupo desse produto e ele não será inserido!";
				}
				}else{
				$msg_prod = "Não foi escolhido a Escala 1 desse produto e ele não será inserido!";
				}
				}else{
				$msg_prod = "Produto escolhido não tem saldo para esta loja e não será inserido!";
				}
				}elseif ($lstLoja == $ljcod){
				if(mysql_num_rows($query_saldo) > 0){
				$linha_saldo = mysql_fetch_object($query_saldo);
				if ($incluirpc == "s"){
				$parapc  = " pm_pc, ";
				$valorpc = " 'S', "; //".$parapc."   ".$valorpc."
				}
				if ($linha_saldo->sal_estreal >= $edtqtd){
				$estoqueok = 'S';
				}else{
				$estoqueok = 'N';
				}

				$sql_prom   = "select * from promocoes where prom_prod = '$edtProd'
				AND prom_loja = '$lstLoja' AND prom_dtinicial <= '$data'
				AND prom_dtfinal >= '$data' ";
				$query_prom = mysql_query($sql_prom)or die("Erro");
				if(mysql_num_rows($query_prom) > 0){
				$linha_prom = mysql_fetch_object($query_prom);
				$pro_comissao = $linha_prom->prom_comissao;
				$pro_promocao = 'S';
				}

				if ($lstEscala != "ESC1"){
				if ($lstProg != "GRU1"){
				if ($lstCor != "ESC2"){
				if ($edtNomeprod != "Produto não encontrado"){
				if (($edtqtd == "") || ($edtqtd == "0") || ($edtProd == "")){
				$msg_prod = "Não foi escolhido quantidade para este produto e ele não será inserido!";
				}else{
				include("atualiza_pmfab.php");
				$sql_incl   = "INSERT INTO pedmov (pm_num,pm_prod,pm_emp,pm_loja,pm_lojaloc, pm_cor,pm_escala, pm_progrupo, pm_es,
				pm_desc,pm_comple,pm_qtd,pm_valuni,pm_valtot, ".$parapc."
				pm_incluido,pm_alterado,pm_dtincluido,pm_dtalterado, pm_entregue,pm_comissao,pm_estoqueok,pm_promocao, pm_hora, pm_login, pm_fab)
				VALUES ('$edtNumPed', '$edtProd','$codemp', '$lstLoja', '$ljcod', '$lstCor','$lstEscala','$lstProg','S',
			'	$pro_descabv*','$pro_comple','$edtqtd','$edtValunit','$edtTotal', ".$valorpc."
				'S', 'N','$data','$data','N','$pro_comissao','".$estoqueok."','$pro_promocao','$hora','$acelogin', '$fab')";
				$query_incl = mysql_query($sql_incl)or die("Erro na Conclusao do Item do Pedido ou Item do Pedido já Cadastrado!");
				}
				}else{
				$msg_prod = "Foi escolhido um produto que não existe e ele não será inserido!";
				}
				}else{
				$msg_prod = "Não foi escolhido a Escala 2 desse produto e ele não será inserido!";
				}
				}else{
				$msg_prod = "Não foi escolhido o Grupo desse produto e ele não será inserido!";
				}
				}else{
				$msg_prod = "Não foi escolhido a Escala 1 desse produto e ele não será inserido!";
				}
				}
				}
				$trava = "disabled";
				$trava_list = "disabled";
				$incluir_prod = 'n';
				$edtProd 	  = "";
				$edtRef  	  = "";
				$edtNomeprod  = "";
				$edtValunit   = "";
				$edtTotal     = "";
				$edtqtd     = "";
				$trava_alt = "";
				$trava_cab = "disabled";
				$trava_ped = "readonly";
				}
				}
				} //fim do else mysqlnumrows do query_estoqlj
				} //fim do lj_checaestoque
				elseif ($linha_checaestoque->lj_checaestoque == "N") { //se  checaestoque = N
				$data = muda_data_en($data);
				// checando se produto já foi lançado.
				$sql_checa = "SELECT pm_num,pm_prod,pm_emp,pm_loja from pedmov
				WHERE pm_num = '$edtNumPed' AND pm_prod = '$edtProd'
				AND pm_cor = '$lstCor' AND pm_escala = '$lstEscala' AND pm_progrupo = '$lstProg'
				AND pm_emp = '$codemp' AND pm_loja = '$lstLoja'
				AND pm_es = 'S';";
				$query_checa = mysql_query($sql_checa,$conexao)or die("Erro na Verificação do Item");
				if(mysql_num_rows($query_checa) > 0){
				$trava = "disabled";
				$trava_list = "disabled";
				$incluir_prod = 'n';
				$edtProd 	  = "";
				$edtRef  	  = "";
				$edtNomeprod  = "";
				$edtValunit   = "";
				$edtTotal     = "";
				$edtqtd     = "";
				$trava_alt = "";
				$trava_cab = "disabled";
				$trava_ped = "readonly";
				$incluir_prod = "n";
				$msg_prod = "Erro: Item já Cadastrado!";
				}else{
				if($incluir_prod == "s"){
				// pegando o desc e comple
				$sql_prod = "SELECT pro_descabv,pro_comple, pro_comissao from produtos where
				pro_cod = '$edtProd'";
				$query_prod = mysql_query($sql_prod,$conexao);
				if(mysql_num_rows($query_prod) > 0){
				$linha_prod    	 = mysql_fetch_object($query_prod);
				$pro_descabv  	  	 = $linha_prod->pro_descabv;
				$pro_comple	  	 = $linha_prod->pro_comple;
				$pro_comissao  	 = $linha_prod->pro_comissao;
				}
				//inclui o item no banco pedmov
				$edtValunit = valor_mysql($edtValunit);
				$edtTotal   = $edtqtd * $edtValunit;
				$sql_saldo   = "SELECT * from saldos
				where sal_cod = '$edtProd' AND sal_loja = '$lstLoja'
				AND sal_cor = '$lstCor' AND sal_escala = '$lstEscala'
				AND sal_progrupo = '$lstProg' AND sal_estreal >= '$edtqtd'";
				$query_saldo = mysql_query($sql_saldo)or die("Erro na Conclusao do Saldo!");
				if ($lstLoja != $ljcod) {
				if(mysql_num_rows($query_saldo) > 0){
				$linha_saldo = mysql_fetch_object($query_saldo);
				if ($incluirpc == "s"){
				$parapc  = " pm_pc, ";
				$valorpc = " 'S', "; //".$parapc."   ".$valorpc."
				}
				if ($linha_saldo->sal_estreal >= $edtqtd){
				$estoqueok = 'S';
				}else{
				$estoqueok = 'N';
				}

				$sql_prom   = "select * from promocoes where prom_prod = '$edtProd'
				AND prom_loja = '$lstLoja' AND prom_dtinicial <= '$data'
				AND prom_dtfinal >= '$data' ";
				$query_prom = mysql_query($sql_prom)or die("Erro");
				if(mysql_num_rows($query_prom) > 0){
				$linha_prom = mysql_fetch_object($query_prom);
				$pro_comissao = $linha_prom->prom_comissao;
				$pro_promocao = 'S';
				}

				if ($lstEscala != "ESC1"){
				if ($lstProg != "GRU1"){
				if ($lstCor != "ESC2"){
				if ($edtNomeprod != "Produto não encontrado"){
				if (($edtqtd == "") || ($edtqtd == "0") || ($edtProd == "")){
				$msg_prod = "Não foi escolhido quantidade para este produto e ele não será inserido!";
				}else{
				include("atualiza_pmfab.php");

				$sql_incl   = "INSERT INTO pedmov (pm_num,pm_prod,pm_emp,pm_loja,pm_lojaloc,pm_cor,pm_escala, pm_progrupo, pm_es,
				pm_desc,pm_comple,pm_qtd,pm_valuni,pm_valtot, ".$parapc."
				pm_incluido,pm_alterado,pm_dtincluido,pm_dtalterado, pm_entregue,pm_comissao,pm_estoqueok,pm_promocao, pm_hora, pm_login, pm_fab)
				VALUES ('$edtNumPed', '$edtProd','$codemp', '$lstLoja', '$ljcod', '$lstCor','$lstEscala','$lstProg','S',
				'$pro_descabv*','$pro_comple','$edtqtd','$edtValunit','$edtTotal', ".$valorpc."
				'S', 'N','$data','$data','N','$pro_comissao','".$estoqueok."','$pro_promocao','$hora','$acelogin', '$fab')";
				$query_incl = mysql_query($sql_incl)or die("Erro na Conclusao do Item do Pedido ou Item do Pedido já Cadastrado!");
				}
				}else{
				$msg_prod = "Foi escolhido um produto que não existe e ele não será inserido!";
				}
				}else{
				$msg_prod = "Não foi escolhido a Escala 2 desse produto e ele não será inserido!";
				}
				}else{
				$msg_prod = "Não foi escolhido o Grupo desse produto e ele não será inserido!";
				}
				}else{
				$msg_prod = "Não foi escolhido a Escala 1 desse produto e ele não será inserido!";
				}
				}else{
				$msg_prod = "Produto escolhido não tem saldo para esta loja e não será inserido!";
				}
				}elseif ($lstLoja == $ljcod){
				if(mysql_num_rows($query_saldo) > 0){
				$linha_saldo = mysql_fetch_object($query_saldo);
	
				if ($linha_saldo->sal_estreal >= $edtqtd){
				$estoqueok = 'S';
				}else{
				$estoqueok = 'N';
				}
				}
				if ($incluirpc == "s"){
				$parapc  = " pm_pc, ";
				$valorpc = " 'S', "; //".$parapc."   ".$valorpc."
				}

				$sql_prom   = "select * from promocoes where prom_prod = '$edtProd'
				AND prom_loja = '$lstLoja' AND prom_dtinicial <= '$data'
				AND prom_dtfinal >= '$data' ";
				$query_prom = mysql_query($sql_prom)or die("Erro");
				if(mysql_num_rows($query_prom) > 0){
				$linha_prom = mysql_fetch_object($query_prom);
				$pro_comissao = $linha_prom->prom_comissao;
				$pro_promocao = 'S';
				}

				if ($lstEscala != "ESC1"){
				if ($lstProg != "GRU1"){
				if ($lstCor != "ESC2"){
				if ($edtNomeprod != "Produto não encontrado"){
				if (($edtqtd == "") || ($edtqtd == "0") || ($edtProd == "")){
				$msg_prod = "Não foi escolhido quantidade para este produto e ele não será inserido!";
				}else{
				include("atualiza_pmfab.php");

				$sql_incl   = "INSERT INTO pedmov (pm_num,pm_prod,pm_emp,pm_loja,pm_lojaloc,pm_cor,pm_escala, pm_progrupo, pm_es,
				pm_desc,pm_comple,pm_qtd,pm_valuni,pm_valtot, ".$parapc."
				pm_incluido,pm_alterado,pm_dtincluido,pm_dtalterado, pm_entregue,pm_comissao,pm_estoqueok,pm_promocao, pm_hora, pm_login, pm_fab)
				VALUES ('$edtNumPed', '$edtProd','$codemp', '$lstLoja', '$ljcod', '$lstCor','$lstEscala','$lstProg','S',
				'$pro_descabv*','$pro_comple','$edtqtd','$edtValunit','$edtTotal', ".$valorpc."
				'S', 'N','$data','$data','N','$pro_comissao','".$estoqueok."','$pro_promocao','$hora','$acelogin', '$fab')";
				$query_incl = mysql_query($sql_incl)or die("Erro na Conclusao do Item do Pedido ou Item do Pedido já Cadastrado!");
				}
				}else{
				$msg_prod = "Foi escolhido um produto que não existe e ele não será inserido!";
				}
				}else{
				$msg_prod = "Não foi escolhido a Escala 2 desse produto e ele não será inserido!";
				}
				}else{
				$msg_prod = "Não foi escolhido o Grupo desse produto e ele não será inserido!";
				}
				}else{
				$msg_prod = "Não foi escolhido a Escala 1 desse produto e ele não será inserido!";
				}
				}
				$sql_estoqlj = "SELECT sal_cod,sal_loja,sal_cor,sal_escala,sal_estreal from saldos
				WHERE sal_cod = '$edtProd' AND sal_cor = '$lstCor' AND sal_progrupo = '$lstProg'
				AND sal_loja = '$ljcod' AND sal_escala = '$lstEscala'
				AND sal_estreal >= $edtqtd;";
				$query_estoqlj = mysql_query($sql_estoqlj,$conexao);
				//                                    echo $sql_estoqlj;
				$linha_estoqlj = mysql_fetch_object($query_estoqlj);

				if(mysql_num_rows($query_estoqlj) > 0){
				$trava = "disabled";
				$trava_list = "disabled";
				$incluir_prod = 'n';
				$trava_alt = "";
				$trava_cab = "disabled";
				$trava_ped = "readonly";
				$incluir_prod = "n";
				$edtqtd  = '';  $edtValunit = ''; $edtTotal    = '';
				}else{
				$edtqtd = valor_mysql($edtqtd);
				//										$edtqtd = 0 - $edtqtd;
				$edtqtd = valor_mysql($edtqtd)/100;
				}
				$trava = "disabled";
				$trava_list = "disabled";
				$incluir_prod = 'n';
				}//fim
				}
				}//fim do else lj_checaestoque
				} //fim do if prod_estoque = s
				else{
				$sql_prom   = "select * from promocoes where prom_prod = '$edtProd'
				AND prom_loja = '$lstLoja' AND prom_dtinicial <= '$data'
				AND prom_dtfinal >= '$data' ";
				$query_prom = mysql_query($sql_prom)or die("Erro");
				if(mysql_num_rows($query_prom) > 0){
				$linha_prom = mysql_fetch_object($query_prom);
				$pro_comissao = $linha_prom->prom_comissao;
				$pro_promocao = 'S';
				}

				$data = muda_data_en($data);

				if ($lstEscala != "ESC1"){
				if ($lstProg != "GRU1"){
				if ($lstCor != "ESC2"){
				if ($edtNomeprod != "Produto não encontrado"){
				if (($edtqtd == "") || ($edtqtd == "0") || ($edtProd == "")){
				$msg_prod = "Não foi escolhido quantidade para este produto e ele não será inserido!";
				}else{
				include("atualiza_pmfab.php");
				$sql_incl   = "INSERT INTO pedmov (pm_num,pm_prod,pm_emp,pm_loja,pm_lojaloc,pm_cor,pm_escala, pm_progrupo, pm_es,
				pm_desc,pm_comple,pm_qtd,pm_valuni,pm_valtot, ".$parapc."
				pm_incluido,pm_alterado,pm_dtincluido,pm_dtalterado, pm_entregue,pm_comissao,pm_estoqueok,pm_promocao,pm_hora,pm_login, pm_fab)
				VALUES ('$edtNumPed', '$edtProd','$codemp', '$lstLoja', '$ljcod', '$lstCor','$lstEscala','$lstProg','S',
				'$pro_descabv*','$pro_comple','$edtqtd','$edtValunit','$edtTotal', ".$valorpc."
				'S', 'N','$data','$data','N','$pro_comissao','".$estoqueok."','$pro_promocao', '$hora','$acelogin', '$fab')";
				$query_incl = mysql_query($sql_incl)or die("Erro na Conclusao do Item do Pedido ou Item do Pedido já Cadastrado!");
				}
				}else{
				$msg_prod = "Foi escolhido um produto que não existe e ele não será inserido!";
				}
				}else{
				$msg_prod = "Não foi escolhido a Escala 2 desse produto e ele não será inserido!";
				}
				}else{
				$msg_prod = "Não foi escolhido o Grupo desse produto e ele não será inserido!";
				}
				}else{
				$msg_prod = "Não foi escolhido a Escala 1 desse produto e ele não será inserido!";
				}
				}
				}elseif($lstLoja == "JAC"){
				$edtqtd = valor_mysql($edtqtd);
				$sql_estoqlj = "SELECT sal_cod,sal_loja,sal_cor,sal_escala,sal_estreal from saldos
				WHERE sal_cod = '$edtProd' AND sal_cor = '$lstCor' AND sal_progrupo = '$lstProg'
				AND sal_loja = '$lstLoja' AND sal_escala = '$lstEscala'
				AND sal_estreal < $edtqtd ;";
				$query_estoqlj = mysql_query($sql_estoqlj,$conexao);
				if(mysql_num_rows($query_estoqlj) > 0){
				$trava = "disabled";
				$trava_list = "disabled";
				$incluir_prod = 'n';
				$trava_alt = "";
				$trava_cab = "disabled";
				$trava_ped = "readonly";
				$incluir_prod = "n";
				$msg_prod = "Erro: A Loja Nao Pode Fazer esta Reserva Saldo Insuficiente!";
				}
				$sql_checa = "SELECT pm_num,pm_prod,pm_emp,pm_loja from pedmov
				WHERE pm_num = '$edtNumPed' AND pm_prod = '$edtProd'
				AND pm_cor = '$lstCor' AND pm_escala = '$lstEscala' AND pm_progrupo = '$lstProg'
				AND pm_emp = '$codemp' AND pm_loja = '$lstLoja'
				AND pm_es = 'S';";
				$query_checa = mysql_query($sql_checa,$conexao)or die("Erro na Veririfcação do Item");
				if(mysql_num_rows($query_checa) > 0){
				$trava = "disabled";
				$trava_list = "disabled";
				$incluir_prod = 'n';
				$trava_alt = "";
				$trava_cab = "disabled";
				$trava_ped = "readonly";
				$incluir_prod = "n";
				$msg_prod = "Erro: Item já Cadastrado!";
				}
				$sql_reserva = "SELECT DISTINCT res_cod,res_prod,res_ljdestino,lj_fantasia,res_cor,res_escala,res_progrupo,res_pedido
				FROM reserva,loja
				WHERE res_loja = lj_cod AND res_prod = '$edtProd' AND res_cor = '$lstCor'
				AND res_ljdestino = '$ljcod' AND res_escala = '$lstEscala' AND res_progrupo = '$lstProg'
				AND res_qtd = $edtqtd AND res_situacao = 'A';";
				$query_reserva = mysql_query($sql_reserva,$conexao)or die("Erro na consulta da Reserva!");
				if(mysql_num_rows($query_reserva) > 0){
				if($incluir_prod == "s"){
				// pegando o desc e comple
				$sql_prod = "SELECT pro_descabv,pro_comple, pro_comissao from produtos where pro_cod = '$edtProd'";
				$query_prod = mysql_query($sql_prod,$conexao);
				if(mysql_num_rows($query_prod) > 0){
				$linha_prod    	 = mysql_fetch_object($query_prod);
				$pro_descabv  	  	 = $linha_prod->pro_descabv;
				$pro_comple	  	 = $linha_prod->pro_comple;
				$pro_comissao 	 = $linha_prod->pro_comissao;
				}
				//inclui o item no banco pedmov
				$edtValunit = valor_mysql($edtValunit);
				$edtTotal   = $edtqtd * $edtValunit;
				//$edtTotal   = valor_mysql($edtTotal);
	
				if ($lstLoja != $ljcod) {
				$sql_saldo   = "SELECT * from saldos
				where sal_cod = '$edtProd' AND sal_loja = '$lstLoja'
				AND sal_cor = '$lstCor' AND sal_escala = '$lstEscala'
				AND sal_progrupo = '$lstProg' AND sal_estreal >= '$edtqtd'";
				$query_saldo = mysql_query($sql_saldo)or die("Erro na Conclusao do Saldo!");
				if(mysql_num_rows($query_saldo) > 0){
				$linha_saldo = mysql_fetch_object($query_saldo);
	
				if ($incluirpc == "s"){
				$parapc  = " pm_pc, ";
				$valorpc = " 'S', "; //".$parapc."   ".$valorpc."
				}
				if ($linha_saldo->sal_estreal >= $edtqtd){
				$estoqueok = 'S';
				}else{
				$estoqueok = 'N';
				}

				$sql_prom   = "select * from promocoes where prom_prod = '$edtProd'
				AND prom_loja = '$lstLoja' AND prom_dtinicial <= '$data'
				AND prom_dtfinal >= '$data' ";
				$query_prom = mysql_query($sql_prom)or die("Erro");
				if(mysql_num_rows($query_prom) > 0){
				$linha_prom = mysql_fetch_object($query_prom);
				$pro_comissao = $linha_prom->prom_comissao;
				$pro_promocao = 'S';
				}

				if ($lstEscala != "ESC1"){
				if ($lstProg != "GRU1"){
				if ($lstCor != "ESC2"){
				if ($edtNomeprod != "Produto não encontrado"){
				if (($edtqtd == "") || ($edtqtd == "0") || ($edtProd == "")){
				$msg_prod = "Não foi escolhido quantidade para este produto e ele não será inserido!";
				}else{
				include("atualiza_pmfab.php");
				$sql_incl   = "INSERT INTO pedmov (pm_num,pm_prod,pm_emp,pm_loja,pm_lojaloc,pm_cor,pm_escala, pm_progrupo, pm_es,
				pm_desc,pm_comple,pm_qtd,pm_valuni,pm_valtot, ".$parapc."
				pm_incluido,pm_alterado,pm_dtincluido,pm_dtalterado, pm_entregue,pm_comissao,pm_estoqueok,pm_promocao, pm_hora, pm_login, pm_fab)
				VALUES ('$edtNumPed', '$edtProd','$codemp', '$lstLoja', '$ljcod', '$lstCor','$lstEscala','$lstProg','S',
				'$pro_descabv*','$pro_comple','$edtqtd','$edtValunit','$edtTotal', ".$valorpc."
				'S', 'N','$data','$data','N','$pro_comissao','".$estoqueok."','$pro_promocao','$hora','$acelogin', '$fab')";
				$query_incl = mysql_query($sql_incl)or die("Erro na Conclusao do Item do Pedido ou Item do Pedido já Cadastrado!");
				}
				}else{
				$msg_prod = "Foi escolhido um produto que não existe e ele não será inserido!";
				}
				}else{
				$msg_prod = "Não foi escolhido a Escala 2 desse produto e ele não será inserido!";
				}
				}else{
				$msg_prod = "Não foi escolhido o Grupo desse produto e ele não será inserido!";
				}
				}else{
				$msg_prod = "Não foi escolhido a Escala 1 desse produto e ele não será inserido!";
				}
				}else{
				$msg_prod = "Produto escolhido não tem saldo para esta loja e não será inserido!";
				}
				}elseif ($lstLoja == $ljcod){
				if(mysql_num_rows($query_saldo) > 0){
				$linha_saldo = mysql_fetch_object($query_saldo);
				if ($incluirpc == "s"){
				$parapc  = " pm_pc, ";
				$valorpc = " 'S', "; //".$parapc."   ".$valorpc."
				}
				if ($linha_saldo->sal_estreal >= $edtqtd){
				$estoqueok = 'S';
				}else{
				$estoqueok = 'N';
				}
	
				$sql_prom   = "select * from promocoes where prom_prod = '$edtProd'
				AND prom_loja = '$lstLoja' AND prom_dtinicial <= '$data'
				AND prom_dtfinal >= '$data' ";
				$query_prom = mysql_query($sql_prom)or die("Erro");
				if(mysql_num_rows($query_prom) > 0){
				$linha_prom = mysql_fetch_object($query_prom);
				$pro_comissao = $linha_prom->prom_comissao;
				$pro_promocao = 'S';
				}
	
				if ($lstEscala != "ESC1"){
				if ($lstProg != "GRU1"){
				if ($lstCor != "ESC2"){
				if ($edtNomeprod != "Produto não encontrado"){
				if (($edtqtd == "") || ($edtqtd == "0") || ($edtProd == "")){
				$msg_prod = "Não foi escolhido quantidade para este produto e ele não será inserido!";
				}else{
				include("atualiza_pmfab.php");
				$sql_incl   = "INSERT INTO pedmov (pm_num,pm_prod,pm_emp,pm_loja,pm_lojaloc,pm_cor,pm_escala, pm_progrupo, pm_es,
				pm_desc,pm_comple,pm_qtd,pm_valuni,pm_valtot, ".$parapc."
				pm_incluido,pm_alterado,pm_dtincluido,pm_dtalterado, pm_entregue,pm_comissao,pm_estoqueok,pm_promocao, pm_hora, pm_login, pm_fab)
				VALUES ('$edtNumPed', '$edtProd','$codemp', '$lstLoja', '$ljcod', '$lstCor','$lstEscala','$lstProg','S',
				'$pro_descabv*','$pro_comple','$edtqtd','$edtValunit','$edtTotal', ".$valorpc."
				'S', 'N','$data','$data','N','$pro_comissao','".$estoqueok."','$pro_promocao','$hora','$acelogin', '$fab')";
				$query_incl = mysql_query($sql_incl)or die("Erro na Conclusao do Item do Pedido ou Item do Pedido já Cadastrado!");
				}
				}else{
				$msg_prod = "Foi escolhido um produto que não existe e ele não será inserido!";
				}
				}else{
				$msg_prod = "Não foi escolhido a Escala 2 desse produto e ele não será inserido!";
				}
				}else{
				$msg_prod = "Não foi escolhido o Grupo desse produto e ele não será inserido!";
				}
				}else{
				$msg_prod = "Não foi escolhido a Escala 1 desse produto e ele não será inserido!";
				}
				}
				}
				$trava = "disabled";
				$trava_list = "disabled";
				$incluir_prod = 'n';
				$edtProd 	  = "";
				$edtRef  	  = "";
				$edtNomeprod  = "";
				$edtValunit   = "";
				$edtTotal     = "";
				$edtqtd     = "";
				$trava_alt = "";
				$trava_cab = "disabled";
				$trava_ped = "readonly";
				}
				}else{
				$msg_prod = "Nao Existe Nenhuma Reserva Para este Produto!";
				$msg_estoq3 = "<a href=\"#\" onClick=\"Javascript: history.back()\">Clique Aqui 3</a> para Voltar.";
				}
				} //fim else da query lstLoja = ljcod
				} //fim da flag pedcompra !=ok
				
				//////////////////////////////////////////////
				////FIM DO SEGUNDO IF GRANDE DESTA PARTE
				}else{  // FIM DO CODIGO PARA VERIFICAR O DESCONTO APLICADO NO PRODUTO
				$msg_prod = "Preço do Produto escolhido (R$ ".number_format($edtValunit,'2',',','.').") está abaixo do valor mínimo de venda.<br>Preço de Tabela: (R$ ".number_format($Valunit,'2',',','.')."). Preço Mínimo para venda: (R$ ".number_format(ceil($descontomax),'2',',','.').")! Digite outro valor!";
				}
				}//fim da flag incluir_prod=s
				////////FIM DO INCLUIR_PROD = S
				///////////////////   2   ///////////////////////////
			
			
			
			
			
				//////////////////////////////////////////////
				if ($edtProd != ""){
				$edtProd = '';  $edtRef     = ''; $edtNomeprod = '';
				$lstCor  = '';  $lstEscala  = ''; $lstProg     = '';
				$edtqtd  = '';  $edtValunit = ''; $edtTotal    = '';
				}//////////////////////////////////////////////

			
				//////////////////////////////////////////////
				if ($formcod_ck != "") {
				setcookie("formcod_ck","",time()-6);
				setcookie("formdesc_ck","",time()-6);
				setcookie("formref_ck","",time()-6);
				setcookie("formesc_ck","",time()-6);
				setcookie("formcor_ck","",time()-6);
				setcookie("formprog_ck","",time()-6);
				setcookie("formloja_ck","",time()-6);
				}//////////////////////////////////////////////
			
			
			
				// ULTIMO FECHAMENTO DA PARTE POST DE CIMA
			}/////////////////// 1 ///////////////////////////
			//fim do request post
		?>
<html>
<head>
<style type="text/css">
 <!--
 input {
  background-color: #B0E0E6;
  font: 10px verdana, arial, helvetica, sans-serif;
  color:#003399;
  border:1px solid #000099;
 }
-->
</style>
<link rel="stylesheet" href="est_big.css" type="text/css">
<title>:: gercom.NET - Pedido de Venda ::</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
 	<link rel="stylesheet" type="text/css" href="autocomplete/jquery.autocomplete.css" />
 	<link rel="stylesheet" type="text/css" href="autocomplete/estilomodal.css" />

	<script type="text/javascript" src="autocomplete/jquery.js"></script>
 	<script type="text/javascript" src="autocomplete/jquery.price_format.1.7.js"></script> 
 	<script type='text/javascript' src="autocomplete/jquery.autocomplete.js"></script>
 	<script type='text/javascript' src="autocomplete/functiontest.js"></script>
 	<script type='text/javascript' src="autocomplete/confimodal.js"></script>

<script src="funcoes.js"></script>
<script language="JavaScript">
	function valor_java(valor) {
		valor = valor.replace(".", "");
		valor = valor.replace(".", "");
		valor = valor.replace(".", "");
		valor = valor.replace(",", ".");
		return parseFloat(valor);
	}

	function calc_total() {
		if (document.formpedido.edtValunit.value != "" && document.formpedido.edtqtd.value != "") {
			var valunit = document.formpedido.edtValunit.value;
			valunit = valor_java(valunit);
			var qtd = document.formpedido.edtqtd.value;
			qtd = valor_java(qtd);
			var total = valunit * qtd;
			total = total.toString();
			document.formpedido.edtTotal.value = total.toString();
		}
	}

	function submit_action(caminho) {
		//postando para a verificacao;
		document.formpedido.action = caminho;
		document.formpedido.method = 'post';
		document.formpedido.submit();
	}

	function atualiza() {
		window.opener.location.reload();
	}

	//carrega vetor com o codigo dos produtos.
	var arr_prod = new Array();
		<? 
		 if ($produtos == "ok"){
			$sql_nome = "SELECT pro_cod, pro_descabv, pro_ref, pro_preco1 FROM produtos;";
			$query_nome = mysql_query($sql_nome);
			while ($linha_nome = mysql_fetch_object($query_nome)) {
				?>
										arr_prod["<?=$linha_nome -> pro_cod ?>"] = "<?=$linha_nome -> pro_ref ?>|<?=$linha_nome -> pro_descabv ?>|<?=number_format($linha_nome -> pro_preco1, '2', ',', '.') ?>
					";
				<?
				}
				}
		?>
		
					function nome_prod() {
			if(formpedido.edtProd.value != ""){
				var dados = arr_prod[formpedido.edtProd.value];
				if (dados != undefined){
					//extraindo a referencia do produto
					for(i = 0; i <= dados.length; i++){
						if(dados.substr(i,1) == "|"){						
							ref = dados.substr(0,i);
							formpedido.edtRef.value = dados.substr(0,i);
							break;
						}
					}			
					//extraindo a descricao do produto
					for(a = i+1; a <= dados.length; a++){

						if(dados.substr(a,1) == "|"){													
							descr = dados.substr(i + 1 , a - i);
							formpedido.edtNomeprod.value = dados.substr(i + 1 , a - (i+1));
							break;							
						}					
					}					
					//extraindo o preco do produto
					preco = dados.substr(a + 1 , dados.length);
					formpedido.edtValunit.value  = dados.substr(a + 1 , dados.length);										
				}else{
					formpedido.edtRef.value = "Ref";
					formpedido.edtNomeprod.value = "Produto não encontrado";					
					formpedido.edtValunit.value = "0,00";
				}	
			}else{
				formpedido.edtRef.value = "";
				formpedido.edtNomeprod.value = "";
				formpedido.edtValunit.value = "";
			}
		}
			
		function troca(){
			 eval("popup('troca_prod.php?menuoff=<?=$menuoff ?>
			&edtNumPed="+document.formpedido.edtNumPed.value+
			"',830,220,'center','center',POP_tot);");
			}

			//converte campos do formato 5000 para 5.000,00;

			function verifica_loja(){
			if(formpedido.lstLoja.value == "Escolha a loja"){
			alert("Escolha a Loja!");
			document.formpedido.lstLoja.focus();
			return false;
			}
			}

			function verifica(){
			if(formpedido.edtNumPed.value == ""){
			alert("Digite o Número do Pedido!");
			document.formpedido.edtNumPed.focus();
			return false;
			}
			}

			function foco(obj){
			if(obj){
			obj.focus();
			}
			}

	</script>
</head>
<!-- 

mudou o caminho da imagem


-->
<body onLoad="foco(<?=$campo ?>)" topmargin="0" background="fundomain.png" bottommargin="0" leftmargin="0" 
rightmargin="0"> 
<?
	//if ($menuoff != "ok"){
	//include("menu_java.php");
	//}
?>
<form action="pedido_cad.php?incluir=s&campo=formpedido.edtEmissao&menuoff=<?=$menuoff ?>" method="post" name=
"formpedido">
  <table width="100%" cellpadding="2" cellspacing="2" align="center" border="1" bordercolor="#CCCCCC"> 
    <tr> 
      <td align="left" bgcolor="#085D44">
  		<table width="100%" cellpadding="0" cellspacing="0" align="center" border="0">
         <tr>
	      <td align="left" bgcolor="#085D44"><img src="imagens/pedido_venda.jpg"></td>
          <? if ($orc_num != ""){ ?>
	      <td align="right" bgcolor="#085D44"><font style="color:#FFF; font-size:18px">Orçamento <?=$orc_num ?></
          font></td>
          <? } ?>          
         </tr>
        </table>
	 </td>
    </tr> 
    <?
		if (isset($msg_ped)) {
			echo "<tr>";
			echo "<td align='center'><font class='AVISO'>" . $msg_ped . "</font></td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td align='center'><font class='AVISO'>" . $msg_ped2 . "</font></td>";
			echo "</tr>";
			exit ;
		}
	?> 
    
    <!--############################ INICIO DA PRIMEIRA TABLET ###################################--> 
    <tr> 
      <td width="100%" align="left">
      <table bgcolor="#DFFFDF" width="100%" height="41%" border="0" cellpadding="2" cellspacing="0">
          <tr>
            <td><img src="imagens/cliente.jpg"></td>
            <td><img src="imagens/num_pedido.jpg"></td>
            <td><img src="imagens/emissao.jpg"></td>
            <td><img src="imagens/vendedor.jpg"></td>
            <td><img src="imagens/tipovenda.jpg"></td>
          </tr>          
          <tr>
          
          
            <td>
			  <?
				$sql_lstcli = "SELECT cli_razao FROM clientes WHERE cli_cgccpf = '" . $lstCli . "'";
				$query_lstcli = mysql_query($sql_lstcli, $conexao) or die("Erro 1!");
				if (mysql_num_rows($query_lstcli) > 0) {
					$linha_lstcli = mysql_fetch_object($query_lstcli);
					$lstClirazao = $linha_lstcli -> cli_razao;
				}
              ?>            
             <input id="lstCli" type="text"  style="font-weight:bold; border:solid 1; 
             background-color:#FFFFC0; height:20; width:285;" onFocus="lookup(this.value);" >
             
         
         <!-- Button of popup -->
		 <? if ($trava_cab != "disabled"){?>			 
             <input name="Cliente" type="button" value="Pesq. Cliente" style="font-weight:bold; border:solid 1; 
             background-color:#CCFFFF; height:20; width:80;" onClick=
             "javascript:popup('pesq_cliente.php?tela=pedidocad&campo=formPesq.edtBusca&edtPedido=<?=$edtPedido ?>
             &lstCli=<?=$lstCli ?>
             &prod_estoque=s&incluir_prod=s&produtos=ok&trava=disabled&trava_list=disabled&edtNumPed=<?=$edtNumPed ?>&edtSit=<?=$edtSit ?>&trava_cab=<?=$trava_cab ?>&lstVend=<?=$lstVend ?>&lstTV=<?=$lstTV ?>&edtEmissao=
			 <?=muda_data_en($edtEmissao) ?>menuoff=<?=$menuoff ?>&orc_num=<?=$orc_num ?>
             ',850,550,'center','center',POP_tot);">
         <? } else { ?>
         <a href="#janela1" rel="modal"><img src="imagens/mais.jpg" height="18px" width="18px" ></img></a>
         <!-- FOI CRIADO ESTE BUTTON PARA TESTE DE FUNCIONALIDADE DA PAGINA -->              
             <input name="Cliente" type="button" value="Pesq. Cliente" style="font-weight:bold; border:solid 1; 
             background-color:#CCFFFF; height:20; width:80;" onClick=
             "javascript:popup('pesq_cliente.php?tela=pedidocad&campo=formPesq.edtBusca&edtPedido=<?=$edtPedido ?>
             &lstCli=<?=$lstCli ?>
             &prod_estoque=s&incluir_prod=s&produtos=ok&trava=disabled&trava_list=disabled&edtNumPed=<?=$edtNumPed ?>&edtSit=<?=$edtSit ?>&trava_cab=<?=$trava_cab ?>&lstVend=<?=$lstVend ?>&lstTV=<?=$lstTV ?>&edtEmissao=
             <?=muda_data_en($edtEmissao) ?>menuoff=<?=$menuoff ?>&orc_num=<?=$orc_num ?>
             ',850,550,'center','center',POP_tot);">
            
         <?php
		}
		 ?>
         <!-- Button of popup -->			
         
         
            </td>
            
            
			<td>
				<?
				$sql_pvautomatico = "SELECT lj_sigla, lj_pvautomatico, lj_seqpv FROM loja 
										 where lj_cod = '$ljcod'";
				$query_pvautomatico = mysql_query($sql_pvautomatico, $conexao);
				$linha_pvautomatico = mysql_fetch_object($query_pvautomatico);
				if ($linha_pvautomatico -> lj_pvautomatico == "S") {
					$trava_ped = 'readonly';
					if ($edtNumPed == "") {
						$edtNumPed = $linha_pvautomatico -> lj_sigla . $linha_pvautomatico -> lj_seqpv;
					}
				}
				 ?>
             <input name="edtNumPed" type="text" value="<?=$edtNumPed ?>" size="8" maxlength="20" style="text-align:
             center; font-weight:bold; color:#000000; font-size:12; width:140; background-color: #FFFF80; border:
             solid 1;" <?=$trava_ped ?>>            
            </td>
            
			
			<?
				if ($edtEmissao == "") {
					$edtEmissao = $data;
				}
			 ?> 
             
                        
            <td>             
             <input name="edtEmissao" type="text" style="font-weight:bold; border:solid 1; color:#000000; 
             background-color:#FFFFC0;" size="11" value="<?=muda_data_pt($edtEmissao) ?>" 
			 onBlur="javascript: formpedido.hdEmissao.value = formpedido.edtEmissao.value" <?=$trava_ped ?>>
            </td>
            
            
            
            <td>
             <select name="lstVend" style="font-weight:bold; border:solid 1; background-color:#FFFFC0; width:150;" 
			 	onBlur="javascript: formpedido.hdVend.value = formpedido.lstVend.value;" <?=$trava_cab ?>>
                <?
				$sql_vend = "SELECT ven_cod,ven_nome FROM vendedor 
									WHERE ven_loja = '$ljcod' AND ven_ativo = 'S'
                                    ORDER BY ven_nome;";
				$query_vend = mysql_query($sql_vend, $conexao);
				if (mysql_num_rows($query_vend) > 0) {
					while ($linha_vend = mysql_fetch_object($query_vend)) {
						if ($lstVend == $linha_vend -> ven_cod) {
							echo "<option value='" . $linha_vend -> ven_cod . "' selected>" . $linha_vend -> ven_nome . "
								</option>";
						} else {
							echo "<option value='" . $linha_vend -> ven_cod . "'>" . $linha_vend -> ven_nome . "</option>";
						}
					}
				}
				?>
              </select>              
             </td>
             
       	 	
			
			<? if ($antes == "auricelio foi o responsavel"){ ?>             
            <td><select name="lstTV" style="font-weight:bold; border:solid 1; background-color:#FFFFC0; width:110;"
				onBlur="javascript: formpedido.hdTipove.value = formpedido.lstTV.value" <?=$trava_cab ?>>
                <?
				$sql_tv = "SELECT pp_cod,pp_desc FROM planopag;";
				$query_tv = mysql_query($sql_tv, $conexao);
				if (mysql_num_rows($query_tv) > 0) {
					while ($linha_tv = mysql_fetch_object($query_tv)) {
						if ($lstTV == $linha_tv -> pp_cod) {
							echo "<option value='" . $linha_tv -> pp_cod . "' selected>" . $linha_tv -> pp_desc . "
									  </option>";
						} else {
							echo "<option value='" . $linha_tv -> pp_cod . "'>" . $linha_tv -> pp_desc . "</option>";
						}
					}
				}
				?>
              </select>
              </td>
            
            
            
            <td>
        	<? } ?>
        	<? if ($antes == "ssdfdfsafadsf"){?>
              <select name="lstCli" style="font-weight:bold; border:solid 1; background-color:#FFFFC0; width:340;" 
			  <?=$trava_cab ?>>
                <?
				$sql_cli = "SELECT cli_cgccpf,cli_razao FROM clientes
									order by cli_razao;";
				$query_cli = mysql_query($sql_cli, $conexao);
				if (mysql_num_rows($query_cli) > 0) {
					if (!isset($lstCli)) {
						while ($linha_cli = mysql_fetch_object($query_cli)) {
							if ($lstCli == $linha_cli -> cli_cgccpf) {
								echo "<option value='" . $linha_cli -> cli_cgccpf . "' selected>" . $linha_cli -> cli_razao . "
									</option>";
							} else {
								echo "<option value='" . $linha_cli -> cli_cgccpf . "'>" . $linha_cli -> cli_razao . "</option>";
							}
						}
					} else {
						$sql_cli2 = "SELECT cli_cgccpf,cli_razao FROM clientes
    									WHERE cli_cgccpf = '$lstCli';";
						$query_cli2 = mysql_query($sql_cli2, $conexao);
						if (mysql_num_rows($query_cli2) > 0) {
							$linha_cli2 = mysql_fetch_object($query_cli2);
							echo "<option value='" . $linha_cli2 -> cli_cgccpf . "' selected>" . $linha_cli2 -> cli_razao . "
						 		  </option>";
						}
					}
				}
				?>
              </select>
        	   <? } ?>		
              <input name="hdProd" type="hidden" id="hdProd" value="<?=$edtProd ?>">
              <input name="hdqtd" type="hidden" id="hdqtd" value="<?=$edtqtd ?>">
              <input name="hdCor" type="hidden" id="hdCor" value="<?=$lstCor ?>">
              <input name="hdEscala" type="hidden" id="hdEscala" value="<?=$lstEscala ?>">
              <input name="hdProg" type="hidden" id="hdProg" value="<?=$lstProg ?>">
              <input name="hdValunit" type="hidden" id="hdValunit" value="<?=$edtValunit ?>">
              <input name="hdTotal" type="hidden" id="hdTotal" value="<?=$edtTotal ?>">
              <input name="hdEmissao" type="hidden" id="hdEmissao2" value="<?=$edtEmissao ?>">
              <input name="hdVend" type="hidden" id="hdVend2" value="<?=$lstVend ?>">
              <input name="hdTipove" type="hidden" id="hdTipove2" value="<?=$lstTV ?>">
              <input name="hdCli" type="hidden" id="hdCli2" value="<?=$lstCli ?>">
              <input name="hdProg" type="hidden" id="hdCli2" value="<?=$lstProg ?>">
             </td>
             
             <!--
             	ESTA PARTE E O BUTTON PARA GRAVAR O BABEÇALHO PARA PASSAR PARA A
             	PROXIMA TELA
			 -->
			 
			 <? if ($trava_cab != "disabled"){?>
             <td>
              <a href="#" onClick="javascript: submit_action('pedido_cad.php?incluir=s&produtos=ok&campo=formpedido.edtProd&lstCli=<?=$lstCli ?>&menuoff=<?=$menuoff ?>&orc_num=<?=$orc_num ?>'); javascript: formpedido.hdCli.value = formpedido.lstCli.value"><img src="imagens/gravar_cab.jpg" border='1' alt="Gravar Cabeçalho"></a>
             </td>
             <? }else{ ?>
             <!-- FOI DUPLICADO ESSE BUTTON POR QUESTOES DE TESTE -->
             <td>
              <a href="#" onClick="javascript: submit_action('pedido_cad.php?incluir=s&produtos=ok&campo=formpedido.edtProd&lstCli=<?=$lstCli ?>&menuoff=<?=$menuoff ?>&orc_num=<?=$orc_num ?>'); javascript: formpedido.hdCli.value = formpedido.lstCli.value"><img src="imagens/gravar_cab.jpg" border='1' alt="Gravar Cabeçalho"></a>
             </td>    
			 <? } ?>
             
             
          </tr>
         </table>
        </td>
    </tr>
<!--############################ FIM DA PRIMEIRA TABLET ###################################--> 
     
    <? if ($produtos == "ok"){ ?> 
    <tr> 
      <td colspan="7">
      <table bgcolor="#FFFFEA" width="100%" cellpadding="2" cellspacing="2" align="center" 
      		 border="1" bordercolor="#004000">
          <?
		if (isset($msg_prod)) {
			echo "<tr>";
			echo "<td align='center' bgcolor='#FF0000'><font style='font-size:16px; color:#FFFFFF;'>" . $msg_prod . "</font></td>";
			echo "</tr>";
		}
		if (isset($msg_estoq)) {
			echo "<tr>";
			echo "<td bgcolor='#FF0000' align='center'><font size='3' color='#FFFFFF'>:::::::::::: " . $msg_estoq . " ::::::::::::</font></td>";
			echo "</tr>";
		}
		if (isset($msg_estoq2)) {
			echo "<tr>";
			echo "<td bgcolor='#FF0000' align='center'><font size='3' color='#FFFFFF'>:::::::::::: " . $msg_estoq2 . " ::::::::::::</font></td>";
			echo "</tr>";
		}
		if (isset($msg_estoq3)) {
			echo "<tr>";
			echo "<td bgcolor='#FF0000' align='center'><font size='3' color='#FFFFFF'>:::::::::::: " . $msg_estoq3 . " ::::::::::::</font></td>";
			echo "</tr>";
		}
		?> 
        <? if($pro_det != "ok"){ ?>
          <tr> 
            <td width="100%" bgcolor="#FFFFEA" align="center">
            <table>
     <?
				if (!isset($edtRef)) { $edtRef = 'REF.';
				};
				if (!isset($edtNomeprod)) { $edtNomeprod = 'DESCRIÇÃO';
				};
 ?>
                <tr> 
                  <td colspan="7" ><img src="imagens/cod_prod.jpg">
                    <input name="edtProd" type="text" onBlur="nome_prod(); javascript: lstEscala.focus();" value="<?=$edtProd ?>" size="8">
                    <input name="edtRef" type="text" value="<?=$edtRef ?>" size="8" readonly>
                    <input name="edtNomeprod" type="text" value="<?=$edtNomeprod ?>" size="60" readonly>
                    <a href="#" onClick="javascript:popup('pesq_prod.php?tela=pedidocad&campo=formPesq.edtBusca&edtPedido=<?=$edtPedido ?>&lstCli=<?=$lstCli ?>&prod_estoque=s&incluir_prod=s&produtos=ok&trava=disabled&trava_list=disabled&edtNumPed=<?=$edtNumPed ?>&edtSit=<?=$edtSit ?>&trava_cab=<?=$trava_cab ?>&lstVend=<?=$lstVend ?>&lstTV=<?=$lstTV ?>&edtEmissao=<?=muda_data_en($edtEmissao) ?>&menuoff=<?=$menuoff ?>',850,550,'center','center',POP_tot);"><img src="imagens/pesq.jpg" border='0' alt="Pesquisar Produtos"></a>
                  </td>
                </tr> 
                <tr> 
                  <td><img src="imagens/escala1.jpg"></td>
                  <td><img src="imagens/grupo.jpg"></td>
                  <td><img src="imagens/escala2.jpg"></td>
                  <td><img src="imagens/loja2.jpg"></td>
                  <td><img src="imagens/qtde.jpg"></td>
                  <td><img src="imagens/valor_unit.jpg"></td>
                  <td><img src="imagens/valor_total.jpg"></td>
                </tr>
                <tr>
				
 <?  // echo $campo.'rrr';  echo $edtProd.'aaaa';  echo $escala.'xxx';  echo $progrupo.'bbb'; echo $cor.'ccc'; echo $loja.'ddd'; ?>
				
                  <td><select name="lstEscala" style="width:180; background-color:#D7FFD7;">
		            <option value='ESC1' selected>Escolha a Escala 1</option>
                      <?
					/*      if ($lstEscala == "ESC1" || $lstEscala == ""){
					 $foralinha = " where foralinha <> 'S' ";
					 }else{
					 $foralinha = "";
					 } */
					$sql_esc = "SELECT esc_cod, esc_descabv from escala " . $foralinha . " order by esc_descabv;";
					$query_esc = mysql_query($sql_esc, $conexao);
					if (mysql_num_rows($query_esc) > 0) {
						while ($linha_esc = mysql_fetch_object($query_esc)) {
							if ($linha_esc -> esc_cod == $escala) {
								echo "<option value='" . $linha_esc -> esc_cod . "' selected>" . $linha_esc -> esc_descabv . "</option>";
							} else {
								echo "<option value='" . $linha_esc -> esc_cod . "'>" . $linha_esc -> esc_descabv . "</option>";
							}
						}
					}
						?>
                    </select> </td>
                  <td><select name="lstProg" id="lstProg" style="width:150; background-color:#D7FFD7;">
		            <option value='GRU1' selected>Escolha o Grupo</option>
                    <?
					$sql_prog = "SELECT prog_cod, prog_descabv from progrupo order by prog_descabv;";
					$query_prog = mysql_query($sql_prog, $conexao);
					if (mysql_num_rows($query_prog) > 0) {
						while ($linha_prog = mysql_fetch_object($query_prog)) {
							if ($linha_prog -> prog_cod == $progrupo) {
								echo "<option value='" . $linha_prog -> prog_cod . "' selected>" . $linha_prog -> prog_descabv . "</option>";
							} else {
								echo "<option value='" . $linha_prog -> prog_cod . "'>" . $linha_prog -> prog_descabv . "</option>";
							}
						}
					}
						?>
                  </select></td>
                  <td>
                    <select name="lstCor" style="width:210; background-color:#D7FFD7;">
		            <option value='ESC2' selected>Escolha a Escala 2</option>
                      <?
					$sql_cor = "SELECT cor_cod, cor_descabv FROM cores order by cor_descabv;";
					$query_cor = mysql_query($sql_cor, $conexao);
					if (mysql_num_rows($query_cor) > 0) {
						while ($linha_cor = mysql_fetch_object($query_cor)) {
							if ($linha_cor -> cor_cod == $cor) {
								echo "<option value='" . $linha_cor -> cor_cod . "' selected>" . $linha_cor -> cor_descabv . "</option>";
							} else {
								echo "<option value='" . $linha_cor -> cor_cod . "'>" . $linha_cor -> cor_descabv . "</option>";
							}
						}
					}
						?>
                    </select></td>
                  <td><select name="lstLoja" style="width:120; background-color:#FFFFC0;">
                    <?
					$sql_uf = "SELECT lj_estado FROM loja where lj_cod = '$ljcod';";
					$query_uf = mysql_query($sql_uf, $conexao);
					if (mysql_num_rows($query_uf) > 0) {
						$linha_uf = mysql_fetch_object($query_uf);
					}
					$sql_loja = "SELECT lj_cod, lj_sigla FROM loja where lj_estado = '" . $linha_uf -> lj_estado . "' order by lj_fantasia;";
					$query_loja = mysql_query($sql_loja, $conexao);

					if (mysql_num_rows($query_loja) > 0) {
						while ($linha_loja = mysql_fetch_object($query_loja)) {
							if (!isset($lstLoja)) {
								if ($linha_loja -> lj_cod == $ljcod) {
									echo "<option value='" . $linha_loja -> lj_cod . "' selected>" . $linha_loja -> lj_sigla . "</option>";
								} else {
									echo "<option value='" . $linha_loja -> lj_cod . "'>" . $linha_loja -> lj_sigla . "
										</option>";
								}
							} else {
								if ($linha_loja -> lj_cod == $lstLoja) {
									echo "<option value='" . $linha_loja -> lj_cod . "' selected>" . $linha_loja -> lj_sigla . "</option>";
								} else {
									echo "<option value='" . $linha_loja -> lj_cod . "'>" . $linha_loja -> lj_sigla . "
										</option>";
								}
							}
						}
					}
							?>
                    </select> </td> 
                  <td><input name="edtqtd" style="text-align:right; background-color:#FFFFC0; width:60px;" type="text" value="<?=$edtqtd ?>"></td>
				<?
				include ("calculo_precos.php");
				?>				  
                  <td><input type="text" name="edtValunit" style="text-align:right; background-color:#FFFFC0;" size="11" maxlength="10" onBlur= "javascript: calc_total(); btnIncProd.focus();" value="<?=number_format($edtValunit, '2', ',', '.') ?>"> </td>
                  <td><input type="text" style="text-align:right; background-color:#FF0000; color:#FFFFFF;" name="edtTotal" size="12" readonly></td>
                </tr> 
              </table></td> 
          </tr> 
          <tr>
          <td bgcolor='#DFFFDF' align="center">
              <input type="button" name="btnIncProd" style="font-weight:bold; color:#000000; width:210; background-color:#FFFF00;" value="[ Incluir Produto ]" onClick="submit_action('pedido_cad.php?campo=formpedido.edtProd&edtPedido=<?=$edtPedido ?>&lstCli=<?=$lstCli ?>&edtProd=<?=$edtProd ?>&prod_estoque=s&incluir_prod=s&produtos=ok&trava=disabled&trava_list=disabled&trava_ped=readonly&edtNumPed=<?=$edtNumPed ?>&edtSit=<?=$edtSit ?>&lstLoja=<?=$lstLoja ?>&trava_cab=<?=$trava_cab ?>&lstVend=<?=$lstVend ?>&lstTV=<?=$lstTV ?>&menuoff=<?=$menuoff ?>');" <?=travalist ?>>
              <input type="button" name="finalizar" style="font-weight:bold; color:#000000; width:210; background-color:#FFFF00;" value="[ .:: Finalizar Pedido ::. ]" onClick="submit_action('pedido_cad.php?flag=finalizar&alteraritem=<?=$alteraritem ?>'); javascript:popup('pedido_rel.php?flag=finalizar&edtNumPed=<?=$edtNumPed ?>&edtPedido=<?=$edtPedido ?>&lstLoja=<?=$lstLoja ?>&menuoff=<?=$menuoff ?>',920,650,'center','center',POP_tot) ;">
              <input type="button" name="btnTroca" style="color:#000000; width:210; background-color:#FFFF80;" value="[ Troca de Produtos ]" onClick="troca();">
              <input type="button" name="btnIncProd2" style="color:#000000; width:210; background-color:#FFFF80;" value="[ Incluir Prod. p/ Ped. de Fábrica ]" onClick="submit_action('pedido_cad.php?campo=formpedido.edtProd&edtPedido=<?=$edtPedido ?>&edtProd=<?=$edtProd ?>&prod_estoque=s&incluir_prod=s&produtos=ok&trava=disabled&trava_list=disabled&trava_ped=readonly&edtNumPed=<?=$edtNumPed ?>&edtSit=<?=$edtSit ?>&lstLoja=<?=$lstLoja ?>&incluirpc=s&trava_cab=disabled&trava_ped=readonly&lstCli=<?=$lstCli ?>&lstVend=<?=$lstVend ?>&lstTV=<?=$lstTV ?>&menuoff=<?=$menuoff ?>');" <?=travalist ?>>

           <? if ($botoesantigos == "asdfadsf"){ ?>
              <a href="#" onClick="submit_action('pedido_cad.php?campo=formpedido.edtProd&edtPedido=<?=$edtPedido ?>&edtProd=<?=$edtProd ?>&prod_estoque=s&incluir_prod=s&produtos=ok&trava=disabled&trava_list=disabled&edtNumPed=<?=$edtNumPed ?>&edtSit=<?=$edtSit ?>&lstLoja=<?=$lstLoja ?>&menuoff=<?=$menuoff ?>');"><img src="imagens/inc_prod.jpg" border='1' alt="Incluir Produto"></a>
              <a href="#" onClick="submit_action('pedido_cad.php?flag=finalizar&alteraritem=<?=$alteraritem ?>'); javascript:popup('pedido_rel.php?flag=finalizar&edtNumPed=<?=$edtNumPed ?>&edtPedido=<?=$edtPedido ?>&lstLoja=<?=$lstLoja ?>&menuoff=<?=$menuoff ?>',920,650,'center','center',POP_tot) ;"><img src="imagens/finalizar.jpg" border='1' alt="Finalizar Pedido"></a>
              <a href="#" onClick="troca();"><img src="imagens/troca_prod.jpg" border='1' alt="Troca de Produtos"></a>
              <a href="#" onClick="submit_action('pedido_cad.php?campo=formpedido.edtProd&edtPedido=<?=$edtPedido ?>&edtProd=<?=$edtProd ?>&prod_estoque=s&incluir_prod=s&produtos=ok&trava=disabled&trava_list=disabled&edtNumPed=<?=$edtNumPed ?>&edtSit=<?=$edtSit ?>&lstLoja=<?=$lstLoja ?>&incluirpc=s&trava_ped=readonly&lstCli=<?=$lstCli ?>&menuoff=<?=$menuoff ?>');"><img src="imagens/inc_prod_pf.jpg" border='1' alt="Incluir Produto para Pedido de Fábrica"></a>
           <? } ?>
          </td>
          </tr> 
          <tr> 
            <td background="imagens/back.jpg" align="center">
            <table width="100%" border="1" bordercolor='#00D500'>
                <tr>
                  <td background="imagens/back.jpg" align="center" bgcolor="#00D500"><font size='2' color="#000000"><u>Grade de Produtos Incluídos</u></font></td>
                </tr>
                <tr> 
                  <td>
                  <table bgcolor="#FFFFFF" width="100%" align="center" bordercolor='#008000' border="1">
                      <tr> 
                        <td width="4%" align="center" bgcolor="#DFFFDF">Cód.</td>
                        <td width="22%" align="left" bgcolor="#DFFFDF">Descrição</td>
                        <td width="10%" align="center" bgcolor="#DFFFDF">Escala 1</td>
                        <td width="10%" align="center" bgcolor="#DFFFDF">Grupo</td>
                        <td width="10%" align="center" bgcolor="#DFFFDF">Escala 2</td>
                        <td width="5%" align="right" bgcolor="#DFFFDF">Qtde</td>
                        <td width="10%" align="right" bgcolor="#DFFFDF">Val Unit</td>
                        <td width="10%" align="right" bgcolor="#DFFFDF">Total</td>
                        <td width="2%" align="center" bgcolor="#DFFFDF">Tipo</td>
                        <td width="14%" align="center" bgcolor="#DFFFDF">Loja</td>
                        <td width="7%" align="center" bgcolor="#DFFFDF">Excluir</td>
                      </tr>
                      <?
								$sql_show = "SELECT DISTINCT pm_prod, pro_descabv, pm_cor,pm_loja, pm_escala, 
															 pro_foralinha,
                                                             pm_progrupo, pm_es, pm_qtd,
                                                             pm_valuni,pm_valtot
											 FROM pedmov, produtos
											 WHERE pro_cod = pm_prod AND pm_num = '$edtNumPed' 
											 AND pm_lojaloc = '$ljcod';";
								       // echo $sql_show;
								$query_show = mysql_query($sql_show,$conexao)or die("Erro na Exibição");
								if(mysql_num_rows($query_show) > 0){
									$i = 1;
                                    $totalpedido = 0;
                                    while($linha_itens = mysql_fetch_object($query_show)){
                                       static $flagcolor = false;
                                       if ($flagcolor = !$flagcolor){
                                         $color = "#FFFFC0";
                                       }else{
                                         $color = "#FFFFFF";
                                       }
									   
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
									   
										if ($linha_itens->pro_foralinha == "S"){
										 $foralinha = '<font color="#FF0000"><b> ( FORA DE LINHA )</b></font>';
										}else{
										 $foralinha = '';
										}
									   
                                        $totalpedido = $totalpedido + $linha_itens->pm_valtot;
										echo "<tr>";
											echo "<td align='center' bgcolor='".$color."' width='4%'><font color='#000000'>".$linha_itens->pm_prod."</font></td>";
											echo "<td align='left' bgcolor='".$color."' width='22%'><font color='#400000'>".$linha_itens->pro_descabv."".$foralinha."</font></td>";
											echo "<td align='center' bgcolor='".$color."' width='10%'><font color='#400000'>".$linha_escala_->esc_descabv."</font></td>";
											echo "<td align='center' bgcolor='".$color."' width='10%'><font color='#400000'>".$linha_prog_->prog_descabv."</font></td>";
											echo "<td align='center' bgcolor='".$color."' width='10%'><font color='#400000'>".$linha_cor_->cor_descabv."</font></td>";
											echo "<td align='right' bgcolor='".$color."' width='5%'><font color='#400000'>".number_format($linha_itens->pm_qtd,'2',',','.')."</font></td>";
											echo "<td align='right' bgcolor='".$color."' width='10%'><font color='#400000'>".number_format($linha_itens->pm_valuni,'2',',','.')."</font></td>";
											echo "<td align='right' bgcolor='".$color."' width='10%'><font color='#FF0000'>".number_format($linha_itens->pm_valtot,'2',',','.')."</font></td>";
											echo "<td align='center' bgcolor='".$color."' width='2%'><font color='#000000'>".$linha_itens->pm_es."</font></td>";
										    echo "<td align='center' bgcolor='".$color."' width='14%'><font color='#000000'>".$linha_loja_->lj_sigla."</font></td>";
                                    ?>
								      <td align='center' bgcolor='<?=$color ?>' width='7%'><a href='pedido_cad.php?flag=excluir_item&edtNumPed=<?=$edtNumPed ?>&prod_estoque=s&produtos=ok&trava=disabled&trava_list=disabled&prod=<?=$linha_itens -> pm_prod ?>&escala=<?=$linha_itens -> pm_escala ?>&cor=<?=$linha_itens -> pm_cor ?>&progrupo=<?=$linha_itens -> pm_progrupo ?>&loja=<?=$linha_itens -> pm_loja ?>&lstCli=<?=$lstCli ?>&lstVend=<?=$lstVend ?>&lstTV=<?=$lstTV ?>&menuoff=<?=$menuoff ?>'><img src='imagens/apagar.gif' border="no" alt="Excluir"></a></td>
                                    <?
									echo "</tr>";
									$i++;
									}
									}
							  ?> 
                         <? if ( ( ($ljcod == '04') || ($ljcod == '06') || ($ljcod == '07') || ($ljcod == '37') ) ) {?>                              
                           <tr>
                            <td bgcolor='#FFFFEA' colspan='4' align='right'>
                  			 <table bgcolor="#FFE1E1" width="100%" align="center" bordercolor='#008000' border="0" cellspacing="2" cellpadding="2">
                              <tr>
                                <td bgcolor='#FFE1E1' align='left'>
                                 Desconto:
                                </td>
                                <?  $edtDescontov = '0';
									$sql_descontos = "SELECT ped_desconto, ped_descontop from pedcad
													     WHERE ped_num = '$edtNumPed' AND ped_loja = '$ljcod';";
									$query_descontos = mysql_query($sql_descontos, $conexao);
									if (mysql_num_rows($query_descontos) > 0) {
										$linha_descontos = mysql_fetch_object($query_descontos);
										$edtDesconto = $linha_descontos -> ped_desconto;
									}
								?>
                                
                                <td bgcolor='#FFE1E1' align='left'>
                                 R$: <input name="edtDesconto" type="text" style="width:100; color:#F00; background-color:#FFF; text-align:right;" value="<?=number_format($edtDesconto, '2', ',', '.') ?>"> 
                                </td>
                                <td bgcolor='#FFE1E1' align='left'>
                                <input type="button" name="btnDesconto" style="font-weight:bold; color:#FFF; width:170; background-color:#F00;" value="[ Aplicar Desconto ]" onClick="submit_action('pedido_cad.php?campo=formpedido.edtProd&prod_estoque=s&incluir_desconto=s&produtos=ok&trava=disabled&trava_list=disabled&edtNumPed=<?=$edtNumPed ?>&edtSit=<?=$edtSit ?>&lstCli2=<?=$lstCli ?>&lstCli=<?=$lstCli ?>&trava_cab2=<?=$trava_cab2 ?>&trava_cab=<?=$trava_cab ?>&lstVend=<?=$lstVend ?>&lstTV=<?=$lstTV ?>&edtEmissao=<?=muda_data_en($edtEmissao) ?>&trava_ped=readonly&menuoff=<?=$menuoff ?>');" <?=travalist ?>>
                                </td>
                              </tr>
                             </table>
                            </td>
                            <td bgcolor='#FFFFEA' colspan='3' align='right'>
                              <img src="imagens/total_ped.jpg">
                            </td>
                            <td bgcolor='#FF0000' align='right'>
                             <? $descontoemvalor = 0;
								if ($edtDesconto != "0") {
									$descontoemvalor = $edtDesconto;
								}
								$totalpedido = $totalpedido - $descontoemvalor;
								//total do pedido menos o desconto em valor
							 ?>
                              <font size="3" color="#FFFFFF"><b><?=number_format($totalpedido, '2', ',', '.') ?></b></font>
                            </td>
                            <td bgcolor='#FFFFEA' colspan='3' align='right'>
                              <font size="2" color="#FFFFEA">.</font>
                            </td>
                           </tr>
						<? }else{ ?>
                           <tr>
                            <td bgcolor='#FFFFEA' colspan='7' align='right'>
                              <img src="imagens/total_ped.jpg">
                            </td>
                            <td bgcolor='#FF0000' align='right'>
                              <font size="3" color="#FFFFFF"><b><?=number_format($totalpedido, '2', ',', '.') ?></b></font>
                            </td>
                            <td bgcolor='#FFFFEA' colspan='3' align='right'>
                              <font size="2" color="#FFFFEA">.</font>
                            </td>
                           </tr>
                        <? } ?>                                
                         </table></td>
                </tr>
              </table></td>
          </tr> 
          <? } ?> 
        </table></td> 
    </tr> 
    <? } ?> 
  </table> 
    		<!-- CRIANDO A TELA DE MODAL PARA CADASTRAMENTO DE CLIENTE -->
		<div class="window" id="janela1">
    	<!-- CASO QUEIRA COLOCAR UM BUTTON PARA FECHAR O MODAL 
        	<a href="#" class="fechar">X Fechar</a> -->
	      <?php
		//INCLUNDO A TELA DE CADASRTRO DE CLIENTE
		include ("cli_cad.php?");
			?>
		</div>
		<!-- MASCARA PARA COBRIR O SITE QUANDO ESTIVER NO MODAL -->  
		<div id="mascara"></div>

</form> 
</body>
</html>

<? }//////////////////////////////////////////////  
	//fim do if ($caixatravado == "S"){
	//include("rodape.php");
?>