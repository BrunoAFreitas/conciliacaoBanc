	<?
	include("conexao2.inc.php");
	include("funcoes2.inc.php");
	include("dglogin1.php");

	$arquivo = "pedido_pag.php"; 
	include("auditoria.php");

	$data  = date("d/m/Y");
    $hora2 = date("H:i:s");
	
 $sql_pesq = "SELECT ped_pesqopiniao FROM pedcad where ped_num = '$edtNumPed' AND ped_loja = '$ljcod' AND ped_pesqopiniao = 'N';";
 $query_pesq = mysql_query($sql_pesq,$conexao)or die("Erro!");
 if(mysql_num_rows($query_pesq) > 0){
   echo "<script>window.location = 'pedcad_pesquisaopiniao.php?edtNumPed=".$edtNumPed."';</script>";
 }else{

	//inicio da checagem se o caixa eh antigo e não está liberado
 	$dtcaixa2 = muda_data_en($dtcaixa);
	$sql_cxantigo   = "SELECT cx_fechado FROM caixa	WHERE cx_fechado = 'S' AND cx_data = '$dtcaixa2' AND cx_loja = '$ljcod';"; //echo $sql_cxantigo;
	$query_cxantigo = mysql_query($sql_cxantigo,$conexao)or die("Erro!");
	if (mysql_num_rows($query_cxantigo) > 0){
		$msg_cxfechado = "Caixa para este dia encontra-se fechado. Não é possível realizar pagamento neste caixa.";
		$naopode = 'S';
	}
	//fim da checagem se o caixa eh antigo e não está liberado
	
    if ($flag == "excluir"){
      if ($tipo == 'CC'){
 	    $sql_exc2   = "UPDATE clientes set cli_credito = cli_credito + '$acred'
                        WHERE cli_razao = '$cli_razao';";
	    $query_exc2 = mysql_query($sql_exc2,$conexao)or die("Erro!");
      }
 	 $sql_exc   = "DELETE FROM cxmov_temp WHERE cxmt_pedido = '$edtNumPed'
                     AND cxmt_tipo = '$tipo' AND cxmt_confirmado = 'N';";
     $query_exc = mysql_query($sql_exc,$conexao)or die("Erro na Exclusão do Pagamento!");
    }

	if ($gravaobs == 'ok'){
		$sql_obs = "UPDATE pedcad SET ped_obs = '$edtObs', ped_obsprod = '$edtObsProd', ped_obspend = '".$edtObsPend."'
                           WHERE ped_num = '$edtNumPed' and ped_loja = '$ljcod';";
		$query_obs = mysql_query($sql_obs,$conexao)or die(mysql_error());
    }


	if($REQUEST_METHOD == "POST"){
       if ($flag == "paga"){
			 $dtcaixa = muda_data_en($dtcaixa);
			 $dtcompra = muda_data_en($dtcaixa);
			 
			 /*
			  * -Aqui iniciam as alterações, foi adicionado o cpf dos clientes na tabela, o campo rec_num esta
			  * sendo alimentado, o vencimento em CP , RS, CC, CD foram alterados para o dia seguite, o CP esta recebendo
			  * valor total, todas estão enviando o vendedor, login, data e hora de acesso, esta gravando o vencimento
			  * original, o financiador e alimentadno o campo rec_fabrica.
			  * 
			  */
			 
			 $sql_cxmov_temp = "SELECT DISTINCT cxmt_financiador, cxmt_tipo,cxmt_valor,
							    cxmt_financ,cxmt_plano, cxmt_coeffin, ped_vend, cli_razao,cli_seq,
								cxmt_numdoc,cxmt_venc,cxmt_agencia,cxmt_banco,cxmt_financiador, cxmt_index,
								cxmt_financ, cli_cgccpf
								FROM cxmov_temp, pedcad, clientes
								WHERE cxmt_pedido = ped_num 
								AND ped_cliente = cli_cgccpf 
								AND cxmt_pedido = '$edtNumPed' 
								AND cxmt_loja = '$ljcod' 
								AND cxmt_confirmado = 'N'";
			 $query_cxmov_temp = mysql_query($sql_cxmov_temp,$conexao)or die("Erro: ".mysql_error());
					 if (mysql_num_rows($query_cxmov_temp) > 0)
					 {
					   $dinheiro      = 0; $chequedia = 0;
					   $chequerpeloja = 0; $acredit   = 0; 
					   $financeira    = 0; $cartao    = 0; 
					   $transferencia = 0; $deposito  = 0;
					   $totcaixa      = 0;
					   
					   while ($linha_cxmov_temp = mysql_fetch_object($query_cxmov_temp)) 
					   {
  
						 if ($linha_cxmov_temp->cxmt_tipo == 'CL') 
						 {
							$cxmtplano  = 0;  
							$cxmtfinanc = 0;
							//inserindo no cxdepchq
    						$sql_cxdepchq = "INSERT INTO cxdepchq (
											 cxdep_num, 
											 cxdep_loja, 
											 cxdep_emp, 
											 cxdep_venc, 
											 cxdep_valor,
                                             cxdep_situacao, 
											 cxdep_pedido,
											 cxdep_vendedor, 
											 cxdep_conta)
											 VALUES ('".$linha_cxmov_temp->cxmt_numdoc."','$ljcod','$codemp',
											 '".$linha_cxmov_temp->cxmt_venc."','".$linha_cxmov_temp->cxmt_valor."',
											 'ABERTO',
											 '".$linha_cxmov_temp->cxmt_pedido."','".$linha_cxmov_temp->ped_vend."',
											 '".$linha_cxmov_temp->cxmt_agencia."');";
                            //echo $sql_cxdepchq;
                            $query_cxdepchq = mysql_query($sql_cxdepchq,$conexao)or die("Erro na Inclusao no cxdoc!");
							//$oes = "";
							
							/*
							 * Alterações Leandra e Bruno 30/01/2013 - Cheque Pré loja
							 * -rec_numdoc incluido.
							 * -vencimento original adcionado.
							 * -cpf do cliente adcionado.
							 * -financiador adcionado
							 * -rec_fabrica = N add
							 */
							$dtincluido=date("Y-m-d");
							$hrincluido=date("H:i:s");
							/*
							 * Checagem dia 05/02/2013;
							 *
							 * Corrigido erro com dtincluido e dtemissao.
							 * rec_chqbanco----ok
							 */
							$sql_chqpl_rec = "INSERT INTO receber (
											  rec_loja,          rec_vencimento,   rec_valor,  
											  rec_situacao,      rec_emissao,      rec_loginemissao,
											  rec_hremissao,     rec_pedido,       rec_vendedor, 
											  rec_chqconta,      rec_numtitulo,    rec_tipodoc,
											  rec_cliente,       rec_incluido,     rec_dtincluido, 
											  rec_hrincluido,    rec_loginincluido,rec_vencoriginal,  
											  rec_numdoc,        rec_fabrica,      rec_financiador,
											  rec_chqbanco
											  )VALUES (
											  '$ljcod', '" . $linha_cxmov_temp->cxmt_venc . "','" . $linha_cxmov_temp->cxmt_valor . "', 
											  'A', '$dtcaixa', '$acelogin',
											  '$hora2', '$edtNumPed', '" . $linha_cxmov_temp->ped_vend . "', 
											  '" . $linha_cxmov_temp->cxmt_agencia . "','$edtNumPed', 'CL',
											  '" . $linha_cxmov_temp->cli_cgccpf . "', 'S', '$dtincluido', 
											  '$hora2', '$acelogin','" . $linha_cxmov_temp->cxmt_venc . "', 
											  '" . $linha_cxmov_temp->cxmt_numdoc . "', 'N','" . $linha_cxmov_temp->cxmt_financiador."',
											  '" . $linha_cxmov_temp->cxmt_banco . "');";
							//echo $sql_chqpl_rec;
							$query_chqpl_rec = mysql_query($sql_chqpl_rec,$conexao)or die("Erro na Inclusao no Contas a Receber 1!");
							/*
							 * Fim Alterações Leandra e Bruno 30/01/2013 - Cheque Pré loja
							 */
                       }

					   if ($linha_cxmov_temp->cxmt_tipo == 'CA') {
                            $cxmtplano  = 0;  $cxmtfinanc = 0;
                            //inserindo no cxdoc
                            $data = muda_data_en($data);
    						$sql_cxdoc = "INSERT INTO cxdoc (cxd_data, cxd_login, cxd_emp, cxd_loja, cxd_doc,
										cxd_pedido, cxd_cliente, cxd_tipodoc, cxd_tipocartao, cxd_venc,
										cxd_valor, cxd_conta, cxd_banco, cxd_financiador, cxd_financ, cxd_incluido,
										cxd_alterado, cxd_dtincluido, cxd_dtalterado, cxd_plano, cxd_coeffin, cxd_index)
									  VALUES ('$dtcaixa','$acelogin','$codemp','$ljcod','".$linha_cxmov_temp->cxmt_numdoc."',
									  	'$edtNumPed','".$linha_cxmov_temp->cli_razao."','".$linha_cxmov_temp->cxmt_tipo."','',
                                        '".$linha_cxmov_temp->cxmt_venc."','".$linha_cxmov_temp->cxmt_valor."',
									  	'".$linha_cxmov_temp->cxmt_agencia."','".$linha_cxmov_temp->cxmt_banco."',
                                        '".$linha_cxmov_temp->cxmt_financiador."','".$linha_cxmov_temp->cxmt_plano."',
                                        'S','','$data','', '".$linha_cxmov_temp->cxmt_financ."','".$linha_cxmov_temp->cxmt_coeffin."','".$linha_cxmov_temp->cxmt_index."' );";
                            $query_cxdoc = mysql_query($sql_cxdoc,$conexao)or die("Erro na Inclusao no cxdoc!");
                            $cxmtplano  = $linha_cxmov_temp->cxmt_plano;
                            $cxmtfinanc = $linha_cxmov_temp->cxmt_financ;
                            $cxmtvalor  = $linha_cxmov_temp->cxmt_valor;
                            //$oes    = "OK";
                            //$planilha   = "";							
                       }

					   if (($linha_cxmov_temp->cxmt_tipo == 'CP') || ($linha_cxmov_temp->cxmt_tipo == 'DC') || ($linha_cxmov_temp->cxmt_tipo == 'CT')) {
                            $cxmtplano  = 0;  $cxmtfinanc = 0;
                            //inserindo no cxdoc
                            $data = muda_data_en($data);
    						$sql_cxdoc = "INSERT INTO cxdoc (cxd_data, cxd_login, cxd_emp, cxd_loja, cxd_doc,
										                     cxd_pedido, cxd_cliente, cxd_tipodoc, cxd_tipocartao, cxd_venc,
										                     cxd_valor, cxd_conta, cxd_banco, cxd_financiador, cxd_financ, cxd_incluido,
										                     cxd_alterado, cxd_dtincluido, cxd_dtalterado, cxd_plano, cxd_coeffin, cxd_index)
									                 VALUES ('$dtcaixa','$acelogin','$codemp','$ljcod','".$linha_cxmov_temp->cxmt_numdoc."',
									  	                     '$edtNumPed','".$linha_cxmov_temp->cli_razao."','".$linha_cxmov_temp->cxmt_tipo."','',
                                                             '".$linha_cxmov_temp->cxmt_venc."','".$linha_cxmov_temp->cxmt_valor."',
									   	                     '".$linha_cxmov_temp->cxmt_agencia."','".$linha_cxmov_temp->cxmt_banco."',
                                                             '".$linha_cxmov_temp->cxmt_financiador."','".$linha_cxmov_temp->cxmt_plano."',
                                                             'S','','$data','', '".$linha_cxmov_temp->cxmt_financ."','".$linha_cxmov_temp->cxmt_coeffin."','".$linha_cxmov_temp->cxmt_index."'  );";
                            $query_cxdoc = mysql_query($sql_cxdoc,$conexao)or die("Erro na Inclusao no cxdoc!");
                            $cxmtplano   = $linha_cxmov_temp->cxmt_plano;
                            $cxmtfinanc  = $linha_cxmov_temp->cxmt_financ;

					        //$planilha   = "OK";
							//$oes = "";							
                       }
                      }		
					  		
							/*
							 * Alterações Leandra e Bruno 19 de abril 2013 - Select para inserir receber RS, CD, CC, TC, DE
							 */

    						$sql_total = "SELECT DISTINCT cxmt_financiador, cxmt_numdoc, cxmt_venc, cxmt_valor, 
														cxmt_pedido, cxmt_agencia,
														cxmt_loja,
														cxmt_confirmado, cxmt_tipo,
														ped_vend, cli_seq, cli_cgccpf,			  
														SUM(cxmt_valor) AS total 
														FROM cxmov_temp, pedcad, clientes
														WHERE cxmt_pedido = ped_num 
														AND ped_cliente = cli_cgccpf 
														AND cxmt_pedido = '$edtNumPed'
														AND cxmt_loja = '$ljcod' 
														AND cxmt_confirmado = 'N'
														GROUP BY cxmt_tipo";
							/*
							 * Fim Alterações Leandra e Bruno 30/01/2013 - Select para inserir receber RS, CD, CC
							 */		
							//echo $sql_total;			   
                            $query_total = mysql_query($sql_total,$conexao)or die("Erro 1!!!!");

					        if (mysql_num_rows($query_total) > 0){
      			             while ($linha_total = mysql_fetch_object($query_total)) {
							  
							  //edição 19 de abril 2013	 
							   
							   
							 if ($linha_total->cxmt_tipo == "DE"){ 
							 $deposito = $linha_total->total;
						
							  $sql_de_rec = "INSERT INTO receber (
												 rec_loja, rec_vencimento,rec_valor,
												 rec_situacao,rec_pedido,rec_vendedor,
												 rec_numtitulo,rec_tipodoc,rec_cliente,
												 rec_emissao,rec_loginemissao,rec_hremissao,
												 rec_incluido,rec_dtincluido,rec_hrincluido,
												 rec_loginincluido,rec_vencoriginal,rec_fabrica,
												 rec_numdoc,rec_depconta,rec_financiador
												 )
												 VALUES (
												 '$ljcod','$linha_total->cxmt_venc','$deposito',
												 'A','$edtNumPed','" . $linha_total->ped_vend."',
												 '$edtNumPed','DE','".$linha_total->cli_cgccpf."',
												 '$dtcaixa','$acelogin','$hrincluido',
												 'S','$dtincluido','$hrincluido',
												 '$acelogin','$linha_total->cxmt_venc','N',
												 '".$linha_total->cxmt_numdoc."',
												 '".$linha_total->cxmt_agencia."',
												 '".$linha_total->cxmt_financiador."');";
												 //echo $sql_de_rec."<br>";
												 $query_de_rec = mysql_query($sql_de_rec,$conexao)or die("Erro na Inclusao no Contas a Receber DE!");
							  }  
							  if ($linha_total->cxmt_tipo == "TC"){ $transferencia = $linha_total->total;
							  
					
							  $sql_tc_rec = "INSERT INTO receber (
												 rec_loja,rec_vencimento,rec_valor,
												 rec_situacao,rec_pedido,rec_vendedor,
												 rec_numtitulo,rec_tipodoc,rec_cliente,
												 rec_emissao,rec_loginemissao,rec_hremissao,
												 rec_incluido,rec_dtincluido,rec_hrincluido,
												 rec_loginincluido,rec_vencoriginal,rec_fabrica,
												 rec_numdoc,rec_depconta,rec_financiador
												 )
												 VALUES (
												 '$ljcod','$linha_total->cxmt_venc','$tranferencia',
												 'A','$edtNumPed','" . $linha_total->ped_vend . "',
												 '$edtNumPed','TC','".$linha_total->cli_cgccpf ."',
												 '$dtcaixa','$acelogin','$hrincluido',
												 'S','$dtincluido','$hrincluido',
												 '$acelogin','$linha_total->cxmt_venc','N',
												 '".$linha_total->cxmt_numdoc ."',
												 '".$linha_total->cxmt_agencia ."',
												 '".$linha_total->cxmt_financiador ."');";
												 //echo $sql_tc_rec."<br>";
												 $query_tc_rec = mysql_query($sql_tc_rec,$conexao)or die("Erro na Inclusao no Contas a Receber TC!");
							  }  
							  //fim edição 19 de abril	 
								 
                              if ($linha_total->cxmt_tipo == "RS"){ $dinheiro = $linha_total->total;
							  /*
							   * Alterações Leandra e Bruno 30/01/2013 - Insert RS
							   * -CPF do cliente ------ ok
							   * -Vencimento ---------- ok
							   * -Data hora e login --- ok
							   * -Vencimento original - ok
							   * -rec_fabrica --------- ok
							   */	 
							  $dtatual = date('d/m/Y');
							  $cont=1;
							  $dataad = somadata($dtatual,$cont);
							  $data_en = muda_data_en($dataad);
							  /*
							   * Checagem 04/02/2013.
							   */
							  $sql_chqpl_rec = "INSERT INTO receber (
												 rec_loja,
												 rec_vencimento,
												 rec_valor,
												 rec_situacao, 
												 rec_pedido,
												 rec_vendedor,
												 rec_numtitulo,
												 rec_tipodoc,
												 rec_cliente,
												 rec_emissao,
												 rec_loginemissao,
												 rec_hremissao,
												 rec_incluido,
												 rec_dtincluido,
												 rec_hrincluido,
												 rec_loginincluido,
												 rec_vencoriginal,
												 rec_fabrica
												 )
												 VALUES (
												 '$ljcod',
												 '$data_en',
												 '$dinheiro',
												 'A',
												 '$edtNumPed',
												 '" . $linha_total->ped_vend . "',
												 '$edtNumPed',
												 'RS',
												 '".$linha_total->cli_cgccpf ."',
												 '$dtcaixa',
												 '$acelogin',
												 '$hrincluido',
												 'S',
												 '$dtincluido',
												 '$hrincluido',
												 '$acelogin',
												 '$data_en','N');";
												 $query_chqpl_rec = mysql_query($sql_chqpl_rec,$conexao)or die("Erro na Inclusao no Contas a Receber 2!");
							/*
							 * Fim Alterações Leandra e Bruno 30/01/2013 - Insert RS
							 */	}
							 
							  
                              if ($linha_total->cxmt_tipo == "CD"){ $chequedia = $linha_total->total; 
							  /*
							   *Alterações Leandra e Bruno 30/01/2013 - Insert CD
							   * -CPF do cliente ------ ok
							   * -Vencimento ---------- ok
							   * -Data hora e login --- ok
							   * -Vencimento original - ok
							   * -rec_fabrica --------- ok
							   */	
							  $dtatual = date('d/m/Y');
							  $cont=1;
							  $dataad = somadata($dtatual,$cont);
							  $data_en = muda_data_en($dataad);
							  
							  $sql_chqpl_rec = "INSERT INTO receber (
												 rec_loja,
												 rec_vencimento,
												 rec_valor,
												 rec_situacao, 
												 rec_pedido,
												 rec_vendedor,
												 rec_numtitulo,
												 rec_tipodoc,
												 rec_cliente,
												 rec_emissao,
												 rec_loginemissao,
												 rec_hremissao,
												 rec_incluido,
												 rec_dtincluido,
												 rec_hrincluido,
												 rec_loginincluido,
												 rec_vencoriginal,
												 rec_fabrica
												 )
												 VALUES (
												 '$ljcod',
												 '$data_en',
												 '$chequedia',
												 'A',
												 '$edtNumPed',
												 '" . $linha_total->ped_vend . "',
												 '$edtNumPed',
												 'CD',
												 '".$linha_total->cli_cgccpf ."',
												 '$dtcaixa',
												 '$acelogin',
												 '$hrincluido',
												 'S',
												 '$dtincluido',
												 '$hrincluido',
												 '$acelogin',
												 '$data_en','N');";
												 
											$query_chqpl_rec = mysql_query($sql_chqpl_rec,$conexao)or die("Erro na Inclusao no Contas a Receber 3!");
											
							/*
							 *FIM Alterações Leandra e Bruno 30/01/2013 - Insert CD
							 */	
							  }
                              if ($linha_total->cxmt_tipo == "CL"){ $chequerpeloja = $linha_total->total; }
                              if ($linha_total->cxmt_tipo == "CC"){ $acredit = $linha_total->total; 
								
							  /*
							   * Alterações Leandra e Bruno 30/01/2013 - Insert CC
							   * -CPF do cliente ------ ok
							   * -Vencimento ---------- ok
							   * -Data hora e login --- ok
							   * -Vencimento original - ok
							   * -rec_fabrica --------- ok
							   */	
							  
							  $dtatual = date('d/m/Y');
							  $cont=1;
							  $dataad = somadata($dtatual,$cont);
							  $data_en = muda_data_en($dataad);
							  
							  $sql_chqpl_rec = "INSERT INTO receber (
												 rec_loja,
												 rec_vencimento,
												 rec_valor,
												 rec_situacao, 
												 rec_pedido,
												 rec_vendedor,
												 rec_numtitulo,
												 rec_tipodoc,
												 rec_cliente,
												 rec_emissao,
												 rec_loginemissao,
												 rec_hremissao,
												 rec_incluido,
												 rec_dtincluido,
												 rec_hrincluido,
												 rec_loginincluido,
												 rec_vencoriginal,
												 rec_fabrica
												 )
												 VALUES (
												 '$ljcod',
												 '$data_en',
												 '$acredit',
												 'A',
												 '$edtNumPed',
												 '" . $linha_total->ped_vend . "',
												 '$edtNumPed',
												 'CC',
												 '".$linha_total->cli_cgccpf ."',
												 '$dtcaixa',
												 '$acelogin',
												 '$hrincluido',
												 'S',
												 '$dtincluido',
												 '$hrincluido',
												 '$acelogin',
												 '$data_en','N');";
												 
											$query_chqpl_rec = mysql_query($sql_chqpl_rec,$conexao)or die("Erro na Inclusao no Contas a Receber 4!");
											
							/*
							 *FIM Alterações Leandra e Bruno 30/01/2013 - Insert CD
							 */	
							  }
                              if (($linha_total->cxmt_tipo == "CP") || ($linha_total->cxmt_tipo == "CT") || ($linha_total->cxmt_tipo == "DC")) {
                                 $financeira = $financeira + $linha_total->total;
                              }
                              if ($linha_total->cxmt_tipo == "CA"){ $cartao = $linha_total->total;  }
                              $totcaixa = $totcaixa + $linha_total->total;
                             }
                            }

         					$sql_cxmov_temp2   = "SELECT cxmt_financ,cxmt_plano, cxmt_coeffin FROM cxmov_temp
        									                WHERE cxmt_pedido = '$edtNumPed' AND cxmt_loja = '$ljcod'
                                                              AND cxmt_plano <> '0'";
                            $query_cxmov_temp2 = mysql_query($sql_cxmov_temp2,$conexao) or die ("Erro: ".mysql_error());
                            $linha_cxmov_temp2 = mysql_fetch_object($query_cxmov_temp2);

					 }
					 $totgeral = valor_mysql($totgeral);
                     if ($totgeral != "0.00"){
                           if ($cxmtplano == "") { $cxmtplano  = 0; };  if ($cxmtfinanc == ""){ $cxmtfinanc = 0; };
                 			$sql_paga = "INSERT INTO cxmov (cxm_login, cxm_data, cxm_pedido, cxm_loja,cxm_valor,
            								                cxm_din, cxm_de, cxm_tc, cxm_chd, cxm_chpl, cxm_chp, cxm_ccd, cxm_car, cxm_dup, cxm_finan, cxm_finplano,
            								                cxm_coef, cxm_comissao, cxm_incluido, cxm_alterado, cxm_dtincluido, cxm_dtalterado)
            							VALUES ('$acelogin','$dtcaixa', '$edtNumPed','$ljcod', $totcaixa, '$dinheiro', '$deposito', '$transferencia',
											    '$chequedia', '$chequerpeloja', '$financeira', '$acredit',
            								    '$cartao', '','$cxmtplano','$cxmtfinanc','".$linha_cxmov_temp2->cxmt_coeffin."','','S','','$data','');";
							//echo $sql_paga;												
                            $query_paga = mysql_query($sql_paga,$conexao)or die("Erro na Inclusão do Pagamento1!");

