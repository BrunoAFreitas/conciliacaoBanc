<?
  if (($progrupo == "1") || ($progrupo == "2") || ($progrupo == "3") || ($progrupo == "4") || ($progrupo == "5") || ($progrupo == "6")){
	 if (($edtProd == "12.01") || ($edtProd == "12.02") || ($edtProd == "12.03") || ($edtProd == "12.04") || ($edtProd == "12.05") || ($edtProd == "12.06")){
	   $mostragrupo = " AND pre_grupo = ''";			 
	 }else{
	   $mostragrupo = " AND pre_grupo = '".$progrupo."' ";
	 }
	}else{
	   $mostragrupo = " AND pre_grupo = ''";	
	}
	
	if (substr($edtProd,0,2) == "09"){
	   if ($escala == "9"){  
		 $tipov = "BIZ";
	   }elseif ($escala == "10"){
		 $tipov = "LAP";
		 
 	     $sql_lpp   = "SELECT cor_vidropintado from cores WHERE cor_cod = '".$lstCor."';"; 
		 $query_lpp = mysql_query($sql_lpp, $conexao);
		 if (mysql_num_rows($query_lpp) > 0){
			  $linha_lpp = mysql_fetch_object($query_lpp);
			  if ($linha_lpp->cor_vidropintado == "S") {
				$tipov = "LPP";
			  }
		 }
		 
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

	$sql_precof   = "SELECT DISTINCT pre_precocusto, pro_subgrupo, pre_tipovidro, pro_fabricante from precos, produtos 
					 WHERE pre_prod = pro_cod ".$mostragrupo." AND pre_prod = '".$edtProd."' AND pre_tipovidro = '".$tipov."';"; 
	//echo $sql_precof; echo $tipov.'asdfadfasdfs';
	$query_precof = mysql_query($sql_precof,$conexao);
	if (@mysql_num_rows($query_precof) > 0){
		$linha_precof = mysql_fetch_object($query_precof);

	    $marcacaofab = '1';
		$sql_fabmarcacao = "SELECT fab_marcacaoprecos FROM fabricante WHERE fab_cod = '".$linha_precof->pro_fabricante."';";
	    $query_fabmarcacao = mysql_query($sql_fabmarcacao,$conexao) or die ("Erro na Consulta 1!");
		if (@mysql_num_rows($query_fabmarcacao) > 0){
			$linha_fabmarcacao = mysql_fetch_object($query_fabmarcacao);
			$marcacaofab = $linha_fabmarcacao->fab_marcacaoprecos;
		}

	    $marcacaosgr = '1';
		$sql_sgrmarcacao = "SELECT sgr_marcacaoprecos FROM subgrupo WHERE sgr_cod = '".$linha_precof->pro_subgrupo."';";
	    $query_sgrmarcacao = mysql_query($sql_sgrmarcacao,$conexao) or die ("Erro na Consulta 1!");
		if (@mysql_num_rows($query_sgrmarcacao) > 0){
			$linha_sgrmarcacao = mysql_fetch_object($query_sgrmarcacao);
			$marcacaosgr = $linha_sgrmarcacao->sgr_marcacaoprecos;
		}


		$edtValunit  = $linha_regiao->reg_coef * $linha_precof->pre_precocusto * $marcacaofab  * $marcacaosgr;
		$descontomax = $edtValunit - ($edtValunit * ($linha_regiao->reg_descav1 + $linha_regiao->reg_descav2) ) ;		
	}else{
		$edtValunit  = 0;
		$descontomax = 0;		
	}

?>