<?php
	include("conexao2.inc.php");
	include("funcoes2.inc.php");
	include("dglogin1.php");

    $arquivo = "pedido_alt.php";
    include("auditoria.php");

	$data = date("d/m/Y");
    $hora = date("H:i:s");

   // travamento total do caixa
   $caixatravado = "N";	   
   $sql_trc = "SELECT trc_loja, trc_motivo FROM travacaixa WHERE trc_loja = '$ljcod' AND trc_excluido = 'N';";
   $query_trc = mysql_query($sql_trc,$conexao)or die("Erro na Consulta!");
   if(mysql_num_rows($query_trc) > 0){
     $linha_trc = mysql_fetch_object($query_trc);	   
	 $caixatravado = "S";
   }else{
	 $caixatravado = "N";	   
   }

  if ($caixatravado == "S"){ ?>
		<html>
		<head>
			<link rel="stylesheet" href="est_big.css" type="text/css">
			<title>.:/gercomweb</title>
        </head>
        <body>
          <table align="center">
            <tr valign="middle">
             <td align="center" bgcolor="#FFFFFF"><font style="font-size:36px; color:#FF0000">Caixa da Loja Travado!!!</font></td>
            </tr>
            <tr>
             <td align="center" bgcolor="#FFFFFF"><font style="font-size:36px; color:#FF0000">Motivo: <?=$linha_trc->trc_motivo?></font></td>            
          </table>
        </body>
        </html>    
<? }else{  //fim do if ($caixatravado == "S"){
	
	$sql_regiao   = "SELECT DISTINCT reg_coef, reg_descav1, reg_descav2, reg_descav3 from loja, regioes
					 WHERE lj_regiao = reg_num AND lj_cod = '$ljcod';";
	$query_regiao = mysql_query($sql_regiao,$conexao); 	
	if(mysql_num_rows($query_regiao) > 0){
 	  $linha_regiao = mysql_fetch_object($query_regiao);
	}
	
    
	if($flag == "excluir_item"){
		//dados do pedido
		$sql_ped = "SELECT * FROM pedcad WHERE ped_num = '$edtNumPed' AND ped_loja = '$ljcod';";
		$query_ped = mysql_query($sql_ped)or die("Erro na consulta do Pedido!");
		$linha_ped = mysql_fetch_object($query_ped);
		$edtEmissao = muda_data_pt($linha_ped->ped_emissao);
		$lstVend = $linha_ped->ped_vend;
		$lstTV = $linha_ped->ped_tipove;
		$lstCli = $linha_ped->ped_cliente;
		$lstCli2 = $linha_ped->ped_cliente;
		$edtSit = $linha_ped->ped_situacao;
		$edtStatus = $linha_ped->ped_status;

		$sql_ex = "DELETE FROM pedmov
                                   WHERE pm_num = '$edtNumPed' AND
								   pm_prod = '$prod' AND pm_cor = '$cor' AND pm_escala = '$escala'
                                   AND pm_progrupo = '$progrupo' AND pm_loja = '$loja';";
		$query_ex = mysql_query($sql_ex,$conexao)or die("Erro na Exclusão do Item '$prod'!");
    }

 if($REQUEST_METHOD == "GET"){
	if($flag == "alterar"){
		//dados do pedido
		$sql_ped = "SELECT * FROM pedcad WHERE ped_num = '$edtNumPed' AND ped_loja = '$ljcod';";
		$query_ped = mysql_query($sql_ped)or die("Erro na consulta do Pedido!");
		$linha_ped = mysql_fetch_object($query_ped);
		$edtEmissao = muda_data_pt($linha_ped->ped_emissao);
		$lstVend = $linha_ped->ped_vend;
		$lstTV = $linha_ped->ped_tipove;
		$lstCli = $linha_ped->ped_cliente;
//		$lstCli2 = $linha_ped->ped_cliente;
		$edtSit = $linha_ped->ped_situacao;
		$edtStatus = $linha_ped->ped_status;
		
		$trava_cab  = "disabled";
		$trava_cab2 = "disabled";
		$trava_ped  = "readonly";
    }
 }
 if($REQUEST_METHOD == "POST"){

	if($incluir_desconto == "s"){
		
		$descontov = valor_mysql($edtDesconto);
		$sql_desconto = "UPDATE pedcad SET ped_desconto = '$descontov'
					      WHERE ped_num = '$edtNumPed' AND ped_loja = '$ljcod';";
		$query_desconto = mysql_query($sql_desconto,$conexao)or die("Erro na Alteração do Pedido Desconto!");
		$trava_cab  = "disabled";
		$trava_cab2 = "disabled";
		$trava_ped  = "readonly";
        $pro_det    = "";
	}

	if($flag == "altcab"){
		$hdEmissao = muda_data_en($hdEmissao);
		$data      = muda_data_en($data);
		$sql_alt = "UPDATE pedcad SET ped_cliente = '$lstCli', ped_tipove = '$lstTV', ped_emissao = '$hdEmissao',
						   ped_alterado = 'S', ped_dtalterado = '$data', ped_hora = '$hora', ped_login = '$acelogin'
					 WHERE ped_num = '$edtNumPed' AND ped_loja = '$ljcod';";
		$query_alt = mysql_query($sql_alt,$conexao)or die("Erro na Alteração do Pedido!");
		$trava_cab  = "disabled";
		$trava_cab2 = "disabled";
		$trava_ped  = "readonly";
        $pro_det    = "";
	}

    if($flag == "finalizar"){
		    $data = muda_data_en($data);
			//selecionando os produtos do pedido
			$sql_pm = "SELECT * FROM pedmov WHERE pm_num = '$edtNumPed' AND pm_lojaloc = '$ljcod';";
			$query_pm = mysql_query($sql_pm, $conexao)or die("Erro na Busca dos Produtos do Pedido!");
 			if(mysql_num_rows($query_pm) > 0){
			while($result_pm = mysql_fetch_object($query_pm)){
			if ($alteraritem != "OK") {
                  if($result_pm->pm_es == "S"){
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
							}else{
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
					$sql_alt = "UPDATE pedcad SET ped_valprod = $valprod, ped_valliq = $valliq, ped_hora = '$hora', ped_login = '$acelogin'
								WHERE ped_num = '$edtNumPed' AND ped_emp = '$codemp' AND ped_loja = '$ljcod'";
					$query_alt = mysql_query($sql_alt)or die("Erro na Atualização do Pedido!");

					//retirando o * da descr do prod na movimentacao do pedido
					$descr = substr($result_pm->pm_desc, 0 ,strlen($result_pm->pm_desc)-1);
					$sql_atu_mp = "UPDATE pedmov SET pm_desc='$descr', pm_hora = '$hora', pm_login = '$acelogin'
								  WHERE pm_num = '$edtNumPed' AND pm_prod = '$result_pm->pm_prod'
								  	AND pm_cor = '$result_pm->pm_cor' AND pm_escala = '$result_pm->pm_escala'
									AND pm_progrupo = '$result_pm->pm_progrupo' AND pm_lojaloc = '$ljcod';";
					$query_atu_mp = mysql_query($sql_atu_mp, $conexao)or die("Erro na Retirada do * do Cod do Item do Pedido!");
				}  //fim do alterar item
			  } //fim do while
			} //fim do if alteraritem == ok
			echo "<script>window.location = '../main.php';</script>";
			exit;
    }
		if($incluir_prod == "s"){
		
		// INICIO DO CODIGO PARA VERIFICAR O DESCONTO APLICADO NO PRODUTO

		//avarias escala 102, prog 22 cor 128    promocao prog 254 cor 51       exclusivo cor 1751
		if (($lstProg == "22") || ($lstProg == "254") || ($lstEscala == "102") || ($lstCor == "128") || ($lstCor == "51") || ($lstCor == "1751")  || ($lstCor == "1")){
		  $descontomax = 0;
		}else{
		  include ("calculo_desconto.php");
		}

	   // echo $descontomax.'$descontomax'; echo $edtValunit.'$edtValunit'; echo $Valunit.'$Valunit'; echo $coef.'$coef'; echo $desconto.'$desconto'; 
	  // echo $descontomax.'$descontomax'; 		echo $edtValunit.'$edtValunit';
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
						   	if(mysql_num_rows($query_estoqlj) > 0){
								$data = muda_data_en($data);
								// checando se produto já foi lançado.
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
									$edtProd 	  = "";
									$edtRef  	  = "";
									$edtNomeprod  = "";
									$edtValunit   = "";
									$edtTotal     = "";
									$edtqtd     = "";
									$trava_alt  = "";
									$trava_cab  = "disabled";
									$trava_cab2 = "disabled";
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
								 		 							   pm_incluido,pm_alterado,pm_dtincluido,pm_dtalterado, 
																	   pm_entregue,pm_comissao,pm_estoqueok,pm_promocao,pm_hora, pm_login, pm_fab)
								 		 		   VALUES ('$edtNumPed', '$edtProd','$codemp', '$lstLoja', '$ljcod', '$lstCor','$lstEscala','$lstProg','S',
								 		 				   '$pro_descabv*','$pro_comple','$edtqtd','$edtValunit','$edtTotal', ".$valorpc."
								 	 	 				   'S', 'N','$data','$data','N','$pro_comissao','".$estoqueok."','$pro_promocao','$hora','$acelogin', '$fab')";
                                            //echo $sql_incl.'1';
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
								 		 							   pm_incluido,pm_alterado,pm_dtincluido,pm_dtalterado, pm_entregue,pm_comissao,pm_estoqueok,
																	   pm_promocao, pm_hora, pm_login, pm_fab)
								 		 		   VALUES ('$edtNumPed', '$edtProd','$codemp', '$lstLoja', '$ljcod', '$lstCor','$lstEscala','$lstProg','S',
								 		 				   '$pro_descabv*','$pro_comple','$edtqtd','$edtValunit','$edtTotal', ".$valorpc."
								 	 	 				   'S', 'N','$data','$data','N','$pro_comissao','".$estoqueok."','$pro_promocao','$hora','$acelogin', '$fab')";
                                            //echo $sql_incl.'2';
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
									$trava_cab  = "disabled";
									$trava_cab2 = "disabled";
								}	//fim do incluirprod=s
							}else{
								$pro_det = "ok";
								$sql_loja = "select lj_fantasia from loja where lj_cod = '$lstLoja'";
								$query_loja = mysql_query($sql_loja,$conexao);
								$linha_loja = mysql_fetch_object($query_loja);
								$msg_estoq  = "Produto: ".$edtProd." não possui estoque na Loja: ".$lstLoja." - ".$linha_loja->lj_fantasia."!";
								$msg_estoq2 = "<a href=\"#\" onClick=\"submit_action('pedido_cad.php?incluir_prod=s&campo=formpedido.edtProd&produtos=ok&trava=disabled&trava_list=disabled&edtNumPed=$edtNumPed&edtSit=$edtSit&lstLoja=$lstLoja')\">Clique Aqui 1</a> para Voltar.";
								$trava_alt = "";
								$trava_cab  = "disabled";
								$trava_cab2 = "disabled";
							  if ($lstLoja != $ljcod) {
								$data = muda_data_en($data);
								// checando se produto já foi lançado.
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
									$edtProd 	  = "";
									$edtRef  	  = "";
									$edtNomeprod  = "";
									$edtValunit   = "";
									$edtTotal     = "";
									$edtqtd     = "";
									$trava_alt = "";
									$trava_cab  = "disabled";
									$trava_cab2 = "disabled";
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
								 		 							   pm_incluido,pm_alterado,pm_dtincluido,pm_dtalterado, pm_entregue,pm_comissao,pm_estoqueok,
																	   pm_promocao, pm_hora, pm_login, pm_fab)
								 		 		   VALUES ('$edtNumPed', '$edtProd','$codemp', '$lstLoja', '$ljcod', '$lstCor','$lstEscala','$lstProg','S',
								 		 				   '$pro_descabv*','$pro_comple','$edtqtd','$edtValunit','$edtTotal', ".$valorpc."
								 	 	 				   'S', 'N','$data','$data','N','$pro_comissao','".$estoqueok."','$pro_promocao','$hora','$acelogin', '$fab')";
                                            //echo $sql_incl.'3';
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
								 		 							   pm_incluido,pm_alterado,pm_dtincluido,pm_dtalterado, 
																	   pm_entregue,pm_comissao,pm_estoqueok,pm_promocao,pm_hora, pm_login, pm_fab)
								 		 		   VALUES ('$edtNumPed', '$edtProd','$codemp', '$lstLoja', '$ljcod', '$lstCor','$lstEscala','$lstProg','S',
								 		 				   '$pro_descabv*','$pro_comple','$edtqtd','$edtValunit','$edtTotal', ".$valorpc."
								 	 	 				   'S', 'N','$data','$data','N','$pro_comissao','".$estoqueok."','$pro_promocao','$hora','$acelogin', '$fab')";
                                            //echo $sql_incl.'4';
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
									$trava_cab  = "disabled";
									$trava_cab2 = "disabled";
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
									$trava_cab  = "disabled";
									$trava_cab2 = "disabled";
									$incluir_prod = "n";
									$msg_prod = "Erro: Item já Cadastrado!";
								}else{
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
//									$edtTotal   = valor_mysql($edtTotal);
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
								 		 							   pm_incluido,pm_alterado,pm_dtincluido,pm_dtalterado, pm_entregue,pm_comissao,pm_estoqueok,
																	   pm_promocao, pm_hora, pm_login, pm_fab)
								 		 		   VALUES ('$edtNumPed', '$edtProd','$codemp', '$lstLoja', '$ljcod', '$lstCor','$lstEscala','$lstProg','S',
								 		 				   '$pro_descabv*','$pro_comple','$edtqtd','$edtValunit','$edtTotal', ".$valorpc."
								 	 	 				   'S', 'N','$data','$data','N','$pro_comissao','".$estoqueok."','$pro_promocao','$hora','$acelogin', '$fab')";
                                            //echo $sql_incl.'5';
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
								 		 							   pm_incluido,pm_alterado,pm_dtincluido,pm_dtalterado, pm_entregue,pm_comissao,pm_estoqueok,
																	   pm_promocao, pm_hora, pm_login, pm_fab)
								 		 		   VALUES ('$edtNumPed', '$edtProd','$codemp', '$lstLoja', '$ljcod', '$lstCor','$lstEscala','$lstProg','S',
								 		 				   '$pro_descabv*','$pro_comple','$edtqtd','$edtValunit','$edtTotal', ".$valorpc."
								 	 	 				   'S', 'N','$data','$data','N','$pro_comissao','".$estoqueok."','$pro_promocao','$hora','$acelogin', '$fab')";
                                            //echo $sql_incl.'6';
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
                                    $linha_estoqlj = mysql_fetch_object($query_estoqlj);

						   			if(mysql_num_rows($query_estoqlj) > 0){
					            		$trava = "disabled";
										$trava_list = "disabled";
										$incluir_prod = 'n';
										$trava_alt = "";
										$trava_cab  = "disabled";
										$trava_cab2 = "disabled";
										$incluir_prod = "n";
          		                    }else{
										$edtqtd = valor_mysql($edtqtd);
//										$edtqtd = 0 - $edtqtd;
										$edtqtd = valor_mysql($edtqtd)/100;
                                    }
									 $trava = "disabled";
									 $trava_list = "disabled";
									 $incluir_prod = 'n';