// ====I=========================== CONCLUIDO 21/01/2011 =========================================== //
							//comissao reduzida para percentual de cartão
							if ($cartao > 0) {
								$sql_param = "SELECT perccomissaocartao FROM parametros";
								$query_param = mysql_query($sql_param,$conexao) or die ("Erro no PARAM!");
								if (mysql_num_rows($query_param) > 0) {
									$linha_param = mysql_fetch_object($query_param);
								}
								//pedmov
								$sql_comissaocar = "UPDATE pedmov SET pm_comissao = '".$linha_param->perccomissaocartao."', 
																	  pm_obs = 'REDUZIDO COMISSAO PARA 2 POR CENTO - VENDA COM CARTAO'
													WHERE pm_comissao = '2.5' AND pm_num = '$edtNumPed' AND pm_lojaloc = '$ljcod';";
								$query_comissaocar = mysql_query($sql_comissaocar,$conexao)or die("Erro na Inclusão do Pagamento 3!");
								//pedcad
								$sql_comissaocar2 = "UPDATE pedcad SET ped_obscomiss = 'REDUZIDO COMISSAO PARA 2 POR CENTO - VENDA COM CARTAO'
													WHERE ped_num = '$edtNumPed' AND ped_loja = '$ljcod';";
								$query_comissaocar2 = mysql_query($sql_comissaocar2,$conexao)or die("Erro na Inclusão do Pagamento 4!");
							} // fim do if ($cartao) > 0) {
