<?

  if (($lstProg == "1") || ($lstProg == "2") || ($lstProg == "3") || ($lstProg == "4") || ($lstProg == "5") || ($lstProg == "6")){
	   $mostragrupo = " AND pre_grupo = '".$lstProg."' ";
	}else{
	   $mostragrupo = " AND pre_grupo = ''";	
	}
	
	if (substr($edtProd,0,2) == "09"){
	   if ($lstEscala == "9"){  
		 $tipov = "BIZ";
	   }elseif ($lstEscala == "10"){
		 $tipov = "LAP";
	   }else{
		 $tipov = "MOL";   
	   }
	}  

	if ( (substr($edtProd,0,2) == "14") || substr($edtProd,0,2) == "25" ){
      $tipolaclamver  = " AND pre_acabamento = '' ";							
	}else{
		$sql_laca   = "SELECT esc_laca from escala WHERE esc_cod = '".$lstEscala."';"; 
		$query_laca = mysql_query($sql_laca, $conexao);
		if (mysql_num_rows($query_laca) > 0){
			$linha_laca = mysql_fetch_object($query_laca);
			if ($linha_laca->esc_laca == "S") {
			  $sql_fabrica   = "SELECT pro_fabrica from produtos WHERE pro_cod = '".$edtProd."';"; 
			  $query_fabrica = mysql_query($sql_fabrica, $conexao);
			  if (mysql_num_rows($query_fabrica) > 0){ 
				$linha_fabrica = mysql_fetch_object($query_fabrica);
				if ($linha_fabrica->pro_fabrica == '502'){
				  $tipolaclamver  = " AND pre_acabamento = 'LAC' ";
				}
			  }
			}else{
	         $tipolaclamver  = " AND pre_acabamento = '' ";							
			}
		}else{
	      $tipolaclamver  = " AND pre_acabamento = '' ";							
		}
	}

	$sql_precof   = "SELECT DISTINCT pre_precocusto, pro_subgrupo, pre_tipovidro, pro_fabricante, pro_promocional from precos, produtos 
					 WHERE pre_prod = pro_cod ".$mostragrupo." AND pre_prod = '".$edtProd."' AND pre_tipovidro = '".$tipov."' AND 
					 	   pro_foralinha = 'N' ".$tipolaclamver.";";
	// echo $sql_precof;
	$query_precof = mysql_query($sql_precof,$conexao);
	if (mysql_num_rows($query_precof) > 0){
		$linha_precof = mysql_fetch_object($query_precof);
		
		//novo calculo de preco d custo
	    $marcacaofab = '1'; $descontofab = '1';
		$sql_fabmarcacao = "SELECT fab_marcacaoprecos, fab_descontopreco FROM fabricante WHERE fab_cod = '".$linha_precof->pro_fabricante."' AND fab_travapreco = 'S';";
	    $query_fabmarcacao = mysql_query($sql_fabmarcacao,$conexao) or die ("Erro na Consulta 2!");
		if (@mysql_num_rows($query_fabmarcacao) > 0){
			$linha_fabmarcacao = mysql_fetch_object($query_fabmarcacao);
			$marcacaofab = $linha_fabmarcacao->fab_marcacaoprecos;
			$descontofab = $linha_fabmarcacao->fab_descontopreco;			
		}else{
			$marcacaofab = 0;
			$descontofab = '1';
		}

	    $marcacaosgr = '1';
		$sql_sgrmarcacao = "SELECT sgr_marcacaoprecos FROM subgrupo WHERE sgr_cod = '".$linha_precof->pro_subgrupo."';";
	    $query_sgrmarcacao = mysql_query($sql_sgrmarcacao,$conexao) or die ("Erro na Consulta 3!");
		if (@mysql_num_rows($query_sgrmarcacao) > 0){
			$linha_sgrmarcacao = mysql_fetch_object($query_sgrmarcacao);
			$marcacaosgr = $linha_sgrmarcacao->sgr_marcacaoprecos;
		}

		
		// INICIO DA VERIFICACAO DAS PROMOCOES
			$sql_prom = "SELECT prom_valor from promocoes
						  WHERE prom_prod = '$edtProd' AND prom_ativo = 'S' ";
								//echo $sql_prom;
			$query_prom = mysql_query($sql_prom,$conexao);
	   	    if(mysql_num_rows($query_prom) > 0){
			  $linha_prom = mysql_fetch_object($query_prom);
			  $Valunit = 0; $coef = 0;  $desconto = 0;
			  $Valunit = $linha_prom->prom_valor;
			}else{
			  $Valunit = 0; $coef = 0;  $desconto = 0;
		      $Valunit  = $linha_regiao->reg_coef * $linha_precof->pre_precocusto * $marcacaofab  * $marcacaosgr;	
			  $coef = 1 - $linha_regiao->reg_descav1;		
			  $desconto = $Valunit * $coef ; //50% DE DESCONTO 
			  $coef = 1 - $linha_regiao->reg_descav2;		
			  $desconto = $desconto * $coef ;		
			  $coef = 1 - $linha_regiao->reg_descav3;		
			  $desconto = $desconto * $coef ;		
			  $coef = 1 - $descontofab;		
			  $desconto = $desconto * $coef ;		
			}  
		// FIM DA VERIFICACAO DAS PROMOCOES
/*
		// INICIO DA VERIFICACAO DAS LIBERACOES
			$sql_lp = "SELECT lp_valor from liberacaoprecos
						  WHERE lp_pv = '$edtNumPed' AND lp_prod = '$edtProd' AND lp_escala2 = '$lstCor' AND 
						  		lp_grupo = '$lstProg' AND lp_escala1 = '$lstEscala' AND lp_loja = '$ljcod' ";
								//echo $sql_lp;
			$query_lp = mysql_query($sql_lp,$conexao);
	   	    if(mysql_num_rows($query_lp) > 0){
			  $linha_lp = mysql_fetch_object($query_lp);
			  $Valunit = 0; $coef = 0;  $desconto = 0;
			  $Valunit = $linha_lp->lp_valor;
			}else{
			  $Valunit = 0; $coef = 0;  $desconto = 0;
		      $Valunit  = $linha_regiao->reg_coef * $linha_precof->pre_precocusto;	
			  $coef = $linha_regiao->reg_descav1 + $linha_regiao->reg_descav2 ;		
			  $desconto = $Valunit * $coef ;		
			}  
		// FIM DA VERIFICACAO DAS LIBERACOES
*/

		//INICIO - CHECAR SE É PRODUTO PROMOCIONAL
		if ($linha_precof->pro_promocional == "S"){
		  $descontomax = 0;							
		}else{ //FIM - CHECAR SE É PRODUTO PROMOCIONAL
		  $descontomax = $desconto;
		}
	}else{
		$descontomax = 0;					
	}
	//echo $Valunit.'$Valunit'; echo $coef.'$coef'; echo $desconto.'$desconto'; echo $descontomax.'$descontomax';
?>