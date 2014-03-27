<?
	include("conexao2.inc.php");
	include("funcoes2.inc.php");
	include("dglogin1.php");
	include("fpdf.php");	
//	include("mc_table.php");	
	
	$arquivo = "pedido_rel_novo.php";
	include("auditoria.php");
	
	//pegando a loja
	$sql_loja = "SELECT lj_cod, lj_sigla, lj_fantasia, lj_end, lj_bairro, lj_cidade, lj_uf, lj_cep, lj_fone, lj_fax, lj_logopv, 
						lower(lj_email) as lj_email FROM loja WHERE lj_cod = '$ljcod' ";
    $query_loja = mysql_query($sql_loja, $conexao);
    if (mysql_num_rows($query_loja) > 0){
		$linha_loja = mysql_fetch_object($query_loja);
	}

	//pegando o cliente
	$sql_cli = "SELECT DISTINCT cli_cgccpf, cli_inscrg, cli_razao, cli_end, cli_bairro, cli_cidade, cli_estado, cli_cep, lower(cli_email) as cli_email, 
								cli_pontoref, cli_profissaocod, cli_fone, cli_fax, cli_operadora1, cli_operadora2, cli_celular1, cli_celular2, 
								ped_num, ped_dtpag, ped_obs, ped_obsprod, ped_vend
					       FROM clientes, pedcad WHERE cli_cgccpf = ped_cliente AND ped_num = '$edtNumPed' ";
    $query_cli = mysql_query($sql_cli, $conexao);
    if (mysql_num_rows($query_cli) > 0){
		$linha_cli = mysql_fetch_object($query_cli);
		//pegando a profissao
		$sql_prof = "SELECT clip_desc FROM clientes_prof WHERE clip_cod = '".$linha_cli->cli_profissaocod."' ";
		$query_prof = mysql_query($sql_prof, $conexao);
		if (mysql_num_rows($query_prof) > 0){
			$linha_prof = mysql_fetch_object($query_prof);
		}
		
	}


	//pegando a forma pagto
	$sql_pg = "SELECT DISTINCT cxm_din, cxm_tc, cxm_de, cxm_chd, cxm_chpl, cxm_chp, cxm_car, cxm_ccd
					      FROM cxmov, pedcad WHERE cxm_pedido = ped_num AND cxm_pedido = '$edtNumPed' ";
    $query_pg = mysql_query($sql_pg, $conexao);
    if (mysql_num_rows($query_pg) > 0){
		$linha_pg = mysql_fetch_object($query_pg);
	}

	$mesq = "2"; // Margem Esquerda (mm) 
	$mdir = "2"; // Margem Direita (mm) 
	$msup = "2"; // Margem Superior (mm) 
	$pdf=new FPDF('P','mm','A4'); // Cria um arquivo novo tipo A4, na vertical. 
	$pdf->Open(); // inicia documento 
	$pdf->SetMargins('5','5'); // Define as margens do documento 
    $pdf->SetAutoPageBreak(true, 20.0);
	$pdf->SetAuthor("gercomweb"); // Define o autor 
	$pdf->SetFont('helvetica','',10); // Define a fonte 
	$pdf->SetDisplayMode($zoom,$layout='continuous'); 
	$pdf->AliasNbPages( '{total}' );
	$pdf->AddPage(); // adiciona a primeira pagina 
 	$altprod = 60;
	$pdf->Rect($mesq,$msup,206,293); //borda total da pagina A4

	include("pedido_rel_novo_cabecalho.php");
	
	//inicio da impressao dos produtos do pv
	$pdf->SetFont('helvetica','',8); // Define a fonte 		
	$pdf->Text($mesq+6,$msup+57,'Produtos'); // Imprime o pv	
	//pegando os produtos do pedido
	$qtdprodpv = 0;
	$sql_pm = "SELECT DISTINCT pro_descabv, pro_fototermica, pro_ncm, pm_escala, pm_cor, pm_progrupo, pm_qtd, pm_valuni, pm_valtot, pm_loja,
							   pm_pc, pro_foralinha, pm_pvtroca, pm_lojaloc, pm_prod
					      FROM produtos, pedmov 
						  WHERE pro_cod = pm_prod AND pm_num = '$edtNumPed'  
						  ORDER BY pm_es DESC, pm_pc, pro_descabv ";
    $query_pm = mysql_query($sql_pm, $conexao);
    if (mysql_num_rows($query_pm) > 0){
		$alturadagradedosprod = 12;
		while($linha_pm = mysql_fetch_object($query_pm)){
		  $qtdprodpv = $qtdprodpv + 1;
	      //se a pagina for preenchida total

	      if ($linha_pm->pro_fototermica == ""){
		   $fotodoproduto = "../img_prod/semfoto.jpg";			  
		  }else{
		   $fotodoproduto = "../img_prod/".$linha_pm->pro_fototermica."";
		  }
		  //echo $fotodoproduto ;
		  $pdf->Image($fotodoproduto, $mesq+6, $altprod, 23, 15);
		  $pdf->Rect($mesq+6,$altprod,23,15); //borda do produto		  
		  $pdf->SetFont('helvetica','B',12); // Define a fonte 				  
		  $altprod = $altprod + 5;
		  $pdf->Text($mesq+32,$altprod,substr($linha_pm->pro_descabv,0,40)); // Imprime o nome do produto
		  $pdf->SetFont('helvetica','B',8); // Define a fonte 				  		  
		  $pdf->Text($mesq+130,$altprod,'('.substr($linha_pm->pm_prod,0,8).')'); // Imprime as escalas do produto
		  $pdf->SetFont('helvetica','',8); // Define a fonte 				  		  		  
		  if ($linha_pm->pro_ncm != 0){
		   $pdf->Text($mesq+144,$altprod,'NCM '.substr($linha_pm->pro_ncm,0,11)); // Imprime as escalas do produto
		  }

		  $sql_pf = "SELECT pc_pedvend FROM pedcomp 
		  			  WHERE pc_pedvend = '$edtNumPed' AND pc_prod = '".$linha_pm->pm_prod."' AND pc_escala = '".$linha_pm->pm_escala."' AND 
					  		pc_progrupo = '".$linha_pm->pm_progrupo."' AND pc_cor = '".$linha_pm->pm_cor."'  AND pc_loja = '".$linha_pm->pm_loja."'  "; //echo $sql_pf;
		  $query_pf = mysql_query($sql_pf, $conexao);
		  if (mysql_num_rows($query_pf) > 0){
			$linha_pf = mysql_fetch_object($query_pf);
			$pfsimounao = 'PEDIDO DE FÁBRICA'; 
		  }else{
			$pfsimounao = 'ESTOQUE'; 			  
		  }

		  if ($linha_pm->pm_valtot < 0){ $pfsimounao = 'DEVOLUÇÃO | TROCA'; }
		  $pdf->Text($mesq+170,$altprod,$pfsimounao); // Imprime as escalas do produto		  		  		  
		  //pegando as escalas
		  $sql_esc = "SELECT esc_descabv FROM escala WHERE esc_cod = '".$linha_pm->pm_escala."'";
		  $query_esc = mysql_query($sql_esc, $conexao);
		  if (mysql_num_rows($query_esc) > 0){  $linha_esc = mysql_fetch_object($query_esc); }
		  $sql_prog = "SELECT prog_descabv FROM progrupo WHERE prog_cod = '".$linha_pm->pm_progrupo."'";
		  $query_prog = mysql_query($sql_prog, $conexao);
		  if (mysql_num_rows($query_prog) > 0){  $linha_prog = mysql_fetch_object($query_prog); }
		  $sql_cor = "SELECT cor_descabv FROM cores WHERE cor_cod = '".$linha_pm->pm_cor."'";
		  $query_cor = mysql_query($sql_cor, $conexao);
		  if (mysql_num_rows($query_cor) > 0){  $linha_cor = mysql_fetch_object($query_cor); }
		   $altprod = $altprod + 2;		  
		   $pdf->SetXY($mesq+31,$altprod); // Define as margens do documento 			  
		   $pdf->SetFont('helvetica','',8); // Define a fonte 				  		  

		  if (($linha_esc->esc_descabv == "-") && ($linha_prog->prog_descabv == "-") && ($linha_cor->cor_descabv == "-") ){
			$escalas = '';
		  }else{
			$escalas = substr($linha_esc->esc_descabv.' | '.$linha_prog->prog_descabv.' | '.$linha_cor->cor_descabv,0,57);			  
		  }

		   $pdf->Cell(96,3,$escalas,0,0,'L'); // Imprime o ponto ref cliente

		  $altprod = $altprod + 4;		  			  
		  if ($linha_pm->pro_foralinha == "S"){
		    $pdf->SetXY($mesq+31,$altprod); // Define as margens do documento 		  
		    $pdf->SetFont('helvetica','',8); // Define a fonte 				  		  			  
		    $pdf->Cell(96,3,'[ PRODUTO FORA DE LINHA NÃO SUJEITO À TROCA ]',0,0,'L'); // Imprime se o produto é fora de linha.
		  }
		  $pdf->SetFont('helvetica','B',11); // Define a fonte 				  
		  $pdf->Cell(14,3,'Qtd '.$linha_pm->pm_qtd,0,0,'L'); // Imprime o ponto ref cliente
		  $pdf->Cell(2,3,'x',0,0,'C'); // Imprime o ponto ref cliente		  
		  $pdf->Cell(28,3,' R$ '.number_format($linha_pm->pm_valuni,'2',',','.'),0,0,'R'); // Imprime o ponto ref cliente
		  $pdf->Cell(2,3,'=',0,0,'C'); // Imprime o ponto ref cliente
		  $pdf->Cell(28,3,' R$ '.number_format($linha_pm->pm_valtot,'2',',','.'),0,0,'R'); // Imprime o ponto ref cliente
		  if ($linha_pm->pm_pvtroca != ""){
		    $pdf->SetXY($mesq+127,$altprod); // Define as margens do documento 		  
		    $pdf->SetFont('helvetica','',8); // Define a fonte 				  		  			  
			$pvtroca = '[ PEDIDO ORIGINAL TROCA: '.$linha_pm->pm_pvtroca.' ]';
		    $pdf->Cell(96,3,$pvtroca,0,0,'L'); // Imprime se o produto é fora de linha.
		  }

		  if ($linha_pm->pm_loja != $linha_pm->pm_lojaloc){
		    $pdf->SetXY($mesq+192,$altprod); // Define as margens do documento 		  
		    $pdf->SetFont('helvetica','B',8); // Define a fonte 				  		  			  

			//pegando a loja
			$sql_lojaloc = "SELECT lj_sigla FROM loja WHERE lj_cod = '".$linha_pm->pm_loja."' ";
			$query_lojaloc = mysql_query($sql_lojaloc, $conexao);
			if (mysql_num_rows($query_lojaloc) > 0){
				$linha_lojaloc = mysql_fetch_object($query_lojaloc);
			}

		    $pdf->Cell(8,3,$linha_lojaloc->lj_sigla,1,0,'L'); // Imprime se o produto é fora de linha.
		  }

//		  $pdf->SetFont('helvetica','',8); // Define a fonte 			  		  
//		  $pdf->Text($mesq+32,$altprod+11,substr($linha_esc->esc_descabv.' | '.$linha_prog->prog_descabv.' | '.$linha_cor->cor_descabv,0,57)); // Imprime as escalas do produto
//		  $pdf->SetFont('helvetica','B',12); // Define a fonte 				  
//		  $pdf->Text($mesq+122,$altprod+11,'Qtd '.$linha_pm->pm_qtd); // Imprime as escalas do produto		  
//		  $pdf->Text($mesq+134,$altprod+11,' x R$ '.number_format($linha_pm->pm_valuni,'2',',','.')); // Imprime as escalas do produto		  		  
//		  $pdf->Text($mesq+164,$altprod+11,' = R$ '.number_format($linha_pm->pm_valtot,'2',',','.')); // Imprime as escalas do produto		  		  

		  //$pdf->Text($mesq+32,$altprod+14,'_______________________________________________________________________________'); // Imprime as escalas do produto		  		  			
		  $altprod = $altprod + 4;		  		  							  
		  $pdf->Line($mesq+32,$altprod,202,$altprod); //borda dos dados dos produtos				  
		  //$altprod = $altprod + 1;
		  $totadopv = $totadopv + $linha_pm->pm_valtot;
		  
		  $alturadagradedosprod = $alturadagradedosprod + 15;
		  
		// se for 14 itens, cria uma nova página
		  $qtdprodprod = $qtdprodpv / 14; 
		  if (is_int($qtdprodprod)){
//		  if (($qtdprodpv == "13") || ($qtdprodpv == "26") || ($qtdprodpv == "39") || ($qtdprodpv == "52") || ($qtdprodpv == "65")){
			$pdf->SetFont('helvetica','',10); // Define a fonte 				  			
			$pdf->SetXY(80, 274); // Define as margens do documento 							  
			$pdf->MultiCell(50,3,'continua na próxima página',0,'J'); // Imprime o obs gerais
	
	  	    $pdf->AddPage(); // adiciona a primeira pagina 
			$altprod = 60;
			include("pedido_rel_novo_cabecalho.php");
		    $alturadagradedosprod = 12;
			$pdf->Rect($mesq,$msup,206,293); //borda total da pagina A4
			$pdf->SetFont('helvetica','',8); // Define a fonte 		
			$pdf->Text($mesq+6,$msup+57,'Produtos'); // Imprime o pv	
			$qtdprodprod = 0; $qtdprodpv = 0;
		  }
		  
		}
		  $sql_ped = "SELECT ped_desconto FROM pedcad WHERE ped_num = '$edtNumPed' AND ped_loja = '$ljcod'";
		  $query_ped = mysql_query($sql_ped, $conexao);
		  if (mysql_num_rows($query_ped) > 0){
	  		$linha_ped = mysql_fetch_object($query_ped);
		  }
		  
		  $descontodopv = $linha_ped->ped_desconto;		  
		  $totalgeraldopv = $totadopv - $descontodopv;
		
		if (($ljcod == "04") || ($ljcod == "06") || ($ljcod == "07") || ($ljcod == "37") ){
		  //total dos produtos
		  $pdf->SetFont('helvetica','B',8); // Define a fonte 				  		
		  $altprod = $altprod+4;		
		  $alturadagradedosprod = $alturadagradedosprod + 4;  
	      $pdf->SetXY($mesq+31,$altprod); // Define as margens do documento 							
	      $pdf->Cell(142,3,'TOTAL DOS PRODUTOS',0,0,'R'); // Imprime o ponto ref cliente				
		  $pdf->SetFont('helvetica','B',12); // Define a fonte 				  				
		  $pdf->Cell(28,3,'R$ '.number_format($totadopv,'2',',','.'),0,0,'R'); // Imprime o ponto ref cliente			

		  //desconto do pedido
		  $pdf->SetFont('helvetica','B',8); // Define a fonte 				  		
		  $altprod = $altprod+4;
		  $alturadagradedosprod = $alturadagradedosprod + 4;  
	      $pdf->SetXY($mesq+31,$altprod); // Define as margens do documento 
		  $percdesconto = ($descontodopv * 100) / $totadopv;
		  $percdesconto = number_format($percdesconto,'2',',','.');
		  $descontototal = 'DESCONTO '.$percdesconto.' %';
	      $pdf->Cell(142,3,$descontototal,0,0,'R'); // Imprime o ponto ref cliente				
		  $pdf->SetFont('helvetica','B',12); // Define a fonte 				  				
		  $pdf->Cell(28,3,'R$ '.number_format($descontodopv,'2',',','.'),0,0,'R'); // Imprime o ponto ref cliente			

		  //total do pedido
		  $pdf->SetFont('helvetica','B',11); // Define a fonte 				  				  
		  $altprod = $altprod+4;				  
		  $pdf->Line($mesq+120,$altprod,202,$altprod); //borda dos dados dos produtos				  
		  $altprod = $altprod+2;		
		  $alturadagradedosprod = $alturadagradedosprod + 4;  
	      $pdf->SetXY($mesq+31,$altprod); // Define as margens do documento 							
	      $pdf->Cell(142,3,'TOTAL GERAL DO PEDIDO',0,0,'R'); // Imprime o ponto ref cliente				
		  $pdf->SetFont('helvetica','B',12); // Define a fonte 				  				
		  $pdf->Cell(28,3,'R$ '.number_format($totalgeraldopv,'2',',','.'),0,0,'R'); // Imprime o ponto ref cliente			
		}else{
		  $pdf->SetFont('helvetica','B',11); // Define a fonte 				  		
		  $altprod = $altprod+3;
	      $pdf->SetXY($mesq+31,$altprod); // Define as margens do documento 				
	      $pdf->Cell(142,3,'TOTAL GERAL DO PEDIDO',0,0,'R'); // Imprime o ponto ref cliente	
		  $pdf->SetFont('helvetica','B',12); // Define a fonte 				  				
		  $pdf->Cell(28,3,'R$ '.number_format($totadopv,'2',',','.'),0,0,'R'); // Imprime o ponto ref cliente			
		}
		$pdf->Rect($mesq+2,$msup+54,202,$alturadagradedosprod); //borda dos dados dos produtos		

	}
		$altprod = $altprod+7;	
	//fim da impressao dos produtos do pv	

	//inicio da impressao das obs
	if ($linha_cli->ped_obs != ""){
	  $aumentaaltura = 'S';
	 // $altprod = $altprod+20;
	  $pdf->Rect($mesq+2,$altprod,202,15); //borda das obs gerais
	  $pdf->SetFont('helvetica','',7); // Define a fonte 				  			
	  $altprod = $altprod+4;	
	  $pdf->Text($mesq+6,$altprod,'Observações Gerais'); // Imprime o tit das obs	
	  $altprod = $altprod+1;	
	  $pdf->SetXY($mesq+6,$altprod); // Define as margens do documento 						
	  $pdf->MultiCell(196,3,$linha_cli->ped_obs,0,'J'); // Imprime o obs gerais
	}