// ====F=========================== CONCLUIDO 21/01/2011 =========================================== //								
                     }
						  /*
						   * Alteração: 
						   * -Chamando o campo cxd_financiador 
						   * -Tabelas pedcad e clientes
						   */
                 		  $sql_cxfin = "SELECT DISTINCT cli_cgccpf, 
										ped_vend, cxd_tipodoc,cxd_pedido, 
										cxd_financ, cxd_loja, cxd_financiador,
										cxd_conta, cxd_banco,
										MIN(cxd_venc) as cxd_venc, 
										cxd_coeffin,
										SUM(cxd_valor) as soma_cxd_valor, 
										cxd_valor, cxd_plano, cxd_doc, cxd_index
										FROM cxdoc, pedcad, clientes
										WHERE ped_num = cxd_pedido 
										AND ped_cliente = cli_cgccpf 
										AND cxd_pedido = '$edtNumPed' 
										AND cxd_loja = '$ljcod' 
										GROUP BY cxd_index";
                          //echo $sql_cxfin;
                          $query_cxfin = mysql_query($sql_cxfin,$conexao) or die ("Erro no CXDOC!");
					      if (mysql_num_rows($query_cxfin) > 0) {
   						   $sql_emp   = "SELECT emp_percmasterparcfort, emp_percvisacredfort, emp_percamexcredfort, 
						   				emp_percvisaparcfort, emp_percmaestrodebfort, emp_percvisaparc, emp_percvisadeb, 
										emp_percvisacred FROM empresa";
                           $query_emp = mysql_query($sql_emp,$conexao)or die("Erro 2!!!!");
                           $linha_emp = mysql_fetch_object($query_emp);

                           while ($linha_cxfin = mysql_fetch_object($query_cxfin)){

                           if ($linha_cxfin->cxd_tipodoc == "CA"){
    						$sql_plano   = "SELECT fin_parcelas, fin_cod FROM financeira
                                            WHERE fin_cod = '".$linha_cxfin->cxd_financ."'";
                            $query_plano = mysql_query($sql_plano,$conexao)or die("Erro 3!!!!");
					        if (mysql_num_rows($query_plano) > 0){
                             $linha_plano = mysql_fetch_object($query_plano);
                             $parcelas    = $linha_plano->fin_parcelas;

							 // Para Fortaleza (Ilton)
                          if (($linha_cxfin->cxd_loja == '04') || ($linha_cxfin->cxd_loja == '06') ||
						      ($linha_cxfin->cxd_loja == '07')) {
							 if ($linha_cxfin->cxd_plano == '11') { // MASTERCARD (PARC ou CRED)
                              $perc_cartao = $linha_emp->emp_percmasterparcfort;
                             }
							 if ($linha_cxfin->cxd_plano == '22') { // MASTERCARD (PARC ou CRED)
                              $perc_cartao = $linha_emp->emp_percmasterparcfort;
                             }
                             if ($linha_cxfin->cxd_plano == '12') { // VISA PARCELADO
                              $perc_cartao = $linha_emp->emp_percvisacredfort;
                             }
                             if ($linha_cxfin->cxd_plano == '21') { // VISA CREDITO A VISTA
                              $perc_cartao = $linha_emp->emp_percvisacredfort;
                             }
                             if ($linha_cxfin->cxd_plano == '14') { // AMERICAM CREDITO (CRED ou PARC)
                              $perc_cartao = $linha_emp->emp_percamexcredfort;
                             }							 							 							 
                             if ($linha_plano->fin_cod != '1'){
							 	if ($linha_cxfin->cxd_plano == '12') { // VISA PARCELADO
                              		$perc_cartao = $linha_emp->emp_percvisaparcfort;
								}
							 	if ($linha_cxfin->cxd_plano == '21') { // VISA PARCELADO
                              		$perc_cartao = $linha_emp->emp_percvisaparcfort;
								}
								
                             }
                             if ($linha_cxfin->cxd_plano == '16'){ // MASTERCARD CREDITO A VISTA
                              $perc_cartao = $linha_emp->emp_percmaestrodebfort;
                             }							 
                           }else{
								 // Para as demais Lojas (Ilton)
								 if (($linha_cxfin->cxd_plano == '11') || ($linha_cxfin->cxd_plano == '22') || ($linha_cxfin->cxd_plano == '12') || 
								 	 ($linha_cxfin->cxd_plano == '21') || ($linha_cxfin->cxd_plano == '13') || ($linha_cxfin->cxd_plano == '14')) {
								  $perc_cartao = $linha_emp->emp_percvisacred;
								 }
								 if ($linha_plano->fin_cod != '1'){
								  $perc_cartao = $linha_emp->emp_percvisaparc;
								 }
						    }
						   }
                             if ($linha_cxfin->cxd_plano == '5'){ // VISA ELECTRON
                              $perc_cartao = $linha_emp->emp_percvisadeb;
                             }
							 
                             $juros   = ($linha_cxfin->cxd_valor * $perc_cartao)/100 ;
                             $liquido = $linha_cxfin->cxd_valor - $juros;

                            $dataatual = muda_data_pt($dtcaixa);
                            for($x=1; $x <= $parcelas; $x++){
                             $parcatual = $x;
                             $dataatual = somadata($dataatual,30);
                             $dataatual = muda_data_en($dataatual);
							 
							 // o problema do chapinha eh aqui = 16-09-2011 = verificar
							 /* // retirado dia 07-05-2013
                             $sql_ppc = "INSERT INTO planprotcar (
										 ppc_pedido,  
										 ppc_loja, 
										 ppc_cartao, 
										 ppc_plano, 
										 ppc_dtcompra, 
										 ppc_dtvencim,
                                         ppc_qtdparc, 
										 ppc_parcini, 
										 ppc_parcfim, 
										 ppc_percentual,
                                         ppc_valortotal, 
										 ppc_valorparc, 
										 ppc_valorjuros, 
										 ppc_valorliquido, 
										 ppc_confirmado )
            							 VALUES ('$edtNumPed','$ljcod','".$linha_cxfin->cxd_financ."', '".$linha_cxfin->cxd_plano."', '$dtcaixa',
                                                '$dataatual','$parcelas','$parcatual','$parcelas','$perc_cartao',
                                                '".$linha_cxfin->soma_cxd_valor."','".$linha_cxfin->cxd_valor."','".$juros."','".$liquido."','1');";
                             //echo $sql_ppc; echo $linha_total->cxmt_tipo.'TIPO-CARTAO'; echo $oes.'PLANILHA';
                             $query_ppc = mysql_query($sql_ppc,$conexao)or die("Erro no Protocolo oes!");
							 */
							 /*
							 * Alterações Leandra e Bruno 30/01/2013 - CARTAO
							 * -Inserir dado no campo rec_numdoc alterado.
							 * -cliente cpf incluido.
							 * 
							 * Checagem 04/02/2013. Consertado
							 */
							  $sql_ppc_rec = "INSERT INTO receber (
											 rec_pedido,     rec_loja,       rec_tipocar,      rec_planocar,   rec_incluido,      rec_vencimento, 
											 rec_qtdparccar, rec_parcinicar, rec_parcfinalcar, rec_perccar,    rec_valor,         rec_valorparccar, 
											 rec_juroscar,   rec_liquidocar, rec_numtitulo,    rec_situacao,   rec_tipodoc,       rec_numdoc,
											 rec_cliente,    rec_vendedor,   rec_dtincluido,   rec_hrincluido, rec_loginincluido, rec_vencoriginal,
											 rec_fabrica,    rec_financiador,rec_emissao,      rec_loginemissao, rec_hremissao )
											 VALUES (
											 '$edtNumPed',   '$ljcod', '" . $linha_cxfin->cxd_financ . "', '" . $linha_cxfin->cxd_plano . "', 'S', '$dataatual',
											 '$parcelas', '$parcatual', '$parcelas', '$perc_cartao', '" . $linha_cxfin->soma_cxd_valor . "', '" . $linha_cxfin->cxd_valor . "',
											 '" . $juros . "','" . $liquido . "', '$edtNumPed " . "-" . "$parcatual', 'A', 'CA', '" . $linha_cxfin->cxd_doc . "',
											 '" . $linha_cxfin->cli_cgccpf . "', '" . $linha_cxfin->ped_vend . "', '$data', '$hora2', '$acelogin', '$dataatual',
											 'N', '" . $linha_cxfin->cxd_financiador . "', '$dtcaixa', '$acelogin','$hora2');";
											 
							 $query_ppc_rec = mysql_query($sql_ppc_rec,$conexao)or die("Erro no Protocolo oes!");
							 /*
							 * Fim Alterações Leandra e Bruno 30/01/2013 - CARTÃO
							 */
                             $dataatual = muda_data_pt($dataatual);
                            }
                            //$oes   = "";
                           }

                           if (($linha_cxfin->cxd_tipodoc == "CP") || ($linha_cxfin->cxd_tipodoc == "CT") || ($linha_cxfin->cxd_tipodoc == "DC")){
    						$sql_plano   = "SELECT fin_parcelas FROM financeira
                                            WHERE fin_cod = '".$linha_cxfin->cxd_financ."'";
                            $query_plano = mysql_query($sql_plano,$conexao)or die("Erro 4!!!!");
					        if (mysql_num_rows($query_plano) > 0){
                             $linha_plano = mysql_fetch_object($query_plano);
                             $parcelas    = $linha_plano->fin_parcelas;
                            }
							 /* // retirado dia 01-03-2013
                             $sql_ppf = "INSERT INTO planprotfin (
										 ppf_pedido,  
										 ppf_loja, 
										 ppf_dtcompra, 
										 ppf_dtprimvenc,
                                         ppf_qtdchqs, 
										 ppf_retencao,
										 ppf_valorfin, 
										 ppf_plano,
                                         ppf_financ,  
										 ppf_confirmado )
            							 VALUES ('$edtNumPed',
										 '$ljcod',
										 '$dtcaixa',
										 '" . $linha_cxfin->cxd_venc . "',
                                         '$parcelas',
										 '" . $linha_cxfin->cxd_coeffin . "',
										 '" . $linha_cxfin->soma_cxd_valor . "',
                                         '" . $linha_cxfin->cxd_plano . "',
										 '" . $linha_cxfin->cxd_financ . "',
										 '1');";
							 //echo $sql_ppf; echo $linha_total->cxmt_tipo.'TIPO-FINAN'; echo $planilha.'PLANILHA';			
                             $query_ppf = mysql_query($sql_ppf,$conexao)or die("Erro no Protocolo Financeira 1!");
							 */ // fim do retirado dia 01-03-2013
							 /*
							  * Alterações Leandra e Bruno 30/01/2013 - FINANCEIRA
							  * -rec_numdoc incluido.
							  * -Vencimento para dia seguinte alterado.
							  */
							 $dtatual = muda_data_pt($dtcaixa);
							 $cont=1;
							 $dataad = somadata($dtatual,$cont);
							 $data_en = muda_data_en($dataad);
							
							 /*
							  * Checagem 05/02/2013. Adicionado campos rec_emissao hora e login
							  * Adicionado banco, conta e rec_incluido
							  */
 
							 $sql_rec_fin = "INSERT INTO receber (
											 rec_pedido,      rec_loja,       rec_vencimento,
											 rec_qtdchqfin,   rec_retencaofin,rec_valorfin,
											 rec_finplano,    rec_financeira, rec_numtitulo,
											 rec_situacao,    rec_tipodoc,    rec_numdoc,
											 rec_cliente,     rec_valor,      rec_vendedor,
											 rec_dtincluido,  rec_hrincluido, rec_loginincluido,
											 rec_emissao,     rec_hremissao,  rec_loginemissao,
											 rec_vencoriginal,rec_fabrica,    rec_financiador,
											 rec_chqbanco,    rec_chqconta,   rec_incluido
											 )VALUES (
											 '$edtNumPed','$ljcod','" . $linha_cxfin->cxd_venc . "',
											 '$parcelas','" . $linha_cxfin->cxd_coeffin . "','" . $linha_cxfin->soma_cxd_valor . "',
											 '" . $linha_cxfin->cxd_plano . "','" . $linha_cxfin->cxd_financ . "','$edtNumPed',
											 'A','" . $linha_cxfin->cxd_tipodoc . "','" . $linha_cxfin->cxd_cod . "',
											 '" . $linha_cxfin->cli_cgccpf . "','" . $linha_cxfin->soma_cxd_valor . "','" . $linha_cxfin->ped_vend . "',
											 '$dtcaixa','$hora2','$acelogin',
											 '$dtcaixa','$hora2','$acelogin',
											 '" . $linha_cxfin->cxd_venc . "', 'N', '" . $linha_cxfin->cxd_financiador . "',
											 '" . $linha_cxfin->cxd_banco . "', '" . $linha_cxfin->cxd_conta . "', 'S' );";
							//	echo $sql_rec_fin;			 											 
							 $query_rec_fin = mysql_query($sql_rec_fin,$conexao) or die ("Erro no Protocolo Financeira 2!");
							 
							 /*
							  *Alterações Leandra e Bruno 30/01/2013 - FINANCEIRA
							  */
                            }
                           }
                          }

                     if (($totgeral == "0") || ($totgeral == "0,00")){
                            $data = muda_data_en($data);
                           if ($cxmtplano == "") { $cxmtplano  = 0; };  if ($cxmtfinanc == ""){ $cxmtfinanc = 0; };
                 			$sql_paga = "INSERT INTO cxmov (cxm_login, cxm_data, cxm_pedido, cxm_loja,cxm_valor,
            								cxm_din, cxm_chd, cxm_chpl, cxm_chp, cxm_ccd, cxm_car, cxm_dup, cxm_finan, cxm_finplano,
            								cxm_coef, cxm_comissao, cxm_incluido, cxm_alterado, cxm_dtincluido,
            								cxm_dtalterado)
            							VALUES ('$acelogin','$dtcaixa', '$edtNumPed','$ljcod','0', '0', '0', '0', '0', '0',
            								    '0', '0', '0', '0', '0', '0', 'S', '0', '$data', '');";
                            $query_paga = mysql_query($sql_paga,$conexao)or die("Erro na Inclusão do Pagamento 2!");
                     }

				//buscando o caixa do dia para atualização
				$sql_cx   = "SELECT cx_din, cx_chd, cx_chpl, cx_chp, cx_car, cx_dup, cx_ccd FROM caixa
						     WHERE cx_login = '$acelogin' AND cx_data = '$dtcaixa' AND cx_loja = '$ljcod'
							 AND cx_emp = '$codemp';";
				$query_cx = mysql_query($sql_cx,$conexao)or die("Erro na Consulta do Caixa!");
				$linha_cx = mysql_fetch_object($query_cx);
				$din_atu  = $linha_cx->cx_din  + $dinheiro;
				$td_atu  = $linha_cx->cx_tc  + $transferencia;
				$de_atu  = $linha_cx->cx_de  + $deposito;								
				$chd_atu  = $linha_cx->cx_chd  + $chequedia;
				$chpl_atu = $linha_cx->cx_chpl + $chequerpeloja;
				$chp_atu  = $linha_cx->cx_chp  + $financeira;
				$car_atu  = $linha_cx->cx_car  + $cartao;
				$ccd_atu  = $linha_cx->cx_ccd  + $acredit;
				$dup_atu  = $linha_cx->cx_dup;

				//atualizando o caixa do dia
				$sql_atu  = "UPDATE caixa SET cx_din = '$din_atu', cx_de = '$de_atu', cx_tc = '$tc_atu', 
											  cx_chd = '$chd_atu', cx_chpl = '$chpl_atu', cx_chp = '$chp_atu',
								cx_car = '$car_atu', cx_ccd = '$ccd_atu', cx_dup = '$dup_atu', cx_alterado = 'S', cx_dtalterado = '$data'
							WHERE cx_login = '$acelogin' AND cx_data = '$dtcaixa' AND cx_loja = '$ljcod'
								AND cx_emp = '$codemp';";
				$query_atu = mysql_query($sql_atu,$conexao)or die("Erro na Atualização do Caixa!");

                $data = muda_data_en($data);
                $edtPrevEntrega = muda_data_en($edtPrevEntrega);				
				//atualizando o pedido como PAGO
				$sql_ped = "UPDATE pedcad SET ped_situacao = 'P', ped_alterado = 'S', ped_dtalterado = '$data',
                                   ped_dtsituacao = '$data', ped_dtpag = '$dtcaixa', 
								   ped_dtpreventrega = '$edtPrevEntrega', ped_hrpreventrega = '$hora2', ped_loginpreventrega = '$acelogin'
                                   WHERE ped_num = '$edtNumPed' AND ped_loja = '$ljcod';";
				$query_ped = mysql_query($sql_ped,$conexao)or die("Erro na Atualização do Pedido!");

                //atualizando com S o cxmov_temp = N
				$sql_cxmov_temp_s  = "UPDATE cxmov_temp SET cxmt_confirmado = 'S'
                                      WHERE cxmt_pedido = '$edtNumPed' AND cxmt_loja = '$ljcod' AND cxmt_confirmado = 'N'";
				$query_cxmov_temp_s = mysql_query($sql_cxmov_temp_s,$conexao)or die("Erro na Atualização do Caixa!");
				
				$msg_pag = "Pagamento Realizado com Sucesso!";
				$sucesso = ok;
				
                //atualizando clientes
			    $qtdclientes = 1;					  					
				$sql_contacli   = "SELECT ped_cliente FROM pedcad WHERE ped_num = '$edtNumPed' AND ped_loja = '$ljcod';;";
				$query_contacli = mysql_query($sql_contacli,$conexao)or die("Erro na Consulta do Caixa!");
				if (mysql_num_rows($query_contacli) > 0){
				 $linha_contacli = mysql_fetch_object($query_contacli);

				  $sql_contacli2   = "SELECT COUNT(ped_cliente) as qtdclientes FROM pedcad WHERE ped_cliente = '".$linha_contacli->ped_cliente."';;";
				  $query_contacli2 = mysql_query($sql_contacli2,$conexao)or die("Erro na Consulta do Caixa!");
				  if (mysql_num_rows($query_contacli2) > 0){
					  $linha_contacli2 = mysql_fetch_object($query_contacli2);					  
					  $qtdclientes = $linha_contacli2->qtdclientes;
				  }else{
					  $qtdclientes = 1;					  
				  }
				}else{
				  $qtdclientes = 1;					  					
			    }
				
				$sql_cli_upd  = "UPDATE clientes SET cli_dtultcompra = '$data', cli_hrultcompra = '$hora2', cli_vendultcompra = '$vendedor', 
													 cli_pvultcompra = '$edtNumPed', cli_ljultcompra = '$ljcod', cli_qtdpv = '".$qtdclientes."'
                                      WHERE cli_razao = '$cli_razao'";
									  //echo $sql_cli_upd;
				$query_cli_upd = mysql_query($sql_cli_upd,$conexao)or die("Erro na Atualização do Cliente!");

				//abrindo a tela de impressao.
				echo "<script> window.open('pedido_rel.php?flag=finalizar&naoimprimebotoes=ok&edtNumPed=$edtNumPed&lstLoja=$ljcod','Impressão','width=920; height=650; scrollbars=yes');</script>";
				$edtNumPed = "";
				$trava = "disabled";
       }

	   if ($incluirpag == "ok"){
        $dtcaixa = muda_data_en ($dtcaixa);
        $edtVenc = muda_data_en ($edtVenc);
        $edtValor = valor_mysql($edtValor);
		$select_max="SELECT MAX(cxmt_index) as seq FROM cxmov_temp";
		$query_max=mysql_query($select_max);
		$linha_max=mysql_fetch_array($query_max);
		$index=$linha_max['seq']+1;
        if ($financ == "ok"){
          if ($lstFinplano == "0"){
            ?>
             <script>
                alert("Escolha a Financeira ou Cartão");
             </script>
            <?
          }else{
            if ($lstFinanc == "0"){
              ?>
               <script>
                  alert("Escolha o Plano");
               </script>
              <?
            }else{
				$edtCoef = valor_mysql($edtCoef);
				$data = muda_data_en($data);
				for($z=1; $z <= 24; $z++){
					$lstTipo = "lstTipo".$z;
					$lstTipo = $$lstTipo;
					$edtVencDia = "edtVencDia".$z;
					$edtVencMes = "edtVencMes".$z;
					$edtVencAno = "edtVencAno".$z;
					$edtVenc = $$edtVencDia."/".$$edtVencMes."/".$$edtVencAno;
					$edtVenc = muda_data_en($edtVenc);

					$edtNumDoc = "edtNumDoc".$z;
					$edtNumDoc = $$edtNumDoc;
					$edtValor = "edtValor".$z;
					$edtValor = valor_mysql($$edtValor);
					//echo $lstTipo.'lsttipo==';
					if(($lstTipo == "CP") || ($lstTipo == "DC") || ($lstTipo == "CT")){
						$edtConta = "edtConta".$z;
						$edtConta = $$edtConta;
						//$edtAgencia = "edtAgencia".$z;
						//$edtAgencia = $$edtAgencia;
						$edtBanco = "edtBanco".$z;
						$edtBanco = $$edtBanco;

 					    $sql_cxmov_temp_existe = "SELECT * FROM cxmov_temp
									                    WHERE cxmt_pedido = '$edtNumPed' AND cxmt_login = '$acelogin' AND cxmt_loja = '$ljcod'
                                                          AND cxmt_tipo = '$lstTipo' AND cxmt_numdoc = '$edtNumDoc' AND cxmt_venc = '$edtVenc' AND cxmt_financiador = '$edtFinanciador'
                                                          AND cxmt_banco = '$edtBanco' AND cxmt_agencia = '$edtConta' AND cxmt_financ = '$lstFinplano' AND cxmt_plano = '$lstFinanc'
                                                          AND cxmt_confirmado = 'N'";
                        $query_cxmov_temp_existe = mysql_query($sql_cxmov_temp_existe,$conexao)or die("Erro: ".mysql_error());
					    if (mysql_num_rows($query_cxmov_temp_existe) > 0) {
					    }else{
						 if ($edtVenc != "0000-00-00"){
						   //incluindo no cxdoc
						   $sql_inc = "INSERT INTO cxmov_temp (cxmt_data, cxmt_hora, cxmt_pedido, cxmt_login, cxmt_loja, cxmt_tipo, cxmt_valor,
															   cxmt_numdoc, cxmt_venc, cxmt_financiador, cxmt_banco, cxmt_agencia,
															   cxmt_financ, cxmt_plano, cxmt_coeffin, cxmt_cartao, cxmt_coefcar, cxmt_confirmado, cxmt_index)
													  VALUES ('$dtcaixa', '$hora2', '$edtNumPed','$acelogin','$ljcod', '$lstTipo', '$edtValor',
															   '$edtNumDoc','$edtVenc','$edtFinanciador','$edtBanco','$edtConta',
															   '$lstFinplano','$lstFinanc','$edtCoef','','','N', '$index' );";
						   $query_inc = mysql_query($sql_inc,$conexao)or die("Erro na Inclusão do Pagamento cxmov_temp1!");
						 }else{ //fim do else if ($edtVenc != "0000-00-00"){						   
						   $msg_cxfechado = "Não pode lançar data 00/00/0000! Refaça o pagamento!";						 
						 } //fim do if ($edtVenc != "0000-00-00"){						   						 
                		}
					}
					if($lstTipo == "CA"){
					
 					    $sql_cxmov_temp_existe = "SELECT * FROM cxmov_temp
									                    WHERE cxmt_pedido = '$edtNumPed' AND cxmt_login = '$acelogin' AND cxmt_loja = '$ljcod'
                                                          AND cxmt_tipo = '$lstTipo' AND cxmt_numdoc = '$edtNumDoc' AND cxmt_venc = '$edtVenc' AND cxmt_financiador = '$edtFinanciador'
                                                          AND cxmt_banco = '$edtBanco' AND cxmt_agencia = '$edtConta' AND cxmt_financ = '$lstFinplano' AND cxmt_plano = '$lstFinanc'
                                                          AND cxmt_confirmado = 'N'";
                        $query_cxmov_temp_existe = mysql_query($sql_cxmov_temp_existe,$conexao)or die("Erro: ".mysql_error());
					    if (mysql_num_rows($query_cxmov_temp_existe) > 0) {
					    }else{
						 if ($edtVenc != "0000-00-00"){
						   //incluindo no cxdoc
						   $sql_inc = "INSERT INTO cxmov_temp (cxmt_data, cxmt_hora, cxmt_pedido, cxmt_login, cxmt_loja, cxmt_tipo, cxmt_valor,
															   cxmt_numdoc, cxmt_venc, cxmt_financiador, cxmt_banco, cxmt_agencia,
															   cxmt_financ, cxmt_plano, cxmt_coeffin, cxmt_cartao, cxmt_coefcar, cxmt_confirmado, cxmt_index)
													  VALUES ('$dtcaixa', '$hora2', '$edtNumPed','$acelogin','$ljcod', '$lstTipo', '$edtValor',
															   '$edtNumDoc','$edtVenc','$edtFinanciador','$edtBanco','$edtConta',
															   '$lstFinplano','$lstFinanc','$edtCoef','','','N', '$index' );";
						   $query_inc = mysql_query($sql_inc,$conexao)or die("Erro na Inclusão do Pagamento cxmov_temp2!");
						 }else{ //fim do else if ($edtVenc != "0000-00-00"){						   
						   $msg_cxfechado = "Não pode lançar data 00/00/0000! Refaça o pagamento!";						 
						 } //fim do if ($edtVenc != "0000-00-00"){						   						 
                		}
					}
				}
              }
			}
        }else{
         if ($lstTipo == "CC"){
//             $edtCred  = valor_mysql($edtCred);
//             $edtValor = valor_mysql($edtValor);
           if ($edtCred < $edtValor){
             $edtValor = number_format($edtValor,'2',',','.');
             $edtCred  = number_format($edtCred,'2',',','.');
             echo "<script> alert('Valor do Crédito informado, R$ $edtValor, maior do que o crédito do cliente, R$ $edtCred !'); </script>";
           }else{
             $lstFinplano = 0; $lstFinanc = 0;
             
 			 $sql_cxmov_temp_existe = "SELECT * FROM cxmov_temp
			 	                    WHERE cxmt_pedido = '$edtNumPed' AND cxmt_login = '$acelogin' AND cxmt_loja = '$ljcod'
                                    AND cxmt_tipo = '$lstTipo' AND cxmt_numdoc = '$edtNumDoc' AND cxmt_venc = '$edtVenc' AND cxmt_financiador = '$edtFinanciador'
                                    AND cxmt_banco = '$edtBanco' AND cxmt_agencia = '$edtConta' AND cxmt_financ = '$lstFinplano' AND cxmt_plano = '$lstFinanc'
                                    AND cxmt_confirmado = 'N'";
             $query_cxmov_temp_existe = mysql_query($sql_cxmov_temp_existe,$conexao)or die("Erro: ".mysql_error());
			 if (mysql_num_rows($query_cxmov_temp_existe) > 0) {
			 }else{
			   if ($edtVenc != "0000-00-00"){
				 //incluindo no cxdoc
				  $sql_inc   = "INSERT INTO cxmov_temp (cxmt_data,cxmt_hora,cxmt_pedido,cxmt_login,cxmt_loja,cxmt_tipo,cxmt_valor,
													 cxmt_numdoc,cxmt_venc,cxmt_financiador,cxmt_banco,cxmt_agencia,
													 cxmt_financ,cxmt_plano,cxmt_coeffin,cxmt_cartao,cxmt_coefcar,cxmt_confirmado, cxmt_index)
											 VALUES ('$dtcaixa','$hora2','$edtNumPed','$acelogin','$ljcod','$lstTipo','$edtValor',
													 '$edtNumdoc','$edtVenc','$edtFinanciador','$edtBco','$edtAgencia',
													 '$lstFinplano','$lstFinanc','$edtCoef','','','N' , '$index');";
				  $query_inc = mysql_query($sql_inc,$conexao)or die("Erro na Inclusão do Pagamento cxmov_temp3!");
			   }else{ //fim do else if ($edtVenc != "0000-00-00"){						   
				 $msg_cxfechado = "Não pode lançar data 00/00/0000! Refaça o pagamento!";						 
			   } //fim do if ($edtVenc != "0000-00-00"){						   						 
             }
    	 	 $sql_updcli   = "UPDATE clientes SET cli_credito = cli_credito - $edtValor
                                     WHERE cli_razao = '$cli_razao';";
             //echo $sql_updcli;
    	  	 $query_updcli = mysql_query($sql_updcli,$conexao)or die("Erro na Inclusão do Pagamento 6!");

           }
 		 }else{
               $lstFinplano = 0; $lstFinanc = 0;
               
		    $sql_cxmov_temp_existe = "SELECT * FROM cxmov_temp
				                      WHERE cxmt_pedido = '$edtNumPed' AND cxmt_login = '$acelogin' AND cxmt_loja = '$ljcod'
                                      AND cxmt_tipo = '$lstTipo' AND cxmt_numdoc = '$edtNumDoc' AND cxmt_venc = '$edtVenc' AND cxmt_financiador = '$edtFinanciador'
                                      AND cxmt_banco = '$edtBanco' AND cxmt_agencia = '$edtConta' AND cxmt_financ = '$lstFinplano' AND cxmt_plano = '$lstFinanc'
                                      AND cxmt_confirmado = 'N'";
            $query_cxmov_temp_existe = mysql_query($sql_cxmov_temp_existe,$conexao)or die("Erro: ".mysql_error());
		    if (mysql_num_rows($query_cxmov_temp_existe) > 0) {
		    }else{
			   if ($edtVenc != "0000-00-00"){
				 $sql_inc   = "INSERT INTO cxmov_temp (cxmt_data, cxmt_hora, cxmt_pedido, cxmt_login, cxmt_loja, cxmt_tipo, cxmt_valor,
												   cxmt_numdoc, cxmt_venc, cxmt_financiador, cxmt_banco, cxmt_agencia,
												   cxmt_financ, cxmt_plano, cxmt_coeffin, cxmt_cartao, cxmt_coefcar, cxmt_confirmado, cxmt_index)
										   VALUES ('$dtcaixa', '$hora2', '$edtNumPed','$acelogin','$ljcod', '$lstTipo', '$edtValor',
												   '$edtNumdoc','$edtVenc','$edtFinanciador','$edtBco','$edtAgencia',
												   '$lstFinplano','$lstFinanc','$edtCoef','','','N', '$index' );";
			   //echo $sql_inc;
				 $query_inc = mysql_query($sql_inc,$conexao)or die("Erro na Inclusão do Pagamento cxmov_temp4!");
			   }else{ //fim do else if ($edtVenc != "0000-00-00"){						   
				 $msg_cxfechado = "Não pode lançar data 00/00/0000! Refaça o pagamento!";						 
			   } //fim do if ($edtVenc != "0000-00-00"){						   						 
    	  	}
         }
		}//fim do if ($financ == "ok"){
       }//fim do if ($incluirpag == "ok"){
	} //fim do if($REQUEST_METHOD == "POST"){
