<?
 include("conexao2.inc.php");
 include("funcoes2.inc.php");
 include("dglogin1.php");

 $arquivo = "pedido_fecha.php";
 include("auditoria.php");

 $data = date("d/m/Y");
 $data = muda_data_en($data);
 $hora = date("H:i:s");
 
	if($REQUEST_METHOD == "POST"){
		if($flag == "busca"){
 		 $sql_busca_f = "SELECT ped_num FROM pedcad
				         WHERE ped_num = '$edtNumPed' AND ped_situacao = 'F' AND ped_loja = '$ljcod'";
         $query_busca_f = mysql_query($sql_busca_f,$conexao)or die("Erro na Busca do Pedido 1!");
		 if(mysql_num_rows($query_busca_f)>0){
			$msg_fecha = "Erro: Pedido já Fechado!";
         }else{
			$sql_busca = "SELECT distinct ped_num, ped_situacao, ped_valliq, ped_emissao,
                          ped_cliente, ped_valprod, ped_loja, cli_cgccpf, cli_razao
                          FROM pedcad, clientes
						  WHERE ped_num = '$edtNumPed' AND cli_cgccpf = ped_cliente AND ped_loja = '$ljcod';";
            $query_busca = mysql_query($sql_busca,$conexao)or die("Erro na Busca do Pedido 2!");
			if(mysql_num_rows($query_busca)>0){
                $linha_busca = mysql_fetch_object($query_busca);
				if($linha_busca->ped_situacao == "A" OR $linha_busca->ped_situacao == "D"){
					$edtCliente = $linha_busca->cli_razao;
					$edtEmissao = muda_data_pt($linha_busca->ped_emissao);
					$edtValor = number_format($linha_busca->ped_valliq,'2',',','.');
				}else{
					$msg_fecha = "Erro: Este Pedido Não está Aberto!";
					$trava = "disabled";
				}
			}else{
				$msg_fecha = "Erro: Este Pedido Não Existe!";
				$trava = "disabled";
			}
	      }
		}else{ //flag = fecha
 		 $sql_busca_f = "SELECT ped_num FROM pedcad
						  WHERE ped_num = '$edtNumPed' AND ped_loja = '$ljcod' AND ped_situacao = 'F'";
         $query_busca_f = mysql_query($sql_busca_f,$conexao)or die("Erro na Busca do Pedido!");
		 if(mysql_num_rows($query_busca_f)>0){
			$msg_fecha = "Erro: Pedido já Fechado!";
         }else{
			$msg_fecha = "";
			//finalizar o pedido
			$sql = "SELECT ped_num FROM pedcad WHERE ped_num = '$edtNumPed' AND ped_loja = '$ljcod';";
			$query = mysql_query($sql,$conexao)or die("Erro na Consulta do Pedido!");
			if(mysql_num_rows($query)>0){
			 $linha = mysql_fetch_object($query);	
			 //verifica os itens se tinha estoque e agora nao tem mais
			 $sql_estq = "SELECT pm_num, pm_loja, pm_prod, pm_cor, pm_escala, pm_progrupo, pm_qtd, pm_es,
                                    pm_pc, pm_estoqueok, pm_entregue
						   FROM pedmov WHERE pm_num ='$edtNumPed' AND pm_lojaloc = '$ljcod';";
				$query_estq = mysql_query($sql_estq,$conexao)or die("Erro na Consulta dos Itens do Pedido");

				while($linha_estq = mysql_fetch_object($query_estq)){
                    if ($linha_estq->pm_estoqueok == "S"){
					 $sql_busca   = "SELECT sal_estreal, sal_reserva FROM saldos
									    WHERE sal_cod      = '".$linha_estq->pm_prod."'
									   	  AND sal_cor      = '".$linha_estq->pm_cor."'
									   	  AND sal_escala   = '".$linha_estq->pm_escala."'
									   	  AND sal_progrupo = '".$linha_estq->pm_progrupo."'
									   	  AND sal_loja     = '".$linha_estq->pm_loja."';";
					 $query_busca = mysql_query($sql_busca)or die("Erro na Consulta do Estoque!");
				     if(mysql_num_rows($query_busca) > 0) {
                      $linha_busca = mysql_fetch_object($query_busca);
                      if ($linha_busca->sal_estreal < $linha_estq->pm_qtd){
				        $msg_fecha = "O Produto ".$linha_estq->pm_prod." neste pedido tinha saldo, mas foi vendido! 
									  Volte ao pedido, clicando abaixo";
				        $tinhaestoque = "S";
                      }
				     }
				    }
				}

				//VARRE OS ITENS
				$sql_mv = "SELECT pm_num, pm_prod, pm_loja, pm_lojaloc, pm_cor, pm_escala, pm_progrupo, pm_qtd, 
								  pm_valuni, pm_valtot, pm_entregue, pm_es, pm_incluido, pm_alterado, pm_dtincluido,
								  pm_dtalterado, pm_qtdentregue, pm_dtentrega, pm_horaentrega, pm_comissao,
                                  pm_pc, pm_pvtroca, pm_estoqueok, pm_promocao, pm_hora
						   FROM pedmov WHERE pm_num ='$edtNumPed' AND pm_lojaloc = '$ljcod';";
				$query_mv = mysql_query($sql_mv,$conexao) or die ("Erro na Consulta dos Itens do Pedido");

				if(mysql_num_rows($query_mv) > 0) {
				 if ($tinhaestoque != "S") {
					$ehumtroca = 'N';
					while($linha_mv = mysql_fetch_object($query_mv)) {
					
					    $lojaatual = $linha_mv->pm_loja;

						// buscando o saldo do produto
						$sql_saldos   = "SELECT sal_estreal, sal_reserva FROM saldos
									      WHERE sal_cod      = '".$linha_mv->pm_prod."'     AND
									   	        sal_cor      = '".$linha_mv->pm_cor."'      AND
									   	        sal_escala   = '".$linha_mv->pm_escala."'   AND
									   	        sal_progrupo = '".$linha_mv->pm_progrupo."' AND
									   	   	    sal_loja     = '".$linha_mv->pm_loja."';";
						$query_saldos = mysql_query($sql_saldos) or die ("Erro na Consulta do Estoque!");
						
                        if(mysql_num_rows($query_saldos) > 0){ 
							//guarda as variáveis de saldo, reserva e diferenca
                           $linha_saldos    = mysql_fetch_object($query_saldos);
						   $saldoanterior   = $linha_saldos->sal_estreal;
						   $reservaanterior = $linha_saldos->sal_reserva;
						   $diferenca1	    = $linha_saldos->sal_estreal - $linha_mv->pm_qtd;
						   $saldogerado     = '';
						}else{ //se nao encontrar o saldo ele inclui
						   $saldoanterior   = 0;
						   $reservaanterior = 0;
						   $diferenca1	    = -1;

						   $sql_buscabc = "SELECT * FROM barcodes
										  WHERE bc_prod = '".$linha_mv->pm_prod."' AND bc_escala1 = '".$linha_mv->pm_escala."' AND
										        bc_grupo = '".$linha_mv->pm_progrupo."' AND bc_escala2 = '".$linha_mv->pm_cor."';";
						   $query_buscabc = mysql_query($sql_buscabc,$conexao)or die("Erro na Busca do CodBarra!");
						   if(mysql_num_rows($query_buscabc) <= 0){
						 
							  //pegando o codbarra atual
							  $sql_cb1   = "SELECT codbarra FROM parametros";
							  $query_cb1 = mysql_query($sql_cb1);
							  $linha_cb1   = mysql_fetch_object($query_cb1);
							  $codbar1 = $linha_cb1->codbarra + 1;

							  $dig1  = substr($codbar1,0,1);  $dig2  = (substr($codbar1,1,1))*3;  
							  $dig3  = substr($codbar1,2,1);  $dig4  = (substr($codbar1,3,1))*3;  
							  $dig5  = substr($codbar1,4,1);  $dig6  = (substr($codbar1,5,1))*3;
							  $dig7  = substr($codbar1,6,1);  $dig8  = (substr($codbar1,7,1))*3;  
							  $dig9  = substr($codbar1,8,1);  $dig10 = (substr($codbar1,9,1))*3; 
							  $dig11 = substr($codbar1,10,1); $dig12 = (substr($codbar1,11,1))*3;	  	  	  
							  
							  $somadig = $dig1 + $dig3 + $dig5 + $dig7 + $dig9 + $dig11 + $dig2 + $dig4 + $dig6 + $dig8 + $dig10 + $dig12;
							  $digito = 10 - ($somadig % 10 );
							  if ($digito == "10"){
								$digito = 0;
							  }

							  $codbaratual1 = $codbar1.$digito;

							  //gerando codbarra para saldos
							  $sql_bc1   = "INSERT INTO barcodes 
													  (bc_codbarra, bc_prod, bc_escala1, 
													   bc_grupo, bc_escala2, bc_pedidof,
													   bc_loja, bc_login, bc_data, bc_hora)
										   VALUES     ('".$codbaratual1."', '".$linha_mv->pm_prod."', '".$linha_mv->pm_escala."', 
													   '".$linha_mv->pm_progrupo."', '".$linha_mv->pm_cor."', '',
													   '', '$acelogin', '$data','$hora')";
							  //echo $sql_pc.'sql_pc<br>';							 
							  $query_bc1 = mysql_query($sql_bc1) or die ("Erro na Inclusao do CobBarra 1");
							  
							  //atualizando o cod barra atual
							  $sql_updcb1   = "UPDATE parametros SET codbarra = '".$codbar1."'";
							  $query_updcb1 = mysql_query($sql_updcb1);
						   }							  

						   //insere saldo senão existir
						   $sql_inssaldo = "INSERT INTO saldos ( sal_cod, sal_loja, sal_cor, sal_escala, sal_progrupo, 
						   								     	 sal_estreal, sal_reserva, sal_incluido, sal_dtincluido, sal_pv, sal_codbarra )
													    VALUES ( '".$linha_mv->pm_prod."','".$linha_mv->pm_loja."',
															     '".$linha_mv->pm_cor."', '".$linha_mv->pm_escala."',
															     '".$linha_mv->pm_progrupo."','0','0','S', '$data', 
															     '".$linha_mv->pm_num."', '".$codbaratual1."' );";
						   //echo $sql_inssaldo.'$sql_inssaldo<br>';
						   $query_inssaldo = mysql_query($sql_inssaldo, $conexao) or die ("Erro na Inclusão do Saldo");
						 						   
						   $saldoatual          = 0;
						   $reservaatual	    = 0;				   
						   $atualizarsaldo      = '';
						   $qtdpedidocompra     = $linha_mv->pm_qtd;
						   $gerarpedidodecompra = 'S';
						   $qtdreserva          = 0;
						   $gerarreserva        = '';
						   $saldogerado 		= 'S';
						}

						//produtos de saida
					    if ($linha_mv->pm_es == "S") {
					     if ($linha_mv->pm_loja == $ljcod) {
					      if ($linha_mv->pm_pc != "S") {
						   if ($diferenca1 >= 0) {
						       //atualizando saldo
						   	   $saldoatual     = $linha_saldos->sal_estreal - $linha_mv->pm_qtd;
						   	   $reservaatual   = $linha_saldos->sal_reserva + $linha_mv->pm_qtd;
							   $atualizarsaldo = 'S';
							   
							   $gerarpedidodecompra = '';
							   
							   //criando reserva
						   	   $qtdreserva    = $linha_mv->pm_qtd;							  
							   $gerarreserva  = 'S';
							   
							   $saldogerado   = '';

						   }else{
						    if ($saldogerado != "S"){
						      //atualizando saldo
							  $saldoatual          = 0;
							  $reservaatual	       = $linha_saldos->sal_reserva + $linha_saldos->sal_estreal;;				   
							  $atualizarsaldo      = 'S';
							  
							  //cria pedido de compra da diferenca2
							  $diferenca2   = $linha_mv->pm_qtd - $linha_saldos->sal_estreal;
							  $qtdpedidocompra     = $diferenca2;							  
							  $gerarpedidodecompra = 'S';							  
							  
							  //criando reserva para o q tinha em estoque
							  $qtdreserva   	   = $linha_saldos->sal_estreal;
							  $gerarreserva 	   = 'S';
							  
							  $saldogerado 		   = '';
							}							  
						   } 
						  }else{ //else do if ($linha_mv->pm_pc != "S") {

							   $saldoatual          = 0;
							   $reservaatual	    = 0;				   
							   $qtdreserva          = 0;
							   $qtdpedidocompra     = $linha_mv->pm_qtd;
							   
							   $atualizarsaldo      = '';
							   //cria pedido de compra da diferenca1
							   $gerarpedidodecompra = 'S';
	
							   $gerarreserva        = '';
							   $saldogerado 		= '';

						  } //fim do if ($linha_mv->pm_pc != "S") {
						 }else{ //else do if ($linha_mv->pm_loja == $ljcod) {
					      if ($linha_mv->pm_pc != "S") {
						   if ($diferenca1 >= 0) {
						   	   $saldoatual   = $linha_saldos->sal_estreal - $linha_mv->pm_qtd;
						   	   $reservaatual = $linha_saldos->sal_reserva + $linha_mv->pm_qtd;
							   $qtdreserva          = $linha_mv->pm_qtd;							  
							   $qtdpedidocompra     = 0;
							   
							   $atualizarsaldo      = 'S';
							   //cria pedido de compra da diferenca1
							   $gerarpedidodecompra = '';
	  						   //criando reserva
							   $gerarreserva        = 'S';
							   $saldogerado 		= '';
						   }else{
							   $saldoatual          = 0;
							   $reservaatual	    = 0;				   
							   $atualizarsaldo      = '';
							   $qtdpedidocompra     = 0;
							   $gerarpedidodecompra = '';
							   $qtdreserva          = 0;
							   $gerarreserva        = '';
							   $saldogerado 		= '';
						   }
						  }else{
							   $saldoatual          = 0;
							   $reservaatual	    = 0;				   
							   $atualizarsaldo      = '';
							   $qtdpedidocompra     = 0;
							   $gerarpedidodecompra = '';
							   $qtdreserva          = 0;
							   $gerarreserva        = '';
							   $saldogerado 		= '';
						  }
						 } //fim do }else{ //else do if ($linha_mv->pm_loja == $ljcod) {
						}//fim do if ($linha_mv->pm_es == "S") {

//////////////////////////////////////////////////////// PRODUTOS DE ENTRADA - INICIO \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

					    if ($linha_mv->pm_es == "E") {

						 $ehumtroca = 'S';
						 // guarda a situacao pm_entregue do produto da troca
						 //SITUACÕES: N (NÃO), S (SIM), T (TOTAL) E P (PARCIAL)						 
						 $sql_prodtroca    = " SELECT pm_entregue, pm_qtdentregue FROM pedmov 
												  WHERE pm_num      = '".$linha_mv->pm_pvtroca."'  AND
														pm_prod     = '".$linha_mv->pm_prod."'     AND
														pm_loja     = '".$linha_mv->pm_loja."'     AND
														pm_escala   = '".$linha_mv->pm_escala."'   AND
														pm_cor      = '".$linha_mv->pm_cor."'      AND
														pm_progrupo = '".$linha_mv->pm_progrupo."' ";
						 //echo $sql_prodtroca.'sql_prodtroca<br>';
						 $query_prodtroca = mysql_query($sql_prodtroca) or die ("Erro na Verificação do Produto trocado");
						 if(mysql_num_rows($query_prodtroca) > 0){
						   $linha_prodtroca = mysql_fetch_object($query_prodtroca);
						 }	
						 
						 $tempfeestoque = ''; $qtdadevolver = 0;
						 // verifica se tem pedido de compra 
						 //SITUACÕES: A (ABERTO), B (BAIXADO), C (CANCELADO), X (CANC. TROCA) E P (PARCIAL)
						 $sql_pedidocompra    = "SELECT pc_qtd, pc_situacao FROM pedcomp 
						 						  WHERE pc_pedvend  = '".$linha_mv->pm_pvtroca."' AND
												        pc_prod     = '".$linha_mv->pm_prod."'    AND
												        pc_loja     = '".$linha_mv->pm_loja."'    AND
												        pc_escala   = '".$linha_mv->pm_escala."'  AND
												        pc_cor      = '".$linha_mv->pm_cor."'     AND
												        pc_progrupo = '".$linha_mv->pm_progrupo."' ";
						 //echo $sql_pedidocompra.'sql_pedidocompra<br>';
						 $query_pedidocompra = mysql_query($sql_pedidocompra) or die ("Erro na Verificação do Pedido de Compra");
						 if(mysql_num_rows($query_pedidocompra) > 0){
						  $linha_pedidocompra = mysql_fetch_object($query_pedidocompra);
						  if ($linha_pedidocompra->pc_qtd != $linha_mv->pm_qtd ){
							 $tempfeestoque = 'S';
							 $qtdadevolver = $linha_mv->pm_qtd - $linha_pedidocompra->pc_qtd;
							 //echo 'entrei na quantidade diferente do estoque qtdadevolver: '.$qtdadevolver.' linha_pedidocomprapc_qtd: '.$linha_pedidocompra->pc_qtd.'<br>';
						  }else{
							 //echo 'nao entrei na quantidade diferente do estoque<br>';							  
						  }
						  //SE SITUACAO PEDIDO DE COMPRA FOR BAIXADO
						  if ($linha_pedidocompra->pc_situacao == 'B') {
							//entrega for S ou T (SIM ou TOTAL)
						    //if ($linha_prodtroca->pm_entregue == 'S' || $linha_prodtroca->pm_entregue == 'T') {
						   	  $saldoatual 			= $linha_saldos->sal_estreal + $linha_pedidocompra->pc_qtd;
						   	  $reservaatual 	    = $linha_saldos->sal_reserva;
							  $qtdreserva          = 0;
							  $qtdpedidocompra     = 0;
							  $atualizarsaldo 	   = 'S';
							  //cria pedido de compra
							  $gerarpedidodecompra = '';
	  						  //criando reserva
							  $gerarreserva        = '';
							  $saldogerado 		   = '';
							//}
						  }else{//fim do if ($linha_pedidocompra->pc_situacao == 'B') {
						   	  $saldoatual 		   = $linha_saldos->sal_estreal;
							  //echo 'entrei aki pq o pf está cancelado. saldo atual é:'.$saldoatual.'<br>';
						   	  $reservaatual 	   = $linha_saldos->sal_reserva;

							  $qtdreserva          = 0;
							  $qtdpedidocompra     = 0;
							   
							  $atualizarsaldo 	   = '';
							  //cria pedido de compra
							  $gerarpedidodecompra = '';
	  						  //criando reserva
							  $gerarreserva        = '';
							  $saldogerado 		   = '';
						  }
						  
						 }else{ //fim do if(mysql_num_rows($query_pedidocompra) > 0){
							 //echo 'entrei pedido de fabrica nao encontrado<br>';							  							 
							//entrega for S ou T (SIM ou TOTAL)
						    //if ($linha_prodtroca->pm_entregue == 'S' || $linha_prodtroca->pm_entregue == 'T') {

								// buscando o saldo do produto
								$sql_saldos2   = "SELECT sal_estreal, sal_reserva FROM saldos
												  WHERE sal_cod      = '".$linha_mv->pm_prod."'     AND
														sal_cor      = '".$linha_mv->pm_cor."'      AND
														sal_escala   = '".$linha_mv->pm_escala."'   AND
														sal_progrupo = '".$linha_mv->pm_progrupo."' AND
														sal_loja     = '".$linha_mv->pm_loja."';";
								//echo $sql_saldos2.'$sql_saldos2<br>';						
								$query_saldos2 = mysql_query($sql_saldos2) or die ("Erro na Consulta do Estoque!");
								
								if(mysql_num_rows($query_saldos2) > 0){ 
									//guarda as variáveis de saldo, reserva e diferenca
								   $linha_saldos2    = mysql_fetch_object($query_saldos2);

								   $saldoatual 	  		= $linha_saldos2->sal_estreal + $linha_mv->pm_qtd;
								   $reservaatual 		= $linha_saldos2->sal_reserva;
	
								   $qtdreserva          = 0;
								   $qtdpedidocompra     = 0;
								   
								   $atualizarsaldo 		= 'S';
								   //cria pedido de compra
								   $gerarpedidodecompra = '';
								   //criando reserva
								   $gerarreserva        = '';
								   $saldogerado 		= '';
								}   
							//}
							
						 }//fim do else if(mysql_num_rows($query_pedidocompra) > 0){ 
							 
						 if ($tempfeestoque == 'S'){
							 //echo 'entrei no tempfeestoque = S<br>';							  							 							 
								// buscando o saldo do produto
								$sql_saldos2   = "SELECT sal_estreal, sal_reserva FROM saldos
												  WHERE sal_cod      = '".$linha_mv->pm_prod."'     AND
														sal_cor      = '".$linha_mv->pm_cor."'      AND
														sal_escala   = '".$linha_mv->pm_escala."'   AND
														sal_progrupo = '".$linha_mv->pm_progrupo."' AND
														sal_loja     = '".$linha_mv->pm_loja."';";
								//echo $sql_saldos2.'$sql_saldos2<br>';						
								$query_saldos2 = mysql_query($sql_saldos2) or die ("Erro na Consulta do Estoque!");
								
								if(mysql_num_rows($query_saldos2) > 0){ 
									//guarda as variáveis de saldo, reserva e diferenca
								   $linha_saldos2       = mysql_fetch_object($query_saldos2);
								   $saldoatual 	  		= $linha_saldos2->sal_estreal + $qtdadevolver;
								   //echo 'valores das variaveis: saldoatual = '.$saldoatual.' | $qtdadevolver: '.$$qtdadevolver;
								   $reservaatual 		= $linha_saldos2->sal_reserva;
	
								   $qtdreserva          = 0;
								   $qtdpedidocompra     = 0;
								   
								   $atualizarsaldo 		= 'S';
								   //cria pedido de compra
								   $gerarpedidodecompra = '';
								   //criando reserva
								   $gerarreserva        = '';
								   $saldogerado 		= '';
								}   
						 } //fim do if ($tempfeestoque == 'S'){
							 
							 
						} //fim do if ($linha_mv->pm_es == "E") {

//////////////////////////////////////////////////////// PRODUTOS DE ENTRADA - FIM \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

							if ($gerarreserva == 'S') {
						   	  //cria reserva
							 if ($linha_mv->pm_es == "S") {							  
						   	  $sql_reserva    = "INSERT INTO reserva 
						   	    							(res_prod, res_cor, res_escala, res_prog, res_loja, res_qtd, 
						   	  								 res_func, res_pedido, res_situacao, res_data)
						   	  			 	     VALUES     ('".$linha_mv->pm_prod."','".$linha_mv->pm_cor."',
						   	  					  		     '".$linha_mv->pm_escala."','".$linha_mv->pm_progrupo."',
						   	   	 							 '".$linha_mv->pm_loja."','".$qtdreserva."','$acelogin',
						   	  								 '".$linha_mv->pm_num."','A','$data')";
							  //echo $sql_reserva.'sql_reserva<br>';							 
						   	  $query_reserva  = mysql_query($sql_reserva) or die ("Erro na Inclusao da Reserva");
							 }
						    }

							if ($gerarpedidodecompra == 'S'){							
							  //se for deposito, muda pra loja
							  if (($linha_mv->pm_loja == "14") || ($linha_mv->pm_loja == "26") || ($linha_mv->pm_loja == "36") || ($linha_mv->pm_loja == "13") || ($linha_mv->pm_loja == "29") || ($linha_mv->pm_loja == "39") || ($linha_mv->pm_loja == "42") ){ 
							    $lojaatual = $linha_mv->pm_lojaloc;
							  }
							
						      //cria pedido de compra
							 if ($linha_mv->pm_es == "S") {

							  $sql_sig   = "SELECT lj_sigla FROM loja WHERE lj_cod = ".$lojaatual."";
							  $query_sig = mysql_query($sql_sig) or die ("Erro na Inclusao do Pedido de Compra");
							  if(mysql_num_rows($query_sig) > 0){
							     $linha_sig   = mysql_fetch_object($query_sig);
							  }

							  //pegando o codbarra atual
							  $sql_cb   = "SELECT codbarra FROM parametros";
							  $query_cb = mysql_query($sql_cb);
						      $linha_cb   = mysql_fetch_object($query_cb);
							  $codbar = $linha_cb->codbarra + 1;

							  $dig1  = substr($codbar,0,1);  $dig2  = (substr($codbar,1,1))*3;  
							  $dig3  = substr($codbar,2,1);  $dig4  = (substr($codbar,3,1))*3;  
							  $dig5  = substr($codbar,4,1);  $dig6  = (substr($codbar,5,1))*3;
							  $dig7  = substr($codbar,6,1);  $dig8  = (substr($codbar,7,1))*3;  
							  $dig9  = substr($codbar,8,1);  $dig10 = (substr($codbar,9,1))*3; 
							  $dig11 = substr($codbar,10,1); $dig12 = (substr($codbar,11,1))*3;	  	  	  
							  
							  $somadig = $dig1 + $dig3 + $dig5 + $dig7 + $dig9 + $dig11 + $dig2 + $dig4 + $dig6 + $dig8 + $dig10 + $dig12;
							  $digito = 10 - ($somadig % 10 );
							  if ($digito == "10"){
								$digito = 0;
							  }

							  $codbaratual = $codbar.$digito;

						  							  							  							  
							  //gerando pedido de fabrica
							  $sql_pc   = "INSERT INTO pedcomp 
							  				          (pc_cod, pc_data, pc_prod, pc_cor, pc_escala, 
							 						   pc_progrupo, pc_qtd, pc_qtdorig, pc_loja, pc_pedvend, pc_situacao,
							 						   pc_hora, pc_login, pc_codbarra)
							 			   VALUES     ('".$linha_sig->lj_sigla."".$linha_mv->pm_num."','$data',
										   			   '".$linha_mv->pm_prod."',
							 					 	   '".$linha_mv->pm_cor."','".$linha_mv->pm_escala."',
							 						   '".$linha_mv->pm_progrupo."','".$qtdpedidocompra."','".$qtdpedidocompra."',
							 						   '".$lojaatual."', '".$linha_mv->pm_num."','A',
							 						   '$hora','$acelogin', '".$codbaratual."' )";
							  //echo $sql_pc.'sql_pc<br>';							 
							  $query_pc = mysql_query($sql_pc) or die ("Erro na Inclusao do Pedido de Compra");

							  //gerando pedido de fabrica
							  $sql_bc   = "INSERT INTO barcodes 
							  				          (bc_codbarra, bc_prod, bc_escala1, 
													   bc_grupo, bc_escala2, bc_pedidof,
													   bc_loja, bc_login, bc_data, bc_hora)
							 			   VALUES     ('".$codbaratual."', '".$linha_mv->pm_prod."', '".$linha_mv->pm_escala."', 
										   			   '".$linha_mv->pm_progrupo."', '".$linha_mv->pm_cor."', '".$linha_mv->pm_num."',
							 						   '".$lojaatual."', '$acelogin', '$data','$hora')";
							  //echo $sql_pc.'sql_pc<br>';							 
							  $query_bc = mysql_query($sql_bc) or die ("Erro na Inclusao do CobBarra 2");
							  //atualizando o cod barra atual
							  $sql_updcb   = "UPDATE parametros SET codbarra = '".$codbar."'";
							  $query_updcb = mysql_query($sql_updcb);

							 } 
						    }					

							if ($atualizarsaldo == 'S'){ 
							  $sql_estoque = "UPDATE saldos SET sal_estreal  = '".$saldoatual."', 
							  								    sal_reserva  = '".$reservaatual."', 							  
														        sal_alterado = 'S', sal_dtalterado = '$data'
												  		  WHERE sal_cod      = '".$linha_mv->pm_prod."'     AND
												  	    		sal_cor      = '".$linha_mv->pm_cor."'      AND
												        		sal_escala   = '".$linha_mv->pm_escala."'   AND
												        		sal_progrupo = '".$linha_mv->pm_progrupo."' AND
												        		sal_loja     = '".$lojaatual."' ;";
							  //echo $sql_estoque.'sql_estoque<br>';							 
							  $query_estoque = mysql_query($sql_estoque) or die ("Erro na Atualização do Estoque!");

							  //gravando logs armadilhas fechamento de estoque
							  $sql_estoque = mysql_escape_string($sql_estoque);
							  $sql_armadilha   = "INSERT INTO logarmadilhas 
														  (logar_loja, logar_pedido, logar_login, logar_data, 
														   logar_hora, logar_sql)
												   VALUES ('".$ljcod."', '".$linha_mv->pm_num."', '".$acelogin."', '".$data."', 
														   '".$hora."', '".$sql_estoque."')";
							  $query_armadilha = mysql_query($sql_armadilha) or die ("Erro na Inclusao do CobBarra 3");
							}							

					} //fim do while com os produtos

					//$msg_fecha .= "Pedido Baixado Com Sucesso!";
					//muda a situacao do pedido para fechado e obs
					if ($ehumtroca == 'S'){
					  $desabilitapesq = " ,ped_pesqopiniao = 'S', ped_pesqopiniaodata = '$data', ped_pesqopiniaohora = '$hora', ped_pesqopiniaologin = '$acelogin', ped_pedidodetroca = 'S' ";
					}else{
					  $desabilitapesq = '';						
					}
					$sql_alt = "UPDATE pedcad 
								   SET ped_situacao = 'F', ped_obs = '$edtObs' ".$desabilitapesq."
								 WHERE ped_num = '$edtNumPed' AND ped_loja = '$ljcod'";
                    //echo $sql_alt; exit;
					$query_alt = mysql_query($sql_alt)or die("Erro na Atualização do Pedido!");
					
					//checa se tem PV Troca e cancela as liberações
					$sql_plt = "SELECT pm_pvtroca FROM pedmov 
								 WHERE pm_num = '$edtNumPed' AND pm_lojaloc = '$ljcod' AND pm_pvtroca <> '' 
						      GROUP BY pm_pvtroca ";
					$query_plt = mysql_query($sql_plt)or die("Erro na Atualização do Pedido!");
					if(mysql_num_rows($query_plt)>0){
					  while($linha_plt = mysql_fetch_object($query_plt)){
						$sql_updplt = "UPDATE pedcad SET ped_liberatroca = 'N' WHERE ped_num = '".$linha_plt->pm_pvtroca."' AND ped_loja = '$ljcod' ";
						$query_updplt = mysql_query($sql_updplt)or die(mysql_error());
					  }
					}

					$edtNumPed = "";
					$edtEmissao = "";
					$edtValor = "";
					$edtCliente = "";
                  }//fim da flag ==  tinhaestoque = ok;
					$msg_fecha .= "Pedido ".$linha_mv->pm_num." Finalizado!<br>";
				} //fim do mysql_num_rows($query_mv) > 0
			}else{
				$msg_fecha = "Erro: Este Pedido Não Existe ou já está fechado!";
			} //fim do se encontrar o pedido
		  } //mysql_num_rows($query_busca_f
		}//fim da flag==fecha
		$edtNumPed = $linha_mv->pm_num;
	} //FIM DO if($REQUEST_METHOD == "POST"){