/*
	if ($linha_cli->ped_obsprod != ""){
	  $aumentaaltura = 'S';		
	  $pdf->Rect($mesq+134,$altprod,70,19); //borda das obs pv	
	  $pdf->SetFont('helvetica','',7); // Define a fonte 				  				  
	  $altprod = $altprod+4;	
	  $pdf->Text($mesq+138,$altprod,'Observações Pedidos de Fábrica'); // Imprime o pv		
	  $altprod = $altprod+1;		  
	  $pdf->SetXY($mesq+138,$altprod); // Define as margens do documento 							
	  $pdf->MultiCell(64,3, $linha_cli->ped_obsprod,0,'J'); // Imprime o obs gerais	
	  $altprod = $altprod-5;		  
	}
*/	
	  if ($aumentaaltura == 'S'){
		  $altprod = $altprod + 12;
	  }	
	//fim da impressao das obs

		  //echo $qtdprodpv;	
/*		  
  if ( ($qtdprodpv > 12) ){
  	   $pdf->SetFont('helvetica','',10); // Define a fonte 				  			
	   $pdf->SetXY(80, 260); // Define as margens do documento 							  
	   $pdf->MultiCell(50,3,'continua na próxima página',0,'J'); // Imprime o obs gerais
	   $pdf->AddPage(); // adiciona a primeira pagina 
   	   $altprod = 60;
	   $pdf->Rect($mesq,$msup,206,293); //borda total da pagina A4
	   include("pedido_rel_novo_cabecalho.php");
  } // fim do if ($qtdprodpv > 3){
*/
	$alturadosdetpag = $altprod;				  	
	$altprod = $altprod+4;