?>
<html>
<head>
	<link rel="stylesheet" href="est_big.css" type="text/css">
	<title>:: Gercom.NET - Pagamento do Pedido ::</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<script language="JavaScript">
		function submit_action(campo,caminho){
			//postando para a verificacao;
			if(campo.value != ""){
				document.formPedPag.action= caminho; 
				document.formPedPag.method= 'post'; 
				document.formPedPag.submit();
			}
		}
		
		function enviar(caminho,mensagem){
			var confirma = confirm(mensagem);
			if(mensagem){
				if(confirma){
					document.formPedPag.action=caminho;
					document.formPedPag.method='post';
					document.formPedPag.submit();
				}
			}else{
				document.formfecha.action=caminho;
				document.formfecha.method='post';
				document.formfecha.submit();
			}
		}
		
		function submit_action2(caminho){
			//postando para a verificacao;
			document.formPedPag.action= caminho; 
			document.formPedPag.method= 'post'; 
			document.formPedPag.submit();
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

		function TiraDizima(valor){
			var ind = valor.indexOf('.');
			var decimal = "";

			if(ind != -1){
				decimal = valor.substr(ind+1,2);
				valor = valor.substr(0,ind+1) + decimal;
				return formata_valor(valor);
			}else{
				return formata_valor(valor);
			}
		}

		//converte campos do formato 5000 para 5.000,00;
		function converte(obj){
			if(obj.value != ""){
				var valor = obj.value;
				obj.value = formata_valor(valor);
			}
		}

		function calc_total(){
			if(document.formPedPag.edtValunit.value != "" && document.formPedPag.edtqtd.value != ""){
				var valunit = document.formPedPag.edtValunit.value;
				valunit = valor_java(valunit);
				var qtd = document.formPedPag.edtqtd.value;
				qtd = valor_java(qtd);
				var total = valunit * qtd;
				total = total.toString();
				document.formPedPag.edtTotal.value = TiraDizima(total.toString());
			}
		}

		var arr_finparc = new Array();
		var arr_fincoef = new Array();
		<? 
			$sql = "SELECT fin_cod, fin_coef, fin_parcelas FROM financeira;";
			$query = mysql_query($sql,$conexao);
			$i = 0;
			while ($linha = mysql_fetch_object($query)) {
				?>
					arr_fincoef["<?=$linha->fin_cod?>"] = "<?=$linha->fin_coef?>";
					arr_finparc["<?=$linha->fin_cod?>"] = "<?=$linha->fin_parcelas?>";					
				<?
				$i++;
			} 
		?>
		
		function Busca_Dados(list) {
			//limpa os edits
			formPedPag.edtQtdParc.value = "";
			formPedPag.edtCoef.value = "";
			//exibe o coef e as parcelas
			if(list.value != 0 && list.value != ""){
				formPedPag.edtQtdParc.value = arr_finparc[list.value];
				formPedPag.edtCoef.value = formata_valor(arr_fincoef[list.value]);
			}
		}
		
		function habilita(list, ind){
			if(list.value == "CP"){
				//eval("formPedPag.lstcartao"+ind+".disabled = true;");					 
				eval("formPedPag.edtBanco"+ind+".disabled = false;");
				//eval("formPedPag.edtAgencia"+ind+".disabled = false;");
				//eval("formPedPag.edtFinanciador"+ind+".disabled = false;");
				eval("formPedPag.edtConta"+ind+".disabled = false;");
				eval("formPedPag.edtNumDoc"+ind+".disabled = false;");
				eval("formPedPag.edtVencDia"+ind+".disabled = false;");
				eval("formPedPag.edtVencMes"+ind+".disabled = false;");
				eval("formPedPag.edtVencAno"+ind+".disabled = false;");
				eval("formPedPag.edtValor"+ind+".disabled = false;");					 
			}else{
				//eval("formPedPag.edtFinanciador"+ind+".disabled = true;");
				eval("formPedPag.edtBanco"+ind+".disabled = true;");
				//eval("formPedPag.edtAgencia"+ind+".disabled = true;");
				eval("formPedPag.edtConta"+ind+".disabled = true;");
				//eval("formPedPag.lstcartao"+ind+".disabled = true;");
				if(list.value == "CA"){
					//eval("formPedPag.lstcartao"+ind+".disabled = false;");
					eval("formPedPag.edtNumDoc"+ind+".disabled = false;");
					eval("formPedPag.edtVencDia"+ind+".disabled = false;");
					eval("formPedPag.edtVencMes"+ind+".disabled = false;");
					eval("formPedPag.edtVencAno"+ind+".disabled = false;");
					eval("formPedPag.edtValor"+ind+".disabled = false;");
				}
				if(list.value == "0"){
					//eval("formPedPag.edtFinanciador"+ind+".disabled = true;");
					eval("formPedPag.edtBanco"+ind+".disabled = true;");
					//eval("formPedPag.edtAgencia"+ind+".disabled = true;");
					eval("formPedPag.edtConta"+ind+".disabled = true;");
					//eval("formPedPag.lstcartao"+ind+".disabled = true;");
					eval("formPedPag.edtNumDoc"+ind+".disabled = true;");
					eval("formPedPag.edtVencDia"+ind+".disabled = false;");
					eval("formPedPag.edtVencMes"+ind+".disabled = false;");
					eval("formPedPag.edtVencAno"+ind+".disabled = false;");
					eval("formPedPag.edtValor"+ind+".disabled = true;");
				}
			}
		}
		
		//funcao para abrir um popup em qualquer lugar da tela		
		POP_win=2
		POP_client=1
		POP_tot=0
		
		function popup(url,w,h,halign,valign,parent){
		
			var t=0,l=0
			box=new getbox(parent)
			
			switch(halign){
				case "":
				case "left":
					l=0
				break;
				case "right":
					l=box.width-w
				break;
				case "center":
					l=(box.width-w)/2
				break;
				default:
					if(typeof(halign)=="string"){
						if(halign.search(/%/g)!="-1"){
							l=(box.width-w)*parseInt(halign)/100
						}else{
							l=parseInt(halign)
						}
					}else if(typeof(halign)=="number"){
						l=halign
					}
			}
		
			switch(valign){
				case "":
				case "top":
					t=0
				break;
				case "bottom":
					t=box.height-h
				break;
				case "center":
					t=(box.height-h)/2
				break;
				default:
					if(typeof(valign)=="string"){
						if(valign.search(/%/g)!="-1"){
							t=(box.height-h)*parseInt(valign)/100
						}else{
							t=parseInt(valign)
						}
					}else if(typeof(valign)=="number"){
						t=valign
					}
			}
			t+=box.top
			l+=box.left
		
			window.open(url,"","width="+w+",height="+h+",top="+t+",left="+l+",scrollbars=yes")
		}
		
		function getbox(parent){
			if(typeof(parent)=="undefined")parent=0
			this.top=0
			this.left=0
			this.width=screen.width
			this.height=screen.height
			if(parent==2){
				this.top=window.screenTop
				this.left=window.screenLeft
				this.width=document.body.offsetWidth
				this.height=document.body.offsetHeight
			}else if(parent==1){
				this.width=screen.availWidth
				this.height=screen.availHeight
			}else if(parent==0){
				this.width=screen.width
				this.height=screen.height
			}else{
				this.top=parent.screenTop
				this.left=parent.screenLeft 
				this.width=parent.document.body.offsetWidth 
				this.height=parent.document.body.offsetHeight
			}
		}
		
		function trava_campos(){
			if(document.formPedPag.edtDIN.value != "0,00" || document.formPedPag.edtCHD.value != "0,00"){	
				document.formPedPag.lstFinanc.disabled = true;
				document.formPedPag.lstFinplano.disabled = true;
				document.formPedPag.edtQtdParc.disabled = true;
				document.formPedPag.edtCoef.disabled = true;
				for(var i=1; i <= 24; i++){
					eval("document.formPedPag.lstTipo"+i+".disabled = true;");
					/*eval("document.formPedPag.edtNumDoc"+i+".disabled = true;");
					eval("document.formPedPag.edtVenc"+i+".disabled = true;");	
					eval("document.formPedPag.edtValor"+i+".disabled = true;");
					eval("document.formPedPag.lstcartao"+i+".disabled = true;");
					//eval("document.formPedPag.edtFinanciador"+i+".disabled = true;");
					eval("document.formPedPag.edtConta"+i+".disabled = true;");
					eval("document.formPedPag.edtBanco"+i+".disabled = true;");*/
				} 
			}else{
				document.formPedPag.lstFinanc.disabled = false;
				document.formPedPag.lstFinplano.disabled = false;
				document.formPedPag.edtQtdParc.disabled = false;
				document.formPedPag.edtCoef.disabled = false;
				for(var i=1; i <= 24; i++){
					eval("document.formPedPag.lstTipo"+i+".disabled = false;");
					/*eval("document.formPedPag.edtNumDoc"+i+".disabled = true;");
					eval("document.formPedPag.edtVenc"+i+".disabled = true;");	
					eval("document.formPedPag.edtValor"+i+".disabled = true;");
					eval("document.formPedPag.lstcartao"+i+".disabled = true;");
					//eval("document.formPedPag.edtFinanciador"+i+".disabled = true;");
					eval("document.formPedPag.edtConta"+i+".disabled = true;");
					eval("document.formPedPag.edtBanco"+i+".disabled = true;");*/
				}
			}
		}
		
		function repetir(campo){
			if(campo.name == "edtConta1"){ 
				//armazenando em variáveis
				var parc   = parseInt(document.formPedPag.edtQtdParc.value);
				var tipo   = document.formPedPag.lstTipo1.selectedIndex;
				var numdoc = document.formPedPag.edtNumDoc1.value;
				var vencdia   = document.formPedPag.edtVencDia1.value;
				var vencmes   = parseInt(document.formPedPag.edtVencMes1.value);
				var vencano   = parseInt(document.formPedPag.edtVencAno1.value);
				var valor  = document.formPedPag.edtValor1.value;
				//var cartao = document.formPedPag.lstcartao1.selectedIndex;
				//var financiador  = document.formPedPag.edtFinanciador1.value;
				var conta  = document.formPedPag.edtConta1.value;
				var banco  = document.formPedPag.edtBanco1.value;
				//incrementando o nº do documento
				var zeros = 0;
				for(var a=0; a<= numdoc.length; a++){	
					if(numdoc.substr(a,1) == "0"){
						zeros = zeros + 1;						
					}else{
						break;
					}
				}				
				//repetindo os valores nos campos
				for(var i=2;  i<=parc; i++){
					eval("document.formPedPag.lstTipo"+i+".selectedIndex = "+tipo+";");			
					numdoc = parseFloat(numdoc) + 1;
					numdoc = numdoc.toString();
					if(zeros > 0){
						for(var b=1; b<=zeros; b++){
							numdoc = "0"+numdoc;
						}
					}
					eval("document.formPedPag.edtNumDoc"+i+".value       = '"+numdoc+"';");					
					eval("document.formPedPag.edtVencDia"+i+".value      = '"+vencdia+"';");
					
					//incrementando mes e ano no vencimento				
					vencmes = parseInt(vencmes) + 1;					
					if(vencmes == 13){
						vencmes = 01;
						vencano = vencano + 1;	
					}
					
					/*if(vencmes == 1 || vencmes == 2 || vencmes == 3 || vencmes == 4 || vencmes == 5 || vencmes == 6 || vencmes == 7 || vencmes == 8 || vencmes == 9){	
						vencmes = "0"+vencmes;
						vencmes = parseInt(vencmes);	
					}*/
					
					eval("document.formPedPag.edtVencMes"+i+".value = '"+vencmes+"';");
					eval("document.formPedPag.edtVencAno"+i+".value = '"+vencano+"';");				
					eval("document.formPedPag.edtValor"+i+".value   = '"+valor+"';");
					eval("document.formPedPag.edtValor"+i+";"); //convertendo o valor para o formato brasileiro
					//eval("document.formPedPag.lstcartao"+i+".selectedIndex = "+cartao+";");
					//eval("document.formPedPag.edtFinanciador"+i+".value 		   = '"+conta+"';");
					eval("document.formPedPag.edtConta"+i+".value 		   = '"+conta+"';");
					eval("document.formPedPag.edtBanco"+i+".value 		   = '"+banco+"';");
					
					//habilitando campos
					eval("document.formPedPag.edtNumDoc"+i+".disabled  = false;");
					eval("document.formPedPag.edtVencDia"+i+".disabled = false;");	
					eval("document.formPedPag.edtVencMes"+i+".disabled = false;");	
					eval("document.formPedPag.edtVencAno"+i+".disabled = false;");	
					eval("document.formPedPag.edtValor"+i+".disabled   = false;");
					/*if(document.formPedPag.lstTipo1.value == "CA"){
						eval("document.formPedPag.lstcartao"+i+".disabled = false;");
					}else{
						eval("document.formPedPag.lstcartao"+i+".disabled = true;");
					}*/
					//eval("document.formPedPag.edtFinanciador"+i+".disabled  = false;");
					eval("document.formPedPag.edtConta"+i+".disabled  = false;");
					eval("document.formPedPag.edtBanco"+i+".disabled  = false;");
				}
			}
		}
		
		function valida(obj,ind){
			nome_campo = obj.name;
			if(nome_campo.substr(0,(nome_campo.length - 1)) == "edtVencMes"){
				if(obj.value > 12){
					alert("Mês Inválido! 01-12");
				//	obj.focus();
				}else{
					mes = obj.value;
					dia = eval("document.formPedPag.edtVencDia"+ind+".value;");
					if(mes == 1 || mes == 3 || mes == 5 || mes == 7 || mes == 8 || mes == 10 || mes == 12){
						if(dia > 31){
							alert("Dia Inválido! Este mês tem 31 dias.");
							eval("document.formPedPag.edtVencDia"+ind+".focus();");
						}	
					}else{
						//fevereiro
						if(mes == 2){
							if(dia > 29){
								alert("Dia Inválido! Este mês tem 29 dias.");
								eval("document.formPedPag.edtVencDia"+ind+".focus();");
							}		
						}else{
							if(mes == 4 || mes == 6 || mes == 9 || mes == 11){
								if(dia > 30){
									alert("Dia Inválido! Este mês tem 30 dias.");
									eval("document.formPedPag.edtVencDia"+ind+".focus();");
								}
							}
						}
					}
				}
			}
			if(nome_campo.substr(0,(nome_campo.length - 1)) == "edtVencAno"){
				//validando o ano
				var ano_atual = new Date();
				ano_atual = ano_atual.getYear();
				var ano = obj.value;
				if(ano.length < 4){
					alert("Digite o Ano Completo! Ex:1999,2003");
					//obj.focus();
				}else{
					if(obj.value < ano_atual){
						alert("Ano Inválido! O ano atual é: "+ano_atual);
						//obj.focus();
					}
				}
			}			
		}
	</script>
</head>

<body background="../imagens/fundomain.jpg" bottommargin="5" leftmargin="5" rightmargin="5" topmargin="5">
<?
   if ($menuoff != "ok") {
     include("menu_java.php");
   }
?>
<form name="formPedPag">
<table width="100%" border="1" cellspacing="2" cellpadding="2" bordercolor="#004000">
    <tr>
     <td align="center" width="100%" bgcolor="#004000"><font size='3' color="#FFFFFF"><u><strong>PAGAMENTO DE PEDIDO</strong></u></font></td>
    </tr>
    <tr><td></td></tr>
<?			if(isset($msg_cxfechado)){
				echo "<tr>";
					echo "<td align='center' style='background-color:#F00; color:white; font-size:16px'>".$msg_cxfechado."</td>";
				echo "</tr>";
			}
?>
    <tr>
        <td>
			<table align="center" width="100%" cellspacing="2" cellpadding="2">
            <tr><td bgcolor="#008000"><font color="#FFFFFF"><? if(!isset($msg_pag)){ ?></font></td></tr>
			<tr>
            <td bgcolor='#FFFFFF'><font size="2">Data do caixa:</font>
			   <?
				//iniciando o campo com a data de hoje
				if(!isset($dtcaixa)){
					$dtcaixa = date("d/m/Y");
				}
			   ?>
			  <input name="dtcaixa" type="text" id="dtcaixa" value="<?=muda_data_pt($dtcaixa)?>" size="12" maxlength="10" <?=$trava_btn?> style="background-color: #C0FFC0; font-weight:bold;">
               <font size="2">Número do Pedido:</font>
              <input type="text" name="edtNumPed" value="<?=$edtNumPed?>" onBlur="submit_action(this, 'pedido_pag.php?menuoff=<?=$menuoff?>');" <?=$trava_btn?> style="background-color: #C0FFC0; font-weight:bold; width:125">
              <input type="hidden" name="hdPed" value="<?=$edtNumPed?>">
              
		  <? //if ($acelogin == "jalen"){
			  if ($edtPrevEntrega == ''){
				$sql_dtpreventregapf = "SELECT lj_dtpreventregamin, lj_dtpreventregapfmin FROM loja WHERE lj_cod = '$ljcod';";
				$query_dtpreventregapf = mysql_query($sql_dtpreventregapf, $conexao);
				if(mysql_num_rows($query_dtpreventregapf) > 0){
				 $linha_dtpreventregapf = mysql_fetch_object($query_dtpreventregapf);				  
				}
				$sql_preventrega = "SELECT pc_pedvend FROM pedcomp WHERE pc_pedvend = '$edtNumPed' AND pc_loja = '$ljcod' AND 
										  								 (pc_situacao = 'A' or pc_situacao = 'P');";
				$query_preventrega = mysql_query($sql_preventrega, $conexao);
				if(mysql_num_rows($query_preventrega) > 0){
				   $PrevEntrega = $linha_dtpreventregapf->lj_dtpreventregapfmin;
				}else{
				   $PrevEntrega = $linha_dtpreventregapf->lj_dtpreventregamin;					
				}
				   $edtPrevEntrega = somar_dias_uteis($data,$PrevEntrega); 
			  }				   
              ?>

              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                   <font size="2" style="color:red">Data Previsão Entrega:</font>
                  <input type="text" name="edtPrevEntrega" value="<?=$edtPrevEntrega?>" style="background-color: #FF0000; color:#FFF; font-weight:bold; width:94">
		  <? // } ?>                  
				</td>
		</tr>
                
<? if ($naopode != "S"){ ?>                
<?
				//$trava = "";
				//$data = muda_data_en($dtcaixa);
				$dtcaixa = muda_data_en($dtcaixa);
				//verifica se existe caixa para este usuario
				if($edtNumPed != ""){
					$sql_cx = "SELECT cx_loja, cx_data FROM caixa
							   WHERE cx_loja = '$ljcod' AND cx_data = '$dtcaixa';";
					$query_cx = mysql_query($sql_cx, $conexao);
					if(mysql_num_rows($query_cx) > 0){
?>
				<?
				echo "<tr>";
					echo "<td>";
						$sql_ped = "SELECT ped_cliente, ped_emissao, cli_razao, cli_cgccpf, ped_tipove, pp_desc, ped_valliq, ven_cod, 
											ven_nome, ped_situacao, ped_desconto
				  					FROM pedcad, clientes, planopag, vendedor
									WHERE ped_num = '$edtNumPed' AND cli_cgccpf = ped_cliente AND ped_vend = ven_cod
										AND pp_cod = ped_tipove AND ped_loja = '$ljcod' AND (ped_situacao = 'F' or ped_situacao = 'D') ;";
						//echo $sql_ped;
						$query_ped = mysql_query($sql_ped, $conexao);
						if(mysql_num_rows($query_ped) > 0){
							$linha_ped = mysql_fetch_object($query_ped);
                            if ($linha_ped->ped_situacao == "F"){
							  $situacao = 'FECHADO';
                            }else if ($linha_ped->ped_situacao == "D"){
							  $situacao = 'PENDÊNCIA';
							}
							$edtFinanciador = $linha_ped->cli_razao;
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
							$desconto = $linha_ped->ped_desconto;
							$valprod = $valprod_saida + $valprod_entra;
							$valliq = ($valprod + $acrescimo) - $desconto;
					?>
							<table border='1' bordercolor='#FFFFFF' bgcolor='#004000' align="center" cellpadding="0" width="100%">
								<tr>
									<td bgcolor='#008000' align='left'><font size='2' color="#FFFFFF">Cliente:</font></td>
									<td bgcolor='#008000' align='center'><font size='2' color="#FFFFFF">Emissão:</font></td>
									<td bgcolor='#008000' align='center'><font size='2' color="#FFFFFF">Forma de Venda:</font></td>
									<td bgcolor='#008000' align='center'><font size='2' color="#FFFFFF">Situação:</font></td>
									<td bgcolor='#008000' align='center'><font size='2' color="#FFFFFF">Vendedor:</font></td>
									<td bgcolor='#008000' align='right'><font size='2' color="#FFFFFF">Valor:</font></td>
								</tr>
								<tr>
									<td bgcolor='#FFFFB9' align='left'><font size='2' color="#FF0000"><?=$linha_ped->cli_razao?></font></td>
									<td bgcolor='#FFFFB9' align='center'><font size='2'color="#FF0000"><?=muda_data_pt($linha_ped->ped_emissao)?></font></td>
									<td bgcolor='#FFFFB9' align='center'><font size='2' color="#FF0000"><?=$linha_ped->pp_desc?></font></td>
									<td bgcolor='#FFFFB9' align='center'><font size='2' color="#FF0000"><?=$situacao?></font></td>
									<td bgcolor='#FFFFB9' align='center'><font size='2' color="#FF0000"><?=$linha_ped->ven_nome?></font></td>
									<td bgcolor='#FFFFB9' align='right'><font size='2' color="#FF0000">R$ <?=number_format($valliq,'2',',','.')?></font></td>
								</tr>
							</table>
             <?
					echo "</td>";
				echo "</tr>";
             ?>
                      <tr><td></td></tr><tr><td></td></tr><tr><td></td></tr><tr><td></td></tr>
                      <tr><td>
                      <table border='1' bordercolor='#000000' bgcolor='#004000' width='100%'><tr><td bgcolor='#DDFFDD'>
							<table align="center" width="100%">
                                <tr>
                                 <td colspan='4' bgcolor="#800000" align='center'><font color='#FFFFFF' size='2'>Escolha o Tipo de Pagamento:</font></td>
                                </tr>
								<tr>
								  <td bgcolor="#DDFFDD" align='center'></td>
                                  <td bgcolor="#DDFFDD" align='center'>
									<select name="lstTipo" style="border:solid 1; font:bold; width:300; height:100; background-color:#C0C0FF;" <?=$trava?>>
										<?
											$sql_td = "SELECT td_cod, td_desc FROM tipodoc where td_loja = 'S' order by td_ordem, td_desc;";
											$query_td = mysql_query($sql_td,$conexao);
											if (mysql_num_rows($query_td) > 0){
												while($linha_td = mysql_fetch_object($query_td)){
													if($linha_td->td_cod == $lstTipo){
														echo "<option value='".$linha_td->td_cod."' selected>".$linha_td->td_desc."</option>";
													}else{
														echo "<option value='".$linha_td->td_cod."'>".$linha_td->td_desc."</option>";
													}
												}
											}
										?>
								  	</select>
                                  </td>
                                  <td bgcolor="#DDFFDD" align='center'>
					                 <input style="border:solid 1; height:25; width:250; background-color:#FFFFC0;" name="btnOK" value="Escolher" type="button" onClick="submit_action(this, 'pedido_pag.php?lstTipo=<?=$lstTipo?>&menuoff=<?=$menuoff?>&formapag=ok&trava=disabled&edtPrevEntrega=<?=$edtPrevEntrega?>');" <?=$trava?>>
                                  </td>
                                </tr>
                            </table>
                        </td></tr></table>
                      </td></tr>

            <? if ($formapag == "ok"){ ?>
               <? if ($lstTipo == "RS"){ ?>
                      <tr><td>
                      <table align='right' bgcolor='#004000'><tr><td bgcolor='#DDFFDD'>
							<table align="center" width="100%">
								<tr>
								  <td colspan='2' bgcolor="#FFFF00" align='center'><font size='2'>ESPÉCIE R$:</font></td>
								</tr>
								<tr>
                                  <td bgcolor="#DDFFDD" align='center'><font size='2' color="#FF0000"><b><u>Digite o Valor R$: </u></b></font><input type="text" name="edtValor" size="15" maxlength="15"></td>
                                  <td bgcolor="#DDFFDD" align='center'>
					                 <input style="border:solid 1; font: bold; height:25; width:150; background-color:#FFFFC0;" name="btnOK" value="Incluir Pagamento" type="button" onClick="submit_action(this, 'pedido_pag.php?incluirpag=ok&lstTipo=<?=$lstTipo?>&menuoff=<?=$menuoff?>&trava=');">
					                 <input style="border:solid 1; font: bold; height:25; width:100; background-color:#FFFFC0;" name="btnVoltar" value="Voltar" type="button" onClick="submit_action(this, 'pedido_pag.php?menuoff=<?=$menuoff?>&formapag=&trava=');">
                                  </td>
                                </tr>
                            </table>
                        </td></tr></table>
                      </td></tr>
               <? } // final do lsttipo = RS ?>
               
			   
			  <?php
			   //DEPOSITO EM CONTA - 18 de abril 2013
			   if ($lstTipo == "DE"){ ?>
                      <tr><td>
                      <table align='right' bgcolor='#004000'><tr><td bgcolor='#DDFFDD'>
                      <table align="center" width="100%">
                        <tr>
                          <td colspan='6' bgcolor="#FFFF00" align='center'><font size='2'>Depósito em Conta:</font></td>
                          </tr>
                          <tr>
                            <td bgcolor="#DDFFDD" align='left'>
                            <font size='2' color="#FF0000"><b><u>Favorecido</u></b></font></td>
                            <td bgcolor="#DDFFDD" align='left'><font size='2' color="#FF0000"><b><u>Data</u></b></font></td>
                            <td bgcolor="#DDFFDD" align='left'>        
                            <font size='2' color="#FF0000"><b><u>Conta</u></b></font></td>
							<!-- 
                            <td bgcolor="#DDFFDD" align='left'>
                            <font size='2' color="#FF0000"><b><u>Identificador</u></b></font></td>
                            -->
                            <td bgcolor="#DDFFDD" align='right'>
                            <font size='2' color="#FF0000"><b><u>Valor R$</u></b></font></td>
                            <td bgcolor="#DDFFDD" align='center' rowspan="2">
                            <input style="border:solid 1; font: bold; height:25; width:160; background-color:#FFFFC0;" name="btnOK" value="Incluir Pagamento" type="button" onClick="submit_action(this, 'pedido_pag.php?incluirpag=ok&lstTipo=<?=$lstTipo?>&menuoff=<?=$menuoff?>&trava=');">
                            <input style="border:solid 1; font: bold; height:25; width:80; background-color:#FFFFC0;" name="btnVoltar" value="Voltar" type="button" onClick="submit_action(this, 'pedido_pag.php?menuoff=<?=$menuoff?>&formapag=&trava=');">
                            </td>
                          </tr>
                          <tr>
                            <td bgcolor="#DDFFDD" align='center'><input style="width:200" type="text" name="edtFinanciador" id="edtFinanciador"></td>
                            <td bgcolor="#DDFFDD" align='center'><input style="width:80" type="text" name="edtVenc"  id="edtVenc"></td>
                            <td bgcolor="#DDFFDD" align='center'><input style="width:80" type="text" name="edtAgencia" id="edtAgencia">
                            </td>
							<!--                             
                            <td bgcolor="#DDFFDD" align='center'><input style="width:120" type="text" name="edtNumdoc" id="edtNumdoc">
                            </td>
                            -->
                            <td bgcolor="#DDFFDD" align='right'><input style="width:100; text-align:right" type="text" name="edtValor"  id="edtValor">    
                            </td>
                          </tr>
                          
                      </table>
                        </td></tr></table>
                      </td></tr>
               <?php } // final do lsttipo = DE
			   //--------------------------------------------------------------------------------//
			   //TRANSFERENCIA
			   if ($lstTipo == "TC"){ ?>
                      <tr><td width="100%">
                      <table align='right' bgcolor='#004000'><tr><td bgcolor='#DDFFDD'>
                      <table align="center" width="100%">
                        <tr>
                          <td colspan='6' bgcolor="#FFFF00" align='left'><font size='2'>Transferência:</font></td>
                          </tr>
                          <tr>
                            <td align='left' bgcolor="#DDFFDD">
                            <font size='2' color="#FF0000"><b><u>Favorecido</u></b></font></td>
                            <td align='left' bgcolor="#DDFFDD"><font size='2' color="#FF0000"><b><u>Data</u></b></font></td>
                            <td align='left' bgcolor="#DDFFDD">        
                            <font size='2' color="#FF0000"><b><u>Conta</u></b></font></td>
                            <!--
                            <td align='left' bgcolor="#DDFFDD">
                            <font size='2' color="#FF0000"><b><u>Identificador</u></b></font></td>
                            -->
                            <td align='right' bgcolor="#DDFFDD">
                            <font size='2' color="#FF0000"><b><u>Valor R$</u></b></font></td>
                            <td rowspan="2" align='center' bgcolor="#DDFFDD">
                            <input style="border:solid 1; font: bold; height:25; width:140; background-color:#FFFFC0;" name="btnOK" value="Incluir Pagamento" type="button" onClick="submit_action(this, 'pedido_pag.php?incluirpag=ok&lstTipo=<?=$lstTipo?>&menuoff=<?=$menuoff?>&trava=');">
                            <input style="border:solid 1; font: bold; height:25; width:60; background-color:#FFFFC0;" name="btnVoltar" value="Voltar" type="button" onClick="submit_action(this, 'pedido_pag.php?menuoff=<?=$menuoff?>&formapag=&trava=');">
                            </td>
                          </tr>
                          <tr>
                            <td bgcolor="#DDFFDD" align='center'><input style="width:180" type="text" name="edtFinanciador" ></td>
                            <td bgcolor="#DDFFDD" align='center'><input style="width:80" type="text" name="edtVenc" ></td>
                            <td bgcolor="#DDFFDD" align='center'><input style="width:80" type="text" name="edtAgencia"></td>
                             <!--
                            <td bgcolor="#DDFFDD" align='center'><input style="width:120" type="text" name="edtNumdoc">
                            </td>
                            -->
                            <td bgcolor="#DDFFDD" align='right'><input style="width:100; text-align:right" type="text" name="edtValor"></td>
                          </tr>
                          
                      </table>
                        </td></tr></table>
                      </td></tr>
               <?php } // final do lsttipo = TC ?>
               
               
			   
			   
			   <? if ($lstTipo == "CD"){ ?>
                      <tr><td>
                      <table align='right' bgcolor='#004000'><tr><td bgcolor='#DDFFDD'>
							<table align="center" width="100%">
								<tr>
								  <td colspan='2' bgcolor="#FFFF00" align='center'><font size='2'>CHEQUE DIA R$:</font></td>
								</tr>
								<tr>
                                  <td bgcolor="#DDFFDD" align='center'><font size='2' color="#FF0000"><b><u>Digite o Valor R$: </u></b></font><input type="text" name="edtValor" size="15" maxlength="15"></td>
                                  <td bgcolor="#DDFFDD" align='center'>
					                 <input style="border:solid 1; font: bold; height:25; width:200; background-color:#FFFFC0;" name="btnOK" value="Incluir Pagamento" type="button" onClick="submit_action(this, 'pedido_pag.php?incluirpag=ok&lstTipo=<?=$lstTipo?>&menuoff=<?=$menuoff?>&trava=');">
					                 <input style="border:solid 1; font: bold; height:25; width:100; background-color:#FFFFC0;" name="btnVoltar" value="Voltar" type="button" onClick="submit_action(this, 'pedido_pag.php?menuoff=<?=$menuoff?>&formapag=&trava=');">
                                  </td>
                                </tr>
                            </table>
                        </td></tr></table>
                      </td></tr>
               <? } // final do lsttipo = CD ?>
               <? if ($lstTipo == "CL"){ ?>
                      <tr><td>
                      <table align='right' bgcolor='#004000'><tr><td bgcolor='#DDFFDD'>
							<table align="center" width="100%">
                                <tr>
								  <td colspan='7' bgcolor="#FFFF00" align='center'><font size='2'><u>CHEQUE PRÉ LOJA:</u></font></td>
                                </tr>
                                <tr>
								  <td bgcolor="#DDFFDD" align='right'>Valor R$:</td>
								  <td bgcolor="#DDFFDD" align='left'>Núm. Doc.:</td>
								  <td bgcolor="#DDFFDD" align='center'>Vencim.:</td>
								  <td bgcolor="#DDFFDD" align='center'>Banco:</td>
								  <td bgcolor="#DDFFDD" align='center'>Agência:</td>
								  <td bgcolor="#DDFFDD" align='left'>Correntista:</td>
                                </tr>
								<tr>
                                  <td bgcolor="#DDFFDD" align='right'><input type="text" name="edtValor" size="12"></td>
                                  <td bgcolor="#DDFFDD" align='left'><input type="text" name="edtNumdoc" size="12"></td>
                                  <td bgcolor="#DDFFDD" align='center'><input type="text" value="<?=muda_data_pt($data)?>" name="edtVenc" size="12"></td>
                                  <td bgcolor="#DDFFDD" align='center'><input type="text" name="edtBco" size="8"></td>
                                  <td bgcolor="#DDFFDD" align='center'><input type="text" name="edtAgencia" size="10"></td>
                                  <td bgcolor="#DDFFDD" align='left'><input type="text" name="edtFinanciador" value="<?=$linha_ped->cli_razao?>" size="40"></td>
                                  <td bgcolor="#DDFFDD" align='center'>
					                 <input style="border:solid 1; font: bold; height:25; width:150; background-color:#FFFFC0;" name="btnOK" value="Incluir Pagamento" type="button" onClick="submit_action(this, 'pedido_pag.php?incluirpag=ok&lstTipo=<?=$lstTipo?>&menuoff=<?=$menuoff?>&trava=');">
					                 <input style="border:solid 1; font: bold; height:25; width:50; background-color:#FFFFC0;" name="btnVoltar" value="Voltar" type="button" onClick="submit_action(this, 'pedido_pag.php?menuoff=<?=$menuoff?>&formapag=&trava=');">
                                  </td>
                                </tr>
                            </table>
                        </td></tr></table>
                      </td></tr>
               <? } // final do lsttipo = CL ?>

               <? if ($lstTipo == "CC"){ ?>
                      <tr><td>
                      <table align='right' bgcolor='#004000'><tr><td bgcolor='#DDFFDD'>
                         <?
							$sql_cc = "SELECT cli_credito FROM clientes where cli_cgccpf = '".$linha_ped->cli_cgccpf."';";
							$query_cc = mysql_query($sql_cc,$conexao);
							if (mysql_num_rows($query_cc) > 0){
							  $linha_cc = mysql_fetch_object($query_cc);
							  $cred_cli = $linha_cc->cli_credito;

                              //$valliq_cc = number_format($valliq,'2',',','.');
                              $valliq_cc = valor_mysql($valliq);
                              //$cred_cli = valor_mysql($cred_cli);
                             // echo $valliq_cc.'$valliq_cc';
                              //echo $cred_cli.'$cred_cli';
                            if (($cred_cli <> '0') || ($cred_cli <> '0.00')){
                            ?>
							<table align="center" width="100%">
                                <tr>
							  	  <td colspan='7' bgcolor="#FFFF00" align='center'><font size='2'><u>A CRÉDITO:</u></font></td>
                                </tr>
                                <tr>
							  	  <td bgcolor="#DDFFDD" align='right'>Valor Crédito da Cliente R$:<b><u><font size='3' color="#FF0000"><?=number_format($cred_cli,'2',',','.')?></font></u></b>
                                    <input style="color:#FFFFFF; font-size:14; font-weight:bold; background-color:#FF0000;" type="hidden" name="edtCred" value="<?=$cred_cli?>" size="12" readonly>
                                  </td>
							  	  <td bgcolor="#DDFFDD" align='left'>Crédito a ser usado R$:
                                    <input type="text" name="edtValor" size="12">
                                  </td>
                                  <td bgcolor="#DDFFDD" align='center'>
					                 <input style="border:solid 1; font: bold; height:25; width:150; background-color:#FFFFC0;" name="btnOK" value="Incluir Pagamento" type="button" onClick="submit_action(this, 'pedido_pag.php?incluirpag=ok&lstTipo=<?=$lstTipo?>&menuoff=<?=$menuoff?>&trava=&cli_razao=<?=$linha_ped->cli_razao?>&vendedor=<?=$linha_ped->ven_cod?>');">
					                 <input style="border:solid 1; font: bold; height:25; width:50; background-color:#FFFFC0;" name="btnVoltar" value="Voltar" type="button" onClick="submit_action(this, 'pedido_pag.php?menuoff=<?=$menuoff?>&formapag=&trava=');">
                                  </td>
                                </tr>
                            </table>
						    <? }else{ ?>
							<table align="center" width="100%">
                                <tr>
							  	  <td bgcolor="#FFFF00" align='center'><font size='2'>CLIENTE SEM CRÉDITO PARA ESTE PAGAMENTO!</font>
				                     <input style="border:solid 1; font: bold; height:25; width:50; background-color:#FFFFC0;" name="btnVoltar" value="Voltar" type="button" onClick="submit_action(this, 'pedido_pag.php?menuoff=<?=$menuoff?>&formapag=&trava=');">
                                  </td>
                                </tr>
                            </table>
						   <? }?>
						 <? } ?>
                        </td></tr></table>
                      </td></tr>
               <? } // final do lsttipo = CC ?>

               <? if ($lstTipo == "CP"){ ?>
                      <tr><td>
                      <table align='right' border='1' bordercolor='#000000' bgcolor='#004000'><tr><td bgcolor='#DDFFDD'>
							<table align="center" width="100%">
								<tr>
								  <td colspan='6' bgcolor="#FFFF00" align='center'><font size='2'>FINANCEIRA:</font></td>
                                                            <tr>
                                                              <td>Financeira</td>
                                                              <td>Plano</td>
                                                              <td align="center">Coeficiente</td>
                                                              <td colspan='3'>Financiador</td>
                                                            </tr>
                                                            <tr>
																<td align="center">
																	<select name="lstFinplano" style="border:solid 1; width:150; background-color:#EEFDE8;">
																		<option value="0" selected>Escolha a Financeira</option>
																		<?
																			$sql_finp = "SELECT fp_cod, fp_financeira FROM finplano WHERE fp_tipo = 'FI' AND fp_ativo = 'S' order by fp_financeira;";
																			$query_finp = mysql_query($sql_finp,$conexao);
																			if (mysql_num_rows($query_finp) > 0) {
																				while($linha_finp = mysql_fetch_object($query_finp)){
																					if($linha_finp->fin_cod == $lstFinancp){
																						echo "<option value='".$linha_finp->fp_cod."'>".$linha_finp->fp_financeira."</option>";
																					}else{
																						echo "<option value='".$linha_finp->fp_cod."'>".$linha_finp->fp_financeira."</option>";
																					}
																				}
																			}
																		?>
																  	</select>
																 </td>
																 <td>
																	<select name="lstFinanc" style="border:solid 1; width:150; background-color:#EEFDE8;" onChange="Busca_Dados(this);" <?=$trava_btn?>>
																		<option value="0">Escolha o Plano</option>
																		<?
																			$sql_fin = "SELECT fin_cod, fin_desc FROM financeira WHERE fin_visivel = 'S' ORDER BY fin_ordem;";
																			$query_fin = mysql_query($sql_fin,$conexao);
																			if (mysql_num_rows($query_fin) > 0) {
																				while($linha_fin = mysql_fetch_object($query_fin)){
																					if($linha_fin->fin_cod == $lstFinanc){
																						echo "<option value='".$linha_fin->fin_cod."' selected>".$linha_fin->fin_desc."</option>";
																					}else{
																						echo "<option value='".$linha_fin->fin_cod."'>".$linha_fin->fin_desc."</option>";
																					}
																				}
																			}
																		?>
																  	</select>
																 </td>
																 <td align="center">
																 	<input style="border:solid 1; width:80; background-color:#F00; color:#FFF" type="text" style="text-align:right;" name="edtCoef" size="6" value="<?=number_format($edtCoef,'2',',','.')?>" maxlength="10" <?=$trava_btn?>>
																 </td>
																 	<input style="border:solid 1; width:40; background-color:#EEFDE8;" type="hidden" style="text-align:right;" name="edtQtdParc" size="6" value="<?=$edtQtdParc?>" maxlength="10" readonly>
                                                                 <td colspan='3' bgcolor="#DDFFDD" align='left'>
                                                                    <input type="text" name="edtFinanciador" value="<?=$linha_ped->cli_razao?>" size="36">
                                                                 </td>

                                                            </tr>
															<tr>
																<td align="center" bgcolor="#FFF2F0" bordercolor="#400000"><font color="#000000">Tipo de Documento</font></td>
																<td align="center" bgcolor="#FFF2F0" bordercolor="#400000"><font color="#000000">Nº Documento</font></td>
																<td align="center" bgcolor="#FFF2F0" bordercolor="#400000"><font color="#000000">Vencimento</font></td>
																<td align="center" bgcolor="#FFF2F0" bordercolor="#400000"><font color="#000000">Valor R$</font></td>
																<td align="center" bgcolor="#FFF2F0" bordercolor="#400000"><font color="#000000">Banco</font></td>
																<td align="center" bgcolor="#FFF2F0" bordercolor="#400000"><font color="#000000">Conta Corrente</font></td>
															</tr>
															<? for($i=1; $i<=24; $i++){ ?>
															<tr>
																<td align="center" >
																	<? $lstTipo = "lstTipo".$i; $lstTipo = $$lstTipo; ?>
																	<select name="lstTipo<?=$i?>" style="border:solid 1; width:150; background-color:#EEFDE8;">
																		<option value="0">--</option>
																		<?
																			$sql_td = "SELECT ft_cod, ft_desc FROM fintipo WHERE ft_tipo = 'FI';";
																			$query_td = mysql_query($sql_td,$conexao);
																			if (mysql_num_rows($query_td) > 0){
																				while($linha_td = mysql_fetch_object($query_td)){
																					if($linha_td->ft_cod == $lstTipo){
																						echo "<option value='".$linha_td->ft_cod."' selected>".$linha_td->ft_desc."</option>";
																					}else{
																						echo "<option value='".$linha_td->ft_cod."'>".$linha_td->ft_desc."</option>";
																					}
																				}
																			}
																		?>
																  	</select>
																	<input type="hidden" name="hdTipo<?=$i?>" value="<?=$lstTipo?>">
																</td>
																<td align="center" >
																	<? $edtNumDoc = "edtNumDoc".$i; $edtNumDoc = $$edtNumDoc; ?>
																	<input style="border:solid 1; background-color:#EEFDE8;" type="text" name="edtNumDoc<?=$i?>"  style="text-align:right;" value="<?=$edtNumDoc?>" size="12" maxlength="10">
																	<input type="hidden" name="hdNumDoc<?=$i?>" value="<?=$edtNumDoc?>">
																</td>
																<td align="center" >
																	<!--- convertendo as variaveis para guardar nos hiddens -->
																	<? $edtVencDia = "edtVencDia".$i; $edtVencDia = $$edtVencDia; ?>
																	<? $edtVencMes = "edtVencMes".$i; $edtVencMes = $$edtVencMes; ?>
																	<? $edtVencAno = "edtVencAno".$i; $edtVencAno = $$edtVencAno; ?>
																	<!--- hiddens para postagem -->
																	<input type="hidden" name="hdVencDia<?=$i?>" value="<?=$edtVencDia?>">
																	<input type="hidden" name="hdVencMes<?=$i?>" value="<?=$edtVencMes?>">
																	<input type="hidden" name="hdVencAno<?=$i?>" value="<?=$edtVencAno?>">
																	<!--- campos de exibição -->
																	<input style="border:solid 1; background-color:#EEFDE8;" type="text" name="edtVencDia<?=$i?>" size="1" value="<?=$edtVencDia?>" maxlength="2" >/
																	<input style="border:solid 1; background-color:#EEFDE8;" type="text" name="edtVencMes<?=$i?>" size="1" value="<?=$edtVencMes?>" maxlength="2" onBlur="valida(this,'<?=$i?>');">/
																	<input style="border:solid 1; background-color:#EEFDE8;" type="text" name="edtVencAno<?=$i?>" size="4" value="<?=$edtVencAno?>" maxlength="4" onBlur="valida(this);">
																</td>
																<td align="center" >
																	<? $edtValor = "edtValor".$i; $edtValor = $$edtValor; ?>
																	<input style="border:solid 1; background-color:#EEFDE8;" type="text" name="edtValor<?=$i?>" size="12" value="<?=$edtValor?>" style="text-align:right;" maxlength="10">
																	<input type="hidden" name="hdValor<?=$i?>" value="<?=$edtValor?>">
																</td>
																<td align="center" >
																	<? $edtBanco = "edtBanco".$i; $edtBanco = $$edtBanco; ?>
																	<input style="border:solid 1; background-color:#EEFDE8;" type="text" name="edtBanco<?=$i?>" size="5" value="<?=$edtBanco?>" maxlength="10">
																	<input type="hidden" name="hdBanco<?=$i?>" value="<?=$edtBanco?>">
																</td>
																<td align="center" >
																	<? $edtConta = "edtConta".$i; $edtConta = $$edtConta; ?>
																	<input style="border:solid 1; background-color:#EEFDE8;" type="text" name="edtConta<?=$i?>" size="10" value="<?=$edtConta?>" maxlength="10" onBlur="repetir(this); javascript: formPedPag.btnInserir.focus();">
																	<input type="hidden" name="hdConta<?=$i?>" value="<?=$edtConta?>">
																</td>
																	<? $edtFinanciador = "edtFinanciador".$i; $edtFinanciador = $$edtFinanciador; ?>
																	<input type="hidden" name="edtFinanciador<?=$i?>" size="5" value="<?=$edtFinanciador?>" maxlength="10" onBlur="repetir(this);">
																	<input type="hidden" name="hdFinanciador<?=$i?>" value="<?=$edtFinanciador?>">
															</tr>
															<? } ?>
                                                            <tr>
                                                            <td bgcolor="#004000" colspan='8' align='center'>
                                                            <table width='100%' bgcolor="#C0FFC0" align='center'>
                                                             <tr>
                                                              <td align='center'>
                        					                    <input style="border:solid 1; font: bold; height:20; width:200; background-color:#C0FFA1;" name="btnInserir" value="Incluir Pagamento" type="button" onClick="submit_action(this, 'pedido_pag.php?incluirpag=ok&financ=ok&menuoff=<?=$menuoff?>&trava=');">
                        					                  </td>
                        					                  <td>
                        					                    <input style="border:solid 1; font: bold; height:20; width:200; background-color:#C0FFA1;" name="btnVoltar" value="Voltar" type="button" onClick="submit_action(this, 'pedido_pag.php?menuoff=<?=$menuoff?>&formapag=&trava=');">
                                                              </td>
                                                             </tr>
                                                            </table>
                                                            </td>
                                                           </tr>
                                </tr>
                            </table>
                        </td></tr></table>
                      </td></tr>
               <? } // final do lsttipo = CA ?>

               <? if ($lstTipo == "CA"){ ?>
                      <tr><td>
                      <table align='right' border='1' bordercolor='#000000' bgcolor='#004000'><tr><td bgcolor='#DDFFDD'>
							<table align="center" width="100%">
								<tr>
								  <td colspan='6' bgcolor="#FFFF00" align='center'><font size='2'>CARTÃO DE CRÉDITO:</font></td>
                                                            <tr>
                                                              <td>Cartão</td>
                                                              <td>Parcelas</td>
                                                              <td align="center">Coeficiente</td>
                                                              <td colspan='3'>Cartão de:</td>
                                                            </tr>
                                                            <tr>
																<td align="center">
																	<select name="lstFinplano" style="border:solid 1; width:240; background-color:#EEFDE8;">
																		<option value="0" selected>Escolha o Cartão</option>
																		<?
																			$sql_finp = "SELECT fp_cod, fp_financeira FROM finplano WHERE fp_tipo = 'CA' AND fp_ativo = 'S' order by fp_financeira;";
																			$query_finp = mysql_query($sql_finp,$conexao);
																			if (mysql_num_rows($query_finp) > 0) {
																				while($linha_finp = mysql_fetch_object($query_finp)){
																					if($linha_finp->fin_cod == $lstFinancp){
																						echo "<option value='".$linha_finp->fp_cod."'>".$linha_finp->fp_financeira."</option>";
																					}else{
																						echo "<option value='".$linha_finp->fp_cod."'>".$linha_finp->fp_financeira."</option>";
																					}
																				}
																			}
																		?>
																  	</select>
																 </td>
																 <td>
																	<select name="lstFinanc" style="border:solid 1; width:150; background-color:#EEFDE8;" onChange="Busca_Dados(this);" <?=$trava_btn?>>
																		<option value="0">Escolha o Plano</option>
																		<?
																			$sql_fin = "SELECT fin_cod, fin_desc FROM financeira where fin_visivel = 'S';";
																			$query_fin = mysql_query($sql_fin,$conexao);
																			if (mysql_num_rows($query_fin) > 0) {
																				while($linha_fin = mysql_fetch_object($query_fin)){
																					if($linha_fin->fin_cod == $lstFinanc){
																						echo "<option value='".$linha_fin->fin_cod."' selected>".$linha_fin->fin_desc."</option>";
																					}else{
																						echo "<option value='".$linha_fin->fin_cod."'>".$linha_fin->fin_desc."</option>";
																					}
																				}
																			}
																		?>
																  	</select>
																 </td>
																 <td align="center">
																 	<input style="border:solid 1; width:80; background-color:#EEFDE8;" type="text" style="text-align:right;" name="edtCoef" size="6" value="<?=number_format($edtCoef,'2',',','.')?>" maxlength="10" <?=$trava_btn?>>
																 </td>
																 	<input style="border:solid 1; width:40; background-color:#EEFDE8;" type="hidden" style="text-align:right;" name="edtQtdParc" size="6" value="<?=$edtQtdParc?>" maxlength="10" readonly>
                                                                 <td colspan='3' bgcolor="#DDFFDD" align='left'>
                                                                    <input type="text" name="edtFinanciador" value="<?=$linha_ped->cli_razao?>" size="36">
                                                                 </td>

                                                            </tr>
															<tr>
																<td align="center" bgcolor="#FFF2F0" bordercolor="#400000"><font color="#000000">Tipo de Documento</font></td>
																<td align="center" bgcolor="#FFF2F0" bordercolor="#400000"><font color="#000000">Nº Documento</font></td>
																<td align="center" bgcolor="#FFF2F0" bordercolor="#400000"><font color="#000000">Vencimento</font></td>
																<td align="center" bgcolor="#FFF2F0" bordercolor="#400000"><font color="#000000">Valor R$</font></td>
															</tr>
															<? for($i=1; $i<=24; $i++){ ?>
															<tr>
																<td align="center" >
																	<? $lstTipo = "lstTipo".$i; $lstTipo = $$lstTipo; ?>
																	<select name="lstTipo<?=$i?>" style="border:solid 1; width:240; background-color:#EEFDE8;">
																		<option value="0">--</option>
																		<?
																			$sql_td = "SELECT ft_cod, ft_desc FROM fintipo WHERE ft_tipo = 'CA';";
																			$query_td = mysql_query($sql_td,$conexao);
																			if (mysql_num_rows($query_td) > 0){
																				while($linha_td = mysql_fetch_object($query_td)){
																					if($linha_td->ft_cod == $lstTipo){
																						echo "<option value='".$linha_td->ft_cod."' selected>".$linha_td->ft_desc."</option>";
																					}else{
																						echo "<option value='".$linha_td->ft_cod."'>".$linha_td->ft_desc."</option>";
																					}
																				}
																			}
																		?>
																  	</select>
																	<input type="hidden" name="hdTipo<?=$i?>" value="<?=$lstTipo?>">
																</td>
																<td align="center" >
																	<? $edtNumDoc = "edtNumDoc".$i; $edtNumDoc = $$edtNumDoc; ?>
																	<input style="border:solid 1; background-color:#EEFDE8;" type="text" name="edtNumDoc<?=$i?>"  style="text-align:right;" value="<?=$edtNumDoc?>" size="12" maxlength="10">
																	<input type="hidden" name="hdNumDoc<?=$i?>" value="<?=$edtNumDoc?>">
																</td>
																<td align="center" >
																	<!--- convertendo as variaveis para guardar nos hiddens -->
																	<? $edtVencDia = "edtVencDia".$i; $edtVencDia = $$edtVencDia; ?>
																	<? $edtVencMes = "edtVencMes".$i; $edtVencMes = $$edtVencMes; ?>
																	<? $edtVencAno = "edtVencAno".$i; $edtVencAno = $$edtVencAno; ?>
																	<!--- hiddens para postagem -->
																	<input type="hidden" name="hdVencDia<?=$i?>" value="<?=$edtVencDia?>">
																	<input type="hidden" name="hdVencMes<?=$i?>" value="<?=$edtVencMes?>">
																	<input type="hidden" name="hdVencAno<?=$i?>" value="<?=$edtVencAno?>">
																	<!--- campos de exibição -->
																	<input style="border:solid 1; background-color:#EEFDE8;" type="text" name="edtVencDia<?=$i?>" size="1" value="<?=$edtVencDia?>" maxlength="2" >/
																	<input style="border:solid 1; background-color:#EEFDE8;" type="text" name="edtVencMes<?=$i?>" size="1" value="<?=$edtVencMes?>" maxlength="2" onBlur="valida(this,'<?=$i?>');">/
																	<input style="border:solid 1; background-color:#EEFDE8;" type="text" name="edtVencAno<?=$i?>" size="4" value="<?=$edtVencAno?>" maxlength="4" onBlur="valida(this);">
																</td>
																<td align="center" >
																	<? $edtValor = "edtValor".$i; $edtValor = $$edtValor; ?>
																	<input style="border:solid 1; background-color:#EEFDE8;" type="text" name="edtValor<?=$i?>" size="12" value="<?=$edtValor?>" style="text-align:right;" maxlength="10">
																	<input type="hidden" name="hdValor<?=$i?>" value="<?=$edtValor?>">
																</td>
																<td align="center" >
																	<? $edtBanco = "edtBanco".$i; $edtBanco = $$edtBanco; ?>
																	<input style="border:solid 1; background-color:#EEFDE8;" type="text" name="edtBanco<?=$i?>" size="5" value="<?=$edtBanco?>" maxlength="10">
																	<input type="hidden" name="hdBanco<?=$i?>" value="<?=$edtBanco?>">
																</td>
																<td align="center" >
																	<? $edtConta = "edtConta".$i; $edtConta = $$edtConta; ?>
																	<input style="border:solid 1; background-color:#EEFDE8;" type="text" name="edtConta<?=$i?>" size="10" value="<?=$edtConta?>" maxlength="10" onBlur="repetir(this); javascript: formPedPag.btnInserir.focus();">
																	<input type="hidden" name="hdConta<?=$i?>" value="<?=$edtConta?>">
																</td>

																	<? $edtFinanciador = "edtFinanciador".$i; $edtFinanciador = $$edtFinanciador; ?>
																	<input type="hidden" name="edtFinanciador<?=$i?>" size="5" value="<?=$edtFinanciador?>" maxlength="10" onBlur="repetir(this);">
																	<input type="hidden" name="hdFinanciador<?=$i?>" value="<?=$edtFinanciador?>">
															</tr>
															<? } ?>
                                                            <tr>
                                                            <td bgcolor="#004000" colspan='8' align='center'>
                                                            <table width='100%' bgcolor="#C0FFC0" align='center'>
                                                             <tr>
                                                              <td align='center'>
                        					                    <input style="border:solid 1; font: bold; height:20; width:200; background-color:#C0FFA1;" name="btnInserir" value="Incluir Pagamento" type="button" onClick="submit_action(this, 'pedido_pag.php?incluirpag=ok&financ=ok&menuoff=<?=$menuoff?>&trava=');">
                        					                  </td>
                        					                  <td>
                        					                    <input style="border:solid 1; font: bold; height:20; width:200; background-color:#C0FFA1;" name="btnVoltar" value="Voltar" type="button" onClick="submit_action(this, 'pedido_pag.php?menuoff=<?=$menuoff?>&formapag=&trava=');">
                                                              </td>
                                                             </tr>
                                                            </table>
                                                            </td>
                                                           </tr>
                                </tr>
                            </table>
                        </td></tr></table>
                      </td></tr>
               <? } // final do lsttipo = CA ?>

            <? } ?>

           <?
                     $dtcaixa = muda_data_en($dtcaixa);
					 
 					 $sql_grade = "SELECT cxmt_tipo,cxmt_valor,cxmt_numdoc,cxmt_venc,cxmt_banco,
                                                   cxmt_agencia,cxmt_financiador, cxmt_plano, cxmt_financ
                                          FROM cxmov_temp
									WHERE cxmt_pedido = '$edtNumPed' AND cxmt_loja = '$ljcod'
                                          AND cxmt_confirmado = 'N'
                                    ORDER BY cxmt_hora, cxmt_venc;";
                     //echo $sql_grade;
                     $query_grade = mysql_query($sql_grade,$conexao)or die("Erro: ".mysql_error());

					  if (mysql_num_rows($query_grade) > 0){
           ?>
                      <tr><td>
                      <table border='1' bordercolor='#000000' bgcolor='#002000' width='100%'><tr><td bgcolor='#DDFFDD'>
							<table border='1' bordercolor='#000000' align="center" width="100%">
								<tr>
								  <td colspan='12' bgcolor="#004000" align='center'><font color='#FFFFFF' size='2'>Forma de Pagamento Escolhida:</font></td>
                                </tr>
                                <tr>
                                <td height="26" align='left' bgcolor="#DDFFDD">Tipo</td>
                                <td align='right' bgcolor="#DDFFDD">Valor R$</td>
                                <td align='left' bgcolor="#DDFFDD">Número Doc.</td>
                                <td align='center' bgcolor="#DDFFDD">Vencimento</td>
                                <td align='center' bgcolor="#DDFFDD">Banco</td>
                                <td align='center' bgcolor="#DDFFDD">Agência</td>
                                <td align='left' bgcolor="#DDFFDD">Financiador</td>
                                <td align='left' bgcolor="#DDFFDD">Financeira</td>
                                <td align='left' bgcolor="#DDFFDD">Plano Financ</td>
                                <td align='left' bgcolor="#DDFFDD">Cartão</td>
                                <td align='left' bgcolor="#DDFFDD">Plano Cartão</td>
                                <td align='center' bgcolor="#DDFFDD">Excluir</td>
                                </tr>

<?php
                       //editado em 19 de abril de 2013
					   $totdinheiro      = 0; $totchequedia  = 0; $totchequepreloja = 0;  $totfinanceirachp = 0;
                       $totfinanceiradeb = 0; $totfinanceira = 0; $totcartao        = 0;  $totaldep         = 0;
					   $totaltrans       = 0;

					   while($linha_grade = mysql_fetch_object($query_grade)){
?>
                       <? if ($linha_grade->cxmt_tipo == 'RS'){ $tipo = 'ESP'; $corfundo = 'BBBBFF'; $totdinheiro       = $totdinheiro       + $linha_grade->cxmt_valor; };  ?>
                       <? if ($linha_grade->cxmt_tipo == 'CD'){ $tipo = 'CHD'; $corfundo = 'BBBBFF'; $totchequedia      = $totchequedia      + $linha_grade->cxmt_valor; };  ?>
                       <? if ($linha_grade->cxmt_tipo == 'CL'){ $tipo = 'CHP'; $corfundo = 'FFFF00'; $totchequepreloja  = $totchequepreloja  + $linha_grade->cxmt_valor; };  ?>
                       <? if ($linha_grade->cxmt_tipo == 'CP'){ $tipo = 'FIN'; $corfundo = 'FFFFE1'; $totfinanceirachp  = $totfinanceirachp  + $linha_grade->cxmt_valor; };  ?>
                       <? if ($linha_grade->cxmt_tipo == 'DC'){ $tipo = 'DEC'; $corfundo = 'FFFFE1'; $totfinanceiradeb  = $totfinanceiradeb  + $linha_grade->cxmt_valor; };  ?>
                       <? if ($linha_grade->cxmt_tipo == 'CA'){ $tipo = 'CAR'; $corfundo = 'FFEAEA'; $totcartao         = $totcartao         + $linha_grade->cxmt_valor; };  ?>
                       <? if ($linha_grade->cxmt_tipo == 'CT'){ $tipo = 'CNT'; $corfundo = 'FFFFE1'; $totfinanceira = $totfinanceira + $linha_grade->cxmt_valor; };  ?>
                       <? if ($linha_grade->cxmt_tipo == 'CC'){ $tipo = 'CCD'; $corfundo = 'C0FFC0'; $totacred      = $totacred      + $linha_grade->cxmt_valor; };  
					   
					   //editado em 19 de abril de 2013
					   if ($linha_grade->cxmt_tipo == 'TC')
					   { 
						 $tipo = 'TRA'; $corfundo = 'FFCEB7'; $totaltrans     = $totaltrans      + $linha_grade->cxmt_valor; 
					   }
					   if ($linha_grade->cxmt_tipo == 'DE')
					   { 
						 $tipo = 'DEP'; $corfundo = 'FFCEB7'; $totaldep      = $totaldep      + $linha_grade->cxmt_valor; 
					   }
					   //fim edição 19 de abril
								
								//pegando os dados do finplano
								$sql_dadosfp   = "select fp_financeira FROM finplano where fp_cod = '".$linha_grade->cxmt_financ."'"; 
								$query_dadosfp = mysql_query($sql_dadosfp, $conexao) or die ('Erro na consulta 6');
								if(mysql_num_rows($query_dadosfp) > 0){
								 $linha_dadosfp = mysql_fetch_object($query_dadosfp);
								}
								//pegando os dados do financeira
								$sql_dadosfin   = "select fin_desc FROM financeira where fin_cod = '".$linha_grade->cxmt_plano."'"; 
								$query_dadosfin = mysql_query($sql_dadosfin, $conexao) or die ('Erro na consulta 6');
								if(mysql_num_rows($query_dadosfin) > 0){
								 $linha_dadosfin = mysql_fetch_object($query_dadosfin);
								}
                        
                        ?>                       
                                <tr>
                                <td align='left' bgcolor="#<?=$corfundo?>"><font size='2' color="#000000"><?=$tipo?></font></td>
                                <td align='right' bgcolor="#<?=$corfundo?>"><font size='2' color="#000000"><?=number_format($linha_grade->cxmt_valor,'2',',','.');?></font></td>
                             <? if (($linha_grade->cxmt_tipo != 'RS') && ($linha_grade->cxmt_tipo != 'CD') && ($linha_grade->cxmt_tipo != 'CC')) { ?>
                                <td align='left' bgcolor="#<?=$corfundo?>"><font color="#000000"><?=$linha_grade->cxmt_numdoc?></font></td>
                                <td align='center' bgcolor="#<?=$corfundo?>"><font color="#000000"><?=muda_data_pt($linha_grade->cxmt_venc)?></font></td>
                                <td align='center' bgcolor="#<?=$corfundo?>"><font color="#000000"><?=$linha_grade->cxmt_banco?></font></td>
                                <td align='center' bgcolor="#<?=$corfundo?>"><font color="#000000"><?=$linha_grade->cxmt_agencia?></font></td>
                                <td align='left' bgcolor="#<?=$corfundo?>"><font color="#000000"><?=$linha_grade->cxmt_financiador?></font></td>
                               <? if (($linha_grade->cxmt_tipo == 'CP') || ($linha_grade->cxmt_tipo == 'DC') || ($linha_grade->cxmt_tipo == 'CT')) { ?>
                                <td align='center' bgcolor="#<?=$corfundo?>"><font color="#000000"><?=$linha_dadosfp->fp_financeira?></font></td>
                                <td align='center' bgcolor="#<?=$corfundo?>"><font color="#000000"><?=$linha_dadosfin->fin_desc?></font></td>
                               <? }else{?>
                                <td colspan='2' align='left' bgcolor="#<?=$corfundo?>"><font color="#000000"></font></td>
                               <? } ?>
                               <? if ($linha_grade->cxmt_tipo == 'CA') { ?>
                                <td align='left' bgcolor="#<?=$corfundo?>"><font color="#000000"><?=$linha_dadosfp->fp_financeira?></font></td>
                                <td align='left' bgcolor="#<?=$corfundo?>"><font color="#000000"><?=$linha_dadosfin->fin_desc?></font></td>
                               <? }else{?>
                                <td colspan='2' align='left' bgcolor="#<?=$corfundo?>"><font color="#000000"></font></td>
                               <? } ?>
                             <? }else{ ?>
                                <td colspan='9' align='left' bgcolor="#<?=$corfundo?>"><font color="#000000"></font></td>
                             <? } ?>
                             <? $edtValor = valor_mysql($edtValor);?>
                                <td align='center' bgcolor="<?=$corfundo?>"><a href='pedido_pag.php?edtNumPed=<?=$edtNumPed?>&dtcaixa=<?=$dtcaixa?>&flag=excluir&tipo=<?=$linha_grade->cxmt_tipo?>&numdoc=<?=$linha_grade->cxmt_numdoc?>&cli_razao=<?=$linha_ped->cli_razao?>&vendedor=<?=$linha_ped->ven_cod?>&acred=<?=$totacred?>&edtPrevEntrega=<?=$edtPrevEntrega?>&menuoff=<?=$menuoff?>'><img src='../imagens/apagar.gif' border="no" alt="Excluir"></a></td>
                                </tr>
                    <? } // fim do while($linha_td = mysql_fet ch_object($query_td)){
                     } // fim do if (mysql_num_rows($query_grade) > 0){ ?>
                            </table>
                        </td></tr>
                      </table>
                      </td></tr>

                      <tr><td>
                      <table border='1' bordercolor='#000000' bgcolor='#C0FFC0' width='100%'><tr><td bgcolor='#DDFFDD'>
								<tr>
								  <td bgcolor='#C0C0FF' colspan='12' align='center'><font size='2'>TOTAIS DOS LANÇAMENTOS</font></td>
                                </tr>
                                <tr>
                                 <td align='right' bgcolor="#DDFFDD">DINHEIRO</td>
                                 <td align='right' bgcolor="#DDFFDD">CHEQUE DIA</td>
                                 <td align='right' bgcolor="#DDFFDD">CHEQUE PRÉ LOJA</td>
                                 <td align='right' bgcolor="#DDFFDD">FINANCEIRA CHQ</td>
                                 <td align='right' bgcolor="#DDFFDD">FINANCEIRA DÉB C</td>
                                 <td align='right' bgcolor="#DDFFDD">FINANCEIRA CARNET</td>
                                 <td align='right' bgcolor="#DDFFDD">CARTÃO</td>
                                 <td align='right' bgcolor="#DDFFDD">CARTA CRÉDITO</td> 
                                 <td align='right' bgcolor="#DDFFDD">DEPÓSITO</td>
                                 <td align='right' bgcolor="#DDFFDD">TRANSFERÊNCIA</td>
                                 <td align='right' bgcolor="#008000"><font size='2' color="#FFFFFF">TOTAL GERAL</font></td>
                                </tr>
                                <tr>
                                 <td align='right' bgcolor="#FFFFFF"><font size='2' color="#400000"><?=number_format($totdinheiro,'2',',','.')?></font></td>
                                 <td align='right' bgcolor="#FFFFFF"><font size='2' color="#400000"><?=number_format($totchequedia,'2',',','.')?></font></td>
                                 <td align='right' bgcolor="#FFFFFF"><font size='2' color="#400000"><?=number_format($totchequepreloja,'2',',','.')?></font></td>
                                 <td align='right' bgcolor="#FFFFFF"><font size='2' color="#400000"><?=number_format($totfinanceirachp,'2',',','.')?></font></td>
                                 <td align='right' bgcolor="#FFFFFF"><font size='2' color="#400000"><?=number_format($totfinanceiradeb,'2',',','.')?></font></td>
                                 <td align='right' bgcolor="#FFFFFF"><font size='2' color="#400000"><?=number_format($totfinanceira,'2',',','.')?></font></td>
                                 <td align='right' bgcolor="#FFFFFF"><font size='2' color="#400000"><?=number_format($totcartao,'2',',','.')?></font></td>
                                 <td align='right' bgcolor="#FFFFFF"><font size='2' color="#400000"><?=number_format($totacred,'2',',','.')?></font></td>
                                 <td align='right' bgcolor="#FFFFFF"><font size='2' color="#400000"><?=number_format($totaldep,'2',',','.')?></font></td>
                                 <td align='right' bgcolor="#FFFFFF"><font size='2' color="#400000"><?=number_format($totaltrans,'2',',','.')?></font></td>
                                 <? $totgeral = $totdinheiro + $totchequedia + $totchequepreloja + $totfinanceirachp + $totfinanceiradeb + $totfinanceira + $totcartao + $totacred+$totaldep+$totaltrans;  ?>
                                 <td align='right' bgcolor="#FFFFB9"><font size='2' color="#FF0000"><?=number_format($totgeral,'2',',','.')?></font></td>
                                </tr>
                      </table>
                      </td></tr>
                                      <tr><td>
                                      <table align="center">
                                      <tr><td></td></tr>
                                      <tr><td colspan='4' align='center'>
                            <?
                            $totgeral = number_format($totgeral,'2',',','.'); $valliq = number_format($valliq,'2',',','.');
                            $totgeral = valor_mysql($totgeral); $valliq = valor_mysql($valliq);
                            //echo $totgeral.'totgeral';  echo $valliq.'valliq';
                            if ($totgeral > $valliq){ ?>
                					    <input style="border:solid 1; font: bold; height: 40; width:400; background-color:#004000; color:#FFFFFF;" name="btnPagar" value="REALIZAR PAGAMENTO COM ACRÉSCIMO" type="button" onClick="enviar('pedido_pag.php?flag=paga&menuoff=ok&cli_razao=<?=$linha_ped->cli_razao?>&vendedor=<?=$linha_ped->ven_cod?>','DESEJA REALMENTE PAGAR O PEDIDO <?=$edtNumPed?> COM ACRÉSCIMO NO VALOR DE R$ <?=$totgeral?>?')">
                            <? } ?>
                            <? if ($totgeral == $valliq){ ?>
                					    <input style="border:solid 1; font: bold; height: 40; width:400; background-color:#004000; color:#FFFFFF;" name="btnPagar" value="REALIZAR PAGAMENTO" type="button" onClick="enviar('pedido_pag.php?flag=paga&menuoff=ok&totgeral=<?=$totgeral?>&cli_razao=<?=$linha_ped->cli_razao?>&vendedor=<?=$linha_ped->ven_cod?>','DESEJA REALMENTE PAGAR O PEDIDO <?=$edtNumPed?> NO VALOR DE R$ <?=$totgeral?>?')">
                            <? } ?>
                                      </td></tr>
                                      </table>
						<? }else{
							 $msg_pag = "Erro: Este Pedido Não Está Apto para ser Pago!";
                           }
					}else{
						$msg_pag = "<tr><td><table align='center'><tr><td bgcolor='#FFFFFF'><font size='3' color='#FF0000'>Erro: Nenhum Caixa Aberto para esta Data!</font></td></tr></table></td></tr>";
					}
				}
			}
					 ?>
	<?php
		if(isset($msg_pag)){
			echo "<tr>";
				  echo "<td align='center' width='100%'><font class='AVISO'>".$msg_pag."</font></td>";
			echo "</tr>";
		}
	?>
<? } ?>                    
</table>
</form>
</body>
</html>
<?
 } // fim do if(mysql_num_rows($query_pesq) > 0){
  include("rodape.php");
?>