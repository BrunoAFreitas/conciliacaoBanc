<?php
	include("conexao2.inc.php");
	include("funcoes2.inc.php");
	//include("dglogin1.php");

	$arquivo = "cli_cad.php";
	//include("auditoria.php");

    $hora = date("H:i:s");
	
	// variaveis para teste
	$acelogin = "jalen";
	//$acelogin = "lucianotm";
	$ljcod = "07";
	$flag;
	
 $sql = "SELECT * FROM acessos WHERE ace_login = '$acelogin'";
 $query = mysql_query($sql)or die("Erro na Consulta!");
 if(mysql_num_rows($query) > 0){
   $linha = mysql_fetch_object($query);	 
   
   /**
    * essas variaveis sao para resgatas
    * alguns dados para a table ele tem que
    * começar no caso traz os valores apra o resto da pagina
    * */
   if ($linha->ace_14 == 'S') {
	//selecionando a cidade e o estado da loja
	$sql_lj = "SELECT lj_cidade, lj_estado, lj_uf, lj_novocadcli FROM loja WHERE lj_cod = '$ljcod'";
	$query_lj = mysql_query($sql_lj)or die(mysql_error());
	$linha_lj = mysql_fetch_object($query_lj);
	$cidade = $linha_lj->lj_cidade;
	$estado = $linha_lj->lj_estado;
	$novocad = $linha_lj->lj_novocadcli;			
	$data = date("d/m/Y");
	$data = muda_data_en($data);
			
    if (@$flag == "jacadastrado"){
     $sql_checacgccpf = "SELECT cli_cgccpf FROM clientes WHERE cli_cgccpf = '$edtCgcCpf'";
     $query_checacgccpf = mysql_query($sql_checacgccpf);
	 if (mysql_num_rows($query_checacgccpf) > 0){
	   	 $msg_cli = "ERRO: Cliente com este CPF/CNPJ JÁ CADASTRADO!";
      }
    }
	
	
	/**
	 * essa parte e quando for clicado para executar
	 * para madandar os dados e gravar no banco
	 * */
	// para começar a cadastrar
	if (@$REQUEST_METHOD == "POST") {
      if ($incluir == "s"){
		//convertendo datas...
		$edtDtNasc = muda_data_en($edtDtNasc);
		$edtDesde = muda_data_en($edtDesde);
		//convertendo valores..
		$edtCredito = valor_mysql($edtCredito);
		$edtLimite = valor_mysql($edtLimite);

		$edtRazao    = any_accentuation($edtRazao);    $edtInscr    = any_accentuation($edtInscr);
        $edtEnd      = any_accentuation($edtEnd);      $edtBairro   = any_accentuation($edtBairro);
        $edtCidade   = any_accentuation($edtCidade);   $edtEstado   = any_accentuation($edtEstado);
        $edtConjuge  = any_accentuation($edtConjuge);  $edtPai      = any_accentuation($edtPai);
        $edtMae      = any_accentuation($edtMae);      $edtTrabalho = any_accentuation($edtTrabalho);
		$edtPontoref = any_accentuation($edtPontoref);

		$edtRazao    = LimparTexto($edtRazao);    $edtInscr    = LimparTexto($edtInscr);
        $edtEnd      = LimparTexto($edtEnd);      $edtBairro   = LimparTexto($edtBairro);
        $edtCidade   = LimparTexto($edtCidade);   $edtEstado   = LimparTexto($edtEstado);
        $edtConjuge  = LimparTexto($edtConjuge);  $edtPai      = LimparTexto($edtPai);
        $edtMae      = LimparTexto($edtMae);      $edtTrabalho = LimparTexto($edtTrabalho);
		$edtPontoref = LimparTexto($edtPontoref);
		//para converter
		
		//$edtCgcCpf nome de um campo
        $sql_checacgccpf   = "SELECT cli_cgccpf FROM clientes WHERE cli_cgccpf = '$edtCgcCpf'";
		$query_checacgccpf = mysql_query($sql_checacgccpf);
		
		if (mysql_num_rows($query_checacgccpf) > 0){
             $msg_cli = "ERRO: Cliente com este CPF/CNPJ JÁ CADASTRADO!";
    	} else {
    		
          if ($edtCgcCpf == ""){
             $msg_cli = "ERRO: Cliente sem CPF/CNPJ não pode ser Cadastrado!";
          }else{
          	
			//$edtRazao o nome da pesso um campo
            if ($edtRazao == ""){
               $msg_cli = "Erro: Cliente sem Nome ou Razão Social não pode ser Cadastrado!";
            }else{
    
	
////////////////////////////////////////////////////////////////////////////////////////////////////////////
             //$edtEmail campo email
             // strtolower faz com que todos as letras fiquem em minusculo
			 $edtEmail = strtolower($edtEmail);
			  //valor da loja
		      if ($novocad  == "S") {
		     	//pega a cidade 	
				if ($edtCidade == "0") {
                    $msg_cli = "Erro: Cliente sem Cidade não pode ser Cadastrado!";					
				} else {
					/**
					 * Colocar essetes codigos em function
					 * */
					//pega o bairro
  				 	if ($edtBairro == "0") {
                  		$msg_cli = "Erro: Cliente sem Bairro não pode ser Cadastrado!";
				 	} else {
				 		
						
				 		// segunda parte para gravar os dados
					//achando o nome da cidade
					$sql_cidade = "SELECT fmun_desc FROM fmunicipios WHERE fmun_cod = '$edtCidade'";
					$query_cidade = mysql_query($sql_cidade)or die(mysql_error());
					if(mysql_num_rows($query_cidade) > 0) {
					 $linha_cidade = mysql_fetch_object($query_cidade);
					 $edtCidadedesc = $linha_cidade->fmun_desc;
					} else {
					 $edtCidadedesc = 'SEM CIDADE';			
					}
					//achando o nome do bairro
					$sql_bairro = "SELECT bair_desc FROM bairros WHERE bair_cod = '$edtBairro';";
					$query_bairro = mysql_query($sql_bairro,$conexao)or die(mysql_error());
					if(mysql_num_rows($query_bairro) > 0){
					 $linha_bairro = mysql_fetch_object($query_bairro);
					 $edtBairrodesc = $linha_bairro->bair_desc;
					}else{
					 $edtBairrodesc = 'SEM BAIRRO';						
					}
					 
				   $sql_cli = "INSERT INTO clientes (cli_cgccpf, cli_emp, cli_razao, cli_fantasia, cli_fisica,
													 cli_inscrg, cli_end, cli_pontoref, cli_bairro, cli_cidade, 
													 cli_bairrocod, cli_cidadecod,
													 cli_estado, cli_cep, cli_fone, cli_fax, cli_contato,
													 cli_limite, cli_dtcad, cli_desde, cli_conjuge, cli_pai,
													 cli_mae, cli_trabalho, cli_fonetrab,
													 cli_incluido, cli_alterado,
													 cli_email, cli_dtincluido, cli_dtalterado, cli_dtnasc, 
													 cli_celular1,  
													 cli_celular2, cli_operadora1, cli_operadora2,
													 cli_hora, cli_loja, cli_login, cli_profissaocod, 
													 cli_endentrega)
													 values('$edtCgcCpf',
													 '$codemp','$edtRazao','$edtRazao','S','$edtInscr',
													 '$edtEnd', '$edtPontoref', '$edtBairrodesc','$edtCidadedesc', 
													 '$edtBairro','$edtCidade',
													 '$edtEstado','$edtCep',
													 '$edtFone','$edtFax','$edtContato','$edtLimite','$data',
													 '$edtDesde','$edtConjuge','$edtPai','$edtMae','$edtTrabalho',
													 '$edtFoneTrab',
													 'S','N','$edtEmail','$data','','$edtDtNasc','$edtCelular1', 
													 '$edtCelular2', '$edtOperadora1', '$edtOperadora2',
													 '$hora','$ljcod','$acelogin', '$edtProfissao', 
													 '$edtEndEntrega');";
				  $query_cli = mysql_query($sql_cli)or die("Erro Na Inclusão!");
				  $msg_cli = "Cadastro Concluido com Sucesso!";					 
				 }
				}
		      } else { // else do if ( ($linha_lj->lj_novocadcli == "S") && ($acelogin == "jalen") ) {				
				 $sql_cli = "INSERT INTO clientes (cli_cgccpf, cli_emp, cli_razao, cli_fantasia, cli_fisica,
											   cli_inscrg, cli_end, cli_pontoref, cli_bairro, cli_cidade,
											   cli_estado, cli_cep, cli_fone, cli_fax, cli_contato,
											   cli_limite, cli_dtcad, cli_desde, cli_conjuge, cli_pai,
											   cli_mae, cli_trabalho, cli_fonetrab,
											   cli_incluido, cli_alterado,
											   cli_email, cli_dtincluido, 
											   cli_dtalterado, cli_dtnasc, 
											   cli_celular1,  cli_celular2, cli_operadora1, cli_operadora2,
											   cli_hora, cli_loja, cli_login, cli_profissaocod, cli_endentrega)
											   values('$edtCgcCpf',
											   '$codemp','$edtRazao','$edtRazao','S','$edtInscr',
											   '$edtEnd', '$edtPontoref', 
											   '$edtBairro','$edtCidade','$edtEstado','$edtCep',
											   '$edtFone','$edtFax','$edtContato','$edtLimite','$data',
											   '$edtDesde','$edtConjuge','$edtPai','$edtMae','$edtTrabalho',
											   '$edtFoneTrab',
											   'S','N','$edtEmail','$data','','$edtDtNasc','$edtCelular1', 
											   '$edtCelular2', '$edtOperadora1', '$edtOperadora2',
											   '$hora','$ljcod','$acelogin', '$edtProfissao', '$edtEndEntrega' );";
				$query_cli = mysql_query($sql_cli)or die("Erro Na Inclusão!");
				$msg_cli = "Cadastro Concluido com Sucesso!";
		      } // fim do if ( ($linha_lj->lj_novocadcli == "S") && ($acelogin == "jalen") ) {
////////////////////////////////////////////////////////////////////////////////////////////////////////////
		  	
		      	
		    //parte que volta para a tela da pesquisa  	
			if($btn == "voltar") {
				if($arq == "pedcad") {
					$abre = "pedido_cad.php?acao=reload&edtNumPed=$edtNumPed&lstCli=$lstCli&lstVend=$lstVend&lstTV=$lstTV&produtos=$produtos&lstCli=$edtCgcCpf";
				}elseif($arq == "pedalt") {
					$abre = "pedido_alt.php?acao=reload&edtNumPed=$edtNumPed&lstCli=$lstCli&lstVend=$lstVend&lstTV=$lstTV&produtos=$produtos";
				}
?>
				<script>
					window.opener.location = "<?=$abre?>";
					window.close();
				</script>
<?
			 }
           }
        }
	  } //fim do else cliente ja cadastrado
   } //fim do incluir = s
}//fim do REQUEST