/*
	if ($altprod >= 150){
	  $pdf->AddPage(); // adiciona a primeira pagina 
   	  $altprod = 60;
	  $alturadosdetpag = 56;				  		  
	  $pdf->Rect($mesq,$msup,206,293); //borda total da pagina A4
	  include("pedido_rel_novo_cabecalho.php");
	}
*/
	if ($qtdprodpv > 11) {
	  $pdf->SetFont('helvetica','',10); // Define a fonte 				  			
	  $pdf->SetXY(80, 274); // Define as margens do documento 							  
	  $pdf->MultiCell(50,3,'continua na próxima página',0,'J'); // Imprime o obs gerais

	  $pdf->AddPage(); // adiciona a primeira pagina 
	  $altprod = 60;
	  include("pedido_rel_novo_cabecalho.php");
//	  $alturadagradedosprod = 12;
	  $pdf->Rect($mesq,$msup,206,293); //borda total da pagina A4
	  $pdf->SetFont('helvetica','',8); // Define a fonte 		
	  $qtdprodprod = 0; $qtdprodpv = 0;
	}


    $pdf->SetFont('helvetica','',8); // Define a fonte 				  				
	$pdf->Text($mesq+6,$altprod,'Detalhes do Pagamento'); // Imprime o tit das obs	
	//inicio da impressao da forma de pagto (fin e car)
	//$pdf->Rect($mesq+86,$altprod+10,118,10); //borda da forma de pagto
	if ($linha_pg->cxm_din  == 0){ $dinheiro      = ''; }else{  $dinheiro      = 'Espécie: R$ '.number_format($linha_pg->cxm_din,'2',',','.').'   '; }
	if ($linha_pg->cxm_de   == 0){ $deposito      = ''; }else{  $deposito      = 'Depósito: R$ '.number_format($linha_pg->cxm_de,'2',',','.').'   '; }
	if ($linha_pg->cxm_tc   == 0){ $transferencia = ''; }else{  $transferencia = 'Transferência: R$ '.number_format($linha_pg->cxm_tc,'2',',','.').'   '; }		
	if ($linha_pg->cxm_chd  == 0){ $chequedia     = ''; }else{  $chequedia     = 'Cheque Dia R$ '.number_format($linha_pg->cxm_chd,'2',',','.').'   '; }
	if ($linha_pg->cxm_chpl == 0){ $chequepreloja = ''; }else{  $chequepreloja = 'Cheque Pré Lj R$ '.number_format($linha_pg->cxm_chpl,'2',',','.').'   '; }
	if ($linha_pg->cxm_chp  == 0){ $chequepre     = ''; }else{  $chequepre     = 'Financeira R$ '.number_format($linha_pg->cxm_chp,'2',',','.').'   '; }
	if ($linha_pg->cxm_car  == 0){ $cartao        = ''; }else{  $cartao        = 'Cartão R$ '.number_format($linha_pg->cxm_car,'2',',','.').'   '; }
	if ($linha_pg->cxm_ccd  == 0){ $cartacred     = ''; }else{  $cartacred     = 'Carta Crédito R$ '.number_format($linha_pg->cxm_ccd,'2',',','.'); }
	$formadepagto = $dinheiro.$deposito.$transferencia.$chequedia.$chequepreloja.$chequepre.$cartao.$cartacred;
	$altprod = $altprod+1;	
	//echo $altprod;
    $pdf->SetXY($mesq+6,$altprod); // Define as margens do documento 									
	$pdf->SetFont('helvetica','B',8); // Define a fonte 		
	if ($formadepagto == ""){
	  $formadepagto = 'Pedido zerado!';
	}
	$pdf->MultiCell(196,6,$formadepagto,1,'J'); // Imprime a forma d pagto
    $altprod = $altprod + 7;
	$alturadosdetalpag = 13;			
	//fim da impressao dos dados do pv, data e forma d pagto.

	$sql_cxd = "SELECT cxd_cliente, cxd_tipodoc, cxd_plano, cxd_financ, cxd_conta, cxd_banco, cxd_financiador FROM cxdoc 
	             WHERE cxd_pedido = '$edtNumPed' 
	             GROUP BY cxd_financiador, cxd_plano
			     ORDER BY cxd_plano, cxd_financ, cxd_valor ";
    $query_cxd = mysql_query($sql_cxd, $conexao);
    if (mysql_num_rows($query_cxd) > 0){
		$contafinanciador = 0;
	    $pdf->SetY($altprod); // Define as margens do documento 									
		while($linha_cxd = mysql_fetch_object($query_cxd)){
             if ($linha_cxd->cxd_tipodoc == "CA"){ $tipodedoc = "CARTÃO DE CRÉDITO";} 
             if ($linha_cxd->cxd_tipodoc == "CP"){ $tipodedoc = "FINANCEIRA CHEQUE";} 
             if ($linha_cxd->cxd_tipodoc == "CT"){ $tipodedoc = "FINANCEIRA CARNET";} 
             if ($linha_cxd->cxd_tipodoc == "DC"){ $tipodedoc = "FINANCEIRA DÉB CC";} 	
			 
			 $sql_fincarqtd   = "SELECT fin_desc FROM financeira WHERE fin_cod = '".$linha_cxd->cxd_financ."';";
			 $query_fincarqtd = mysql_query($sql_fincarqtd)or die("Erro na consulta do cxdoc!");
			 if(mysql_num_rows($query_fincarqtd) > 0){
			   $linha_fincarqtd = mysql_fetch_object($query_fincarqtd);
		     }

			 $sql_fincar   = "SELECT fp_financeira FROM finplano WHERE fp_cod = '".$linha_cxd->cxd_plano."';";
			 $query_fincar = mysql_query($sql_fincar)or die("Erro na consulta do cxdoc!");
			 if(mysql_num_rows($query_fincar) > 0){
			   $linha_fincar = mysql_fetch_object($query_fincar);
		     }
			 $pdf->SetX(8); // Define as margens do documento 			 
			 $pdf->SetFont('helvetica','B',7); // Define a fonte 		
             if ($linha_cxd->cxd_tipodoc == "CP"){ 			 
			   $pdf->MultiCell(196,5,'Financiador '.$linha_cxd->cxd_financiador.' | '.$tipodedoc.' '.$linha_fincar->fp_financeira.' em '.$linha_fincarqtd->fin_desc.' | CC '.$linha_cxd->cxd_conta.' | Agência '.$linha_cxd->cxd_banco,1,'J'); // Imprime o tit das obs
			 }else{
			   $pdf->MultiCell(196,5,'Financiador '.$linha_cxd->cxd_financiador.' | '.$tipodedoc.' '.$linha_fincar->fp_financeira.' em '.$linha_fincarqtd->fin_desc,1,'J'); // Imprime o tit das obs	
			 }
			 if ($linha_cxd->cxd_financiador != $linha_cli->cli_razao){
				$assfinanciador = $assfinanciador."| ".substr($linha_cxd->cxd_financiador,0,28)." |";
				$contafinanciador = $contafinanciador + 1;
			 }

		/*
		  $altprod = $altprod + 6;
		  $alturadosdetalpag = 50;									
		  $pdf->SetXY($mesq+6,$altprod); // Define as margens do documento 
		
		  $pdf->Table("SELECT cxd_doc, cxd_venc, cxd_valor FROM cxdoc WHERE cxd_pedido = '$edtNumPed' AND cxd_seqpag = '".$linha_cxd->cxd_seqpag."' ORDER BY cxd_doc, cxd_venc ");
		  $pdf->AddCol('cxd_doc',10,'','Documento');
		  $pdf->AddCol('cxd_venc',10,'Vencimento');
		  $pdf->AddCol('cxd_valor',10,'Valor R$','R');
		*/ 
	 // -------------------------------------- INICIO DO CXDOC --------------------------------------------------- 
	 
		  $pdf->SetFont('helvetica','',8); // Define a fonte 				  					  
		  $sql_cxddocs = "SELECT cxd_doc, cxd_venc, cxd_valor FROM cxdoc
		  				   WHERE cxd_pedido = '$edtNumPed' AND cxd_financiador = '".$linha_cxd->cxd_financiador."' and cxd_plano = '".$linha_cxd->cxd_plano."'
		  				  ORDER BY cxd_venc "; //echo $sql_cxddocs;
		  $query_cxddocs = mysql_query($sql_cxddocs, $conexao);
		  $cont = 0;
		  if (mysql_num_rows($query_cxddocs) > 0){
			  $texto = '';
			  while($linha_cxddocs = mysql_fetch_object($query_cxddocs)){
				$cont = $cont + 1;
				if ( ($cont == 3) || ($cont == 6) || ($cont == 9) || ($cont == 12) || ($cont == 15) || ($cont == 18) || ($cont == 21) ){
				  $texto = $texto.'Doc. '.$linha_cxddocs->cxd_doc.' | Venc. '.muda_data_pt($linha_cxddocs->cxd_venc).' | R$ '.number_format($linha_cxddocs->cxd_valor,'2',',','.').'     -     '; //sadfasdfasdfasdfasdf
				}else{
				  $texto = $texto.'Doc. '.$linha_cxddocs->cxd_doc.' | Venc. '.muda_data_pt($linha_cxddocs->cxd_venc).' | R$ '.number_format($linha_cxddocs->cxd_valor,'2',',','.').'          '; //sadfasdfasdfasdfasdf
				}				
		        //$pdf->Text($mesq+12,$altprod+45,'Doc. '.$linha_cxddocs->cxd_doc.' | Venc. '.muda_data_pt($linha_cxddocs->cxd_venc).' | R$ '.number_format($linha_cxddocs->cxd_valor,'2',',','.').' |'); // Imprime o tit das obs								
			  }
			/*
				$pdf->SetFont('helvetica', '', 8);
				$altprod = $altprod + 6;
				$alturadosdetalpag = 50;									
				$pdf->SetXY($mesq+6,$altprod); // Define as margens do documento 
				$pdf->SetWidths(array(10,10,10));
				$pdf->Row(array('Doc','Venc','Valor'));		 
			  */
				$pdf->SetFont('helvetica', '', 8);
			    $altprod = $altprod + 6;
				$alturadosdetalpag = 50;									
				$pdf->SetX(8); // Define as margens do documento 
				$pdf->MultiCell(196,3.5,$texto, 0, 'L');
		  }
		  
	// -------------------------------------- FIM DO CXDOC -----------------------------------------------------		  

		}
	}
	//echo $alturadosdetalpag;
	$pdf->Rect($mesq+2,$alturadosdetpag,202,$alturadosdetalpag); //borda das formas de pagto.	
	//fim da impressao da forma de pagto (fin e car)
	
	
	$pdf->SetFont('helvetica','',10); // Define a fonte 				  			
	$pdf->SetXY(80, 274); // Define as margens do documento 							  
	$pdf->MultiCell(50,3,'continua na próxima página',0,'J'); // Imprime o obs gerais

	$pdf->AddPage(); // adiciona a primeira pagina 
	$altprod = 60;