/*
									 $edtProd 	  = "";
									 $edtRef  	  = "";
									 $edtNomeprod  = "";
									 $edtValunit   = "";
									 $edtTotal     = "";
									 $edtqtd     = "";
									 $trava_alt = "";
									 $trava_cab = "disabled";
*/
								//	}
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
								 		 							   pm_incluido,pm_alterado,pm_dtincluido,pm_dtalterado, pm_entregue,pm_comissao,pm_estoqueok,
																	   pm_promocao, pm_hora, pm_login, pm_fab)
								 		 		   VALUES ('$edtNumPed', '$edtProd','$codemp', '$lstLoja', '$ljcod', '$lstCor','$lstEscala','$lstProg','S',
								 		 				   '$pro_descabv*','$pro_comple','$edtqtd','$edtValunit','$edtTotal', ".$valorpc."
								 	 	 				   'S', 'N','$data','$data','N','$pro_comissao','".$estoqueok."','$pro_promocao','$hora','$acelogin', '$fab')";
                                            //echo $sql_incl.'7';
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
									$trava_cab  = "disabled";
									$trava_cab2 = "disabled";
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
								$trava_cab  = "disabled";
								$trava_cab2 = "disabled";
								$incluir_prod = "n";
								$msg_prod = "Erro: Item já Cadastrado!";
							}
						    $sql_reserva = "SELECT res_cod,res_prod,res_ljdestino,lj_fantasia,res_cor,res_escala,res_progrupo,res_pedido
											 FROM reserva,loja
											 WHERE res_prod = '$edtProd' AND res_cor = '$lstCor'
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
//									$edtTotal   = valor_mysql($edtTotal);

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
								 		 							   pm_incluido,pm_alterado,pm_dtincluido,pm_dtalterado, pm_entregue,pm_comissao,pm_estoqueok,
																	   pm_promocao, pm_hora, pm_login, pm_fab)
								 		 		   VALUES ('$edtNumPed', '$edtProd','$codemp', '$lstLoja', '$ljcod', '$lstCor','$lstEscala','$lstProg','S',
								 		 				   '$pro_descabv*','$pro_comple','$edtqtd','$edtValunit','$edtTotal', ".$valorpc."
								 	 	 				   'S', 'N','$data','$data','N','$pro_comissao','".$estoqueok."','$pro_promocao','$hora','$acelogin', '$fab')";
                                            //echo $sql_incl.'8';
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
								 		 							   pm_incluido,pm_alterado,pm_dtincluido,pm_dtalterado, pm_entregue,pm_comissao,pm_estoqueok,
																	   pm_promocao, pm_hora, pm_login, pm_fab)
								 		 		   VALUES ('$edtNumPed', '$edtProd','$codemp', '$lstLoja', '$ljcod', '$lstCor','$lstEscala','$lstProg','S',
								 		 				   '$pro_descabv*','$pro_comple','$edtqtd','$edtValunit','$edtTotal', ".$valorpc."
								 	 	 				   'S', 'N','$data','$data','N','$pro_comissao','".$estoqueok."','$pro_promocao','$hora','$acelogin', '$fab')";
                                            //echo $sql_incl.'9';
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
									$trava_cab  = "disabled";
									$trava_cab2 = "disabled";
							 }
					  }else{
						    	   $msg_prod = "Nao Existe Nenhuma Reserva Para este Produto!";
		                           $msg_estoq3 = "<a href=\"#\" onClick=\"Javascript: history.back()\">Clique Aqui 3</a> para Voltar.";
						  }
					} //fim else da query lstLoja = ljcod
		 }//fim da else flag pedcompra == ok

	 }else{  // FIM DO CODIGO PARA VERIFICAR O DESCONTO APLICADO NO PRODUTO	
       $msg_prod = "Preço do Produto escolhido (R$ ".number_format($edtValunit,'2',',','.').") está abaixo do valor mínimo de venda.<br>Preço de Tabela: (R$ ".number_format($Valunit,'2',',','.')."). Preço Mínimo para venda: (R$ ".number_format(ceil($descontomax),'2',',','.').")! Digite outro valor!";	 
	 }

		}//fim da flag incluir_prod=s
		
       if ($edtProd != ""){
           $edtProd = '';  $edtRef     = ''; $edtNomeprod = '';
           $lstCor  = '';  $lstEscala  = ''; $lstProg     = '';
           $edtqtd  = '';  $edtValunit = ''; $edtTotal    = '';
       }

    if ($formcod_ck != "") {
		setcookie("formcod_ck","");
		setcookie("formdesc_ck","");
		setcookie("formref_ck","");
		setcookie("formesc_ck","");
		setcookie("formcor_ck","");
		setcookie("formprog_ck","");
		setcookie("formloja_ck","");
	}
  }//fim do request post
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
<title>:: gercom.NET - Alteração do Pedido de Venda ::</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script src="funcoes.js"></script>
<script language="JavaScript">
		function verif_mov(){
			if(formpedido.edtNumPed.value == "" || formpedido.lstCli.value == ""){
				alert("Preencha todos os Dados do Pedido!");
				return false;
			}
		}

		function valor_java(valor){
			valor = valor.replace(".","");
			valor = valor.replace(".","");
			valor = valor.replace(".","");
			valor = valor.replace(",",".");
			return parseFloat(valor);
		}

		function calc_total(){
			if(document.formpedido.edtValunit.value != "" && document.formpedido.edtqtd.value != ""){
				var valunit = document.formpedido.edtValunit.value;
				valunit = valor_java(valunit);
				var qtd = document.formpedido.edtqtd.value;
				qtd = valor_java(qtd);
				var total = valunit * qtd;
				total = total.toString();
				document.formpedido.edtTotal.value = total.toString();
			}
		}
		
		function troca(){
			 eval("popup('troca_prod.php?edtNumPed="+document.formpedido.edtNumPed.value+"',830,220,'center','center',POP_tot);");

		}	


		function submit_action(caminho){
			//postando para a verificacao;
			document.formpedido.action= caminho; 
			document.formpedido.method= 'post'; 
			document.formpedido.submit();			
		}
		
 		function atualiza()	{
			window.opener.location.reload();
		}
		
		//carrega vetor com o codigo dos produtos.
		var arr_prod = new Array();
		<? 
			$sql_nome = "SELECT pro_cod, pro_preco1 FROM produtos;";
			$query_nome = mysql_query($sql_nome);
			while ($linha_nome = mysql_fetch_object($query_nome)) {
				?>
					arr_prod["<?=$linha_nome->pro_cod?>"] = "<?=$linha_nome->pro_ref?>|<?=$linha_nome->pro_descabv?>|<?=number_format($linha_nome->pro_preco1,'2',',','.')?>";
				<?
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

		function verifica(){
			if(formpedido.lstLoja.value == "Escolha a loja"){
				alert("Escolha a Loja!");
				document.formpedido.lstLoja.focus();
				return false;
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
	</script>
</head>

<body background="../imagens/fundomain.jpg" topmargin="0" leftmargin="0" rightmargin="0" bottommargin="0">
<?
//  if ($menuoff != "ok") {
    include("menu_java.php");
//  } 
?>


<form action="pedido_alt.php?incluir=s&campo=formpedido.edtEmissao" method="post" name="formpedido" onSubmit="return verif_mov();">
<table width="100%" cellpadding="2" cellspacing="2" align="center" border="1" bordercolor="#CCCCCC">
    <tr>
      <td align="left" bgcolor="#085D44"><img src="../imagens/pedido_venda.jpg"></td>
    </tr>
<?
	if(isset($msg_ok)){
		echo "<tr>";
			echo "<td align='center'><font class='AVISO'>".$msg_ok."</font></td>";
		echo "</tr>";
	}
?>
<tr> 
	<td width="100%" align="center">
      <table bgcolor="#DFFFDF" width="100%" height="41%" border="0" cellpadding="2" cellspacing="0">
          <tr>
            <td><img src="../imagens/num_pedido.jpg"></td>
            <td><img src="../imagens/emissao.jpg"></td>
            <td><img src="../imagens/vendedor.jpg"></td>
            <td colspan='2'><img src="../imagens/cliente.jpg"></td>
          </tr>
          <tr>
            <td>
<?            
					$sql_pvautomatico = "SELECT lj_sigla, lj_pvautomatico, lj_seqpv FROM loja where lj_cod = '$ljcod';";
					$query_pvautomatico = mysql_query($sql_pvautomatico,$conexao);
				    if(@mysql_num_rows($query_pvautomatico) > 0){
					 $linha_pvautomatico = mysql_fetch_object($query_pvautomatico);
					 if ($linha_pvautomatico->lj_pvautomatico == "S"){
					   $trava_ped = 'readonly';
					   if ($edtNumPed == ""){
					     $edtNumPed = $linha_pvautomatico->lj_sigla.$linha_pvautomatico->lj_seqpv;
					   } 
					 }
					} 
?>            
            
             <input name="edtNumPed" type="text" value="<?=$edtNumPed?>" size="10" maxlength="20" style="text-align:center; font-weight:bold; color:#000000; font-size:16; width:140; background-color: #FFFF80; border:solid 1;" <?=$trava_ped?>>
            </td>
            <? if ($edtEmissao == ""){
                   $edtEmissao = $data;
               } ?>
            <td>
             <input name="edtEmissao" type="text" style="font-weight:bold; border:solid 1; color:#000000; background-color:#FFFFC0;" size="11" value="<?=muda_data_pt($edtEmissao)?>" onBlur="javascript: formpedido.hdEmissao.value = formpedido.edtEmissao.value" <?=$trava_cab?>>
            </td>
            <td>
             <select name="lstVend" style="font-weight:bold; border:solid 1; background-color:#FFFFC0; width:180;" onBlur="javascript: formpedido.hdVend.value = formpedido.lstVend.value;" <?=$trava_cab?>>
                <?
					$sql_vend = "SELECT ven_cod,ven_nome FROM vendedor WHERE ven_loja = '$ljcod' AND ven_ativo = 'S'
                                     ORDER BY ven_nome;";
					$query_vend = mysql_query($sql_vend,$conexao);
					if (mysql_num_rows($query_vend) > 0){
						while($linha_vend = mysql_fetch_object($query_vend)){
							if($lstVend == $linha_vend->ven_cod){
								echo "<option value='".$linha_vend->ven_cod."' selected>".$linha_vend->ven_nome."</option>";
							}else{
								echo "<option value='".$linha_vend->ven_cod."'>".$linha_vend->ven_nome."</option>";
							}
						}
					}
				?>
              </select>
             </td>
        <? if ($antes == "auricelio foi o responsavel"){ ?>                          
            <td><select name="lstTV" style="font-weight:bold; border:solid 1; background-color:#FFFFC0; width:120;" 
				onBlur="javascript: formpedido.hdTipove.value = formpedido.lstTV.value" <?=$trava_cab?>>
                <?
					$sql_tv   = "SELECT pp_cod,pp_desc FROM planopag;";
					$query_tv = mysql_query($sql_tv,$conexao);
					if (mysql_num_rows($query_tv) > 0) {
						while($linha_tv = mysql_fetch_object($query_tv)){
							if($lstTV == $linha_tv->pp_cod){
								echo "<option value='".$linha_tv->pp_cod."' selected>".$linha_tv->pp_desc."</option>";
							}else{
								echo "<option value='".$linha_tv->pp_cod."'>".$linha_tv->pp_desc."</option>";
							}
						}
					}
				?>
              </select> </td>
        <? } ?>                           
            <td>
              <select name="lstCli" style="font-weight:bold; border:solid 1; background-color:#FFFFC0; width:340;" <?=$trava_cab?>>
                <?
					$sql_cli   = "SELECT cli_cgccpf,cli_razao FROM clientes
									order by cli_razao;";
                    $query_cli = mysql_query($sql_cli,$conexao);
					if (mysql_num_rows($query_cli) > 0) {
                      if(!isset($lstCli2)){
						while($linha_cli = mysql_fetch_object($query_cli)){
							if ($lstCli == $linha_cli->cli_cgccpf){
								echo "<option value='".$linha_cli->cli_cgccpf."' selected>".$linha_cli->cli_razao."</option>";
							}else{
								echo "<option value='".$linha_cli->cli_cgccpf."'>".$linha_cli->cli_razao."</option>";
							}
						}
                      }else{
    					$sql_cli2   = "SELECT cli_cgccpf,cli_razao FROM clientes
    									WHERE cli_cgccpf = '$lstCli';";
    					$query_cli2 = mysql_query($sql_cli2,$conexao);
					    if (mysql_num_rows($query_cli2) > 0){
					     $linha_cli2 = mysql_fetch_object($query_cli2);
						 echo "<option value='".$linha_cli2->cli_cgccpf."' selected>".$linha_cli2->cli_razao."</option>";
						}
                      }
					}
				?>
              </select>
              <input name="hdProd" type="hidden" id="hdProd" value="<?=$edtProd?>">
              <input name="hdqtd" type="hidden" id="hdqtd" value="<?=$edtqtd?>">
              <input name="hdCor" type="hidden" id="hdCor" value="<?=$lstCor?>">
              <input name="hdEscala" type="hidden" id="hdEscala" value="<?=$lstEscala?>">
              <input name="hdProg" type="hidden" id="hdProg" value="<?=$lstProg?>">
              <input name="hdValunit" type="hidden" id="hdValunit" value="<?=$edtValunit?>">
              <input name="hdTotal" type="hidden" id="hdTotal" value="<?=$edtTotal?>">
              <input name="hdEmissao" type="hidden" id="hdEmissao2" value="<?=$edtEmissao?>">
              <input name="hdVend" type="hidden" id="hdVend2" value="<?=$lstVend?>">
              <input name="hdTipove" type="hidden" id="hdTipove2" value="<?=$lstTV?>">
              <input name="hdCli" type="hidden" id="hdCli2" value="<?=$lstCli?>">
              <input name="hdProg" type="hidden" id="hdCli2" value="<?=$lstProg?>">
             </td>
             <td>
		<? if($pro_det != "ok"){ ?>
		      <input type="button" name="btnIncProd3" style="font-weight:bold; color:#FFFFFF; width:150; background-color:#0000FF;" value="[ Alterar Cabeçalho ]" onClick="submit_action('pedido_alt.php?flag=alterar&edtNumPed=<?=$edtNumPed?>ljcod=<?=$ljcod?>&lstCli=<?=$lstCli?>&lstVend=<?=$lstVend?>&lstTV=<?=$lstTV?>&edtEmissao=<?=muda_data_en($edtEmissao)?>&pro_det=ok&trava_cab2=disabled&trava_ped=readonly');" <?=travalist?>>
		<? }else{ ?>
		      <input type="button" name="btnIncProd3" style="font-weight:bold; color:#FFFFFF; width:150; background-color:#0000FF;" value="[ Confirmar Alteração ]" onClick="submit_action('pedido_alt.php?flag=altcab&edtNumPed=<?=$edtNumPed?>&ljcod=<?=$ljcod?>&lstVend=<?=$lstVend?>');" <?=travalist?>>
		<? } ?>
             </td>
          </tr>

		</table>
	</td>
</tr>		
<tr> 
	<td> 
      <table bgcolor="#FFFFEA" width="100%" cellpadding="2" cellspacing="2" align="center" border="1" bordercolor="#004000">
		<?
			if(isset($msg_prod)){
				echo "<tr>";
					echo "<td align='center' bgcolor='#FF0000'><font style='font-size:14px; color:#FFFFFF;'>".$msg_prod."</font></td>";
				echo "</tr>";
			}
			if(isset($msg_estoq)){
				echo "<tr>";
					echo "<td align='center'><font class='AVISO'>".$msg_estoq."</font></td>";
				echo "</tr>";
			}
			if(isset($msg_estoq2)){
				echo "<tr>";
					echo "<td align='center'><font class='AVISO'>".$msg_estoq2."</font></td>";																					
				echo "</tr>";
			}
			if(isset($msg_estoq3)){
				echo "<tr>";
					echo "<td bgcolor='#FF0000' align='center'><font size='3' color='#FFFFFF'>:::::::::::: ".$msg_estoq3." ::::::::::::</font></td>";
				echo "</tr>";
			}							
		?>
		<? if($pro_det != "ok"){ ?>
          <tr>
            <td width="100%" bgcolor="#FFFFEA" align="center">
            <table>
     <? if (!isset($edtRef) ) { $edtRef = 'REF.'; };  if (!isset($edtNomeprod) ) { $edtNomeprod = 'DESCRIÇÃO'; } ; ?>
                <tr>
                  <td colspan="7" ><img src="../imagens/cod_prod.jpg">
                    <input name="edtProd" type="text" onBlur="nome_prod(); javascript: lstEscala.focus();" value="<?=$edtProd?>" size="8">
                    <input name="edtRef" type="hidden" value="<?=$edtRef?>" size="8" readonly>
                    <input name="edtNomeprod" type="text" value="<?=$edtNomeprod?>" size="50" readonly>
                    <a href="#" onClick="javascript:popup('pesq_prod.php?tela=pedidoalt&campo=formPesq.edtBusca&edtPedido=<?=$edtPedido?>&lstCli=<?=$lstCli?>&prod_estoque=s&incluir_prod=s&produtos=ok&trava=disabled&trava_list=disabled&edtNumPed=<?=$edtNumPed?>&edtSit=<?=$edtSit?>&trava_cab=<?=$trava_cab?>&lstVend=<?=$lstVend?>&lstTV=<?=$lstTV?>&edtEmissao=<?=muda_data_en($edtEmissao)?>',850,550,'center','center',POP_tot);"><img src="../imagens/pesq.jpg" border='0' alt="Pesquisar Produtos"></a>
                  </td>
                </tr>
                <tr>
                  <td><img src="../imagens/escala1.jpg"></td>
                  <td><img src="../imagens/grupo.jpg"></td>
                  <td><img src="../imagens/escala2.jpg"></td>
                  <td><img src="../imagens/loja2.jpg"></td>
                  <td><img src="../imagens/qtde.jpg"></td>
                  <td><img src="../imagens/valor_unit.jpg"></td>
                  <td><img src="../imagens/valor_total.jpg"></td>
                </tr>
                <tr>
                  <td><select name="lstEscala" style="width:180; background-color:#D7FFD7;">
		            <option value='ESC1' selected>Escolha a Escala 1</option>
                      <?
					 /*      if ($lstEscala == "ESC1" || $lstEscala == ""){
							 $foralinha = " where foralinha <> 'S' ";  
						   }else{
							 $foralinha = "";  							   
						   }  */
							$sql_esc   = "SELECT esc_cod, esc_descabv from escala ".$foralinha." order by esc_descabv;";

							$query_esc = mysql_query($sql_esc,$conexao);
							if (mysql_num_rows($query_esc) > 0){
								while($linha_esc = mysql_fetch_object($query_esc)){
									if($linha_esc->esc_cod == $escala){
										echo "<option value='".$linha_esc->esc_cod."' selected>".$linha_esc->esc_descabv."</option>";
									}else{
										echo "<option value='".$linha_esc->esc_cod."'>".$linha_esc->esc_descabv."</option>";
									}
								}
							}
						?>
                    </select> </td>
                  <td><select name="lstProg" id="lstProg" style="width:150; background-color:#D7FFD7;">
		            <option value='GRU1' selected>Escolha o Grupo</option>
                    <?
							$sql_prog   = "SELECT prog_cod, prog_descabv from progrupo order by prog_descabv;";
							$query_prog = mysql_query($sql_prog,$conexao);
							if (mysql_num_rows($query_prog) > 0){
								while($linha_prog = mysql_fetch_object($query_prog)){
									if($linha_prog->prog_cod == $progrupo){
										echo "<option value='".$linha_prog->prog_cod."' selected>".$linha_prog->prog_descabv."</option>";
									}else{
										echo "<option value='".$linha_prog->prog_cod."'>".$linha_prog->prog_descabv."</option>";
									}
								}
							}
						?>
                  </select></td>
                  <td>
                    <select name="lstCor" style="width:210; background-color:#D7FFD7;">
		            <option value='ESC2' selected>Escolha a Escala 2</option>
                      <?
							$sql_cor   = "SELECT cor_cod, cor_descabv FROM cores order by cor_descabv;";
							$query_cor = mysql_query($sql_cor,$conexao);
							if (mysql_num_rows($query_cor) > 0) {
								while($linha_cor = mysql_fetch_object($query_cor)){
									if($linha_cor->cor_cod == $cor){
										echo "<option value='".$linha_cor->cor_cod."' selected>".$linha_cor->cor_descabv."</option>";
									}else{
										echo "<option value='".$linha_cor->cor_cod."'>".$linha_cor->cor_descabv."</option>";
									}
								}
							}
						?>
                    </select></td>
                  <td><select name="lstLoja" style="width:120; background-color:#FFFFC0;">
                    <?
							$sql_uf   = "SELECT lj_estado FROM loja where lj_cod = '$ljcod';";
							$query_uf = mysql_query($sql_uf,$conexao);
							if (mysql_num_rows($query_uf) > 0){
								$linha_uf = mysql_fetch_object($query_uf);
							}
							$sql_loja = "SELECT lj_cod, lj_sigla FROM loja where lj_estado = '".$linha_uf->lj_estado."' order by lj_fantasia;";
							$query_loja = mysql_query($sql_loja,$conexao);

							if (mysql_num_rows($query_loja) > 0) {
								while($linha_loja = mysql_fetch_object($query_loja)){
								  if(!isset($lstLoja)){
									if($linha_loja->lj_cod == $ljcod){
										echo "<option value='".$linha_loja->lj_cod."' selected>".$linha_loja->lj_sigla."</option>";
									}else{
										echo "<option value='".$linha_loja->lj_cod."'>".$linha_loja->lj_sigla."</option>";
									}
								  }else{
									if($linha_loja->lj_cod == $lstLoja){
										echo "<option value='".$linha_loja->lj_cod."' selected>".$linha_loja->lj_sigla."</option>";
									}else{
										echo "<option value='".$linha_loja->lj_cod."'>".$linha_loja->lj_sigla."</option>";
									}
                                  }
								}
							}
							?>
                    </select> </td>
                  <td><input name="edtqtd" style="text-align:right; background-color:#FFFFC0; width:60px" type="text" value="<?=$edtqtd?>"></td>
<?
   include ("calculo_precos.php");
?>
                  <td><input type="text" name="edtValunit" style="text-align:right; background-color:#FFFFC0;" size="11" maxlength="10" onBlur= "javascript: calc_total(); btnIncProd.focus();" value="<?=number_format($edtValunit,'2',',','.')?>"> </td>
                  <td><input type="text" style="text-align:right; background-color:#FF0000; color:#FFFFFF;" name="edtTotal" size="12" readonly></td>
                </tr>
              </table></td>
          </tr>
		<tr>
            <td bgcolor='#DFFFDF' align="center">
				<input type="button" name="btnIncProd" style="font-weight:bold; color:#000000; width:170; background-color:#FFFF00;" value="[ Incluir Produto ]" onClick="submit_action('pedido_alt.php?campo=formpedido.edtProd&prod_estoque=s&incluir_prod=s&produtos=ok&trava=disabled&trava_list=disabled&edtNumPed=<?=$edtNumPed?>&edtSit=<?=$edtSit?>&lstCli2=<?=$lstCli?>&lstCli=<?=$lstCli?>&trava_cab2=<?=$trava_cab2?>&trava_cab=<?=$trava_cab?>&lstVend=<?=$lstVend?>&lstTV=<?=$lstTV?>&edtEmissao=<?=muda_data_en($edtEmissao)?>&trava_ped=readonly');" <?=travalist?>>
                <input type="button" name="finalizar" style="font-weight:bold; color:#000000; width:170; background-color:#FFFF00;" value="[ .:: Finalizar Pedido ::. ]" onClick="submit_action('pedido_alt.php?flag=finalizar&alteraritem=<?=$alteraritem?>'); javascript:popup('pedido_rel.php?flag=finalizar&edtNumPed=<?=$edtNumPed?>&edtPedido=<?=$edtPedido?>&lstLoja=<?=$lstLoja?>',920,650,'center','center',POP_tot) ;">
                <input type="button" name="btnTroca" style="color:#000000; width:170; background-color:#FFFF80;" value="[ Troca de Produtos ]" onClick="troca();">
                <input type="button" name="btnIncProd2" style="color:#000000; width:210; background-color:#FFFF80;" value="[ Incluir Prod. p/ Ped. de Fábrica ]" onClick="submit_action('pedido_alt.php?campo=formpedido.edtProd&edtPedido=<?=$edtPedido?>&edtProd=<?=$edtProd?>&prod_estoque=s&incluir_prod=s&produtos=ok&trava=disabled&trava_list=disabled&edtNumPed=<?=$edtNumPed?>&edtSit=<?=$edtSit?>&lstLoja=<?=$lstLoja?>&incluirpc=s&trava_cab2=disabled&trava_cab=disabled&trava_ped=readonly&lstCli2=<?=$lstCli?>&lstCli=<?=$lstCli?>&edtEmissao=<?=muda_data_en($edtEmissao)?>&lstVend=<?=$lstVend?>&lstTV=<?=$lstTV?>&trava_ped=readonly');" <?=travalist?>>
			</td>
		</tr>
        
		<tr> 
            <td background="../imagens/back.jpg" align="center">
            <table width="100%" border="1" bordercolor='#00D500'>
                <?
				 // $sql_totalpedido = "SELECT DISTINCT SUM(pm_valtot) as totalpedido FROM pedcad, pedmov
				//			  WHERE ped_num = pm_num AND pm_num = '$edtNumPed' AND ped_loja = '$ljcod'
                  //            AND lj_estado = '".$linha_uf->lj_estado."' AND pm_loja = lj_cod; ";
				 // $query_totalpedido = mysql_query($sql_totalpedido ,$conexao)or die("Erro na consulta dos Itens do Pedido!");
              //    echo $sql_totalpedido;
                //  if(mysql_num_rows($query_totalpedido) > 0){
				  //  $linha_totalpedido = mysql_fetch_object($query_totalpedido);
				  //  $totalpedido = $linha_totalpedido->totalpedido;
				  //  if ($totalpedido != "") {
                ?>
                <tr>
                  <td background="../imagens/back.jpg" align="center" bgcolor="#00D500"><font size='2' color="#000000"><u>Grade de Produtos Incluídos</u></font></td>
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
								$sql_show = "SELECT DISTINCT pm_prod, pm_cor,pm_loja, pm_escala,
                                                             pm_progrupo, pm_es, pm_qtd,
                                                             pm_valuni,pm_valtot
											 FROM pedmov
											 WHERE pm_num = '$edtNumPed' AND pm_lojaloc = '$ljcod';";
								       //echo $sql_show;
								$query_show = mysql_query($sql_show,$conexao)or die("Erro na Exibição");
								if(mysql_num_rows($query_show) > 0){
									$i = 1;
									while($linha_itens = mysql_fetch_object($query_show)){
                                       static $flagcolor = false;
                                       if ($flagcolor = !$flagcolor){
                                         $color = "#FFFFC0";
                                       }else{
                                         $color = "#FFFFFF";
                                       }
                                        $totalpedido = $totalpedido + $linha_itens->pm_valtot;
										
//codigo novo aki
									   //pegando o produto
										$sql_pro = "SELECT pro_descabv, pro_foralinha FROM produtos WHERE pro_cod = '".$linha_itens->pm_prod."';";
										$query_pro = mysql_query($sql_pro, $conexao) or die ("Erro na Consulta 2!");
										$linha_pro = mysql_fetch_object($query_pro);

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
										
										if ($linha_pro->pro_foralinha == "S"){
										 $foralinha = '<font color="#FF0000"><b> ( FORA DE LINHA )</b></font>';
										}else{
										 $foralinha = '';
										}
										
										echo "<tr>";
											echo "<td align='center' bgcolor='".$color."' width='4%'><font color='#000000'>".$linha_itens->pm_prod."</font></td>";
											echo "<td align='left' bgcolor='".$color."' width='22%'><font color='#400000'>".$linha_pro->pro_descabv."".$foralinha."</font></td>";
											echo "<td align='center' bgcolor='".$color."' width='10%'><font color='#400000'>".$linha_escala_->esc_descabv."</font></td>";
											echo "<td align='center' bgcolor='".$color."' width='10%'><font color='#400000'>".$linha_prog_->prog_descabv."</font></td>";
											echo "<td align='center' bgcolor='".$color."' width='10%'><font color='#400000'>".$linha_cor_->cor_descabv."</font></td>";
											echo "<td align='right' bgcolor='".$color."' width='5%'><font color='#400000'>".number_format($linha_itens->pm_qtd,'2',',','.')."</font></td>";
											echo "<td align='right' bgcolor='".$color."' width='10%'><font color='#400000'>".number_format($linha_itens->pm_valuni,'2',',','.')."</font></td>";
											echo "<td align='right' bgcolor='".$color."' width='10%'><font color='#FF0000'>".number_format($linha_itens->pm_valtot,'2',',','.')."</font></td>";
											echo "<td align='center' bgcolor='".$color."' width='2%'><font color='#000000'>".$linha_itens->pm_es."</font></td>";
										    echo "<td align='center' bgcolor='".$color."' width='14%'><font color='#000000'>".$linha_loja_->lj_sigla."</font></td>";
                                    ?>
								      <td align='center' bgcolor='<?=$color?>' width='7%'><a href='pedido_cad.php?flag=excluir_item&edtNumPed=<?=$edtNumPed?>&prod_estoque=s&produtos=ok&trava=disabled&trava_list=disabled&prod=<?=$linha_itens->pm_prod?>&escala=<?=$linha_itens->pm_escala?>&cor=<?=$linha_itens->pm_cor?>&progrupo=<?=$linha_itens->pm_progrupo?>&loja=<?=$linha_itens->pm_loja?>&lstCli2=<?=$lstCli?>&lstCli=<?=$lstCli?>&lstVend=<?=$lstVend?>&lstTV=<?=$lstTV?>'><img src='../imagens/apagar.gif' border="no" alt="Excluir"></a></td>
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
									$sql_descontos   = "SELECT ped_desconto, ped_descontop from pedcad
													     WHERE ped_num = '$edtNumPed' AND ped_loja = '$ljcod';";
									$query_descontos = mysql_query($sql_descontos,$conexao);
									if(mysql_num_rows($query_descontos) > 0){
									  $linha_descontos = mysql_fetch_object($query_descontos);
									  $edtDesconto = $linha_descontos->ped_desconto; 
									}
								?>
                                
                                <td bgcolor='#FFE1E1' align='left'>
                                 R$: <input name="edtDesconto" type="text" style="width:100; color:#F00; background-color:#FFF; text-align:right;" value="<?=number_format($edtDesconto,'2',',','.')?>"> 
                                </td>
                                <td bgcolor='#FFE1E1' align='left'>
                                <input type="button" name="btnDesconto" style="font-weight:bold; color:#FFF; width:170; background-color:#F00;" value="[ Aplicar Desconto ]" onClick="submit_action('pedido_alt.php?campo=formpedido.edtProd&prod_estoque=s&incluir_desconto=s&produtos=ok&trava=disabled&trava_list=disabled&edtNumPed=<?=$edtNumPed?>&edtSit=<?=$edtSit?>&lstCli2=<?=$lstCli?>&lstCli=<?=$lstCli?>&trava_cab2=<?=$trava_cab2?>&trava_cab=<?=$trava_cab?>&lstVend=<?=$lstVend?>&lstTV=<?=$lstTV?>&edtEmissao=<?=muda_data_en($edtEmissao)?>&trava_ped=readonly');" <?=travalist?>>
                                </td>
                              </tr>
                             </table>
                            </td>
                            <td bgcolor='#FFFFEA' colspan='3' align='right'>
                              <img src="../imagens/total_ped.jpg">
                            </td>
                            <td bgcolor='#FF0000' align='right'>
                             <? $descontoemvalor = 0;
							    if ($edtDesconto != "0"){
								  $descontoemvalor = $edtDesconto;  
								}
							    $totalpedido = $totalpedido - $descontoemvalor; //total do pedido menos o desconto em valor
							 ?>
                              <font size="3" color="#FFFFFF"><b><?=number_format($totalpedido,'2',',','.')?></b></font>
                            </td>
                            <td bgcolor='#FFFFEA' colspan='3' align='right'>
                              <font size="2" color="#FFFFEA">.</font>
                            </td>
                           </tr>
						<? }else{ ?>
                           <tr>
                            <td bgcolor='#FFFFEA' colspan='7' align='right'>
                              <img src="../imagens/total_ped.jpg">
                            </td>
                            <td bgcolor='#FF0000' align='right'>
                              <font size="3" color="#FFFFFF"><b><?=number_format($totalpedido,'2',',','.')?></b></font>
                            </td>
                            <td bgcolor='#FFFFEA' colspan='3' align='right'>
                              <font size="2" color="#FFFFEA">.</font>
                            </td>
                           </tr>
                        <? } ?>                                
                         </table></td>
                </tr>
                <? //  }
                  // }  ?>
				</table>
			</td>
		</tr>
		<? } ?>
		</table>
	</td>
</tr>
</table>
</form>
</body>
</html>
<? }  //fim do if ($caixatravado == "S"){ 
  include("rodape.php");
?>