?>

<html>
<head>
	<link rel="stylesheet" href="est_big.css" type="text/css"> 
	<title>:: gercom.NET - Cadastro de Produtos ::</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<script type="text/javascript" src="autocomplete/jquery.js"></script>
    <script type="text/javascript" src="js/cbox.js"></script>
	
	<script language="JavaScript">
		function submit_action(){
			//postando para a verificacao;
			var caminho = "";
			var btn = "<?=$btn?>";
			if(btn != "voltar"){
				caminho = 'cli_cad.php?campo=formCliente.edtCgcCpf&btn=<?=$btn?>&incluir=s&menuoff=<?=$menuoff?>';
				if(document.formCliente.edtCgcCpf.value != ""){
					document.formCliente.action= caminho;
					document.formCliente.method= 'post';
					document.formCliente.submit();
				}else{
					alert("Digite o CGC/CPF do Cliente!");
					document.formCliente.edtCgcCpf.focus();
				}
			}else{
				caminho = 'cli_cad.php?btn=<?=$btn?>&arq=<?=$arq?>&edtNumPed=<?=$edtNumPed?>&lstCli=<?=$lstCli?>
				&lstVend=<?=$lstVend?>&lstTV=<?=$lstTV?>&produtos=<?=$produtos?>&incluir=s';
				if(document.formCliente.edtCgcCpf.value != ""){
					document.formCliente.action= caminho;
					document.formCliente.method= 'post';
					document.formCliente.submit();
				}else{
					alert("Digite o CGC/CPF do Cliente!");
					document.formCliente.edtCgcCpf.focus();
				}
			}
		}

		function submit_action2(caminho){
			//postando para a verificacao;
			document.formCliente.action= caminho;
			document.formCliente.method= 'post';
			document.formCliente.submit();
		}
	</script>
	