//	$alturadosdetpag = 56;				  		  
	$pdf->Rect($mesq,$msup,206,293); //borda total da pagina A4
	include("pedido_rel_novo_cabecalho.php");

	//inicio da impressao das obs extras 
	$pdf->SetFont('helvetica','',8); // Define a fonte 				  				
    $altprod = $altprod + $alturadosdetalpag;
	$pdf->Text($mesq+6,$altprod,'Observações Extras'); // Imprime o tit das obs		

	$sql_poe   = "SELECT poe_desc FROM pedcadobsextra;";
	$query_poe = mysql_query($sql_poe,$conexao);
	if(mysql_num_rows($query_poe) > 0){
		$alturadasobsextras = 25;
	    $altprod = $altprod + 1;						
		$pdf->SetXY(5,$altprod); // Define as margens do documento 
		while($linha_poe = mysql_fetch_object($query_poe)){
			$pdf->SetFont('helvetica', '', 9);			
			$pdf->MultiCell(200,3.5,'* '.$linha_poe->poe_desc, 1, 'J');				  			  
			$alturadasobsextras = $alturadasobsextras + 3.5;
			//$pdf->Text($mesq+6,$altprod+58,'* '.$linha_poe->poe_desc); // Imprime o tit das obs		
			//$altprod = $altprod + 4;
		} // fim do while($linha_poe = mysql_fetch_object($query_poe)){
	} // fim do if(mysql_num_rows($query_poe) > 0){
	//$altprod = $altprod+10;
	//$pdf->Rect($mesq+2,$altprod,202,$alturadasobsextras); //borda das obs extras	
	//fim da impressao das obs extras	
	
    $altprod = $altprod + 36 + $alturadasobsextras;			
	//inicio da impressao das assinaturas
	$pdf->SetXY(6,$altprod); // Define as margens do documento 
	$pdf->SetFont('helvetica','',8); // Define a fonte 	
	$pdf->Text($mesq+6,$altprod,'Recebi do(a) Sr(a) cliente que assina abaixo, o valor deste pedido de venda de acordo com as condições de pagamento impressas neste documento.'); // Imprime info de recibo
	
	//pegando o gerente
	$sql_ger   = "SELECT ger_nome FROM gerentes WHERE ger_loja= '$ljcod';";
	$query_ger = mysql_query($sql_ger,$conexao);
	if(mysql_num_rows($query_ger) > 0){
		$linha_ger = mysql_fetch_object($query_ger);
	}
	//pegando o vendedor
	$sql_vend   = "SELECT ven_nome FROM vendedor WHERE ven_cod = '".$linha_cli->ped_vend."';";
	$query_vend = mysql_query($sql_vend,$conexao);
	if(mysql_num_rows($query_vend) > 0){
		$linha_vend = mysql_fetch_object($query_vend);
	}
	//echo $alturadasobsextras;
	$pdf->SetFont('helvetica','',7); // Define a fonte 	
	$pdf->Text($mesq+10,$altprod+8,'_____________________________________'); // Imprime assinatura cliente
	$pdf->Text($mesq+10,$altprod+12,'Cliente(a): '.substr($linha_cli->cli_razao,0,28)); // Imprime assinaturas cliente
	if ($assfinanciador != ""){
	  if ($contafinanciador == "1"){
	   $pdf->Text($mesq+10,$altprod+18,'_____________________________________'); // Imprime assinatura cliente
	  }else if ($contafinanciador == "2"){
	   $pdf->Text($mesq+10,$altprod+18,'_____________________________________   _____________________________________'); // Imprime assinatura cliente
	  }else if ($contafinanciador >= "3"){
	   $pdf->Text($mesq+10,$altprod+18,'_____________________________________   _____________________________________   _____________________________________'); // Imprime assinatura cliente
	  }
	  $pdf->Text($mesq+10,$altprod+22,'Fin.: '.$assfinanciador); // Imprime assinaturas cliente
	}
	$pdf->Text($mesq+76,$altprod+8,'_____________________________________'); // Imprime assinatura vendedor
	$pdf->Text($mesq+76,$altprod+12,'Vendedor(a): '.substr($linha_vend->ven_nome,0,28)); // Imprime assinaturas cliente
	$pdf->Text($mesq+140,$altprod+8,'_____________________________________'); // Imprime assinatura gerente
	$pdf->Text($mesq+140,$altprod+12,'Gerente(a): '.substr($linha_ger->ger_nome,0,28)); // Imprime assinaturas gerente
	//fim da impressao das assinaturas

	// inicio da pesquisa de opiniao dos clientes
	$sql_pesq = "SELECT ped_pesqopiniao FROM pedcad WHERE ped_num = '$edtNumPed' AND ped_loja = '$ljcod' AND ped_pesqopiniao = 'S'";
	$query_pesq = mysql_query($sql_pesq, $conexao);
	if (mysql_num_rows($query_pesq) > 0){
		$pdf->AddPage(); // adiciona a primeira pagina 
		$altprod = 56;
		$pdf->Rect($mesq,$msup,206,293); //borda total da pagina A4
		include("pedido_rel_novo_cabecalho.php");
		$pdf->Rect($mesq+2,$altprod,202,10);
		$altprod = $altprod + 7;	
        $pdf->SetFont('helvetica','',16); // Define a fonte 			  		
		$pdf->Text($mesq+6,$altprod,'Respostas da Pesquisa de Opinião');
				
        $sql_quest = "SELECT DISTINCT ppop_cod, ppop_pergunta 
	 			   			     FROM pedcadpesqopiniaoperg, pedcadpesqopiniaoresult 
					   		    WHERE ppore_pergunta = ppop_cod AND ppop_ativo = 'S' AND ppore_pedido = '$edtNumPed'
							 ORDER BY ppop_cod;"; // echo $sql_quest;
	    $query_quest = mysql_query($sql_quest,$conexao)or die("Erro no questionario!");
	    if(mysql_num_rows($query_quest) > 0){
	     $iperg = 0;
		 while($linha_quest = mysql_fetch_object($query_quest)){
		  $iperg = 1 + $iperg;	 
		  $altprod = $altprod + 5;			
	      $pdf->SetFont('helvetica','',12); // Define a fonte 			  
		  if ($linha_quest->ppop_cod == '5'){
		   $pdf->Rect($mesq+2,$altprod,202,50);	  			  
		  }else{
		   $pdf->Rect($mesq+2,$altprod,202,30);	  			  
	      }

		  $altprod = $altprod + 26;					  
		  $altprodperg = $altprod - 18;		
		  $pergunta = $iperg.') '.$linha_quest->ppop_pergunta;			  		  
		  $pdf->Text($mesq+8,$altprodperg,$pergunta);		  

		  
		  $sql_resp = "SELECT DISTINCT ppor_resposta, ppore_pergunta, ppore_resposta, ppore_obs 
								 FROM pedcadpesqopiniaoresp, pedcadpesqopiniaoresult 
								WHERE ppore_resposta = ppor_cod AND ppore_pergunta = '".$linha_quest->ppop_cod."' AND 
									  ppore_pedido = '$edtNumPed'
					ORDER BY ppore_cod;"; //echo $sql_resp;
		  $query_resp = mysql_query($sql_resp,$conexao)or die("Erro no questionario!");
		  if(mysql_num_rows($query_resp) > 0){
		   $altprodpergr = $altprodperg + 5; $respostas = '';
		   while($linha_resp = mysql_fetch_object($query_resp)){
	        $pdf->SetFont('helvetica','',10); // Define a fonte 
			if ($linha_resp->ppore_pergunta == '5') {						  
			 $respostas = $linha_resp->ppore_obs;							
			}else{
		      if ( ($linha_resp->ppore_resposta == '8') || ($linha_resp->ppore_resposta == '12') ) { 
			    $respostas = $respostas.'| '.$linha_resp->ppor_resposta.' ( '.$linha_resp->ppore_obs.' )  ';				  
			  }else{
			    $respostas = $respostas.'| '.$linha_resp->ppor_resposta.'  ';				  
			  }
			}
		   }
		    $respostas = substr($respostas,0,548);
			$pdf->SetXY($mesq+10, $altprodpergr); // Define as margens do documento 							  
			$pdf->MultiCell(190,4,$respostas,0,'J'); // Imprime o obs gerais
		  }
		 }
		}
		
	}
    // fim da pesquisa de opiniao dos clientes


 	$pdf->Output();  
?>