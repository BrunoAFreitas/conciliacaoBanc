<?php
/***
 * Pagina feita para melhoria do sistema Gercom
 * do grupo Jacauna, Está pagina está livre para
 * bom uso e melhoria do sistema manter este comentário.
 *
 * @author Akarlos Vasconcelos
 * @version 1.0
 * @copyright Ruah Industria
 * @access private
 * @package pedido_de_venda
 * @example cli_cad.php
 *
 * pagina feita para editar alguns dados do cleinte
 */
 
// incluindo a pagina da que pesquisa os dados

include_once ("cliDados.php");

include_once ("function_dados.php");
$valores = new function_dados();
 
?>

<html>
	<head>
		<meta content="text/html; charset=windows-1252" http-equiv="content-type">
		<link rel="stylesheet" type="text/css" href="estilo_css/estili_cad.css" />

		<script type="text/javascript">
			/**
			 * Função para mostrar e esconder os campos da tela
			 */
			function habilita() {
				var obj = document.getElementById('tipoCli').selectedIndex;
				var variaveis = ["nFantasia", "edtFantasia", "edtCgcCnpj", "cadastrar1"];
				var variaveisAl = ["nOrgaoEm", "nDtEm", "edtOrgEm", "edtDtEm", "nSexo", "nNascimento", "selSex", "edtDtNasc", "tabela", "tabela2", "tabela3", "tabela4", "linha", "linhaTra", "nTipoRes", "nResidDed", "edtTipRes", "edtResDesd", "tabela5", "tabela6", "edtCgcCpf", "cadastrar"];
				if (obj == 0) {
					//fisica
					for (var a in variaveis) {
						valoresIn(variaveis[a]);
					}
					for (var b in variaveisAl) {
						valoresVi(variaveisAl[b]);
					}
				}
				if (obj == 1) {
					//juridica
					for (var c in variaveis) {
						valoresVi(variaveis[c]);
					}
					for (var d in variaveisAl) {
						valoresIn(variaveisAl[d]);
					}
				}
			}
			
			// metodo para deixar invisel o campo de naturalidade
			function natural() {
				var obj = document.getElementById('selNacio').selectedIndex;
				if (obj != "31") {
					valoresIn('testTab');
					valoresIn('naturalNome');
				} else {
					valoresVi('testTab');
					valoresVi('naturalNome');
				}
			}
			
			function bancoContas() {
				var obj = document.getElementById('selBanco').selectedIndex;
				var dBancos = ["nAgencia","nConta", "nTipoConta", "nDataAberta",
							   "edtAgencia", "edtConta", "edtTipoConta", "edtDtAbet"];
				if(obj == "50") {
					//sem conta
					for (var banco in dBancos) {				
						valoresIn(dBancos[banco]);
					}
				} else {
					//tem conta
					for (var banco1 in dBancos) {				
						valoresVi(dBancos[banco1]);
					}
				}
			}
			/**
			 * Função para tirar as letras com acentos
			 */
			function tiraAcentos(objResp) {
				var varString = new String(objResp.value);
				var stringAcentos = new String('àâêôûãõáéíóúçüÀÂÊÔÛÃÕÁÉÍÓÚÇÜ');
				var stringSemAcento = new String('aaeouaoaeioucuAAEOUAOAEIOUCU');

				var i = new Number();
				var j = new Number();
				var cString = new String();
				var varRes = '';

				for ( i = 0; i < varString.length; i++) {
					cString = varString.substring(i, i + 1);
					for ( j = 0; j < stringAcentos.length; j++) {
						if (stringAcentos.substring(j, j + 1) == cString) {
							cString = stringSemAcento.substring(j, j + 1);
						}
					}
					varRes += cString;
				}
				objResp.value = varRes;
			}
		</script>
		<title></title>
	</head>
	<body onload="habilita();" background="imagens/fundomain.jpeg">
		<table width="60%" border="0" align="center">
			<tbody>
				<tr>
					<td>
					<form name="form_cleinte" id="form_cleinte" method="post">
						<!-- Tabela tipo de cliente -->
						<fieldset>
							<legend>
								<font size="+2" >Cadastro de Clientes</font>
							</legend>
							<table border="0">
								<tbody>
									<tr>
										<td> Tipo de Cliente:
										<select name="tipoCli" id="tipoCli" onChange="habilita();" class="select" >
											<option value="fisica">Pessoa Fisica</option>
											<option value="juridica">Pessoa Jurica</option>
										</select></td>
										<td>
											<center>
											<label>* Campos Obrigatorios! </label>
											</center>
										</td>
									</tr>
								</tbody>
							</table>
						</fieldset>
						<!-- Tabela da documentação -->
						<fieldset>
							<legend>
								Documentacao
							</legend>
							<table border="0">
								<tbody>
									<tr>
										<td>CPF/CNPJ*</td>
										<td>RG/Inscricao</td>
										<td><label id="nOrgaoEm">Orgao Emissor</label></td>
										<td><label id="nDtEm">Data de Emissao</label></td>
									</tr>
									<tr>
										<td>
										<input name="edtCgcCpf" id="edtCgcCpf" type="text" class="campo" onblur="validarCPF(form_cleinte.edtCgcCpf);">
										<input name="edtCgcCnpj" id="edtCgcCnpj" type="text" class="campo" onblur="validaCnpj(form_cleinte.edtCgcCnpj);">
										</td>
										<td><input name="edtInscr" id="edtInscr" type="text" class="campo"></td>
										<td><input name="edtOrgEm" id="edtOrgEm" type="text" class="campo"></td>
										<td><input name="edtDtEm" id="edtDtEm" type="text" class="campo" onfocus="data(form_cleinte.edtDtEm);" ></td>
									</tr>
								</tbody>
							</table>
						</fieldset>
						<!-- Esta tabela e mais e contem subes tables Dados pessoais-->
						<fieldset>
							<legend>
								Dados Pessoais
							</legend>
							<table border="0">
								<tbody>
									<tr>
										<td>Nome/Razao Social*</td>
										<td><label id="nFantasia">Nome Fantasia</label></td>
										<td><label id="nSexo">Sexo</label></td>
										<td><label id="nNascimento">Dt.Nascimento*</label></td>
									</tr>
									<tr>
										<td><input type="text" name="edtRazao" id="edtRazao" onkeypress="tiraAcentos(this);" onblur="tiraAcentos(this);" class="campoTexto"></td>
										<td><input name="edtFantasia" id="edtFantasia" type="text" class="campoTexto" onkeypress="tiraAcentos(this);" onblur="tiraAcentos(this);"></td>
										<td>
										<select name="selSex" id="selSex" class="select">
											<option>Masculino</option>
											<option>Feminino</option>
										</select></td>
										<td><input name="edtDtNasc" id="edtDtNasc" type="text" class="campo" onfocus="data(form_cleinte.edtDtNasc);"></td>
									</tr>
								</tbody>
							</table>
							<!-- para pessoa fisica -->
							<table border="0" id="tabela">
								<tbody>
									<tr>
										<td>Nacionalidade</td>
										<td id="naturalNome">Natural de</td>
										<td>Time de Futebol</td>
									</tr>
									<tr>
										<td><select name="selNacio" id="selNacio" class="select" onChange="natural();">
											<?php
											$valores->nacionalidade();
											?>										
										</select></td>
										<td id="testTab" ><select name="selNatu" id="selNatu" class="select">
											<?php
											$valores->naturalidade();
											?>
										</select></td>
										<td><select name="selTime" id="selTime" class="select">
											<?php
											$valores->times();
											?>
										</select></td>
									</tr>
								</tbody>
							</table>
							<hr>
							<!-- Para pessoa fisica -->
							<table border="0" id="tabela2">
								<tbody>
									<tr>
										<td>Estado Civel</td>
										<td>Conjuge</td>
										<td>Qtd Filhos</td>
									</tr>
									<tr>
										<td><select name="selEstCiv" id="selEstCiv" class="select">
											<option value="S">Solteiro(a)</option>
											<option value="C">Casado(a)</option>
											<option value="D">Divorciado(a)</option>
											<option value="V">Viuvo(a)</option>
										</select></td>
										<td><input name="edtConjuge" id="edtConjuge" type="text" class="campoTexto" onkeypress="tiraAcentos(this);" onblur="tiraAcentos(this);"></td>
										<td><input name="edtQtFi" id="edtQtFi" type="text" class="campo"></td>
									</tr>
								</tbody>
							</table>
							<table border="0" id="tabela5">
								<tbody>
									<tr>
										<td>Pai</td>
										<td>Mae</td>
									</tr>
									<tr>
										<td><input name="edtPai" id="edtPai" type="text" class="campoTexto" onkeypress="tiraAcentos(this);" onblur="tiraAcentos(this);"></td>
										<td><input name="edtMae" id="edtMae" type="text" class="campoTexto" onkeypress="tiraAcentos(this);" onblur="tiraAcentos(this);"></td>
									</tr>
								</tbody>
							</table>
							<hr id="linha">

							<!-- Tabela de endereço tanto para pessoa fisica e juridica -->
							<table border="0">
								<tbody>
									<tr>
										<td>Tipo Ende.</td>
										<td>Endereco</td>
										<td>Numero</td>
										<td>Complemento</td>
									</tr>
									<tr>
										<td><select name="edtEnd" id="edtEnd" class="select">
											<?php
											$valores->tendereco();
											?>
										</select></td>
										<td><input name="edtTipoEnde" id="edtTipoEnde" type="text" class="campoTexto" onkeypress="tiraAcentos(this);" onblur="tiraAcentos(this);"></td>
										<td><input name="edtNum" id="edtNum" type="text" class="campo"></td>
										<td><input name="edtCompt" id="edtCompt" type="text" class="campoTexto" onkeypress="tiraAcentos(this);" onblur="tiraAcentos(this);"></td>
									</tr>
								</tbody>
							</table>
							<!-- Segunda Parte do cadastro de endereço -->
							<!-- aqui e caso a loja seja $novocad == "S" -->
							<table border="0">
								<tbody>
									<tr>
										<td>Estado</td>
										<td>Cidade</td>
										<td>Bairro</td>
										<td>Cep</td>
										<td><label id="nTipoRes">Tipo Residencia</label></td>
										<td><label id="nResidDed">Reside Desde</label></td>
									</tr>
									<tr>
										<?
										// faz a verificação
										// para teste
										if ($novocad != "S") {
										?>
										<td><select name="edtEstado" id="edtEstado" class="select1">
										<?
										if ($edtEstado == "") {
											$edtEstado = $estado;
										}
										$valores -> estados($edtEstado);
										?>
										</select></td>
										<td><select name="edtCidade" id="edtCidade" class="select1">
										<?
										if ($edtCidade == "") {
											$edtCidade = strtoupper($cidade);
										}
										$valores -> cidades($estado, $edtCidade);
										?>
										</select></td>
										<td><select name="edtBairro" id="edtBairro" class="select1">
										<?
										$valores -> bairros($cidade, $edtBairro);
										?>
										</select></td>
										<?php
										} else {
										?>
										<td><input name="edtEstado" id="edtEstado" type="text" class="campo" value="<?=$estado ?>"></td>
										<td><input name="edtCidade" id="edtCidade" type="text" class="campo" value="<?=$cidade ?>"></td>
										<td><input name="edtBairro" id="edtBairro" type="text" class="campo"></td>
										<?php
										}
										?>
										<td><input name="edtCep" id="edtCep" type="text" class="campo" onblur="validaCep(form_cleinte.edtCep);"></td>
										<td><select name="edtTipRes" id="edtTipRes" class="select1">
											<?php
											$valores->tresidencia();
											?>
										</select></td>
										<td><input name="edtResDesd" id="edtResDesd" type="text" class="campo" onfocus="data(form_cleinte.edtResDesd);"></td>
									</tr>
								</tbody>
							</table>
							<!-- Cria aqui outra tabela caso seja $novocad != "S" -->
							<!-- Separando a parte de endereço -->
							<table border="0">
								<tbody>
									<tr>
										<td>Ponto de Referencia</td>
										<td>Endereco de Entrega</td>
									</tr>
									<tr>
										<td><textarea id="edtPontRef" name="edtPontRef" cols="40" rows="2" onkeypress="tiraAcentos(this);" onblur="tiraAcentos(this);"></textarea></td>
										<td><textarea id="edtEndEntrega" name="edtEndEntrega" cols="40" rows="2" onkeypress="tiraAcentos(this);" onblur="tiraAcentos(this);" title="o mesmo."></textarea></td>
									</tr>
								</tbody>
							</table>
							<hr>
							<!-- Informando o Email -->
							<table border="0">
								<tbody>
									<tr>
										<td>Fone</td>
										<td>Celular1</td>
										<td>Celular2</td>
									</tr>
									<tr>
										<td><input name="edtFone" id="edtFone" type="text" class="campo"></td>
										<td>
										<select name="edtOperadora1" id="edtOperadora1" class="select">
											<?
											$valores -> operadoras();
											?>
										</select>
										<input name="edtCelular1" id="edtCelular1" type="text" class="campo">
										</td>
										<td>
										<select name="edtOperadora2" id="edtOperadora2" class="select">
											<?
											$valores -> operadoras();
											?>
										</select>
										<input name="edtCelular2" id="edtCelular2" type="text" class="campo"></td>
									</tr>
								</tbody>
							</table>
							<table border="0">
								<tbody>
									<tr>
										<td>Email*</td>
										<td><input name="edtEmail" id="edtEmail" type="text" class="campoTexto" onblur="validaEmail(form_cleinte.edtEmail);"></td>
										<td>Redes Sociais?</td>
										<td>
										<select name="selRedeSoc" id="selRedeSoc" class="select">
											<option>Sim</option>
											<option>Nao</option>
										</select></td>
										<td>Quais</td>
										<td><input name="edtRedeSoc" id="edtRedeSoc" type="text" class="campo" onkeypress="tiraAcentos(this);" onblur="tiraAcentos(this);" title="facebook"></td>
									</tr>
								</tbody>
							</table>
							<hr>
							<!-- Parte somente para pessoa fisica -->
							<table border="0" id="tabela3">
								<tbody>
									<tr>
										<td>Profissao</td>
										<!-- Trabalho -->
										<td>Nome da Empresa</td>
										<td>DT Admissao</td>
										<td>Fone Trabalho</td>
									</tr>
									<tr>
										<td>
										<select name="edtProfissao" id="edtProfissao" class="select2">
											<?
											$valores -> profissao($edtProfissao);
											?>
										</select></td>
										<td><input name="edtTrabalho" id="edtTrabalho" type="text" class="campoTexto" onkeypress="tiraAcentos(this);" onblur="tiraAcentos(this);"></td>
										<td><input name="edtDtAdmis" id="edtDtAdmis" type="text" class="campo" onfocus="data(form_cleinte.edtDtAdmis);"></td>
										<td><input name="edtFoneTrab" id="edtFoneTrab" type="text" class="campo"></td>
									</tr>
								</tbody>
							</table>
							<table border="0" id="tabela6">
								<tbody>
									<tr>
										<td>Tipo End.</td>
										<td>Endereco</td>
										<td>Numero</td>
										<td>Complemento</td>
									</tr>
									<tr>
										<td><select name="edtEndTrabalho" id="edtEndTrabalho" class="select1">
											<?php
											$valores->tendereco();
											?>
										</select></td>
										<td><input name="edtTipoEndTrabalho" id="edtTipoEndTrabalho" type="text" class="campoTexto" onkeypress="tiraAcentos(this);" onblur="tiraAcentos(this);"></td>
										<td><input name="edtNumTrabalho" id="edtNumTrabalho" type="text" class="campo"></td>
										<td><input name="edtComTrabalho" id="edtComTrabalho" type="text" class="campoTexto" onkeypress="tiraAcentos(this);" onblur="tiraAcentos(this);"></td>
									</tr>
								</tbody>
							</table>
							<table border="0" id="tabela4">
								<tbody>
									<tr>
										<td>Estado</td>
										<td>Cidade</td>
										<td>Bairro</td>
										<td>Cep</td>
										<td>Renda Mensal</td>
										<td>Outras Rendas</td>
									</tr>
									<tr>
										<td>
										<select name="selEstadoTrab" id="selEstadoTrab" class="select1">
											<?
											if ($selEstadoTrab == "") {
												$selEstadoTrab = $estado;
											}
											$valores -> estados($selEstadoTrab);
											?>
										</select></td>
										<td>
										<select name="selCidadeTrab" id="selCidadeTrab" class="select1">
											<?
											if ($selCidadeTrab == "") {
												// strtoupper usado para conver as letras minuscalas em maiusculas
												$selCidadeTrab = strtoupper($cidade);
											}
											$valores -> cidades($estado, $selCidadeTrab);
											?>
										</select></td>
										<td>
										<select name="selBairroTrab" id="selBairroTrab" class="select1">
											<?
											$valores -> bairros($cidade, $selBairroTrab);
											?>
										</select></td>
										<td><input name="edtCepTrab" id="edtCepTrab" type="text" class="campo" onblur="validaCep(form_cleinte.edtCepTrab);"></td>
										<td><input name="edtRenda" id="edtRenda" type="text" class="campo"></td>
										<td><input name="edtOtRendas" id="edtOtRendas" type="text" class="campo"></td>
									</tr>
								</tbody>
							</table>
							<hr id="linhaTra" >
							<table border="0">
								<tbody>
									<tr>
										<td>Banco</td>
										<td id="nAgencia" >Agencia</td>
										<td id="nConta" >Conta</td>
										<td id="nTipoConta" >Tipo Conta</td>
										<td id="nDataAberta" >Dt Aberta</td>
									</tr>
									<tr>
										<td><select name="selBanco" id="selBanco" class="select" onChange="bancoContas();">
											<?php
											$valores->banco();
											?>
										</select></td>
										<td><input name="edtAgencia" id="edtAgencia" type="text" class="campo" onkeypress="tiraAcentos(this);" onblur="tiraAcentos(this);"></td>
										<td><input name="edtConta" id="edtConta" type="text" class="campo"></td>
										<td><select name="edtTipoConta" id="edtTipoConta" class="campo">
											<option value="C">Corrente</option>
											<option value="P">Poupanca</option>
										</select></td>
										<td><input name="edtDtAbet" id="edtDtAbet" type="text" class="campo" onfocus="data(form_cleinte.edtDtAbet);"></td>
									</tr>
								</tbody>
							</table>
							<!-- -->
							<table border="0">
								<tbody>
									<tr>
										<td>Obs. Extras</td>
									</tr>
									<tr>
										<td><textarea id="edtExtras" name="edtExtras" cols="60" rows="3" onkeypress="tiraAcentos(this);" onblur="tiraAcentos(this);"> </textarea></td>
									</tr>
								</tbody>
							</table>
							<table border="0" align="center">
								<tbody>
									<tr>
										<td>
										<input name="cadastrar" value="Cadastrar PF" id="cadastrar" type="button" class="button" onclick="camposVerificar();" />
										<input name="cadastrar1" value="Cadastrar PJ" id="cadastrar1" type="button" class="button" onclick="camposVerificar1();" />
										</td>
										<td>
										<input value="Limpar" type="reset" class="button" />
										</td>
									</tr>
								</tbody>
							</table>
							<!--Fim da tabela dados pessoais -->
						</fieldset>
					</form></td>
				</tr>
			</tbody>
		</table>
	</body>
</html>