</head>

<body background="fundomain.png" topmargin="5" leftmargin="5" rightmargin="5" bottommargin="5">
<?
//  if ($menuoff != "ok") {
    //include("menu_java.php");
//  } 
?>

<form action="" method="post" name="formCliente">
			<table width="100%" cellpadding="2" align="center" cellspacing="2" border="1" bordercolor="#CCCCCC">
				<tr>
					<td colspan='2' width="100%" align="center" bgcolor="#004000"><b><font size='3' color="#FFFFFF"> <u>CADASTRO DE CLIENTES</u></font></b></td>
				</tr>
<?php
	if(isset($msg_cli)){
		echo "<tr>";
			  echo "<td bgcolor='#800000' align='center' width='100%'><font size='5' color='#FFFFFF'>".$msg_cli."
			  </font></td>";
		echo "</tr>";
	}
?>
<tr>
 <td colspan='2'>
  <table bgcolor='#ECECFF' width="100%" align="center">
      <tr>
        <td align='right'><font color="#400000">CPF ou CGC *:</font></td>
        <td colspan='4' ><input style="border:solid 1; color:#000000; width:198; background-color:#C0C0FF;" type=
        "text" name="edtCgcCpf" value="<?=$edtCgcCpf?>" onKeyPress="verifica_cgccpf()" onBlur=
        "submit_action2('cli_cad.php?menuoff=<?=$menuoff?>&flag=jacadastrado&checaponto=<?=$checaponto?>');">
        <font color="#400000">&nbsp; RG ou Inscri&ccedil;&atilde;o:</font>
        <input style="border:solid 1; width:197; background-color:#FFFFC0;" type="text" name="edtInscr">
        <font size='2' color="#400000">S&oacute; n&uacute;meros... (* campos obrigat&oacute;rios)</font></td>
      </tr>
      <tr>
        <td align='right'><font color="#400000">Nome ou Raz&atilde;o Social *:</font></td>
        <td colspan='4'><input style="border:solid 1; width:500; background-color:#FFFFC0;" type="text" name=
        "edtRazao">
            <input type="hidden" name="edtFantasia" size="40" maxlength="100"></td>
      </tr>
      <tr>
        <td align='right'><font color="#400000">E-mail *:</font></td>
        <td colspan='4'><input style="border:solid 1; width:500; background-color:#FFFFC0;" type="text" name=
        "edtEmail"></td>
      </tr>
      <tr>
        <td align="right"><font color="#400000">Endere&ccedil;o:</font></td>
        <td colspan='4'><input style="border:solid 1; width:500; background-color:#FFFFC0;" type="text" name=
        "edtEnd"></td> 
      </tr>
      <tr>
        <td align="right"><font color="#400000">Ponto Refer&ecirc;ncia:</font></td>
        <td colspan='4'><input style="border:solid 1; width:500; background-color:#FFFFC0;" type="text" name=
        "edtPontoref"></td>
      </tr>
      <tr>
		<? 
		@$novocad = "S";
		if ($novocad == "S") { ?>        
        <td align="right"><font color="#400000">Estado:</font></td>
        <td colspan='4'>
          <select name="edtEstado" style="border:solid 1; width:50; background-color:#FFFFC0;" >
		  <?
		      if ($edtEstado == "") { $edtEstado = $estado; }
              $sql_uf = "SELECT fmun_uf FROM fmunicipios group by fmun_uf order by fmun_uf;";
              $query_uf = mysql_query($sql_uf,$conexao);
              if (mysql_num_rows($query_uf) > 0){
                  while($linha_uf = mysql_fetch_object($query_uf)){
                      if($linha_uf->fmun_uf == $edtEstado){
                          echo "<option value='".$linha_uf->fmun_uf."' selected>".$linha_uf->fmun_uf."</option>";
                      }else{
                          echo "<option value='".$linha_uf->fmun_uf."'>".$linha_uf->fmun_uf."</option>";
                      }
                  }
              }
          ?>	
          </select>

          Cidade: 
          <select name="edtCidade" style="border:solid 1; width:205; background-color:#FFFFC0;" >
            <?
		      if ($edtCidade == "") { $edtCidade = strtoupper($linha_lj->lj_cidade); }
              $sql_cidade = "SELECT fmun_desc,fmun_cod FROM fmunicipios where fmun_uf = '$estado' order by 
			  fmun_desc;";
              $query_cidade = mysql_query($sql_cidade,$conexao);
              if (mysql_num_rows($query_cidade) > 0){
                          echo "<option value='0' selected>Escolha Cidade</option>";				  
                  while($linha_cidade = mysql_fetch_object($query_cidade)){
                      if($linha_cidade->fmun_cod == $edtCidade){
                          echo "<option value='".$linha_cidade->fmun_cod."' selected>".$linha_cidade->fmun_desc."
						  </option>";
                      }else{
                          echo "<option value='".$linha_cidade->fmun_cod."'>".$linha_cidade->fmun_desc."</option>";
                      }
                  }
              }
          ?>
          </select>
		  Bairro:
		  <select name="edtBairro" style="border:solid 1; width:205; background-color:#FFFFC0;" >
		  <?
              $sql_bair = "SELECT bair_cod,bair_desc FROM bairros WHERE bair_cidade='$cidade' ORDER BY bair_desc";

              $query_bair = mysql_query($sql_bair,$conexao);
              if (mysql_num_rows($query_bair) > 0){
                          echo "<option value='0' selected>Escolha Bairro</option>"; 
                  while($linha_bair = mysql_fetch_object($query_bair)){
					  $bairrodesc = $linha_bair->bair_desc;
                      if($linha_bair->bair_cod == $edtBairro){
                          echo "<option value='".$linha_bair->bair_cod."' selected>".any_accentuation($bairrodesc)."
						  </option>";
                      }else{
                          echo "<option value='".$linha_bair->bair_cod."'>".$bairrodesc."</option>";
                      }
                  }
              }
          ?>	
          </select>

		<? 	  }else{	 ?>                
        <td align="right"><font color="#400000">Bairro:</font></td>
        <td colspan='4'>
          <input style="border:solid 1; width:196; background-color:#FFFFC0;" type="text" name="edtBairro">
           <font color="#400000">&nbsp; Cidade:</font>
          <input style="border:solid 1; width:197; background-color:#FFFFC0;" type="text" name="edtCidade" value="
		  <?=$cidade?>">
          <font color="#400000">&nbsp; UF:</font>
          <input style="border:solid 1; width:22; background-color:#FFFFC0;" name="edtEstado" type="text" value="<?=
		  $estado?>">
		<? } ?>                
        </td>
      </tr>
      <tr>
        <td align="right"><font color="#400000">CEP:</font></td>
        <td colspan='4'>
        <input style="border:solid 1; width:100; background-color:#FFFFC0;" type="text" name="edtCep">
         <font color="#400000">&nbsp; Fone:</font>
		<input style="border:solid 1; width:80; background-color:#FFFFC0;" type="text" name="edtFone">
        &nbsp; <font color="#400000">&nbsp; Celular 1:</font>
             <select name="edtOperadora1">
                <?
                        echo "<option value='TIM'>TIM</option>";
                        echo "<option value='VIVO'>VIVO</option>";
                        echo "<option value='CLARO'>CLARO</option>";
                        echo "<option value='OI'>OI</option>";
                        echo "<option value='NEXTEL'>NEXTEL</option>";	
                        echo "<option value='OUTRA'>OUTRA</option>";																																																																	
                ?>
            </select>
			
        <input style="border:solid 1; width:80; background-color:#FFFFC0;" name="edtCelular1" type="text">
		<font color="#400000">&nbsp; Celular 2:</font>
             <select name="edtOperadora2">
                <?
                        echo "<option value='TIM'>TIM</option>";
                        echo "<option value='VIVO'>VIVO</option>";
                        echo "<option value='CLARO'>CLARO</option>";
                        echo "<option value='OI'>OI</option>";
                        echo "<option value='NEXTEL'>NEXTEL</option>";																											
                        echo "<option value='OUTRA'>OUTRA</option>";																																	
                ?>
            </select>
        <input style="border:solid 1; width:80; background-color:#FFFFC0;" name="edtCelular2" type="text">        
        </td>        
      </tr>
      <tr>
        <td align="right"><font color="#400000">Dt. Nascim.:</font></td>
        <td colspan='4'><input style="border:solid 1; width:100; background-color:#FFFFC0;" type="text" 
        name="edtDtNasc">
        </td>
      </tr>
      <tr>
        <td align="right"><font color="#400000">Endere&ccedil;o de Entrega.:</font></td>
        <td colspan='4'><textarea style="border:solid 1; background-color:#FFFFC0;" name="edtEndEntrega" cols="80" 
        rows="3"></textarea> 
        </td>
      </tr>
  </table>
 </td>