?>
<html>
<head>
	<link rel="stylesheet" href="est_big.css" type="text/css">
	<title>:: Gercom.NET - Fechamento do Pedido de Venda ::</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<script language="JavaScript">
		function submit_action(campo, caminho){
			if(campo.value != ""){
				document.formfecha.action = caminho;
				document.formfecha.method = 'post';
				document.formfecha.submit();
			}
		}
		
		function submit_action2(caminho){
			//postando para a verificacao;
			document.formfecha.action= caminho; 
			document.formfecha.method= 'post'; 
			document.formfecha.submit();			
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

<body topmargin="0" background="../imagens/fundomain.jpg" bottommargin="0" leftmargin="0" rightmargin="0">
<? if ($menu != off) {
     include("menu_java.php");
   }
?>
<form action="pedido_fecha.php?flag=fecha&edtObs=<?=$edtObs?>&edtNumPed=<?=$edtNumPed?>&edtPedido=<?=$edtPedido?>&lstLoja=<?=$lstLoja?>" method="post" name="formfecha" id="formfecha">

  <table width="100%" border="1" cellspacing="2" cellpadding="2" bordercolor="#CCCCCC">
    <tr>
        <td align="center" width="100%" bgcolor="#004000"><font color="#FFFFFF">Fechamento do Pedido de Venda <?=$edtPedido ?></font></td>
    </tr>
	<?
		if(isset($msg_fecha)){
			echo "<tr>";
				echo "<td align='center' width='100%'><font class='AVISO'>".$msg_fecha."</font></td>";
			echo "</tr>";
		}
	?>
	<? if($flag != "fecha"){ ?>
    <?  if ($menu != off) {
    ?>
    <tr>
        <td>
			<table bgcolor ="#FFFFFF" width="100%" border="0" cellspacing="0" cellpadding="2">
				<tr>
					<td width="100%">
						<table width="100%" border="0" cellspacing="0" cellpadding="2">
							<tr>
								<td width="11%">Nº do Pedido:</td>
								<td width="46%">
									<input type="text" name="edtNumPed" value="<?=$edtNumPed?>" size="10" maxlength="10" onBlur="submit_action(this, 'pedido_fecha.php?flag=busca');">
									<a href="#" onClick="javascript: popup('pedido_cons_rap.php',650,300,'center','center',POP_tot); "><img src="../imagens/lupa.gif" width="29" height="23" border="0" alt="Consultar Pedido"></a>
								</td>
								<td width="13%">Emissão:</td>
								<td width="30%"><input type="text" name="edtEmissao" value="<?=$edtEmissao?>" readonly size="12" maxlength="10"></td>
							</tr>
						</table>
						<table width="100%" border="0" cellspacing="0" cellpadding="2">
							<tr>
								<td width="11%">Cliente:</td>
								<td width="89%"><input type="text" name="edtCliente" value="<?=$edtCliente?>" readonly size="40"></td>
							</tr>
							<tr>
								<td>Valor:</td>
								<td><input type="text" name="edtValor" value="<?=$edtValor?>" readonly size="10" maxlength="10"></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
    </tr>
    <tr>
        <td align="center">
			<input type="submit" value="Finalizar" <?=$trava?>>
			<input type="reset" value="Limpar">
		</td>
    </tr>
<?
    } // fim do menu==off
?>
        <tr align="center"><td>
<a href="pedido_rel.php?flag=detalhe&edtNumPed=<?=$linha->ped_num?>">
<img src="../imagens/btn_voltar.jpg" alt="Voltar" width="76" height="21" border='1'></a>
        </td>
        </tr>
	<? } //fim do $flag != fecha ?>
</table>
</form>
</body>
</html>
<?
  include("rodape.php");
?>