</tr>
<tr>
 <td colspan='2'>
  <table bgcolor='#ECECFF' width="100%" align="center">
      <tr>
        <td align='right'><font color="#400000">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp 
        Contato:</font></td>
        <td colspan='3'><input style="border:solid 1; width:140; background-color:#FFFFC0;" type="text" 
        name="edtContato">
         <font color="#400000">&nbsp; Renda R$:</font>
         <input style="border:solid 1; width:97; background-color:#FFFFC0;" type="text" name="edtLimite">
         <font color="#400000">Profiss&atilde;o:</font>
          <select name="edtProfissao" style="border:solid 1; width:205; background-color:#FFFFC0;" >
              <?
                  $sql_prof = "SELECT clip_cod, clip_desc FROM clientes_prof WHERE clip_ativo = 'S' ORDER BY 
				  clip_desc;";
                  $query_prof = mysql_query($sql_prof,$conexao);
                  if (mysql_num_rows($query_prof) > 0){
                      while($linha_prof = mysql_fetch_object($query_prof)){
                          if($linha_prof->clip_cod == $edtProfissao){
                              echo "<option value='".$linha_prof->clip_cod."' selected>".$linha_prof->clip_desc."
							  </option>";
                          }else{
                              echo "<option value='".$linha_prof->clip_cod."'>".$linha_prof->clip_desc."</option>";
                          }
                      }
                  }
              ?>	
          </select>
<!--<input style="border:solid 1; width:140; background-color:#FFFFC0;" type="text" name="edtProfissao"> -->
        </td>
      </tr>
      <tr>
        <td align='right'><font color="#400000">Trabalho:</font></td>
        <td colspan='3' ><input style="border:solid 1; color:#000000; width:174; background-color:#C0C0FF;" 
        type="text" name="edtTrabalho">
         <font color="#400000">&nbsp; Fone Trab:</font>
         <input style="border:solid 1; width:90; background-color:#FFFFC0;" type="text" name="edtFoneTrab">
         <font color="#400000">&nbsp; Fone Fax:</font>
         <input style="border:solid 1; width:95; background-color:#FFFFC0;" type="text" name="edtFax">
        </td>
      </tr>
  </table>
 </td>
</tr>
<tr>
 <td colspan='2'>
  <table bgcolor='#ECECFF' width="100%" align="center">
      <tr>
        <td align='right'><font color="#400000">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        C&ocirc;njuge:</font></td>
        <td colspan='3'><input style="border:solid 1; width:499; background-color:#FFFFC0;" type="text" 
        name="edtConjuge"></td>
      </tr>
      <tr>
        <td align='right'><font color="#400000">        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Pai:</font></td>
        <td colspan='3'><input style="border:solid 1; width:499; background-color:#FFFFC0;" type="text" name="edtPai"></td>
      </tr>
      <tr>
        <td align='right'><font color="#400000">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; M&atilde;e:</font></td>
        <td colspan='3'><input style="border:solid 1; width:499; background-color:#FFFFC0;" type="text" name="edtMae"></td>
      </tr>
  </table>
 </td>
</tr>
<tr>
 <td colspan='4' align="center">
  <input style="border:solid 1; height:25; width:200; color:#FFFFFF; background-color:#004000;" type="button" value="Cadastrar" onClick="javascript:submit_action();">
  <input style="border:solid 1; height:25; width:200; color:#FFFFFF; background-color:#004000;" type="reset" value="Limpar">
 </td>
</tr>
</table>
</form>
</body>
</html>
<?
 }else{
	//include("naoautorizado.php");
  }
 }
?>
<?
  //include("rodape.php");
?>